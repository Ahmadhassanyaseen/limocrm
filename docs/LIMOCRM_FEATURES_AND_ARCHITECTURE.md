# LimoCRM (LimoGen) — Feature & Architecture Documentation

This document describes the **PHP web application** at `limocrm/` as implemented in this repository: user-facing screens, permissions, integrations with **SuiteCRM via `CustomEntryPoint`**, local services (workflows engine, PDFs, AJAX bridges), and the **embedded booking widget** assets under `limogen-widget/`.

**Important boundary:** Persistent business data for leads, contacts, vehicles, users, roles, emails, agreements, payments, etc. lives in **SuiteCRM** (remote). This app is primarily a **logged-in SPA-style UI** that calls `https://zabrin.xyz/limogen/index.php?entryPoint=CustomEntryPoint` (see `config/api.php`). SuiteCRM handlers must implement the corresponding `action` names documented below.

---

## 1. Executive summary

| Area | What the app provides |
|------|------------------------|
| **Sales & CRM** | Lead pipeline (list, detail, create, edit), contacts, notes, tasks, dashboard KPIs, reports & analytics |
| **Fleet** | Vehicle catalog, detail pages, pricing defaults (admin), vehicle-linked lead flows |
| **Commerce** | Agreements (internal + **public** signing page), Stripe/PayPal/offline preferences, transactions |
| **Communications** | Email templates, outbound SMTP account management, sending mail from leads, email analytics |
| **Automation** | **Local** workflow definitions (conditions + actions), cron runner against local MySQL |
| **Growth** | Website **widget** embed, per-user **accent color + Google Font**, domain/lead-source tracking |
| **Administration** | Users, roles & permissions (mirrored in session), payment credentials, integrations screen |

---

## 2. System architecture

### 2.1 Layers

1. **Browser UI** — PHP pages under project root (`index.php`, `leads.php`, …) plus shared layout (`components/layout/header.php`, `sidebar.php`), Tailwind/Bootstrap-style assets, Remix icons, SweetAlert2 on many flows.
2. **Session** — PHP `$_SESSION['user']` holds identity, `admin` flag, and **`role_permissions`** (module × CRUD flags). Helpers in `config/session_permissions.php` and legacy ACL shaping in `acll.php`.
3. **Application API bridge** — `config/api.php` defines `curlRequest()` posting form/array fields to SuiteCRM **`CustomEntryPoint`**. Each wrapper sets `action` and delegates.
4. **SuiteCRM** — Authoritative datastore and server-side logic (not in this repo). Handlers must exist server-side for each `action`.
5. **Local MySQL** (optional but used for workflows) — `config/database.php`, tables such as `limocrm_workflows`, `limocrm_workflow_conditions`, `limocrm_workflow_actions` (see `app/Workflows/`).
6. **Public / standalone endpoints** — Examples: `agreement.php` + `config/agreement_api.php` (token-less bridge using `lead_id`), various `config/*_endpoint.php` helpers, `limogen-widget/widget.js` + `widget-frame.php` hosted separately.

### 2.2 Typical request flow

```
User → PHP page → include api.php → curlRequest(['action' => '…', …])
    → SuiteCRM CustomEntryPoint → JSON response → PHP/JS renders UI
```

---

## 3. Authentication & authorization

### 3.1 Login & session

- **`login.php`** / **`config/login.php`** — Establishes session with user record from CRM.
- **`config/logout.php`** — Clears session.

### 3.2 Admin vs role-based access

- **`limo_nav_session_admin_full_access()`** — `(int)$_SESSION['user']['admin'] === 1` grants full navigation (including Pricing, User Management, Payment Management, Email Management submenus).
- **`limo_nav_can_module('ModuleName')`** — Non-admins need at least one of `can_create|can_read|can_update|can_delete` on that module in `$_SESSION['user']['role_permissions']`.
- **`limo_user_module_access($module, $access)`** — Fine-grained CRUD check for pages that enforce create/update/delete.

