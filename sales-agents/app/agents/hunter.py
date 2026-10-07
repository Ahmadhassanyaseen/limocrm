from __future__ import annotations

import csv
import io
import json
import logging
import re
from urllib.parse import urljoin, urlparse

import httpx
from bs4 import BeautifulSoup
from sqlalchemy.orm import Session

from app.config import get_settings
from app.models import Contact, Prospect, utcnow

log = logging.getLogger("sales-agents.hunter")

LIMO_KEYWORDS = (
    "limo",
    "limousine",
    "chauffeur",
    "black car",
    "ground transport",
    "party bus",
    "sedan service",
    "airport transfer",
    "coach",
)
US_CA = {"us", "usa", "united states", "ca", "canada", "united states of america"}

EMAIL_RE = re.compile(r"[A-Z0-9._%+\-]+@[A-Z0-9.\-]+\.[A-Z]{2,}", re.I)
SOCIAL_HOSTS = {
    "linkedin.com": "linkedin_url",
    "instagram.com": "instagram_url",
    "facebook.com": "facebook_url",
    "fb.com": "facebook_url",
    "twitter.com": "x_url",
    "x.com": "x_url",
}


def _norm_url(url: str) -> str:
    url = (url or "").strip()
    if not url:
        return ""
    if not url.startswith(("http://", "https://")):
        url = "https://" + url
    return url.rstrip("/")


def _domain(url: str) -> str:
    host = urlparse(_norm_url(url)).netloc.lower()
    if host.startswith("www."):
        host = host[4:]
    return host


def score_prospect(prospect: Prospect, research: dict, has_email: bool) -> tuple[float, str]:
    score = 0.0
    notes: list[str] = []
    blob = " ".join(
        [
            prospect.company,
            prospect.website,
            prospect.city,
            prospect.region,
            json.dumps(research),
        ]
    ).lower()
    if any(k in blob for k in LIMO_KEYWORDS):
        score += 40
        notes.append("limo/ground-transport language")
    if prospect.website:
        score += 15
        notes.append("has website")
    if has_email:
        score += 20
        notes.append("has email")
    country = (prospect.country or "").lower()
    region = (prospect.region or "").lower()
    if country in US_CA or region in {
        "ca",
        "california",
        "ontario",
        "texas",
        "florida",
        "ny",
        "new york",
    }:
        score += 15
        notes.append("US/CA geography")
    socials = any(research.get(k) for k in ("linkedin_url", "instagram_url", "facebook_url", "x_url"))
    if socials:
        score += 10
        notes.append("social profiles found")
    return min(score, 100), "; ".join(notes)


def upsert_contact(db: Session, prospect: Prospect, **fields) -> Contact:
    email = (fields.get("email") or "").strip().lower()
    existing = None
    if email:
        existing = (
            db.query(Contact)
            .filter(Contact.prospect_id == prospect.id, Contact.email == email)
            .first()
        )
    if existing is None and not email:
        existing = (
            db.query(Contact)
            .filter(Contact.prospect_id == prospect.id, Contact.is_primary.is_(True))
            .first()
        )
    if existing is None:
        existing = Contact(prospect_id=prospect.id, is_primary=True)
        db.add(existing)
    for key, value in fields.items():
        if value in (None, ""):
            continue
        if key == "email":
            value = str(value).lower().strip()
        setattr(existing, key, value)
    db.flush()
    return existing


