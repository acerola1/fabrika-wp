<?php

declare(strict_types=1);

function fabrika62_termek_add_submenu(): void
{
    add_submenu_page(
        'edit.php?post_type=' . FABRIKA62_TERMEK_POST_TYPE,
        __('Termék feltöltés (egylépéses)', 'fabrika-62'),
        __('Termék feltöltés', 'fabrika-62'),
        'publish_posts',
        'fabrika62-termek-add',
        'fabrika62_render_termek_add_page',
        0
    );
}
add_action('admin_menu', 'fabrika62_termek_add_submenu');

function fabrika62_render_termek_add_page(): void
{
    if (!current_user_can('publish_posts')) {
        wp_die(esc_html__('Nincs jogosultság.', 'fabrika-62'));
    }

    $created = isset($_GET['created']) && is_string($_GET['created']) ? (int) $_GET['created'] : 0;
    $error = isset($_GET['error']) && is_string($_GET['error']) ? sanitize_text_field((string) $_GET['error']) : '';

    $tags = get_terms([
        'taxonomy' => FABRIKA62_TERMEK_TAX,
        'hide_empty' => false,
        'orderby' => 'name',
        'order' => 'ASC',
    ]);

    ?>
    <div class="wrap">
      <h1><?php echo esc_html__('Termék feltöltés', 'fabrika-62'); ?></h1>

      <?php if ($created > 0) : ?>
        <div class="notice notice-success">
          <p>
            <?php echo esc_html__('Termék létrehozva.', 'fabrika-62'); ?>
            <a href="<?php echo esc_url(get_edit_post_link($created)); ?>"><?php echo esc_html__('Szerkesztés', 'fabrika-62'); ?></a>
            |
            <a href="<?php echo esc_url(get_permalink($created)); ?>" target="_blank" rel="noopener"><?php echo esc_html__('Megtekintés', 'fabrika-62'); ?></a>
          </p>
        </div>
      <?php endif; ?>

      <?php if ($error !== '') : ?>
        <div class="notice notice-error"><p><?php echo esc_html($error); ?></p></div>
      <?php endif; ?>

      <p><?php echo esc_html__('Töltsd ki a mezőket, tölts fel 1 képet, és mentsd. A termék kód automatikusan generálódik (a termék ID / szám).', 'fabrika-62'); ?></p>

      <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" enctype="multipart/form-data">
        <?php wp_nonce_field('fabrika62_termek_create', 'fabrika62_termek_create_nonce'); ?>
        <input type="hidden" name="action" value="fabrika62_termek_create" />

        <table class="form-table" role="presentation">
          <tbody>
            <tr>
              <th scope="row"><label for="termek_title"><?php echo esc_html__('Név', 'fabrika-62'); ?> *</label></th>
              <td><input id="termek_title" name="termek_title" type="text" class="regular-text" required /></td>
            </tr>
            <tr>
              <th scope="row"><label for="termek_description"><?php echo esc_html__('Leírás', 'fabrika-62'); ?></label></th>
              <td><textarea id="termek_description" name="termek_description" rows="6" class="large-text"></textarea></td>
            </tr>
            <tr>
              <th scope="row"><label for="termek_price"><?php echo esc_html__('Ár (Ft)', 'fabrika-62'); ?></label></th>
              <td>
                <input id="termek_price" name="termek_price" type="number" inputmode="numeric" min="0" step="1" class="small-text" placeholder="3500" />
                <p class="description"><?php echo esc_html__('Csak szám (Ft). Üresen hagyható.', 'fabrika-62'); ?></p>
              </td>
            </tr>
            <tr>
              <th scope="row"><label for="termek_image"><?php echo esc_html__('Kép', 'fabrika-62'); ?> *</label></th>
              <td>
                <input id="termek_image" name="termek_image" type="file" accept="image/*" required />
                <p class="description"><?php echo esc_html__('Ajánlott: 1200×1200 (1:1) JPEG/WebP.', 'fabrika-62'); ?></p>
              </td>
            </tr>
            <tr>
              <th scope="row"><?php echo esc_html__('Címkék', 'fabrika-62'); ?></th>
              <td>
                <?php if (is_array($tags) && $tags !== []) : ?>
                  <fieldset style="max-width: 820px;">
                    <legend class="screen-reader-text"><?php echo esc_html__('Címkék', 'fabrika-62'); ?></legend>
                    <div style="display:flex;flex-wrap:wrap;gap:10px;align-items:flex-start;">
                      <?php foreach ($tags as $t) :
                          if (!($t instanceof WP_Term)) continue;
                          $id = (int) $t->term_id;
                          $slug = (string) $t->slug;
                          ?>
                        <label style="display:inline-flex;align-items:center;gap:6px;padding:6px 10px;border:1px solid #dcdcde;border-radius:999px;background:#fff;">
                          <input type="checkbox" name="termek_tags[]" value="<?php echo esc_attr($slug); ?>" />
                          <span><?php echo esc_html((string) $t->name); ?></span>
                        </label>
                      <?php endforeach; ?>
                    </div>
                  </fieldset>
                  <p class="description" style="margin-top:10px;">
                    <a href="<?php echo esc_url(admin_url('edit-tags.php?taxonomy=' . FABRIKA62_TERMEK_TAX . '&post_type=' . FABRIKA62_TERMEK_POST_TYPE)); ?>">
                      <?php echo esc_html__('Címkék szerkesztése külön', 'fabrika-62'); ?>
                    </a>
                  </p>
                <?php else : ?>
                  <p class="description"><?php echo esc_html__('Nincsenek címkék. Először vegyél fel legalább egy címkét.', 'fabrika-62'); ?></p>
                <?php endif; ?>
              </td>
            </tr>
          </tbody>
        </table>

        <?php submit_button(__('Termék mentése (publikus)', 'fabrika-62')); ?>
      </form>
    </div>
    <?php
}

