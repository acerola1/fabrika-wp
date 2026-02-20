<?php

declare(strict_types=1);

function fabrika62_default_options(): array
{
    return [
        'meta_description' => 'Kézműves fa ajándékok, lézergravírozás, fa táblák, kerti díszek és egyedi nyomatok. Fabrika Ajándék, Szarvas.',
        'brand_name' => 'Fabrika Ajándék',
        'hero_badge' => 'Est. 2024<br/>&middot; Szarvas &middot;',
        'hero_title' => 'Fabrika Ajándék',
        'hero_subtitle' => 'Kézműves fa ajándékok &ndash; lézergravírozás, fa táblák, kerti díszek, egyedi nyomatok',
        'hero_cta_label' => 'Egyedi rendelés',
        'hero_cta_href' => '#kapcsolat',
        'hero_cta2_label' => 'Katalógus',
        'hero_cta2_href' => '/termekek/',
        'catalog_title' => 'Termékkatalógus',
        'catalog_subtitle' => 'Böngészd át kézműves termékeinket! Ha valami megtetszik, kattints az „Érdekel" gombra és írj nekünk.',
        'catalog_filter_all_label' => 'Összes',
        'catalog_no_image_label' => 'Nincs kép',
        'catalog_price_contact_label' => 'Ár egyeztetéssel',
        'catalog_interest_label' => 'Érdekel',
        'catalog_empty_filtered_label' => 'Ebben a kategóriában még nincsenek termékek.',
        'catalog_show_all_button_label' => 'Összes termék mutatása',
        'catalog_empty_label' => 'Még nincsenek termékek.',
        'catalog_cta_title' => 'Nem találtad, amit kerestél?',
        'catalog_cta_text' => 'Írj nekünk és készítünk bármit egyedire! Egyedi méretben, felirattal, saját képed alapján.',
        'catalog_cta_button_label' => 'Egyedi rendelés',
        'products_title' => 'Amit a műhelyben készítünk',
        'gallery_title' => 'Munkáink',
        'order_title' => 'Hogyan rendelhetsz?',
        'gifts_title' => 'Mikor adj kézműveset?',
        'market_title' => 'Keress minket személyesen!',
        'market_lead' => 'Szarvasi és környékbeli piacok, vásárok, rendezvények.',
        'market_sub' => 'Személyes átvétel egyeztetéssel.',
        'contact_title' => 'Írj nekünk!',
        'contact_form_shortcode' => '',
        'contact_email' => 'info@fabrikaajandek.hu',
        'contact_facebook_label' => 'Fabrika Ajándék',
        'contact_facebook_url' => '#',
        'contact_viber' => '+36 XX XXX XXXX',
        'contact_instagram' => '@fabrikaajandek',
        'contact_instagram_url' => '#',
        'faq_title' => 'Gyakori kérdések',
        'footer_location' => 'Szarvas, Magyarország',
        'footer_facebook_url' => '#',
        'footer_instagram_url' => '#',
        'product_categories' => [
            ['image' => '01-laser-engraved-magnet.jpg', 'title' => 'Lézergravírozott képek', 'slug' => 'lezer-gravirozott-kepek', 'description' => 'Portrék és minták, lézertechnológiával fába vésve.'],
            ['image' => '03-handmade-wood-sign-shop-1.jpg', 'title' => 'Fa táblák / feliratok', 'slug' => 'fa-tablak', 'description' => 'Személyre szabott feliratok bármilyen alkalomra.'],
            ['image' => '05-fridge-magnets.jpg', 'title' => 'Kis ajándéktárgyak', 'slug' => 'kis-ajandektargyak', 'description' => 'Hűtőmágnesek, kulcstartók és apró kedvességek.'],
            ['image' => '07-terrariums-florist.jpg', 'title' => 'Kerti díszek / virágtatók', 'slug' => 'kerti-diszek', 'description' => 'Kézzel készített dekorációk a szabadba.'],
            ['image' => '09-california-travel-poster.jpg', 'title' => 'Színes nyomatok', 'slug' => 'szines-nyomatok', 'description' => 'Egyedi grafikák és illusztrációk, nyomtatva.'],
        ],
        'gallery_items' => [
            ['image' => '01-laser-engraved-magnet.jpg', 'alt' => 'Lézergravírozott mágnes'],
            ['image' => '02-laser-engraved-wall-plaque.jpg', 'alt' => 'Lézergravírozott fali tábla'],
            ['image' => '03-handmade-wood-sign-shop-1.jpg', 'alt' => 'Kézműves fa tábla'],
            ['image' => '04-waterfront-rules-wood-sign.jpg', 'alt' => 'Szabályok fa tábla'],
            ['image' => '05-fridge-magnets.jpg', 'alt' => 'Hűtőmágnesek'],
            ['image' => '06-picnic-basket-gifts.jpg', 'alt' => 'Piknik kosár ajándék'],
            ['image' => '07-terrariums-florist.jpg', 'alt' => 'Terráriumok'],
            ['image' => '08-blue-painted-wall-planters.jpg', 'alt' => 'Fali virágtatók'],
            ['image' => '09-california-travel-poster.jpg', 'alt' => 'Poszter nyomat'],
            ['image' => '10-jane-avril-poster.jpg', 'alt' => 'Művészi poszter'],
        ],
        'order_steps' => [
            ['title' => 'Mesélj az ötletedről', 'description' => 'Válassz meglévő termékeket, vagy írd le az egyedi elképzelésed.'],
            ['title' => 'Egyeztetünk', 'description' => 'Megbeszéljük a részleteket, az árat és az átvétel módját.'],
            ['title' => 'Elkészül és tied', 'description' => 'Személyes átvétel készpénzzel, postánál előre utalás.'],
        ],
        'gift_ideas' => [
            ['icon' => 'gift', 'title' => 'Születésnap', 'description' => 'Gravírozott keret vagy egyedi fa tábla.'],
            ['icon' => 'heart', 'title' => 'Esküvő', 'description' => 'Személyes esküvői emléktábla.'],
            ['icon' => 'user', 'title' => 'Névnap', 'description' => 'Apró meglepetés: mágnes vagy kulcstartó.'],
            ['icon' => 'sparkles', 'title' => 'Karácsony', 'description' => 'Fából készült ünnepi ajándékok.'],
            ['icon' => 'smile', 'title' => 'Csak úgy', 'description' => 'A legjobb ajándék: amire nem számít.'],
        ],
        'faq_items' => [
            ['question' => 'Mennyi idő alatt készül el egy egyedi termék?', 'answer' => 'Általában 3-7 munkanap.'],
            ['question' => 'Hogyan fizethetek?', 'answer' => 'Személyesen: készpénz. Egyedi/posta: előre utalás.'],
            ['question' => 'Van postázás?', 'answer' => 'Igen, Magyarországon belül.'],
            ['question' => 'Külföldre is küldtök?', 'answer' => 'Jelenleg csak belföldre.'],
            ['question' => 'Saját ötletet is megvalósítotok?', 'answer' => 'Persze! Küldj leírást, képet vagy vázlatot.'],
        ],
    ];
}

