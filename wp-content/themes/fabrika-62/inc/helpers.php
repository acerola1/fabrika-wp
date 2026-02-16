<?php

declare(strict_types=1);

function fabrika62_default_options(): array
{
    return [
        'meta_description' => 'Kezmuves fa ajandekok, lezergravirozas, fa tablak, kerti diszek es egyedi nyomatok. Fabrika Ajandek, Szarvas.',
        'brand_name' => 'Fabrika Ajandek',
        'hero_badge' => 'Est. 2024<br/>&middot; Szarvas &middot;',
        'hero_title' => 'Fabrika Ajandek',
        'hero_subtitle' => 'Kezmuves fa ajandekok &ndash; lezergravirozas, fa tablak, kerti diszek, egyedi nyomatok',
        'hero_cta_label' => 'Egyedi rendeles',
        'hero_cta_href' => '#kapcsolat',
        'products_title' => 'Amit a muhelyben keszitunk',
        'gallery_title' => 'Munkaink',
        'order_title' => 'Hogyan rendelhetsz?',
        'gifts_title' => 'Mikor adj kezmuveset?',
        'market_title' => 'Keress minket szemelyesen!',
        'market_lead' => 'Szarvasi es kornyekbeli piacok, vasarok, rendezvenyek.',
        'market_sub' => 'Szemelyes atvetel egyeztetessel.',
        'contact_title' => 'Irj nekunk!',
        'contact_form_shortcode' => '',
        'contact_email' => 'info@fabrikaajandek.hu',
        'contact_facebook_label' => 'Fabrika Ajandek',
        'contact_facebook_url' => '#',
        'contact_viber' => '+36 XX XXX XXXX',
        'contact_instagram' => '@fabrikaajandek',
        'contact_instagram_url' => '#',
        'faq_title' => 'Gyakori kerdesek',
        'footer_location' => 'Szarvas, Magyarorszag',
        'footer_facebook_url' => '#',
        'footer_instagram_url' => '#',
        'product_categories' => [
            ['image' => '01-laser-engraved-magnet.jpg', 'title' => 'Lezergravirozott kepek', 'description' => 'Portrek es mintak, lezertechnologiaval faba vesve.'],
            ['image' => '03-handmade-wood-sign-shop-1.jpg', 'title' => 'Fa tablak / feliratok', 'description' => 'Szemelyre szabott feliratok barmilyen alkalomra.'],
            ['image' => '05-fridge-magnets.jpg', 'title' => 'Kis ajandektargyak', 'description' => 'Hutomagnesek, kulcstartok es apro kedvessegek.'],
            ['image' => '07-terrariums-florist.jpg', 'title' => 'Kerti diszek / viragtartok', 'description' => 'Kezzel keszitett dekoraciok a szabadba.'],
            ['image' => '09-california-travel-poster.jpg', 'title' => 'Szines nyomatok', 'description' => 'Egyedi grafikak es illusztraciok, nyomtatva.'],
        ],
        'gallery_items' => [
            ['image' => '01-laser-engraved-magnet.jpg', 'alt' => 'Lezergravirozott magnes'],
            ['image' => '02-laser-engraved-wall-plaque.jpg', 'alt' => 'Lezergravirozott fali tabla'],
            ['image' => '03-handmade-wood-sign-shop-1.jpg', 'alt' => 'Kezmuves fa tabla'],
            ['image' => '04-waterfront-rules-wood-sign.jpg', 'alt' => 'Szabalyok fa tabla'],
            ['image' => '05-fridge-magnets.jpg', 'alt' => 'Hutomagnesek'],
            ['image' => '06-picnic-basket-gifts.jpg', 'alt' => 'Piknik kosar ajandek'],
            ['image' => '07-terrariums-florist.jpg', 'alt' => 'Terrariumok'],
            ['image' => '08-blue-painted-wall-planters.jpg', 'alt' => 'Fali viragtartok'],
            ['image' => '09-california-travel-poster.jpg', 'alt' => 'Poszter nyomat'],
            ['image' => '10-jane-avril-poster.jpg', 'alt' => 'Muveszi poszter'],
        ],
        'order_steps' => [
            ['title' => 'Meselj az otletedrol', 'description' => 'Valassz meglevo termekeket, vagy ird le az egyedi elkepzelesed.'],
            ['title' => 'Egyeztetunk', 'description' => 'Megbeszeljuk a reszleteket, az arat es az atvetel modjat.'],
            ['title' => 'Elkeszul es tied', 'description' => 'Szemelyes atvetel keszpenzzel, postanal elore utalas.'],
        ],
        'gift_ideas' => [
            ['icon' => 'gift', 'title' => 'Szuletesnap', 'description' => 'Gravirozott keret vagy egyedi fa tabla.'],
            ['icon' => 'heart', 'title' => 'Eskuvo', 'description' => 'Szemelyes eskuvoi emlektabla.'],
            ['icon' => 'user', 'title' => 'Nevnap', 'description' => 'Apro meglepetes: magnes vagy kulcstarto.'],
            ['icon' => 'sparkles', 'title' => 'Karacsony', 'description' => 'Fabol keszult unnepi ajandekok.'],
            ['icon' => 'smile', 'title' => 'Csak ugy', 'description' => 'A legjobb ajandek: amire nem szamit.'],
        ],
        'faq_items' => [
            ['question' => 'Mennyi ido alatt keszul el egy egyedi termek?', 'answer' => 'Altalaban 3-7 munkanap.'],
            ['question' => 'Hogyan fizethetek?', 'answer' => 'Szemelyesen: keszpenz. Egyedi/posta: elore utalas.'],
            ['question' => 'Van postazas?', 'answer' => 'Igen, Magyarorszagon belul.'],
            ['question' => 'Kulfoldre is kuldtok?', 'answer' => 'Jelenleg csak belfoldre.'],
            ['question' => 'Sajat otletet is megvalositotok?', 'answer' => 'Persze! Kuldj leirast, kepet vagy vazlatot.'],
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