def import_csv(db: Session, text: str) -> dict:
    reader = csv.DictReader(io.StringIO(text))
    created = 0
    skipped = 0
    if not reader.fieldnames:
        return {"created": 0, "skipped": 0, "error": "CSV has no header row"}

    def g(row: dict, *names: str) -> str:
        lower = {(k or "").strip().lower(): (v or "").strip() for k, v in row.items()}
        for name in names:
            if name in lower and lower[name]:
                return lower[name]
        return ""

    for row in reader:
        company = g(row, "company", "name", "business")
        if not company:
            skipped += 1
            continue
        website = _norm_url(g(row, "website", "url", "site"))
        existing = None
        if website:
            existing = db.query(Prospect).filter(Prospect.website == website).first()
        if existing is None:
            existing = (
                db.query(Prospect)
                .filter(Prospect.company == company, Prospect.city == g(row, "city"))
                .first()
            )
        if existing is None:
            existing = Prospect(company=company, source="csv")
            db.add(existing)
            created += 1
        else:
            skipped += 1
        existing.city = g(row, "city") or existing.city
        existing.region = g(row, "region", "state", "province") or existing.region
        existing.country = g(row, "country") or existing.country or "US"
        existing.website = website or existing.website
        existing.phone = g(row, "phone", "tel") or existing.phone
        existing.stage = existing.stage or "new"
        db.flush()
        upsert_contact(
            db,
            existing,
            name=g(row, "contact", "contact_name", "owner"),
            role=g(row, "role", "title"),
            email=g(row, "email"),
            phone=g(row, "phone", "mobile") or existing.phone,
            linkedin_url=g(row, "linkedin", "linkedin_url"),
            instagram_url=g(row, "instagram", "instagram_url"),
            facebook_url=g(row, "facebook", "facebook_url"),
            x_url=g(row, "x", "twitter", "x_url"),
        )
        research = {"imported": True}
        has_email = bool(g(row, "email"))
        existing.icp_score, existing.icp_notes = score_prospect(existing, research, has_email)
        existing.updated_at = utcnow()
    db.commit()
    return {"created": created, "skipped": skipped}


async def places_search(db: Session, query: str, max_results: int = 12) -> dict:
    settings = get_settings()
    if not settings.google_places_api_key:
        return {"error": "GOOGLE_PLACES_API_KEY is not set", "created": 0}
    created = 0
    async with httpx.AsyncClient(timeout=30) as client:
        resp = await client.get(
            "https://maps.googleapis.com/maps/api/place/textsearch/json",
            params={"query": query, "key": settings.google_places_api_key},
        )
        resp.raise_for_status()
        data = resp.json()
        if data.get("status") not in {"OK", "ZERO_RESULTS"}:
            return {"error": data.get("error_message") or data.get("status"), "created": 0}
        for place in (data.get("results") or [])[:max_results]:
            place_id = place.get("place_id") or ""
            if place_id:
                exists = db.query(Prospect).filter(Prospect.google_place_id == place_id).first()
                if exists:
                    continue
            details: dict = {}
            if place_id:
                det = await client.get(
                    "https://maps.googleapis.com/maps/api/place/details/json",
                    params={
                        "place_id": place_id,
                        "fields": "name,website,formatted_phone_number,formatted_address,address_components",
                        "key": settings.google_places_api_key,
                    },
                )
                if det.status_code == 200:
                    details = det.json().get("result") or {}
            company = details.get("name") or place.get("name") or "Unknown"
            address = details.get("formatted_address") or place.get("formatted_address") or ""
            city, region, country = _parse_address_components(
                details.get("address_components") or [], address
            )
            prospect = Prospect(
                company=company,
                city=city,
                region=region,
                country=country or "US",
                website=_norm_url(details.get("website") or ""),
                phone=details.get("formatted_phone_number") or "",
                google_place_id=place_id,
                source="places",
                stage="new",
            )
            db.add(prospect)
            db.flush()
            upsert_contact(db, prospect, phone=prospect.phone)
            created += 1
    db.commit()
    return {"created": created, "query": query}


def _parse_address_components(components: list, fallback: str) -> tuple[str, str, str]:
    city = region = country = ""
    for comp in components:
        types = set(comp.get("types") or [])
        if "locality" in types:
            city = comp.get("long_name") or ""
        if "administrative_area_level_1" in types:
            region = comp.get("short_name") or ""
        if "country" in types:
            country = comp.get("short_name") or ""
    if not city and fallback:
        parts = [p.strip() for p in fallback.split(",")]
        if len(parts) >= 2:
            city = parts[-3] if len(parts) >= 3 else parts[0]
    return city, region, country


