# 6-3 Feladatpontok

## Fazis 1 – Kattinthato mock (statikus HTML prototipus)

- [x] **1.1** `6-3/` konyvtar letrehozasa a `6-2/index.html` masolatabol
- [x] **1.2** Fooldal mock – Hero szekcion masodlagos CTA gomb hozzaadasa ("Katalogus" → `termekek.html`)
- [x] **1.3** Fooldal mock – Kategoriak szekcion: minden kartya aljara "Nezd meg a termekeket →" link (`termekek.html?kategoria={cimke}`)
- [x] **1.4** Fooldal mock – Munkaink szekcion: galeria aljara "Osszes termek megtekintese →" gomb (`termekek.html`)
- [x] **1.5** Uj oldal mock – `6-3/termekek.html` letrehozasa:
  - Navbar (azonos a fooldalival, "Fabrika Ajandek" logo linkje a fooldara mutat)
  - Szuro sav kategoriak/tagek alapjan (kattinthato badge-ek/gombok)
  - Termek grid (3-4 oszlop desktop, 2 tablet, 1 mobil)
  - Minta kartyak (12 db): kep, nev, ar ("3 500 Ft"), kategoria badge-ek, "Erdekel" gomb, FA-XXX azonosito badge
  - Footer (azonos a fooldalival)
- [x] **1.6** Termek kartya "Erdekel" gomb: link vissza a fooldara `index.html?termek=FA-XXX&nev={nev}#kapcsolat`
- [x] **1.7** Fooldal JS: URL query param (`?termek=FA-XXX&nev=...`) kiolvasasa es a kapcsolat urlap termek mezo automatikus kitoltese ("FA-007 – Fa viragtarto terrarium" formatum)
- [x] **1.8** Szuro mukodes: query param (`?kategoria=...`) kiolvasasa es a megfelelo kartyak szurese JS-bol
- [ ] **1.9** Vizualis ellenorzes: a fooldal animacioi, Hero, navbar, FAQ, anchorok, effektek valtozatlanul mukodnek
- [ ] **1.10** Mobil responsive ellenorzes: termek grid, szuro sav, kartyak 375px-tol
- [ ] **1.11** Mock terv elfogadtatasa (review)

## Fazis 2 – WordPress integralas

### Adatmodell

- [ ] **2.1** Custom Post Type regisztracio: `fabrika_termek` (`functions.php`)
  - Cim, editor, featured image tamogatas
  - Public, has_archive = true, rewrite slug = `termekek`
- [ ] **2.2** Custom taxonomy: `fabrika_kategoria` (hierarchikus, mint WP kategoriak)
- [ ] **2.3** Custom taxonomy: `fabrika_tag` (nem hierarchikus, mint WP cimkek)
- [ ] **2.4** Admin meta box: "Ar (Ft)" mezo a termek szerkeszto oldalon (`add_meta_box`)
- [ ] **2.5** Ar mezo mentese es sanitizalasa (`save_post` hook)
- [ ] **2.5a** (Opcionalis, de ajanlott) Termek azonosito mezo: "FA-XXX" (meta) + megjelenites a katalogus kartyakon
- [ ] **2.5b** (Ajanlott) Rewrite rules frissites theme aktivlaskor (`after_switch_theme` + `flush_rewrite_rules`)

### Fooldal modositasok (front-page.php)

- [ ] **2.6** Kategoriak repeater bovitese: uj `cimke` (slug) mezo hozzaadasa az admin oldalon
- [ ] **2.7** Kategoriak szekcion: link generalas a kartya aljara (`/termekek?kategoria={cimke}`)
- [ ] **2.8** Munkaink szekcion atalakitasa: `WP_Query` a legutolso 10 `fabrika_termek` posztbol (featured image kiolvasas)
- [ ] **2.9** Munkaink szekcion aljara "Osszes termek megtekintese" gomb (`/termekek`)
- [ ] **2.10** Hero szekcio: masodlagos CTA gomb ("Katalogus" → `/termekek`)

### Termekoldal template

