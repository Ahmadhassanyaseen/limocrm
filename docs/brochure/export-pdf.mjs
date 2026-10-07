/**
 * Export brochure HTML to PDF via Playwright.
 * Usage: node export-pdf.mjs [html-file]
 * Default source: design.html
 */
import { chromium } from 'playwright';
import path from 'path';
import { fileURLToPath } from 'url';

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const htmlFile = process.argv[2] || 'design.html';
const htmlPath = path.join(__dirname, htmlFile);
const outPath = path.join(__dirname, 'LimoCRM_Modules_Brochure.pdf');
const fileUrl = 'file:///' + htmlPath.replace(/\\/g, '/');

async function waitForImages(page) {
  await page.evaluate(async () => {
    const images = [...document.images];
    await Promise.all(
      images.map((img) => {
        if (img.complete && img.naturalWidth > 0) return Promise.resolve();
        return new Promise((resolve) => {
          img.addEventListener('load', resolve, { once: true });
          img.addEventListener('error', resolve, { once: true });
        });
      })
    );
  });
}

async function scrollToLoadAll(page) {
  await page.evaluate(async () => {
    const step = Math.max(window.innerHeight, 800);
    const max = document.body.scrollHeight;
    for (let y = 0; y <= max; y += step) {
      window.scrollTo(0, y);
      await new Promise((r) => setTimeout(r, 50));
    }
    window.scrollTo(0, 0);
  });
}

async function main() {
  const browser = await chromium.launch({ headless: true });
  const page = await browser.newPage();
  await page.goto(fileUrl, { waitUntil: 'domcontentloaded', timeout: 60000 });
  await page.evaluate(() => document.fonts.ready);
  await page.waitForTimeout(2000);
  await scrollToLoadAll(page);
  await waitForImages(page);
  await page.emulateMedia({ media: 'print' });
  await page.pdf({
    path: outPath,
    format: 'A4',
    printBackground: true,
    preferCSSPageSize: true,
    margin: { top: '0', bottom: '0', left: '0', right: '0' },
  });
  await browser.close();
  console.log('PDF saved:', outPath);
  console.log('Source:', htmlPath);
}

main().catch((e) => {
  console.error(e);
  process.exit(1);
});
