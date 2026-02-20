<?php

declare(strict_types=1);

function fabrika62_termek_add_page_url(array $args = []): string
{
    return add_query_arg(array_merge([
        'post_type' => FABRIKA62_TERMEK_POST_TYPE,
        'page' => 'fabrika62-termek-add',
    ], $args), admin_url('edit.php'));
}

function fabrika62_termek_edit_page_url(int $post_id, array $args = []): string
{
    return fabrika62_termek_add_page_url(array_merge(['edit' => $post_id], $args));
}

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

function fabrika62_termek_hide_default_add_new_menu(): void
{
    remove_submenu_page(
        'edit.php?post_type=' . FABRIKA62_TERMEK_POST_TYPE,
        'post-new.php?post_type=' . FABRIKA62_TERMEK_POST_TYPE
    );
}
add_action('admin_menu', 'fabrika62_termek_hide_default_add_new_menu', 99);

function fabrika62_termek_redirect_default_editors(): void
{
    if (!is_admin()) {
        return;
    }

    global $pagenow;
    if (!is_string($pagenow)) {
        return;
    }

    if ($pagenow === 'post-new.php') {
        $post_type = isset($_GET['post_type']) && is_string($_GET['post_type']) ? sanitize_key($_GET['post_type']) : 'post';
        if ($post_type === FABRIKA62_TERMEK_POST_TYPE) {
            wp_safe_redirect(fabrika62_termek_add_page_url());
            exit;
        }
    }

    if ($pagenow === 'post.php') {
        $action = isset($_GET['action']) && is_string($_GET['action']) ? sanitize_key($_GET['action']) : '';
        $post_id = isset($_GET['post']) ? (int) $_GET['post'] : 0;
        if ($action !== 'edit' || $post_id <= 0) {
            return;
        }
        $post = get_post($post_id);
        if ($post instanceof WP_Post && $post->post_type === FABRIKA62_TERMEK_POST_TYPE) {
            wp_safe_redirect(fabrika62_termek_edit_page_url($post_id));
            exit;
        }
    }
}
add_action('admin_init', 'fabrika62_termek_redirect_default_editors', 20);

function fabrika62_termek_hide_add_new_button(): void
{
    global $pagenow;
    if (!is_string($pagenow) || $pagenow !== 'edit.php') {
        return;
    }
    $post_type = isset($_GET['post_type']) && is_string($_GET['post_type']) ? sanitize_key($_GET['post_type']) : '';
    if ($post_type !== FABRIKA62_TERMEK_POST_TYPE) {
        return;
    }
    echo '<style>.page-title-action{display:none!important;}</style>';
}
add_action('admin_head', 'fabrika62_termek_hide_add_new_button');

function fabrika62_termek_remove_admin_bar_new_menu(WP_Admin_Bar $admin_bar): void
{
    $admin_bar->remove_node('new-' . FABRIKA62_TERMEK_POST_TYPE);
}
add_action('admin_bar_menu', 'fabrika62_termek_remove_admin_bar_new_menu', 999);

function fabrika62_termek_disable_quick_edit(array $actions, WP_Post $post): array
{
    if ($post->post_type !== FABRIKA62_TERMEK_POST_TYPE) {
        return $actions;
    }
    unset($actions['inline hide-if-no-js']);
    return $actions;
}
add_filter('post_row_actions', 'fabrika62_termek_disable_quick_edit', 10, 2);

function fabrika62_termek_clean_tags(mixed $raw_tags): array
{
    if (!is_array($raw_tags) || $raw_tags === []) {
        return [];
    }
    $clean = [];
    foreach ($raw_tags as $t) {
        if (!is_string($t)) {
            continue;
        }
        $slug = sanitize_title((string) $t);
        if ($slug !== '') {
            $clean[] = $slug;
        }
    }
    return array_values(array_unique($clean));
}

