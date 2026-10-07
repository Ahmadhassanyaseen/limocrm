# LimoCRM — Module-by-module pitch

LimoCRM is built as connected modules, each handling one part of running a limousine business. Together they cover the full journey from website inquiry to signed agreement, payment, and reporting—without switching between disconnected tools.

For a short executive summary, see [CLIENT_PITCH.md](CLIENT_PITCH.md).

---

## Command center

### Dashboard

See year-to-date lead volume, open pipeline, and wins in charts and KPI cards at a glance. Owners get a team-wide view; staff see only the pipeline they own, so everyone starts the day knowing what needs attention.

### Reports & Analytics

Drill into performance by month, quarter, or year with funnel stages, geographic breakdowns, and revenue-style metrics drawn from real lead data. Use it to spot which routes, channels, and periods drive growth—and where deals stall.

---

## Sales pipeline

### Leads

Every inquiry lives in one searchable list with status, assignment, trip details, and pricing. It is the central hub for moving a job from first contact to a won booking.

### Lead detail

Open a single lead to see full history, related emails, and actions to send a formal quote or agreement link. Your team stays in one workspace instead of jumping between inbox, docs, and payment tools.

### Add / Edit lead

Capture new bookings or update existing ones with forms built for limo trips—pickup, dropoff, date, passengers, vehicle, and assignee. Keeps data clean from the first touch so quotes and agreements are accurate.

### Contacts

Store long-term customer records separate from one-off leads, with detail and edit views for repeat clients and corporate accounts. Builds a relationship layer that supports upsell and loyalty over time.

### Notes

Attach internal notes to CRM records so dispatch, sales, and owners share context in one place. Replaces scattered email threads and sticky notes when handoffs happen.

### Tasks

Track follow-up to-dos tied to CRM work—calls, quotes, and check-ins—so nothing slips after a conversation. Keeps the team accountable without a separate task app.

---

## Fleet & pricing

### Vehicles (Fleet)

Maintain a catalog of limos with capacity, features, photos, and status in a fleet grid. Sales always quotes from what you actually run, not outdated spreadsheets.

### Vehicle detail

View deep specs, image gallery, and pricing for one vehicle when configuring quotes or reviewing fleet ops. Gives dispatch and sales the same source of truth on each unit.

### Pricing (Admin)

Set global hourly rates, fuel surcharge, and driver commission defaults, then override per vehicle when needed. Protects margin while keeping quotes consistent across the team.

---

## Revenue: agreements & payments

### Agreements (staff)

Manage agreement records and generate signing links from inside CRM for jobs ready to close. Track which bookings are still awaiting signature so sales can follow up quickly.

### Public agreement page

Customers open a branded page to review the trip, sign electronically, and pay via Stripe, PayPal, or offline—without staff in the loop. Shortens the gap between “yes” and money in the bank.

### Payment methods (Admin)

Connect Stripe and PayPal and choose the default payment mode for each operator account. Each owner controls how their customers pay while staying inside one platform.

### Transactions

View a ledger of payments linked to jobs for reconciliation and cash-flow visibility. Owners see what cleared, when, and against which booking.

---

## Communications

### Email settings

Configure outbound SMTP accounts so quotes and agreements send from your domain and brand. Professional delivery builds trust and keeps messages out of spam folders.

### Email templates

Build and edit templates with a visual editor and LimoCRM placeholders for tracking and unsubscribe links. Every outreach looks consistent—formal quotes, agreements, and follow-ups included.

### Email analytics

See how outbound mail performs with open and engagement metrics per user. Refine templates and timing based on data, not guesswork.

### Email detail

Open a single sent message from a lead’s timeline for support, disputes, or audit. Full context on what went out and when, tied to the job.

---

## Growth & automation

### Integrations (Widget)

Get embed code, live preview, and controls for accent color and font to match your brand on any website. See which domains drive leads so marketing spend ties back to CRM data.

### Instant quote widget

Visitors enter pickup, destination, date, and passengers, pick a vehicle, and submit—creating a new lead in CRM automatically. Turns passive site traffic into qualified pipeline without phone tag.

### Workflows

Define rule-based automation with conditions and actions on leads and contacts—status changes, follow-ups, and repetitive updates. Frees staff from manual clicks as volume grows.

### Welcome / OTP funnel

Prospects arrive via a marketing link, verify their email with a one-time code, and enter CRM securely. Campaign-driven growth stays verified and traceable from first click.

### Explore demo login

Campaign links with tracking IDs can auto-open a demo workspace so prospects evaluate the product immediately. Sales and marketing see which sends convert to product exploration.

---

## Administration & account

### Users

Add team members, assign roles, and manage active accounts as the company scales. Onboarding new hires takes minutes, not a IT project.

### Role management

Build roles with module-level create, read, update, and delete permissions—sales sees leads, not payment keys. Owners keep control; staff see only what their job requires.

### Settings

Update profile details, username, phone, and password in one account screen. Self-service changes reduce support load on admins.

### Profile

Quick view of the signed-in user’s identity and shortcuts to account actions. Keeps personal info accessible from anywhere in the app.

### Intro wizard

A first-run guided tour walks new operators through fleet, templates, widget embed, and leads. Faster adoption means ROI sooner after go-live.

---

## The full journey

**Website → lead → quote → agreement → payment → report**—each module above owns one step, and LimoCRM connects them in a single workspace built for limousine operators.

---

## Printable brochure

Open [docs/brochure/design.html](brochure/design.html) in your browser and use **Save as PDF**, or run `node export-pdf.mjs` in `docs/brochure/` to generate `LimoCRM_Modules_Brochure.pdf`. See [brochure/README.md](brochure/README.md).
