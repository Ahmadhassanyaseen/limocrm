"""Load LimoGen pitch corpus and brand rules for the agent team."""

from __future__ import annotations

from dataclasses import dataclass, field
from pathlib import Path
import re

from app.config import REPO_ROOT

BRAND_RULES = """
Public brand is LimoGen (capital L, capital G, one word).
Never write Limogen, Limo Gen, or LimoCRM in outbound copy.
Do not invent features. Do not pitch: vendors, standalone quotes module, calendar,
campaign builder, chat, coupons, vendor tiers, audit log, commissions, employee analytics.
ICP: US/CA limousine and ground-transport operators still running Gmail + group chat + Venmo.
Offer: instant-quote widget, workflows, lead → quote → sign → pay, lower cost than stitching tools.
CTAs: explore demo link (tracked send_id), optional calendar link, never share demo passwords.
Tone: operator-to-operator, specific, short. No hype, no fake urgency, no spam bait.
""".strip()

PILLARS = [
    "Instant quotes — branded booking widget on their site turns visitors into leads without phone tag.",
    "Automation — workflows handle follow-ups, status changes, and repetitive tasks.",
    "Low cost — purpose-built for limo ops instead of stitching enterprise CRM + docs + payments.",
]


@dataclass
class Pain:
    number: int
    title: str
    problem: str
    without: str
    solution: str
    category: str


@dataclass
class KnowledgeBase:
    brand_rules: str = BRAND_RULES
    pillars: list[str] = field(default_factory=lambda: list(PILLARS))
    short_pitch: str = ""
    module_summary: str = ""
    pains: list[Pain] = field(default_factory=list)
    do_not_claim: list[str] = field(default_factory=list)
    demo_order: list[str] = field(default_factory=list)

    def prompt_block(self, max_pains: int = 8) -> str:
        pain_lines = []
        for pain in self.pains[:max_pains]:
            pain_lines.append(
                f"- #{pain.number} {pain.title}: {pain.problem} → {pain.solution}"
            )
        banned = ", ".join(self.do_not_claim) or "(see brand rules)"
        return (
            f"{self.brand_rules}\n\n"
            f"Three pillars:\n- " + "\n- ".join(self.pillars) + "\n\n"
            f"Short pitch:\n{self.short_pitch}\n\n"
            f"Operator pains (use one that fits the prospect):\n"
            + "\n".join(pain_lines)
            + f"\n\nDo not claim: {banned}\n"
        )

    def pick_pain(self, research_text: str) -> Pain:
        text = (research_text or "").lower()
        keyword_map = [
            (["widget", "website", "quote", "booking", "form"], 4),
            (["gmail", "group chat", "spreadsheet", "venmo", "inbox"], 1),
            (["report", "pipeline", "dashboard", "numbers"], 3),
            (["sign", "agreement", "docusign", "pdf"], 10),
            (["pay", "stripe", "paypal", "venmo"], 19),
            (["fleet", "vehicle", "limo"], 16),
            (["email", "smtp", "template"], 22),
            (["follow", "workflow"], 15),
        ]
        for keys, num in keyword_map:
            if any(k in text for k in keys):
                found = next((p for p in self.pains if p.number == num), None)
                if found:
                    return found
        return self.pains[0] if self.pains else Pain(1, "CRM is a group chat", "Bookings die in the gaps.", "", "Lead → quote → sign → pay in one system.", "Ops chaos")


_KB: KnowledgeBase | None = None


def _read(path: Path) -> str:
    if not path.exists():
        return ""
    return path.read_text(encoding="utf-8", errors="replace")


def _parse_pains(issues_md: str) -> tuple[list[Pain], list[str]]:
    pains: list[Pain] = []
    category = ""
    current: dict | None = None

    def flush():
        nonlocal current
        if current and current.get("problem"):
            pains.append(
                Pain(
                    number=int(current["number"]),
                    title=current["title"],
                    problem=current.get("problem", ""),
                    without=current.get("without", ""),
                    solution=current.get("solution", ""),
                    category=current.get("category", ""),
                )
            )
        current = None

    for line in issues_md.splitlines():
        if line.startswith("## ") and "Do not claim" not in line and "How to use" not in line:
            flush()
            category = line[3:].strip()
            continue
        m = re.match(r"^### (\d+)\.\s+(.+)$", line)
        if m:
            flush()
            current = {
                "number": m.group(1),
                "title": m.group(2).strip(),
                "category": category,
            }
            continue
        if current is None:
            continue
        for key, prefix in (
            ("problem", "- **Problem:**"),
            ("without", "- **Without LimoGen:**"),
            ("solution", "- **Solution:**"),
        ):
            if line.startswith(prefix):
                current[key] = line[len(prefix) :].strip()
    flush()

    do_not: list[str] = []
    in_ban = False
    for line in issues_md.splitlines():
        if line.startswith("## Do not claim"):
            in_ban = True
            continue
        if in_ban and line.startswith("## "):
            break
        if in_ban and line.startswith("- "):
            do_not.append(line[2:].strip())
    return pains, do_not


def _extract_section(md: str, heading: str) -> str:
    pattern = rf"^## {re.escape(heading)}\s*$"
    parts = re.split(pattern, md, maxsplit=1, flags=re.M)
    if len(parts) < 2:
        return ""
    rest = re.split(r"^## ", parts[1], maxsplit=1, flags=re.M)[0]
    return rest.strip()


def load_knowledge() -> KnowledgeBase:
    global _KB
    if _KB is not None:
        return _KB

    pitch = _read(REPO_ROOT / "docs" / "CLIENT_PITCH.md")
    modules = _read(REPO_ROOT / "docs" / "CLIENT_PITCH_MODULES.md")
    issues = _read(REPO_ROOT / "posts" / "ISSUES.md")

    pains, do_not = _parse_pains(issues)
    short = _extract_section(pitch, "Short version (3–4 sentences)")
    short = re.sub(r"\*\*LimoCRM\*\*", "LimoGen", short)
    short = short.replace("LimoCRM", "LimoGen")

    demo = [
        "Dashboard — pipeline and wins at a glance",
        "Lead record — quote email → agreement link → payment",
        "Fleet + pricing — vehicles tied to quotes",
        "Integrations — widget embed on their site",
        "Admin — roles, payment methods, email templates",
    ]

    module_bits = []
    for title in (
        "Command center",
        "Sales pipeline",
        "Fleet & pricing",
        "Revenue: agreements & payments",
        "Communications",
        "Growth & automation",
        "Administration & account",
    ):
        block = _extract_section(modules, title)
        if block:
            module_bits.append(f"{title}: " + " ".join(block.split())[:500])

    _KB = KnowledgeBase(
        short_pitch=short or "LimoGen is an all-in-one operating system for limousine companies: website inquiry → signed, paid booking in one workspace.",
        module_summary="\n".join(module_bits),
        pains=pains,
        do_not_claim=do_not,
        demo_order=demo,
    )
    return _KB


def reload_knowledge() -> KnowledgeBase:
    global _KB
    _KB = None
    return load_knowledge()
