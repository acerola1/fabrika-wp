# 6-2 statikus terv -> WordPress template terv

## WordPress elérhetőség (lokál)
- Frontend: http://localhost:8080
- Installer/Admin: http://localhost:8080/wp-admin/install.php
- Docker status: `docker compose ps`
- Stop: `docker compose down`

## Cél
- A `6-2/index.html` one-page oldal teljes átemelése egy custom WordPress theme-be.
- A tartalom ne kódban legyen, hanem adminból szerkeszthető legyen.
- A teljes kezdőoldal szöveges tartalma egy helyről kezelhető legyen.
- A galéria és kapcsolatűrlap dinamikus legyen.

## Javasolt plugin stack
- Fizetős plugin nélkül: egyedi admin oldal (`Fabrika Kezdőlap`) saját mezőkkel és repeaterekkel.
- Opcionális: `Fluent Forms` (vagy Contact Form 7) kapcsolatűrlap szerkesztéshez.
- Opcionális: `Post SMTP` megbízható email küldéshez.
- Galéria: saját repeater + WP media URL-k (külön galéria plugin nélkül).

## Admin "egy helyről szerkesztés" koncepció
- Egy egyedi admin menü: `Fabrika Kezdőlap`.
- Ez egy saját theme admin oldal, logikusan csoportosított blokkokkal:
  - Általános (meta title, meta description, footer szöveg, social linkek)
  - Hero
  - Termékkategóriák
  - Galéria
  - Rendelés lépései
  - Ajándékötletek
  - Piaci megjelenés
  - Kapcsolat blokk
  - GYIK
- Így minden szöveg egy admin felületen szerkeszthető, nem külön oldalakban.

## Layout és szerkeszthetőség elvek (kiemelt)
- Minden olyan blokk, amiben több elem van (pl. kategóriák, galéria képek, lépések, GYIK), saját repeater legyen.
- A repeaterek elemei szabadon hozzáadhatók/elvenhetők legyenek, és a frontend layout ezt törvényszerűen kezelje:
  - Grid/flex auto-wrap, responsive breakpointok, ésszerű gap-ek.
  - Nincs "üres hely" vagy eltérő magasság miatti szétesés; a kártyák/képek rugalmasan igazodnak a darabszámhoz.
- A tartalommodell legyen logikus: ahol kell cím+leírás+kép, ott ezek egyetlen repeater sorban legyenek, ne külön mezőkben.

## Animációk és effektek megőrzése (kiemelt)
- A jelenlegi animációk/effektek maradjanak meg, különösen a `Hero` szekció:
  - Hero háttér interaction reveal
  - Gear spin / scroll state
  - Parallax engine
  - Scroll reveal, navbar scroll state, back-to-top, smooth scroll
- Implementációs szabály: a JS csak úgy mozgatható/theme-be szervezhető, hogy az eredeti DOM struktúra (id-k, classok, `data-*` attribútumok) kompatibilis maradjon, különben az animációk eltörnek.

## Oldalszerkezet -> WP adatmodell
- Navbar:
  - Forrás: WP Menu (`wp_nav_menu`) + `Fabrika Kezdőlap` brand beállítások.
- Hero:
  - Forrás: saját szöveges mezők (badge, főcím, alcím, CTA szöveg, CTA URL).
- Termék kategóriák (5 kártya):
  - Forrás: saját repeater (cím, leírás, kép).
- Galéria rács (10 kép):
  - Forrás: saját repeater (kép, alt szöveg opcionális).
- Hogyan rendelhetsz (3 lépés):
  - Forrás: saját repeater (lépés cím + leírás).
- Ajándékötletek (5 blokk):
  - Forrás: saját repeater (cím + leírás + ikon választó).
- Piaci megjelenés:
  - Forrás: saját szöveges mezők (cím, leírás, másodlagos leírás).
- Kapcsolat szekció:
  - Forrás: opcionális form shortcode + saját mezők az oldalsó kontakt infókhoz.
- GYIK:
  - Forrás: saját repeater (kérdés, válasz).
- Footer:
  - Forrás: saját mezők (név, helyszín, copyright, social URL-ek).

## Theme implementációs terv
- `wp-content/themes/fabrika-62/` custom theme létrehozása.
- Alap fájlok: `style.css`, `functions.php`, `front-page.php`, `header.php`, `footer.php`.
- Asset kezelés:
  - A jelenlegi design CSS/JS átemelése külön `assets/css` és `assets/js` fájlokba.
  - `functions.php`-ban enqueue (`wp_enqueue_style`, `wp_enqueue_script`).
- Statikus HTML feldarabolása:
  - Fejléc és footer rész kiszervezése.
  - Minden szekció dinamikus adatokkal renderelve.
- URL-ek WP kompatibilissé tétele:
  - Kép hivatkozások `wp_get_attachment_image()` vagy attachment ID alapon.

