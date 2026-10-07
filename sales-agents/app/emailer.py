from __future__ import annotations

import logging
import smtplib
from email.message import EmailMessage

import httpx

from app.config import get_settings
from app.compliance import canspam_footer, html_footer

log = logging.getLogger("sales-agents.emailer")


def _plain_and_html(to_email: str, body: str) -> tuple[str, str]:
    plain = body.rstrip() + canspam_footer(to_email)
    paragraphs = "".join(
        f"<p style='margin:0 0 12px;line-height:1.55'>{line}</p>" if line.strip() else "<br>"
        for line in body.splitlines()
    )
    html = (
        "<div style='font-family:Segoe UI,Helvetica,Arial,sans-serif;color:#0b1220;max-width:640px'>"
        f"{paragraphs}{html_footer(to_email)}</div>"
    )
    return plain, html


async def send_email(to_email: str, subject: str, body: str) -> str:
    settings = get_settings()
    if not to_email:
        raise ValueError("Missing recipient email")
    plain, html = _plain_and_html(to_email, body)

    if settings.resend_api_key:
        return await _send_resend(to_email, subject, plain, html)
    if settings.smtp_host:
        return _send_smtp(to_email, subject, plain, html)
    raise RuntimeError("No email provider configured (set RESEND_API_KEY or SMTP_HOST)")


async def _send_resend(to_email: str, subject: str, plain: str, html: str) -> str:
    settings = get_settings()
    payload = {
        "from": f"{settings.sender_name} <{settings.sender_email}>",
        "to": [to_email],
        "subject": subject,
        "text": plain,
        "html": html,
    }
    async with httpx.AsyncClient(timeout=30) as client:
        response = await client.post(
            "https://api.resend.com/emails",
            headers={"Authorization": f"Bearer {settings.resend_api_key}"},
            json=payload,
        )
        response.raise_for_status()
        data = response.json()
    return str(data.get("id") or "resend")


def _send_smtp(to_email: str, subject: str, plain: str, html: str) -> str:
    settings = get_settings()
    msg = EmailMessage()
    msg["Subject"] = subject
    msg["From"] = f"{settings.sender_name} <{settings.sender_email}>"
    msg["To"] = to_email
    msg.set_content(plain)
    msg.add_alternative(html, subtype="html")

    with smtplib.SMTP(settings.smtp_host, settings.smtp_port, timeout=30) as smtp:
        if settings.smtp_starttls:
            smtp.starttls()
        if settings.smtp_user:
            smtp.login(settings.smtp_user, settings.smtp_password)
        smtp.send_message(msg)
    return "smtp-ok"
