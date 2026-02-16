# Fabrika Ajandek - Admin hasznalati leiras

## Bejelentkezes
1. Nyisd meg: `http://localhost:8080/wp-admin/`
2. Jelentkezz be az admin fiokkal.

## Tartalom szerkesztes: "Fabrika Kezdolap" menu

A teljes kezdooldal tartalma **egyetlen admin oldalon** szerkesztheto, plugin nelkul.

**Megtalalhato:** WP Admin bal oldali menuben -> **Fabrika Kezdolap** (ecset ikon)

### Blokkok es mezoik

| Blokk | Mezok | Megjegyzes |
|-------|-------|------------|
| **Altalanos** | Meta description, Brand nev | A brand nev a navbarban es a footerben jelenik meg. |
| **Hero** | Badge szoveg, Hero cim, Hero alcim, CTA felirat, CTA link | A badge es alcim HTML-t is elfogad (pl. `<br>`). |
| **Termekkategoriak** | Szekcio cim + repeater (kep, cim, leiras) | Kartya hozzaadas: "Uj kartya" gomb. Torles: "Sor torlese". |
| **Galeria** | Szekcio cim + repeater (kep, alt szoveg) | Kep hozzaadas: "Uj kep" gomb, majd "Kep valasztasa" a Media Library-bol. |
| **Rendeles lepesei** | Szekcio cim + repeater (lepes cim, leiras) | A lepesek automatikusan szamozodnak (1, 2, 3...). |
| **Ajandekotletek** | Szekcio cim + repeater (ikon, cim, leiras) | Ikon valaszthato: Ajandek, Sziv, Ember, Csillag, Mosoly. |
| **Piaci megjelenes** | Szekcio cim, Fo szoveg, Masodlagos szoveg | Egyszeruen szoveg mezok. |
| **Kapcsolat** | Szekcio cim, Urlap shortcode, Email, Facebook, Viber, Instagram | A contact form shortcode-ot nem kell modositani, hacsak nem cserelik a plugint. |
| **GYIK** | Szekcio cim + repeater (kerdes, valasz) | Az accordion (lenyilo) automatikusan mukodik a frontenden. |
| **Footer** | Helyszin, Facebook URL, Instagram URL | A copyright szoveg automatikus (ev + brand nev). |

### Repeaterek (ismetlodo elemek) kezelese

A repeater blokkok (kategoriak, galeria, lepesek, otletek, GYIK) igy mukonek:

- **Uj elem hozzaadasa:** kattints az "Uj kartya" / "Uj kep" / "Uj lepes" / "Uj otlet" / "Uj kerdes" gombra a blokk aljan.
- **Elem torlese:** kattints az adott sor aljan levo "Sor torlese" linkre.
- **Kep valasztasa:** a kep mezo mellett levo "Kep valasztasa" gomb megnyitja a WordPress Media Library-t. Valassz kepet, vagy tolts fel ujat.
- **Mentes:** mindig kattints az oldal aljan levo **"Mentes"** gombra a modositasok utan!

> A frontend layout automatikusan alkalmazkodik a darabszamhoz: ha tobb vagy kevesebb elem van, a grid/flex elrendezes tores nelkul kezeli.

## Kapcsolaturlap (Contact Form 7)

Az urlap a **Contact Form 7** pluginnal mukodik.

- **Urlap szerkesztese:** WP Admin -> **Kapcsolat** -> **Fabrika Kapcsolat** form
- **Mezok:** Nev, Email, Telefon (opcionalis), Kategoria (lenyilo), Uzenet
- **Email cimzett:** az urlap beallitasokban (Contact Form 7 -> Fabrika Kapcsolat -> Mail ful)

### Email kuldes (fejlesztoi kornyezet)

Lokalis fejlesztesnel az emailek a **MailHog** email-csapdaba erkeznek (nem mennek ki valodi email cimre):

- **MailHog Web UI:** `http://localhost:8025`
- Itt lathatod az osszes teszt-emailt, amit az urlap kuld.

> **Eles kornyezetben:** a MailHog-ot le kell cserelni valos SMTP-re (pl. Post SMTP plugin + Gmail/SMTP szerver).

## Technikai informaciok

### Docker parancsok
| Parancs | Leiras |
|---------|--------|
| `docker compose up -d` | Inditja a WordPress + DB + MailHog kontenert |
| `docker compose down` | Leallitja a konternereket (adatok megmaradnak) |
| `docker compose ps` | Kontenerek allapota |

### Elerhetosegek (lokal)
| Szolgaltatas | URL |
|-------------|-----|
| Frontend | http://localhost:8080 |
| WP Admin | http://localhost:8080/wp-admin/ |
| MailHog | http://localhost:8025 |

### Theme fajlok
A theme a `wp-content/themes/fabrika-62/` konyvtarban talalhato (bind mount a Docker-ben, tehat a helyi fajlrendszerben szerkesztheto):

| Fajl | Funkcio |
|------|---------|
| `front-page.php` | A fooldal sablon (minden szekcio) |
| `header.php` | Fejlec, navbar, CSS |
| `footer.php` | Lablec, back-to-top, JS betoltes |
| `functions.php` | Theme setup, asset-ek, helper fuggvenyek |
| `inc/admin.php` | Admin felulet ("Fabrika Kezdolap") |
| `assets/app.css` | Tailwind CSS |
| `assets/app.js` | Animaciok, effektek (parallax, scroll reveal, FAQ, stb.) |

### Vizualis regresszio teszt
A statikus referencia (`6-2/index.html`) es a WP oldal vizualis osszehasonlitasa Playwright-tel:

```bash
# 1. Inditsd el a Vite dev szervert (statikus referencia)
npx vite --port 3999

# 2. A Docker fusson (WP)
docker compose up -d

# 3. Futtasd a tesztet
node tests/visual-regression.mjs

# 4. Eredmenyek: tests/screenshots/
#    static_*.png vs wp_*.png osszehasonlitas
```
