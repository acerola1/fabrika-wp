# Fabrika Ajándék

Kézműves fa ajándékokhoz készült landing page variációk és a kiválasztott dizájnból épített, telepíthető WordPress téma.

## Mi van ebben a repóban?

- **9 statikus landing oldal variáció** (`1/` - `9/`) - ugyanarra a márkára több vizuális irány (Tailwind + Vite).
- **`6-2/`** - a kiválasztott dizájn, egylapos felépítéssel (hero, termékkategóriák, galéria, rendelési lépések, ajándékötletek, piaci jelenlét, kapcsolat, GYIK).
- **`wp-content/themes/fabrika-62/`** - a WordPress téma, ami a `6-2` dizájnt valósítja meg adminból szerkeszthető tartalommal.
- **`tests/`** - Playwright vizuális regresszió + PHP repeater terheléses tesztek.
- **`feladatok/`** - projektleírások és roadmap.

## Statikus oldalak futtatása

```bash
npm install
npm run dev
```

Ezután a fő index: `http://localhost:5173/`, vagy közvetlenül bármelyik variáció:

- `http://localhost:5173/1/`
- `http://localhost:5173/2/`
- ...
- `http://localhost:5173/6-2/`

## WordPress futtatása lokálisan

Előfeltétel: Docker + Docker Compose.

```bash
docker compose up -d
```

| Szolgáltatás | URL |
|---|---|
| WordPress | http://localhost:8080 |
| WP Admin | http://localhost:8080/wp-admin/ |
| MailHog | http://localhost:8025 |

Első indításkor végig kell menni a WP telepítőn (`/wp-admin/install.php`), majd aktiválni a **fabrika-62** témát.

A téma mappa bind mounttal csatolva van, ezért a `wp-content/themes/fabrika-62/` alatti módosítások azonnal látszanak.

### Tartalom szerkesztése

A nyitóoldal tartalma egy helyen kezelhető:
**WP Admin -> Fabrika Kezdolap**.

Itt szerkeszthetők a szövegek, repeaterek (kategóriák/galéria/lépések/ajándékötletek/GYIK), valamint a kapcsolat blokk beállításai.
Részletes útmutató: [`feladatok/admin-hasznalati-leiras.md`](feladatok/admin-hasznalati-leiras.md)

### Email tesztelés

A kapcsolat űrlap (Contact Form 7) fejlesztői környezetben MailHogba küld:
`http://localhost:8025`

## Tesztek futtatása

**E2E (statikus mock `6-3/`)**:

```bash
npx playwright install chromium
npm test
```

**Vizuális regresszió** (`6-2` statikus vs WordPress kimenet):

```bash
npm run dev -- --port 3999 &   # statikus referencia
docker compose up -d            # WordPress
node tests/visual-regression.mjs
# Képek: tests/screenshots/
```

**Repeater stressz teszt** (0/1/2/3/5/10 elem + hosszú szöveg):

```bash
docker cp tests/stress-test-repeaters.php fabrika_wp_app:/tmp/
docker exec fabrika_wp_app php /tmp/stress-test-repeaters.php
```

## Éles telepítés (téma export és feltöltés)

### 1. ZIP előállítása

```bash
bash scripts/export-theme.sh
```

Ez alapból **lean** export:
- a mock galéria/kategória képek kimaradnak,
- kisebb lesz a ZIP (könnyebb feltöltés limitált tárhelyen).

Ha a teljes mock referencia készlet is kell:

```bash
bash scripts/export-theme.sh --full
```

A script a `dist/` mappába generál:

| Fájl | Tartalom |
|---|---|
| `dist/fabrika-62-theme.zip` | Telepíthető WordPress téma |
| `dist/fabrika62_options.json` | Aktuális admin beállítások (ha fut a Dockeres WP) |

Megjegyzés:
- A script automatikusan kihagyja a fejlesztői fájlokat (`test-results`, `ci/`, `inc/6-2-source.html`, `.DS_Store`, stb.).
- Csomagoláskor optimalizálja a referencia képeket.
- Alapértelmezésben lean módot használ.

### 2. Telepítés a céloldalon

**A. Téma feltöltése**

WP Admin -> Megjelenés -> Témák -> Témafeltöltés -> `dist/fabrika-62-theme.zip` -> Aktiválás

Alternatíva: FTP-vel másold a `fabrika-62/` mappát a szerver `wp-content/themes/` könyvtárába, majd aktiváld.

**B. Rewrite frissítése**

WP Admin -> Beállítások -> Közvetlen hivatkozások -> Mentés

Vagy WP-CLI:

```bash
wp rewrite flush
```

**C. Admin beállítások importálása (opcionális)**

A téma alapértékekkel is működik (`inc/helpers.php`, `fabrika62_default_options()`), import csak akkor kell, ha lokálban már testreszabtátok a tartalmakat.

```bash
# WP-CLI (ha van a szerveren)
wp option update fabrika62_options "$(cat dist/fabrika62_options.json)"
```

**D. Termékek és média**

A mock termékadatok nem kerülnek a ZIP-be (DB adat), ezeket az adminban kell létrehozni.

## Mi kerül a ZIP-be?

| Benne van | Nincs benne |
|---|---|
| PHP template fájlok | Mock/teszt termékek (DB) |
| CSS + JS assetek | `wp-content/uploads/` tartalma |
| Referencia képek (`assets/references/`) | Docker/dev konfiguráció |
| CPT + taxonómia logika | Lokális tesztfájlok |

## Technológia

- **Statikus oldalak:** Vite, Tailwind CSS 4
- **WordPress téma:** egyedi admin oldal repeaterekkel
- **Űrlap:** Contact Form 7
- **Lokális környezet:** Docker Compose (WordPress + MariaDB + MailHog)
- **Tesztek:** Playwright, egyedi PHP stressz teszt
