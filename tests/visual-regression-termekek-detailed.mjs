/**
 * Detailed element-level visual comparison: static mock vs WordPress /termekek/.
 *
 * Compares computed CSS properties of key elements on both pages and reports
 * differences. Also captures full-page screenshots for manual inspection.
 *
 * Usage:
 *   node tests/visual-regression-termekek-detailed.mjs
 *
 * Env:
 *   STATIC_URL  (default: http://localhost:5174/6-3/termekek.html)
 *   WP_URL      (default: http://localhost:8080/termekek/)
 */

import { chromium } from 'playwright';
import { existsSync, mkdirSync, writeFileSync } from 'fs';
import { resolve, join } from 'path';

const ROOT = resolve(import.meta.dirname, '..');
const OUT = join(ROOT, 'tests', 'screenshots');

const STATIC_URL = process.env.STATIC_URL || 'http://localhost:5174/6-3/termekek.html';
const WP_URL = process.env.WP_URL || 'http://localhost:8080/termekek/';

// CSS properties to compare on each element
const CSS_PROPS = [
  'font-size', 'font-weight', 'font-family', 'line-height', 'letter-spacing',
  'color', 'background-color',
  'padding-top', 'padding-right', 'padding-bottom', 'padding-left',
  'margin-top', 'margin-right', 'margin-bottom', 'margin-left',
  'border-top-width', 'border-right-width', 'border-bottom-width', 'border-left-width',
  'border-radius',
  'gap', 'row-gap', 'column-gap',
  'width', 'height',
  'display', 'position',
  'box-shadow',
];

// Elements to compare (CSS selector => human label)
const ELEMENTS = {
  // Page header
  'section:first-of-type': 'page-header-section',
  'section:first-of-type h1': 'page-header-h1',
  'section:first-of-type .copper-divider': 'page-header-divider',
  'section:first-of-type p': 'page-header-description',

  // Filter bar
  'section.sticky': 'filter-bar-section',
  '#filter-bar': 'filter-bar-container',
  '.filter-btn': 'filter-btn-first',

  // Product grid
  '#product-grid': 'product-grid',

  // First product card (if exists)
  '.product-card': 'product-card-first',
  '.product-card .aspect-square': 'card-image-container',
  '.product-card .product-id': 'card-product-id-badge',
  '.product-card .p-4': 'card-content-area',
  '.product-card .tag-badge': 'card-tag-badge-first',
  '.product-card .flex.flex-wrap.gap-1': 'card-tags-container',
  '.product-card h3': 'card-title',
  '.product-card p.text-lg, .product-card p.text-sm': 'card-price',
  '.product-card a[href*="kapcsolat"]': 'card-cta-button',

  // CTA Banner
  'section[style*="E8DCC8"]': 'cta-banner-section',
  'section[style*="E8DCC8"] h2': 'cta-banner-title',
  'section[style*="E8DCC8"] a': 'cta-banner-button',

  // Footer
  'footer': 'footer-section',
};

async function gotoStable(page, url) {
  await page.goto(url, { waitUntil: 'networkidle', timeout: 15000 });
  await page.evaluate(() => document.fonts && document.fonts.ready);
  await page.addStyleTag({
    content: '*, *::before, *::after { animation-duration: 0s !important; transition-duration: 0s !important; }',
  });
  await page.waitForTimeout(300);
}

async function getComputedStyles(page, selector, props) {
  return page.evaluate(({ selector, props }) => {
    const el = document.querySelector(selector);
    if (!el) return null;
    const cs = window.getComputedStyle(el);
    const result = {};
    for (const p of props) {
      result[p] = cs.getPropertyValue(p);
    }
    // Also get bounding box
    const rect = el.getBoundingClientRect();
    result['__bbox'] = {
      width: Math.round(rect.width * 100) / 100,
      height: Math.round(rect.height * 100) / 100,
    };
    return result;
  }, { selector, props });
}

