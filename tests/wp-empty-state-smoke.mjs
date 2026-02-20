/**
 * WP empty catalog state smoke test.
 *
 * Usage:
 *   node tests/wp-empty-state-smoke.mjs
 */

import { chromium } from 'playwright';

const WP_URL = process.env.WP_URL || 'http://localhost:8080';

async function main() {
  const browser = await chromium.launch();
  const ctx = await browser.newContext({ viewport: { width: 1280, height: 1200 } });
  const page = await ctx.newPage();

  try {
    await page.goto(`${WP_URL}/termekek/`, { waitUntil: 'networkidle' });

    const cards = await page.locator('.product-card').count();
    if (cards !== 0) {
      throw new Error(`Expected empty catalog, but found ${cards} product card(s).`);
    }

    const emptyBox = page.locator('#no-results');
    if (!(await emptyBox.count())) {
      throw new Error('Empty state container (#no-results) is missing.');
    }
    const isVisible = await emptyBox.isVisible();
    if (!isVisible) {
      throw new Error('Empty state container is hidden.');
    }
    const emptyText = (await emptyBox.innerText()).trim().toLowerCase();
    const normalized = emptyText.normalize('NFD').replace(/[\u0300-\u036f]/g, '');
    if (!normalized.includes('nincsenek termek')) {
      throw new Error(`Empty state text is unexpected: "${emptyText}"`);
    }

    console.log('Empty state smoke OK');
  } finally {
    await ctx.close();
    await browser.close();
  }
}

main().catch((err) => {
  console.error(err);
  process.exit(1);
});