### 3.3 ACL normalization (`acll.php`)

Maps CRM permission module labels to keys (e.g. `lead` → `leads`, `integration` → `integration`). Used together with sidebar visibility.

---

## 4. Navigation map (sidebar)

High-level modules exposed in **`components/layout/sidebar.php`** (subject to permissions):

| Menu item | Module key / notes |
|-----------|---------------------|
| Dashboard | `index.php` — all users with access |
| Leads | `Leads` |
| Vehicles | `Vehicles` |
| Agreements | `Agreements` → `agreements.php` |
| Notes | `Notes` |
| Contacts | `Contacts` |
| Reports & Analytics | `Reports` |
| Pricing | Admin-only (`pricing.php`) |
| **ADMIN — User Management** | Admin-only: `users.php`, `role_management.php` |
| **ADMIN — Payment Management** | Admin-only: `transactions.php`, `payment_methods.php` |
| **ADMIN — Email Management** | Admin-only: `email_settings.php`, `email_templates.php` / `email_template.php`, `email_analytics.php` |
| Workflows | `Workflows` — `workflows.php` (local automation UI) |
| Integrations | `Integrations` — `integration.php` (widget embed + theme) |
| Settings | `settings.php` — generally visible |

Commented-out placeholders in sidebar include Vendors, Quotes, Calendar, Email Tracking, Campaigns, Chat, Commissions, Vendor Tiers, Audit Log, System — **not active** in current menu.

---

## 5. Feature catalog (detailed)

### 5.1 Dashboard (`index.php`)

- Fetches leads: **`fetchAllLeads`** (admin) vs **`fetchAllUserLeads`** (standard user).
- Computes **year-to-date** aggregates: leads created per month, “open” additions, wins (`Converted` / `won` / `success` style statuses).
- Visual KPIs (charts/cards), recent activity flavor — limousine sales oriented.

### 5.2 Leads

- **`leads.php`** — Listing with table component (`components/tables/leads.php` pattern).
- **`lead.php`** — Single-lead workspace: details, emails, formal quote / agreement hooks, SweetAlert-driven sends (`sendFormalQuoteEmail`, `sendAgreementEmail`, etc. per `lead.php`).
- **`add_lead.php`** — Create lead (**`save_lead`**).
- **`edit_lead.php`** — Update (**`update_lead`** via API patterns).

Lead-related API actions used across app include: **`fetchSingleLead`**, **`update_lead`**, **`save_lead`**, **`send_lead_email`**, **`send_formal_quote_email`**, **`send_agreement_email`**, **`fetch_lead_emails`**, **`fetch_single_email`**, **`update_lead_after_payment`**, **`get_agreement_signing_link`**, **`fetch_lead_stripe_key`**, **`submit_agreement`**.

### 5.3 Contacts

- **`contacts.php`** — List (**`fetch_contacts`** / list variants).
- **`contact_detail.php`**, **`edit_contact.php`** — Detail and edit (**`fetch_contact_detail`**, **`save_contact`**, **`update_contact`**, **`delete_contact`**).

SuiteCRM-side **`update_contact`** implementation should align with fields your UI posts (names, phones, address, description, lead source, do-not-call, primary email helpers — see any CRM snippets you maintain).

### 5.4 Notes

- **`notes.php`** — CRUD notes tied to CRM (**`fetch_notes`**, **`save_note`**, **`update_note`**, **`delete_note`**).
- **`create_notes.php`** — Additional entry path if present.

### 5.5 Tasks

- **`task.php`** — Task UI backed by **`fetch_tasks`**, **`save_task`**, **`update_task_status`**, **`delete_task`**.

### 5.6 Vehicles (fleet)

- **`vehicles.php`** — Fleet grid; **`fetch_vehicles`** passes `is_admin` from session.
- **`vehicle.php`**, **`vehicle_detail.php`** — Create/edit/view single vehicle (**`save_vehicle`**, **`get_vehicle`**, **`delete_vehicle`**).
- **`pricing.php`** (admin) — Default pricing (**`fetch_pricing_defaults`**, **`save_pricing_defaults`**, **`update_vehicle_pricing`**).

