# 6-3 Termekoldal bovites – Feladatleiras es terv

## Attekintes

A meglevo one-page Fabrika oldalt (6-2) bovitjuk egy kulon `/termekek` (katalogus) oldallal. A fooldal storytellingje, animacioi es DOM szerkezete valtozatlan marad. A katalogus nem webshop – nincs kosar/checkout –, hanem egy arak + termeklista + kapcsolatfelvetel CTA lancot valosit meg.

## Jelenlegi allapot (6-2)

- One-page landing: Hero → Kategoriak → Galeria (Munkaink) → Rendeles lepesei → Ajandekotletek → Piaci megjelenes → Kapcsolat → GYIK → Footer
- WP theme: `fabrika-62`, egyedi admin oldal (`Fabrika Kezdolap`), repeaterek, CF7 urlap
- Animaciok: Hero interaction, gear spin, parallax, scroll reveal, navbar scroll, back-to-top, smooth scroll
- Docker dev kornyezet: WP + MariaDB + MailHog

## Uj funkciok osszefoglalasa

### 1. Fooldal modositasok (minimalis)

**Kategoriak szekcion:**
- A jelenlegi admin repeater (cim + leiras + kep) kiegeszul egy **cimke** (slug/tag) mezoval.
- Minden kategoria kartya aljara kerul egy link: "Nezd meg a termekeket →", ami a `/termekek?kategoria={cimke}` URL-re mutat.
- A kategoria kartya vizualisan valtozatlan marad (kep + cim + leiras); a link egy uj elem a kartya aljan.

**Munkaink (Galeria) szekcion:**
- A jelenlegi statikus 10 kepes galeria **dinamikussa** valik: a legutoljara feltoltott 10 termek kepei jelennek meg automatikusan.
- Minden kep kattinthato, a termek reszleteit megjelenithetjuk (opcionalis: lightbox vagy link a termek oldalra).
- A szekcion aljan egy gomb: **"Osszes termek megtekintese →"** → `/termekek`.

**Hero szekcion:**
- Az elsodleges CTA marad: `#kapcsolat` ("Egyedi rendeles").
- Uj masodlagos CTA gomb a Hero-ban: **"Katalogus / Osszes termek"** → `/termekek`.

### 2. Uj oldal: `/termekek` (Katalogus)

**Megjelenes:**
- Racsos (grid) nezet, responsive: 1 oszlop mobil, 2 tablet, 3-4 desktop.
- Minden termek kartya tartalmazza:
  - Kep (thumbnail)
  - Termek nev
  - Fix ar megjelenitest (pl. "3 500 Ft")
  - Kategoriak/tagek badge-kent
  - **"Megrendelem / Erdekel"** gomb

**Szures:**
- Kategoriak/tagek alapjan szurheto (pl. "lezer gravir", "testreszabhato", "kerti").
- A szuro URL-ben query paramben tarolodik (`?kategoria=lezer-gravir`), igy a fooldal kategoria linkjei is mukodnek.

**CTA gomb logika:**
- A "Megrendelem / Erdekel" gomb a fooldal `#kapcsolat` szekciojara (vagy kulon kapcsolat oldalra) navigal.
- Query paramban atadja a termek nevet/azonositojat: `/?termek=lezer-portre-30x40#kapcsolat` vagy `/kapcsolat?termek=lezer-portre-30x40`.
- Az urlap JS-bol automatikusan kitolti a termek nevet egy rejtett vagy lathato mezobe.

### 3. WP adatmodell (Termekek)

**Custom Post Type: `fabrika_termek`**
- Mezok:
  - Cim (beepitett WP title)
  - Kep (beepitett WP featured image – mobilrol is feltoltheto)
  - Leiras (beepitett WP editor vagy egyedi mezo)
  - **Ar** (egyedi meta mezo, szam, Ft)
  - **Kategoriak** (egyedi taxonomy: `fabrika_kategoria`)
  - **Tagek** (egyedi taxonomy: `fabrika_tag`)
- A WP admin feluleten a termek hozzaadasa a szokasos "Uj hozzaadasa" feluleten tortenik (mint egy bejegyzes).
- A WP media feltoltes alapbol tamogatja a mobil fenykepezo/fajlfeltoltest.

### 4. CTA-lanc osszefoglalo

```
Hero
  ├── Elsodleges CTA: "Egyedi rendeles" → #kapcsolat
  └── Masodlagos CTA: "Katalogus" → /termekek

Kategoriak szekcion
  └── Kategoria kartya → "Nezd meg →" → /termekek?kategoria={slug}

Munkaink szekcion
  └── "Osszes termek megtekintese" → /termekek

/termekek oldal
  └── Termek kartya → "Megrendelem / Erdekel" → /#kapcsolat?termek={slug}

Kapcsolat urlap
  └── Termek mezo automatikusan kitoltve (ha query param erkezett)
```

## Megvalositasi fazisok

### Fazis 1: Kattinthato mock terv (6-3 statikus HTML)

A `6-3/` konyvtarba keszul egy kattinthato statikus prototipus, ami a jelenlegi `6-2/index.html`-re epul:
- Fooldal mock: modositott kategoriak (linkekkel), modositott galeria (gombbal), Hero masodlagos CTA-val.
- `/termekek` mock oldal: racsos grid, szuro savval, minta termekekkel, CTA gombokkal.
- Kattinthato linkek osszekotik a ket oldalt.
- Nincs backend logika, csak HTML/CSS/JS.

### Fazis 2: WP integralas

- Custom Post Type regisztralas (`fabrika_termek`).
- Custom taxonomy-k: `fabrika_kategoria`, `fabrika_tag`.
- Admin meta box: ar mezo.
- WP template: `archive-fabrika_termek.php` (katalogus oldal).
- Fooldali template modositasok (`front-page.php`):
  - Kategoriak repeater bovitese cimke mezoval + link generalas.
  - Galeria szekcion: `WP_Query` a legutolso 10 termekre.
- Kapcsolat urlap: query param feldolgozas JS-bol, mezo kitoltes.
- Mobil kep feltoltes teszteles.

## Kiegeszito szempontok (ajanlott)

- **Query param standard:** eldonteni, hogy `?termek=` slug / ID / "FA-XXX" kod legyen-e (es kell-e `?nev=` a displayhez).
- **Termek azonosito (FA-XXX):** ha marad a mockban, akkor WP-ben is erdemes egy opcionális meta mezokent kezelni.
- **SEO minimum:** `/termekek` title/description + canonical (ha nincs SEO plugin).
- **Minoseg kapu:** legalabb 1-2 automata e2e teszt (Playwright) a CTA-lancra, hogy refaktoroknel se torjon.
