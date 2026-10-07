# LimoGen — Operator issues this system solves

Numbered catalog of pains LimoGen actually fixes. Live modules only. Use this list as the source of truth for `posts/` — issue **#1** is the first still post.

**Brand:** LimoGen (never Limogen, Limo Gen, or LimoCRM in post copy).

**Sources:** [`docs/CLIENT_PITCH.md`](../docs/CLIENT_PITCH.md), [`docs/CLIENT_PITCH_MODULES.md`](../docs/CLIENT_PITCH_MODULES.md), [`docs/LIMOCRM_COMPLETE_FEATURE_GUIDE.md`](../docs/LIMOCRM_COMPLETE_FEATURE_GUIDE.md).

Each issue uses the same shape:

- **Problem** — one-line operator pain
- **Without LimoGen** — how it shows up day to day
- **Solution** — what the product does
- **Surfaces** — real pages / modules

---

## Ops chaos (umbrella)

### 1. CRM is a group chat

- **Problem:** Your CRM is a group text with nine drivers — inbox, spreadsheet, and Venmo. Bookings die in the gaps.
- **Without LimoGen:** Inquiries live in Gmail. Dispatch lives in a group chat. Quotes live in Excel. Money lives in Venmo. Nobody has one picture of the job.
- **Solution:** One workspace for the full journey — lead → quote → sign → pay.
- **Surfaces:** Dashboard (`index.php`), leads (`leads.php`, `lead.php`), agreements (`agreements.php`, public `agreement.php`), payments (`payment_methods.php`, `transactions.php`).

### 2. Owner starts the day in texts

- **Problem:** No shared picture of pipeline — the day starts by scrolling messages.
- **Without LimoGen:** “What’s open?” means asking the group chat. Wins, new leads, and stalled jobs live in different heads.
- **Solution:** Dashboard KPIs — year-to-date leads, open pipeline, and wins. Owners see the team; staff see the pipeline they own.
- **Surfaces:** Dashboard (`index.php`).

### 3. Guessing which routes and months actually book

- **Problem:** Performance lives in a messy spreadsheet, so growth is a guess.
- **Without LimoGen:** Nobody can say which routes, channels, or months drive bookings — or where deals stall.
- **Solution:** Reports by month, quarter, or year — funnel stages, geographic breakdowns, and revenue-style metrics from real lead data.
- **Surfaces:** Reports (`reports.php`).

---

## Demand capture

### 4. Website doesn’t convert

- **Problem:** Site traffic dies in phone tag — visitors never get an instant price.
- **Without LimoGen:** Someone has to call back, look up a vehicle, and type a quote. The tab is already closed.
- **Solution:** Embeddable instant-quote widget — pickup, destination, date, passengers, vehicle — submits a lead into CRM automatically.
- **Surfaces:** Integrations (`integration.php`), widget (`limogen-widget/`), `instantQuoteForm.php`.

### 5. Don’t know which domains send leads

- **Problem:** Marketing spend is blind — you can’t see which sites actually produce pipeline.
- **Without LimoGen:** Widget (or forms) sit on several domains with no source trail back to CRM.
- **Solution:** Widget branding (accent color, font) plus domain stats so spend ties back to leads.
- **Surfaces:** Integrations (`integration.php`) — embed snippet, live preview, theme, `fetch_embedded_domains`.

### 6. Campaign clicks aren’t proven

- **Problem:** Anyone can wander in from a marketing link — the email is never verified.
- **Without LimoGen:** Campaign traffic is untraceable and the inbox is full of fake or mistyped addresses.
- **Solution:** Welcome / OTP funnel — marketing link → one-time code → verified entry.
- **Surfaces:** `welcome.php`, `otp_verify.php`.

### 7. Demo is a calendar delay

- **Problem:** The prospect cools off waiting for a walkthrough slot.
- **Without LimoGen:** Sales books a call next week. The click is already cold.
- **Solution:** Explore demo login — campaign links with tracking IDs open a demo workspace the same day.
- **Surfaces:** `login_explore.php`, `config/demo_credentials.php`.

---

## Pipeline

### 8. Leads live in email and Excel

- **Problem:** No assignee, no history, no single list — inquiries scatter.
- **Without LimoGen:** Pickup, passengers, and “who owns this?” live in threads and sheets. Jobs get double-quoted or dropped.
- **Solution:** Searchable lead list plus a lead workspace — assign staff, send formal quotes and agreements, email alerts on every move.
- **Surfaces:** `leads.php`, `lead.php`, `add_lead.php`, `edit_lead.php`.

