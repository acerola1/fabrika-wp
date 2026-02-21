<?php
declare(strict_types=1);
?><!DOCTYPE html>
<html <?php language_attributes(); ?> class="scroll-smooth">
<head>
  <meta charset="<?php bloginfo('charset'); ?>" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="description" content="<?php echo esc_attr(fabrika62_opt_str('meta_description', 'Kézműves fa ajándékok, lézergravírozás, fa táblák, kerti díszek és egyedi nyomatok. Fabrika Ajándék, Szarvas.')); ?>" />
  <?php wp_head(); ?>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Bitter:wght@400;500;600;700&family=Source+Sans+3:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
  <style>
    body { font-family: 'Source Sans 3', sans-serif; color: #2A2018; }
    h1, h2, h3, h4, h5, h6 { font-family: 'Bitter', serif; }

    /* Reveal animation */
    .reveal {
      opacity: 0;
      transform: translateY(20px);
      transition: opacity 0.7s ease, transform 0.7s ease;
    }
    .reveal.revealed {
      opacity: 1;
      transform: translateY(0);
    }
    /* Elements with data-parallax: let JS control transform, only animate opacity for reveal */
    .reveal[data-parallax] {
      transition: opacity 0.7s ease;
    }
    .reveal.revealed[data-parallax] {
      transform: none; /* JS will override this via inline style */
    }
    @media (prefers-reduced-motion: reduce) {
      .reveal { opacity: 1; transform: none; transition: none; }
    }
    [data-parallax] {
      will-change: transform;
    }

    /* Wood grain texture */
    .wood-grain {
      background-image:
        linear-gradient(180deg, rgba(255, 248, 236, 0.16), rgba(42, 24, 12, 0.38)),
        url('<?php echo esc_url(get_stylesheet_directory_uri() . "/assets/references/fine_grained_wood_col_4k.jpg"); ?>');
      background-repeat: no-repeat, no-repeat;
      background-size: 100% 100%, 125% auto;
      background-position: 0px 0px, center top;
      background-blend-mode: multiply;
      opacity: 0.92;
      filter: contrast(1.14) saturate(0.95);
    }
    .gear-scroll {
      will-change: transform, filter, opacity;
    }
    .gear-scroll svg {
      transform-origin: center;
      will-change: transform;
    }
    .hero-interaction-bg {
      background-image: url('<?php echo esc_url(get_stylesheet_directory_uri() . "/assets/references/background.png"); ?>');
      background-repeat: no-repeat;
      background-size: cover;
      background-position: center;
      opacity: 0;
      filter: saturate(1.05) contrast(1.02);
      will-change: opacity;
    }
    @keyframes machine-gear-spin-cw {
      from { transform: rotate(0deg); }
      to { transform: rotate(360deg); }
    }
    @keyframes machine-gear-spin-ccw {
      from { transform: rotate(0deg); }
      to { transform: rotate(-360deg); }
    }
    .machine-gear {
      position: absolute;
      z-index: 2;
      opacity: 0.54;
      pointer-events: none;
      color: #B87333;
    }
    .machine-gear svg {
      display: block;
      transform-origin: center;
    }
    .machine-gear--one {
      width: 130px;
      height: 130px;
      left: 17%;
      top: 24%;
    }
    .machine-gear--two {
      width: 98px;
      height: 98px;
      right: 23%;
      bottom: 19%;
    }
    .machine-gear--one svg {
      animation: machine-gear-spin-cw 9.2s linear infinite;
      animation-play-state: paused;
    }
    .machine-gear--two svg {
      animation: machine-gear-spin-ccw 7.4s linear infinite;
      animation-play-state: paused;
    }
    #hero.hero-gears-on .machine-gear--one svg,
    #hero.hero-gears-on .machine-gear--two svg {
      animation-play-state: running;
    }

    /* Stamp badge */
    .stamp-badge {
      border: 3px solid #B87333;
      border-radius: 50%;
      width: 120px;
      height: 120px;
      display: flex;
      align-items: center;
      justify-content: center;
      text-align: center;
      font-family: 'Bitter', serif;
      color: #B87333;
      font-size: 0.75rem;
      font-weight: 700;
      letter-spacing: 0.15em;
      text-transform: uppercase;
      line-height: 1.4;
      position: relative;
    }
    .stamp-badge::before,
    .stamp-badge::after {
      content: '';
      position: absolute;
      border-radius: 50%;
      border: 1px solid rgba(184, 115, 51, 0.4);
    }
    .stamp-badge::before {
      inset: 4px;
    }
    .stamp-badge::after {
      inset: -6px;
      border-style: dashed;
    }

    /* Copper divider */
    .copper-divider {
      height: 2px;
      background: linear-gradient(90deg, transparent, #B87333, #C9A84C, #B87333, transparent);
    }

    /* FAQ accordion */
    .faq-answer {
      max-height: 0;
      overflow: hidden;
      transition: max-height 0.4s ease, padding 0.3s ease;
    }
    .faq-item.open .faq-answer {
      max-height: 200px;
    }
    .faq-item.open .faq-chevron {
      transform: rotate(180deg);
    }
    .faq-chevron {
      transition: transform 0.3s ease;
    }

    /* Card hover */
    .gallery-item img {
      transition: transform 0.4s ease, box-shadow 0.4s ease;
    }
    .gallery-item:hover img {
      transform: scale(1.03);
      box-shadow: 0 8px 30px rgba(92, 61, 46, 0.25);
    }

    /* ===== Catalog (termekek) – match 6-3 mock ===== */
    /* Tailwind utilities missing from compiled app.css (PHP templates not scanned by Vite) */
    .pb-12 { padding-bottom: calc(var(--spacing) * 12); }
    .top-16 { top: calc(var(--spacing) * 16); }
    @media (min-width: 40rem) {
      .sm\:pt-32 { padding-top: calc(var(--spacing) * 32); }
      .sm\:pb-16 { padding-bottom: calc(var(--spacing) * 16); }
      .sm\:py-16 { padding-block: calc(var(--spacing) * 16); }
    }
    @media (min-width: 80rem) {
      .xl\:grid-cols-4 { grid-template-columns: repeat(4, minmax(0, 1fr)); }
    }

    .post-type-archive-fabrika_termek .filter-btn {
      transition: all 0.2s ease;
    }
    .post-type-archive-fabrika_termek .filter-btn.active {
      background: linear-gradient(135deg, #B87333, #C9A84C) !important;
      color: #FFFBF5 !important;
      border-color: transparent !important;
    }
    .post-type-archive-fabrika_termek .product-card {
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .post-type-archive-fabrika_termek .product-card:hover {
      transform: translateY(-4px);
      box-shadow: 0 8px 30px rgba(92, 61, 46, 0.2);
    }
    .post-type-archive-fabrika_termek .product-card img {
      transition: transform 0.4s ease;
    }
    .post-type-archive-fabrika_termek .product-card:hover img {
      transform: scale(1.05);
    }
    .post-type-archive-fabrika_termek .tag-badge {
      font-size: 0.7rem;
      padding: 2px 8px;
      border-radius: 9999px;
      background: rgba(184, 115, 51, 0.1);
      color: #B87333;
      font-weight: 600;
      white-space: nowrap;
    }
    .post-type-archive-fabrika_termek .product-id {
      position: absolute;
      top: 8px;
      left: 8px;
      background: rgba(59, 35, 20, 0.85);
      backdrop-filter: blur(4px);
      color: #E8DCC8;
      font-size: 0.65rem;
      font-weight: 700;
      letter-spacing: 0.08em;
      padding: 3px 8px;
      border-radius: 4px;
      font-family: 'Source Sans 3', monospace;
      z-index: 1;
    }

    /* Category card rotations */
    .cat-card:nth-child(odd) { transform: rotate(-0.5deg); }
    .cat-card:nth-child(even) { transform: rotate(0.5deg); }
    .cat-card:hover { transform: rotate(0deg) translateY(-4px) !important; }
    .cat-card { transition: transform 0.3s ease, box-shadow 0.3s ease; }

    /* Back to top */
    .back-to-top {
      opacity: 0;
      pointer-events: none;
      transition: opacity 0.3s ease;
    }
    .back-to-top.visible {
      opacity: 1;
      pointer-events: auto;
    }

    /* Step connector */
    .step-connector {
      border-left: 2px dashed #B87333;
      position: absolute;
      left: 28px;
      top: 56px;
      bottom: -32px;
    }

    /* Mobile menu */
    .mobile-menu {
      max-height: 0;
      overflow: hidden;
      transition: max-height 0.3s ease;
    }
    .mobile-menu.open {
      max-height: 300px;
    }

    /* Navbar scroll state */
    .navbar-scrolled {
      background-color: rgba(59, 35, 20, 0.97) !important;
      box-shadow: 0 2px 16px rgba(42, 32, 24, 0.2);
    }

    /* Contact Form 7 styling */
    .wpcf7-form {
      background-color: #FFFBF5;
      border: 1px solid rgba(184, 115, 51, 0.15);
      box-shadow: 0 4px 20px rgba(92, 61, 46, 0.08);
      border-radius: 0.75rem;
      padding: 1.5rem;
    }
    @media (min-width: 640px) {
      .wpcf7-form { padding: 2rem; }
    }
    .fabrika-cf7-grid { display: flex; flex-direction: column; gap: 1.25rem; }
    .fabrika-cf7-row { display: grid; grid-template-columns: 1fr; gap: 1.25rem; }
    @media (min-width: 640px) {
      .fabrika-cf7-row { grid-template-columns: 1fr 1fr; }
    }
    .wpcf7-form label {
      display: block;
      font-size: 0.875rem;
      font-weight: 600;
      color: #3B2314;
      margin-bottom: 0.375rem;
    }
    .wpcf7-form input[type="text"],
    .wpcf7-form input[type="email"],
    .wpcf7-form input[type="tel"],
    .wpcf7-form select,
    .wpcf7-form textarea {
      width: 100%;
      border-radius: 0.5rem;
      border: 1px solid rgba(184, 115, 51, 0.25);
      background: #FFFBF5;
      padding: 0.625rem 1rem;
      font-size: 0.875rem;
      outline: none;
      transition: box-shadow 0.2s ease;
    }
    .wpcf7-form input:focus,
    .wpcf7-form select:focus,
    .wpcf7-form textarea:focus {
      box-shadow: 0 0 0 2px #B87333;
    }
    .wpcf7-form textarea { resize: vertical; }
    .wpcf7-form input[type="submit"] {
      width: 100%;
      padding: 0.75rem 2rem;
      border-radius: 0.5rem;
      font-weight: 600;
      color: #FFFBF5;
      background: linear-gradient(135deg, #B87333, #C9A84C);
      box-shadow: 0 4px 15px rgba(184, 115, 51, 0.3);
      cursor: pointer;
      border: none;
      font-size: 1rem;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    @media (min-width: 640px) {
      .wpcf7-form input[type="submit"] { width: auto; }
    }
    .wpcf7-form input[type="submit"]:hover {
      transform: scale(1.05);
      box-shadow: 0 6px 20px rgba(184, 115, 51, 0.4);
    }
    .wpcf7-form .wpcf7-response-output {
      border-color: #B87333;
      color: #3B2314;
      margin: 1rem 0 0;
      padding: 0.75rem 1rem;
      border-radius: 0.5rem;
    }
    .wpcf7-form .wpcf7-not-valid-tip {
      color: #c0392b;
      font-size: 0.8rem;
      margin-top: 0.25rem;
    }

    /* WPForms styling (match theme look used by fallback / CF7 form) */
    .wpforms-container .wpforms-form {
      background-color: #FFFBF5;
      border: 1px solid rgba(184, 115, 51, 0.15);
      box-shadow: 0 4px 20px rgba(92, 61, 46, 0.08);
      border-radius: 0.75rem;
      padding: 1.5rem;
    }
    @media (min-width: 640px) {
      .wpforms-container .wpforms-form { padding: 2rem; }
    }
    .wpforms-container .wpforms-field-label {
      display: block;
      font-size: 0.875rem;
      font-weight: 600;
      color: #3B2314;
      margin-bottom: 0.375rem;
    }
    /* WPForms "label hide" should still be visible in this theme to match local layout. */
    .wpforms-container .wpforms-field-label.wpforms-label-hide {
      position: static !important;
      width: auto !important;
      height: auto !important;
      margin: 0 0 0.375rem !important;
      clip: auto !important;
      overflow: visible !important;
    }
    .wpforms-container .wpforms-field-container {
      display: grid;
      grid-template-columns: 1fr;
      gap: 1rem 1rem;
    }
    @media (min-width: 640px) {
      .wpforms-container .wpforms-field-container {
        grid-template-columns: repeat(2, minmax(0, 1fr));
      }
    }
    .wpforms-container .wpforms-field { margin: 0 !important; }
    /* Unified ordering by WPForms field ID (works for local and live). */
    .wpforms-container .wpforms-field[data-field-id="0"] { order: 1; } /* Név */
    .wpforms-container .wpforms-field[data-field-id="1"] { order: 2; } /* E-mail */
    .wpforms-container .wpforms-field[data-field-id="7"] { order: 3; } /* Telefon */
    .wpforms-container .wpforms-field[data-field-id="4"] { order: 4; } /* Kategória */
    .wpforms-container .wpforms-field[data-field-id="8"] { order: 5; grid-column: 1 / -1; } /* Termék */
    .wpforms-container .wpforms-field[data-field-id="2"] { order: 6; grid-column: 1 / -1; } /* Üzenet */
    .wpforms-container .wpforms-field-hp { display: none !important; }
    .wpforms-container .wpforms-field[style*="z-index: -1000"] { display: none !important; }
    .wpforms-container .wpforms-field input[type="text"],
    .wpforms-container .wpforms-field input[type="email"],
    .wpforms-container .wpforms-field input[type="tel"],
    .wpforms-container .wpforms-field select,
    .wpforms-container .wpforms-field textarea {
      width: 100%;
      border-radius: 0.5rem;
      border: 1px solid rgba(184, 115, 51, 0.25);
      background: #FFFBF5;
      padding: 0.625rem 1rem;
      font-size: 0.875rem;
      outline: none;
      transition: box-shadow 0.2s ease;
      max-width: none !important;
    }
    .wpforms-container .wpforms-field input:focus,
    .wpforms-container .wpforms-field select:focus,
    .wpforms-container .wpforms-field textarea:focus {
      box-shadow: 0 0 0 2px #B87333;
    }
    .wpforms-container .wpforms-field textarea { resize: vertical; }
    .wpforms-container .wpforms-submit {
      width: 100%;
      padding: 0.75rem 2rem;
      border-radius: 0.5rem;
      font-weight: 600;
      color: #FFFBF5;
      background: linear-gradient(135deg, #B87333, #C9A84C);
      box-shadow: 0 4px 15px rgba(184, 115, 51, 0.3);
      cursor: pointer;
      border: none;
      font-size: 1rem;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    @media (min-width: 640px) {
      .wpforms-container .wpforms-submit { width: auto; }
    }
    .wpforms-container .wpforms-submit:hover {
      transform: scale(1.05);
      box-shadow: 0 6px 20px rgba(184, 115, 51, 0.4);
    }
    .wpforms-container .wpforms-confirmation-container,
    .wpforms-container .wpforms-error-container {
      border: 1px solid #B87333;
      color: #3B2314;
      margin-top: 1rem;
      padding: 0.75rem 1rem;
      border-radius: 0.5rem;
      background: #FFFBF5;
    }
    .wpforms-container .wpforms-field.wpforms-has-error input,
    .wpforms-container .wpforms-field.wpforms-has-error textarea,
    .wpforms-container .wpforms-field.wpforms-has-error select {
      border-color: #c0392b;
      box-shadow: 0 0 0 1px rgba(192, 57, 43, 0.2);
    }
    .wpforms-container .wpforms-error {
      color: #c0392b;
      font-size: 0.8rem;
      margin-top: 0.25rem;
    }
  </style>
</head>
<body <?php body_class('bg-[#FFFBF5] antialiased'); ?>>
<?php wp_body_open(); ?>

  <!-- Fixed Navbar -->
  <nav id="navbar" class="fixed top-0 left-0 right-0 z-50 transition-all duration-300" style="background-color: rgba(59, 35, 20, 0.85); backdrop-filter: blur(8px);">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex items-center justify-between h-16">
        <!-- Brand -->
        <?php $brand_href = is_front_page() ? '#hero' : home_url('/'); ?>
        <a href="<?php echo esc_url($brand_href); ?>" class="flex items-center gap-2 text-[#FFFBF5] font-bold text-lg" style="font-family: 'Bitter', serif;">
          <svg class="w-7 h-7 text-[#B87333]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="12" y1="18" x2="12" y2="12"/><line x1="9" y1="15" x2="15" y2="15"/></svg>
          <?php echo esc_html(fabrika62_opt_str('brand_name', 'Fabrika Ajándék')); ?>
        </a>
        <!-- Desktop Nav -->
        <div class="hidden md:flex items-center gap-8">
          <?php fabrika62_render_anchor_menu('text-[#F4EDE4] hover:text-[#B87333] transition-colors text-sm font-medium tracking-wide'); ?>
        </div>
        <!-- Hamburger -->
        <button id="menu-toggle" class="md:hidden text-[#FFFBF5] p-2" aria-label="Menu">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>
      </div>
      <!-- Mobile Menu -->
      <div id="mobile-menu" class="mobile-menu md:hidden">
        <div class="pb-4 pt-2 flex flex-col gap-3">
          <?php fabrika62_render_anchor_menu('text-[#F4EDE4] hover:text-[#B87333] transition-colors text-sm font-medium tracking-wide mobile-link'); ?>
        </div>
      </div>
    </div>
  </nav>
