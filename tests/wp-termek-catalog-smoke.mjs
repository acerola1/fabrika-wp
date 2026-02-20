/**
 * WP catalog + CTA chain smoke test.
 *
 * Usage:
 *   node tests/wp-termek-catalog-smoke.mjs
 */

import { chromium } from 'playwright';

const WP_URL = process.env.WP_URL || 'http://localhost:8080';

async function main() {
  const browser = await chromium.launch();
  const ctx = await browser.newContext({ viewport: { width: 1440, height: 1400 } });
  const page = await ctx.newPage();

  try {
    await page.goto(`${WP_URL}/`, { waitUntil: 'networkidle' });

    const heroCatalogBtn = page.locator('#hero a:has-text("Katalógus")').first();
    if (!(await heroCatalogBtn.count())) throw new Error('Hero "Katalógus" button missing.');
    await heroCatalogBtn.click();
    await page.waitForLoadState('networkidle');
    if (!page.url().includes('/termekek/')) throw new Error(`Hero CTA did not open /termekek/: ${page.url()}`);

    if (!(await page.locator('#filter-bar .filter-btn').count())) throw new Error('Filter bar buttons are missing.');
    const firstCard = page.locator('.product-card').first();
    if (!(await firstCard.count())) throw new Error('No product card found on /termekek/.');

    if (!(await firstCard.locator('img').count())) throw new Error('Product card image missing.');
    if (!(await firstCard.locator('h3').count())) throw new Error('Product card title missing.');
    const hasPriceOrFallback = (await firstCard.locator('p.text-lg, p.text-sm.font-semibold').count()) > 0;
    if (!hasPriceOrFallback) throw new Error('Product card has neither price nor fallback price label.');

    const cta = firstCard.locator('a:has-text("Érdekel")').first();
    if (!(await cta.count())) throw new Error('Product CTA missing.');
    await cta.click();
    await page.waitForLoadState('networkidle');
    await page.waitForTimeout(200);

    const landingUrl = page.url();
    if (!landingUrl.includes('?termek=') || !landingUrl.includes('#kapcsolat')) {
      throw new Error(`CTA chain target URL invalid: ${landingUrl}`);
    }

    const termekInput = page.locator('#kapcsolat input#termek, #kapcsolat input[name="your-termek"]').first();
    if (!(await termekInput.count())) throw new Error('Contact product input missing.');
    const val = await termekInput.inputValue();
    if (!val || (!val.includes('–') && !val.includes('-'))) {
      throw new Error(`Contact product prefill missing/invalid: "${val}"`);
    }

    console.log('Catalog + CTA smoke OK:', val);
  } finally {
    await ctx.close();
    await browser.close();
  }
}

main().catch((err) => {
  console.error(err);
  process.exit(1);
});
