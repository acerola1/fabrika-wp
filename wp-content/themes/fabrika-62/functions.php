<?php

declare(strict_types=1);

require_once __DIR__ . '/inc/helpers.php';
require_once __DIR__ . '/inc/icons.php';

const FABRIKA62_TERMEK_POST_TYPE = 'fabrika_termek';
const FABRIKA62_TERMEK_TAX = 'fabrika_tag';

require_once __DIR__ . '/inc/admin.php';
require_once __DIR__ . '/inc/termek-admin.php';

function fabrika62_setup(): void
{
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', ['search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script']);

    // Image sizes for product cards / catalog.
    // These rely on WP's image editor (GD/Imagick) to generate thumbnails on upload.
    add_image_size('square_600', 600, 600, true);
    add_image_size('square_1200', 1200, 1200, true);

    register_nav_menus([
        'primary' => __('Primary Menu', 'fabrika-62'),
    ]);
}
add_action('after_setup_theme', 'fabrika62_setup');

function fabrika62_register_termek_post_type(): void
{
    $labels = [
        'name' => __('Termékek', 'fabrika-62'),
        'singular_name' => __('Termék', 'fabrika-62'),
        'add_new' => __('Új termék', 'fabrika-62'),
        'add_new_item' => __('Új termék hozzáadása', 'fabrika-62'),
        'edit_item' => __('Termék szerkesztése', 'fabrika-62'),
        'new_item' => __('Új termék', 'fabrika-62'),
        'view_item' => __('Termék megtekintése', 'fabrika-62'),
        'search_items' => __('Termék keresése', 'fabrika-62'),
        'not_found' => __('Nincs találat', 'fabrika-62'),
        'not_found_in_trash' => __('A kukában sincs találat', 'fabrika-62'),
        'menu_name' => __('Termékek', 'fabrika-62'),
    ];

    register_post_type(FABRIKA62_TERMEK_POST_TYPE, [
        'labels' => $labels,
        'public' => true,
        'show_in_rest' => true,
        'menu_icon' => 'dashicons-cart',
        'supports' => ['title', 'editor', 'thumbnail', 'excerpt'],
        'has_archive' => true,
        'rewrite' => ['slug' => 'termekek', 'with_front' => false],
    ]);
}
add_action('init', 'fabrika62_register_termek_post_type');

function fabrika62_register_termek_taxonomies(): void
{
    register_taxonomy(FABRIKA62_TERMEK_TAX, FABRIKA62_TERMEK_POST_TYPE, [
        'label' => __('Címkék', 'fabrika-62'),
        'public' => true,
        'hierarchical' => false,
        'show_in_rest' => true,
        'show_admin_column' => true,
        'rewrite' => ['slug' => 'termek-cimke', 'with_front' => false],
    ]);
}
add_action('init', 'fabrika62_register_termek_taxonomies');

function fabrika62_ensure_default_termek_tags(): void
{
    // Keep the catalog filter bar consistent with the 6-3 mock by ensuring the base set of tags exist.
    $defaults = [
        'lezer-gravirozott-kepek' => 'Lézergravírozott képek',
        'fa-tablak' => 'Fa táblák',
        'kis-ajandektargyak' => 'Kis ajándéktárgyak',
        'kerti-diszek' => 'Kerti díszek',
        'szines-nyomatok' => 'Színes nyomatok',
        'testreszabhato' => 'Testreszabható',
    ];

    foreach ($defaults as $slug => $name) {
        $exists = term_exists($slug, FABRIKA62_TERMEK_TAX);
        if (!$exists) {
            wp_insert_term($name, FABRIKA62_TERMEK_TAX, ['slug' => $slug]);
            continue;
        }

        $term = get_term_by('slug', $slug, FABRIKA62_TERMEK_TAX);
        if ($term instanceof WP_Term) {
            $current = trim((string) $term->name);
            if ($current === $slug) {
                wp_update_term((int) $term->term_id, FABRIKA62_TERMEK_TAX, ['name' => $name]);
            }
        }
    }
}
add_action('init', 'fabrika62_ensure_default_termek_tags', 30);

