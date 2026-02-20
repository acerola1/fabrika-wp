import { expect, test } from '@playwright/test';

function categoriesContainToken(categories: string, token: string): boolean {
  const parts = categories
    .split(/\s+/)
    .map((p) => p.trim())
    .filter(Boolean);
  return parts.includes(token);
}

test('6-3: query param filter shows only matching cards', async ({ page }) => {
  await page.goto('/6-3/termekek.html?kategoria=fa-tablak');

  const activeBtn = page.locator('.filter-btn[data-filter="fa-tablak"]');
  await expect(activeBtn).toHaveClass(/active/);

  const cards = page.locator('.product-card');
  const count = await cards.count();
  expect(count).toBeGreaterThan(0);

  let visible = 0;
  for (let i = 0; i < count; i++) {
    const card = cards.nth(i);
    const display = await card.evaluate((el) => getComputedStyle(el).display);
    if (display === 'none') continue;
    visible++;
    const categories = (await card.getAttribute('data-categories')) ?? '';
    expect(categoriesContainToken(categories, 'fa-tablak')).toBe(true);
  }
  expect(visible).toBeGreaterThan(0);

  await expect(page.locator('#no-results')).toHaveClass(/hidden/);
});

test('6-3: clicking a filter updates URL and results', async ({ page }) => {
  await page.goto('/6-3/termekek.html');

  const btn = page.locator('.filter-btn[data-filter="testreszabhato"]');
  await btn.click();
  await expect(btn).toHaveClass(/active/);
  await expect(page).toHaveURL(/kategoria=testreszabhato/);

  const cards = page.locator('.product-card');
  const count = await cards.count();
  expect(count).toBeGreaterThan(0);

  let visible = 0;
  for (let i = 0; i < count; i++) {
    const card = cards.nth(i);
    const display = await card.evaluate((el) => getComputedStyle(el).display);
    if (display === 'none') continue;
    visible++;
    const categories = (await card.getAttribute('data-categories')) ?? '';
    expect(categoriesContainToken(categories, 'testreszabhato')).toBe(true);
  }
  expect(visible).toBeGreaterThan(0);
});

test('6-3: product CTA navigates back and pre-fills contact field', async ({ page }) => {
  await page.goto('/6-3/termekek.html');

  const firstCard = page.locator('.product-card').first();
  const id = (await firstCard.getAttribute('data-id')) ?? '';
  expect(id).toMatch(/^FA-\d{3}$/);

  const name = (await firstCard.locator('h3').textContent())?.trim() ?? '';
  expect(name.length).toBeGreaterThan(0);

  const cta = firstCard.locator('a[href*="#kapcsolat"]');
  await expect(cta).toHaveAttribute('href', /termek=FA-\d{3}.*nev=/);
  await cta.click();

  await expect(page).toHaveURL(/\/6-3\/index\.html\?termek=FA-\d{3}&nev=.*#kapcsolat/);

  const termekInput = page.locator('#kapcsolat input#termek');
  await expect(termekInput).toHaveValue(new RegExp(`${id}.*${name.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')}`));
});

test('6-3: category link from homepage lands on filtered catalog', async ({ page }) => {
  await page.goto('/6-3/index.html');

  const catLink = page.locator('a[href^="termekek.html?kategoria="]').first();
  const href = (await catLink.getAttribute('href')) ?? '';
  expect(href).toMatch(/^termekek\.html\?kategoria=[a-z0-9-]+$/);
  const token = href.split('kategoria=')[1] ?? '';
  expect(token.length).toBeGreaterThan(0);

  await catLink.click();
  await expect(page).toHaveURL(/\/6-3\/termekek\.html\?kategoria=/);

  const activeBtn = page.locator(`.filter-btn[data-filter="${token}"]`);
  await expect(activeBtn).toHaveClass(/active/);
});
