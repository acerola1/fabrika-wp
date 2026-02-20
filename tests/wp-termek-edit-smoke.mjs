/**
 * WP unified product edit smoke test.
 *
 * Usage:
 *   WP_ADMIN_USER=... WP_ADMIN_PASS=... node tests/wp-termek-edit-smoke.mjs
 */

import { chromium } from 'playwright';

const WP_URL = process.env.WP_URL || 'http://localhost:8080';
const WP_ADMIN_USER = process.env.WP_ADMIN_USER;
const WP_ADMIN_PASS = process.env.WP_ADMIN_PASS;

function required(name, value) {
  if (!value) throw new Error(`Missing env var: ${name}`);
  return value;
}

async function loginToWpAdmin(page) {
  for (let i = 0; i < 8; i += 1) {
    await page.goto(`${WP_URL}/wp-admin/`, { waitUntil: 'domcontentloaded' });

    if (await page.locator('#wpadminbar').count()) return;

    if (await page.locator('#user_login').count()) {
      await page.fill('#user_login', WP_ADMIN_USER);
      await page.fill('#user_pass', WP_ADMIN_PASS);
      await page.click('#wp-submit');
      await page.waitForLoadState('domcontentloaded');

      if (await page.locator('#wpadminbar').count()) return;
      if (await page.locator('#login_error').count()) {
        const err = (await page.locator('#login_error').innerText()).trim();
        throw new Error(`Login failed: ${err}`);
      }
    }

    await page.waitForTimeout(2000);
  }

  throw new Error(`Could not reach WP admin login/dashboard. Last URL: ${page.url()}`);
}

async function main() {
  required('WP_ADMIN_USER', WP_ADMIN_USER);
  required('WP_ADMIN_PASS', WP_ADMIN_PASS);

  const browser = await chromium.launch();
  const ctx = await browser.newContext();
  const page = await ctx.newPage();

  try {
    await loginToWpAdmin(page);

    await page.goto(`${WP_URL}/wp-admin/edit.php?post_type=fabrika_termek`, { waitUntil: 'domcontentloaded' });
    const firstRowEdit = page.locator('#the-list tr .row-actions .edit a').first();
    if (!(await firstRowEdit.count())) {
      throw new Error('No product found to edit. Run create smoke first.');
    }
    const href = await firstRowEdit.getAttribute('href');
    if (!href) throw new Error('Edit link missing href.');

    await page.goto(href, { waitUntil: 'domcontentloaded' });
    const editUrl = page.url();
    if (!editUrl.includes('page=fabrika62-termek-add') || !editUrl.includes('edit=')) {
      throw new Error(`Edit URL did not redirect to unified form: ${editUrl}`);
    }
    const editId = new URL(editUrl).searchParams.get('edit');
    if (!editId) throw new Error('Missing edit id in unified form URL.');

    const newTitle = `Frissitett teszt termek ${Date.now()}`;
    await page.fill('#termek_title', newTitle);
    await page.fill('#termek_price', '4900');

    const nav = page.waitForNavigation({ waitUntil: 'domcontentloaded', timeout: 120_000 }).catch(() => null);
    await page.getByRole('button', { name: /Termék frissítése|Termek frissitese/i }).click();
    await nav;

    const updatedUrl = page.url();
    const updatedNotice = await page.locator('.notice-success', { hasText: /Termék frissítve|Termek frissitve/i }).count();
    if (!updatedUrl.includes('updated=') && !updatedNotice) {
      throw new Error(`Edit save did not return success indicator. URL: ${updatedUrl}`);
    }

    await page.goto(`${WP_URL}/termekek/`, { waitUntil: 'domcontentloaded' });
    const hasTitle = await page.locator('.product-card h3', { hasText: newTitle }).count();
    if (!hasTitle) {
      throw new Error('Updated title is not visible on /termekek/.');
    }

    console.log('Product update smoke OK:', editId, newTitle);
  } finally {
    await ctx.close();
    await browser.close();
  }
}

main().catch((err) => {
  console.error(err);
  process.exit(1);
});
