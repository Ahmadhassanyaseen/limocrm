# LimoCRM — Security & Quality Audit

**Date:** 2026-06-03  
**Scope:** Static review of `G:\XAMPP\htdocs\limocrm` (PHP UI, `config/`, `api/`, widget, auth flows, assets, links)  
**Method:** Source inspection, pattern search, automated internal link scan (PHP files, excluding `vendor/`)

---

## Executive summary

LimoCRM is a session-based PHP front-end over a remote SuiteCRM `CustomEntryPoint` API, with a local MySQL workflow engine. The UI is polished, but several **production-blocking security issues** were found:

| Severity | Count | Top themes |
|----------|-------|------------|
| **Critical** | 6 | Secrets in source control, unauthenticated write APIs, Supabase service-role exposure |
| **High** | 8 | Missing CSRF, weak endpoint auth, admin pages without server guards, TLS disabled |
| **Medium** | 10 | XSS risk, debug settings, exposed logs/data, sidebar URL bug |
| **Low / UX** | 12+ | Missing brand assets, template dead links, commented vs active nav drift |

**Immediate actions (before any public deployment):**

1. Rotate every secret currently committed (DB password, Supabase service role JWT, Stripe keys, demo password).
2. Move secrets to environment variables; extend `.gitignore`.
3. Add authentication to `config/save_lead_endpoint.php`, `config/update_lead_endpoint.php`, and `api/index.php`.
4. Fix `config/change_password_endpoint.php` IDOR (require session + match `id`).
5. Block web access to `logs/`, `cron/`, and `data/` via Apache/nginx rules.

---

## Architecture (auth model)

```
Browser → login.php → config/login.php → SuiteCRM user_login
                ↓
         $_SESSION['user']  (includes admin, role_permissions)
                ↓
    components/layout/header.php  → redirect if no session
                ↓
         Module pages + AJAX → config/*_endpoint.php → curlRequest()
```

**Strengths**

- Most CRM pages include `header.php`, which enforces login.
- `profile_update_endpoint.php` correctly blocks cross-user updates.
- `edit_lead.php` checks module permissions server-side.
- Public flows (`agreement.php`, widget, OTP) are intentionally unauthenticated by design.

**Weaknesses**

- No centralized middleware; each endpoint implements auth independently.
- Sidebar/RBAC checks are **UI-only** on many admin pages.
- No CSRF tokens on any POST/AJAX form.
- Session is not regenerated after login; no explicit `HttpOnly` / `Secure` / `SameSite` cookie flags in code.

---

## Critical vulnerabilities

### C1 — Database credentials committed in plain PHP

**File:** `config/database.php`  
**Issue:** Production MySQL host, username, and password are hardcoded and tracked in git.  
**Impact:** Full read/write access to the local workflow database (`zabrinxyz_suitecrm7`) if repo or server is compromised.  
**Fix:** Use `.env` + `getenv()`; add `config/database.php` to `.gitignore`; rotate password immediately.

---

### C2 — Supabase service-role JWT in `welcome.php`

**File:** `welcome.php` (lines ~17–18)  
**Issue:** Full **service role** key is embedded in source. Service role bypasses Row Level Security.  
**Impact:** Anyone with repo or file access can read/write all Supabase tables (leads, enquiry_activity, etc.).  
**Fix:** Move to server-side env var; use anon key + RLS on client-facing paths; restrict service role to a backend-only service.

---

### C3 — Unauthenticated lead create/update endpoints

**Files:**

- `config/save_lead_endpoint.php` — POST → `saveLead($_POST)` with **no session check**
- `config/update_lead_endpoint.php` — POST → `updateLead($_POST)` with **no session check**

**Impact:** Anonymous actors can create or modify CRM leads via direct POST if the endpoint URL is known.  
**Fix:** Require valid session; validate `user_id` / permissions; rate-limit; optionally require CSRF token.

---

### C4 — Unauthenticated workflow REST API

**File:** `api/index.php`  
**Routes:** `GET/POST/PUT/DELETE /api/workflows`, `execute-now`, email templates, logs, etc.  
**Issue:** No session, API key, or IP restriction.  
**Impact:** Anyone can list, create, modify, delete workflows and trigger execution against local MySQL.  
**Fix:** Require session or signed API token; restrict `cron/` and `api/` at web-server level.

---

### C5 — Public demo credentials + auto-login path

**Files:**