function compareProp(prop, mockVal, wpVal) {
  if (mockVal === wpVal) return null;

  // Normalize font-family comparison (ignore minor quoting differences)
  if (prop === 'font-family') {
    const norm = s => s.replace(/['"]/g, '').replace(/\s+/g, ' ').trim().toLowerCase();
    if (norm(mockVal) === norm(wpVal)) return null;
  }

  // Normalize color comparison (both to lowercase)
  if (prop === 'color' || prop === 'background-color') {
    if (mockVal.toLowerCase() === wpVal.toLowerCase()) return null;
  }

  return { prop, mock: mockVal, wp: wpVal };
}

async function main() {
  if (!existsSync(OUT)) mkdirSync(OUT, { recursive: true });

  const browser = await chromium.launch();
  const viewport = { width: 1440, height: 900 };
  const differences = [];

  try {
    const ctx = await browser.newContext();
    const page = await ctx.newPage();
    await page.setViewportSize(viewport);

    // Capture mock
    console.log('Loading STATIC mock...');
    await gotoStable(page, STATIC_URL);
    await page.screenshot({ path: join(OUT, 'compare_static_full.png'), fullPage: true });

    const mockStyles = {};
    for (const [sel, label] of Object.entries(ELEMENTS)) {
      mockStyles[label] = await getComputedStyles(page, sel, CSS_PROPS);
    }

    // Capture WP
    console.log('Loading WORDPRESS...');
    await gotoStable(page, WP_URL);
    await page.screenshot({ path: join(OUT, 'compare_wp_full.png'), fullPage: true });

    const wpStyles = {};
    for (const [sel, label] of Object.entries(ELEMENTS)) {
      wpStyles[label] = await getComputedStyles(page, sel, CSS_PROPS);
    }

    // Compare
    console.log('\n========== ELEMENT COMPARISON ==========\n');

    for (const [sel, label] of Object.entries(ELEMENTS)) {
      const m = mockStyles[label];
      const w = wpStyles[label];

      if (!m && !w) {
        console.log(`[SKIP] ${label} — not found on either page`);
        continue;
      }
      if (!m) {
        console.log(`[MISSING MOCK] ${label} — only found on WP`);
        continue;
      }
      if (!w) {
        console.log(`[MISSING WP] ${label} — only found on mock`);
        continue;
      }

      const diffs = [];
      for (const prop of CSS_PROPS) {
        const diff = compareProp(prop, m[prop], w[prop]);
        if (diff) diffs.push(diff);
      }

      // Compare bounding box
      if (m.__bbox && w.__bbox) {
        if (Math.abs(m.__bbox.width - w.__bbox.width) > 1) {
          diffs.push({ prop: 'bbox-width', mock: m.__bbox.width + 'px', wp: w.__bbox.width + 'px' });
        }
        if (Math.abs(m.__bbox.height - w.__bbox.height) > 1) {
          diffs.push({ prop: 'bbox-height', mock: m.__bbox.height + 'px', wp: w.__bbox.height + 'px' });
        }
      }

      if (diffs.length === 0) {
        console.log(`[OK] ${label}`);
      } else {
        console.log(`[DIFF] ${label} (${sel}):`);
        for (const d of diffs) {
          console.log(`   ${d.prop}: mock="${d.mock}" vs wp="${d.wp}"`);
          differences.push({ element: label, selector: sel, ...d });
        }
      }
    }

    // Also do mobile comparison
    console.log('\n========== MOBILE (375px) COMPARISON ==========\n');
    await page.setViewportSize({ width: 375, height: 812 });

    await gotoStable(page, STATIC_URL);
    await page.screenshot({ path: join(OUT, 'compare_static_mobile.png'), fullPage: true });
    const mockMobileStyles = {};
    for (const [sel, label] of Object.entries(ELEMENTS)) {
      mockMobileStyles[label] = await getComputedStyles(page, sel, CSS_PROPS);
    }

    await gotoStable(page, WP_URL);
    await page.screenshot({ path: join(OUT, 'compare_wp_mobile.png'), fullPage: true });
    const wpMobileStyles = {};
    for (const [sel, label] of Object.entries(ELEMENTS)) {
      wpMobileStyles[label] = await getComputedStyles(page, sel, CSS_PROPS);
    }

    for (const [sel, label] of Object.entries(ELEMENTS)) {
      const m = mockMobileStyles[label];
      const w = wpMobileStyles[label];
      if (!m || !w) continue;

      const diffs = [];
      for (const prop of CSS_PROPS) {
        const diff = compareProp(prop, m[prop], w[prop]);
        if (diff) diffs.push(diff);
      }
      if (m.__bbox && w.__bbox) {
        if (Math.abs(m.__bbox.width - w.__bbox.width) > 1) {
          diffs.push({ prop: 'bbox-width', mock: m.__bbox.width + 'px', wp: w.__bbox.width + 'px' });
        }
        if (Math.abs(m.__bbox.height - w.__bbox.height) > 1) {
          diffs.push({ prop: 'bbox-height', mock: m.__bbox.height + 'px', wp: w.__bbox.height + 'px' });
        }
      }

      if (diffs.length === 0) {
        console.log(`[OK] ${label}`);
      } else {
        console.log(`[DIFF] ${label} (${sel}):`);
        for (const d of diffs) {
          console.log(`   ${d.prop}: mock="${d.mock}" vs wp="${d.wp}"`);
          differences.push({ element: label + ' (mobile)', selector: sel, ...d });
        }
      }
    }

    // Summary
    console.log('\n========== SUMMARY ==========');
    console.log(`Total differences found: ${differences.length}`);
    if (differences.length > 0) {
      writeFileSync(
        join(OUT, 'diff-report.json'),
        JSON.stringify(differences, null, 2),
      );
      console.log(`Full report: ${join(OUT, 'diff-report.json')}`);
    }

    await ctx.close();
  } finally {
    await browser.close();
  }
}

main().catch((err) => {
  console.error(err);
  process.exit(1);
});