### 5.7 Agreements & payments

**Staff-facing**

- **`agreements.php`** — Internal agreement management / links (works with CRM + signing flow).
- **`config/get_agreement_link_endpoint.php`** — Supports generating signing links (**`get_agreement_signing_link`**).

**Public signing**

- **`agreement.php`** — Standalone page: booking summary, signature canvas, Stripe Elements when owner **`preferred_payment`** is Stripe; offline/PayPal modes per product rules; posts to **`config/agreement_api.php`** which calls **`submit_agreement`** / **`fetchLeadStripeKey`** and generates **TCPDF** agreement PDFs under `pdf/` (Composer package `tecnickcom/tcpdf`).

**Payments & preferences**

- **`payment_methods.php`** — Connect Stripe/PayPal, set **`preferred_payment`** (stripe | paypal | offline) via **`fetch_user_stripe_keys`**, **`save_user_stripe_keys`**, PayPal variants, **`save_user_payment_preference`**.
- **`transactions.php`** — Ledger view (**`fetch_user_transactions`**).

Database **snippets** in repo for SuiteCRM tables: `database/limo_user_stripe_keys.sql` / payment columns / **`limo_payment_table`** naming in migration paths, **`limo_stripe_agreement.sql`**, `custom_entry_point_stripe_keys.php`, agreement helpers.

### 5.8 Email

- **`email_templates.php`**, **`email_template.php`** — Template list & editor (**`fetch_email_templates`**, **`get_email_template`**, **`save_email_template`**, **`delete_email_template`**).
- **`email_settings.php`** — Outbound SMTP accounts (**`fetch_outbound_email_accounts`**, detail, save, delete, **`test_outbound_email_account_connection`**). Snippets: `database/limo_outbound_email_accounts.sql`, `custom_entry_point_limo_outbound_email_accounts.php`.
- **`email_analytics.php`** — **`fetch_user_email_analytics`**.
- **`email_detail.php`**, **`email_actions.php`** — Supporting views/actions where implemented.

### 5.9 Reports & analytics (`reports.php`)

- Uses **`fetchAllUserLeads`** for the logged-in user.
- Rich analytics: periods (month, quarter, year), geographic breakdown from addresses, funnel stages, revenue-style metrics from custom fields (rates/totals), trend series — limousine operator reporting.

### 5.10 Workflows (local engine)

- **`workflows.php`**, **`create_workflow.php`**, **`edit_workflow.php`** — UI for defining workflows stored in **local** DB (`WorkflowController`, `ConditionEvaluator`).
- **`cron/workflow_engine.php`** — Runs **`WorkflowExecutionEngine`** once (intended for OS cron every ~60s).
- **`cron/workflow_executor.php`** — Alternate/legacy executor entry if present.

Supports modules **Leads**, **Contacts**, **AOS_Quotes** for field introspection (`WorkflowController::moduleFields`). Actions typically update CRM via outbound logic inside engine classes.

### 5.11 Integrations & widget

- **`integration.php`** — Embed snippet for **`limogen-widget`**, live preview, domain statistics (**`fetch_embedded_domains`**), widget appearance (**accent + Google Font**) saved via **`fetch_widget_theme`** / **`save_widget_theme`** (SweetAlert UX). Snippets: `database/limo_widget_theme.sql`, `custom_entry_point_widget_theme.php`.
- **`limogen-widget/widget.js`** — Injects iframe → **`widget-frame.php`** with `user_id`, `source`, optional **`accent_color`** / **`font_family`** / **`theme`**.
- **`limogen-widget/widget-frame.php`** — Full booking UX (Leaflet/OSRM/Nominatim, vehicle grid, quote submission **`save_lead`**, theme from query + CRM fallback **`fetch_widget_theme`**).

### 5.12 User management & roles (admin)

