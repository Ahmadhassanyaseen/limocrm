# Post 22 — Quotes from personal Gmail

**Issue:** [#22 in ISSUES.md](ISSUES.md)  
**Pillar:** Pain / Relatable  
**Angle:** A $12,000 wedding quote from a Gmail address looks like a side hustle. Spam agrees.  
**CTA keyword:** SMTP  
**Format:** Static still — 4:5 feed (`1080×1350`). No video.  
**Asset:** [`images/22-personal-gmail.png`](images/22-personal-gmail.png) *(not generated yet)*

---

## Problem

Outreach looks unprofessional and lands in spam.

`mike.limos@gmail.com` sends the quote. Clients don’t trust it. Filters bury it.

## Solution

Outbound SMTP from the operator domain — quotes and agreements send as the brand.

---

## Image spec

**Aspect:** 4:5 (`1080×1350`) for Instagram / Facebook feed. Optional 1:1 (`1080×1080`) for X / LinkedIn.

**Brand accent:** `linear-gradient(90deg, #1d4ed8 0%, #38bdf8 100%)`  
**Atmosphere:** Dark navy studio, sharp sans, lots of negative space, high mobile legibility.

**Composition (locked — solution only, no split):**

Same style as [`06-otp-funnel.png`](images/06-otp-funnel.png) / [`07-same-day-demo.png`](images/07-same-day-demo.png): full-frame 3D miniature diorama, photoreal 3D people, no left/right problem-vs-solution.

```
Full-width headline
GMAIL ISN’T YOUR BRAND.

Center: 3D miniature world
• Giant LimoGen Email Settings (SMTP) screen as architecture
• 3D people: connect domain, test connection, send as the brand
• Quotes leaving from you@yourfleet.com — not a personal inbox

Bottom CTA pill: COMMENT SMTP
```

**On-image text (exact spelling — do not paraphrase):**

| Zone | Text |
|------|------|
| Headline | GMAIL ISN’T YOUR BRAND. |
| Product mark | LimoGen |
| Subline | DOMAIN · SEND · TRUST |
| CTA pill | COMMENT SMTP |

**On-image text rules:**

- “LimoGen” — capital L, capital G, one word. Never “Limo Gen”, “Limogen”, or “LimoCRM”.
- Apostrophe in `ISN’T` is required.
- CTA is exactly `COMMENT SMTP` (all caps).
- No hashtags, no “link in bio”, no emoji, no extra words on the image.
- Solution only — no personal Gmail inbox as the hero, no “THE OLD WAY.”
- Do not show real SMTP passwords, API keys, or live secrets. Blur credential fields or use empty placeholders.
- Do not put a real personal email address on the image.

**Visual reference:** [`docs/brochure/screenshots/email-settings.png`](../docs/brochure/screenshots/email-settings.png). Recolor pink/magenta accents to blue→sky.

---

## Gemini / ChatGPT / Midjourney — locked still prompt

```
Create a single premium editorial still for LimoGen, a CRM for limousine / ground transportation operators. Image-only. No video. No split-screen. No before/after. THE ENTIRE FRAME IS THE SOLUTION.

ASPECT: 4:5 portrait, 1080×1350.

STYLE: cinematic 3D miniature diorama / tilt-shift tiny world. Octane / Cinema 4D quality. Photoreal 3D people at miniature scale. Dark navy studio (#0d1117), blue→sky accent (#1d4ed8 → #38bdf8). Match prior LimoGen miniature stills. Creative twist: sending email feels like a branded dispatch tower — domain connected, test passed, mail leaving as the company.

SCENE:
- Giant standing screen: clean LimoGen Email Settings — outbound SMTP account, domain, connection test success. Blue→sky accents. Brand: LimoGen only.
- Three photoreal miniature professionals:
  1) Admin connecting the domain / SMTP on the giant screen.
  2) Staff running a connection test — subtle “CONNECTED” badge (not emoji).
  3) Sales sending a quote that leaves as the brand (generic you@yourfleet.com style — no real personal Gmail address).
- Optional tiny 3D envelope / domain-nameplate object. Subtle. Premium. Not cartoon.
- Do NOT show personal Gmail inboxes, spam folders, passwords, or “THE OLD WAY.”
- Do NOT display real SMTP passwords or live secrets; blur credential fields.

TOP headline exactly: GMAIL ISN’T YOUR BRAND.
Product mark: LimoGen
Under diorama exactly: DOMAIN · SEND · TRUST
BOTTOM pill exactly: COMMENT SMTP

SPELLING: LimoGen only. ISN’T with apostrophe. No hashtags, emoji, watermark, purple glow, “THE OLD WAY.”
```

**Attach (when generating):** `docs/brochure/screenshots/email-settings.png`

---

## Instagram

**Visual:** 4:5 3D miniature still (SMTP / email settings, solution only).

**Hook (on-screen):** GMAIL ISN’T YOUR BRAND.

**Caption**

$12,000 wedding quote.  
From mike.limos@gmail.com.

The planner doesn’t open it.  
The filter already voted.

That’s not “casual.” That’s a costume.

LimoGen Email Settings: connect your domain. Quotes and agreements send as the brand.

Comment SMTP · link in bio.

`#limobusiness #limousine #email #chauffeurlife #groundtransportation #blackcar #fleetowner #salesops #dispatchlife`

**CTA:** Comment SMTP / link in bio

---

## Facebook

**Visual:** Same 3D miniature still.

**Hook:** If the quote comes from Gmail, the client assumes the company does too.

**Copy**

Personal Gmail looks like a side hustle and lands like spam. Clients don’t trust it. Filters bury it. Your $12k quote never had a chance.

LimoGen outbound SMTP: connect the operator domain. Quotes and agreements leave as you — the brand, not a personal inbox.

Comment SMTP or DM us.

**CTA:** Comment SMTP  
**Tags:** `#limobusiness #email #fleetowner`

---

## X (Twitter)

**Visual:** Same still, 1:1 crop if needed.

**Tweet**

A stretch quote from a Gmail address is a tuxedo with sneakers.

Looks almost right. Clients don’t buy almost.

LimoGen: send from your domain.

Reply SMTP.

**Tags:** `#limobusiness` only (optional)  
**CTA:** Reply SMTP

---

## LinkedIn

**Visual:** Same 3D miniature still.

**Hook:** Deliverability is brand. Personal Gmail is a costume.

**Copy**

When quotes leave from a personal Gmail, two things happen: the inbox doesn’t trust you, and the client doesn’t either. Spam filters and planners vote the same way.

LimoGen Email Settings:
• Outbound SMTP from the operator domain  
• Connection test before you send live  
• Quotes and agreements as the brand  

Gmail isn’t your brand. Comment SMTP or message for a short walkthrough.

**CTA:** Comment SMTP / message  
**Hashtags:** optional — `#GroundTransportation #LimoBusiness #Email`
