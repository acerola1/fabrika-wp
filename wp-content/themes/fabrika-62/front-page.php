<?php
declare(strict_types=1);

get_header();

$ref_base = get_stylesheet_directory_uri() . '/assets/references';

$brand_name = fabrika62_opt_str('brand_name', 'Fabrika Ajándék');
?>

  <!-- ===================== HERO ===================== -->
  <section id="hero" class="relative min-h-screen flex items-center justify-center" style="background-color: #3B2314; clip-path: inset(0);">
    <div id="hero-interaction-bg" class="hero-interaction-bg absolute inset-0 z-[1] pointer-events-none"></div>

    <div class="machine-gear machine-gear--one hidden md:block" aria-hidden="true">
      <svg viewBox="0 0 140 140" fill="none" xmlns="http://www.w3.org/2000/svg">
        <g transform="translate(70 70)">
          <g stroke="currentColor" stroke-width="1.45" fill="none" opacity="0.95">
            <rect x="-4.8" y="-56" width="9.6" height="16" rx="1.7"/>
            <rect x="-4.8" y="-56" width="9.6" height="16" rx="1.7" transform="rotate(30)"/>
            <rect x="-4.8" y="-56" width="9.6" height="16" rx="1.7" transform="rotate(60)"/>
            <rect x="-4.8" y="-56" width="9.6" height="16" rx="1.7" transform="rotate(90)"/>
            <rect x="-4.8" y="-56" width="9.6" height="16" rx="1.7" transform="rotate(120)"/>
            <rect x="-4.8" y="-56" width="9.6" height="16" rx="1.7" transform="rotate(150)"/>
            <rect x="-4.8" y="-56" width="9.6" height="16" rx="1.7" transform="rotate(180)"/>
            <rect x="-4.8" y="-56" width="9.6" height="16" rx="1.7" transform="rotate(210)"/>
            <rect x="-4.8" y="-56" width="9.6" height="16" rx="1.7" transform="rotate(240)"/>
            <rect x="-4.8" y="-56" width="9.6" height="16" rx="1.7" transform="rotate(270)"/>
            <rect x="-4.8" y="-56" width="9.6" height="16" rx="1.7" transform="rotate(300)"/>
            <rect x="-4.8" y="-56" width="9.6" height="16" rx="1.7" transform="rotate(330)"/>
          </g>
          <circle r="39" stroke="currentColor" stroke-width="1.7" fill="none"/>
          <circle r="23" stroke="#C9A84C" stroke-width="1.5" fill="none"/>
          <line x1="0" y1="-21" x2="0" y2="21" stroke="#C9A84C" stroke-width="1.4" stroke-linecap="round"/>
          <line x1="-21" y1="0" x2="21" y2="0" stroke="#C9A84C" stroke-width="1.4" stroke-linecap="round"/>
          <line x1="-15" y1="-15" x2="15" y2="15" stroke="#C9A84C" stroke-width="1.3" stroke-linecap="round"/>
          <line x1="15" y1="-15" x2="-15" y2="15" stroke="#C9A84C" stroke-width="1.3" stroke-linecap="round"/>
          <circle r="7.6" stroke="#C9A84C" stroke-width="1.45" fill="none"/>
        </g>
      </svg>
    </div>
    <div class="machine-gear machine-gear--two hidden md:block" aria-hidden="true">
      <svg viewBox="0 0 120 120" fill="none" xmlns="http://www.w3.org/2000/svg">
        <g transform="translate(60 60)">
          <g stroke="currentColor" stroke-width="1.3" fill="none" opacity="0.95">
            <rect x="-4.05" y="-45" width="8.1" height="13" rx="1.5"/>
            <rect x="-4.05" y="-45" width="8.1" height="13" rx="1.5" transform="rotate(36)"/>
            <rect x="-4.05" y="-45" width="8.1" height="13" rx="1.5" transform="rotate(72)"/>
            <rect x="-4.05" y="-45" width="8.1" height="13" rx="1.5" transform="rotate(108)"/>
            <rect x="-4.05" y="-45" width="8.1" height="13" rx="1.5" transform="rotate(144)"/>
            <rect x="-4.05" y="-45" width="8.1" height="13" rx="1.5" transform="rotate(180)"/>
            <rect x="-4.05" y="-45" width="8.1" height="13" rx="1.5" transform="rotate(216)"/>
            <rect x="-4.05" y="-45" width="8.1" height="13" rx="1.5" transform="rotate(252)"/>
            <rect x="-4.05" y="-45" width="8.1" height="13" rx="1.5" transform="rotate(288)"/>
            <rect x="-4.05" y="-45" width="8.1" height="13" rx="1.5" transform="rotate(324)"/>
          </g>
          <circle r="31" stroke="currentColor" stroke-width="1.55" fill="none"/>
          <circle r="18.5" stroke="#C9A84C" stroke-width="1.35" fill="none"/>
          <line x1="0" y1="-16.5" x2="0" y2="16.5" stroke="#C9A84C" stroke-width="1.25" stroke-linecap="round"/>
          <line x1="-16.5" y1="0" x2="16.5" y2="0" stroke="#C9A84C" stroke-width="1.25" stroke-linecap="round"/>
          <line x1="-11.7" y1="-11.7" x2="11.7" y2="11.7" stroke="#C9A84C" stroke-width="1.15" stroke-linecap="round"/>
          <line x1="11.7" y1="-11.7" x2="-11.7" y2="11.7" stroke="#C9A84C" stroke-width="1.15" stroke-linecap="round"/>
          <circle r="6.2" stroke="#C9A84C" stroke-width="1.25" fill="none"/>
        </g>
      </svg>
    </div>

    <!-- Decorative SVG gear (parallax) — visible, moves clearly -->
    <div data-parallax="0.5" data-parallax-max="180" data-parallax-spin="540" data-parallax-hue="120" data-parallax-stroke="1" class="gear-scroll absolute top-[20%] right-[10%] z-[3] hidden md:block" style="opacity: 0.55;">
      <svg width="180" height="180" viewBox="0 0 140 140" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M70 20 L76 40 L90 28 L84 48 L104 46 L92 60 L112 66 L92 72 L104 86 L84 84 L90 104 L76 92 L70 112 L64 92 L50 104 L56 84 L36 86 L48 72 L28 66 L48 60 L36 46 L56 48 L50 28 L64 40 Z" stroke="#B87333" stroke-width="2.5" fill="none"/>
        <circle cx="70" cy="66" r="20" stroke="#B87333" stroke-width="2.5" fill="none"/>
        <circle cx="70" cy="66" r="8" stroke="#C9A84C" stroke-width="2" fill="none"/>
      </svg>
    </div>

    <!-- Decorative SVG diamond (parallax) — visible and moving with scroll -->
    <div data-parallax="0.4" data-parallax-max="150" data-parallax-x="-95" data-parallax-spin="-260" data-parallax-scale="0.24" class="absolute bottom-[25%] left-[6%] z-[3] hidden md:block" style="opacity: 0.5;">
      <svg width="120" height="120" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
        <circle cx="50" cy="50" r="35" stroke="#C9A84C" stroke-width="2" fill="none" stroke-dasharray="6 4"/>
        <path d="M30 50 L50 30 L70 50 L50 70 Z" stroke="#B87333" stroke-width="2" fill="none"/>
      </svg>
    </div>

    <!-- Hero content -->
    <div class="relative z-10 text-center px-4 max-w-3xl mx-auto">
      <!-- Stamp badge -->
      <div class="stamp-badge mx-auto mb-8">
        <span><?php echo wp_kses_post(fabrika62_opt_str('hero_badge', 'Est. 2024<br/>&middot; Szarvas &middot;')); ?></span>
      </div>

      <h1 class="text-5xl sm:text-6xl md:text-7xl font-bold text-[#FFFBF5] mb-6 leading-tight">
        <?php echo esc_html(fabrika62_opt_str('hero_title', $brand_name)); ?>
      </h1>

      <div class="copper-divider max-w-xs mx-auto mb-6"></div>

      <p class="text-lg sm:text-xl text-[#E8DCC8] mb-10 leading-relaxed max-w-2xl mx-auto">
        <?php echo wp_kses_post(fabrika62_opt_str('hero_subtitle', 'Kézműves fa ajándékok &ndash; lézergravírozás, fa táblák, kerti díszek, egyedi nyomatok')); ?>
      </p>

      <?php
      $archive_url = get_post_type_archive_link(FABRIKA62_TERMEK_POST_TYPE);
      $cta2_href = fabrika62_opt_str('hero_cta2_href', '');
      $cta2_href = $cta2_href !== '' ? $cta2_href : ($archive_url ?: '/?post_type=' . FABRIKA62_TERMEK_POST_TYPE);
      ?>
      <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
        <a href="<?php echo esc_attr(fabrika62_opt_str('hero_cta_href', '#kapcsolat')); ?>" class="inline-block px-8 py-3.5 rounded-lg font-semibold text-[#FFFBF5] transition-all duration-300 hover:scale-105 hover:shadow-lg" style="background: linear-gradient(135deg, #B87333, #C9A84C); box-shadow: 0 4px 15px rgba(184, 115, 51, 0.35);">
          <?php echo esc_html(fabrika62_opt_str('hero_cta_label', 'Egyedi rendelés')); ?>
        </a>
        <a href="<?php echo esc_url($cta2_href); ?>" class="inline-flex items-center gap-2 px-8 py-3.5 rounded-lg font-semibold transition-all duration-300 hover:scale-105 hover:shadow-lg" style="background-color: rgba(255, 251, 245, 0.12); border: 1px solid rgba(201, 168, 76, 0.4); color: #FFFBF5;">
          <?php echo esc_html(fabrika62_opt_str('hero_cta2_label', 'Katalógus')); ?>
          <svg class="w-5 h-5 text-[#C9A84C]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        </a>
      </div>
    </div>

    <!-- Scroll hint -->
    <div class="absolute bottom-8 left-1/2 -translate-x-1/2 z-10 animate-bounce">
      <svg class="w-6 h-6 text-[#B87333]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
    </div>
  </section>

  <!-- ===================== TERMEK KATEGORIAK ===================== -->
  <section id="termekek" class="py-20 sm:py-28" style="background-color: #F4EDE4;">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="text-center mb-16">
        <h2 class="text-3xl sm:text-4xl font-bold text-[#3B2314] mb-4"><?php echo esc_html(fabrika62_opt_str('products_title', 'Amit a műhelyben készítünk')); ?></h2>
        <div class="copper-divider max-w-[200px] mx-auto"></div>
      </div>

      <div class="relative">
        <!-- Decorative copper SVG between cards -->
        <div data-parallax="0.35" data-parallax-max="120" class="absolute -left-8 top-[10%] z-[2] hidden md:block" style="opacity: 0.65;">
          <svg width="90" height="90" viewBox="0 0 60 60" fill="none"><circle cx="30" cy="30" r="25" stroke="#B87333" stroke-width="2" stroke-dasharray="4 3"/><circle cx="30" cy="30" r="12" stroke="#C9A84C" stroke-width="1.5"/></svg>
        </div>
        <div data-parallax="0.45" data-parallax-max="150" class="absolute -right-8 top-[40%] z-[2] hidden md:block" style="opacity: 0.65;">
          <svg width="80" height="80" viewBox="0 0 50 50" fill="none"><rect x="5" y="5" width="40" height="40" rx="4" stroke="#B87333" stroke-width="2" transform="rotate(15 25 25)"/><rect x="15" y="15" width="20" height="20" rx="2" stroke="#C9A84C" stroke-width="1.5" transform="rotate(15 25 25)"/></svg>
        </div>
        <div data-parallax="0.4" data-parallax-max="130" class="absolute left-[45%] top-[72%] z-[2] hidden md:block" style="opacity: 0.6;">
          <svg width="100" height="50" viewBox="0 0 70 40" fill="none"><path d="M5 20 Q20 5, 35 20 Q50 35, 65 20" stroke="#B87333" stroke-width="2" fill="none"/></svg>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
          <?php
          $cats = fabrika62_opt('product_categories', null);
          if (!is_array($cats) || $cats === []) {
              $cats = [
                  ['image' => $ref_base . '/01-laser-engraved-magnet.jpg', 'title' => 'Lézergravírozott képek', 'description' => 'Portrék és minták, lézertechnológiával fába vésve.'],
                  ['image' => $ref_base . '/03-handmade-wood-sign-shop-1.jpg', 'title' => 'Fa táblák / feliratok', 'description' => 'Személyre szabott feliratok bármilyen alkalomra.'],
                  ['image' => $ref_base . '/05-fridge-magnets.jpg', 'title' => 'Kis ajándéktárgyak', 'description' => 'Hűtőmágnesek, kulcstartók és apró kedvességek.'],
                  ['image' => $ref_base . '/07-terrariums-florist.jpg', 'title' => 'Kerti díszek / virágtartók', 'description' => 'Kézzel készített dekorációk a szabadba.'],
                  ['image' => $ref_base . '/09-california-travel-poster.jpg', 'title' => 'Színes nyomatok', 'description' => 'Egyedi grafikák és illusztrációk, nyomtatva.'],
              ];
          }

          foreach ($cats as $cat) :
              $img_url = fabrika62_image_url($cat['image'] ?? null, is_string($cat['image'] ?? null) ? (string) $cat['image'] : '');
              $title = is_string($cat['title'] ?? null) ? (string) $cat['title'] : '';
              $slug = is_string($cat['slug'] ?? null) ? sanitize_title((string) $cat['slug']) : '';
              if ($slug === '' && $title !== '') {
                  $slug = sanitize_title($title);
              }
              $desc = is_string($cat['description'] ?? null) ? (string) $cat['description'] : '';
              if ($title === '') {
                  continue;
              }
              $cat_href = '';
              if ($slug !== '') {
                  $base = get_post_type_archive_link(FABRIKA62_TERMEK_POST_TYPE);
                  if ($base) {
                      // Match the 6-3 mock param name.
                      $cat_href = add_query_arg(['kategoria' => $slug], $base);
                  } else {
                      $cat_href = add_query_arg(['post_type' => FABRIKA62_TERMEK_POST_TYPE, 'kategoria' => $slug], home_url('/'));
                  }
              }
          ?>
          <div class="cat-card rounded-lg overflow-hidden shadow-md hover:shadow-xl" style="background-color: #E8DCC8; border: 1px solid rgba(184, 115, 51, 0.15);">
            <div class="aspect-[4/3] overflow-hidden">
              <?php if ($img_url !== '') : ?>
                <img src="<?php echo esc_url($img_url); ?>" alt="<?php echo esc_attr($title); ?>" loading="lazy" class="w-full h-full object-cover" />
              <?php endif; ?>
            </div>
            <div class="p-5">
              <h3 class="text-lg font-bold text-[#3B2314] mb-2"><?php echo esc_html($title); ?></h3>
              <?php if ($desc !== '') : ?>
                <p class="text-sm text-[#5C3D2E] leading-relaxed"><?php echo wp_kses_post($desc); ?></p>
              <?php endif; ?>
              <?php if ($cat_href !== '') : ?>
                <a href="<?php echo esc_url($cat_href); ?>" class="inline-flex items-center gap-1 mt-4 text-sm font-semibold text-[#B87333] hover:text-[#C9A84C] transition-colors">
                  <?php echo esc_html__('Nézd meg a termékeket', 'fabrika-62'); ?>
                  <span aria-hidden="true">→</span>
                </a>
              <?php endif; ?>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </section>

  <!-- ===================== GALERIA ===================== -->
  <section id="galeria" class="py-20 sm:py-28" style="background-color: #E8DCC8;">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="text-center mb-16">
        <h2 class="text-3xl sm:text-4xl font-bold text-[#3B2314] mb-4"><?php echo esc_html(fabrika62_opt_str('gallery_title', 'Munkáink')); ?></h2>
        <div class="copper-divider max-w-[200px] mx-auto"></div>
      </div>

      <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
        <?php
        $product_gallery = new WP_Query([
            'post_type' => FABRIKA62_TERMEK_POST_TYPE,
            'post_status' => 'publish',
            'posts_per_page' => 10,
            'no_found_rows' => true,
            'ignore_sticky_posts' => true,
            'orderby' => 'date',
            'order' => 'DESC',
        ]);

        $gallery_from_products = [];
        if ($product_gallery->have_posts()) {
            while ($product_gallery->have_posts()) {
                $product_gallery->the_post();
                $pid = (int) get_the_ID();
                $thumb_id = get_post_thumbnail_id($pid);
                if (!$thumb_id) {
                    continue;
                }
                $gallery_from_products[] = [
                    'thumb_id' => (int) $thumb_id,
                    'alt' => (string) get_the_title($pid),
                ];
            }
            wp_reset_postdata();
        }

        if ($gallery_from_products !== []) {
            foreach ($gallery_from_products as $gi) :
                $thumb_id = (int) ($gi['thumb_id'] ?? 0);
                if ($thumb_id <= 0) continue;
                $alt = is_string($gi['alt'] ?? null) ? (string) $gi['alt'] : '';
        ?>
          <div class="gallery-item rounded-lg overflow-hidden" style="border: 3px solid rgba(184, 115, 51, 0.2); background: #FFFBF5; padding: 3px;">
            <?php
            $img = wp_get_attachment_image($thumb_id, 'square_600', false, ['class' => 'w-full aspect-square object-cover rounded-md', 'loading' => 'lazy', 'alt' => $alt]);
            echo is_string($img) ? $img : '';
            ?>
          </div>
        <?php
            endforeach;
        } else {
        $gallery_items = fabrika62_opt('gallery_items', null);
        if (!is_array($gallery_items) || $gallery_items === []) {
            $gallery_items = [
                ['image' => $ref_base . '/01-laser-engraved-magnet.jpg', 'alt' => 'Lézergravírozott mágnes'],
                ['image' => $ref_base . '/02-laser-engraved-wall-plaque.jpg', 'alt' => 'Lézergravírozott fali tábla'],
                ['image' => $ref_base . '/03-handmade-wood-sign-shop-1.jpg', 'alt' => 'Kézműves fa tábla'],
                ['image' => $ref_base . '/04-waterfront-rules-wood-sign.jpg', 'alt' => 'Szabályok fa tábla'],
                ['image' => $ref_base . '/05-fridge-magnets.jpg', 'alt' => 'Hűtőmágnesek'],
                ['image' => $ref_base . '/06-picnic-basket-gifts.jpg', 'alt' => 'Piknik kosar ajandek'],
                ['image' => $ref_base . '/07-terrariums-florist.jpg', 'alt' => 'Terráriumok'],
                ['image' => $ref_base . '/08-blue-painted-wall-planters.jpg', 'alt' => 'Fali virágtartók'],
                ['image' => $ref_base . '/09-california-travel-poster.jpg', 'alt' => 'Poszter nyomat'],
                ['image' => $ref_base . '/10-jane-avril-poster.jpg', 'alt' => 'Muveszi poszter'],
            ];
        }

        foreach ($gallery_items as $gi) :
            $img_url = fabrika62_image_url($gi['image'] ?? null, is_string($gi['image'] ?? null) ? (string) $gi['image'] : '');
            $alt = is_string($gi['alt'] ?? null) ? (string) $gi['alt'] : '';
            if ($img_url === '') {
                continue;
            }
        ?>
        <div class="gallery-item rounded-lg overflow-hidden" style="border: 3px solid rgba(184, 115, 51, 0.2); background: #FFFBF5; padding: 3px;">
          <img src="<?php echo esc_url($img_url); ?>" alt="<?php echo esc_attr($alt); ?>" loading="lazy" class="w-full aspect-square object-cover rounded-md" />
        </div>
        <?php endforeach; ?>
        <?php } ?>
      </div>

      <?php
      $archive = get_post_type_archive_link(FABRIKA62_TERMEK_POST_TYPE);
      $archive = $archive ?: add_query_arg(['post_type' => FABRIKA62_TERMEK_POST_TYPE], home_url('/'));
      ?>
      <div class="text-center" style="margin-top: 80px;">
        <a href="<?php echo esc_url($archive); ?>" class="inline-flex items-center gap-2 px-8 py-3.5 rounded-lg font-semibold text-[#FFFBF5] transition-all duration-300 hover:scale-105 hover:shadow-lg" style="background: linear-gradient(135deg, #B87333, #C9A84C); box-shadow: 0 4px 15px rgba(184, 115, 51, 0.35);">
          <?php echo esc_html__('Összes termék megtekintése', 'fabrika-62'); ?>
          <span aria-hidden="true">→</span>
        </a>
      </div>
    </div>
  </section>

  <!-- ===================== HOGYAN RENDELHETSZ ===================== -->
  <section id="rendeles" class="py-20 sm:py-28" style="background-color: #F4EDE4;">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="text-center mb-16">
        <h2 class="text-3xl sm:text-4xl font-bold text-[#3B2314] mb-4"><?php echo esc_html(fabrika62_opt_str('order_title', 'Hogyan rendelhetsz?')); ?></h2>
        <div class="copper-divider max-w-[200px] mx-auto"></div>
      </div>

      <div class="relative flex flex-col gap-12 max-w-lg mx-auto">
        <?php
        $steps = fabrika62_opt('order_steps', null);
        if (!is_array($steps) || $steps === []) {
            $steps = [
                ['title' => 'Mesélj az ötletedről', 'description' => 'Válassz meglévő tereket, vagy írd le az egyedi elképzelésed.'],
                ['title' => 'Egyeztetünk', 'description' => 'Megbeszéljük a részleteket, az árát és az átvétel módját.'],
                ['title' => 'Elkészül és tiéd', 'description' => 'Személyes átvétel készpénzzel, postánál előre utalás.'],
            ];
        }
        $speed_by_index = [0.3, 0.45, 0.6];
        $max_by_index = [80, 120, 160];
        foreach (array_values($steps) as $idx => $step) :
            $step_title = is_string($step['title'] ?? null) ? (string) $step['title'] : '';
            $step_desc = is_string($step['description'] ?? null) ? (string) $step['description'] : '';
            if ($step_title === '') {
                continue;
            }
            $num = $idx + 1;
            $speed = $speed_by_index[$idx] ?? 0.35;
            $max = $max_by_index[$idx] ?? 120;
        ?>
        <div data-parallax="<?php echo esc_attr((string) $speed); ?>" data-parallax-max="<?php echo esc_attr((string) $max); ?>" class="reveal relative flex items-start gap-6">
          <div class="relative flex-shrink-0">
            <div class="w-14 h-14 rounded-full flex items-center justify-center text-[#FFFBF5] font-bold text-xl" style="background: linear-gradient(135deg, #B87333, #C9A84C);"><?php echo esc_html((string) $num); ?></div>
            <?php if ($idx < (count($steps) - 1)) : ?>
              <div class="step-connector"></div>
            <?php endif; ?>
          </div>
          <div class="pt-1">
            <h3 class="text-xl font-bold text-[#3B2314] mb-2"><?php echo esc_html($step_title); ?></h3>
            <?php if ($step_desc !== '') : ?>
              <p class="text-[#5C3D2E] leading-relaxed"><?php echo wp_kses_post($step_desc); ?></p>
            <?php endif; ?>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- ===================== AJÁNDÉKÖTLETEK ===================== -->
  <section class="py-20 sm:py-28" style="background-color: #E8DCC8;">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="text-center mb-16">
        <h2 class="text-3xl sm:text-4xl font-bold text-[#3B2314] mb-4"><?php echo esc_html(fabrika62_opt_str('gifts_title', 'Mikor adj kézművest?')); ?></h2>
        <div class="copper-divider max-w-[200px] mx-auto"></div>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php
        $gift_ideas = fabrika62_opt('gift_ideas', null);
        if (!is_array($gift_ideas) || $gift_ideas === []) {
            $gift_ideas = [
                ['icon' => 'gift', 'title' => 'Születésnap', 'description' => 'Gravírozott keret vagy egyedi fa tábla.'],
                ['icon' => 'heart', 'title' => 'Esküvő', 'description' => 'Személyes esküvői emléktábla.'],
                ['icon' => 'user', 'title' => 'Névnap', 'description' => 'Apró meglepetés: mágnes vagy kulcstartó.'],
                ['icon' => 'sparkles', 'title' => 'Karácsony', 'description' => 'Fából készült ünnepi ajándékok.'],
                ['icon' => 'smile', 'title' => 'Csak úgy', 'description' => 'A legjobb ajándék: amire nem számít.'],
            ];
        }
        foreach ($gift_ideas as $gi) :
            $icon = is_string($gi['icon'] ?? null) ? (string) $gi['icon'] : 'gift';
            $title = is_string($gi['title'] ?? null) ? (string) $gi['title'] : '';
            $desc = is_string($gi['description'] ?? null) ? (string) $gi['description'] : '';
            if ($title === '') continue;
        ?>
        <div class="rounded-lg p-6 transition-shadow duration-300 hover:shadow-lg" style="background-color: #FFFBF5; border: 1px solid rgba(184, 115, 51, 0.15);">
          <div class="w-10 h-10 rounded-full flex items-center justify-center mb-4" style="background: rgba(184, 115, 51, 0.12);">
            <?php echo fabrika62_icon_svg($icon); ?>
          </div>
          <h3 class="text-lg font-bold text-[#3B2314] mb-2"><?php echo esc_html($title); ?></h3>
          <?php if ($desc !== '') : ?>
            <p class="text-sm text-[#5C3D2E] leading-relaxed"><?php echo wp_kses_post($desc); ?></p>
          <?php endif; ?>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- ===================== PIACI MEGJELENES ===================== -->
  <section class="py-20 sm:py-28" style="background-color: #F4EDE4;">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
      <h2 class="text-3xl sm:text-4xl font-bold text-[#3B2314] mb-4"><?php echo esc_html(fabrika62_opt_str('market_title', 'Keress minket személyesen!')); ?></h2>
      <div class="copper-divider max-w-[200px] mx-auto mb-10"></div>

      <div class="max-w-xl mx-auto rounded-xl p-8 sm:p-10" style="background-color: #FFFBF5; border: 1px solid rgba(184, 115, 51, 0.15); box-shadow: 0 4px 20px rgba(92, 61, 46, 0.08);">
        <div class="flex justify-center mb-6">
          <svg class="w-12 h-12 text-[#B87333]" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>
        </div>
        <p class="text-lg text-[#3B2314] font-medium mb-3"><?php echo esc_html(fabrika62_opt_str('market_lead', 'Szarvasi és környékbeli piacok, vásárok, rendezvények.')); ?></p>
        <p class="text-[#5C3D2E]"><?php echo esc_html(fabrika62_opt_str('market_sub', 'Személyes átvétel egyeztetéssel.')); ?></p>
      </div>
    </div>
  </section>

  <!-- ===================== KAPCSOLAT / URLAP ===================== -->
  <section id="kapcsolat" class="py-20 sm:py-28" style="background-color: #E8DCC8;">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="text-center mb-16">
        <h2 class="text-3xl sm:text-4xl font-bold text-[#3B2314] mb-4"><?php echo esc_html(fabrika62_opt_str('contact_title', 'Írj nekünk!')); ?></h2>
        <div class="copper-divider max-w-[200px] mx-auto"></div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
        <!-- Form (placeholder; kesobb Fluent Forms) -->
        <div class="lg:col-span-2">
          <?php
          $contact_shortcode = fabrika62_opt_str('contact_form_shortcode', '');
          if ($contact_shortcode !== '') {
              echo do_shortcode($contact_shortcode);
          } else {
          ?>
          <form class="rounded-xl p-6 sm:p-8 space-y-5" style="background-color: #FFFBF5; border: 1px solid rgba(184, 115, 51, 0.15); box-shadow: 0 4px 20px rgba(92, 61, 46, 0.08);">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
              <div>
                <label for="nev" class="block text-sm font-semibold text-[#3B2314] mb-1.5">Név *</label>
                <input type="text" id="nev" name="nev" required class="w-full rounded-lg border px-4 py-2.5 text-sm focus:outline-none focus:ring-2" style="border-color: rgba(184, 115, 51, 0.25); background: #FFFBF5; focus-ring-color: #B87333;" placeholder="A neved" />
              </div>
              <div>
                <label for="email" class="block text-sm font-semibold text-[#3B2314] mb-1.5">E-mail *</label>
                <input type="email" id="email" name="email" required class="w-full rounded-lg border px-4 py-2.5 text-sm focus:outline-none focus:ring-2" style="border-color: rgba(184, 115, 51, 0.25); background: #FFFBF5;" placeholder="példa@email.com" />
              </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
              <div>
                <label for="telefon" class="block text-sm font-semibold text-[#3B2314] mb-1.5">Telefon <span class="font-normal text-[#5C3D2E]">(opcionális)</span></label>
                <input type="tel" id="telefon" name="telefon" class="w-full rounded-lg border px-4 py-2.5 text-sm focus:outline-none focus:ring-2" style="border-color: rgba(184, 115, 51, 0.25); background: #FFFBF5;" placeholder="+36 XX XXX XXXX" />
              </div>
              <div>
                <label for="kategoria" class="block text-sm font-semibold text-[#3B2314] mb-1.5">Kategória</label>
                <select id="kategoria" name="kategoria" class="w-full rounded-lg border px-4 py-2.5 text-sm focus:outline-none focus:ring-2" style="border-color: rgba(184, 115, 51, 0.25); background: #FFFBF5;">
                  <option value="">Válassz kategóriát...</option>
                  <option value="lezer">Lézergravírozás</option>
                  <option value="tabla">Fa táblák / feliratok</option>
                  <option value="ajandek">Kis ajándéktárgyak</option>
                  <option value="kert">Kerti díszek</option>
                  <option value="nyomat">Színes nyomatok</option>
                  <option value="egyeb">Egyéb</option>
                </select>
              </div>
            </div>
            <div>
              <label for="uzenet" class="block text-sm font-semibold text-[#3B2314] mb-1.5">Üzenet *</label>
              <textarea id="uzenet" name="uzenet" required rows="5" class="w-full rounded-lg border px-4 py-2.5 text-sm focus:outline-none focus:ring-2 resize-y" style="border-color: rgba(184, 115, 51, 0.25); background: #FFFBF5;" placeholder="Írd le az elképzelésed..."></textarea>
            </div>
            <button type="submit" class="w-full sm:w-auto px-8 py-3 rounded-lg font-semibold text-[#FFFBF5] transition-all duration-300 hover:scale-105 hover:shadow-lg cursor-pointer" style="background: linear-gradient(135deg, #B87333, #C9A84C); box-shadow: 0 4px 15px rgba(184, 115, 51, 0.3);">
              Üzenet küldése
            </button>
          </form>
          <?php } ?>
        </div>

        <!-- Contact sidebar -->
        <div class="space-y-6">
          <div class="rounded-xl p-6" style="background-color: #FFFBF5; border: 1px solid rgba(184, 115, 51, 0.15); box-shadow: 0 4px 20px rgba(92, 61, 46, 0.08);">
            <h3 class="font-bold text-[#3B2314] text-lg mb-5">Elérhetőségek</h3>

            <div class="space-y-4">
              <!-- Email -->
              <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-[#B87333] mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                <div>
                  <p class="text-sm font-medium text-[#3B2314]">E-mail</p>
                  <?php $email = fabrika62_opt_str('contact_email', 'info@fabrikaajandek.hu'); ?>
                  <a href="mailto:<?php echo esc_attr($email); ?>" class="text-sm text-[#B87333] hover:underline"><?php echo esc_html($email); ?></a>
                </div>
              </div>

              <!-- Facebook -->
              <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-[#B87333] mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                <div>
                  <p class="text-sm font-medium text-[#3B2314]">Facebook</p>
                  <?php $fb_label = fabrika62_opt_str('contact_facebook_label', 'Fabrika Ajándék'); ?>
                  <?php $fb_url = fabrika62_opt_str('contact_facebook_url', '#'); ?>
                  <a href="<?php echo esc_url($fb_url); ?>" class="text-sm text-[#B87333] hover:underline"><?php echo esc_html($fb_label); ?></a>
                </div>
              </div>

              <!-- Viber -->
              <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-[#B87333] mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                <div>
                  <p class="text-sm font-medium text-[#3B2314]">Viber</p>
                  <?php $viber = fabrika62_opt_str('contact_viber', '+36 XX XXX XXXX'); ?>
                  <a href="#" class="text-sm text-[#B87333] hover:underline"><?php echo esc_html($viber); ?></a>
                </div>
              </div>

              <!-- Instagram -->
              <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-[#B87333] mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1112.63 8 4 4 0 0116 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>
                <div>
                  <p class="text-sm font-medium text-[#3B2314]">Instagram</p>
                  <?php $ig = fabrika62_opt_str('contact_instagram', '@fabrikaajandek'); ?>
                  <?php $ig_url = fabrika62_opt_str('contact_instagram_url', '#'); ?>
                  <a href="<?php echo esc_url($ig_url); ?>" class="text-sm text-[#B87333] hover:underline"><?php echo esc_html($ig); ?></a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ===================== GYIK ===================== -->
  <section class="py-20 sm:py-28" style="background-color: #F4EDE4;">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="text-center mb-16">
        <h2 class="text-3xl sm:text-4xl font-bold text-[#3B2314] mb-4"><?php echo esc_html(fabrika62_opt_str('faq_title', 'Gyakori kérdések')); ?></h2>
        <div class="copper-divider max-w-[200px] mx-auto"></div>
      </div>

      <div class="space-y-3">
        <?php
        $faq_items = fabrika62_opt('faq_items', null);
        if (!is_array($faq_items) || $faq_items === []) {
            $faq_items = [
                ['question' => 'Mennyi idő alatt készül el egy egyedi termék?', 'answer' => 'Általában 3–7 munkanap.'],
                ['question' => 'Hogyan fizethetek?', 'answer' => 'Személyesen: készpénz. Egyedi/posta: előre utalás.'],
                ['question' => 'Van postázás?', 'answer' => 'Igen, Magyarországon belül.'],
                ['question' => 'Külföldre is küldtök?', 'answer' => 'Jelenleg csak belföldre.'],
                ['question' => 'Saját ötletet is megvalósítotok?', 'answer' => 'Persze! Küldj leírást, képet vagy vázlatot.'],
            ];
        }
        foreach ($faq_items as $fi) :
            $q = is_string($fi['question'] ?? null) ? (string) $fi['question'] : '';
            $a = is_string($fi['answer'] ?? null) ? (string) $fi['answer'] : '';
            if ($q === '') continue;
        ?>
        <div class="faq-item rounded-lg overflow-hidden" style="background-color: #FFFBF5; border: 1px solid rgba(184, 115, 51, 0.15);">
          <button class="faq-toggle w-full flex items-center justify-between px-6 py-4 text-left cursor-pointer">
            <span class="font-semibold text-[#3B2314] pr-4"><?php echo esc_html($q); ?></span>
            <svg class="faq-chevron w-5 h-5 text-[#B87333] flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
          </button>
          <div class="faq-answer px-6">
            <p class="pb-4 text-[#5C3D2E] leading-relaxed"><?php echo wp_kses_post($a); ?></p>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

<?php
get_footer();
