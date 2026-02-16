/**
 * Visual regression test: static 6-2 reference vs WordPress theme.
 *
 * Usage:
 *   1. Start Vite dev server:  npx vite --port 3999
 *   2. Ensure WP is running:   docker compose up -d
 *   3. Run:                     node tests/visual-regression.mjs
 *
 * Output: tests/screenshots/ directory with side-by-side PNGs.
 */

import { chromium } from 'playwright';
import { existsSync, mkdirSync } from 'fs';
import { resolve, join } from 'path';

const ROOT = resolve(import.meta.dirname, '..');
const OUT = join(ROOT, 'tests', 'screenshots');

const WP_URL = 'http://localhost:8080';
const STATIC_URL = 'http://localhost:3999/6-2/';

const VIEWPORTS = [
  { name: 'desktop', width: 1440, height: 900 },
  { name: 'mobile', width: 375, height: 812 },
];

const SECTIONS = [
  { name: 'hero', selector: '#hero' },
  { name: 'termekek', selector: '#termekek' },
  { name: 'galeria', selector: '#galeria' },
  { name: 'rendeles', selector: '#rendeles' },
  { name: 'kapcsolat', selector: '#kapcsolat' },
];

async function captureScreenshots(page, baseUrl, label, viewport) {
  await page.setViewportSize({ width: viewport.width, height: viewport.height });

  const separator = baseUrl.includes('?') ? '&' : '?';
  await page.goto(baseUrl + separator + 'parallax=0', { waitUntil: 'networkidle' });
  await page.evaluate(() => document.fonts && document.fonts.ready);

  // Disable animations for stable screenshots
  await page.addStyleTag({
    content: '*, *::before, *::after { animation-duration: 0s !important; transition-duration: 0s !important; }'
  });
  await page.waitForTimeout(500);

  // Full page
  const fullPath = join(OUT, `${label}_${viewport.name}_full.png`);
  await page.screenshot({ path: fullPath, fullPage: true });
  console.log(`  ${fullPath}`);

  // Per-section
  for (const section of SECTIONS) {
    const el = await page.$(section.selector);
    if (el) {
      const sectionPath = join(OUT, `${label}_${viewport.name}_${section.name}.png`);
      await el.screenshot({ path: sectionPath });
      console.log(`  ${sectionPath}`);
    } else {
      console.log(`  [SKIP] ${section.name} not found on ${label}`);
    }
  }
}

async function main() {
  if (!existsSync(OUT)) mkdirSync(OUT, { recursive: true });

  const browser = await chromium.launch();

  try {
    for (const vp of VIEWPORTS) {
      console.log(`\n=== ${vp.name} (${vp.width}x${vp.height}) ===`);

      const ctx = await browser.newContext();
      const page = await ctx.newPage();

      console.log(`\nCapturing STATIC reference...`);
      await captureScreenshots(page, STATIC_URL, 'static', vp);

      console.log(`\nCapturing WORDPRESS...`);
      await captureScreenshots(page, WP_URL, 'wp', vp);

      await ctx.close();
    }

    console.log('\n=== DONE ===');
    console.log(`Screenshots saved to: ${OUT}`);
    console.log('Compare visually: open static_*.png vs wp_*.png side by side.');
  } finally {
    await browser.close();
  }
}

main().catch((err) => {
  console.error(err);
  process.exit(1);
});