- **`users.php`** — **`create_user`**, **`update_user`**, **`delete_user`**, team listing **`fetchAllTeamMembers`** / related.
- **`role_management.php`** — **`create_role`**, **`fetch_roles`**, **`update_role`**, **`delete_role`**, **`get_module_template`** for permission matrix.

### 5.13 Settings & profile

- **`settings.php`** — Profile & password flows (endpoints under `config/` e.g. **`change_password_endpoint.php`**, **`profile_update_endpoint.php`**).
- **`profile.php`** — User profile view/edit pattern.

### 5.14 Onboarding & UX polish

- **`assets/js/limo-intro-wizard.js`** — Guided intro (vehicles → templates → **integration** → leads) coordinated with **`header.php`** flags.
- **`logs/session_visit_log.php`**, **`logs/logger.php`** — Session/visit logging infrastructure.

### 5.15 Other entry points

- **`signup.php`** — Registration / invite flow (if enabled).
- **`welcome.php`** — Post-login welcome.
- **`tester.php`** — Development/testing.
- **`api/index.php`** — Secondary API facade (verify in deployment).

---

## 6. Backend API surface (`config/api.php`)

Each function name maps to a SuiteCRM **`action`** value (unless noted as local-only).

| PHP function | `action` sent to CustomEntryPoint |
|--------------|-----------------------------------|
| `fetchAllLeads` | `fetchAllLeads` |
| `fetchAllUserLeads` | `fetchAllUserLeads` |
| `createNote` | `createNote` |
| `fetchAllTeamMembers` | `fetchAllTeamMembers` |
| `fetchSingleLead` | `fetchSingleLead` |
| `updateLead` | `update_lead` |
| `updateLeadAfterPayment` | `update_lead_after_payment` |
| `userLogin` | `user_login` |
| `fetchRoles` / `fetch_roles` | `fetch_roles` |
| `fetchEmailTemplates` | `fetch_email_templates` |
| `getEmailTemplate` | `get_email_template` |
| `saveEmailTemplate` | `save_email_template` |
| `deleteEmailTemplate` | `delete_email_template` |
| `fetchTasks` | `fetch_tasks` |
| `fetch_workflows` | `fetch_workflows` (CRM workflows if used) |
| `saveTask` | `save_task` |
| `updateTaskStatus` | `update_task_status` |
| `deleteTask` | `delete_task` |
| `fetchVehicles` | `fetch_vehicles` |
| `saveVehicle` | `save_vehicle` |
| `getVehicle` | `get_vehicle` |
| `deleteVehicle` | `delete_vehicle` |
| `fetchPricingDefaults` | `fetch_pricing_defaults` |
| `savePricingDefaults` | `save_pricing_defaults` |
| `updateVehiclePricing` | `update_vehicle_pricing` |
| `deleteworkflow` | `delete_workflow` (CRM-side workflow delete) |
| `create_role` | `create_role` |
| `delete_role` | `delete_role` |
| `update_role` | `update_role` |
| `get_module_template` | `get_module_template` |
| `getUserIdByEmail` | `getUserIdByEmail` |
| `fetchCurrentUserPermissions` | `fetch_current_user_permissions` |
| `create_user` | `create_user` |
| `delete_user` | `delete_user` |
| `update_user` | `update_user` |
| `sendLeadEmail` | `send_lead_email` |
| `sendFormalQuoteEmail` | `send_formal_quote_email` |
| `sendAgreementEmail` | `send_agreement_email` |
| `saveLead` | `save_lead` |
| `fetchLeadEmails` | `fetch_lead_emails` |
| `fetchSingleEmail` | `fetch_single_email` |
| `fetchUserEmailAnalytics` | `fetch_user_email_analytics` |
| `fetchNotes` | `fetch_notes` |
| `saveNote` | `save_note` |
| `updateNoteApi` | `update_note` |
| `deleteNote` | `delete_note` |
| `fetchContacts` | `fetch_contacts` |
| `fetchContactsList` | `fetch_contacts_list` |
| `fetchContactDetail` | `fetch_contact_detail` |
| `saveContactRecord` | `save_contact` |
| `updateContactRecord` | `update_contact` |
| `deleteContactRecord` | `delete_contact` |
| `fetchEmbeddedDomains` | `fetch_embedded_domains` |
| `fetchWidgetTheme` | `fetch_widget_theme` |
| `saveWidgetTheme` | `save_widget_theme` |
| `fetchAgreementLeadData` | `fetch_agreement_lead` |
| `submitAgreementPayment` | `submit_agreement` |
| `getAgreementSigningLink` | `get_agreement_signing_link` |
| `fetchLeadStripeKey` | `fetch_lead_stripe_key` |
| `fetchUserStripeKeys` | `fetch_user_stripe_keys` |
| `fetchPaymentMethods` | `fetch_payment_methods` |
| `saveUserStripeKeys` | `save_user_stripe_keys` |
| `deleteUserStripeKeys` | `delete_user_stripe_keys` |
| `saveUserPaymentPreference` | `save_user_payment_preference` |
| `saveUserPaypalKeys` | `save_user_paypal_keys` |
| `deleteUserPaypalKeys` | `delete_user_paypal_keys` |
| `fetchUserTransactions` | `fetch_user_transactions` |
| `fetchOutboundEmailAccounts` | `fetch_outbound_email_accounts` |
| `fetchOutboundEmailAccountDetail` | `fetch_outbound_email_account_detail` |
| `saveOutboundEmailAccount` | `save_outbound_email_account` |
| `deleteOutboundEmailAccount` | `delete_outbound_email_account` |
| `testOutboundEmailAccountConnection` | `test_outbound_email_account_connection` |

