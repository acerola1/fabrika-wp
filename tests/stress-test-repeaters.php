<?php
/**
 * Repeater stress test: sets different element counts & long text,
 * then renders the front-page template and checks for PHP errors / broken HTML.
 *
 * Usage: docker cp tests/stress-test-repeaters.php fabrika_wp_app:/tmp/ && docker exec fabrika_wp_app php /tmp/stress-test-repeaters.php
 */

// Bootstrap WordPress
require_once '/var/www/html/wp-load.php';

$original_options = get_option('fabrika62_options', []);
$errors_list = [];
$pass = 0;

$long_text = str_repeat('Lorem ipsum dolor sit amet, consectetur adipiscing elit. ', 20);
$long_title = 'Ez egy nagyon-nagyon hosszu cim ami tobb sort is elfoglalhat a kepernyoen es teszteli a sortoresi viselkedest';

$repeater_configs = [
    'product_categories' => [
        'template' => ['image' => '', 'title' => 'Teszt kategoria', 'description' => 'Teszt leiras'],
        'long' => ['image' => '', 'title' => $long_title, 'description' => $long_text],
    ],
    'gallery_items' => [
        'template' => ['image' => 'https://picsum.photos/400/400', 'alt' => 'Teszt kep'],
        'long' => ['image' => 'https://picsum.photos/400/400', 'alt' => $long_title],
    ],
    'order_steps' => [
        'template' => ['title' => 'Teszt lepes', 'description' => 'Teszt lepes leiras'],
        'long' => ['title' => $long_title, 'description' => $long_text],
    ],
    'gift_ideas' => [
        'template' => ['icon' => 'gift', 'title' => 'Teszt otlet', 'description' => 'Teszt otlet leiras'],
        'long' => ['icon' => 'heart', 'title' => $long_title, 'description' => $long_text],
    ],
    'faq_items' => [
        'template' => ['question' => 'Teszt kerdes?', 'answer' => 'Teszt valasz.'],
        'long' => ['question' => $long_title . '?', 'answer' => $long_text],
    ],
];

$counts_to_test = [0, 1, 2, 3, 5, 10];

function render_frontpage(): string {
    // Clear any cached options
    wp_cache_delete('fabrika62_options', 'options');
    wp_cache_delete('alloptions', 'options');

    ob_start();
    // Suppress any output errors and capture them
    $old_error = error_reporting(E_ALL);
    $old_display = ini_get('display_errors');
    ini_set('display_errors', '1');

    try {
        // Set up minimal query for front page
        global $wp_query, $post;
        $front_page_id = (int) get_option('page_on_front', 0);
        if ($front_page_id > 0) {
            $wp_query = new WP_Query(['page_id' => $front_page_id]);
        } else {
            $wp_query = new WP_Query(['pagename' => '']);
            $wp_query->is_front_page = true;
            $wp_query->is_home = true;
        }

        include get_stylesheet_directory() . '/header.php';
        include get_stylesheet_directory() . '/front-page.php';
        include get_stylesheet_directory() . '/footer.php';
    } catch (\Throwable $e) {
        echo '<!-- PHP EXCEPTION: ' . $e->getMessage() . ' -->';
    }

    ini_set('display_errors', $old_display);
    error_reporting($old_error);

    return ob_get_clean();
}

function check_render(string $html): array {
    $issues = [];

    // Check for PHP errors/warnings/notices
    if (preg_match('/(Fatal error|Warning|Notice|Parse error|Deprecated).*on line \d+/i', $html, $m)) {
        $issues[] = 'PHP error: ' . substr($m[0], 0, 200);
    }
    if (strpos($html, 'PHP EXCEPTION:') !== false) {
        preg_match('/PHP EXCEPTION: (.+?)-->/', $html, $m);
        $issues[] = 'Exception: ' . ($m[1] ?? 'unknown');
    }

    // Check key sections exist
    foreach (['hero', 'termekek', 'galeria', 'rendeles', 'kapcsolat'] as $id) {
        if (strpos($html, 'id="' . $id . '"') === false) {
            $issues[] = "Missing section: #$id";
        }
    }

    // Check closing tags
    if (strpos($html, '</html>') === false) {
        $issues[] = 'Missing </html>';
    }

    if (count($issues) > 0) {
        return ['ok' => false, 'error' => implode('; ', $issues)];
    }
    return ['ok' => true, 'size' => strlen($html)];
}

echo "=== Repeater Stress Test ===\n\n";

foreach ($repeater_configs as $repeater_name => $config) {
    echo "--- $repeater_name ---\n";

    foreach ($counts_to_test as $count) {
        $test_options = $original_options;

        if ($count === 0) {
            $test_options[$repeater_name] = [];
        } else {
            $items = [];
            for ($i = 0; $i < $count; $i++) {
                $item = $config['template'];
                $title_key = array_key_exists('title', $item) ? 'title' : (array_key_exists('question', $item) ? 'question' : null);
                if ($title_key) {
                    $item[$title_key] .= ' #' . ($i + 1);
                }
                $items[] = $item;
            }
            $test_options[$repeater_name] = $items;
        }

        update_option('fabrika62_options', $test_options, false);
        $html = render_frontpage();
        $result = check_render($html);

        if ($result['ok']) {
            echo "  [PASS] $count elements (HTML: {$result['size']} bytes)\n";
            $pass++;
        } else {
            echo "  [FAIL] $count elements: {$result['error']}\n";
            $errors_list[] = "$repeater_name/$count: {$result['error']}";
        }
    }

    // Long text test
    $test_options = $original_options;
    $test_options[$repeater_name] = [$config['long'], $config['long'], $config['long']];
    update_option('fabrika62_options', $test_options, false);
    $html = render_frontpage();
    $result = check_render($html);

    if ($result['ok']) {
        echo "  [PASS] 3x long text (HTML: {$result['size']} bytes)\n";
        $pass++;
    } else {
        echo "  [FAIL] 3x long text: {$result['error']}\n";
        $errors_list[] = "$repeater_name/long: {$result['error']}";
    }
}

// Restore
update_option('fabrika62_options', $original_options, false);
echo "\n--- Original options restored ---\n";

echo "\n=== SUMMARY ===\n";
echo "Passed: $pass\n";
echo "Failed: " . count($errors_list) . "\n";

if (count($errors_list) > 0) {
    echo "\nFailures:\n";
    foreach ($errors_list as $e) {
        echo "  - $e\n";
    }
    exit(1);
}

echo "\nAll tests passed!\n";
