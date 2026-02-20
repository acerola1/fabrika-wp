<?php

declare(strict_types=1);

$root = '/var/www/html';
if (!defined('WP_INSTALLING')) {
    // On a fresh DB, wp-load.php can otherwise redirect+die to install.php before this script runs.
    define('WP_INSTALLING', true);
}
if (!isset($_SERVER['HTTP_HOST']) || !is_string($_SERVER['HTTP_HOST']) || $_SERVER['HTTP_HOST'] === '') {
    $_SERVER['HTTP_HOST'] = 'localhost';
}
if (!isset($_SERVER['REQUEST_URI']) || !is_string($_SERVER['REQUEST_URI']) || $_SERVER['REQUEST_URI'] === '') {
    $_SERVER['REQUEST_URI'] = '/';
}
require_once $root . '/wp-load.php';

$mode = getenv('WP_BOOTSTRAP_MODE');
if (!is_string($mode) || $mode === '') {
    $mode = 'seeded';
}
$mode = in_array($mode, ['empty', 'seeded'], true) ? $mode : 'seeded';

$site_url = getenv('WP_URL');
if (!is_string($site_url) || $site_url === '') {
    $site_url = 'http://localhost:8080';
}

$admin_user = getenv('WP_ADMIN_USER');
if (!is_string($admin_user) || $admin_user === '') {
    $admin_user = 'ci-admin';
}
$admin_pass = getenv('WP_ADMIN_PASS');
if (!is_string($admin_pass) || $admin_pass === '') {
    $admin_pass = 'ci-admin-pass';
}
$admin_email = getenv('WP_ADMIN_EMAIL');
if (!is_string($admin_email) || $admin_email === '') {
    $admin_email = 'ci@example.com';
}

if (!is_blog_installed()) {
    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    wp_install('Fabrika CI', $admin_user, $admin_email, true, '', $admin_pass);
}

update_option('home', $site_url);
update_option('siteurl', $site_url);

if (get_template() !== 'fabrika-62') {
    switch_theme('fabrika-62');
}

if (function_exists('fabrika62_default_options')) {
    $opts = fabrika62_default_options();
    $opts['contact_form_shortcode'] = '';
    update_option('fabrika62_options', $opts, false);
}

if (function_exists('fabrika62_ensure_default_termek_tags')) {
    fabrika62_ensure_default_termek_tags();
}

update_option('permalink_structure', '/%postname%/');
flush_rewrite_rules();

$user = get_user_by('login', $admin_user);
if ($user instanceof WP_User) {
    wp_set_password($admin_pass, $user->ID);
} else {
    wp_create_user($admin_user, $admin_pass, $admin_email);
    $created = get_user_by('login', $admin_user);
    if ($created instanceof WP_User) {
        $created->set_role('administrator');
    }
}

if ($mode === 'empty') {
    $posts = get_posts([
        'post_type' => 'fabrika_termek',
        'post_status' => 'any',
        'numberposts' => -1,
        'fields' => 'ids',
    ]);
    foreach ($posts as $post_id) {
        wp_delete_post((int) $post_id, true);
    }
}

echo "BOOTSTRAP_OK mode={$mode}\n";
