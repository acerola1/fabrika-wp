<?php

declare(strict_types=1);

function fabrika62_admin_menu(): void
{
    add_menu_page(
        'Fabrika Kezdolap',
        'Fabrika Kezdolap',
        'manage_options',
        'fabrika62-home',
        'fabrika62_render_admin_page',
        'dashicons-admin-customizer',
        61
    );
}
add_action('admin_menu', 'fabrika62_admin_menu');

function fabrika62_admin_assets(string $hook): void
{
    if ($hook !== 'toplevel_page_fabrika62-home') {
        return;
    }
    wp_enqueue_media();
}
add_action('admin_enqueue_scripts', 'fabrika62_admin_assets');

function fabrika62_render_repeater(string $name, array $rows, array $columns, string $add_label): void
{
    echo '<div class="fabrika62-repeater" data-repeater-name="' . esc_attr($name) . '">';
    echo '<div class="fabrika62-repeater-rows">';
    foreach ($rows as $index => $row) {
        fabrika62_render_repeater_row($name, (int) $index, is_array($row) ? $row : [], $columns);
    }
    echo '</div>';
    echo '<p><button type="button" class="button fabrika62-add-row">' . esc_html($add_label) . '</button></p>';
    echo '<template class="fabrika62-row-template">';
    fabrika62_render_repeater_row($name, 999999, [], $columns, true);
    echo '</template>';
    echo '</div>';
}

function fabrika62_render_repeater_row(string $name, int $index, array $row, array $columns, bool $is_template = false): void
{
    $row_class = 'fabrika62-row';
    if ($is_template) {
        $row_class .= ' fabrika62-row-template';
    }
    echo '<div class="' . esc_attr($row_class) . '" style="border:1px solid #dcdcde;padding:12px;margin-bottom:10px;background:#fff;">';
    echo '<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:10px;">';
    foreach ($columns as $col) {
        $key = $col['key'];
        $type = $col['type'];
        $label = $col['label'];
        $value = isset($row[$key]) && is_string($row[$key]) ? $row[$key] : '';
        $safe_index = $is_template ? '__INDEX__' : (string) $index;
        $input_name = 'fabrika62_options[' . $name . '][' . $safe_index . '][' . $key . ']';

        echo '<label style="display:block;">';
        echo '<strong>' . esc_html($label) . '</strong><br/>';
        if ($type === 'textarea') {
            echo '<textarea name="' . esc_attr($input_name) . '" rows="3" style="width:100%;">' . esc_textarea($value) . '</textarea>';
        } elseif ($type === 'select') {
            echo '<select name="' . esc_attr($input_name) . '" style="width:100%;">';
            foreach (($col['choices'] ?? []) as $choice_val => $choice_label) {
                $selected = selected($value, (string) $choice_val, false);
                echo '<option value="' . esc_attr((string) $choice_val) . '"' . $selected . '>' . esc_html((string) $choice_label) . '</option>';
            }
            echo '</select>';
        } else {
            echo '<input type="text" name="' . esc_attr($input_name) . '" value="' . esc_attr($value) . '" style="width:100%;" />';
            if ($type === 'image') {
                echo '<button type="button" class="button fabrika62-media-pick" style="margin-top:6px;">Kep valasztasa</button>';
            }
        }
        echo '</label>';
    }
    echo '</div>';
    echo '<p style="margin-top:10px;"><button type="button" class="button-link-delete fabrika62-remove-row">Sor torlese</button></p>';
    echo '</div>';
}

function fabrika62_render_text_field(string $key, string $label, array $options, string $type = 'text'): void
{
    $value = isset($options[$key]) && is_string($options[$key]) ? $options[$key] : '';
    echo '<tr>';
    echo '<th scope="row"><label for="' . esc_attr($key) . '">' . esc_html($label) . '</label></th>';
    echo '<td>';
    if ($type === 'textarea') {
        echo '<textarea id="' . esc_attr($key) . '" name="fabrika62_options[' . esc_attr($key) . ']" rows="3" class="large-text">' . esc_textarea($value) . '</textarea>';
    } else {
        echo '<input id="' . esc_attr($key) . '" type="text" name="fabrika62_options[' . esc_attr($key) . ']" value="' . esc_attr($value) . '" class="regular-text" />';
    }
    echo '</td>';
    echo '</tr>';
}