function fabrika62_get_options(): array
{
    $stored = get_option('fabrika62_options', []);
    if (!is_array($stored)) {
        $stored = [];
    }
    return array_merge(fabrika62_default_options(), $stored);
}

function fabrika62_opt(string $field_name, mixed $default = null): mixed
{
    $options = fabrika62_get_options();
    if (array_key_exists($field_name, $options)) {
        $value = $options[$field_name];
        if ($value === null || $value === false || $value === '') {
            return $default;
        }
        return $value;
    }
    return $default;
}

function fabrika62_opt_str(string $field_name, string $default = ''): string
{
    $val = fabrika62_opt($field_name, $default);
    return is_string($val) ? $val : $default;
}

/**
 * Returns a WordPress image URL from an ACF image field value.
 * Supports return formats: array, ID, URL.
 */
function fabrika62_image_url(mixed $acf_image, string $fallback_url = ''): string
{
    if (is_array($acf_image) && isset($acf_image['url'])) {
        return (string) $acf_image['url'];
    }
    if (is_int($acf_image) || (is_string($acf_image) && ctype_digit($acf_image))) {
        $url = wp_get_attachment_image_url((int) $acf_image, 'full');
        return $url ? (string) $url : $fallback_url;
    }
    if (is_string($acf_image) && $acf_image !== '') {
        if (!str_starts_with($acf_image, 'http://') && !str_starts_with($acf_image, 'https://') && !str_starts_with($acf_image, '/')) {
            return get_stylesheet_directory_uri() . '/assets/references/' . ltrim($acf_image, '/');
        }
        return $acf_image;
    }
    return $fallback_url;
}

function fabrika62_post_square_thumbnail(int $post_id, string $size = 'square_600', array $attrs = []): string
{
    $thumb_id = get_post_thumbnail_id($post_id);
    if (!$thumb_id) {
        return '';
    }
    $attrs = array_merge(['loading' => 'lazy', 'decoding' => 'async'], $attrs);
    $html = wp_get_attachment_image((int) $thumb_id, $size, false, $attrs);
    return is_string($html) ? $html : '';
}