### 9. Quotes rebuilt by hand every time

- **Problem:** Every quote is a rewrite — slow, inconsistent, easy to mistype.
- **Without LimoGen:** Copy-paste from last week’s email. Rates drift. Branding is whoever last touched Word.
- **Solution:** Formal quote email from the lead record, using saved templates and placeholders.
- **Surfaces:** Lead workspace (`lead.php` — `send_formal_quote_email`), email templates (`email_templates.php`, `email_template.php`).

### 10. Agreement chase

- **Problem:** PDF in email, DocuSign login, “did they sign yet?” in the group chat.
- **Without LimoGen:** Sales chases signatures across tools. Dispatch never knows which jobs are actually closed.
- **Solution:** Staff generate a signing link from CRM. The customer reviews the trip, e-signs, and pays on a branded public page. Staff see what’s still awaiting signature.
- **Surfaces:** `agreements.php`, public `agreement.php`, `config/get_agreement_link_endpoint.php`.

### 11. Sign/pay never updates the CRM

- **Problem:** The client signed and paid. Dispatch is still chasing.
- **Without LimoGen:** DocuSign in one tab, Stripe in another, CRM status stuck on “quoted.”
- **Solution:** After payment, the lead updates — dispatch sees the paid booking without a group-chat ping.
- **Surfaces:** Public `agreement.php` → `update_lead_after_payment` on the lead.

### 12. Repeat clients mixed into one-off leads

- **Problem:** Loyalty and corporate accounts get lost in one-off inquiries.
- **Without LimoGen:** Last year’s wedding mom is another row in the inbox. Nobody has a durable customer record.
- **Solution:** Contacts separate from leads — detail and edit for repeat clients and corporate accounts.
- **Surfaces:** `contacts.php`, `contact_detail.php`, `edit_contact.php`.

### 13. Handoffs are a group-chat scavenger hunt

- **Problem:** Context for a job lives in texts, sticky notes, and “I thought you knew.”
- **Without LimoGen:** Sales, dispatch, and the owner reconstruct the story every shift change.
- **Solution:** Internal notes on CRM records — one place for handoff context.
- **Surfaces:** `notes.php`, `create_notes.php`.

### 14. Follow-ups die after the call

- **Problem:** The promised callback never becomes a to-do.
- **Without LimoGen:** “I’ll call them Thursday” lives in someone’s head. No separate task app, and none tied to the job.
- **Solution:** Tasks tied to CRM work — calls, quotes, check-ins — so nothing slips after the conversation.
- **Surfaces:** `task.php` (UI exists; not a primary sidebar item).

### 15. Follow-up depends on memory while you’re on the road

- **Problem:** Nobody babysits the inbox when the team is on a run.
- **Without LimoGen:** Hot leads go cold. Status never moves. Welcome emails wait until someone is at a desk.
- **Solution:** Workflows — trigger → conditions → actions (send email, delay, update field, webhook) so follow-up runs without staff clicks.
- **Surfaces:** `workflows.php`, `create_workflow.php`, `edit_workflow.php`, cron `cron/workflow_engine.php`.

---

## Fleet and margin

### 16. Quotes from an outdated vehicle spreadsheet

- **Problem:** Sales quotes cars you don’t run — or capacity that was true last season.
- **Without LimoGen:** Photos, passenger count, and “is it in service?” live in a sheet nobody updates.
- **Solution:** Fleet catalog — capacity, features, photos, and status in one grid. Quotes from what you actually run.
- **Surfaces:** `vehicles.php`, `vehicle.php`.

### 17. Sales and dispatch disagree on the unit

- **Problem:** Two people, two stories about the same car.
- **Without LimoGen:** Specs in a text, photos in a Drive folder, price in someone’s head.
- **Solution:** Vehicle detail — specs, gallery, and per-unit pricing as the shared source of truth.
- **Surfaces:** `vehicle_detail.php`.

### 18. Margin leaks

- **Problem:** Hourly rate, fuel surcharge, and driver commission get rewritten by hand on every quote.
- **Without LimoGen:** Each salesperson invents a number. Margin disappears in the exceptions.
- **Solution:** Admin pricing defaults, with per-vehicle override when a unit needs it.
- **Surfaces:** `pricing.php` (admin).

---

## Money

### 19. Payments in five tabs, not tied to the job

- **Problem:** Clients pay how they want — somewhere else. The booking never sees it.
- **Without LimoGen:** Stripe link, PayPal invoice, Venmo, “pay the driver cash.” Five tools, zero job link.
- **Solution:** Stripe, PayPal, or offline on the same agreement page, with the operator’s preferred mode.
- **Surfaces:** `payment_methods.php`, public `agreement.php`.

