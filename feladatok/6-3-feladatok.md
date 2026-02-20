# 6-3 Feladatpontok

## Fázis 1 – Kattintható mock (statikus HTML prototípus)

- [x] **1.1** `6-3/` könyvtár létrehozása a `6-2/index.html` másolatából
- [x] **1.2** Főoldal mock – Hero szekción másodlagos CTA gomb hozzáadása ("Katalógus" → `termekek.html`)
- [x] **1.3** Főoldal mock – Kategóriák szekción: minden kártya aljára "Nézd meg a termékeket →" link (`termekek.html?kategoria={cimke}`)
- [x] **1.4** Főoldal mock – Munkáink szekción: galéria aljára "Összes termék megtekintése →" gomb (`termekek.html`)
- [x] **1.5** Új oldal mock – `6-3/termekek.html` létrehozása:
  - Navbar (azonos a főoldalival, "Fabrika Ajándék" logó linkje a főoldalra mutat)
  - Szűrő sáv kategóriák/tagek alapján (kattintható badge-ek/gombok)
  - Termék grid (3-4 oszlop desktop, 2 tablet, 1 mobil)
  - Minta kártyák (12 db): kép, név, ár ("3 500 Ft"), kategória badge-ek, "Érdekel" gomb, FA-XXX azonosító badge
  - Footer (azonos a főoldalival)
- [x] **1.6** Termék kártya "Érdekel" gomb: link vissza a főoldalra `index.html?termek=FA-XXX&nev={nev}#kapcsolat`
- [x] **1.7** Főoldal JS: URL query param (`?termek=FA-XXX&nev=...`) kiolvasása és a kapcsolat űrlap termék mező automatikus kitöltése ("FA-007 – Fa virágtartó terrárium" formátum)
- [x] **1.8** Szűrő működés: query param (`?kategoria=...`) kiolvasása és a megfelelő kártyák szűrése JS-ből
- [x] **1.9** Vizuális ellenőrzés: a főoldal animációi, Hero, navbar, FAQ, anchorok, effektek változatlanul működnek
- [x] **1.10** Mobil responsive ellenőrzés: termék grid, szűrő sáv, kártyák 375px-től
- [x] **1.11** Mock terv elfogadtatása (review)
- [x] **1.12** Mock – Termék kártyára kattintva termék modal/carousel megjelenítése (nagy kép + infók + CTA)
  - Bezárás: X, overlay click, ESC
- [x] **1.13** Mock – Carousel navigáció: előző/következő + mobil swipe + billentyűzet nyilak
  - Sorrend: ugyanaz, mint a gridben (szűrő után csak a látható termékek között lép)
- [x] **1.14** Mock – Termék leírás megjelenítése hoverre (áttetsző panel, animáció)
  - Mobilon: fallback (mindig látszik, 2 sorra csonkítva – `@media (hover: none)`)

## Fázis 2 – WordPress integrálás

### Adatmodell

- [x] **2.1** Custom Post Type regisztráció: `fabrika_termek` (`functions.php`)
  - Cím, editor, featured image támogatás
  - Public, has_archive = true, rewrite slug = `termekek`
- [x] **2.2** Custom taxonomy: `fabrika_tag` (címkék) – **egyetlen** szűrő dimenzió
- [x] **2.3** (N/A) Külön `fabrika_kategoria` taxonomy nem kell
- [x] **2.4** Admin meta box: "Ár (Ft)" mező a termék szerkesztő oldalon (`add_meta_box`)
- [x] **2.5** Ár mező mentése és szanitizálása (`save_post` hook)
- [x] **2.5a** (Opcionális, de ajánlott) Termék azonosító mező: "FA-XXX" (meta) + megjelenítés a katalógus kártyákon
- [x] **2.5b** (Ajánlott) Rewrite rules frissítés theme aktiváláskor (`after_switch_theme` + `flush_rewrite_rules`)
- [x] **2.5c** Képek átméretezése (ingyen, WP core): új képméretek regisztrálása + használat
  - `square_600` (600×600, hard crop) – katalógus kártya + főoldal galéria
  - `product_large` (1200×1200, **no crop**, eredeti aspect ratio) – modal / carousel
    - A modalban `object-fit: contain` mutatja az eredeti arányban a képet
    - Régi `square_1200` slug törölve (hard crop volt, nem megfelelő)
    - Meglévő képeket újra kell generálni: `wp media regenerate --yes`
  - Ellenőrzés: feltöltés után legenerálja-e a WP a méreteket (Docker + később éles)
