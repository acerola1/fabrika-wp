#!/usr/bin/env bash
# =============================================================================
# setup-wpforms.sh – Lokál WPForms kapcsolat űrlap automatikus beállítása
#
# Mit csinál:
#   1. Létrehozza vagy frissíti a "Fabrika Kapcsolat" WPForms űrlapot
#   2. Magyar mezők/szövegek + AJAX submit + magyar visszajelzés
#   3. Beállítja a theme optionben a contact_form_shortcode értékét
#
# Futtatás:
#   bash scripts/setup-wpforms.sh
#
# Elvárt környezet:
#   - Docker fut
#   - WordPress konténer neve: fabrika_wp_app (alapértelmezett)
# =============================================================================

set -euo pipefail

CONTAINER="${WP_CONTAINER:-fabrika_wp_app}"

if ! docker ps --format '{{.Names}}' | grep -q "^${CONTAINER}$"; then
  echo "Hiba: nem fut a konténer: ${CONTAINER}"
  echo "Indítsd el: docker compose up -d"
  exit 1
fi

TMP_PHP="$(mktemp)"
cleanup() {
  rm -f "$TMP_PHP"
  docker exec "$CONTAINER" rm -f /tmp/setup-wpforms.php >/dev/null 2>&1 || true
}
trap cleanup EXIT

cat >"$TMP_PHP" <<'PHP'
<?php
declare(strict_types=1);

require '/var/www/html/wp-load.php';

if (!post_type_exists('wpforms')) {
    fwrite(STDERR, "Hiba: WPForms post_type nem elérhető. Aktiváld a WPForms plugint.\n");
    exit(2);
}

$form_title = 'Fabrika Kapcsolat';
$confirmation_message = '<p>Köszönjük az üzeneted! Hamarosan felvesszük veled a kapcsolatot.</p>';

// CLI alatt állítsunk admin felhasználót, hogy a WPForms cap check átmenjen.
$admins = get_users(['role' => 'administrator', 'number' => 1, 'fields' => ['ID']]);
if (!empty($admins[0]->ID)) {
    wp_set_current_user((int) $admins[0]->ID);
}

$form_renders = static function(int $id): bool {
    if ($id <= 0) {
        return false;
    }
    $html = do_shortcode('[wpforms id="' . $id . '"]');
    return is_string($html) && strlen(trim($html)) > 0;
};

$form_id = 0;
$existing = get_page_by_title($form_title, OBJECT, 'wpforms');
if ($existing instanceof WP_Post && $form_renders((int) $existing->ID)) {
    $form_id = (int) $existing->ID;
}

if ($form_id <= 0) {
    // Keressünk működő meglévő WPForms űrlapot.
    $all_forms = get_posts([
        'post_type'        => 'wpforms',
        'post_status'      => 'publish',
        'numberposts'      => -1,
        'orderby'          => 'ID',
        'order'            => 'ASC',
        'suppress_filters' => false,
    ]);
    foreach ($all_forms as $candidate) {
        if ($candidate instanceof WP_Post && $form_renders((int) $candidate->ID)) {
            $form_id = (int) $candidate->ID;
            break;
        }
    }
}

if ($form_id <= 0) {
    // Ha semmi nem renderel, próbáljuk WPForms API-val létrehozni.
    if (function_exists('wpforms') && wpforms()->obj('form')) {
        $created = wpforms()->obj('form')->add($form_title, [], ['builder' => false]);
        if (!is_wp_error($created) && (int) $created > 0) {
            $form_id = (int) $created;
        }
    }
}

if ($form_id <= 0) {
    // Utolsó fallback.
    $created = wp_insert_post([
        'post_type'   => 'wpforms',
        'post_status' => 'publish',
        'post_title'  => $form_title,
    ], true);
    if (is_wp_error($created)) {
        fwrite(STDERR, "Hiba: nem sikerült létrehozni a WPForms űrlapot: " . $created->get_error_message() . "\n");
        exit(3);
    }
    $form_id = (int) $created;
}

wp_update_post([
    'ID' => $form_id,
    'post_title' => $form_title,
]);