- [ ] **2.11** `archive-fabrika_termek.php` template letrehozasa a mock alap alapjan
- [ ] **2.12** Szuro sav megvalositasa: `fabrika_kategoria` es `fabrika_tag` taxonomy-k listazasa gombkent
- [ ] **2.13** Szures megvalositasa: `tax_query` a `WP_Query`-ben, query param (`?kategoria=slug` es/vagy `?tag=slug`) feldolgozas
- [ ] **2.14** Termek kartyak renderelese: kep, nev, ar (meta), kategoria/tag badge-ek, CTA gomb
- [ ] **2.15** "Megrendelem / Erdekel" gomb: link a fooldal `/#kapcsolat?termek={post_slug}` URL-re
- [ ] **2.16** Lapozas (pagination) ha tobb mint X termek van
- [ ] **2.16a** (Ajanlott) Ures szuro allapot: ha nincs talalat, legyen "Szuro torlese" CTA + vissza az osszeshez

### Kapcsolat urlap integralas

- [ ] **2.17** CF7 urlap bovitese: "Melyik termek erdekli?" mezo (text input, lathhato)
- [ ] **2.18** JS: `?termek=` query param kiolvasasa es a CF7 mezo automatikus kitoltese
- [ ] **2.19** Teszteles: termek oldalrol erkezo link → urlap kitoltve a termek nevevel
- [ ] **2.19a** (Ajanlott) Param standard: egyeztetni, hogy a link `?termek=` erteke **slug / ID / FA-kod** legyen-e, es ehhez igazitsuk a kitoltest (mock + WP ugyanugy)

### Mobil feltoltes teszteles

- [ ] **2.20** Mobil bongeszbol kep feltoltes teszteles (WP media uploader)
- [ ] **2.21** WP admin mobil nézet teszteles: termek hozzaadasa telefonrol (cim, kep, ar, kategoriak)

### Minosegbiztositas

- [ ] **2.22** Fooldal animaciok regresszios ellenorzese (Hero, parallax, gear, scroll reveal, FAQ)
- [ ] **2.23** DOM contract ellenorzes: id-k, classok, data-* attributumok valtozatlanok
- [ ] **2.24** Termek oldal responsive teszt: 375px, 768px, 1440px
- [ ] **2.25** 0 termek allapot: ures katalogus oldal uzenettel ("Meg nincsenek termekek")
- [ ] **2.26** Szuro mukodes ellenorzes: kategoria linkek a fooldalrol, szuro gombok a termek oldalon
- [ ] **2.27** CTA-lanc teljes teszteles: Hero → Katalogus → Termek → Megrendelem → Kapcsolat urlap (kitoltve)
- [ ] **2.28** Admin hasznalati leiras frissitese: termek feltoltes, kategoria kezeles, ar megadas
- [ ] **2.29** (Ajanlott) SEO alapok: /termekek oldal title/description, OG kep (ha nincs SEO plugin), canonical
- [ ] **2.30** (Ajanlott) A11y: szuro gombok `aria-pressed`, fokusz allapotok, keyboard navigacio minimalisan

## Fazis 3 – Automata tesztek es CI (ajanlott)

- [ ] **3.1** Playwright e2e tesztek a `6-3/` mockhoz (szuro, CTA-lanc, urlap termek mez kitoltese)
- [ ] **3.2** CI workflow (pl. GitHub Actions): `npm ci` + `npm run build` + `npm run test:e2e`
- [ ] **3.3** (Opcionalis) Vizualis regresszio CI-ban: Playwright screenshot snapshotok (stabil beallitasokkal: `parallax=0`, animaciok tiltasa)
- [ ] **3.4** (Kesobb) WP E2E tesztek: Docker compose + WP bootstrap (wp-cli) + `/termekek` szures + CTA-lanc smoke

## Dontesi pontok (implementacio elott)

1. Termek reszletes oldal (`single-fabrika_termek.php`) kell-e, vagy elég a katalogus grid + CTA?
2. Ar mezo formatum: egyszeru szam (Ft) vagy lehetoseg "Artol" / "X Ft-tol" tipusu megjelenitesre?
3. Galeria szekcio: a termek kep kattintasra lightbox vagy link a termek oldalra (ha lesz single template)?
4. Szuro: JS-alapu kliens oldali szures (gyorsabb, de max ~100 termek) vagy szerver oldali (WP query, lapozassal)?
5. `?termek=` parameter: slug / ID / FA-kod? (es kell-e mellette `?nev=` a displayhez)