function fabrika62_flush_rewrite_on_theme_switch(): void
{
    fabrika62_register_termek_post_type();
    fabrika62_register_termek_taxonomies();
    fabrika62_ensure_default_termek_tags();
    flush_rewrite_rules();
}
add_action('after_switch_theme', 'fabrika62_flush_rewrite_on_theme_switch');

function fabrika62_add_termek_meta_boxes(): void
{
    add_meta_box(
        'fabrika62_termek_meta',
        __('Termék adatok', 'fabrika-62'),
        'fabrika62_render_termek_meta_box',
        FABRIKA62_TERMEK_POST_TYPE,
        'side',
        'default'
    );
}
add_action('add_meta_boxes', 'fabrika62_add_termek_meta_boxes');

function fabrika62_render_termek_meta_box(\WP_Post $post): void
{
    wp_nonce_field('fabrika62_save_termek_meta', 'fabrika62_termek_meta_nonce');

    $price = get_post_meta($post->ID, 'fabrika62_termek_ar', true);
    $price_str = is_string($price) ? $price : '';

    $code = get_post_meta($post->ID, 'fabrika62_termek_kod', true);
    $code_str = is_string($code) ? $code : '';
    $code_display = trim($code_str) !== '' ? trim($code_str) : (string) $post->ID;
    ?>
    <p>
      <label for="fabrika62_termek_ar"><strong><?php echo esc_html__('Ár (Ft)', 'fabrika-62'); ?></strong></label><br/>
      <input
        id="fabrika62_termek_ar"
        name="fabrika62_termek_ar"
        type="number"
        inputmode="numeric"
        min="0"
        step="1"
        value="<?php echo esc_attr($price_str); ?>"
        style="width:100%;"
        placeholder="3500"
      />
    </p>
    <p>
      <label for="fabrika62_termek_kod"><strong><?php echo esc_html__('Termék kód (automatikus)', 'fabrika-62'); ?></strong></label><br/>
      <input
        id="fabrika62_termek_kod"
        type="text"
        value="<?php echo esc_attr($code_display); ?>"
        style="width:100%;"
        placeholder="<?php echo esc_attr((string) $post->ID); ?>"
        readonly
      />
    </p>
    <p style="margin:0;">
      <small><?php echo esc_html__('Tipp: képekhez 1200×1200 master, a katalógushoz square_600 készül.', 'fabrika-62'); ?></small>
    </p>
    <?php
}

