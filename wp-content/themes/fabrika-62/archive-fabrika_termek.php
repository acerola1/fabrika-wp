<?php

declare(strict_types=1);

get_header();

$catalog_title = fabrika62_opt_str('catalog_title', 'Termékkatalógus');
$catalog_subtitle = fabrika62_opt_str('catalog_subtitle', 'Böngészd át kézműves termékeinket! Ha valami megtetszik, kattints az „Érdekel" gombra és írj nekünk.');
$catalog_filter_all_label = fabrika62_opt_str('catalog_filter_all_label', 'Összes');
$catalog_no_image_label = fabrika62_opt_str('catalog_no_image_label', 'Nincs kép');
$catalog_price_contact_label = fabrika62_opt_str('catalog_price_contact_label', 'Ár egyeztetéssel');
$catalog_interest_label = fabrika62_opt_str('catalog_interest_label', 'Érdekel');
$catalog_empty_filtered_label = fabrika62_opt_str('catalog_empty_filtered_label', 'Ebben a kategóriában még nincsenek termékek.');
$catalog_show_all_button_label = fabrika62_opt_str('catalog_show_all_button_label', 'Összes termék mutatása');
$catalog_empty_label = fabrika62_opt_str('catalog_empty_label', 'Még nincsenek termékek.');
$catalog_cta_title = fabrika62_opt_str('catalog_cta_title', 'Nem találtad, amit kerestél?');
$catalog_cta_text = fabrika62_opt_str('catalog_cta_text', 'Írj nekünk és készítünk bármit egyedire! Egyedi méretben, felirattal, saját képed alapján.');
$catalog_cta_button_label = fabrika62_opt_str('catalog_cta_button_label', 'Egyedi rendelés');

$cimke = '';
if (isset($_GET['cimke']) && is_string($_GET['cimke'])) {
    $cimke = sanitize_title((string) $_GET['cimke']);
} elseif (isset($_GET['kategoria']) && is_string($_GET['kategoria'])) {
    // Backward-compatible alias (mock used ?kategoria=...).
    $cimke = sanitize_title((string) $_GET['kategoria']);
} elseif (isset($_GET['tag']) && is_string($_GET['tag'])) {
    // Backward-compatible alias.
    $cimke = sanitize_title((string) $_GET['tag']);
}

$tax_query = [];
if ($cimke !== '') {
    $tax_query[] = [
        'taxonomy' => FABRIKA62_TERMEK_TAX,
        'field' => 'slug',
        'terms' => $cimke,
    ];
}

$paged = max(1, (int) get_query_var('paged', 1));
$q = new WP_Query([
    'post_type' => FABRIKA62_TERMEK_POST_TYPE,
    'post_status' => 'publish',
    'posts_per_page' => -1,
    // Match the 6-3 mock behavior: render all cards, filter client-side (JS) + update URL.
    // We still read `?kategoria=` to set the active filter button on first paint.
]);

$categories = get_terms([
    'taxonomy' => FABRIKA62_TERMEK_TAX,
    'hide_empty' => false,
]);

if (is_array($categories)) {
    $preferred = [
        'lezer-gravirozott-kepek',
        'fa-tablak',
        'kis-ajandektargyak',
        'kerti-diszek',
        'szines-nyomatok',
        'testreszabhato',
    ];
    $order = array_flip($preferred);
    usort($categories, static function ($a, $b) use ($order) {
        if (!($a instanceof WP_Term) || !($b instanceof WP_Term)) return 0;
        $ai = $order[$a->slug] ?? PHP_INT_MAX;
        $bi = $order[$b->slug] ?? PHP_INT_MAX;
        if ($ai === $bi) {
            return strcasecmp((string) $a->name, (string) $b->name);
        }
        return $ai <=> $bi;
    });

    // Accept legacy/non-canonical slugs from older admin data (e.g. missing dash variants)
    // and map them to the closest existing taxonomy slug.
    if ($cimke !== '') {
        $exists = term_exists($cimke, FABRIKA62_TERMEK_TAX);
        if (!$exists) {
            $needle = preg_replace('/[^a-z0-9]+/i', '', $cimke);
            $best_slug = '';
            $best_score = -1;
            foreach ($categories as $t) {
                if (!($t instanceof WP_Term)) {
                    continue;
                }
                $candidate = preg_replace('/[^a-z0-9]+/i', '', (string) $t->slug);
                if ($candidate === '' || $needle === '') {
                    continue;
                }
                if ($candidate === $needle) {
                    $best_slug = (string) $t->slug;
                    $best_score = 10_000;
                    break;
                }
                if (strpos($needle, $candidate) !== false || strpos($candidate, $needle) !== false) {
                    $score = strlen($candidate);
                    if ($score > $best_score) {
                        $best_score = $score;
                        $best_slug = (string) $t->slug;
                    }
                }
            }
            if ($best_slug !== '') {
                $cimke = $best_slug;
            }
        }
    }
}

