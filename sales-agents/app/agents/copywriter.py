from __future__ import annotations

import json
from sqlalchemy.orm import Session

from app.agents.store import get_or_create_thread, queue_approval
from app.compliance import SOCIAL_CHANNELS
from app.config import get_settings
from app.knowledge import load_knowledge
from app.llm import complete_json
from app.models import Contact, Message, Prospect, SequenceState, utcnow


EMAIL_STEPS = (
    ("1", "pain"),
    ("2", "widget"),
    ("3", "signpay"),
    ("4", "demo"),
)

SOCIAL_STEPS = {
    "linkedin": ("connection", "dm1", "dm2"),
    "instagram": ("comment_reply", "dm1"),
    "facebook": ("comment_reply", "dm1"),
    "x": ("reply", "dm1"),
}


def _first_name(contact: Contact) -> str:
    name = (contact.name or "").strip()
    if not name or name.lower() == (contact.prospect.company if contact.prospect else "").lower():
        return ""
    return name.split()[0]


def _research_text(prospect: Prospect) -> str:
    try:
        data = json.loads(prospect.research_json or "{}")
    except json.JSONDecodeError:
        data = {}
    return json.dumps(data)[:2500]


def template_emails(contact: Contact, prospect: Prospect) -> list[tuple[str, str, str]]:
    kb = load_knowledge()
    pain = kb.pick_pain(_research_text(prospect))
    settings = get_settings()
    first = _first_name(contact)
    hi = f"Hi {first}," if first else "Hi there,"
    company = prospect.company
    city = f" in {prospect.city}" if prospect.city else ""
    sender = settings.sender_name
    demo = settings.explore_demo_base_url

    e1_subj = f"{company} still running bookings out of the inbox?"
    e1 = (
        f"{hi}\n\n"
        f"Most limo shops{city} still run the day from Gmail, a driver group chat, and Venmo. "
        f"{pain.problem}\n\n"
        f"LimoGen puts lead → quote → sign → pay in one workspace built for operators, not a generic CRM. "
        f"Website visitors can get an instant quote from a branded widget; follow-up and status changes can run on workflows.\n\n"
        f"Worth a look if {company} is still stitching tools together.\n\n"
        f"{sender}"
    )

    e2_subj = f"Instant quotes on the {company} site"
    e2 = (
        f"{hi}\n\n"
        f"Quick follow-up: the piece that usually pays for itself first is the embeddable quote widget. "
        f"Pickup, destination, date, passengers, vehicle — it creates a lead in the pipeline instead of a voicemail.\n\n"
        f"If the {company} site still asks people to call for a price, that’s the leak.\n\n"
        f"{sender}"
    )

    e3_subj = f"Sign and pay without the DocuSign / Venmo chase"
    e3 = (
        f"{hi}\n\n"
        f"Once a job is quoted, LimoGen sends an agreement link. The customer reviews the trip, e-signs, "
        f"and pays (Stripe, PayPal, or offline) on one branded page. The booking updates when it clears — "
        f"dispatch isn’t hunting Venmo screenshots.\n\n"
        f"Happy to show the path on a {company}-style job.\n\n"
        f"{sender}"
    )

    e4_subj = f"Open a LimoGen workspace today"
    cal = f" Or grab time here: {settings.calendar_url}" if settings.calendar_url else " Reply with a time that works."
    e4 = (
        f"{hi}\n\n"
        f"I’ll keep this short. You can click into a live LimoGen demo workspace from this link "
        f"(I’ll attach a tracked explore URL when this send goes out): {demo}\n\n"
        f"Dashboard, lead → quote → agreement → payment, fleet, widget.{cal}\n\n"
        f"{sender}"
    )
    return [
        ("1", e1_subj, e1),
        ("2", e2_subj, e2),
        ("3", e3_subj, e3),
        ("4", e4_subj, e4),
    ]


def template_social(contact: Contact, prospect: Prospect, channel: str, step: str) -> str:
    first = _first_name(contact) or "there"
    company = prospect.company
    if channel == "linkedin" and step == "connection":
        return (
            f"Hi {first} — I work on LimoGen, a CRM built for limo operators "
            f"(lead → quote → sign → pay). Thought of {company} if bookings still live in the inbox."
        )
    if channel == "linkedin" and step == "dm1":
        return (
            f"Thanks for connecting. Curious how {company} handles website inquiries today — "
            f"phone tag, or do visitors get an instant quote?"
        )
    if channel == "linkedin" and step == "dm2":
        return (
            f"If useful, I can send a tracked demo workspace so you can click through the pipeline "
            f"without a calendar delay. No pitch deck required."
        )
    if step == "comment_reply":
        return (
            f"If {company} is still quoting from a spreadsheet, that’s the exact mess LimoGen was built to kill. "
            f"Lead, formal quote, e-sign, pay — one system."
        )
    if channel == "x" and step == "reply":
        return (
            f"This is why operators outgrow Gmail + group chat. LimoGen is the limo-specific workspace "
            f"for that gap — not a generic CRM bent into shape."
        )
    return (
        f"Happy to send a LimoGen explore link if you want to see lead → quote → sign → pay on a real workspace."
    )


