# LimoCRM module brochure

Print-ready HTML brochure built from [CLIENT_PITCH_MODULES.md](../CLIENT_PITCH_MODULES.md).

## Source files

| File | Purpose |
|------|---------|
| **`design.html`** | **Current brochure design** (cover, TOC, module sections, CTA) — use this for PDF export |
| `index.html` | Earlier brochure layout (kept for reference) |
| `brochure.css` | Styles for `index.html` only |
| `screenshots/*.png` | Module screenshots |

## View in browser

```
http://localhost/limocrm/docs/brochure/design.html
```

## Export as PDF

### Automated (recommended)

```bash
cd docs/brochure
node export-pdf.mjs
```

Exports **`design.html`** → `LimoCRM_Modules_Brochure.pdf` (A4, background graphics on). The script uses print media + CSS `@page` margins from `design.html`. Page breaks and spacing are tuned in the `@media print` block inside `design.html`.

Optional: export a different HTML file:

```bash
node export-pdf.mjs index.html
```

### Manual (Chrome / Edge)

1. Open `design.html`
2. Press **Ctrl+P** → **Save as PDF**
3. Paper: **A4**, enable **Background graphics**
4. Save as `LimoCRM_Modules_Brochure.pdf`

## Refresh screenshots

Screenshots live in `screenshots/`. Re-capture when the UI changes.

Requirements: Node.js, XAMPP at `http://localhost/limocrm/`, demo login (`test_limo_crm` — see `config/demo_credentials.php`).

```bash
cd docs/brochure
node capture.mjs
```

URLs and filenames: [capture-manifest.json](capture-manifest.json).

Manual capture: log in as admin, open each URL from the manifest at **1440×900**, save PNGs into `screenshots/`.