function fabrika62_handle_termek_create(): void
{
    if (!current_user_can('publish_posts')) {
        wp_die(esc_html__('Nincs jogosultság.', 'fabrika-62'));
    }
    if (!current_user_can('upload_files')) {
        wp_die(esc_html__('Nincs jogosultság.', 'fabrika-62'));
    }
    check_admin_referer('fabrika62_termek_create', 'fabrika62_termek_create_nonce');

    $title = isset($_POST['termek_title']) && is_string($_POST['termek_title']) ? trim((string) $_POST['termek_title']) : '';
    $desc = isset($_POST['termek_description']) && is_string($_POST['termek_description']) ? trim((string) $_POST['termek_description']) : '';
    $price_raw = isset($_POST['termek_price']) && is_string($_POST['termek_price']) ? trim((string) $_POST['termek_price']) : '';

    if ($title === '') {
        wp_safe_redirect(add_query_arg(['post_type' => FABRIKA62_TERMEK_POST_TYPE, 'page' => 'fabrika62-termek-add', 'error' => 'Hiányzó név.'], admin_url('edit.php')));
        exit;
    }
    if (!isset($_FILES['termek_image']) || !is_array($_FILES['termek_image']) || (int) ($_FILES['termek_image']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        wp_safe_redirect(add_query_arg(['post_type' => FABRIKA62_TERMEK_POST_TYPE, 'page' => 'fabrika62-termek-add', 'error' => 'Hiányzó vagy hibás kép feltöltés.'], admin_url('edit.php')));
        exit;
    }

    $post_id = wp_insert_post([
        'post_type' => FABRIKA62_TERMEK_POST_TYPE,
        'post_status' => 'publish',
        'post_title' => sanitize_text_field($title),
        'post_content' => wp_kses_post($desc),
    ], true);

    if (is_wp_error($post_id)) {
        wp_safe_redirect(add_query_arg(['post_type' => FABRIKA62_TERMEK_POST_TYPE, 'page' => 'fabrika62-termek-add', 'error' => 'Nem sikerült létrehozni a terméket.'], admin_url('edit.php')));
        exit;
    }
    $post_id = (int) $post_id;

    // Price meta.
    if ($price_raw !== '') {
        $digits = preg_replace('/[^\d]/', '', $price_raw);
        $intVal = (int) $digits;
        if ($intVal > 0) {
            update_post_meta($post_id, 'fabrika62_termek_ar', (string) $intVal);
        }
    }

    // Auto ID/code: use the post ID (unique, simple).
    update_post_meta($post_id, 'fabrika62_termek_kod', (string) $post_id);

    // Tags.
    $tags = $_POST['termek_tags'] ?? [];
    if (is_array($tags) && $tags !== []) {
        $clean = [];
        foreach ($tags as $t) {
            if (!is_string($t)) continue;
            $slug = sanitize_title((string) $t);
            if ($slug !== '') $clean[] = $slug;
        }
        if ($clean !== []) {
            wp_set_object_terms($post_id, $clean, FABRIKA62_TERMEK_TAX, false);
        }
    }

    // Image upload -> featured image.
    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/image.php';
    require_once ABSPATH . 'wp-admin/includes/media.php';

    $attachment_id = media_handle_upload('termek_image', $post_id);
    if (!is_wp_error($attachment_id)) {
        set_post_thumbnail($post_id, (int) $attachment_id);
    }

    wp_safe_redirect(add_query_arg(['post_type' => FABRIKA62_TERMEK_POST_TYPE, 'page' => 'fabrika62-termek-add', 'created' => (string) $post_id], admin_url('edit.php')));
    exit;
}
add_action('admin_post_fabrika62_termek_create', 'fabrika62_handle_termek_create');
