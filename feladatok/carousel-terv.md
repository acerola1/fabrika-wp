# Termék Modal / Carousel terv – 1.12, 1.13, 1.14

## Érintett feladatok (mock, `6-3/termekek.html`)

- **1.12** Termék kártyára kattintva modal/carousel (nagy kép + infók + CTA)
- **1.13** Carousel navigáció: előző/következő, swipe, billentyűzet
- **1.14** Termék leírás hoverre (áttetsző panel, animáció) + mobil fallback

---

## Megközelítés: natív JS + CSS, külső függőség nélkül

A meglévő `termekek.html` inline `<style>` és `<script>` blokkjába kerül minden.
Nincs új fájl, nincs library – összhangban az eddigi mock stílussal.

---

## 1. HTML változtatások

### 1a. `data-desc` attribútum minden `.product-card`-on

Minden kártyára rövid leírás kerül (mock szöveg). Ez táplálja:
- a hover overlay-t (1.14)
- a modalban megjelenő leírást (1.12)

```html
<div class="product-card ..."
     data-categories="..."
     data-id="FA-001"
     data-desc="Egyedi fotóból lézergravírozott portré mágnes. Hűtőre, fémfelületre. Kiváló ajándék névnapra, születésnapra.">
```

### 1b. Hover leírás overlay – minden `.aspect-square`-be, a kép után

```html
<div class="aspect-square overflow-hidden relative">
  <span class="product-id">FA-001</span>
  <img ... />
  <!-- ÚJ -->
  <div class="desc-overlay" aria-hidden="true">
    <p class="desc-text">Egyedi fotóból lézergravírozott portré mágnes...</p>
  </div>
</div>
```

### 1c. Modal HTML – `</body>` előtt, egyszer

```html
<div id="product-modal" role="dialog" aria-modal="true"
     aria-labelledby="modal-title" style="display:none">
  <!-- háttér overlay (kattintva zár) -->
  <div id="modal-backdrop" class="modal-backdrop"></div>
  <!-- modal doboz -->
  <div class="modal-box" role="document">
    <!-- Bezárás -->
    <button id="modal-close" class="modal-close-btn" aria-label="Bezárás (ESC)">
      <svg ...><!-- X ikon --></svg>
    </button>
    <!-- Navigáció -->
    <button id="modal-prev" class="modal-nav-btn modal-prev-btn" aria-label="Előző termék">&#8249;</button>
    <button id="modal-next" class="modal-nav-btn modal-next-btn" aria-label="Következő termék">&#8250;</button>
    <!-- Kép szekció -->
    <div class="modal-image-wrap">
      <img id="modal-img" src="" alt="" loading="eager" />
      <span id="modal-product-id" class="product-id"></span>
    </div>
    <!-- Info szekció -->
    <div class="modal-info">
      <div id="modal-tags" class="flex flex-wrap gap-1 mb-3"></div>
      <h2 id="modal-title" class="modal-product-name"></h2>
      <p id="modal-price" class="modal-price"></p>
      <p id="modal-desc" class="modal-desc"></p>
      <a id="modal-cta" href="#" class="modal-cta-btn">Érdekel</a>
    </div>
    <!-- Számláló -->
    <div id="modal-counter" class="modal-counter" aria-live="polite"></div>
  </div>
</div>
```

---

## 2. CSS (a meglévő `<style>` blokkba)

### Hover overlay (1.14)

```css
/* === HOVER LEÍRÁS OVERLAY === */
.desc-overlay {
  position: absolute;
  inset: 0;
  background: rgba(42, 32, 24, 0.72);
  backdrop-filter: blur(2px);
  color: #FFFBF5;
  padding: 1rem;
  display: flex;
  align-items: flex-end;
  opacity: 0;
  transition: opacity 0.3s ease;
  pointer-events: none;
}
.product-card:hover .desc-overlay {
  opacity: 1;
}
.desc-text {
  font-size: 0.8rem;
  line-height: 1.4;
}
/* Mobil fallback: mindig látszik (kisebb) */
@media (hover: none) {
  .desc-overlay {
    opacity: 1;
    background: rgba(42, 32, 24, 0.55);
    padding: 0.5rem 0.75rem;
    align-items: flex-end;
  }
  .desc-text {
    font-size: 0.72rem;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
  }
}
```