function fabrika62_render_termek_add_page(): void
{
    if (!current_user_can('publish_posts')) {
        wp_die(esc_html__('Nincs jogosultság.', 'fabrika-62'));
    }

    $edit_id = isset($_GET['edit']) ? (int) $_GET['edit'] : 0;
    $edit_post = $edit_id > 0 ? get_post($edit_id) : null;
    if ($edit_post instanceof WP_Post && $edit_post->post_type !== FABRIKA62_TERMEK_POST_TYPE) {
        $edit_post = null;
        $edit_id = 0;
    }
    if (!$edit_post instanceof WP_Post) {
        $edit_id = 0;
    }
    $is_edit = $edit_post instanceof WP_Post;

    $created = isset($_GET['created']) && is_string($_GET['created']) ? (int) $_GET['created'] : 0;
    $updated = isset($_GET['updated']) && is_string($_GET['updated']) ? (int) $_GET['updated'] : 0;
    $error = isset($_GET['error']) && is_string($_GET['error']) ? sanitize_text_field((string) $_GET['error']) : '';

    $tags = get_terms([
        'taxonomy' => FABRIKA62_TERMEK_TAX,
        'hide_empty' => false,
        'orderby' => 'name',
        'order' => 'ASC',
    ]);

    $selected_tags = [];
    if ($is_edit) {
        $current_tags = wp_get_object_terms($edit_id, FABRIKA62_TERMEK_TAX, ['fields' => 'slugs']);
        if (is_array($current_tags)) {
            foreach ($current_tags as $slug) {
                if (is_string($slug)) {
                    $selected_tags[] = $slug;
                }
            }
        }
    }

    $title_value = $is_edit ? (string) $edit_post->post_title : '';
    $desc_value = $is_edit ? (string) $edit_post->post_content : '';
    $price_value = '';
    if ($is_edit) {
        $stored_price = get_post_meta($edit_id, 'fabrika62_termek_ar', true);
        $price_value = is_string($stored_price) ? trim($stored_price) : '';
    }
    $thumb_id = $is_edit ? (int) get_post_thumbnail_id($edit_id) : 0;
    $thumb_url = $thumb_id > 0 ? wp_get_attachment_image_url($thumb_id, 'thumbnail') : false;

    ?>
    <div class="wrap">
      <h1><?php echo esc_html($is_edit ? __('Termék szerkesztése', 'fabrika-62') : __('Termék feltöltés', 'fabrika-62')); ?></h1>

      <?php if ($created > 0) : ?>
        <div class="notice notice-success">
          <p>
            <?php echo esc_html__('Termék létrehozva.', 'fabrika-62'); ?>
            <a href="<?php echo esc_url(fabrika62_termek_edit_page_url($created)); ?>"><?php echo esc_html__('Szerkesztés', 'fabrika-62'); ?></a>
            |
            <a href="<?php echo esc_url(get_permalink($created)); ?>" target="_blank" rel="noopener"><?php echo esc_html__('Megtekintés', 'fabrika-62'); ?></a>
          </p>
        </div>
      <?php endif; ?>

      <?php if ($updated > 0) : ?>
        <div class="notice notice-success"><p><?php echo esc_html__('Termék frissítve.', 'fabrika-62'); ?></p></div>
      <?php endif; ?>

      <?php if ($error !== '') : ?>
        <div class="notice notice-error"><p><?php echo esc_html($error); ?></p></div>
      <?php endif; ?>

      <p>
        <?php
        echo esc_html(
            $is_edit
                ? __('A módosításokat itt tudod elvégezni egyetlen űrlapon. A termék kód automatikus (termék ID / szám).', 'fabrika-62')
                : __('Töltsd ki a mezőket, tölts fel 1 képet, és mentsd. A termék kód automatikusan generálódik (a termék ID / szám).', 'fabrika-62')
        );
        ?>
      </p>

      <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" enctype="multipart/form-data">
        <?php if ($is_edit) : ?>
          <?php wp_nonce_field('fabrika62_termek_update_' . $edit_id, 'fabrika62_termek_update_nonce'); ?>
          <input type="hidden" name="action" value="fabrika62_termek_update" />
          <input type="hidden" name="termek_id" value="<?php echo esc_attr((string) $edit_id); ?>" />
        <?php else : ?>
          <?php wp_nonce_field('fabrika62_termek_create', 'fabrika62_termek_create_nonce'); ?>
          <input type="hidden" name="action" value="fabrika62_termek_create" />
        <?php endif; ?>

        <table class="form-table" role="presentation">
          <tbody>
            <tr>
              <th scope="row"><label for="termek_title"><?php echo esc_html__('Név', 'fabrika-62'); ?> *</label></th>
              <td><input id="termek_title" name="termek_title" type="text" class="regular-text" required value="<?php echo esc_attr($title_value); ?>" /></td>
            </tr>
            <tr>
              <th scope="row"><label for="termek_description"><?php echo esc_html__('Leírás', 'fabrika-62'); ?></label></th>
              <td><textarea id="termek_description" name="termek_description" rows="6" class="large-text"><?php echo esc_textarea($desc_value); ?></textarea></td>
            </tr>
            <tr>
              <th scope="row"><label for="termek_price"><?php echo esc_html__('Ár (Ft)', 'fabrika-62'); ?></label></th>
              <td>
                <input id="termek_price" name="termek_price" type="number" inputmode="numeric" min="0" step="1" class="small-text" placeholder="3500" value="<?php echo esc_attr($price_value); ?>" />
                <p class="description"><?php echo esc_html__('Csak szám (Ft). Üresen hagyható.', 'fabrika-62'); ?></p>
              </td>
            </tr>
            <tr>
              <th scope="row"><label for="termek_image"><?php echo esc_html__('Kép', 'fabrika-62'); ?><?php echo $is_edit ? '' : ' *'; ?></label></th>
              <td>
                <input id="termek_image" name="termek_image" type="file" accept="image/*" <?php echo $is_edit ? '' : 'required'; ?> />
                <?php if ($is_edit && is_string($thumb_url) && $thumb_url !== '') : ?>
                  <p style="margin:10px 0 6px;">
                    <img src="<?php echo esc_url($thumb_url); ?>" alt="" style="max-width:100px;height:auto;border:1px solid #dcdcde;border-radius:4px;" />
                  </p>
                <?php endif; ?>
                <p class="description">
                  <?php
                  echo esc_html(
                      $is_edit
                          ? __('A kép cseréje opcionális. Ha üresen hagyod, a jelenlegi kép marad.', 'fabrika-62')
                          : __('Ajánlott: 1200×1200 (1:1) JPEG/WebP.', 'fabrika-62')
                  );
                  ?>
                </p>
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
                          $slug = (string) $t->slug;
                          $checked = in_array($slug, $selected_tags, true);
                          ?>
                        <label style="display:inline-flex;align-items:center;gap:6px;padding:6px 10px;border:1px solid #dcdcde;border-radius:999px;background:#fff;">
                          <input type="checkbox" name="termek_tags[]" value="<?php echo esc_attr($slug); ?>" <?php checked($checked); ?> />
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

        <?php submit_button($is_edit ? __('Termék frissítése', 'fabrika-62') : __('Termék mentése (publikus)', 'fabrika-62')); ?>
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
        wp_safe_redirect(fabrika62_termek_add_page_url(['error' => 'Hiányzó név.']));
        exit;
    }
    if (!isset($_FILES['termek_image']) || !is_array($_FILES['termek_image']) || (int) ($_FILES['termek_image']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        wp_safe_redirect(fabrika62_termek_add_page_url(['error' => 'Hiányzó vagy hibás kép feltöltés.']));
        exit;
    }

    $post_id = wp_insert_post([
        'post_type' => FABRIKA62_TERMEK_POST_TYPE,
        'post_status' => 'publish',
        'post_title' => sanitize_text_field($title),
        'post_content' => wp_kses_post($desc),
    ], true);

    if (is_wp_error($post_id)) {
        wp_safe_redirect(fabrika62_termek_add_page_url(['error' => 'Nem sikerült létrehozni a terméket.']));
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
    $clean_tags = fabrika62_termek_clean_tags($_POST['termek_tags'] ?? []);
    if ($clean_tags !== []) {
        wp_set_object_terms($post_id, $clean_tags, FABRIKA62_TERMEK_TAX, false);
    }

    // Image upload -> featured image.
    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/image.php';
    require_once ABSPATH . 'wp-admin/includes/media.php';

    $attachment_id = media_handle_upload('termek_image', $post_id);
    if (!is_wp_error($attachment_id)) {
        set_post_thumbnail($post_id, (int) $attachment_id);
    }

    wp_safe_redirect(fabrika62_termek_add_page_url(['created' => (string) $post_id]));
    exit;
}
add_action('admin_post_fabrika62_termek_create', 'fabrika62_handle_termek_create');

function fabrika62_handle_termek_update(): void
{
    $post_id = isset($_POST['termek_id']) ? (int) $_POST['termek_id'] : 0;
    if ($post_id <= 0) {
        wp_die(esc_html__('Hiányzó termék azonosító.', 'fabrika-62'));
    }
    if (!current_user_can('edit_post', $post_id)) {
        wp_die(esc_html__('Nincs jogosultság.', 'fabrika-62'));
    }
    check_admin_referer('fabrika62_termek_update_' . $post_id, 'fabrika62_termek_update_nonce');

    $post = get_post($post_id);
    if (!($post instanceof WP_Post) || $post->post_type !== FABRIKA62_TERMEK_POST_TYPE) {
        wp_safe_redirect(fabrika62_termek_add_page_url(['error' => 'A termék nem található.']));
        exit;
    }

    $title = isset($_POST['termek_title']) && is_string($_POST['termek_title']) ? trim((string) $_POST['termek_title']) : '';
    $desc = isset($_POST['termek_description']) && is_string($_POST['termek_description']) ? trim((string) $_POST['termek_description']) : '';
    $price_raw = isset($_POST['termek_price']) && is_string($_POST['termek_price']) ? trim((string) $_POST['termek_price']) : '';

    if ($title === '') {
        wp_safe_redirect(fabrika62_termek_edit_page_url($post_id, ['error' => 'Hiányzó név.']));
        exit;
    }

    $updated = wp_update_post([
        'ID' => $post_id,
        'post_title' => sanitize_text_field($title),
        'post_content' => wp_kses_post($desc),
    ], true);
    if (is_wp_error($updated)) {
        wp_safe_redirect(fabrika62_termek_edit_page_url($post_id, ['error' => 'Nem sikerült frissíteni a terméket.']));
        exit;
    }

    if ($price_raw === '') {
        delete_post_meta($post_id, 'fabrika62_termek_ar');
    } else {
        $digits = preg_replace('/[^\d]/', '', $price_raw);
        $int_val = (int) $digits;
        if ($int_val > 0) {
            update_post_meta($post_id, 'fabrika62_termek_ar', (string) $int_val);
        } else {
            delete_post_meta($post_id, 'fabrika62_termek_ar');
        }
    }

    $clean_tags = fabrika62_termek_clean_tags($_POST['termek_tags'] ?? []);
    wp_set_object_terms($post_id, $clean_tags, FABRIKA62_TERMEK_TAX, false);

    $file_error = isset($_FILES['termek_image']['error']) ? (int) $_FILES['termek_image']['error'] : UPLOAD_ERR_NO_FILE;
    if ($file_error === UPLOAD_ERR_OK) {
        if (!current_user_can('upload_files')) {
            wp_die(esc_html__('Nincs jogosultság.', 'fabrika-62'));
        }
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/image.php';
        require_once ABSPATH . 'wp-admin/includes/media.php';
        $attachment_id = media_handle_upload('termek_image', $post_id);
        if (!is_wp_error($attachment_id)) {
            set_post_thumbnail($post_id, (int) $attachment_id);
        }
    } elseif ($file_error !== UPLOAD_ERR_NO_FILE) {
        wp_safe_redirect(fabrika62_termek_edit_page_url($post_id, ['error' => 'Hibás kép feltöltés.']));
        exit;
    }

    wp_safe_redirect(fabrika62_termek_edit_page_url($post_id, ['updated' => (string) $post_id]));
    exit;
}
add_action('admin_post_fabrika62_termek_update', 'fabrika62_handle_termek_update');
