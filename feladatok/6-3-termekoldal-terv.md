# 6-3 Termékoldal bővítés – Feladatleírás és terv

## Áttekintés

A meglévő one-page Fabrika oldalt (6-2) bővítjük egy külön `/termekek` (katalógus) oldallal. A főoldal storytellingje, animációi és DOM szerkezete változatlan marad. A katalógus nem webshop, nincs kosár/checkout, hanem egy árak + terméklista + kapcsolatfelvétel CTA láncot valósít meg.

## Jelenlegi állapot (6-2)

- One-page landing: Hero → Kategóriák → Galéria (Munkáink) → Rendelés lépései → Ajándékötletek → Piaci megjelenés → Kapcsolat → GYIK → Footer
- WP theme: `fabrika-62`, egyedi admin oldal (`Fabrika Kezdőlap`), repeaterek, CF7 űrlap
- Animációk: Hero interaction, gear spin, parallax, scroll reveal, navbar scroll, back-to-top, smooth scroll
- Docker dev környezet: WP + MariaDB + MailHog

## Új funkciók összefoglalása

### 1. Főoldal módosítások (minimális)

**Kategóriák szekción:**
- A jelenlegi admin repeater (cím + leírás + kép) kiegészül egy **címke** (slug/tag) mezővel.
- Minden kategória kártya aljára kerül egy link: "Nézd meg a termékeket →", ami a `/termekek?kategoria={cimke}` URL-re mutat.
- A kategória kártya vizuálisan változatlan marad (kép + cím + leírás); a link egy új elem a kártya alján.

**Munkáink (Galéria) szekción:**
- A jelenlegi statikus 10 képes galéria **dinamikussá** válik: a legutoljára feltöltött 10 termék képei jelennek meg automatikusan.
- Minden kép kattintható, a termék részleteit megjeleníthetjük (opcionális: lightbox vagy link a termék oldalra).
- A szekción alján egy gomb: **"Összes termék megtekintése →"** → `/termekek`.

**Hero szekción:**
- Az elsődleges CTA marad: `#kapcsolat` ("Egyedi rendelés").
- Új másodlagos CTA gomb a Hero-ban: **"Katalógus / Összes termék"** → `/termekek`.

### 2. Új oldal: `/termekek` (Katalógus)

**Megjelenés:**
- Rácsos (grid) nézet, responsive: 1 oszlop mobil, 2 tablet, 3-4 desktop.
- Minden termék kártya tartalmazza:
  - Kép (thumbnail)
  - Termék név
  - Fix ár megjelenítést (pl. "3 500 Ft")
  - Címkék badge-ként
  - **"Megrendelem / Érdekel"** gomb

**Szűrés:**
- Címkék alapján szűrhető (pl. "lézer gravír", "testreszabható", "kerti").
- A szűrő URL-ben query paramben tárolódik (`?kategoria=lezer-gravir`), így a főoldal kategória linkjei is működnek (alias: `?cimke=`, `?tag=`).

**CTA gomb logika:**
- A "Megrendelem / Érdekel" gomb a főoldal `#kapcsolat` szekciójára (vagy külön kapcsolat oldalra) navigál.
- Query paramban átadja a termék nevét/azonosítóját: `/?termek=lezer-portre-30x40#kapcsolat` vagy `/kapcsolat?termek=lezer-portre-30x40`.
- Az űrlap JS-ből automatikusan kitölti a termék nevét egy rejtett vagy látható mezőbe.

**(Ajánlott UX) Termék modal/carousel:**
- Termék kártyára kattintva egy modal/lightbox jön fel nagyobb képpel, ugyanazzal az infóval és ugyanazzal a CTA gombbal.
- A modalban lehet előző/következő termékek között lépni (ugyanabban a sorrendben, mint a grid, és a szűrőt is követve).

**(Ajánlott UX) Leírás hoverre:**
- A termék képen/kártyán egy áttetsző, animált panel jelenik meg hoverre (mobilon fallbackkel), ami a rövid leírást mutatja.

### 3. WP adatmodell (Termékek)

