"""Opt-in SMS / WhatsApp only. Never used for cold outreach."""

from __future__ import annotations

import logging
from datetime import datetime, timezone

import httpx
from sqlalchemy.orm import Session

from app.agents.store import get_or_create_thread, queue_approval
from app.compliance import has_consent, is_suppressed, under_daily_limit
from app.config import get_settings
from app.models import Consent, Contact, Message, utcnow

log = logging.getLogger("sales-agents.sms")


def record_consent(
    db: Session,
    contact: Contact,
    channel: str,
    *,
    source: str,
    text: str,
) -> Consent:
    if channel not in {"sms", "whatsapp"}:
        raise ValueError("Consent is only for sms or whatsapp")
    row = Consent(
        contact_id=contact.id,
        channel=channel,
        source=source,
        text=text,
        granted_at=datetime.now(timezone.utc),
    )
    db.add(row)
    db.flush()
    return row


def draft_opt_in_message(db: Session, contact: Contact, channel: str, body: str) -> Message:
    if not has_consent(db, contact.id, channel):
        raise RuntimeError("No consent on file for this channel")
    if is_suppressed(db, phone=contact.phone, email=contact.email):
        raise RuntimeError("Contact is suppressed")
    if not contact.phone:
        raise RuntimeError("Contact has no phone number")
    thread = get_or_create_thread(db, contact, channel)
    msg = Message(
        thread_id=thread.id,
        contact_id=contact.id,
        prospect_id=contact.prospect_id,
        channel=channel,
        direction="outbound",
        body=body,
        status="pending_approval",
        sequence_step="optin",
    )
    db.add(msg)
    db.flush()
    queue_approval(db, msg, channel)
    db.commit()
    return msg


async def send_opt_in_message(db: Session, message: Message) -> None:
    contact = db.query(Contact).filter(Contact.id == message.contact_id).first()
    if not contact:
        raise RuntimeError("Missing contact")
    channel = message.channel
    if not has_consent(db, contact.id, channel):
        raise RuntimeError("Cannot send without consent")
    if is_suppressed(db, phone=contact.phone):
        message.status = "skipped"
        message.error = "suppressed"
        return
    if not under_daily_limit(db, channel):
        raise RuntimeError("Daily SMS/WhatsApp limit reached")

    if channel == "sms":
        provider_id = await send_sms(contact.phone, message.body)
    else:
        provider_id = await send_whatsapp(contact.phone, message.body)
    message.status = "sent"
    message.sent_at = utcnow()
    message.provider_id = provider_id


async def send_sms(phone: str, body: str) -> str:
    settings = get_settings()
    if not (settings.twilio_account_sid and settings.twilio_auth_token and settings.twilio_from_number):
        raise RuntimeError("Twilio SMS is not configured")
    url = f"https://api.twilio.com/2010-04-01/Accounts/{settings.twilio_account_sid}/Messages.json"
    async with httpx.AsyncClient(timeout=30) as client:
        resp = await client.post(
            url,
            auth=(settings.twilio_account_sid, settings.twilio_auth_token),
            data={"From": settings.twilio_from_number, "To": phone, "Body": body},
        )
        resp.raise_for_status()
        return str(resp.json().get("sid") or "twilio")


async def send_whatsapp(phone: str, body: str) -> str:
    settings = get_settings()
    to = phone if phone.startswith("+") else "+" + "".join(ch for ch in phone if ch.isdigit())

    if settings.twilio_whatsapp_from and settings.twilio_account_sid:
        url = f"https://api.twilio.com/2010-04-01/Accounts/{settings.twilio_account_sid}/Messages.json"
        async with httpx.AsyncClient(timeout=30) as client:
            resp = await client.post(
                url,
                auth=(settings.twilio_account_sid, settings.twilio_auth_token),
                data={
                    "From": settings.twilio_whatsapp_from
                    if settings.twilio_whatsapp_from.startswith("whatsapp:")
                    else f"whatsapp:{settings.twilio_whatsapp_from}",
                    "To": f"whatsapp:{to}",
                    "Body": body,
                },
            )
            resp.raise_for_status()
            return str(resp.json().get("sid") or "twilio-wa")

    if settings.meta_whatsapp_token and settings.meta_whatsapp_phone_id:
        url = f"https://graph.facebook.com/v21.0/{settings.meta_whatsapp_phone_id}/messages"
        payload = {
            "messaging_product": "whatsapp",
            "to": "".join(ch for ch in to if ch.isdigit()),
            "type": "text",
            "text": {"body": body, "preview_url": True},
        }
        async with httpx.AsyncClient(timeout=30) as client:
            resp = await client.post(
                url,
                headers={"Authorization": f"Bearer {settings.meta_whatsapp_token}"},
                json=payload,
            )
            resp.raise_for_status()
            data = resp.json()
        messages = data.get("messages") or [{}]
        return str(messages[0].get("id") or "meta-wa")

    raise RuntimeError("WhatsApp is not configured (Twilio or Meta Cloud API)")
