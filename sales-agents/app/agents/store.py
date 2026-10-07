from __future__ import annotations

from sqlalchemy.orm import Session

from app.models import Approval, Contact, Message, Thread, utcnow


def get_or_create_thread(db: Session, contact: Contact, channel: str) -> Thread:
    thread = (
        db.query(Thread)
        .filter(Thread.contact_id == contact.id, Thread.channel == channel)
        .first()
    )
    if thread is None:
        thread = Thread(contact_id=contact.id, channel=channel, status="open")
        db.add(thread)
        db.flush()
    return thread


def queue_approval(db: Session, message: Message, kind: str) -> Approval:
    existing = (
        db.query(Approval)
        .filter(Approval.message_id == message.id, Approval.status == "pending")
        .first()
    )
    if existing:
        return existing
    row = Approval(message_id=message.id, kind=kind, status="pending")
    db.add(row)
    db.flush()
    message.status = "pending_approval"
    return row


def add_inbound(
    db: Session,
    contact: Contact,
    channel: str,
    body: str,
    *,
    subject: str = "",
    classification: str = "",
) -> Message:
    thread = get_or_create_thread(db, contact, channel)
    msg = Message(
        thread_id=thread.id,
        contact_id=contact.id,
        prospect_id=contact.prospect_id,
        channel=channel,
        direction="inbound",
        subject=subject,
        body=body,
        status="received",
        classification=classification,
    )
    db.add(msg)
    thread.last_message_at = utcnow()
    thread.unread = True
    db.flush()
    return msg
