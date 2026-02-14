# 6-2 statikus terv -> WordPress template terv

## WordPress elerhetoseg (lokal)
- Frontend: http://localhost:8080
- Installer/Admin: http://localhost:8080/wp-admin/install.php
- Docker status: `docker compose ps`
- Stop: `docker compose down`

## Cel
- A `6-2/index.html` one-page oldal teljes atemelese egy custom WordPress theme-be.
- A tartalom ne kodban legyen, hanem adminbol szerkesztheto legyen.
- A teljes kezdooldal szoveges tartalma egy helyrol kezelheto legyen.
- A galeria es kapcsolaturlap dinamikus legyen.

## Javasolt plugin stack
- `Advanced Custom Fields Pro` (ajanlott): mezok, repeater-ek, es egyetlen "Kezdolap beallitasok" options oldal.
- `Fluent Forms` (vagy Contact Form 7): kapcsolaturlap szerkesztes, bekuldesek, validacio.
- `Post SMTP`: megbizhato email kuldes kapcsolaturlaphoz.
- Galeria: ACF repeater + WP media (kulon galeria plugin nelkul).

## Admin "egy helyrol szerkesztes" koncepcio
- Egy egyedi admin menu: `Fabrika Kezdolap`.
- Ez egy ACF Options oldal, tabokra bontva:
  - Altalanos (meta title, meta description, footer szoveg, social linkek)
  - Hero
  - Termekkategoriak
  - Galeria
  - Rendeles lepesei
  - Ajandekotletek
  - Piaci megjelenes
  - Kapcsolat blokk
  - GYIK
- Igy minden szoveg egy admin feluleten szerkesztheto, nem kulon oldalakban.

## Layout es szerkeszthetoseg elvek (kiemelt)
- Minden olyan blokk, amiben tobb elem van (pl. kategoriak, galeria kepek, lepesek, GYIK), ACF repeater legyen.
- A repeaterek elemei szabadon hozzaadhatok/elvenhetok legyenek, es a frontend layout ezt torvenyszeruen kezelje:
  - Grid/flex auto-wrap, responsive breakpointok, eszeru gap-ek.
  - Nincs \"ures hely\" vagy eltoro magassag miatti szeteses; a kartyak/kepek rugalmasan igazodnak a darabszamhoz.
- A tartalommodell legyen logikus: ahol kell cim+leiras+kep, ott ezek egyetlen repeater sorban legyenek, ne kulon mezokben.

## Animaciok es effektek megorzese (kiemelt)
- A jelenlegi animaciok/effektek maradjanak meg, kulonosen a `Hero` szekcio:
  - Hero hatter interaction reveal
  - Gear spin / scroll state
  - Parallax engine
  - Scroll reveal, navbar scroll state, back-to-top, smooth scroll
- Implementacios szabaly: a JS csak ugy mozgathato/theme-be szervezheto, hogy az eredeti DOM struktura (id-k, classok, `data-*` attributumok) kompatibilis maradjon, kulonben az animaciok eltornek.

## Oldalszerkezet -> WP adatmodell
- Navbar:
  - Forras: WP Menu (`wp_nav_menu`) + ACF oldalcim/brand mezonev.
- Hero:
  - Forras: ACF fields (badge, fociim, alcim, CTA szoveg, CTA URL).
- Termek kategoriak (5 kartya):
  - Forras: ACF Repeater (cim, leiras, kep).
- Galeria racs (10 kep):
  - Forras: ACF Repeater (kep, alt szoveg opcionális).
- Hogyan rendelhetsz (3 lepes):
  - Forras: ACF Repeater (lepes cim + leiras).
- Ajandekotletek (5 blokk):
  - Forras: ACF Repeater (cim + leiras + ikon valaszto).
- Piaci megjelenes:
  - Forras: ACF fields (cim, leiras, masodlagos leiras).
- Kapcsolat szekcio:
  - Forras: Fluent Forms shortcode + ACF fields az oldalso kontakt infokhoz.
- GYIK:
  - Forras: ACF Repeater (kerdes, valasz).
- Footer:
  - Forras: ACF fields (nev, helyszin, copyright, social URL-ek).

## Theme implementacios terv
- `wp-content/themes/fabrika-62/` custom theme letrehozasa.
- Alap fajlok: `style.css`, `functions.php`, `front-page.php`, `header.php`, `footer.php`.
- Asset kezeles:
  - A jelenlegi design CSS/JS atemelese kulon `assets/css` es `assets/js` fajlokba.
  - `functions.php`-ban enqueue (`wp_enqueue_style`, `wp_enqueue_script`).
- Statikus HTML feldarabolasa:
  - Fejlec es footer resz kiszervezese.
  - Minden szekcio dinamikus adatokkal renderelve.
- URL-ek WP kompatibilisse tetele:
  - Kepa hivatkozasok `wp_get_attachment_image()` vagy attachment ID alapon.

