# Fabrika Ajándék - Admin használati leírás

## Bejelentkezés
1. Nyisd meg: `http://localhost:8080/wp-admin/`
2. Jelentkezz be az admin fiókkal.

## Tartalom szerkesztés: "Fabrika Kezdőlap" menü

A teljes kezdőoldal tartalma **egyetlen admin oldalon** szerkeszthető, plugin nélkül.

**Megtalálható:** WP Admin bal oldali menüben -> **Fabrika Kezdőlap** (ecset ikon)

### Blokkok és mezőik

| Blokk | Mezők | Megjegyzés |
|-------|-------|------------|
| **Általános** | Meta description, Brand név | A brand név a navbarban és a footerben jelenik meg. |
| **Hero** | Badge szöveg, Hero cím, Hero alcím, CTA felirat, CTA link | A badge és alcím HTML-t is elfogad (pl. `<br>`). |
| **Termékkategóriák** | Szekció cím + repeater (kép, cím, leírás) | Kártya hozzáadás: "Új kártya" gomb. Törlés: "Sor törlése". |
| **Galéria** | Szekció cím | A képek automatikusan a feltöltött termékek kiemelt képeiből jönnek. |
| **Rendelés lépései** | Szekció cím + repeater (lépés cím, leírás) | A lépések automatikusan számozódnak (1, 2, 3...). |
| **Ajándékötletek** | Szekció cím + repeater (ikon, cím, leírás) | Ikon választható: Ajándék, Szív, Ember, Csillag, Mosoly. |
| **Piaci megjelenés** | Szekció cím, Fő szöveg, Másodlagos szöveg | Egyszerűen szöveg mezők. |
| **Kapcsolat** | Szekció cím, Űrlap shortcode, Email, Facebook, Viber, Instagram | A contact form shortcode-ot nem kell módosítani, hacsak nem cserélik a plugint. |
| **GYIK** | Szekció cím + repeater (kérdés, válasz) | Az accordion (lenyíló) automatikusan működik a frontenden. |
| **Footer** | Helyszín, Facebook URL, Instagram URL | A copyright szöveg automatikus (év + brand név). |

### Repeaterek (ismétlődő elemek) kezelése

A repeater blokkok (kategóriák, galéria, lépések, ötletek, GYIK) így működnek:

- **Új elem hozzáadása:** kattints az "Új kártya" / "Új kép" / "Új lépés" / "Új ötlet" / "Új kérdés" gombra a blokk alján.
- **Elem törlése:** kattints az adott sor alján lévő "Sor törlése" linkre.
- **Kép választása:** a kép mező mellett lévő "Kép választása" gomb megnyitja a WordPress Media Library-t. Válassz képet, vagy tölts fel újat.
- **Mentés:** mindig kattints az oldal alján lévő **"Mentés"** gombra a módosítások után.

> A frontend layout automatikusan alkalmazkodik a darabszámhoz: ha több vagy kevesebb elem van, a grid/flex elrendezés törés nélkül kezeli.

## Termékek feltöltése (egylépéses)

Termék feltöltéshez nem kell a klasszikus "dobozos" WordPress szerkesztő: van egy egyszerű, telefonbarát form.

**Megtalálható:** WP Admin bal oldali menüben -> **Termékek** -> **Termék feltöltés**

### Mezők
- **Név** (kötelező)
- **Leírás** (opcionális)
- **Ár (Ft)** (opcionális, csak szám)
- **Kép** (kötelező, 1 db)
- **Címkék** (opcionális, csak választás a meglévőkből)

### Mentés
- A **"Termék mentése (publikus)"** gombbal a termék azonnal publikus lesz.
- A **Termék kód** automatikus: a termék ID (szám).

### Címkék szerkesztése (ritkán kell)
- WP Admin -> **Termékek** -> **Címkék**

## Kapcsolatűrlap (Contact Form 7)

Az űrlap a **Contact Form 7** pluginnal működik.

- **Űrlap szerkesztése:** WP Admin -> **Kapcsolat** -> **Fabrika Kapcsolat** form
- **Mezők:** Név, E-mail, Telefon (opcionális), Kategória (lenyíló), **Melyik termék érdekli?**, Üzenet
- **Email címzett:** az űrlap beállításokban (Contact Form 7 -> Fabrika Kapcsolat -> Mail fül)

### Email küldés (fejlesztői környezet)

Lokális fejlesztésnél az emailek a **MailHog** email-csapdába érkeznek (nem mennek ki valódi email címre):

- **MailHog Web UI:** `http://localhost:8025`
- Itt láthatod az összes teszt-emailt, amit az űrlap küld.

> **Éles környezetben:** a MailHog-ot le kell cserélni valós SMTP-re (pl. Post SMTP plugin + Gmail/SMTP szerver).

## Technikai információk

### Docker parancsok
| Parancs | Leírás |
|---------|--------|
| `docker compose up -d` | Indítja a WordPress + DB + MailHog konténert |
| `docker compose down` | Leállítja a konténereket (adatok megmaradnak) |
| `docker compose ps` | Konténerek állapota |

### Elérhetőségek (lokál)
| Szolgáltatás | URL |
|-------------|-----|
| Frontend | http://localhost:8080 |
| WP Admin | http://localhost:8080/wp-admin/ |
| MailHog | http://localhost:8025 |

### Theme fájlok
A theme a `wp-content/themes/fabrika-62/` könyvtárban található (bind mount a Docker-ben, tehát a helyi fájlrendszerben szerkeszthető):

| Fájl | Funkció |
|------|---------|
| `front-page.php` | A főoldal sablon (minden szekció) |
| `header.php` | Fejléc, navbar, CSS |
| `footer.php` | Lábléc, back-to-top, JS betöltés |
| `functions.php` | Theme setup, asset-ek, helper függvények |
| `inc/admin.php` | Admin felület ("Fabrika Kezdőlap") |
| `assets/app.css` | Tailwind CSS |
| `assets/app.js` | Animációk, effektek (parallax, scroll reveal, FAQ, stb.) |

### Vizuális regresszió teszt
A statikus referencia (`6-2/index.html`) és a WP oldal vizuális összehasonlítása Playwright-tal:

```bash
# 1. Indítsd el a Vite dev szervert (statikus referencia)
npx vite --port 3999

# 2. A Docker fusson (WP)
docker compose up -d

# 3. Futtasd a tesztet
node tests/visual-regression.mjs

# 4. Eredmények: tests/screenshots/
#    static_*.png vs wp_*.png összehasonlítás
```
