from __future__ import annotations

from fastapi import APIRouter, Depends, File, Form, HTTPException, UploadFile
from sqlalchemy.orm import Session, joinedload

from app.agents import closer, copywriter, hunter, inbox, sequencer
from app.compliance import has_consent
from app.db import get_db
from app.imap_poll import poll_imap
from app.models import Approval, Contact, Message, Prospect, utcnow
from app.sms import draft_opt_in_message, record_consent

router = APIRouter(prefix="/api")


@router.post("/prospects")
def create_prospect(
    company: str = Form(...),
    city: str = Form(""),
    region: str = Form(""),
    country: str = Form("US"),
    website: str = Form(""),
    phone: str = Form(""),
    contact_name: str = Form(""),
    email: str = Form(""),
    role: str = Form(""),
    linkedin_url: str = Form(""),
    instagram_url: str = Form(""),
    facebook_url: str = Form(""),
    x_url: str = Form(""),
    db: Session = Depends(get_db),
):
    prospect = Prospect(
        company=company.strip(),
        city=city.strip(),
        region=region.strip(),
        country=country.strip() or "US",
        website=website.strip(),
        phone=phone.strip(),
        source="manual",
        stage="new",
    )
    db.add(prospect)
    db.flush()
    hunter.upsert_contact(
        db,
        prospect,
        name=contact_name,
        email=email,
        role=role,
        phone=phone,
        linkedin_url=linkedin_url,
        instagram_url=instagram_url,
        facebook_url=facebook_url,
        x_url=x_url,
    )
    prospect.icp_score, prospect.icp_notes = hunter.score_prospect(
        prospect, {}, bool(email)
    )
    db.commit()
    return {"id": prospect.id}


@router.post("/prospects/{prospect_id}/stage")
def set_stage(prospect_id: int, stage: str = Form(...), db: Session = Depends(get_db)):
    prospect = db.query(Prospect).filter(Prospect.id == prospect_id).first()
    if not prospect:
        raise HTTPException(404)
    prospect.stage = stage
    prospect.updated_at = utcnow()
    db.commit()
    return {"ok": True, "stage": stage}


@router.post("/import/csv")
async def import_csv(file: UploadFile = File(...), db: Session = Depends(get_db)):
    text = (await file.read()).decode("utf-8-sig", errors="replace")
    return hunter.import_csv(db, text)


@router.post("/hunter/places")
async def places(query: str = Form(...), db: Session = Depends(get_db)):
    return await hunter.places_search(db, query.strip())


@router.post("/hunter/enrich/{prospect_id}")
async def enrich_one(prospect_id: int, db: Session = Depends(get_db)):
    prospect = db.query(Prospect).filter(Prospect.id == prospect_id).first()
    if not prospect:
        raise HTTPException(404)
    research = await hunter.enrich_prospect(db, prospect)
    return {"ok": True, "score": prospect.icp_score, "research": research}


@router.post("/hunter/enrich-all")
async def enrich_all(db: Session = Depends(get_db)):
    rows = (
        db.query(Prospect)
        .filter(Prospect.stage == "new", Prospect.website != "")
        .limit(10)
        .all()
    )
    done = 0
    errors = []
    for prospect in rows:
        try:
            await hunter.enrich_prospect(db, prospect)
            done += 1
        except Exception as exc:
            errors.append({"id": prospect.id, "error": str(exc)})
    return {"enriched": done, "errors": errors}


@router.post("/copywriter/draft/{contact_id}")
async def draft_contact(contact_id: int, db: Session = Depends(get_db)):
    contact = (
        db.query(Contact)
        .options(joinedload(Contact.prospect))
        .filter(Contact.id == contact_id)
        .first()
    )
    if not contact:
        raise HTTPException(404)
    return await copywriter.draft_outreach(db, contact, include_social=True)


@router.post("/copywriter/draft-prospect/{prospect_id}")
async def draft_prospect(prospect_id: int, db: Session = Depends(get_db)):
    contacts = (
        db.query(Contact)
        .options(joinedload(Contact.prospect))
        .filter(Contact.prospect_id == prospect_id)
        .all()
    )
    if not contacts:
        raise HTTPException(404, "No contacts on this prospect")
    total = 0
    for contact in contacts:
        result = await copywriter.draft_outreach(db, contact, include_social=True)
        total += result.get("created", 0)
    return {"created": total}


