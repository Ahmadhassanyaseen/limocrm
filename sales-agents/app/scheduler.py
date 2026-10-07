from __future__ import annotations

import logging

from apscheduler.schedulers.asyncio import AsyncIOScheduler

from app.agents.sequencer import tick
from app.config import get_settings
from app.db import SessionLocal
from app.imap_poll import poll_imap

log = logging.getLogger("sales-agents.scheduler")
scheduler = AsyncIOScheduler()


async def _tick_job() -> None:
    db = SessionLocal()
    try:
        result = await tick(db)
        log.info("sequencer tick %s", result)
    except Exception:
        log.exception("sequencer tick failed")
        db.rollback()
    finally:
        db.close()


async def _imap_job() -> None:
    result = await poll_imap()
    log.info("imap poll %s", result)


def start_scheduler() -> None:
    settings = get_settings()
    if settings.disable_scheduler:
        return
    if scheduler.running:
        return
    scheduler.configure(timezone=settings.timezone)
    scheduler.add_job(_tick_job, "interval", minutes=15, id="sequencer", replace_existing=True)
    scheduler.add_job(_imap_job, "interval", minutes=2, id="imap", replace_existing=True)
    scheduler.start()