**Mobil döntés:** `@media (hover: none)` → overlay mindig látszik, csonkítva (2 sor), így nem nyomja el a képet. Ez ütközésmentes a card-tap-modal-nyitással.

### Modal (1.12, 1.13)

```css
/* === MODAL OVERLAY === */
#product-modal {
  position: fixed;
  inset: 0;
  z-index: 200;
  display: flex !important; /* JS kezeli a display:none / flex váltást */
  align-items: center;
  justify-content: center;
  opacity: 0;
  transition: opacity 0.25s ease;
}
#product-modal.modal-open {
  opacity: 1;
}
.modal-backdrop {
  position: absolute;
  inset: 0;
  background: rgba(42, 32, 24, 0.85);
  backdrop-filter: blur(6px);
  cursor: pointer;
}
.modal-box {
  position: relative;
  background: #FFFBF5;
  border-radius: 12px;
  max-width: 860px;
  width: 95%;
  max-height: 90vh;
  overflow-y: auto;
  overflow-x: hidden;
  display: flex;
  flex-direction: column;
  transform: translateY(24px) scale(0.97);
  transition: transform 0.25s ease;
  border: 1px solid rgba(184, 115, 51, 0.2);
}
#product-modal.modal-open .modal-box {
  transform: translateY(0) scale(1);
}
/* Desktop: kép bal, info jobb */
@media (min-width: 640px) {
  .modal-box { flex-direction: row; min-height: 420px; }
  .modal-image-wrap { width: 48%; flex-shrink: 0; }
  .modal-info { flex: 1; }
}
.modal-image-wrap {
  position: relative;
  background: #E8DCC8;
}
.modal-image-wrap img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
  min-height: 260px;
}
.modal-info {
  padding: 1.5rem;
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}
.modal-product-name {
  font-family: 'Bitter', serif;
  font-size: 1.4rem;
  font-weight: 700;
  color: #3B2314;
}
.modal-price {
  font-size: 1.5rem;
  font-weight: 700;
  color: #B87333;
}
.modal-desc {
  font-size: 0.9rem;
  color: #5C3D2E;
  line-height: 1.6;
  flex: 1;
}
.modal-cta-btn {
  display: block;
  text-align: center;
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  font-weight: 600;
  color: #FFFBF5;
  background: linear-gradient(135deg, #B87333, #C9A84C);
  transition: transform 0.2s, box-shadow 0.2s;
  margin-top: auto;
}
.modal-cta-btn:hover { transform: scale(1.04); box-shadow: 0 4px 16px rgba(184, 115, 51, 0.4); }
/* Bezárás gomb */
.modal-close-btn {
  position: absolute;
  top: 0.75rem;
  right: 0.75rem;
  z-index: 10;
  background: rgba(59, 35, 20, 0.85);
  color: #FFFBF5;
  border-radius: 50%;
  width: 36px;
  height: 36px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: background 0.2s;
}
.modal-close-btn:hover { background: #B87333; }
/* Navigációs gombok */
.modal-nav-btn {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  z-index: 10;
  background: rgba(59, 35, 20, 0.75);
  color: #FFFBF5;
  border-radius: 50%;
  width: 40px;
  height: 40px;
  font-size: 1.5rem;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: background 0.2s, opacity 0.2s;
}
.modal-nav-btn:hover { background: #B87333; }
.modal-prev-btn { left: 0.5rem; }
.modal-next-btn { right: 0.5rem; }
@media (min-width: 640px) {
  .modal-prev-btn { left: -1.25rem; }
  .modal-next-btn { right: -1.25rem; }
}
/* Számláló */
.modal-counter {
  position: absolute;
  bottom: 0.75rem;
  left: 50%;
  transform: translateX(-50%);
  font-size: 0.75rem;
  color: #E8DCC8;
  background: rgba(59, 35, 20, 0.7);
  padding: 2px 10px;
  border-radius: 999px;
}
/* body scroll lock */
body.modal-body-lock { overflow: hidden; }
```

---

## 3. JS logika (a meglévő `<script>` blokkba, az IIFE végén)

### Struktúra és állapot