$form_data = [
    'fields' => [
        '0' => [
            'id' => '0',
            'type' => 'name',
            'label' => 'Név',
            'format' => 'simple',
            'description' => '',
            'required' => '1',
            'size' => 'large',
            'simple_placeholder' => 'A neved',
            'simple_default' => '',
            'first_placeholder' => '',
            'first_default' => '',
            'middle_placeholder' => '',
            'middle_default' => '',
            'last_placeholder' => '',
            'last_default' => '',
            'css' => '',
        ],
        '1' => [
            'id' => '1',
            'type' => 'email',
            'label' => 'E-mail',
            'description' => '',
            'required' => '1',
            'size' => 'large',
            'placeholder' => 'pelda@email.com',
            'confirmation_placeholder' => '',
            'default_value' => false,
            'filter_type' => '',
            'allowlist' => '',
            'denylist' => '',
            'css' => '',
        ],
        '7' => [
            'id' => '7',
            'type' => 'text',
            'label' => 'Telefon (opcionális)',
            'description' => '',
            'required' => '',
            'size' => 'large',
            'placeholder' => '+36 XX XXX XXXX',
            'limit_count' => '1',
            'limit_mode' => 'characters',
            'default_value' => '',
            'css' => '',
        ],
        '4' => [
            'id' => '4',
            'type' => 'text',
            'label' => 'Kategória',
            'description' => '',
            'required' => '',
            'size' => 'large',
            'placeholder' => 'Válassz kategóriát...',
            'limit_count' => '1',
            'limit_mode' => 'characters',
            'default_value' => '',
            'css' => '',
        ],
        '8' => [
            'id' => '8',
            'type' => 'text',
            'label' => 'Melyik termék érdekli?',
            'description' => '',
            'required' => '',
            'size' => 'large',
            'placeholder' => 'Pl.: 157 - Gravírozott fatábla',
            'limit_count' => '1',
            'limit_mode' => 'characters',
            'default_value' => '',
            'css' => '',
        ],
        '2' => [
            'id' => '2',
            'type' => 'textarea',
            'label' => 'Üzenet',
            'description' => '',
            'required' => '1',
            'size' => 'medium',
            'placeholder' => 'Írd le az elképzelésed...',
            'limit_count' => '1',
            'limit_mode' => 'characters',
            'default_value' => '',
            'css' => '',
        ],
    ],
    'id' => (string) $form_id,
    'field_id' => 9,
    'settings' => [
        'form_title' => $form_title,
        'form_desc' => '',
        'submit_text' => 'Üzenet küldése',
        'submit_text_processing' => 'Küldés...',
        'form_class' => '',
        'submit_class' => '',
        'ajax_submit' => '1',
        'notification_enable' => '1',
        'notifications' => [
            '1' => [
                'email' => '{admin_email}',
                'subject' => 'Új üzenet: Fabrika Kapcsolat',
                'sender_name' => get_bloginfo('name'),
                'sender_address' => '{admin_email}',
                'replyto' => '{field_id="1"}',
                'message' => '{all_fields}',
                'template' => '',
            ],
        ],
        'confirmations' => [
            '1' => [
                'type' => 'message',
                'message' => $confirmation_message,
                'message_scroll' => '1',
                'page' => 'previous_page',
                'page_url_parameters' => '',
                'redirect' => '',
            ],
        ],
        'antispam_v3' => '1',
        'anti_spam' => [
            'time_limit' => ['enable' => '1', 'duration' => '2'],
            'filtering_store_spam' => '1',
        ],
        'store_spam_entries' => '0',
        'form_tags' => [],
    ],
    'search_terms' => '',
    'providers' => ['constant-contact-v3' => []],
    'meta' => ['template' => 'simple-contact-form-template'],
];

$updated_ok = false;
if (function_exists('wpforms') && wpforms()->obj('form')) {
    $updated_id = wpforms()->obj('form')->update($form_id, $form_data, ['cap' => 'edit_form_single']);
    $updated_ok = !empty($updated_id);
}

if (!$updated_ok) {
    $updated = wp_update_post([
        'ID'           => $form_id,
        'post_title'   => $form_title,
        'post_status'  => 'publish',
        'post_content' => wp_json_encode($form_data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
    ], true);
    if (is_wp_error($updated)) {
        fwrite(STDERR, "Hiba: nem sikerült frissíteni a WPForms űrlapot: " . $updated->get_error_message() . "\n");
        exit(4);
    }
}

$opts = get_option('fabrika62_options');
if (!is_array($opts)) {
    $opts = [];
}
$opts['contact_form_shortcode'] = '[wpforms id="' . $form_id . '"]';
update_option('fabrika62_options', $opts);

echo "WPForms form ID: {$form_id}\n";
echo "Shortcode: [wpforms id=\"{$form_id}\"]\n";
echo "Kész: magyar mezők + AJAX + visszajelzés beállítva.\n";
PHP

docker cp "$TMP_PHP" "${CONTAINER}:/tmp/setup-wpforms.php"
docker exec "$CONTAINER" php /tmp/setup-wpforms.php

echo ""
echo "Következő lépés:"
echo "  - Nyisd meg: http://localhost:8080/?nocache=1#kapcsolat"
echo "  - Teszteld a küldést és a ?termek= prefillt"