- `config/demo_credentials.php` — username/password in repo
- `login_explore.php` — renders password in HTML (`<dd>`, `data-password`)
- `otp_verify.php` — auto-logs in as demo user after OTP

**Impact:** Shared test account gives broad CRM access; credentials are trivially harvestable.  
**Fix:** Use per-demo isolated tenants, time-limited tokens, or IP allowlists; never commit real passwords; rotate `test_limo_crm` password if used in production DB.

---

### C6 — Stripe live publishable key in config

**File:** `config/agreement_config.php`  
**Issue:** `pk_live_…` committed (publishable keys are less sensitive than secret keys, but still tie to live account).  
**Fix:** Load from env; ensure secret keys never appear in this repo.

---

## High-severity issues

### H1 — Password change IDOR

**File:** `config/change_password_endpoint.php`  
**Issue:** Accepts `$_POST['id']` without requiring login or verifying `id === $_SESSION['user']['id']`.  
**Impact:** Attacker who knows/guesses a user UUID could attempt password changes (still needs current password unless backend is weak).  
**Fix:** Mirror `profile_update_endpoint.php`: reject if session empty; reject if posted `id` ≠ session id.

---

### H2 — Send lead email without strict auth

**File:** `config/send_lead_email_endpoint.php`  
**Issue:** Starts session but **does not exit** when `$_SESSION['user']['id']` is empty.  
**Impact:** Unauthenticated email dispatch (formal quote / agreement) for arbitrary `lead_id` if SuiteCRM backend does not reject.  
**Fix:** Hard-fail with 401 when session user id is missing.

---

### H3 — No CSRF protection

**Scope:** All forms and jQuery `$.ajax` POSTs (`login.php`, settings, leads, workflows, config endpoints).  
**Impact:** Cross-site request forgery against logged-in users (state-changing actions).  
**Fix:** Issue per-session CSRF token; validate on every mutating endpoint.

---

### H4 — TLS certificate verification disabled

**Files:** `config/api.php`, `email_actions.php`, `limogen-widget/widget-frame.php`, `app/Workflows/WorkflowExecutionEngine.php`, `cron/workflow_executor.php`  
**Pattern:** `CURLOPT_SSL_VERIFYPEER => false`, `CURLOPT_SSL_VERIFYHOST => 0`  
**Impact:** MITM on all SuiteCRM and tracking traffic.  
**Fix:** Enable verification in production; pin CA bundle if needed.

---

### H5 — Admin-only pages lack server-side admin checks

**Pages:** `users.php`, `role_management.php`, `pricing.php`, `payment_methods.php`, `transactions.php`, `email_settings.php`, etc.  
**Issue:** Sidebar hides links via `limo_nav_session_admin_full_access()`, but pages only include `header.php` (any logged-in user).  
**Impact:** Non-admin users can open admin URLs directly unless SuiteCRM API enforces authorization.  
**Fix:** Add at top of each admin page:

```php
if (!limo_nav_session_admin_full_access()) {
    header('Location: index.php');
    exit;
}
```

---

### H6 — Agreement / lead data accessible by UUID only

**Files:** `agreement.php`, `config/agreement_api.php`  
**Issue:** Public access via `?lead_id={uuid}` without signed token or expiry.  
**Impact:** Lead PII enumeration if UUIDs are predictable or leaked; payment surface exposed.  
**Fix:** Signed, time-limited agreement links (see `database/limo_agreement_config.sample.php` `link_secret` pattern).

---

### H7 — Log and data files potentially web-readable

**Paths:**

- `logs/application_*.log`, `logs/php_errors.log`, `logs/crm_click.log`
- `data/leads.json` (sample lead PII: names, emails, phones, addresses)
- `welcome.php` writes `crm_visit.logs` at repo root (if present)

**Issue:** No `.htaccess` deny rules under `logs/` or `data/`.  
**Impact:** Information disclosure via direct URL fetch on Apache.  
**Fix:** Deny HTTP access; move logs outside web root.

---

### H8 — Cron scripts executable via HTTP

**Files:** `cron/workflow_engine.php`, `cron/workflow_executor.php`  
**Issue:** No `php_sapi_name() === 'cli'` guard.  
**Impact:** If web server serves `.php` in `cron/`, workflows can be triggered remotely.  
**Fix:** CLI-only guard + deny web access in server config.

---

## Medium-severity issues

### M1 — Stored/reflected XSS in lead table rendering