function fabrika62_save_termek_meta(int $post_id): void
{
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (!isset($_POST['fabrika62_termek_meta_nonce']) || !wp_verify_nonce((string) $_POST['fabrika62_termek_meta_nonce'], 'fabrika62_save_termek_meta')) {
        return;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    if (isset($_POST['fabrika62_termek_ar'])) {
        $raw = is_string($_POST['fabrika62_termek_ar']) ? $_POST['fabrika62_termek_ar'] : '';
        $raw = trim($raw);
        if ($raw === '') {
            delete_post_meta($post_id, 'fabrika62_termek_ar');
        } else {
            $val = preg_replace('/[^\d]/', '', $raw);
            $intVal = (int) $val;
            if ($intVal <= 0) {
                delete_post_meta($post_id, 'fabrika62_termek_ar');
            } else {
                update_post_meta($post_id, 'fabrika62_termek_ar', (string) $intVal);
            }
        }
    }
}
add_action('save_post_' . FABRIKA62_TERMEK_POST_TYPE, 'fabrika62_save_termek_meta');

function fabrika62_ensure_termek_kod(int $post_id, \WP_Post $post, bool $update): void
{
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (wp_is_post_revision($post_id) || wp_is_post_autosave($post_id)) {
        return;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    if ($post->post_status === 'auto-draft') {
        return;
    }

    $existing = get_post_meta($post_id, 'fabrika62_termek_kod', true);
    $existing_str = is_string($existing) ? trim($existing) : '';
    if ($existing_str !== '') {
        return;
    }

    update_post_meta($post_id, 'fabrika62_termek_kod', (string) $post_id);
}
add_action('save_post_' . FABRIKA62_TERMEK_POST_TYPE, 'fabrika62_ensure_termek_kod', 30, 3);

function fabrika62_image_size_labels(array $sizes): array
{
    $sizes['square_600'] = __('Négyzet 600 (katalógus)', 'fabrika-62');
    $sizes['square_1200'] = __('Négyzet 1200 (master)', 'fabrika-62');
    return $sizes;
}
add_filter('image_size_names_choose', 'fabrika62_image_size_labels');

function fabrika62_admin_notice_image_editor(): void
{
    if (!is_admin() || !current_user_can('manage_options')) {
        return;
    }
    $supported = wp_image_editor_supports(['mime_type' => 'image/jpeg'])
        && wp_image_editor_supports(['mime_type' => 'image/png']);
    if ($supported) {
        return;
    }
    echo '<div class="notice notice-warning"><p>';
    echo esc_html__('Figyelem: a szerveren nem elérhető a WordPress kép-átméretező (GD/Imagick). Így a square_600/square_1200 méretek nem fognak legenerálódni feltöltéskor.', 'fabrika-62');
    echo '</p></div>';
}
add_action('admin_notices', 'fabrika62_admin_notice_image_editor');

function fabrika62_delete_original_image_after_scaling(array $metadata, int $attachment_id): array
{
    if (!is_array($metadata) || $metadata === []) {
        return $metadata;
    }
    if (!isset($metadata['original_image']) || !is_string($metadata['original_image']) || $metadata['original_image'] === '') {
        return $metadata;
    }

    $attached_file = get_attached_file($attachment_id);
    if (!is_string($attached_file) || $attached_file === '') {
        return $metadata;
    }

    $original_image = $metadata['original_image'];
    $original_path = '';

    if (str_contains($original_image, '/')) {
        $uploads = wp_upload_dir();
        $basedir = is_string($uploads['basedir'] ?? null) ? (string) $uploads['basedir'] : '';
        if ($basedir !== '') {
            $original_path = trailingslashit($basedir) . ltrim($original_image, '/');
        }
    } else {
        $original_path = trailingslashit(dirname($attached_file)) . $original_image;
    }

    if ($original_path !== '' && $original_path !== $attached_file && file_exists($original_path)) {
        // Keep the scaled image + generated sizes, but remove the large original to save disk.
        @unlink($original_path);
    }

    // Avoid referring to a removed file in the attachment metadata.
    unset($metadata['original_image']);
    return $metadata;
}
add_filter('wp_generate_attachment_metadata', 'fabrika62_delete_original_image_after_scaling', 20, 2);

function fabrika62_enqueue_assets(): void
{
    $theme_uri = get_stylesheet_directory_uri();
    $theme_dir = get_stylesheet_directory();

    $css_path = $theme_dir . '/assets/app.css';
    $css_ver = file_exists($css_path) ? (string) filemtime($css_path) : null;
    wp_enqueue_style('fabrika62-app', $theme_uri . '/assets/app.css', [], $css_ver);

    $js_path = $theme_dir . '/assets/app.js';
    $js_ver = file_exists($js_path) ? (string) filemtime($js_path) : null;
    wp_enqueue_script('fabrika62-app', $theme_uri . '/assets/app.js', [], $js_ver, true);
}
add_action('wp_enqueue_scripts', 'fabrika62_enqueue_assets');

function fabrika62_render_anchor_menu(string $menu_class = ''): void
{
    // For one-page anchors, a hardcoded fallback is often better than forcing menu setup.
    $prefix = is_front_page() ? '' : home_url('/');
    $items = [
        ['href' => $prefix . '#termekek', 'label' => 'Termékeink'],
        ['href' => $prefix . '#galeria', 'label' => 'Galéria'],
        ['href' => $prefix . '#rendeles', 'label' => 'Rendelés'],
        ['href' => $prefix . '#kapcsolat', 'label' => 'Kapcsolat'],
    ];

    foreach ($items as $item) {
        printf(
            '<a href="%s" class="%s">%s</a>',
            esc_attr($item['href']),
            esc_attr($menu_class),
            esc_html($item['label'])
        );
    }
}