async def enrich_prospect(db: Session, prospect: Prospect) -> dict:
    research: dict = {}
    website = _norm_url(prospect.website)
    if website:
        fetched = await fetch_website(website)
        research.update(fetched)
        if fetched.get("emails"):
            for email in fetched["emails"][:3]:
                upsert_contact(db, prospect, email=email, name=prospect.company)
        for key in ("linkedin_url", "instagram_url", "facebook_url", "x_url"):
            if fetched.get(key):
                primary = (
                    db.query(Contact)
                    .filter(Contact.prospect_id == prospect.id, Contact.is_primary.is_(True))
                    .first()
                )
                if primary:
                    setattr(primary, key, fetched[key])
        if fetched.get("phone") and not prospect.phone:
            prospect.phone = fetched["phone"]

    domain = _domain(website)
    hunter_emails = await hunter_domain_search(domain) if domain else []
    if hunter_emails:
        research["hunter_emails"] = hunter_emails
        for item in hunter_emails[:5]:
            upsert_contact(
                db,
                prospect,
                email=item.get("email") or "",
                name=" ".join(filter(None, [item.get("first_name"), item.get("last_name")])),
                role=item.get("position") or "",
            )

    contacts = db.query(Contact).filter(Contact.prospect_id == prospect.id).all()
    has_email = any(c.email for c in contacts)
    prospect.icp_score, prospect.icp_notes = score_prospect(prospect, research, has_email)
    prospect.research_json = json.dumps(research, default=str)
    prospect.stage = "researched" if prospect.stage in {"new", "researched"} else prospect.stage
    prospect.next_action = "Draft outreach"
    prospect.updated_at = utcnow()
    db.commit()
    return research


async def fetch_website(url: str) -> dict:
    headers = {"User-Agent": "LimoGenResearchBot/1.0 (+https://limogen.io)"}
    out: dict = {"url": url, "emails": [], "title": "", "description": ""}
    try:
        async with httpx.AsyncClient(timeout=12, follow_redirects=True, headers=headers) as client:
            resp = await client.get(url)
            resp.raise_for_status()
            html = resp.text[:400_000]
    except Exception as exc:
        out["error"] = str(exc)
        return out

    soup = BeautifulSoup(html, "lxml")
    title = soup.title.string.strip() if soup.title and soup.title.string else ""
    desc = ""
    tag = soup.find("meta", attrs={"name": "description"})
    if tag and tag.get("content"):
        desc = tag["content"].strip()
    out["title"] = title[:300]
    out["description"] = desc[:500]

    emails = sorted({m.group(0).lower() for m in EMAIL_RE.finditer(html)})
    emails = [e for e in emails if not e.endswith((".png", ".jpg", ".gif", ".webp"))]
    out["emails"] = emails[:8]

    for a in soup.find_all("a", href=True):
        href = urljoin(url, a["href"])
        host = urlparse(href).netloc.lower()
        if host.startswith("www."):
            host = host[4:]
        for domain, field in SOCIAL_HOSTS.items():
            if host.endswith(domain) and not out.get(field):
                out[field] = href.split("?")[0]
        if href.lower().startswith("tel:") and not out.get("phone"):
            out["phone"] = href[4:]
        if href.lower().startswith("mailto:"):
            mail = href[7:].split("?")[0].strip()
            if mail and mail.lower() not in out["emails"]:
                out["emails"].append(mail.lower())

    blob = f"{title} {desc} {soup.get_text(' ', strip=True)[:4000]}".lower()
    out["signals"] = [k for k in LIMO_KEYWORDS if k in blob]
    out["has_quote_form"] = any(w in blob for w in ("get a quote", "book now", "instant quote", "reserve"))
    return out


async def hunter_domain_search(domain: str) -> list[dict]:
    settings = get_settings()
    if not settings.hunter_api_key or not domain:
        return []
    try:
        async with httpx.AsyncClient(timeout=20) as client:
            resp = await client.get(
                "https://api.hunter.io/v2/domain-search",
                params={"domain": domain, "api_key": settings.hunter_api_key, "limit": 5},
            )
            resp.raise_for_status()
            data = resp.json()
        return data.get("data", {}).get("emails") or []
    except Exception:
        log.exception("Hunter.io lookup failed for %s", domain)
        return []