**File:** `components/tables/leads.php`  
**Issue:** CRM fields echoed without `htmlspecialchars()` (`first_name`, `email1`, addresses, etc.).  
**Impact:** Malicious lead data → script execution in staff browsers.  
**Fix:** Escape all dynamic output; use `ENT_QUOTES, 'UTF-8'`.

---

### M2 — Debug mode enabled on profile page

**File:** `profile.php`  
```php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```
**Impact:** Path/stack trace disclosure in production.  
**Fix:** Remove or gate behind `APP_DEBUG` env flag.

---

### M3 — Sidebar active-state URL parsing is fragile

**File:** `components/layout/sidebar.php`  
```php
$url = explode('/', $_SERVER['REQUEST_URI']);
$url = $url[2];
```
**Issue:** Assumes fixed path depth (e.g. `/limocrm/leads.php`). Breaks in subpaths, rewrites, or different virtual hosts → wrong active nav, possible logic bugs elsewhere.  
**Fix:** Use `basename($_SERVER['SCRIPT_NAME'])`.

---

### M4 — Session not regenerated on login

**File:** `config/login.php`  
**Issue:** No `session_regenerate_id(true)` after successful auth.  
**Impact:** Session fixation if an attacker seeds a session ID.  
**Fix:** Regenerate ID on login and privilege change.

---

### M5 — `.gitignore` does not protect secrets

**File:** `.gitignore`  
**Currently ignores:** log files only.  
**Missing:** `config/database.php`, `config/agreement_config.php`, `welcome.php` secrets, `.env*`.

---

### M6 — `login_explore.php` tracking proxy

**File:** `login_explore.php`  
**Issue:** Accepts `?send_id=` / `?id=` and forwards to external mail-server tracking URL; logs to `logs/crm_click.log`.  
**Impact:** Open redirect/SSRF-style abuse if parameter validation is loose; log injection.  
**Fix:** Validate `send_id` format; use allowlisted tracking base URL only.

---

### M7 — Unused / dead permission module

**File:** `acll.php` — not included anywhere; permissions flow through `config/session_permissions.php` only.  
**Impact:** Confusion during security reviews; possible duplicate logic if re-enabled incorrectly.

---

### M8 — Remote API single point of trust

**File:** `config/api.php` → `https://zabrin.xyz/limogen/index.php?entryPoint=CustomEntryPoint`  
**Issue:** All CRM authorization ultimately depends on remote SuiteCRM implementation (not visible in this repo).  
**Recommendation:** Document and test remote authorization for each `action`; do not rely on UI-only checks here.

---

### M9 — Header template placeholders (Envato / demo notifications)

**File:** `components/layout/header.php`  
**Issue:** Links to `https://1.envato.market/…` (template purchase ads); fake notification linking to `chat.php`.  
**Impact:** Confusing UX; accidental outbound clicks; broken internal link.

---

### M10 — `signup.php` is a stock template page

**File:** `signup.php`  
**Issue:** Still branded “Xintra / Spruko”; posts to CRM signup without fitting current auth model.  
**Impact:** User confusion; potential unintended account creation surface.

---

## Broken links & missing assets

Automated scan found **23** unresolved internal references. Many sidebar targets are inside **HTML comments** (planned features). **Active** broken items that affect users today:

### Active broken navigation / links

| Link | Referenced from | Notes |
|------|-----------------|-------|
| `chat.php` | `components/layout/header.php` | Notification dropdown “New Messages” — **page does not exist** |
| `index.html` | `signup.php` | Logo/home link — **file does not exist** |
| `j#` | `components/layout/footer.php` | Typo; should be `#` or `javascript:void(0)` — **LimoGen footer link broken** |

### Missing brand & UI assets

Only `assets/images/brand-logos/abcs.svg` exists under brand-logos. Referenced but **missing**:

| Asset | Used in |
|-------|---------|
| `favicon.ico` | All auth pages, `header.php` |
| `desktop-dark.png`, `desktop-white.png` | `login.php`, `login_explore.php`, `otp_verify.php`, `header.php` |
| `desktop-logo.png`, `toggle-*.png`, `logo.png` | `sidebar.php`, `header.php` |
| `assets/images/faces/1.jpg` (and similar) | `header.php` notification avatars |
| `assets/images/media/login-a.svg` | Login hero (exists ✓) |
| `assets/js/authentication-main.js` | Referenced in `signup.php` (commented out — low impact) |

**User-visible effect:** Broken favicons, missing logos (partially masked by `onerror="this.style.display='none'"` on login pages), blank avatars in header.

### Commented sidebar links (not shown in UI, but code debt)

