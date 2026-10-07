from __future__ import annotations

from fastapi import APIRouter, Depends, Form, Request
from fastapi.responses import HTMLResponse, RedirectResponse
from fastapi.templating import Jinja2Templates
from sqlalchemy.orm import Session, joinedload

from app.config import get_settings
from app.compliance import SOCIAL_CHANNELS, is_suppressed, sent_today, verify_unsub_token, suppress
from app.db import get_db
from app.knowledge import load_knowledge
from app.models import Approval, Contact, Message, Prospect, SequenceState, Thread

templates: Jinja2Templates
router = APIRouter()

NAV = [
    ("/", "Dashboard"),
    ("/prospects", "Prospects"),
    ("/approvals", "Approvals"),
    ("/inbox", "Inbox"),
    ("/today", "Today"),
    ("/import", "Import"),
    ("/settings", "Settings"),
]


def render(request: Request, name: str, db: Session | None = None, **extra):
    return templates.TemplateResponse(request, name, ctx(request, db=db, **extra))


def ctx(request: Request, **extra):
    db: Session | None = extra.get("db")
    pending = 0
    unread = 0
    if db is not None:
        pending = db.query(Approval).filter(Approval.status == "pending").count()
        unread = db.query(Thread).filter(Thread.unread.is_(True)).count()
        extra.pop("db", None)
    settings = get_settings()
    return {
        "request": request,
        "nav": NAV,
        "pending_count": pending,
        "unread_count": unread,
        "brand": "LimoGen",
        "sender_name": settings.sender_name,
        **extra,
    }


@router.get("/", response_class=HTMLResponse)
def dashboard(request: Request, db: Session = Depends(get_db)):
    stages = [
        "new",
        "researched",
        "queued",
        "contacted",
        "talking",
        "demo",
        "won",
        "lost",
    ]
    counts = {s: db.query(Prospect).filter(Prospect.stage == s).count() for s in stages}
    return render(
        request,
        "dashboard.html",
        db,
        counts=counts,
        total=db.query(Prospect).count(),
        emails_today=sent_today(db, "email"),
        kb=load_knowledge(),
    )


@router.get("/prospects", response_class=HTMLResponse)
def prospects(request: Request, stage: str = "", db: Session = Depends(get_db)):
    q = db.query(Prospect).order_by(Prospect.icp_score.desc(), Prospect.updated_at.desc())
    if stage:
        q = q.filter(Prospect.stage == stage)
    rows = q.limit(200).all()
    return render(request, "prospects.html", db, prospects=rows, stage=stage)


@router.get("/prospects/{prospect_id}", response_class=HTMLResponse)
def prospect_detail(request: Request, prospect_id: int, db: Session = Depends(get_db)):
    prospect = (
        db.query(Prospect)
        .options(joinedload(Prospect.contacts))
        .filter(Prospect.id == prospect_id)
        .first()
    )
    if not prospect:
        return RedirectResponse("/prospects", status_code=302)
    contacts = prospect.contacts
    messages = (
        db.query(Message)
        .filter(Message.prospect_id == prospect_id)
        .order_by(Message.created_at.desc())
        .limit(50)
        .all()
    )
    consents = []
    from app.models import Consent

    if contacts:
        consents = (
            db.query(Consent)
            .filter(Consent.contact_id.in_([c.id for c in contacts]))
            .all()
        )
    return render(
        request,
        "prospect_detail.html",
        db,
        prospect=prospect,
        contacts=contacts,
        messages=messages,
        consents=consents,
        suppressed={
            c.id: bool(is_suppressed(db, email=c.email, phone=c.phone)) for c in contacts
        },
    )


@router.get("/approvals", response_class=HTMLResponse)
def approvals(request: Request, db: Session = Depends(get_db)):
    rows = (
        db.query(Approval)
        .options(joinedload(Approval.message))
        .filter(Approval.status == "pending")
        .order_by(Approval.created_at.asc())
        .all()
    )
    cards = []
    for ap in rows:
        msg = ap.message
        contact = db.query(Contact).filter(Contact.id == msg.contact_id).first() if msg else None
        prospect = db.query(Prospect).filter(Prospect.id == msg.prospect_id).first() if msg else None
        cards.append({"approval": ap, "message": msg, "contact": contact, "prospect": prospect})
    return render(request, "approvals.html", db, cards=cards)


