from __future__ import annotations

import json
import logging

import httpx

from app.config import get_settings

log = logging.getLogger("sales-agents.llm")


async def complete(system: str, user: str, *, json_mode: bool = False) -> str:
    settings = get_settings()
    if not settings.openai_api_key:
        return ""

    headers = {
        "Authorization": f"Bearer {settings.openai_api_key}",
        "Content-Type": "application/json",
    }
    payload: dict = {
        "model": settings.openai_model,
        "messages": [
            {"role": "system", "content": system},
            {"role": "user", "content": user},
        ],
        "temperature": 0.4,
    }
    if json_mode:
        payload["response_format"] = {"type": "json_object"}

    url = settings.openai_base_url.rstrip("/") + "/chat/completions"
    try:
        async with httpx.AsyncClient(timeout=60) as client:
            response = await client.post(url, headers=headers, json=payload)
            response.raise_for_status()
            data = response.json()
        return (data["choices"][0]["message"]["content"] or "").strip()
    except Exception:
        log.exception("LLM call failed")
        return ""


async def complete_json(system: str, user: str) -> dict:
    raw = await complete(system, user, json_mode=True)
    if not raw:
        return {}
    try:
        return json.loads(raw)
    except json.JSONDecodeError:
        start = raw.find("{")
        end = raw.rfind("}")
        if start >= 0 and end > start:
            try:
                return json.loads(raw[start : end + 1])
            except json.JSONDecodeError:
                return {}
        return {}