- [ ] **2.5d** (Fallback, ha szerveroldali méretgenerálás nem megy) JS-es feltöltés/átméretezés
  - Custom "Termék kép" mező a termék szerkesztőben (media picker), **feltöltés előtt** canvas/WebP vagy JPEG 1200×1200 export
  - Megjegyzés: csak ingyenes, saját theme kód (nincs fizetős plugin)

### Admin UX (termék feltöltés egyszerűsítése)

- [x] **2.5e** Egylépéses termék feltöltés admin oldalon (név, leírás, ár, 1 kép, címkék) – azonnal publikus
- [x] **2.5f** Termék kód automatikus (egyedi, egyszerű): post ID (szám), és ne legyen kézzel szerkeszthető
- [ ] **2.5g** (Ajánlott) Playwright smoke teszt: "Új termék" oldalról termék létrehozás + kép feltöltés (lokál)

### Főoldal módosítások (front-page.php)

- [x] **2.6** Kategóriák repeater bővítése: új `cimke` (slug) mező hozzáadása az admin oldalon
- [x] **2.7** Kategóriák szekción: link generálás a kártya aljára (`/termekek?kategoria={cimke}`)
- [x] **2.8** Munkáink szekción átalakítása: `WP_Query` a legutolsó 10 `fabrika_termek` posztból (featured image kiolvasás)
- [x] **2.9** Munkáink szekción aljára "Összes termék megtekintése" gomb (`/termekek`)
- [x] **2.10** Hero szekció: másodlagos CTA gomb ("Katalógus" → `/termekek`)

### Termékoldal template

- [x] **2.11** `archive-fabrika_termek.php` template létrehozása a mock alap alapján
- [x] **2.12** Szűrő sáv megvalósítása: `fabrika_tag` (címkék) listázása gombként
- [x] **2.13** Szűrés megvalósítása: mock-paritás kliens oldali filter (`?kategoria=slug`), alias: `?cimke=`, `?tag=`
- [x] **2.14** Termék kártyák renderelése: kép, név, ár (meta), kategória/tag badge-ek, CTA gomb
- [x] **2.14a** Termék kép render: `wp_get_attachment_image()` + `square_600` (srcset, lazy-load, alt)
- [x] **2.15** "Megrendelem / Érdekel" gomb: link a főoldal `/?termek={kod}&nev={cim}#kapcsolat` URL-re
- [x] **2.16** Képek betöltése lazy loadinggal (katalógus + galéria): `loading="lazy"` (+ ahol lehet `decoding="async"`)
- [x] **2.16a** (Ajánlott) Üres szűrő állapot: ha nincs találat, legyen "Szűrő törlése" CTA + vissza az összeshez
- [x] **2.16b** WP – Termék kártyára kattintva ugyanaz a modal/carousel, mint a mockban (nagy kép + infók + CTA)
- [x] **2.16c** WP – Modal/carousel A11y: fókusz kezelés (open/close), `aria-*`, keyboard (ESC/nyilak)
- [x] **2.16d** WP – Termék leírás megjelenítése a kártyán (static szöveg a kártya bodyban, 2 sorra csonkítva)

### Kapcsolat űrlap integrálás

- [x] **2.17** CF7 űrlap bővítése: "Melyik termék érdekli?" mező (text input, látható)
- [x] **2.18** JS: `?termek=` query param kiolvasása és a CF7 mező automatikus kitöltése
- [x] **2.19** Tesztelés: termék oldalról érkező link → űrlap kitöltve a termék nevével
- [x] **2.19a** (Ajánlott) Param standard: egyeztetni, hogy a link `?termek=` értéke **slug / ID / FA-kód** legyen-e, és ehhez igazítsuk a kitöltést (mock + WP ugyanúgy) — **Döntés:** `?termek=` = termék kód (WP-ben jelenleg post ID), `?nev=` = termék neve; az űrlap mező kitöltése: `{termek} – {nev}`.