```js
// ========== MODAL / CAROUSEL ==========
var modal = document.getElementById('product-modal');
var modalBackdrop = document.getElementById('modal-backdrop');
var modalClose = document.getElementById('modal-close');
var modalPrev = document.getElementById('modal-prev');
var modalNext = document.getElementById('modal-next');
var modalImg = document.getElementById('modal-img');
var modalProductId = document.getElementById('modal-product-id');
var modalTags = document.getElementById('modal-tags');
var modalTitle = document.getElementById('modal-title');
var modalPrice = document.getElementById('modal-price');
var modalDesc = document.getElementById('modal-desc');
var modalCta = document.getElementById('modal-cta');
var modalCounter = document.getElementById('modal-counter');

var currentModalIndex = 0;
var visibleProductsList = [];
```

### Segédfüggvény: látható termékek listája

```js
function getVisibleProducts() {
  return Array.from(productCards).filter(function(c) {
    return c.style.display !== 'none';
  });
}
```

### Modal tartalom renderelés

```js
function renderModal(card) {
  var img = card.querySelector('img');
  var id = card.getAttribute('data-id') || '';
  var name = card.querySelector('h3').textContent.trim();
  var priceEl = card.querySelector('.text-lg.font-bold');
  var price = priceEl ? priceEl.textContent.trim() : '';
  var desc = card.getAttribute('data-desc') || '';
  var tags = Array.from(card.querySelectorAll('.tag-badge'))
    .map(function(t) { return t.textContent.trim(); });
  var ctaEl = card.querySelector('a[href*="#kapcsolat"]');
  var ctaHref = ctaEl ? ctaEl.getAttribute('href') : '#';

  modalImg.src = img ? img.src : '';
  modalImg.alt = img ? img.alt : name;
  modalProductId.textContent = id;
  modalTitle.textContent = name;
  modalPrice.textContent = price;
  modalDesc.textContent = desc;
  modalCta.href = ctaHref;
  modalTags.innerHTML = tags.map(function(t) {
    return '<span class="tag-badge">' + t + '</span>';
  }).join(' ');
  modalCounter.textContent = (currentModalIndex + 1) + ' / ' + visibleProductsList.length;
}
```

### Megnyitás / Zárás

```js
function openModal(card) {
  visibleProductsList = getVisibleProducts();
  currentModalIndex = visibleProductsList.indexOf(card);
  renderModal(card);
  modal.style.display = 'flex';
  requestAnimationFrame(function() {
    modal.classList.add('modal-open');
  });
  document.body.classList.add('modal-body-lock');
  modalClose.focus(); // a11y: fókusz a bezárás gombra
}

function closeModal() {
  modal.classList.remove('modal-open');
  document.body.classList.remove('modal-body-lock');
  setTimeout(function() {
    modal.style.display = 'none';
  }, 260);
}
```

### Navigáció

```js
function navigateModal(direction) {
  if (visibleProductsList.length < 2) return;
  currentModalIndex = (currentModalIndex + direction + visibleProductsList.length)
    % visibleProductsList.length;
  renderModal(visibleProductsList[currentModalIndex]);
}
```

### Kártya kattintható (card click)

```js
productCards.forEach(function(card) {
  card.addEventListener('click', function(e) {
    // CTA gombra kattintva NE nyissa a modalt
    if (e.target.closest('a[href*="#kapcsolat"]')) return;
    openModal(card);
  });
  // Kurzor változtatás hogy kattintható legyen (CSS: cursor-pointer hiányzik a meglévő kártyákon)
  card.style.cursor = 'pointer';
});
```

### Bezárás: X gomb + overlay + ESC

```js
modalClose.addEventListener('click', closeModal);
modalBackdrop.addEventListener('click', closeModal);

document.addEventListener('keydown', function(e) {
  if (!modal.classList.contains('modal-open')) return;
  if (e.key === 'Escape') { e.preventDefault(); closeModal(); }
  if (e.key === 'ArrowLeft')  { e.preventDefault(); navigateModal(-1); }
  if (e.key === 'ArrowRight') { e.preventDefault(); navigateModal(1); }
});
```

### Navigációs gombok

```js
modalPrev.addEventListener('click', function() { navigateModal(-1); });
modalNext.addEventListener('click', function() { navigateModal(1); });
```

### Mobil swipe (1.13)

```js
var touchStartX = 0;
var touchStartY = 0;

modal.addEventListener('touchstart', function(e) {
  touchStartX = e.touches[0].clientX;
  touchStartY = e.touches[0].clientY;
}, { passive: true });

modal.addEventListener('touchend', function(e) {
  var dx = e.changedTouches[0].clientX - touchStartX;
  var dy = e.changedTouches[0].clientY - touchStartY;
  // Csak vízszintes swipe (|dx| > |dy| és legalább 50px)
  if (Math.abs(dx) > Math.abs(dy) && Math.abs(dx) > 50) {
    navigateModal(dx < 0 ? 1 : -1); // balra = következő
  }
}, { passive: true });
```