@router.post("/approvals/{approval_id}/approve")
async def approve(approval_id: int, body: str = Form(""), subject: str = Form(""), db: Session = Depends(get_db)):
    approval = (
        db.query(Approval)
        .options(joinedload(Approval.message))
        .filter(Approval.id == approval_id)
        .first()
    )
    if not approval or approval.status != "pending":
        raise HTTPException(404)
    if body:
        approval.message.body = body
    if subject:
        approval.message.subject = subject
    try:
        result = await sequencer.approve_and_dispatch(db, approval)
    except Exception as exc:
        db.rollback()
        approval = db.query(Approval).filter(Approval.id == approval_id).first()
        raise HTTPException(400, str(exc)) from exc
    return result


@router.post("/approvals/{approval_id}/reject")
def reject(approval_id: int, note: str = Form(""), db: Session = Depends(get_db)):
    approval = db.query(Approval).filter(Approval.id == approval_id).first()
    if not approval:
        raise HTTPException(404)
    approval.status = "rejected"
    approval.note = note
    approval.decided_at = utcnow()
    if approval.message:
        approval.message.status = "skipped"
    db.commit()
    return {"ok": True}


@router.post("/inbox/paste")
async def paste_inbound(
    contact_id: int = Form(...),
    channel: str = Form(...),
    body: str = Form(...),
    db: Session = Depends(get_db),
):
    contact = (
        db.query(Contact)
        .options(joinedload(Contact.prospect))
        .filter(Contact.id == contact_id)
        .first()
    )
    if not contact:
        raise HTTPException(404)
    return await inbox.ingest(db, contact, channel, body)


@router.post("/inbox/{thread_id}/reply-draft")
async def reply_draft(thread_id: int, db: Session = Depends(get_db)):
    last = (
        db.query(Message)
        .filter(Message.thread_id == thread_id, Message.direction == "inbound")
        .order_by(Message.id.desc())
        .first()
    )
    if not last:
        raise HTTPException(404, "No inbound message")
    contact = db.query(Contact).options(joinedload(Contact.prospect)).filter(Contact.id == last.contact_id).first()
    msg = await closer.draft_reply(db, contact, last, last.classification or "interested")
    db.commit()
    return {"message_id": msg.id if msg else None}


@router.post("/today/{message_id}/mark-sent")
def mark_sent(message_id: int, db: Session = Depends(get_db)):
    msg = db.query(Message).filter(Message.id == message_id).first()
    if not msg:
        raise HTTPException(404)
    msg.status = "sent"
    msg.sent_at = utcnow()
    prospect = db.query(Prospect).filter(Prospect.id == msg.prospect_id).first()
    if prospect and prospect.stage in {"queued", "researched", "new"}:
        prospect.stage = "contacted"
    db.commit()
    return {"ok": True}


@router.post("/consent/{contact_id}")
def consent(
    contact_id: int,
    channel: str = Form(...),
    text: str = Form(...),
    source: str = Form("operator"),
    db: Session = Depends(get_db),
):
    contact = db.query(Contact).filter(Contact.id == contact_id).first()
    if not contact:
        raise HTTPException(404)
    if channel not in {"sms", "whatsapp"}:
        raise HTTPException(400, "channel must be sms or whatsapp")
    record_consent(db, contact, channel, source=source, text=text)
    db.commit()
    return {"ok": True}


@router.post("/sms/draft/{contact_id}")
def sms_draft(
    contact_id: int,
    channel: str = Form("sms"),
    body: str = Form(...),
    db: Session = Depends(get_db),
):
    contact = db.query(Contact).filter(Contact.id == contact_id).first()
    if not contact:
        raise HTTPException(404)
    if not has_consent(db, contact.id, channel):
        raise HTTPException(400, "Record explicit consent before drafting SMS/WhatsApp")
    msg = draft_opt_in_message(db, contact, channel, body)
    return {"message_id": msg.id}


@router.post("/sequencer/tick")
async def sequencer_tick(db: Session = Depends(get_db)):
    return await sequencer.tick(db)


@router.post("/imap/poll")
async def imap_now():
    return await poll_imap()
