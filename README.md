# Fabrika Ajandek

Landing page design variations for a handmade wooden gift shop, plus a full WordPress theme built from the chosen design.

## What's in the repo

- **9 static landing page designs** (`1/` through `9/`) — different visual directions for the same brand, built with Tailwind CSS + Vite.
- **`6-2/`** — the selected design variant, a single-page site with sections: hero, product categories, gallery, ordering steps, gift ideas, market info, contact form, FAQ.
- **`wp-content/themes/fabrika-62/`** — a custom WordPress theme that reproduces the `6-2` design with fully editable content from the admin panel (no page builder, no ACF — just a custom admin page with repeaters).
- **`tests/`** — Playwright visual regression and PHP repeater stress tests.
- **`feladatok/`** — project documentation and roadmap (in Hungarian).

## Previewing the static designs

```bash
npm install
npm run dev
```

Then open `http://localhost:5173/` for the main index, or go directly to any variant:

- `http://localhost:5173/1/`
- `http://localhost:5173/2/`
- ...
- `http://localhost:5173/6-2/`

## Running the WordPress site

Requirements: Docker & Docker Compose.

```bash
docker compose up -d
```

| Service   | URL                          |
|-----------|------------------------------|
| WordPress | http://localhost:8080         |
| WP Admin  | http://localhost:8080/wp-admin/ |
| MailHog   | http://localhost:8025         |

On first run you'll go through the WordPress installer at `/wp-admin/install.php`, then activate the **fabrika-62** theme from Appearance → Themes.

The theme directory is bind-mounted, so edits to `wp-content/themes/fabrika-62/` are reflected live.

### Content editing

All homepage content is managed from a single admin page: **WP Admin → Fabrika Kezdolap**. This includes text fields, repeaters for categories/gallery/steps/gift ideas/FAQ, and contact form settings. See [`feladatok/admin-hasznalati-leiras.md`](feladatok/admin-hasznalati-leiras.md) for a detailed guide.

### Email testing

The contact form (Contact Form 7) sends emails to MailHog in the dev environment — no real emails are sent. Check http://localhost:8025 to see them.

## Running the tests

**Visual regression** (compares static `6-2` design vs WordPress output):

```bash
npm run dev -- --port 3999 &   # static reference
docker compose up -d            # WordPress
node tests/visual-regression.mjs
# Screenshots saved to tests/screenshots/
```

**Repeater stress test** (0/1/2/3/5/10 elements + long text):

```bash
docker cp tests/stress-test-repeaters.php fabrika_wp_app:/tmp/
docker exec fabrika_wp_app php /tmp/stress-test-repeaters.php
```

## Tech stack

- **Static sites:** Vite, Tailwind CSS 4
- **WordPress theme:** PHP 8.3, custom admin page with repeaters (no ACF/page builder)
- **Contact form:** Contact Form 7
- **Dev environment:** Docker Compose (WordPress + MariaDB + MailHog)
- **Testing:** Playwright, custom PHP stress test
