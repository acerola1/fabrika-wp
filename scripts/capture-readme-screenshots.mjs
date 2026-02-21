import { chromium } from 'playwright';

const base = process.env.WP_BASE_URL || 'http://localhost:8080';
const user = process.env.WP_ADMIN_USER || 'ci-admin';
const pass = process.env.WP_ADMIN_PASS || 'Admin12345!';

const targets = [
  {
    url: `${base}/wp-admin/admin.php?page=fabrika62-home`,
    file: 'docs/readme-screenshots/01-fabrika-kezdolap.png',
    waitFor: 'h1',
  },
  {
    url: `${base}/wp-admin/edit.php?post_type=fabrika_termek&page=fabrika62-termek-add`,
    file: 'docs/readme-screenshots/02-termek-feltoltes.png',
    waitFor: 'form[action*="admin-post.php"]',
  },
  {
    url: `${base}/wp-admin/edit-tags.php?taxonomy=fabrika_tag&post_type=fabrika_termek`,
    file: 'docs/readme-screenshots/03-termek-cimkek.png',
    waitFor: '#col-left',
  },
  {
    url: `${base}/wp-admin/edit.php?post_type=fabrika_termek`,
    file: 'docs/readme-screenshots/04-termek-lista.png',
    waitFor: '.wp-list-table',
  },
];

async function login(page) {
  await page.goto(`${base}/wp-login.php`, { waitUntil: 'domcontentloaded' });

  // If already logged in, /wp-login.php redirects to admin dashboard.
  if (!page.url().includes('/wp-login.php')) return;

  await page.fill('#user_login', user);
  await page.fill('#user_pass', pass);
  await page.click('#wp-submit');
  await page.waitForURL((url) => !url.toString().includes('/wp-login.php'), { timeout: 15000 });
}

const browser = await chromium.launch({ headless: true });
const context = await browser.newContext({ viewport: { width: 1600, height: 1100 } });
const page = await context.newPage();

try {
  await login(page);

  for (const t of targets) {
    await page.goto(t.url, { waitUntil: 'domcontentloaded' });
    await page.waitForSelector(t.waitFor, { timeout: 15000 });
    await page.screenshot({ path: t.file, fullPage: true });
    console.log(`saved: ${t.file}`);
  }
} finally {
  await browser.close();
}