function fabrika62_render_admin_page(): void
{
    if (!current_user_can('manage_options')) {
        return;
    }
    $options = fabrika62_get_options();
    $updated = isset($_GET['updated']) && $_GET['updated'] === '1';
    ?>
    <div class="wrap">
      <h1>Fabrika Kezdolap</h1>
      <?php if ($updated) : ?>
        <div class="notice notice-success is-dismissible"><p>Beallitasok mentve.</p></div>
      <?php endif; ?>
      <p>Itt tudod szerkeszteni a kezdooldal teljes statikus tartalmat egy helyen, plugin nelkul.</p>

      <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
        <?php wp_nonce_field('fabrika62_save_options'); ?>
        <input type="hidden" name="action" value="fabrika62_save_options" />

        <h2>Altalanos</h2>
        <table class="form-table">
          <?php
          fabrika62_render_text_field('meta_description', 'Meta description', $options, 'textarea');
          fabrika62_render_text_field('brand_name', 'Brand nev', $options);
          ?>
        </table>

        <h2>Hero</h2>
        <table class="form-table">
          <?php
          fabrika62_render_text_field('hero_badge', 'Badge szoveg (HTML engedett)', $options);
          fabrika62_render_text_field('hero_title', 'Hero cim', $options);
          fabrika62_render_text_field('hero_subtitle', 'Hero alcim (HTML engedett)', $options, 'textarea');
          fabrika62_render_text_field('hero_cta_label', 'CTA felirat', $options);
          fabrika62_render_text_field('hero_cta_href', 'CTA link', $options);
          ?>
        </table>

        <h2>Termekkategoriak</h2>
        <table class="form-table">
          <?php fabrika62_render_text_field('products_title', 'Szekcio cim', $options); ?>
        </table>
        <?php
        fabrika62_render_repeater(
            'product_categories',
            is_array($options['product_categories'] ?? null) ? $options['product_categories'] : [],
            [
                ['key' => 'image', 'label' => 'Kep URL vagy fajlnev', 'type' => 'image'],
                ['key' => 'title', 'label' => 'Cim', 'type' => 'text'],
                ['key' => 'description', 'label' => 'Leiras', 'type' => 'textarea'],
            ],
            'Uj kartya'
        );
        ?>

        <h2>Galeria</h2>
        <table class="form-table">
          <?php fabrika62_render_text_field('gallery_title', 'Szekcio cim', $options); ?>
        </table>
        <?php
        fabrika62_render_repeater(
            'gallery_items',
            is_array($options['gallery_items'] ?? null) ? $options['gallery_items'] : [],
            [
                ['key' => 'image', 'label' => 'Kep URL vagy fajlnev', 'type' => 'image'],
                ['key' => 'alt', 'label' => 'Alt szoveg', 'type' => 'text'],
            ],
            'Uj kep'
        );
        ?>

        <h2>Rendeles lepesei</h2>
        <table class="form-table">
          <?php fabrika62_render_text_field('order_title', 'Szekcio cim', $options); ?>
        </table>
        <?php
        fabrika62_render_repeater(
            'order_steps',
            is_array($options['order_steps'] ?? null) ? $options['order_steps'] : [],
            [
                ['key' => 'title', 'label' => 'Cim', 'type' => 'text'],
                ['key' => 'description', 'label' => 'Leiras', 'type' => 'textarea'],
            ],
            'Uj lepes'
        );
        ?>

        <h2>Ajandekotletek</h2>
        <table class="form-table">
          <?php fabrika62_render_text_field('gifts_title', 'Szekcio cim', $options); ?>
        </table>
        <?php
        fabrika62_render_repeater(
            'gift_ideas',
            is_array($options['gift_ideas'] ?? null) ? $options['gift_ideas'] : [],
            [
                ['key' => 'icon', 'label' => 'Ikon', 'type' => 'select', 'choices' => ['gift' => 'Ajandek', 'heart' => 'Sziv', 'user' => 'Ember', 'sparkles' => 'Csillag', 'smile' => 'Mosoly']],
                ['key' => 'title', 'label' => 'Cim', 'type' => 'text'],
                ['key' => 'description', 'label' => 'Leiras', 'type' => 'textarea'],
            ],
            'Uj otlet'
        );
        ?>

        <h2>Piaci megjelenes</h2>
        <table class="form-table">
          <?php
          fabrika62_render_text_field('market_title', 'Szekcio cim', $options);
          fabrika62_render_text_field('market_lead', 'Fo szoveg', $options);
          fabrika62_render_text_field('market_sub', 'Masodlagos szoveg', $options);
          ?>
        </table>

        <h2>Kapcsolat</h2>
        <table class="form-table">
          <?php
          fabrika62_render_text_field('contact_title', 'Szekcio cim', $options);
          fabrika62_render_text_field('contact_form_shortcode', 'Urlap shortcode (opcionalis)', $options, 'textarea');
          fabrika62_render_text_field('contact_email', 'Email', $options);
          fabrika62_render_text_field('contact_facebook_label', 'Facebook felirat', $options);
          fabrika62_render_text_field('contact_facebook_url', 'Facebook URL', $options);
          fabrika62_render_text_field('contact_viber', 'Viber/Telefon', $options);
          fabrika62_render_text_field('contact_instagram', 'Instagram felirat', $options);
          fabrika62_render_text_field('contact_instagram_url', 'Instagram URL', $options);
          ?>
        </table>

        <h2>GYIK</h2>
        <table class="form-table">
          <?php fabrika62_render_text_field('faq_title', 'Szekcio cim', $options); ?>
        </table>
        <?php
        fabrika62_render_repeater(
            'faq_items',
            is_array($options['faq_items'] ?? null) ? $options['faq_items'] : [],
            [
                ['key' => 'question', 'label' => 'Kerdes', 'type' => 'text'],
                ['key' => 'answer', 'label' => 'Valasz', 'type' => 'textarea'],
            ],
            'Uj kerdes'
        );
        ?>

        <h2>Footer</h2>
        <table class="form-table">
          <?php
          fabrika62_render_text_field('footer_location', 'Helyszin', $options);
          fabrika62_render_text_field('footer_facebook_url', 'Footer Facebook URL', $options);
          fabrika62_render_text_field('footer_instagram_url', 'Footer Instagram URL', $options);
          ?>
        </table>

        <?php submit_button('Mentés'); ?>
      </form>
    </div>
    <script>
      (function() {
        function normalizeIndexes(repeater) {
          var rows = repeater.querySelectorAll('.fabrika62-row');
          rows.forEach(function(row, idx) {
            row.querySelectorAll('input,textarea,select').forEach(function(input) {
              input.name = input.name.replace(/\[\d+\](?=\[[^\]]+\]$)/, '[' + idx + ']');
            });
          });
        }

        document.querySelectorAll('.fabrika62-repeater').forEach(function(repeater) {
          repeater.addEventListener('click', function(event) {
            var addBtn = event.target.closest('.fabrika62-add-row');
            if (addBtn) {
              event.preventDefault();
              var tmpl = repeater.querySelector('.fabrika62-row-template');
              if (!tmpl) return;
              var rowsWrap = repeater.querySelector('.fabrika62-repeater-rows');
              var index = rowsWrap.querySelectorAll('.fabrika62-row').length;
              var html = tmpl.innerHTML.replaceAll('__INDEX__', String(index));
              rowsWrap.insertAdjacentHTML('beforeend', html);
              normalizeIndexes(repeater);
              return;
            }

            var removeBtn = event.target.closest('.fabrika62-remove-row');
            if (removeBtn) {
              event.preventDefault();
              var row = removeBtn.closest('.fabrika62-row');
              if (row) row.remove();
              normalizeIndexes(repeater);
              return;
            }

            var mediaBtn = event.target.closest('.fabrika62-media-pick');
            if (mediaBtn) {
              event.preventDefault();
              if (!window.wp || !wp.media) return;
              var field = mediaBtn.parentElement.querySelector('input[type="text"]');
              if (!field) return;
              var picker = wp.media({
                title: 'Valassz kepet',
                button: { text: 'Kep hasznalata' },
                library: { type: 'image' },
                multiple: false
              });
              picker.on('select', function() {
                var attachment = picker.state().get('selection').first().toJSON();
                field.value = attachment.url || '';
              });
              picker.open();
            }
          });
        });
      })();
    </script>
    <?php
}

