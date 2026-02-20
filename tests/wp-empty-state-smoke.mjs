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
    async function openCatalogAndFindEmptyState() {
      const candidates = [
        `${WP_URL}/termekek/`,
        `${WP_URL}/?post_type=fabrika_termek`,
      ];
      for (const url of candidates) {
        await page.goto(url, { waitUntil: 'networkidle' });
        const box = page.locator('#no-results');
        if (await box.count()) {
          return { box, url: page.url() };
        }
      }
      return null;
    }

    const result = await openCatalogAndFindEmptyState();
    if (!result) {
      const bodySnippet = (await page.locator('body').innerText()).replace(/\s+/g, ' ').slice(0, 220);
      throw new Error(`Empty state container (#no-results) is missing on archive URLs. Last URL: ${page.url()} Body: ${bodySnippet}`);
    }
    const { box: emptyBox } = result;

    const cards = await page.locator('.product-card').count();
    if (cards !== 0) {
      throw new Error(`Expected empty catalog, but found ${cards} product card(s).`);
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
