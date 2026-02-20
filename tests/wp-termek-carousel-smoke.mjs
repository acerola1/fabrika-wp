/**
 * WP product modal/carousel smoke test.
 *
 * Usage:
 *   node tests/wp-termek-carousel-smoke.mjs
 */

import { chromium } from 'playwright';

const WP_URL = process.env.WP_URL || 'http://localhost:8080';

async function main() {
  const browser = await chromium.launch();
  const ctx = await browser.newContext({ viewport: { width: 1440, height: 1200 } });
  const page = await ctx.newPage();

  try {
    await page.goto(`${WP_URL}/termekek/`, { waitUntil: 'networkidle' });

    const cards = page.locator('.product-card');
    const count = await cards.count();
    if (count < 1) throw new Error('No product card found for modal test.');

    const firstCard = cards.first();
    const firstId = (await firstCard.getAttribute('data-id')) || '';
    if (!firstId) throw new Error('First card has no data-id.');

    await firstCard.click();
    await page.waitForTimeout(250);

    const modal = page.locator('#product-modal');
    if ((await modal.count()) === 0) throw new Error('Modal container missing.');
    const modalOpen = await modal.evaluate((el) => el.classList.contains('modal-open') && el.style.display !== 'none');
    if (!modalOpen) throw new Error('Modal did not open after card click.');

    const modalId = (await page.locator('#modal-product-id').innerText()).trim();
    const modalTitle = (await page.locator('#modal-title').innerText()).trim();
    const modalImgSrc = (await page.locator('#modal-img').getAttribute('src')) || '';
    if (!modalId || !modalTitle || !modalImgSrc) {
      throw new Error('Modal content is incomplete (id/title/image).');
    }

    if (count >= 2) {
      await page.locator('#modal-next').click();
      await page.waitForTimeout(150);
      const nextId = (await page.locator('#modal-product-id').innerText()).trim();
      if (!nextId || nextId === modalId) {
        throw new Error('Carousel next navigation did not switch product.');
      }

      await page.keyboard.press('ArrowLeft');
      await page.waitForTimeout(150);
      const backId = (await page.locator('#modal-product-id').innerText()).trim();
      if (backId !== modalId) {
        throw new Error('Carousel ArrowLeft navigation did not return to previous product.');
      }
    }

    await page.keyboard.press('Escape');
    await page.waitForTimeout(280);
    const modalClosed = await modal.evaluate((el) => !el.classList.contains('modal-open'));
    if (!modalClosed) throw new Error('Modal did not close with Escape.');

    console.log('Carousel smoke OK');
  } finally {
    await ctx.close();
    await browser.close();
  }
}

main().catch((err) => {
  console.error(err);
  process.exit(1);
});