function fabrika62_termek_filter_link(string $cimke): string
{
    $base = get_post_type_archive_link(FABRIKA62_TERMEK_POST_TYPE);
    if ($cimke === '') return $base;
    // Keep the same param name as the mock: ?kategoria=
    return add_query_arg(['kategoria' => $cimke], $base);
}

function fabrika62_hu_price(string $raw): string
{
    $raw = trim($raw);
    if ($raw === '') return '';
    $intVal = (int) preg_replace('/[^\d]/', '', $raw);
    if ($intVal <= 0) return '';
    return number_format_i18n($intVal, 0) . ' Ft';
}

?>

  <style>
    /* ===== STATIKUS LEÍRÁS A KÁRTYÁBAN (2.16d) ===== */
    .desc-short {
      font-size: 0.78rem;
      color: #5C3D2E;
      line-height: 1.4;
      margin: 0 0 0.5rem;
      display: -webkit-box;
      -webkit-line-clamp: 2;
      -webkit-box-orient: vertical;
      overflow: hidden;
    }

    /* ===== MODAL / CAROUSEL (2.16b, 2.16c) ===== */
    #product-modal {
      position: fixed;
      inset: 0;
      z-index: 200;
      align-items: center;
      justify-content: center;
      opacity: 0;
      transition: opacity 0.25s ease;
    }
    #product-modal.modal-open {
      opacity: 1;
    }
    .modal-backdrop {
      position: absolute;
      inset: 0;
      background: rgba(42, 32, 24, 0.85);
      backdrop-filter: blur(6px);
      cursor: pointer;
    }
    .modal-box {
      position: relative;
      background: #FFFBF5;
      border-radius: 12px;
      width: 95vw;
      max-width: 95vw;
      max-height: 92vh;
      overflow-y: auto;
      overflow-x: hidden;
      display: flex;
      flex-direction: column;
      transform: translateY(24px) scale(0.97);
      transition: transform 0.25s ease;
      border: 1px solid rgba(184, 115, 51, 0.2);
      box-shadow: 0 20px 60px rgba(42, 32, 24, 0.5);
    }
    #product-modal.modal-open .modal-box {
      transform: translateY(0) scale(1);
    }
    @media (min-width: 640px) {
      .modal-box { flex-direction: row; height: 85vh; max-height: 85vh; }
      .modal-image-wrap { width: 75%; flex-shrink: 0; }
      .modal-info { flex: 1; overflow-y: auto; }
    }
    .modal-image-wrap {
      position: relative;
      background: #2a2015;
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 55vw;
    }
    @media (min-width: 640px) {
      .modal-image-wrap { min-height: unset; }
    }
    .modal-image-wrap img {
      width: 100%;
      height: 100%;
      object-fit: contain;
      display: block;
    }
    .modal-info {
      padding: 1.5rem;
      display: flex;
      flex-direction: column;
      gap: 0.5rem;
    }
    .modal-product-name {
      font-family: 'Bitter', serif;
      font-size: 1.4rem;
      font-weight: 700;
      color: #3B2314;
      margin: 0;
    }
    .modal-price {
      font-size: 1.5rem;
      font-weight: 700;
      color: #B87333;
      margin: 0;
    }
    .modal-desc {
      font-size: 0.9rem;
      color: #5C3D2E;
      line-height: 1.6;
      flex: 1;
      margin: 0;
    }
    .modal-cta-btn {
      display: block;
      text-align: center;
      padding: 0.75rem 1.5rem;
      border-radius: 8px;
      font-weight: 600;
      color: #FFFBF5;
      background: linear-gradient(135deg, #B87333, #C9A84C);
      transition: transform 0.2s, box-shadow 0.2s;
      margin-top: 0.5rem;
      text-decoration: none;
    }
    .modal-cta-btn:hover {
      transform: scale(1.04);
      box-shadow: 0 4px 16px rgba(184, 115, 51, 0.4);
    }
    .modal-close-btn {
      position: absolute;
      top: 0.75rem;
      right: 0.75rem;
      z-index: 10;
      background: rgba(59, 35, 20, 0.85);
      color: #FFFBF5;
      border-radius: 50%;
      width: 36px;
      height: 36px;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      transition: background 0.2s;
      border: none;
    }
    .modal-close-btn:hover { background: #B87333; }
    .modal-nav-btn {
      position: absolute;
      top: 50%;
      transform: translateY(-50%);
      z-index: 10;
      background: linear-gradient(135deg, #B87333, #C9A84C);
      color: #FFFBF5;
      border-radius: 50%;
      width: 60px;
      height: 60px;
      font-size: 2.4rem;
      line-height: 1;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      transition: transform 0.15s, box-shadow 0.15s;
      border: 3px solid rgba(255,255,255,0.35);
      user-select: none;
      box-shadow: 0 4px 20px rgba(0,0,0,0.5);
    }
    .modal-nav-btn:hover { transform: translateY(-50%) scale(1.12); box-shadow: 0 6px 28px rgba(0,0,0,0.65); }
    .modal-prev-btn { left: 1rem; }
    .modal-next-btn { right: 1rem; }
    .modal-counter {
      position: absolute;
      bottom: 0.75rem;
      left: 50%;
      transform: translateX(-50%);
      font-size: 0.75rem;
      color: #E8DCC8;
      background: rgba(20, 14, 8, 0.65);
      padding: 3px 12px;
      border-radius: 999px;
      white-space: nowrap;
      pointer-events: none;
      backdrop-filter: blur(4px);
    }
    body.modal-body-lock { overflow: hidden; }
    .back-home-link {
      display: inline-flex;
      align-items: center;
      gap: 0.375rem;
      color: #C9A84C;
      font-size: 0.875rem;
      font-weight: 500;
      text-decoration: none;
      margin-bottom: 1.5rem;
      transition: color 0.2s ease;
    }
    .back-home-link:hover { color: #FFFBF5; }
    .back-home-link svg { transition: transform 0.2s ease; }
    .back-home-link:hover svg { transform: translateX(-4px); }
  </style>

  <section class="pt-28 pb-12 sm:pt-32 sm:pb-16" style="background-color: #3B2314;">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
      <a href="<?php echo esc_url(home_url('/')); ?>" class="back-home-link">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
        Vissza a főoldalra
      </a>
      <h1 class="text-3xl sm:text-4xl md:text-5xl font-bold text-[#FFFBF5] mb-4"><?php echo esc_html($catalog_title); ?></h1>
      <div class="copper-divider max-w-[200px] mx-auto mb-4"></div>
      <p class="text-[#E8DCC8] max-w-xl mx-auto"><?php echo esc_html($catalog_subtitle); ?></p>
    </div>
  </section>

  <section class="py-6 sticky top-16 z-40" style="background-color: #F4EDE4; border-bottom: 1px solid rgba(184, 115, 51, 0.15);">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
      <?php if (is_array($categories) && $categories !== []) : ?>
        <div class="flex flex-wrap gap-2 justify-center" id="filter-bar">
          <?php
          $allActive = ($cimke === '');
          ?>
          <button type="button" class="filter-btn <?php echo $allActive ? 'active' : ''; ?> px-4 py-2 rounded-full text-sm font-semibold border cursor-pointer" style="border-color: rgba(184, 115, 51, 0.3); color: #3B2314;<?php echo $allActive ? '' : ' background: #FFFBF5;'; ?>" data-filter="all">
            <?php echo esc_html($catalog_filter_all_label); ?>
          </button>
          <?php foreach ($categories as $t) :
              if (!($t instanceof WP_Term)) continue;
              $slug = (string) $t->slug;
              $active = ($cimke === $slug);
              ?>
            <button type="button" class="filter-btn <?php echo $active ? 'active' : ''; ?> px-4 py-2 rounded-full text-sm font-semibold border cursor-pointer" style="border-color: rgba(184, 115, 51, 0.3); color: #3B2314;<?php echo $active ? '' : ' background: #FFFBF5;'; ?>" data-filter="<?php echo esc_attr($slug); ?>">
              <?php echo esc_html((string) $t->name); ?>
            </button>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  </section>

  <section class="py-12 sm:py-16" style="background-color: #F4EDE4;">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6" id="product-grid">
          <?php
          if ($q->have_posts()) :
              while ($q->have_posts()) :
                  $q->the_post();
                  $post_id = (int) get_the_ID();
                  $price = get_post_meta($post_id, 'fabrika62_termek_ar', true);
                  $price_str = is_string($price) ? fabrika62_hu_price($price) : '';
                  $code = get_post_meta($post_id, 'fabrika62_termek_kod', true);
                  $code_str = is_string($code) ? trim((string) $code) : '';
                  if ($code_str === '') {
                      $code_str = (string) $post_id;
                  }
                  $cta = add_query_arg(
                      [
                          'termek' => $code_str,
                          'nev' => get_the_title($post_id),
                      ],
                      home_url('/')
                  );
                  $cta .= '#kapcsolat';

                  $terms_tag = get_the_terms($post_id, FABRIKA62_TERMEK_TAX);
                  $categories_attr = '';
                  if (is_array($terms_tag)) {
                      $slugs = [];
                      foreach ($terms_tag as $t) {
                          if (!($t instanceof WP_Term)) continue;
                          $slugs[] = (string) $t->slug;
                      }
                      $categories_attr = implode(' ', array_filter($slugs));
                  }

                  // Leírás: kézi excerpt vagy az első ~20 szó a tartalomból (2.16d)
                  $desc_str = '';
                  if (has_excerpt($post_id)) {
                      $desc_str = trim(wp_strip_all_tags((string) get_the_excerpt()));
                  } else {
                      $content = get_the_content(null, false, $post_id);
                      if ($content !== '') {
                          $desc_str = wp_trim_words(wp_strip_all_tags($content), 20, '…');
                      }
                  }
                  ?>
                <?php
                  $thumb_id = (int) get_post_thumbnail_id($post_id);
                  $img_large_url = $thumb_id ? (string) wp_get_attachment_image_url($thumb_id, 'product_large') : '';
                  if ($img_large_url === '') {
                      // Fallback: full size if product_large not generated yet
                      $img_large_url = $thumb_id ? (string) wp_get_attachment_image_url($thumb_id, 'full') : '';
                  }
                ?>
                <div class="product-card rounded-lg overflow-hidden shadow-md" style="background-color: #FFFBF5; border: 1px solid rgba(184, 115, 51, 0.15);" data-categories="<?php echo esc_attr($categories_attr); ?>" data-id="<?php echo esc_attr($code_str); ?>" data-desc="<?php echo esc_attr($desc_str); ?>" data-img-large="<?php echo esc_attr($img_large_url); ?>">
                  <div class="aspect-square overflow-hidden relative">
                    <span class="product-id"><?php echo esc_html($code_str); ?></span>
                    <?php
                    $thumb = fabrika62_post_square_thumbnail($post_id, 'square_600', ['class' => 'w-full h-full object-cover']);
                    if ($thumb !== '') {
                        echo $thumb; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                    } else {
                        echo '<div class="w-full h-full flex items-center justify-center text-sm text-[#5C3D2E]" style="background:#E8DCC8;">' . esc_html($catalog_no_image_label) . '</div>';
                    }
                    ?>
                  </div>
                  <div class="p-4">
                    <div class="flex flex-wrap gap-1 mb-2">
                      <?php
                      if (is_array($terms_tag)) {
                          foreach ($terms_tag as $t) {
                              if (!($t instanceof WP_Term)) continue;
                              echo '<span class="tag-badge">' . esc_html((string) $t->name) . '</span>';
                          }
                      }
                      ?>
                    </div>
                    <h3 class="text-base font-bold text-[#3B2314] mb-1"><?php the_title(); ?></h3>
                    <?php if ($desc_str !== '') : ?>
                      <p class="desc-short"><?php echo esc_html($desc_str); ?></p>
                    <?php endif; ?>
                    <?php if ($price_str !== '') : ?>
                      <p class="text-lg font-bold text-[#B87333] mb-3"><?php echo esc_html($price_str); ?></p>
                    <?php else : ?>
                      <p class="text-sm font-semibold text-[#5C3D2E] mb-3"><?php echo esc_html($catalog_price_contact_label); ?></p>
                    <?php endif; ?>
                    <a href="<?php echo esc_url($cta); ?>" class="block text-center px-4 py-2.5 rounded-lg font-semibold text-[#FFFBF5] transition-all duration-300 hover:scale-105" style="background: linear-gradient(135deg, #B87333, #C9A84C);">
                      <?php echo esc_html($catalog_interest_label); ?>
                    </a>
                  </div>
                </div>
              <?php endwhile; ?>
          <?php endif; ?>
      </div>

      <div id="no-results" class="<?php echo $q->have_posts() ? 'hidden ' : ''; ?>text-center py-16">
        <svg class="w-16 h-16 text-[#B87333] mx-auto mb-4 opacity-40" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
        <?php if ($cimke !== '') : ?>
          <p class="text-lg text-[#5C3D2E]"><?php echo esc_html($catalog_empty_filtered_label); ?></p>
          <button type="button" onclick="resetFilter()" class="mt-4 px-6 py-2 rounded-lg font-semibold text-[#B87333] border-2 border-[#B87333] hover:bg-[#B87333] hover:text-[#FFFBF5] transition-all cursor-pointer">
            <?php echo esc_html($catalog_show_all_button_label); ?>
          </button>
        <?php else : ?>
          <p class="text-lg text-[#5C3D2E]"><?php echo esc_html($catalog_empty_label); ?></p>
        <?php endif; ?>
      </div>
    </div>
  </section>

  <!-- ===================== CTA BANNER ===================== -->
  <section class="py-16" style="background-color: #E8DCC8;">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
      <h2 class="text-2xl sm:text-3xl font-bold text-[#3B2314] mb-4"><?php echo esc_html($catalog_cta_title); ?></h2>
      <p class="text-[#5C3D2E] mb-8"><?php echo esc_html($catalog_cta_text); ?></p>
      <a href="<?php echo esc_url(home_url('/#kapcsolat')); ?>" class="inline-flex items-center gap-2 px-8 py-3.5 rounded-lg font-semibold text-[#FFFBF5] transition-all duration-300 hover:scale-105 hover:shadow-lg" style="background: linear-gradient(135deg, #B87333, #C9A84C); box-shadow: 0 4px 15px rgba(184, 115, 51, 0.35);">
        <?php echo esc_html($catalog_cta_button_label); ?>
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
      </a>
    </div>
  </section>

  <!-- ===================== PRODUCT MODAL / CAROUSEL (2.16b, 2.16c) ===================== -->
  <div id="product-modal" role="dialog" aria-modal="true" aria-labelledby="modal-title" style="display:none">
    <div id="modal-backdrop" class="modal-backdrop"></div>
    <div class="modal-box">
      <button id="modal-close" class="modal-close-btn" aria-label="<?php esc_attr_e('Bezárás (ESC)', 'fabrika-62'); ?>">
        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
      <div class="modal-image-wrap">
        <img id="modal-img" src="" alt="" loading="eager" decoding="async" />
        <span id="modal-product-id" class="product-id"></span>
        <!-- Lapozó gombok a képszekción belül: soha nem lógnak ki -->
        <button id="modal-prev" class="modal-nav-btn modal-prev-btn" aria-label="<?php esc_attr_e('Előző termék', 'fabrika-62'); ?>">&#8249;</button>
        <button id="modal-next" class="modal-nav-btn modal-next-btn" aria-label="<?php esc_attr_e('Következő termék', 'fabrika-62'); ?>">&#8250;</button>
        <div id="modal-counter" class="modal-counter" aria-live="polite"></div>
      </div>
      <div class="modal-info">
        <div id="modal-tags" class="flex flex-wrap gap-1"></div>
        <h2 id="modal-title" class="modal-product-name"></h2>
        <p id="modal-price" class="modal-price"></p>
        <p id="modal-desc" class="modal-desc"></p>
        <a id="modal-cta" href="#" class="modal-cta-btn"><?php echo esc_html($catalog_interest_label); ?></a>
      </div>
    </div>
  </div>

<?php
wp_reset_postdata();
get_footer();
