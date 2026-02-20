<?php

declare(strict_types=1);

/**
 * Deletes WordPress "original_image" files created by big image scaling, and
 * removes the "original_image" key from attachment metadata to save disk space.
 *
 * Usage (inside the WP container):
 *   php /tmp/wp-delete-original-images.php
 *
 * Dry run:
 *   DRY_RUN=1 php /tmp/wp-delete-original-images.php
 */

require_once '/var/www/html/wp-load.php';

$dryRun = (getenv('DRY_RUN') === '1');

$q = new WP_Query([
    'post_type' => 'attachment',
    'post_status' => 'inherit',
    'posts_per_page' => -1,
    'fields' => 'ids',
    'meta_query' => [
        [
            'key' => '_wp_attachment_metadata',
            'compare' => 'EXISTS',
        ],
    ],
]);

$deleted = 0;
$skipped = 0;

foreach ($q->posts as $attachment_id) {
    $attachment_id = (int) $attachment_id;
    $meta = wp_get_attachment_metadata($attachment_id);
    if (!is_array($meta) || !isset($meta['original_image']) || !is_string($meta['original_image']) || $meta['original_image'] === '') {
        continue;
    }

    $attached_file = get_attached_file($attachment_id);
    if (!is_string($attached_file) || $attached_file === '') {
        $skipped++;
        continue;
    }

    $original_image = $meta['original_image'];
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

    if ($original_path === '' || $original_path === $attached_file || !file_exists($original_path)) {
        // Still remove the metadata key to avoid dangling refs.
        unset($meta['original_image']);
        if (!$dryRun) {
            wp_update_attachment_metadata($attachment_id, $meta);
        }
        $skipped++;
        continue;
    }

    $size = (int) filesize($original_path);
    echo 'Attachment #' . $attachment_id . ': delete ' . $original_path . ' (' . $size . " bytes)\n";

    if (!$dryRun) {
        @unlink($original_path);
        unset($meta['original_image']);
        wp_update_attachment_metadata($attachment_id, $meta);
    }
    $deleted++;
}

echo "\nDONE\n";
echo 'dry_run=' . ($dryRun ? '1' : '0') . "\n";
echo 'deleted=' . $deleted . "\n";
echo 'skipped=' . $skipped . "\n";

