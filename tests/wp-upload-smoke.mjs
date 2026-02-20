/**
 * WP admin upload smoke test (local dev).
 *
 * Usage:
 *   WP_ADMIN_USER=... WP_ADMIN_PASS=... node tests/wp-upload-smoke.mjs
 *
 * Optional:
 *   WP_URL=http://localhost:8080
 *   WP_UPLOAD_FILE=./test-results/upload-test.jpg
 */

import { chromium } from 'playwright';
import { resolve, basename, extname, join } from 'path';
import { existsSync, mkdirSync, copyFileSync } from 'fs';

const WP_URL = process.env.WP_URL || 'http://localhost:8080';
const WP_ADMIN_USER = process.env.WP_ADMIN_USER;
const WP_ADMIN_PASS = process.env.WP_ADMIN_PASS;
const UPLOAD_FILE = resolve(process.env.WP_UPLOAD_FILE || 'wp-content/themes/fabrika-62/test-results/upload-test.png');
const TEMP_DIR = resolve(process.env.WP_UPLOAD_TEMP_DIR || 'test-results');

function required(name, value) {
  if (!value) throw new Error(`Missing env var: ${name}`);
  return value;
}

async function main() {
  required('WP_ADMIN_USER', WP_ADMIN_USER);
  required('WP_ADMIN_PASS', WP_ADMIN_PASS);

  if (!existsSync(UPLOAD_FILE)) throw new Error(`Upload file not found: ${UPLOAD_FILE}`);
  if (!existsSync(TEMP_DIR)) mkdirSync(TEMP_DIR, { recursive: true });

  const stamp = Date.now();
  const srcBase = basename(UPLOAD_FILE);
  const ext = extname(srcBase) || '.png';
  const uploadBase = `wp-upload-${stamp}${ext}`;
  const uploadPath = join(TEMP_DIR, uploadBase);
  copyFileSync(UPLOAD_FILE, uploadPath);

  const browser = await chromium.launch();
  const ctx = await browser.newContext();
  const page = await ctx.newPage();

  try {
    await page.goto(`${WP_URL}/wp-login.php`, { waitUntil: 'domcontentloaded' });
    await page.fill('#user_login', WP_ADMIN_USER);
    await page.fill('#user_pass', WP_ADMIN_PASS);
    await page.click('#wp-submit');

    // Wait for admin to load (or a login error).
    await page.waitForLoadState('domcontentloaded');
    if (await page.locator('#login_error').count()) {
      const err = (await page.locator('#login_error').innerText()).trim();
      throw new Error(`Login failed: ${err}`);
    }

    await page.goto(`${WP_URL}/wp-admin/upload.php`, { waitUntil: 'domcontentloaded' });

    const maxSizeText = page.locator('.max-upload-size');
    if (await maxSizeText.count()) {
      console.log('WP max upload size UI:', (await maxSizeText.innerText()).trim());
    } else {
      console.log('WP max upload size UI: (not found)');
    }

    // Open the uploader on the Media Library page.
    const addBtnLink = page.getByRole('link', { name: /Médiafájl hozzáadása|Media File|Add New Media File/i });
    const addBtnButton = page.getByRole('button', { name: /Médiafájl hozzáadása|Media File|Add New Media File/i });
    if (await addBtnLink.count()) await addBtnLink.first().click();
    else if (await addBtnButton.count()) await addBtnButton.first().click();

    const browseInput = page.locator('#plupload-browse-button input[type="file"]');
    if (await browseInput.count()) {
      await browseInput.first().waitFor({ state: 'attached', timeout: 10_000 });
    }
    const asyncUpload = page
      .locator('#plupload-browse-button input[type="file"], input[type="file"]#async-upload, input[type="file"]')
      .first();

    const uploadResp = page.waitForResponse(
      (r) => r.url().includes('async-upload.php') && r.status() >= 200 && r.status() < 400,
      { timeout: 120_000 }
    );
    await asyncUpload.setInputFiles(uploadPath);
    const resp = await uploadResp;
    const respText = await resp.text().catch(() => '');
    const contentType = resp.headers()['content-type'] || '';
    console.log('Upload response content-type:', contentType);

    const attachmentId =
      respText.match(/media-item-(\d+)/)?.[1] ||
      respText.match(/"id"\s*:\s*(\d+)/)?.[1] ||
      respText.match(/attachment_id\s*=\s*(\d+)/)?.[1] ||
      null;

    if (!attachmentId) {
      const snippet = respText.replace(/\s+/g, ' ').slice(0, 400);
      throw new Error(`Upload response did not include an attachment id. Snippet: ${snippet || '(empty)'}`);
    }

    await page.goto(`${WP_URL}/wp-admin/post.php?post=${attachmentId}&action=edit`, { waitUntil: 'domcontentloaded' });
    if (!(await page.locator('#poststuff').count())) {
      throw new Error('Attachment edit page did not load as expected.');
    }

    console.log('Upload appears successful:', uploadBase, '(attachment id:', attachmentId + ')');
  } finally {
    await ctx.close();
    await browser.close();
  }
}

main().catch((err) => {
  console.error(err);
  process.exit(1);
});
