from __future__ import annotations

import secrets

from sqlalchemy.orm import Session

from app.agents.store import get_or_create_thread, queue_approval
from app.config import get_settings
from app.knowledge import load_knowledge
from app.llm import complete
from app.models import Contact, Message, Prospect, utcnow


def new_send_id(contact_id: int) -> str:
    return f"c{contact_id}_{int(utcnow().timestamp())}_{secrets.token_hex(3)}"


def explore_link(send_id: str) -> str:
    settings = get_settings()
    base = settings.explore_demo_base_url.rstrip("/")
    sep = "&" if "?" in base else "?"
    return f"{base}{sep}send_id={send_id}"


def template_reply(contact: Contact, inbound: Message, label: str, send_id: str) -> tuple[str, str]:
    settings = get_settings()
    kb = load_knowledge()
    company = contact.prospect.company if contact.prospect else "your shop"
    first = (contact.name or "").split()[0] if contact.name else ""
    hi = f"Hi {first}," if first and first.lower() != company.lower() else "Hi,"
    url = explore_link(send_id)
    cal = f" If you’d rather walk it together: {settings.calendar_url}" if settings.calendar_url else " Or reply with a time that works for a 15-minute walkthrough."
    sender = settings.sender_name
    company = contact.prospect.company if contact.prospect else "your shop"

    if label == "objection":
        subject = "Fair — here’s the actual product"
        body = (
            f"{hi}\n\n"
            f"Understood. LimoGen is not a generic CRM with a limo coat of paint — "
            f"it’s lead → quote → e-sign → pay, plus an instant-quote widget for the website.\n\n"
            f"Click into a live workspace (no calendar delay): {url}\n"
            f"{kb.demo_order[0]}. {cal}\n\n"
            f"If SMS is easier, reply TEXT and I’ll send the link to your phone after you confirm.\n\n"
            f"{sender}"
        )
        return subject, body

    if label == "wrong_person":
        subject = "Thanks — who should I talk to?"
        body = (
            f"{hi}\n\n"
            f"Appreciate the heads-up. Who at {company} owns bookings / the website quotes?\n\n"
            f"{sender}"
        )
        return subject, body

    subject = "LimoGen demo workspace"
    body = (
        f"{hi}\n\n"
        f"Glad this is useful. Here’s a tracked explore link into a live LimoGen workspace — "
        f"dashboard, lead → quote → agreement → payment, fleet, and the widget:\n\n"
        f"{url}\n\n"
        f"{cal}\n\n"
        f"{settings.brochure_note}\n"
        f"If you want the link by SMS or WhatsApp instead, reply TEXT or WHATSAPP and confirm the number.\n\n"
        f"{sender}"
    )
    return subject, body


async def draft_reply(db: Session, contact: Contact, inbound: Message, label: str) -> Message | None:
    if label in {"unsubscribe", "bounce", "ooo"}:
        return None

    send_id = new_send_id(contact.id)
    subject, body = template_reply(contact, inbound, label, send_id)
    kb = load_knowledge()
    polished = await complete(
        "You are the LimoGen closer. Rewrite the reply. Keep the explore URL exactly. "
        "Do not invent features. Brand is LimoGen never LimoCRM. Short, operator tone.",
        f"{kb.prompt_block()}\n\nLabel: {label}\nInbound:\n{inbound.body[:2000]}\n\nDraft:\n{body}",
    )
    if polished:
        body = polished
        if explore_link(send_id) not in body:
            body = body.rstrip() + f"\n\nExplore: {explore_link(send_id)}\n"

    channel = inbound.channel if inbound.channel in {"email", "linkedin", "instagram", "facebook", "x", "sms", "whatsapp"} else "email"
    thread = get_or_create_thread(db, contact, channel)
    msg = Message(
        thread_id=thread.id,
        contact_id=contact.id,
        prospect_id=contact.prospect_id,
        channel=channel,
        direction="outbound",
        subject=subject if channel == "email" else "",
        body=body,
        status="pending_approval",
        sequence_step="closer",
        send_id=send_id,
    )
    db.add(msg)
    db.flush()
    kind = "inbox_reply" if channel == "email" else "social"
    if channel in {"sms", "whatsapp"}:
        kind = channel
    queue_approval(db, msg, kind)
    prospect = db.query(Prospect).filter(Prospect.id == contact.prospect_id).first()
    if prospect and "explore" in body.lower():
        if prospect.stage == "talking":
            prospect.next_action = "Approve closer reply"
    return msg
