import { chromium } from 'playwright';

const BASE_URL = process.env.BASE_URL || 'http://localhost:8080';
const TIMEOUT = Number(process.env.SMOKE_TIMEOUT_MS || 12000);
const SCREENSHOT = process.env.SMOKE_SCREENSHOT || 'tmp/smoke-contact.png';

function fail(msg) {
  console.error(`FAIL: ${msg}`);
  process.exit(1);
}

function info(msg) {
  console.log(`INFO: ${msg}`);
}

async function detectFormId(page) {
  const id = await page.evaluate(() => {
    const form = document.querySelector('#kapcsolat form[id^="wpforms-form-"]');
    if (!form) return null;
    const m = form.id.match(/wpforms-form-(\d+)/);
    return m ? Number(m[1]) : null;
  });
  return Number.isFinite(id) ? id : null;
}

async function getPrefillValue(page) {
  return page.evaluate(() => {
    const kapcsolat = document.getElementById('kapcsolat');
    if (!kapcsolat) return null;
    const input =
      kapcsolat.querySelector('input[name="wpforms[fields][8]"]') ||
      kapcsolat.querySelector('input[id^="wpforms-"][id$="-field_8"]') ||
      kapcsolat.querySelector('input#termek') ||
      kapcsolat.querySelector('input[name="termek"]');
    if (!input) return null;
    return input.value || '';
  });
}

async function fillVisibleRequiredFields(page, formId) {
  await page.evaluate((id) => {
    const form = document.getElementById(`wpforms-form-${id}`);
    if (!form) return;
    const isVisible = (el) => !!(el && el.offsetParent !== null && getComputedStyle(el).visibility !== 'hidden');
    const email = `teszt+${Date.now()}@example.com`;

    form.querySelectorAll('input, textarea, select').forEach((el) => {
      if (!isVisible(el)) return;
      const name = (el.getAttribute('name') || '').toLowerCase();
      if (name.includes('[hp]') || name.includes('[submit]')) return;

      const required = el.hasAttribute('required') || el.classList.contains('wpforms-field-required');
      if (!required) return;

      const tag = el.tagName.toLowerCase();
      const type = (el.getAttribute('type') || '').toLowerCase();

      if (tag === 'textarea') {
        el.value = 'Playwright smoke teszt üzenet.';
      } else if (tag === 'select') {
        if (el.options.length > 1) el.selectedIndex = 1;
      } else if (type === 'email') {
        el.value = email;
      } else if (type === 'checkbox' || type === 'radio') {
        el.checked = true;
      } else {
        el.value = 'Teszt Kitöltés';
      }

      el.dispatchEvent(new Event('input', { bubbles: true }));
      el.dispatchEvent(new Event('change', { bubbles: true }));
    });
  }, formId);
}

async function run() {
  const browser = await chromium.launch({ headless: true });
  const page = await browser.newPage({ viewport: { width: 1440, height: 1100 } });
  page.setDefaultTimeout(TIMEOUT);

  try {
    info(`Base URL: ${BASE_URL}`);

    await page.goto(`${BASE_URL}/?nocache=${Date.now()}#kapcsolat`, { waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(1200);

    const hasContact = await page.locator('#kapcsolat').count();
    if (!hasContact) fail('A #kapcsolat szekció nem található.');

    const formId = await detectFormId(page);
    if (!formId) fail('Nem találtam WPForms űrlapot a kapcsolat szekcióban.');
    info(`Detected form ID: ${formId}`);

    const sectionText = await page.locator('#kapcsolat').innerText();
    const expectedTokens = ['Név', 'E-mail', 'Üzenet', 'Üzenet küldése'];
    for (const t of expectedTokens) {
      if (!sectionText.includes(t)) {
        fail(`Hiányzó szöveg a kapcsolati panelen: "${t}"`);
      }
    }

    await page.goto(`${BASE_URL}/?termek=2083&nev=sor&nocache=${Date.now()}#kapcsolat`, { waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(1000);
    const prefillValue = await getPrefillValue(page);
    if (!prefillValue || !prefillValue.includes('2083')) {
      fail('A ?termek= előtöltés nem működik a termék mezőn.');
    }
    info(`Prefill OK: ${prefillValue}`);

    const ajaxRequests = [];
    page.on('requestfinished', (req) => {
      const url = req.url();
      const body = req.postData() || '';
      if (url.includes('admin-ajax.php') && body.includes('wpforms_submit')) {
        ajaxRequests.push(url);
      }
    });

    let navigated = false;
    page.on('framenavigated', () => {
      navigated = true;
    });

    await fillVisibleRequiredFields(page, formId);
    await page.locator(`#wpforms-submit-${formId}`).click();
    await page.waitForTimeout(3500);

    const confirmation = await page.locator('.wpforms-confirmation-container-full, .wpforms-confirmation-container').first().innerText().catch(() => '');
    const errors = await page.locator('.wpforms-error').allInnerTexts().catch(() => []);

    await page.screenshot({ path: SCREENSHOT, fullPage: true });

    if (navigated) {
      fail('Beküldéskor teljes oldalas navigáció történt (nem AJAX viselkedés).');
    }
    if (ajaxRequests.length === 0) {
      fail('Nem láttam wpforms AJAX submit kérést.');
    }
    if (errors.length > 0) {
      fail(`Validációs hibák maradtak: ${errors.join(' | ')}`);
    }

    info(`AJAX submit requests: ${ajaxRequests.length}`);
    info(`Confirmation: ${confirmation ? confirmation.trim() : '(nem jelent meg, de AJAX beküldés megtörtént)'}`);
    console.log(`PASS: smoke kontakt teszt rendben. Screenshot: ${SCREENSHOT}`);
  } finally {
    await browser.close();
  }
}

run().catch((err) => {
  console.error(err);
  process.exit(1);
});
