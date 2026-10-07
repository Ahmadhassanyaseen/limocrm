from __future__ import annotations

import logging
from datetime import timedelta

from sqlalchemy.orm import Session

from app.agents.store import get_or_create_thread
from app.compliance import followup_offsets, is_suppressed, under_daily_limit
from app.config import get_settings
from app.emailer import send_email
from app.models import Approval, Contact, Message, Prospect, SequenceState, utcnow

log = logging.getLogger("sales-agents.sequencer")


def _seq(db: Session, contact_id: int) -> SequenceState | None:
    return (
        db.query(SequenceState)
        .filter(SequenceState.contact_id == contact_id, SequenceState.channel == "email")
        .first()
    )


def pause_for_reply(db: Session, contact_id: int) -> None:
    row = _seq(db, contact_id)
    if row:
        row.paused = True
        row.replied = True
    for msg in (
        db.query(Message)
        .filter(
            Message.contact_id == contact_id,
            Message.channel == "email",
            Message.status == "queued_auto",
        )
        .all()
    ):
        msg.status = "skipped"


def pause_all(db: Session, contact_id: int) -> None:
    row = _seq(db, contact_id)
    if row:
        row.paused = True
        row.completed = True
    for msg in (
        db.query(Message)
        .filter(
            Message.contact_id == contact_id,
            Message.status.in_(["queued_auto", "draft", "pending_approval", "ready_to_send"]),
        )
        .all()
    ):
        msg.status = "skipped"
    for ap in (
        db.query(Approval)
        .join(Message, Approval.message_id == Message.id)
        .filter(Message.contact_id == contact_id, Approval.status == "pending")
        .all()
    ):
        ap.status = "rejected"
        ap.note = "suppressed"
        ap.decided_at = utcnow()


def explore_url_for(contact: Contact, send_id: str) -> str:
    settings = get_settings()
    base = settings.explore_demo_base_url.rstrip("/")
    sep = "&" if "?" in base else "?"
    return f"{base}{sep}send_id={send_id}"


def attach_demo_link(body: str, url: str) -> str:
    if "send_id=" in body or "login_explore" in body:
        return body.replace("http://localhost/limocrm/login_explore.php", url)
    return body.rstrip() + f"\n\nExplore the workspace: {url}\n"


async def send_outbound_email(db: Session, message: Message) -> None:
    contact = db.query(Contact).filter(Contact.id == message.contact_id).first()
    if not contact or not contact.email:
        raise RuntimeError("Contact has no email")
    if is_suppressed(db, email=contact.email):
        message.status = "skipped"
        message.error = "suppressed"
        return
    if not under_daily_limit(db, "email"):
        raise RuntimeError("Daily email limit reached")

    body = message.body
    if message.sequence_step == "4" or "explore" in (message.subject or "").lower():
        if not message.send_id:
            message.send_id = f"c{contact.id}_{int(utcnow().timestamp())}_{contact.id:04d}"
        body = attach_demo_link(body, explore_url_for(contact, message.send_id))
        message.body = body

    provider_id = await send_email(contact.email, message.subject or "LimoGen", body)
    message.status = "sent"
    message.sent_at = utcnow()
    message.provider_id = provider_id
    thread = message.thread
    if thread is None:
        thread = get_or_create_thread(db, contact, "email")
        message.thread_id = thread.id
    thread.last_message_at = utcnow()
    prospect = db.query(Prospect).filter(Prospect.id == message.prospect_id).first()
    if prospect and prospect.stage in {"queued", "researched", "new"}:
        prospect.stage = "contacted"
        prospect.next_action = "Wait for reply / follow-up"
        prospect.next_action_at = utcnow() + timedelta(days=3)


async def approve_and_dispatch(db: Session, approval: Approval) -> dict:
    message = approval.message
    approval.status = "approved"
    approval.decided_at = utcnow()
    message.status = "approved"

    if message.channel == "email" and message.sequence_step == "1":
        await send_outbound_email(db, message)
        _schedule_followups(db, message)
        seq = _seq(db, message.contact_id)
        if seq:
            seq.first_approved_at = utcnow()
            seq.current_step = 1
        db.commit()
        return {"sent": True, "channel": "email"}

    if message.channel == "email":
        await send_outbound_email(db, message)
        db.commit()
        return {"sent": True, "channel": "email"}

    if message.channel in {"sms", "whatsapp"}:
        from app.sms import send_opt_in_message

        await send_opt_in_message(db, message)
        db.commit()
        return {"sent": True, "channel": message.channel}

    message.status = "ready_to_send"
    db.commit()
    return {"sent": False, "channel": message.channel, "queued_for_today": True}


def _schedule_followups(db: Session, first: Message) -> None:
    offsets = followup_offsets()
    followups = (
        db.query(Message)
        .filter(
            Message.contact_id == first.contact_id,
            Message.channel == "email",
            Message.sequence_step.in_(["2", "3", "4"]),
            Message.status == "draft",
        )
        .all()
    )
    by_step = {m.sequence_step: m for m in followups}
    for idx, step in enumerate(["2", "3", "4"]):
        msg = by_step.get(step)
        if not msg:
            continue
        offset = offsets[idx] if idx < len(offsets) else timedelta(days=3 * (idx + 1))
        msg.status = "queued_auto"
        msg.scheduled_at = utcnow() + offset


async def tick(db: Session) -> dict:
    now = utcnow()
    due = (
        db.query(Message)
        .filter(
            Message.channel == "email",
            Message.status == "queued_auto",
            Message.scheduled_at.is_not(None),
            Message.scheduled_at <= now,
        )
        .all()
    )
    sent = 0
    errors = 0
    for msg in due:
        seq = _seq(db, msg.contact_id)
        contact = db.query(Contact).filter(Contact.id == msg.contact_id).first()
        if seq and (seq.paused or seq.replied):
            msg.status = "skipped"
            continue
        if contact and is_suppressed(db, email=contact.email):
            msg.status = "skipped"
            continue
        try:
            await send_outbound_email(db, msg)
            if seq:
                seq.current_step = int(msg.sequence_step or seq.current_step)
            sent += 1
        except Exception as exc:
            log.exception("Follow-up send failed")
            msg.error = str(exc)
            msg.status = "failed"
            errors += 1
    db.commit()
    return {"sent": sent, "errors": errors, "due": len(due)}
