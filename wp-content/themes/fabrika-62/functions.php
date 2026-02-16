<?php

declare(strict_types=1);

require_once __DIR__ . '/inc/helpers.php';
require_once __DIR__ . '/inc/icons.php';
require_once __DIR__ . '/inc/admin.php';

function fabrika62_setup(): void
{
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', ['search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script']);

    register_nav_menus([
        'primary' => __('Primary Menu', 'fabrika-62'),
    ]);
}
add_action('after_setup_theme', 'fabrika62_setup');

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
    $items = [
        ['href' => '#termekek', 'label' => 'Termekeink'],
        ['href' => '#galeria', 'label' => 'Galeria'],
        ['href' => '#rendeles', 'label' => 'Rendeles'],
        ['href' => '#kapcsolat', 'label' => 'Kapcsolat'],
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