async def llm_polish(contact: Contact, prospect: Prospect, drafts: dict) -> dict:
    kb = load_knowledge()
    data = await complete_json(
        "You write short B2B outreach for LimoGen. Return JSON only. Keep facts. Follow brand rules.",
        f"{kb.prompt_block()}\n\nProspect: {prospect.company}, {prospect.city} {prospect.region}\n"
        f"Website: {prospect.website}\nContact: {contact.name} ({contact.role})\n"
        f"Research: {_research_text(prospect)}\n\n"
        f"Rewrite these drafts. Keep similar length. JSON keys must match exactly:\n"
        f"{json.dumps(drafts)}\n"
        "Also include email_subjects as a dict of step number to subject.",
    )
    if not data:
        return drafts
    return data


def _existing_draft(db: Session, contact_id: int, channel: str, step: str) -> Message | None:
    return (
        db.query(Message)
        .filter(
            Message.contact_id == contact_id,
            Message.channel == channel,
            Message.sequence_step == step,
            Message.direction == "outbound",
            Message.status.in_(["draft", "pending_approval", "queued_auto", "ready_to_send", "approved"]),
        )
        .first()
    )


def _ensure_sequence(db: Session, contact_id: int) -> SequenceState:
    row = (
        db.query(SequenceState)
        .filter(SequenceState.contact_id == contact_id, SequenceState.channel == "email")
        .first()
    )
    if row is None:
        row = SequenceState(contact_id=contact_id, channel="email")
        db.add(row)
        db.flush()
    return row


async def draft_outreach(db: Session, contact: Contact, *, include_social: bool = True) -> dict:
    prospect = contact.prospect
    created: list[int] = []

    emails = template_emails(contact, prospect) if contact.email else []
    social_drafts: dict[str, str] = {}
    if include_social:
        for channel, steps in SOCIAL_STEPS.items():
            url_field = {
                "linkedin": contact.linkedin_url,
                "instagram": contact.instagram_url,
                "facebook": contact.facebook_url,
                "x": contact.x_url,
            }[channel]
            if not url_field:
                continue
            for step in steps:
                social_drafts[f"{channel}:{step}"] = template_social(contact, prospect, channel, step)

    bundle = {
        "emails": {step: {"subject": subj, "body": body} for step, subj, body in emails},
        "social": social_drafts,
    }
    polished = await llm_polish(contact, prospect, bundle)
    email_map = polished.get("emails") or bundle["emails"]
    social_map = polished.get("social") or bundle["social"]
    subjects = polished.get("email_subjects") or {}

    thread = get_or_create_thread(db, contact, "email")
    for step, subj, body in emails:
        item = email_map.get(step) if isinstance(email_map, dict) else None
        if isinstance(item, dict):
            subj = item.get("subject") or subjects.get(step) or subj
            body = item.get("body") or body
        if _existing_draft(db, contact.id, "email", step):
            continue
        status = "pending_approval" if step == "1" else "draft"
        msg = Message(
            thread_id=thread.id,
            contact_id=contact.id,
            prospect_id=prospect.id,
            channel="email",
            direction="outbound",
            subject=subj,
            body=body,
            status=status,
            sequence_step=step,
        )
        db.add(msg)
        db.flush()
        if step == "1":
            queue_approval(db, msg, "first_email")
        created.append(msg.id)

    if include_social:
        for key, body in social_map.items():
            channel, step = key.split(":", 1)
            if channel not in SOCIAL_CHANNELS:
                continue
            if _existing_draft(db, contact.id, channel, step):
                continue
            thread = get_or_create_thread(db, contact, channel)
            msg = Message(
                thread_id=thread.id,
                contact_id=contact.id,
                prospect_id=prospect.id,
                channel=channel,
                direction="outbound",
                subject="",
                body=body if isinstance(body, str) else str(body),
                status="pending_approval",
                sequence_step=step,
            )
            db.add(msg)
            db.flush()
            queue_approval(db, msg, "social")
            created.append(msg.id)

    _ensure_sequence(db, contact.id)
    if prospect.stage in {"new", "researched"}:
        prospect.stage = "queued"
    prospect.next_action = "Approve first email"
    prospect.updated_at = utcnow()
    db.commit()
    return {"created": len(created), "message_ids": created}
