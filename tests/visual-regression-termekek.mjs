/**
 * Visual regression helper: static 6-3 catalog mock vs WordPress /termekek/.
 *
 * Usage:
 *   1) Ensure mock is reachable (e.g. Vite): http://localhost:5174/6-3/termekek.html
 *   2) Ensure WP is running:               http://localhost:8080/termekek/
 *   3) Run:                                node tests/visual-regression-termekek.mjs
 *
 * Optional env:
 *   STATIC_URL=http://localhost:5174/6-3/termekek.html
 *   WP_URL=http://localhost:8080/termekek/
 */

import { chromium } from 'playwright';
import { existsSync, mkdirSync } from 'fs';
import { resolve, join } from 'path';

const ROOT = resolve(import.meta.dirname, '..');
const OUT = join(ROOT, 'tests', 'screenshots');

const STATIC_URL = process.env.STATIC_URL || 'http://localhost:5174/6-3/termekek.html';
const WP_URL = process.env.WP_URL || 'http://localhost:8080/termekek/';

const VIEWPORTS = [
  { name: 'desktop', width: 1440, height: 900 },
  { name: 'mobile', width: 375, height: 812 },
];

async function gotoStable(page, url) {
  await page.goto(url, { waitUntil: 'networkidle' });
  await page.evaluate(() => document.fonts && document.fonts.ready);
  await page.addStyleTag({
    content: '*, *::before, *::after { animation-duration: 0s !important; transition-duration: 0s !important; }',
  });
  await page.waitForTimeout(250);
}

async function capture(page, url, label, viewport) {
  await page.setViewportSize({ width: viewport.width, height: viewport.height });
  await gotoStable(page, url);

  const fullPath = join(OUT, `${label}_${viewport.name}_termekek_full.png`);
  await page.screenshot({ path: fullPath, fullPage: true });
  console.log(`  ${fullPath}`);

  const firstCard = page.locator('.product-card').first();
  if (await firstCard.count()) {
    await firstCard.hover();
    await page.waitForTimeout(50);
    const hoverPath = join(OUT, `${label}_${viewport.name}_termekek_card_hover.png`);
    await firstCard.screenshot({ path: hoverPath });
    console.log(`  ${hoverPath}`);
  } else {
    console.log(`  [SKIP] .product-card not found on ${label}`);
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

      console.log('\nCapturing STATIC mock...');
      await capture(page, STATIC_URL, 'static', vp);

      console.log('\nCapturing WORDPRESS...');
      await capture(page, WP_URL, 'wp', vp);

      await ctx.close();
    }

    console.log('\n=== DONE ===');
    console.log(`Screenshots saved to: ${OUT}`);
    console.log('Compare: static_* vs wp_* PNGs (full + hovered card).');
  } finally {
    await browser.close();
  }
}

main().catch((err) => {
  console.error(err);
  process.exit(1);
});

