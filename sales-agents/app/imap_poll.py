from __future__ import annotations

import email
import imaplib
import logging
from email.header import decode_header
from email.utils import parseaddr

from sqlalchemy.orm import Session

from app.agents.inbox import extract_email, find_contact_by_email, ingest
from app.config import get_settings
from app.db import SessionLocal
from app.models import ImapCursor, utcnow

log = logging.getLogger("sales-agents.imap")


def _decode(value) -> str:
    if not value:
        return ""
    if isinstance(value, str):
        return value
    parts = decode_header(value)
    out = []
    for text, enc in parts:
        if isinstance(text, bytes):
            out.append(text.decode(enc or "utf-8", errors="replace"))
        else:
            out.append(text)
    return "".join(out)


def _body(msg: email.message.Message) -> str:
    if msg.is_multipart():
        for part in msg.walk():
            ctype = part.get_content_type()
            if ctype == "text/plain" and "attachment" not in (part.get("Content-Disposition") or ""):
                payload = part.get_payload(decode=True) or b""
                charset = part.get_content_charset() or "utf-8"
                return payload.decode(charset, errors="replace")
        return ""
    payload = msg.get_payload(decode=True) or b""
    charset = msg.get_content_charset() or "utf-8"
    return payload.decode(charset, errors="replace")


async def poll_imap() -> dict:
    settings = get_settings()
    if not (settings.imap_host and settings.imap_user and settings.imap_password):
        return {"skipped": True, "reason": "IMAP not configured"}

    db: Session = SessionLocal()
    matched = 0
    unmatched = 0
    try:
        cursor = db.query(ImapCursor).first()
        if cursor is None:
            cursor = ImapCursor(last_uid=0)
            db.add(cursor)
            db.commit()

        mail = imaplib.IMAP4_SSL(settings.imap_host, settings.imap_port)
        mail.login(settings.imap_user, settings.imap_password)
        mail.select(settings.imap_folder)
        criteria = f"(UID {cursor.last_uid + 1}:*)" if cursor.last_uid else "UNSEEN"
        typ, data = mail.uid("SEARCH", None, criteria)
        if typ != "OK":
            return {"error": "IMAP search failed"}
        uids = [int(x) for x in (data[0] or b"").split() if x]
        last = cursor.last_uid
        for uid in uids:
            last = max(last, uid)
            typ, fetched = mail.uid("FETCH", str(uid), "(RFC822)")
            if typ != "OK" or not fetched or fetched[0] is None:
                continue
            raw = fetched[0][1]
            parsed = email.message_from_bytes(raw)
            from_email = extract_email(parseaddr(parsed.get("From", ""))[1] or parsed.get("From", ""))
            subject = _decode(parsed.get("Subject"))
            body = _body(parsed)
            contact = find_contact_by_email(db, from_email)
            if not contact:
                unmatched += 1
                continue
            await ingest(db, contact, "email", body, subject=subject)
            matched += 1
        cursor.last_uid = last
        cursor.updated_at = utcnow()
        db.commit()
        mail.logout()
        return {"matched": matched, "unmatched": unmatched, "last_uid": last}
    except Exception:
        log.exception("IMAP poll failed")
        db.rollback()
        return {"error": "IMAP poll failed"}
    finally:
        db.close()