---

## 4. Leírás szövegek a 12 minta termékhez

| ID     | data-desc (rövid, 1–2 mondat)                                                                           |
|--------|----------------------------------------------------------------------------------------------------------|
| FA-001 | Egyedi fotóból lézergravírozott portré mágnes. Hűtőre, fémfelületre tapad. Kiváló névnapi ajándék.       |
| FA-002 | Lézergravírozott fali dísz, egyedi felirattal vagy fényképpel. Méret: 30×20 cm. Akasztóval.              |
| FA-003 | Kézzel festett fa tábla, tetszőleges szöveggel. Vintage stílus, beltéri és kültéri használatra.          |
| FA-004 | Fa tábla "házirendhez" – ideális nyaralóba, konyhába. Személyre szabható szöveg.                         |
| FA-005 | 4 db-os hűtőmágnes szett fa alappal. Tájkép, állat, vagy egyedi kép alapján.                             |
| FA-006 | Minifontású piknik kosár ajándékba csomagolva. Saját fotóval díszített fa kísérőkártya mellé.             |
| FA-007 | Terrárium tartó fa kerettel, ültetett növénnyel. Kézműves, természetes anyagokból. Beltérre ajánlott.    |
| FA-008 | Falra akasztható virágtartó szett (3 db). Egyedi fafaragott keret, dekoratív cserepekkel.                |
| FA-009 | Színes utazási poszter nyomat, A3-as méret. Kedvenc városod, táj alapján is rendelhető.                  |
| FA-010 | Vintage stílusú művészi poszter nyomat. Keret nélkül, A3, matt papír. Egyedi illusztráció.               |
| FA-011 | Lézergravírozott nagy méretű portré (30×40 cm), keretezve. Fotó alapján gravírozzuk.                     |
| FA-012 | Személyre szabott fali felirat, tetszőleges szöveggel. 40×15 cm, keményfa alap.                          |

---

## 5. Sorrend és szűrőkompatibilitás

A `navigateModal()` mindig `getVisibleProducts()`-ot hív, ami a **jelenleg nem rejtett** (`display !== 'none'`) kártyákat adja vissza.

Ha a szűrő aktív és a modal nyitva van, a navigáció csak a szűrt termékek között lép.
Ha a szűrő megváltozik miközben a modal nyitva van (ez nem valószínű, de lehetséges): `visibleProductsList` a navigálásig nem frissül – ez elfogadható a mock szinten.

---

## 6. A11y – előkészítés (teljes A11y a 2.16c-ben lesz)

| Elem                          | Attribútum / viselkedés                           |
|-------------------------------|---------------------------------------------------|
| Modal container               | `role="dialog"` `aria-modal="true"` `aria-labelledby="modal-title"` |
| Bezárás gomb                  | `aria-label="Bezárás (ESC)"`, fókuszt kap megnyitáskor |
| Navigáció gombok              | `aria-label="Előző termék"` / `"Következő termék"` |
| Számláló div                  | `aria-live="polite"` (képernyőolvasó értesül)     |
| Hover overlay                 | `aria-hidden="true"` (dekoratív)                  |
| Body scroll lock              | `body.modal-body-lock { overflow: hidden; }`       |
| ESC                           | Bezárja a modalt                                  |
| Fókusz trap                   | Nincs (2.16c-ban lesz Tab trap)                   |

---

## 7. Megvalósítási sorrend

1. **1.14 leírás overlay** – CSS + `data-desc` attribútumok + HTML overlay div a kártyákon
   (önálló, nem függ a modaltól; legkönnyebb)
2. **1.12 modal alapok** – HTML + CSS + alapvető JS (open/close/render)
3. **1.13 navigáció** – prev/next gombok JS, billentyűzet, swipe

Mindhárom egy commit-ban is megoldható, sorban ellenőrizve.

---

## 8. Fájlváltozások

| Fájl                       | Változás                                                          |
|----------------------------|-------------------------------------------------------------------|
| `6-3/termekek.html`        | `<style>` + `<script>` + 12x `data-desc` + 12x `.desc-overlay` + modal HTML |
| Nincs más fájl             | –                                                                 |