function fabrika62_sanitize_repeater(array $rows, array $shape): array
{
    $clean = [];
    foreach ($rows as $row) {
        if (!is_array($row)) {
            continue;
        }
        $item = [];
        foreach ($shape as $key => $rule) {
            $raw = isset($row[$key]) && is_string($row[$key]) ? trim($row[$key]) : '';
            if ($rule === 'url') {
                $item[$key] = esc_url_raw($raw);
            } elseif ($rule === 'textarea') {
                $item[$key] = sanitize_textarea_field($raw);
            } else {
                $item[$key] = sanitize_text_field($raw);
            }
        }
        $has_content = false;
        foreach ($item as $value) {
            if ($value !== '') {
                $has_content = true;
                break;
            }
        }
        if ($has_content) {
            $clean[] = $item;
        }
    }
    return $clean;
}

function fabrika62_save_options(): void
{
    if (!current_user_can('manage_options')) {
        wp_die('Nincs jogosultsag.');
    }
    check_admin_referer('fabrika62_save_options');

    $raw = $_POST['fabrika62_options'] ?? [];
    if (!is_array($raw)) {
        $raw = [];
    }

    $defaults = fabrika62_default_options();
    $clean = [];

    $text_fields = [
        'brand_name', 'hero_title', 'hero_cta_label', 'hero_cta_href',
        'products_title', 'gallery_title', 'order_title', 'gifts_title',
        'market_title', 'market_lead', 'market_sub', 'contact_title',
        'contact_email', 'contact_facebook_label', 'contact_facebook_url',
        'contact_viber', 'contact_instagram', 'contact_instagram_url',
        'faq_title', 'footer_location', 'footer_facebook_url', 'footer_instagram_url',
    ];
    foreach ($text_fields as $field) {
        $value = isset($raw[$field]) && is_string($raw[$field]) ? trim($raw[$field]) : '';
        $clean[$field] = sanitize_text_field($value);
    }

    $html_fields = ['hero_badge', 'hero_subtitle'];
    foreach ($html_fields as $field) {
        $value = isset($raw[$field]) && is_string($raw[$field]) ? trim($raw[$field]) : '';
        $clean[$field] = wp_kses_post($value);
    }

    $textarea_fields = ['meta_description', 'contact_form_shortcode'];
    foreach ($textarea_fields as $field) {
        $value = isset($raw[$field]) && is_string($raw[$field]) ? trim($raw[$field]) : '';
        $clean[$field] = sanitize_textarea_field($value);
    }

    $clean['product_categories'] = fabrika62_sanitize_repeater(
        isset($raw['product_categories']) && is_array($raw['product_categories']) ? $raw['product_categories'] : [],
        ['image' => 'text', 'title' => 'text', 'description' => 'textarea']
    );
    $clean['gallery_items'] = fabrika62_sanitize_repeater(
        isset($raw['gallery_items']) && is_array($raw['gallery_items']) ? $raw['gallery_items'] : [],
        ['image' => 'text', 'alt' => 'text']
    );
    $clean['order_steps'] = fabrika62_sanitize_repeater(
        isset($raw['order_steps']) && is_array($raw['order_steps']) ? $raw['order_steps'] : [],
        ['title' => 'text', 'description' => 'textarea']
    );
    $clean['gift_ideas'] = fabrika62_sanitize_repeater(
        isset($raw['gift_ideas']) && is_array($raw['gift_ideas']) ? $raw['gift_ideas'] : [],
        ['icon' => 'text', 'title' => 'text', 'description' => 'textarea']
    );
    $clean['faq_items'] = fabrika62_sanitize_repeater(
        isset($raw['faq_items']) && is_array($raw['faq_items']) ? $raw['faq_items'] : [],
        ['question' => 'text', 'answer' => 'textarea']
    );

    foreach ($clean as $key => $value) {
        if ($value === '' || $value === []) {
            if (array_key_exists($key, $defaults)) {
                $clean[$key] = $defaults[$key];
            }
        }
    }

    update_option('fabrika62_options', $clean, false);

    wp_safe_redirect(add_query_arg(['page' => 'fabrika62-home', 'updated' => '1'], admin_url('admin.php')));
    exit;
}
add_action('admin_post_fabrika62_save_options', 'fabrika62_save_options');

