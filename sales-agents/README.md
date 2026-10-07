# LimoGen sales agent team

Supervised outreach app for selling **LimoGen** to limousine operators. It lives next to the PHP CRM, not inside it.

Agents research companies, draft copy from the pitch corpus, and queue sends. **You approve the first email.** Follow-ups can auto-send. **LinkedIn / Instagram / Facebook / X never send themselves** — they land on Today for copy-paste. **SMS and WhatsApp require explicit consent** (no cold texts).

## Run

```bash
cd sales-agents
python -m venv .venv
.venv\Scripts\activate
pip install -r requirements.txt
copy .env.example .env
python run.py
```

Open [http://127.0.0.1:8787](http://127.0.0.1:8787).

## Daily path

1. **Import** a CSV of operators (or Google Places if `GOOGLE_PLACES_API_KEY` is set).
2. Open a prospect → **Enrich website** (pulls emails/socials, scores ICP).
3. **Draft outreach** — first email + social notes go to **Approvals**.
4. Edit and **Approve**. Email 1 sends if Resend/SMTP is configured; emails 2/3/4 queue for days 3/7/14.
5. Approved social copy appears on **Today**. Send from the real account, then **Mark sent**.
6. Replies: IMAP polls automatically, or paste a DM into **Inbox**. The closer drafts an explore link (`login_explore.php?send_id=`).
7. If they ask to be texted, record consent on the prospect, then draft SMS/WhatsApp (still approved before send).

## What must be in `.env` before real email

- `SECRET_KEY`, `SENDER_NAME`, `SENDER_EMAIL`, `COMPANY_LEGAL_NAME`, `MAILING_ADDRESS`
- `PUBLIC_BASE_URL` (used in unsubscribe links)
- `RESEND_API_KEY` or SMTP fields
- `EXPLORE_DEMO_BASE_URL` (default `http://localhost/limocrm/login_explore.php`)
- Optional: `OPENAI_API_KEY` (otherwise template copy is used)
- Optional: IMAP, Hunter.io, Google Places, Twilio, Meta WhatsApp

## Hard rules (code, not the model)

- CAN-SPAM footer + unsubscribe token on every email
- Suppression list (unsubscribe, bounce, do-not-contact)
- Daily send caps
- Social = draft only
- SMS/WhatsApp blocked without a consent row

Public copy always says **LimoGen**, never LimoCRM. Unshipped modules from `posts/ISSUES.md` are not pitched.