**Custom Post Type: `fabrika_termek`**
- Mezők:
  - Cím (beépített WP title)
  - Kép (beépített WP featured image, mobilról is feltölthető)
  - Leírás (beépített WP editor vagy egyedi mező)
  - **Ár** (egyedi meta mező, szám, Ft)
  - **Címkék** (egyedi taxonomy: `fabrika_tag`)
- A WP admin felületen a termék hozzáadása **egylépéses** formon történik: **Termékek → Termék feltöltés** (név, leírás, ár, 1 kép, címkék).
  - A termék **azonnal publikus** mentéssel.
  - A termék kód automatikus: a termék ID (szám).
- A WP media feltöltés alapból támogatja a mobil fényképező/fájlfeltöltést.

### 4. CTA-lánc összefoglaló

```text
Hero
  ├── Elsődleges CTA: "Egyedi rendelés" → #kapcsolat
  └── Másodlagos CTA: "Katalógus" → /termekek

Kategóriák szekción
  └── Kategória kártya → "Nézd meg →" → /termekek?kategoria={slug}

Munkáink szekción
  └── "Összes termék megtekintése" → /termekek

/termekek oldal
  └── Termék kártya → "Megrendelem / Érdekel" → /#kapcsolat?termek={slug}

Kapcsolat űrlap
  └── Termék mező automatikusan kitöltve (ha query param érkezett)
```

## Megvalósítási fázisok

### Fázis 1: Kattintható mock terv (6-3 statikus HTML)

A `6-3/` könyvtárba készül egy kattintható statikus prototípus, ami a jelenlegi `6-2/index.html`-re épül:
- Főoldal mock: módosított kategóriák (linkekkel), módosított galéria (gombbal), Hero másodlagos CTA-val.
- `/termekek` mock oldal: rácsos grid, szűrő sávval, minta termékekkel, CTA gombokkal.
- Kattintható linkek összekötik a két oldalt.
- Nincs backend logika, csak HTML/CSS/JS.

### Fázis 2: WP integrálás

- Custom Post Type regisztrálás (`fabrika_termek`).
- Custom taxonomy: `fabrika_tag` (címkék).
- Admin meta box: ár mező.
- WP template: `archive-fabrika_termek.php` (katalógus oldal).
- Főoldali template módosítások (`front-page.php`):
  - Kategóriák repeater bővítése címke mezővel + link generálás.
  - Galéria szekción: `WP_Query` a legutolsó 10 termékre.
- Kapcsolat űrlap: query param feldolgozás JS-ből, mező kitöltés.
- Mobil kép feltöltés tesztelés.

## Kiegészítő szempontok (ajánlott)

- **Query param standard:** eldönteni, hogy `?termek=` slug / ID / "FA-XXX" kód legyen-e (és kell-e `?nev=` a displayhez).
- **Termék azonosító (FA-XXX):** ha marad a mockban, akkor WP-ben is érdemes egy opcionális meta mezőként kezelni.
- **SEO minimum:** `/termekek` title/description + canonical (ha nincs SEO plugin).
- **Minőség kapu:** legalább 1-2 automata e2e teszt (Playwright) a CTA-láncra, hogy refaktoroknál se törjön.

## Képek: feltöltés és átméretezés (ingyen)

### Cél
- A termék kártyákon és a galériában a képek gyorsak legyenek, és a layout stabil maradjon.
- Feltöltött "master" kép: **1200×1200** (1:1), normális fájlmérettel.

### Első választás (ajánlott): WP core szerveroldali méretgenerálás
- Theme-ben új képméretek: `square_600` és `square_1200` (hard crop, 1:1).
- Template-ben `wp_get_attachment_image()` használata, hogy legyen `srcset` / `sizes` / lazy-load.
- QA: ellenőrizni, hogy a szerver (Docker + később éles) tud-e thumbnails-t generálni.

### Fallback (ha a szerver nem generál méreteket): JS-es átméretezés feltöltés előtt
- Custom "Termék kép" mező a termék admin felületen (media picker).
- JS: kép választás után canvas/WebP vagy JPEG export **1200×1200**-re, majd feltöltés.
- Megkötés: továbbra is ingyen (saját theme kód), fizetős plugin nélkül.