## Minőségbiztosítás (hogyan tartjuk magas szinten a minőséget)
- "Golden master" referencia: a statikus `6-2/index.html` (és/vagy `dist/6-2/index.html`) legyen a vizuális/viselkedési etalon; minden szekciót ehhez hasonlítunk.
- Tartalom stresszteszt minden repeaterre: 0 / 1 / 2 / 3 / 5 / 10 elem; rövid/extra hosszú szöveg; kép nélkül; alt szöveg nélkül. A layoutnak törés nélkül kell alkalmazkodnia.
- Animáció "DOM contract": a Hero és a scroll/parallax JS működése függ id-k/classok/`data-*` attribútumok jelenlététől; ezekből csinálunk egy rövid checklistet, és implementáció közben nem törjük meg.
- JS smoke-check (dev): ha valami hiányzik (pl. `#hero`, `#navbar`), ne törjön el az egész script; inkább `console.warn` és graceful fallback.
- Csökkentett mozgás ellenőrzés: `prefers-reduced-motion` alatt ne legyen "betöréssel" járó élményromlás (szöveg/CTA legyen azonnal látható).
- Vizuális regresszió (opcionális, de ajánlott): Playwright/Backstop-szerű screenshot összehasonlítás desktop + mobil viewporttal legalább a Hero + 2 további szekcióra.
- Playwright "design parity" (opcionális, de ajánlott): ugyanazokat a screenshotokat elkészítjük a statikus referencia oldalon és a WP alatti oldalon, majd diffeljük.
- Stabilitás: `?parallax=0` paraméter a mozgáshoz, Playwright screenshot `animations: 'disabled'`, és `document.fonts.ready` megvárása.
- Nézetek: legalább 375x812 és 1440x900; állapotok: felső (hero), és egy lejjebb görgetett pont.
- Cél: pixel-diff minimalizálása; eltérések esetén konkrétan megmondható melyik szekció csúszott el.
- Frontend minőség kapuk: képek `srcset`-tel (WP), lazy-load ahol lehet; nincs console error; nincs layout shift a főbb betöltés után.
- Űrlap/email QA: lokál fejlesztésnél ajánlott először email-csapda (pl. MailHog) a biztonságos teszteléshez, majd utána valós SMTP (Post SMTP) bekötése.

## Feladatok (roadmap)
- [x] 1. Saját mezőstruktúra véglegesítése a 6-2 oldal minden szekciójához.
- [x] 2. `Fabrika Kezdőlap` admin oldal létrehozása (egyhelyes tartalomszerkesztés, plugin nélkül).
- [x] 3. Custom theme skeleton létrehozása (`fabrika-62`).
- [x] 4. `front-page.php` elkészítése a 6-2 szerkezet szerint.
- [x] 5. Repeaterek bekötése úgy, hogy a layout darabszámfüggetlenül szépen törjön (kategóriák/galéria/lépések/ötletek/GYIK).
- [x] 6. Animációk/effektek átemelése és regresszióellenőrzése (különösen Hero).
- [x] 7. Kapcsolatűrlap plugin telepítése és shortcode bekötése.
- [x] 8. SMTP plugin beállítása és űrlap email küldés teszt.
- [x] 9. GYIK, footer, kapcsolati adatok teljes admin bekötése.
- [x] 10. Mobil + desktop vizuális regresszió teszt a statikus 6-2-höz képest (layout + animációk).
- [x] 11. Átadás: rövid admin használati leírás (ki mit hol szerkeszt).
- [x] 12. Tartalom stresszteszt futtatása a repeatereken (0/1/2/3/5/10 elem + hosszú szöveg).
- [x] 13. (Opcionális) Playwright screenshot regresszió beállítása (baseline: statikus `dist/6-2`, target: WP futtatás).
- [x] 14. (Opcionális) Email-csapda (MailHog) felhúzása lokális űrlap tesztekhez.

## Döntési pontok (implementáció előtt)
- Kapcsolatűrlap plugin: Fluent Forms vagy Contact Form 7.
- (Opcionális) Bevezetünk-e Playwright/Backstop screenshot regressziót.

## Elfogadási kritériumok
- A kezdőoldal layoutja és viselkedése vizuálisan megfelel a `6-2` tervnek.
- Minden jelenlegi statikus szöveg szerkeszthető a `Fabrika Kezdőlap` admin oldalon.
- A több elemű blokkok (kategóriák/galéria/lépések/ötletek/GYIK) adminból bővíthetők/csökkenthetők, és a layout törés nélkül alkalmazkodik.
- A Hero és a többi animáció/effekt megmarad (parallax/reveal/navbar/back-to-top/smooth scroll).
- A repeaterek stressztesztjein (0/1/2/3/5/10 elem + hosszú szöveg) a layout és a JS nem törik.
- Kapcsolatűrlap sikeresen küld emailt.
