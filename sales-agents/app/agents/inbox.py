from __future__ import annotations

import logging
import re

from sqlalchemy.orm import Session

from app.agents.closer import draft_reply
from app.agents.sequencer import pause_all, pause_for_reply
from app.agents.store import add_inbound
from app.compliance import suppress
from app.knowledge import load_knowledge
from app.llm import complete_json
from app.models import Contact, Prospect, utcnow

log = logging.getLogger("sales-agents.inbox")

LABELS = ("interested", "objection", "ooo", "unsubscribe", "wrong_person", "bounce", "other")


def heuristic_classify(text: str) -> str:
    t = (text or "").lower()
    if any(w in t for w in ("unsubscribe", "stop emailing", "remove me", "opt out")):
        return "unsubscribe"
    if any(w in t for w in ("out of office", "on vacation", "parental leave", "auto-reply")):
        return "ooo"
    if any(w in t for w in ("undeliverable", "mailbox unavailable", "bounce")):
        return "bounce"
    if any(w in t for w in ("wrong person", "no longer with", "not the owner")):
        return "wrong_person"
    if any(w in t for w in ("not interested", "too expensive", "already have", "no thanks")):
        return "objection"
    if any(w in t for w in ("interested", "tell me more", "demo", "pricing", "sounds good", "let's talk", "send the link")):
        return "interested"
    return "other"


async def classify(text: str) -> dict:
    kb = load_knowledge()
    data = await complete_json(
        "Classify a sales reply. Return JSON {label, reason}. "
        f"label must be one of: {', '.join(LABELS)}.",
        f"{kb.brand_rules}\n\nReply:\n{text[:4000]}",
    )
    label = (data.get("label") or "").lower()
    if label not in LABELS:
        label = heuristic_classify(text)
    return {"label": label, "reason": data.get("reason") or ""}


async def ingest(
    db: Session,
    contact: Contact,
    channel: str,
    body: str,
    *,
    subject: str = "",
) -> dict:
    result = await classify(body)
    label = result["label"]
    inbound = add_inbound(db, contact, channel, body, subject=subject, classification=label)
    prospect = db.query(Prospect).filter(Prospect.id == contact.prospect_id).first()

    if label == "unsubscribe":
        suppress(db, email=contact.email, phone=contact.phone, reason="unsubscribe")
        pause_all(db, contact.id)
        if prospect:
            prospect.stage = "lost"
            prospect.next_action = "Do not contact"
    elif label in {"bounce"}:
        suppress(db, email=contact.email, reason="bounce")
        pause_all(db, contact.id)
    elif label == "ooo":
        pass
    elif label == "wrong_person":
        pause_for_reply(db, contact.id)
        if prospect:
            prospect.next_action = "Find a better contact"
    else:
        pause_for_reply(db, contact.id)
        if prospect:
            prospect.stage = "talking" if label == "interested" else prospect.stage
            if prospect.stage in {"contacted", "queued", "researched"}:
                prospect.stage = "talking"
            prospect.next_action = "Review closer draft"
            prospect.updated_at = utcnow()
        await draft_reply(db, contact, inbound, label)

    db.commit()
    return {"message_id": inbound.id, "label": label, "reason": result.get("reason", "")}


def find_contact_by_email(db: Session, email: str) -> Contact | None:
    if not email:
        return None
    return db.query(Contact).filter(Contact.email == email.lower().strip()).first()


def extract_email(raw: str) -> str:
    m = re.search(r"[\w.+-]+@[\w.-]+\.[A-Za-z]{2,}", raw or "")
    return (m.group(0) if m else "").lower()
