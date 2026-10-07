#!/usr/bin/env node
/**
 * Capture LimoCRM module screenshots for the brochure.
 * Usage (from docs/brochure):
 *   npm init -y && npm i playwright && npx playwright install chromium
 *   node capture.mjs
 *
 * Requires XAMPP running at http://localhost/limocrm/
 */
import { chromium } from 'playwright';
import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const manifest = JSON.parse(fs.readFileSync(path.join(__dirname, 'capture-manifest.json'), 'utf8'));
const outDir = path.join(__dirname, 'screenshots');

if (!fs.existsSync(outDir)) {
  fs.mkdirSync(outDir, { recursive: true });
}

const base = manifest.baseUrl.replace(/\/$/, '');
const { width, height } = manifest.viewport;

async function login(page) {
  const formUrl = `${base}${manifest.login.formUrl || '/login.php'}`;
  await page.goto(formUrl, { waitUntil: 'networkidle', timeout: 60000 });
  await page.fill('#username', manifest.login.user);
  await page.fill('#signin-password', manifest.login.pass);
  await page.click('#signinBtn');
  await page.waitForURL(/index\.php/, { timeout: 60000 }).catch(async () => {
    await page.waitForTimeout(3000);
  });
}

async function shot(page, relPath, filename, fullPage = false) {
  const url = `${base}${relPath}`;
  console.log(`  → ${filename} (${url})`);
  try {
    await page.goto(url, { waitUntil: 'networkidle', timeout: 60000 });
    await page.waitForTimeout(1500);
    await page.screenshot({
      path: path.join(outDir, filename),
      fullPage: !!fullPage,
    });
    return true;
  } catch (err) {
    console.warn(`  ✗ Failed ${filename}:`, err.message);
    return false;
  }
}

async function main() {
  console.log('Launching browser…');
  const browser = await chromium.launch({ headless: true });
  const context = await browser.newContext({ viewport: { width, height } });
  const page = await context.newPage();

  console.log('Logging in…');
  await login(page);

  console.log('Authenticated pages:');
  for (const item of manifest.authenticatedPages) {
    await shot(page, item.path, item.file, item.fullPage);
  }

  console.log('Public pages:');
  for (const item of manifest.publicPages) {
    await shot(page, item.path, item.file, item.fullPage);
  }

  if (manifest.imageMap) {
    console.log('Copying alias screenshots:');
    for (const [target, source] of Object.entries(manifest.imageMap)) {
      const src = path.join(outDir, source);
      const dst = path.join(outDir, target);
      if (fs.existsSync(src)) {
        fs.copyFileSync(src, dst);
        console.log(`  → ${target} ← ${source}`);
      }
    }
  }

  await browser.close();
  console.log('Done. Screenshots in', outDir);
}

main().catch((e) => {
  console.error(e);
  process.exit(1);
});