### Mobil feltöltés tesztelés

- [ ] **2.20** Mobil böngészőből kép feltöltés tesztelés (WP media uploader)
- [ ] **2.21** WP admin mobil nézet tesztelés: termék hozzáadása telefonról (cím, kép, ár, kategóriák)
- [ ] **2.21a** Képek QA: 1200×1200 feltöltés, fájlméret (cél: ~200–600 KB), vágás/középre komponálás ellenőrzése

### Minőségbiztosítás

- [x] **2.22** Főoldal animációk regressziós ellenőrzése (Hero, parallax, gear, scroll reveal, FAQ)
- [x] **2.23** DOM contract ellenőrzés: id-k, classok, data-* attribútumok változatlanok
- [x] **2.24** Termék oldal responsive teszt: 375px, 768px, 1440px
- [x] **2.25** 0 termék állapot: üres katalógus oldal üzenettel ("Még nincsenek termékek")
- [x] **2.26** Szűrő működés ellenőrzés: kategória linkek a főoldalról, szűrő gombok a termék oldalon
- [x] **2.27** CTA-lánc teljes tesztelés: Hero → Katalógus → Termék → Megrendelem → Kapcsolat űrlap (kitöltve)
- [ ] **2.28** Admin használati leírás frissítése: termék feltöltés, kategória kezelés, ár megadás
- [ ] **2.29** (Ajánlott) SEO alapok: /termekek oldal title/description, OG kép (ha nincs SEO plugin), canonical
- [ ] **2.30** (Ajánlott) A11y: szűrő gombok `aria-pressed`, fókusz állapotok, keyboard navigáció minimálisan

## Fázis 3 – Automata tesztek és CI (ajánlott)

- [x] **3.1** Playwright e2e tesztek a `6-3/` mockhoz (szűrő, CTA-lánc, űrlap termék mező kitöltése)
- [ ] **3.2** WP bootstrap CI-hoz (üres indulás): Docker compose + üres DB + alap theme/plugin/options seed script
  - Theme aktiválás, permalink flush, `fabrika62_options` alapértékek, alap címkék, CF7 alap űrlap
- [ ] **3.3** WP E2E – admin termék feltöltés (kritikus): belépés → egylépéses feltöltés → mentés → publikus megjelenés
- [ ] **3.4** WP E2E – admin termék szerkesztés (kritikus): listából szerkesztés → ugyanazon űrlap → mentés → frontend ellenőrzés
- [ ] **3.5** WP smoke – `/termekek` megjelenés: kártyák (kép, cím, ár/ár-egyeztetéssel), szűrő sáv, CTA gombok
- [ ] **3.6** WP E2E – CTA lánc: Hero → Katalógus → Érdekel → Kapcsolat (`?termek=` + `?nev=`) mező előtöltés
- [ ] **3.7** WP smoke – üres állapot: seed nélküli futásban nincs termék, üres üzenet helyes
- [ ] **3.8** CI workflow frissítés: külön `empty-state` és `seeded-state` job, stabil smoke/E2E futtatás

## Döntési pontok (implementáció előtt)

1. Termék részletes oldal (`single-fabrika_termek.php`) kell-e, vagy elég a katalógus grid + CTA?
2. Ár mező formátum: egyszerű szám (Ft) vagy lehetőség "Ártól" / "X Ft-tól" típusú megjelenítésre?
3. Galéria szekció: a termék kép kattintásra lightbox vagy link a termék oldalra (ha lesz single template)?
4. Szűrő: JS-alapú kliens oldali szűrés (gyorsabb, de max ~100 termék) vagy szerver oldali (WP query, lapozással)?
5. `?termek=` paraméter: **lezárva** → termék kód (WP-ben jelenleg post ID), mellette `?nev=` a megjelenített kitöltési értékhez.
6. Képek: elég a WP featured image + WP core méretgenerálás, vagy kell custom JS-es átméretezős feltöltés fallbackként?