## Minosegbiztositas (hogyan tartjuk magas szinten a minoseget)
- "Golden master" referencia: a statikus `6-2/index.html` (es/vagy `dist/6-2/index.html`) legyen a vizualis/viselkedesi etalon; minden szekciot ehhez hasonlitunk.
- Tartalom stresszteszt minden repeaterre: 0 / 1 / 2 / 3 / 5 / 10 elem; rovid/extra hosszu szoveg; kep nelkul; alt szoveg nelkul. A layoutnak toras nelkul kell alkalmazkodnia.
- Animacio "DOM contract": a Hero es a scroll/parallax JS mukodese fugg id-k/classok/`data-*` attributumok jelenletetol; ezekbol csinalunk egy rovid checklistet, es implementacio kozben nem torjuk meg.
- JS smoke-check (dev): ha valami hianyzik (pl. `#hero`, `#navbar`), ne torjon el az egesz script; inkabb `console.warn` es graceful fallback.
- Csokkentett mozgas ellenorzes: `prefers-reduced-motion` alatt ne legyen "betoressel" jaro elmenyromlas (szoveg/CTA legyen azonnal lathato).
- Vizualis regresszio (opcionális, de ajanlott): Playwright/Backstop-szeru screenshot osszehasonlitas desktop + mobil viewporttal legalabb a Hero + 2 tovabbi szekciora.
- Playwright "design parity" (opcionális, de ajanlott): ugyanazokat a screenshotokat elkeszitjuk a statikus referencia oldalon es a WP alatti oldalon, majd diffeljük.\n  - Stabilitas: `?parallax=0` parameter a mozgashoz, Playwright screenshot `animations: 'disabled'`, es `document.fonts.ready` megvarasa.\n  - Nezetek: legalabb 375x812 es 1440x900; allapotok: felso (hero), es egy lejjebb gorgetett pont.\n  - Cel: pixel-diff minimalizalasa; eltérések eseten konkretan megmondhato melyik szekcio csuszott el.
- Frontend minoseg kapuk: kepek `srcset`-tel (WP), lazy-load ahol lehet; nincs console error; nincs layout shift a fobb betoltes utan.
- Urlap/email QA: lokal fejlesztesnel ajanlott eloszor email-csapda (pl. MailHog) a biztonsagos teszteleshez, majd utana valos SMTP (Post SMTP) bekotese.

## Feladatok (roadmap)
- [ ] 1. ACF mezostruktura veglegesitese a 6-2 oldal minden szekciojahoz.
- [ ] 2. `Fabrika Kezdolap` options oldal letrehozasa (egyhelyes tartalomszerkesztes).
- [ ] 3. Custom theme skeleton letrehozasa (`fabrika-62`).
- [ ] 4. `front-page.php` elkeszitese a 6-2 szerkezet szerint.
- [ ] 5. Repeaterek bekotese ugy, hogy a layout darabszamfuggetlenul szepen torjon (kategoriak/galeria/lepesek/otletek/GYIK).
- [ ] 6. Animaciok/effektek atemelese es regresszioellenorzese (kulonosen Hero).
- [ ] 7. Kapcsolaturlap plugin telepitese es shortcode bekotese.
- [ ] 8. SMTP plugin beallitasa es urlap email kuldes teszt.
- [ ] 9. GYIK, footer, kapcsolati adatok teljes adminbekotese.
- [ ] 10. Mobil + desktop vizualis regresszio teszt a statikus 6-2-hoz kepest (layout + animaciok).
- [ ] 11. Atadas: rovid admin hasznalati leiras (ki mit hol szerkeszt).
- [ ] 12. Tartalom stresszteszt futtatasa a repeatereken (0/1/2/3/5/10 elem + hosszu szoveg).
- [ ] 13. (Opcionális) Playwright screenshot regresszio beallitasa (baseline: statikus `dist/6-2`, target: WP futtatas).
- [ ] 14. (Opcionális) Email-csapda (MailHog) felhuzasa lokalis urlap tesztekhez.

## Dontesi pontok (implementacio elott)
- ACF Pro rendelkezesre all-e, vagy kell free alternativa (pl. Pods + custom settings page).
- Kapcsolaturlap plugin: Fluent Forms vagy Contact Form 7.
- (Opcionális) Bevezetunk-e Playwright/Backstop screenshot regressziot.

## Elfogadasi kriteriumok
- A kezdooldal layoutja es viselkedese vizualisan megfelel a `6-2` tervnek.
- Minden jelenlegi statikus szoveg szerkesztheto a `Fabrika Kezdolap` admin oldalon.
- A tobbelemu blokkok (kategoriak/galeria/lepesek/otletek/GYIK) adminbol bovithetoek/csokkenthetoek, es a layout toras nelkul alkalmazkodik.
- A Hero es a tobbi animacio/effekt megmarad (parallax/reveal/navbar/back-to-top/smooth scroll).
- A repeaterek stressztesztjein (0/1/2/3/5/10 elem + hosszu szoveg) a layout es a JS nem torik.
- Kapcsolaturlap sikeresen kuld emailt.