@router.get("/inbox", response_class=HTMLResponse)
def inbox(request: Request, db: Session = Depends(get_db)):
    threads = (
        db.query(Thread)
        .options(joinedload(Thread.contact))
        .order_by(Thread.unread.desc(), Thread.last_message_at.desc())
        .limit(100)
        .all()
    )
    contacts = db.query(Contact).order_by(Contact.name).limit(300).all()
    return render(request, "inbox.html", db, threads=threads, contacts=contacts)


@router.get("/inbox/{thread_id}", response_class=HTMLResponse)
def thread_view(request: Request, thread_id: int, db: Session = Depends(get_db)):
    thread = (
        db.query(Thread)
        .options(joinedload(Thread.contact), joinedload(Thread.messages))
        .filter(Thread.id == thread_id)
        .first()
    )
    if not thread:
        return RedirectResponse("/inbox", status_code=302)
    thread.unread = False
    db.commit()
    prospect = db.query(Prospect).filter(Prospect.id == thread.contact.prospect_id).first()
    messages = sorted(thread.messages, key=lambda m: m.created_at or m.id)
    return render(request, "thread.html", db, thread=thread, prospect=prospect, messages=messages)


@router.get("/today", response_class=HTMLResponse)
def today(request: Request, db: Session = Depends(get_db)):
    rows = (
        db.query(Message)
        .filter(
            Message.channel.in_(SOCIAL_CHANNELS),
            Message.status == "ready_to_send",
            Message.direction == "outbound",
        )
        .order_by(Message.channel, Message.id)
        .all()
    )
    grouped: dict[str, list] = {c: [] for c in SOCIAL_CHANNELS}
    for msg in rows:
        contact = db.query(Contact).filter(Contact.id == msg.contact_id).first()
        prospect = db.query(Prospect).filter(Prospect.id == msg.prospect_id).first()
        grouped.setdefault(msg.channel, []).append(
            {"message": msg, "contact": contact, "prospect": prospect}
        )
    return render(request, "today.html", db, grouped=grouped)


@router.get("/import", response_class=HTMLResponse)
def import_page(request: Request, db: Session = Depends(get_db)):
    return render(request, "import.html", db)


@router.get("/settings", response_class=HTMLResponse)
def settings_page(request: Request, db: Session = Depends(get_db)):
    s = get_settings()
    kb = load_knowledge()
    flags = {
        "LLM": bool(s.openai_api_key),
        "Resend": bool(s.resend_api_key),
        "SMTP": bool(s.smtp_host),
        "IMAP": bool(s.imap_host and s.imap_user),
        "Google Places": bool(s.google_places_api_key),
        "Hunter.io": bool(s.hunter_api_key),
        "Twilio SMS": bool(s.twilio_account_sid and s.twilio_from_number),
        "WhatsApp": bool(s.twilio_whatsapp_from or (s.meta_whatsapp_token and s.meta_whatsapp_phone_id)),
        "Calendar URL": bool(s.calendar_url),
    }
    return render(
        request,
        "settings.html",
        db,
        flags=flags,
        kb=kb,
        sender_email=s.sender_email,
        mailing_address=s.mailing_address,
        explore=s.explore_demo_base_url,
        sequences=db.query(SequenceState).count(),
    )


@router.get("/unsubscribe", response_class=HTMLResponse)
def unsubscribe_get(request: Request, email: str = "", token: str = "", db: Session = Depends(get_db)):
    ok = bool(email and verify_unsub_token(email, token))
    return render(request, "unsubscribe.html", email=email, token=token, ok=ok, done=False)


@router.post("/unsubscribe", response_class=HTMLResponse)
def unsubscribe_post(
    request: Request,
    email: str = Form(""),
    token: str = Form(""),
    db: Session = Depends(get_db),
):
    ok = bool(email and verify_unsub_token(email, token))
    if ok:
        suppress(db, email=email, reason="unsubscribe")
        contact = db.query(Contact).filter(Contact.email == email.lower().strip()).first()
        if contact:
            from app.agents.sequencer import pause_all

            pause_all(db, contact.id)
        db.commit()
    return render(request, "unsubscribe.html", email=email, token=token, ok=ok, done=ok)