### 20. Can’t reconcile what cleared vs which booking

- **Problem:** Cash-flow is a pile of screenshots, not a ledger.
- **Without LimoGen:** Owner asks “did the Saturday wedding pay?” and someone scrolls Venmo.
- **Solution:** Transactions ledger — what cleared, when, and against which job.
- **Surfaces:** `transactions.php`.

### 21. No signed PDF on file

- **Problem:** After the client signs, there is no durable document on the job.
- **Without LimoGen:** A photo of a signature on a phone, or a DocuSign PDF in someone’s email.
- **Solution:** TCPDF agreement PDF generated after sign / pay and stored with the booking.
- **Surfaces:** Public `agreement.php` → PDFs under `pdf/`.

---

## Email

### 22. Quotes from personal Gmail

- **Problem:** Outreach looks unprofessional and lands in spam.
- **Without LimoGen:** `mike.limos@gmail.com` sends the quote. Clients don’t trust it; filters bury it.
- **Solution:** Outbound SMTP from the operator domain — quotes and agreements send as the brand.
- **Surfaces:** `email_settings.php` (admin) — accounts CRUD + connection test.

### 23. Inconsistent outreach / Canva export chaos

- **Problem:** Every email looks different. Someone exports a graphic, pastes it, and hopes.
- **Without LimoGen:** No shared quote, agreement, or follow-up template. Branding drifts.
- **Solution:** Drag-and-drop email templates with placeholders (tracking, unsubscribe) — save once, reuse.
- **Surfaces:** `email_templates.php`, `email_template.php`.

### 24. Guessing which emails get opened

- **Problem:** Nobody knows which subject lines or templates actually get read.
- **Without LimoGen:** Timing and copy are vibes. Dead templates keep going out.
- **Solution:** Email analytics — open and engagement metrics per user, so templates get fixed with data.
- **Surfaces:** `email_analytics.php` (admin).

### 25. No audit of what went out on a job

- **Problem:** Support, disputes, and “I never got that quote” have no paper trail.
- **Without LimoGen:** The sent folder is a different person’s Gmail.
- **Solution:** Email detail on the lead timeline — what went out, when, tied to the job.
- **Surfaces:** `email_detail.php`, lead-related email list on `lead.php`.

---

## Team

### 26. Everyone sees everything

- **Problem:** Sales sees payment keys. Dispatch sees the whole pipeline. Access is all-or-nothing.
- **Without LimoGen:** One shared login, or everyone is an admin.
- **Solution:** Role management with module-level create / read / update / delete — sales sees leads, not payment keys. Owners keep control.
- **Surfaces:** `role_management.php`. Permission modules include Leads, Vehicles, Contacts, Notes, Agreements (sidebar also gates Reports, Workflows, Integrations).

### 27. Onboarding staff is an IT project

- **Problem:** Adding a dispatcher takes a week of “what password did we use?”
- **Without LimoGen:** Shared inboxes and tribal knowledge instead of named accounts.
- **Solution:** Users — add team members, assign roles, manage active accounts in minutes.
- **Surfaces:** `users.php`.

### 28. Blank CRM after go-live

- **Problem:** First login is an empty product stare. ROI waits on guesswork.
- **Without LimoGen:** No guided path through fleet, templates, widget, and leads.
- **Solution:** Intro wizard — first-run tour: vehicles → templates → widget embed → leads.
- **Surfaces:** `assets/js/limo-intro-wizard.js`, `assets/js/limo-intro-vehicle.js`.

### 29. Account changes wait on an admin

- **Problem:** Password, phone, and profile edits are a support ticket.
- **Without LimoGen:** Owner becomes IT for every name change.
- **Solution:** Settings and profile self-service — display name, username, phone, password.
- **Surfaces:** `settings.php`, `profile.php`.

---

## Do not claim

Placeholder or commented-out sidebar items — **not shipped**. Do not pitch these as solved issues in posts:

- Vendors (including “auto vendor assign by pickup location”)
- Standalone Quotes module
- Calendar
- Email Tracking (as a separate nav item)
- Commissions
- Campaigns / Campaign Builder
- Chat
- Coupons
- Vendor Tiers
- Audit Log
- System
- Employee Analytics

---

## How to use this file

1. Pick the next unused issue in order.
2. Create `posts/NN-slug.md` — image-only, problem and solution both visible (split-screen still).
3. Keep CTAs unique across recent posts when possible.
4. If a module is later retired, move that issue into **Do not claim**.