These PHP files are linked only in commented blocks — safe for now, but easy to re-enable accidentally:

`vendors.php`, `quotes.php`, `calendar.php`, `email_tracking.php`, `commissions.php`, `campaigns.php`, `chat.php`, `coupons.php`, `campaign_builder.php`, `employee_analytics.php`, `vendor_tiers.php`, `audit_log.php`, `system.php`

### False positives from scanner

| Pattern | Reason |
|---------|--------|
| `$tracking_pixel_url`, `$unsubscribe_url` | Email template **variables**, not URLs (`email_template.php`) |
| `tel:`, `tel:<` | PHP-generated phone hrefs in `lead.php` / `edit_lead.php` |
| `@` in `agreement_api.php` | Email address in PDF/HTML template |

---

## Authentication matrix (config endpoints)

| Endpoint | Session required? | Notes |
|----------|-------------------|-------|
| `config/login.php` | Public | Creates session |
| `config/logout.php` | Session | Clears session |
| `config/profile_update_endpoint.php` | **Yes** | Validates own id |
| `config/change_password_endpoint.php` | **Weak** | Accepts arbitrary POST `id` |
| `config/outbound_email_endpoint.php` | **Yes** | |
| `config/get_agreement_link_endpoint.php` | **Yes** | |
| `config/send_lead_email_endpoint.php` | **Weak** | No hard stop without session |
| `config/save_lead_endpoint.php` | **No** | Critical |
| `config/update_lead_endpoint.php` | **No** | Critical |
| `config/agreement_api.php` | Public | By design for signing |
| `api/index.php` | **No** | Critical |

---

## Public attack surface (by design)

| Entry | Risk to monitor |
|-------|-----------------|
| `welcome.php?id={uuid}` | UUID guessing; Supabase writes |
| `otp_verify.php` | OTP brute force — ensure rate limits on mail-server |
| `login_explore.php` | Demo account abuse |
| `agreement.php?lead_id=` | Lead UUID exposure |
| `instantQuoteForm.php` / widget | Spam leads, embed abuse |
| `email_actions.php` | Tracking pixel; email_id validation present (UUID regex ✓) |

---

## Prioritized remediation roadmap

### Phase 1 — Same day

- [ ] Rotate DB password, Supabase service role, demo user password, review Stripe keys
- [ ] Add auth checks to `save_lead_endpoint.php`, `update_lead_endpoint.php`, `api/index.php`
- [ ] Fix `change_password_endpoint.php` and `send_lead_email_endpoint.php` auth
- [ ] Deny web access to `logs/`, `data/`, `cron/`
- [ ] Remove `display_errors` from `profile.php`

### Phase 2 — This week

- [ ] Introduce `.env` + secret loading helper
- [ ] CSRF tokens on all mutating endpoints
- [ ] `session_regenerate_id()` on login
- [ ] Enable SSL verification for outbound cURL
- [ ] Server-side admin guards on admin pages
- [ ] Escape output in `components/tables/leads.php` and similar views

### Phase 3 — Next sprint

- [ ] Signed agreement links with expiry
- [ ] Replace missing brand assets or update references to existing `abcs.svg`
- [ ] Fix footer `j#`, header `chat.php`, signup `index.html`
- [ ] Remove Envato template links from `header.php`
- [ ] Fix sidebar `$url = $url[2]` → `basename()`
- [ ] Add CLI guard to cron scripts
- [ ] Central auth middleware for `config/*_endpoint.php`

---

## Files reviewed (representative)

- **Auth/layout:** `login.php`, `login_explore.php`, `otp_verify.php`, `welcome.php`, `components/layout/header.php`, `sidebar.php`, `footer.php`, `config/session_permissions.php`
- **Endpoints:** `config/*.php`, `api/index.php`
- **Public:** `agreement.php`, `instantQuoteForm.php`, `email_actions.php`, `limogen-widget/widget-frame.php`
- **Data:** `config/database.php`, `config/demo_credentials.php`, `config/agreement_config.php`, `data/leads.json`
- **App:** `components/tables/leads.php`, `profile.php`, `users.php`, `edit_lead.php`, `signup.php`

---

## Disclaimer

This audit is **static** — it did not include dynamic penetration testing, SuiteCRM backend code review, or server hardening (Apache/PHP.ini). Remote API behavior may mitigate some local gaps; verify each `action` on the SuiteCRM side independently.

*Generated from codebase analysis on 2026-06-03.*
