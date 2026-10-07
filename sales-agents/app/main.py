from __future__ import annotations

from contextlib import asynccontextmanager
from pathlib import Path

from fastapi import FastAPI, Request
from fastapi.responses import JSONResponse, RedirectResponse
from fastapi.staticfiles import StaticFiles
from fastapi.templating import Jinja2Templates

from app.config import get_settings
from app.db import init_db
from app.knowledge import load_knowledge
from app.scheduler import scheduler, start_scheduler
from app.web import api, pages

APP_DIR = Path(__file__).resolve().parent


@asynccontextmanager
async def lifespan(_app: FastAPI):
    init_db()
    load_knowledge()
    start_scheduler()
    yield
    if scheduler.running:
        scheduler.shutdown(wait=False)


app = FastAPI(title="LimoGen Sales Agents", lifespan=lifespan)
app.mount("/static", StaticFiles(directory=str(APP_DIR / "static")), name="static")
templates = Jinja2Templates(directory=str(APP_DIR / "templates"))
pages.templates = templates
app.include_router(pages.router)
app.include_router(api.router)


@app.middleware("http")
async def operator_gate(request: Request, call_next):
    settings = get_settings()
    path = request.url.path
    if not settings.operator_token:
        return await call_next(request)
    if path.startswith("/static") or path in {"/unsubscribe", "/health"}:
        return await call_next(request)
    token = request.query_params.get("token") or request.headers.get("x-operator-token") or ""
    cookie = request.cookies.get("operator_token") or ""
    if token == settings.operator_token or cookie == settings.operator_token:
        response = await call_next(request)
        if token == settings.operator_token:
            response.set_cookie("operator_token", token, httponly=True, samesite="lax")
        return response
    if path.startswith("/api"):
        return JSONResponse({"error": "unauthorized"}, status_code=401)
    return RedirectResponse("/health", status_code=302)


@app.get("/health")
def health():
    kb = load_knowledge()
    return {
        "ok": True,
        "brand": "LimoGen",
        "pains": len(kb.pains),
        "do_not_claim": len(kb.do_not_claim),
    }
