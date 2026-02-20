/**
 * WP admin product quick-add smoke test (local dev).
 *
 * Usage:
 *   WP_ADMIN_USER=... WP_ADMIN_PASS=... node tests/wp-termek-create-smoke.mjs
 *
 * Optional:
 *   WP_URL=http://localhost:8080
 *   WP_PRODUCT_UPLOAD_FILE=./path/to/image.jpg
 */

import { chromium } from 'playwright';
import { resolve, basename, extname, join } from 'path';
import { existsSync, mkdirSync, copyFileSync } from 'fs';

const WP_URL = process.env.WP_URL || 'http://localhost:8080';
const WP_ADMIN_USER = process.env.WP_ADMIN_USER;
const WP_ADMIN_PASS = process.env.WP_ADMIN_PASS;
const UPLOAD_FILE = resolve(process.env.WP_PRODUCT_UPLOAD_FILE || 'wp-content/themes/fabrika-62/test-results/upload-test.jpg');
const TEMP_DIR = resolve(process.env.WP_UPLOAD_TEMP_DIR || 'test-results');

function required(name, value) {
  if (!value) throw new Error(`Missing env var: ${name}`);
  return value;
}

function nowStamp() {
  const d = new Date();
  const pad = (n) => String(n).padStart(2, '0');
  return `${d.getFullYear()}${pad(d.getMonth() + 1)}${pad(d.getDate())}-${pad(d.getHours())}${pad(d.getMinutes())}${pad(d.getSeconds())}`;
}

async function main() {
  required('WP_ADMIN_USER', WP_ADMIN_USER);
  required('WP_ADMIN_PASS', WP_ADMIN_PASS);

  if (!existsSync(UPLOAD_FILE)) throw new Error(`Upload file not found: ${UPLOAD_FILE}`);
  if (!existsSync(TEMP_DIR)) mkdirSync(TEMP_DIR, { recursive: true });

  const ext = extname(basename(UPLOAD_FILE)) || '.jpg';
  const uploadPath = join(TEMP_DIR, `wp-termek-${Date.now()}${ext}`);
  copyFileSync(UPLOAD_FILE, uploadPath);

  const browser = await chromium.launch();
  const ctx = await browser.newContext();
  const page = await ctx.newPage();

  try {
    await page.goto(`${WP_URL}/wp-login.php`, { waitUntil: 'domcontentloaded' });
    await page.fill('#user_login', WP_ADMIN_USER);
    await page.fill('#user_pass', WP_ADMIN_PASS);
    await page.click('#wp-submit');

    await page.waitForLoadState('domcontentloaded');
    if (await page.locator('#login_error').count()) {
      const err = (await page.locator('#login_error').innerText()).trim();
      throw new Error(`Login failed: ${err}`);
    }

    // Quick-add page (Termekek -> Uj termek).
    await page.goto(`${WP_URL}/wp-admin/edit.php?post_type=fabrika_termek&page=fabrika62-termek-add`, {
      waitUntil: 'domcontentloaded',
    });

    const title = `Teszt termek ${nowStamp()}`;
    await page.fill('#termek_title', title);
    await page.fill('#termek_description', 'Automata smoke teszt. Nyugodtan torolheto.');
    await page.fill('#termek_price', '3500');

    const firstTag = page.locator('input[type="checkbox"][name="termek_tags[]"]').first();
    if (await firstTag.count()) await firstTag.check();

    await page.setInputFiles('#termek_image', uploadPath);

    const nav = page.waitForNavigation({ waitUntil: 'domcontentloaded', timeout: 120_000 });
    await page.getByRole('button', { name: /Termek mentese/i }).click();
    await nav;

    const url = page.url();
    const created = new URL(url).searchParams.get('created');
    if (!created || !/^\d+$/.test(created)) {
      throw new Error(`Product create redirect did not include ?created=. URL: ${url}`);
    }

    // Basic verification: edit page loads.
    await page.goto(`${WP_URL}/wp-admin/post.php?post=${created}&action=edit`, { waitUntil: 'domcontentloaded' });
    if (!(await page.locator('#poststuff').count())) {
      throw new Error('Product edit page did not load as expected.');
    }

    console.log('Product appears created:', created, '-', title);
  } finally {
    await ctx.close();
    await browser.close();
  }
}

main().catch((err) => {
  console.error(err);
  process.exit(1);
});

