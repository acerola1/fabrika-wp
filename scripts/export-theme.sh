#!/usr/bin/env bash
# =============================================================================
# export-theme.sh – Fabrika Ajándék WP theme csomagolása éles telepítéshez
#
# Mit csinál:
#   1. A fabrika-62 theme-et zip-be csomagolja → dist/fabrika-62-theme.zip
#   2. Ha a Docker fut, exportálja az admin beállításokat → dist/fabrika62_options.json
#
# Futtatás:
#   bash scripts/export-theme.sh
#
# Eredmény (dist/ mappa):
#   fabrika-62-theme.zip   – feltölthető a célszerver WP admin-jába
#   fabrika62_options.json – az aktuális admin beállítások (import: WP-CLI)
# =============================================================================

set -euo pipefail

REPO_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
THEME_SRC="$REPO_ROOT/wp-content/themes/fabrika-62"
OUT_DIR="$REPO_ROOT/dist"
ZIP_NAME="fabrika-62-theme.zip"
CONTAINER="fabrika_wp_app"

echo ""
echo "=== Fabrika Ajándék – theme export ==="
echo ""

# 1. dist/ könyvtár létrehozása
mkdir -p "$OUT_DIR"

# 2. Theme zip
echo "▶ Theme csomagolása: $ZIP_NAME"
(
  cd "$REPO_ROOT/wp-content/themes"
  zip -r "$OUT_DIR/$ZIP_NAME" "fabrika-62" \
    --exclude "*/test-results/*" \
    --exclude "*/.DS_Store" \
    --exclude "*/__pycache__/*" \
    -q
)
echo "  ✓ Mentve: dist/$ZIP_NAME  ($(du -sh "$OUT_DIR/$ZIP_NAME" | cut -f1))"

# 3. Admin options export (csak ha Docker fut)
echo ""
if docker ps --format '{{.Names}}' 2>/dev/null | grep -q "^${CONTAINER}$"; then
  echo "▶ Admin beállítások exportálása (Docker: $CONTAINER)"
  RAW=$(docker exec "$CONTAINER" php -r "
    require '/var/www/html/wp-load.php';
    \$v = get_option('fabrika62_options');
    echo \$v ? json_encode(\$v, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) : 'null';
  " 2>/dev/null)
  if [ "$RAW" = "null" ] || [ -z "$RAW" ]; then
    echo "  ℹ  Az admin beállítások még nem lettek módosítva az alapértelmezettektől."
    echo "     Az alapértelmezett szövegek a kódban vannak (inc/helpers.php: fabrika62_default_options)."
    echo "     Nincs szükség options importra a telepítéshez."
  else
    echo "$RAW" > "$OUT_DIR/fabrika62_options.json"
    echo "  ✓ Mentve: dist/fabrika62_options.json  ($(wc -c < "$OUT_DIR/fabrika62_options.json") byte)"
  fi
else
  echo "⚠  Docker nem fut – options export kihagyva."
  echo "   Indítsd el: docker compose up -d, majd futtasd újra a scriptet."
fi

echo ""
echo "=== Kész ==="
echo ""
echo "Következő lépések a célszerveren:"
echo "  1. WP Admin → Megjelenés → Témák → Témafeltöltés → dist/$ZIP_NAME"
echo "  2. Aktiváld a fabrika-62 témát"
echo "  3. WP Admin → Beállítások → Állandó hivatkozások → Mentés  (rewrite flush)"
echo "  4. Ha van dist/fabrika62_options.json, importáld WP-CLI-vel:"
echo "       wp option update fabrika62_options \"\$(cat dist/fabrika62_options.json)\""
echo ""