**Security note:** `curlRequest` disables SSL verification — acceptable only in dev; tighten for production.

---

## 7. Repository database & deployment artifacts

Under **`database/`**:

- **Stripe / PayPal / payment prefs** — `custom_entry_point_stripe_keys.php`, `limo_user_stripe_keys.sql`, `limo_user_payment_method_columns.sql` (evolve to **`limo_payment_table`** per your migration).
- **Outbound email** — `limo_outbound_email_accounts.sql`, `custom_entry_point_limo_outbound_email_accounts.php`, `custom_entry_point_outbound_email.php`.
- **Widget theme** — `limo_widget_theme.sql`, `custom_entry_point_widget_theme.php`.
- **Agreements / Stripe log** — `limo_stripe_agreement.sql`, `limo_agreement_config.sample.php`.
- **Pricing** — `limo_pricing_defaults.sql`.

These are **instructions / SQL to run on SuiteCRM** (or related DBs), not necessarily auto-applied by this PHP app.

---

## 8. Technology stack (from repo)

| Layer | Choices |
|-------|---------|
| Server | PHP (7.4+ / 8.x patterns mixed) |
| UI | Mixed template + Tailwind-style utility classes, jQuery on many pages, SweetAlert2 |
| Charts / maps in widget | Leaflet, OSRM, Nominatim (no API keys) |
| PDF | TCPDF via Composer |
| CRM | SuiteCRM CustomEntryPoint over HTTP POST |
| Local DB | MySQLi (`config/database.php`) for workflows |

---

## 9. Glossary

- **LimoCRM / LimoGen** — This front-end CRM product name used in UI copy and paths.
- **CustomEntryPoint** — SuiteCRM single entry script receiving `action` + POST fields.
- **Widget** — Third-party site embed; attributes `data-user-id`, `data-accent-color`, `data-font-family`, etc.

---

## 10. Maintaining this document

When you add a new top-level `.php` page or `config/api.php` wrapper:

1. Register it in SuiteCRM `CustomEntryPoint` if needed.
2. Add a sidebar link (with `limo_nav_can_module` if role-gated).
3. Update **Section 5** and the **API table** in this file.

---

*Generated from repository structure and source analysis. SuiteCRM handler behavior for each `action` is defined on the server and may extend beyond what this UI exercises.*
