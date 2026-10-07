"""Hard rules: suppression, CAN-SPAM, opt-in, rate limits. Not an LLM."""

from __future__ import annotations

import hashlib
import hmac
from datetime import datetime, timedelta, timezone
from urllib.parse import quote

from sqlalchemy import func, or_
from sqlalchemy.orm import Session

from app.config import get_settings
from app.models import Consent, Message, Suppression

SOCIAL_CHANNELS = ("linkedin", "instagram", "facebook", "x")
OPT_IN_CHANNELS = ("sms", "whatsapp")
EMAIL_CHANNEL = "email"


def unsubscribe_token(email: str) -> str:
    settings = get_settings()
    digest = hmac.new(
        settings.secret_key.encode(),
        email.lower().strip().encode(),
        hashlib.sha256,
    ).hexdigest()
    return digest[:32]


def unsubscribe_url(email: str) -> str:
    settings = get_settings()
    token = unsubscribe_token(email)
    return f"{settings.public_base_url.rstrip('/')}/unsubscribe?email={quote(email)}&token={token}"


def verify_unsub_token(email: str, token: str) -> bool:
    return hmac.compare_digest(unsubscribe_token(email), token or "")


def is_suppressed(db: Session, *, email: str = "", phone: str = "") -> Suppression | None:
    clauses = []
    if email:
        clauses.append(func.lower(Suppression.email) == email.lower().strip())
    if phone:
        digits = _digits(phone)
        if digits:
            clauses.append(Suppression.phone == digits)
    if not clauses:
        return None
    return db.query(Suppression).filter(or_(*clauses)).first()


def suppress(db: Session, *, email: str = "", phone: str = "", reason: str) -> Suppression:
    row = Suppression(email=(email or "").lower().strip(), phone=_digits(phone), reason=reason)
    db.add(row)
    db.flush()
    return row


def has_consent(db: Session, contact_id: int, channel: str) -> Consent | None:
    return (
        db.query(Consent)
        .filter(Consent.contact_id == contact_id, Consent.channel == channel)
        .order_by(Consent.granted_at.desc())
        .first()
    )


def sent_today(db: Session, channel: str) -> int:
    start = datetime.now(timezone.utc).replace(hour=0, minute=0, second=0, microsecond=0)
    return (
        db.query(Message)
        .filter(
            Message.channel == channel,
            Message.direction == "outbound",
            Message.status == "sent",
            Message.sent_at >= start,
        )
        .count()
    )


def under_daily_limit(db: Session, channel: str) -> bool:
    settings = get_settings()
    if channel == "email":
        return sent_today(db, "email") < settings.daily_email_limit
    if channel in OPT_IN_CHANNELS:
        return sent_today(db, channel) < settings.daily_sms_limit
    return True


def canspam_footer(email: str) -> str:
    settings = get_settings()
    url = unsubscribe_url(email)
    return (
        f"\n\n--\n{settings.sender_name}\n"
        f"{settings.company_legal_name}\n"
        f"{settings.mailing_address}\n\n"
        f"Unsubscribe: {url}\n"
    )


def html_footer(email: str) -> str:
    settings = get_settings()
    url = unsubscribe_url(email)
    return (
        "<hr style='border:none;border-top:1px solid #1e2a44;margin:24px 0'>"
        f"<p style='color:#8b9bb4;font-size:12px;line-height:1.5'>"
        f"{settings.sender_name}<br>{settings.company_legal_name}<br>"
        f"{settings.mailing_address}<br>"
        f"<a href='{url}' style='color:#38bdf8'>Unsubscribe</a></p>"
    )


def followup_offsets() -> list[timedelta]:
    settings = get_settings()
    days = []
    for part in settings.email_followup_days.split(","):
        part = part.strip()
        if part.isdigit():
            days.append(timedelta(days=int(part)))
    return days or [timedelta(days=3), timedelta(days=7), timedelta(days=14)]


def _digits(phone: str) -> str:
    return "".join(ch for ch in (phone or "") if ch.isdigit())
