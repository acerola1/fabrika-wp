<?php

declare(strict_types=1);

function fabrika62_register_acf_options(): void
{
    if (!function_exists('acf_add_options_page') || !function_exists('acf_add_local_field_group')) {
        return;
    }

    acf_add_options_page([
        'page_title' => 'Fabrika Kezdolap',
        'menu_title' => 'Fabrika Kezdolap',
        'menu_slug' => 'fabrika62-home',
        'capability' => 'edit_posts',
        'redirect' => false,
        'position' => 61,
        'icon_url' => 'dashicons-admin-customizer',
    ]);

    acf_add_local_field_group([
        'key' => 'group_fabrika62_home',
        'title' => 'Fabrika Kezdolap Tartalom',
        'fields' => [
            [
                'key' => 'field_fabrika62_tab_general',
                'label' => 'Altalanos',
                'type' => 'tab',
                'placement' => 'top',
            ],
            [
                'key' => 'field_fabrika62_meta_description',
                'label' => 'Meta description',
                'name' => 'meta_description',
                'type' => 'textarea',
                'rows' => 2,
                'new_lines' => '',
            ],
            [
                'key' => 'field_fabrika62_brand_name',
                'label' => 'Brand nev',
                'name' => 'brand_name',
                'type' => 'text',
            ],

            [
                'key' => 'field_fabrika62_tab_hero',
                'label' => 'Hero',
                'type' => 'tab',
                'placement' => 'top',
            ],
            [
                'key' => 'field_fabrika62_hero_badge',
                'label' => 'Hero badge szöveg',
                'name' => 'hero_badge',
                'type' => 'text',
            ],
            [
                'key' => 'field_fabrika62_hero_title',
                'label' => 'Hero cim',
                'name' => 'hero_title',
                'type' => 'text',
            ],
            [
                'key' => 'field_fabrika62_hero_subtitle',
                'label' => 'Hero alcim',
                'name' => 'hero_subtitle',
                'type' => 'textarea',
                'rows' => 2,
                'new_lines' => 'br',
            ],
            [
                'key' => 'field_fabrika62_hero_cta_label',
                'label' => 'Hero CTA felirat',
                'name' => 'hero_cta_label',
                'type' => 'text',
            ],
            [
                'key' => 'field_fabrika62_hero_cta_href',
                'label' => 'Hero CTA link',
                'name' => 'hero_cta_href',
                'type' => 'text',
                'instructions' => 'Pl. #kapcsolat vagy https://...',
            ],
            [
                'key' => 'field_fabrika62_tab_catalog',
                'label' => 'Katalógus oldal',
                'type' => 'tab',
                'placement' => 'top',
            ],
            [
                'key' => 'field_fabrika62_catalog_title',
                'label' => 'Oldalcím',
                'name' => 'catalog_title',
                'type' => 'text',
            ],
            [
                'key' => 'field_fabrika62_catalog_subtitle',
                'label' => 'Fejléc alcím',
                'name' => 'catalog_subtitle',
                'type' => 'textarea',
                'rows' => 2,
                'new_lines' => 'br',
            ],
            [
                'key' => 'field_fabrika62_catalog_filter_all_label',
                'label' => 'Szűrő: Összes gomb',
                'name' => 'catalog_filter_all_label',
                'type' => 'text',
            ],
            [
                'key' => 'field_fabrika62_catalog_no_image_label',
                'label' => 'Kártya: nincs kép felirat',
                'name' => 'catalog_no_image_label',
                'type' => 'text',
            ],
            [
                'key' => 'field_fabrika62_catalog_price_contact_label',
                'label' => 'Kártya: ár hiányzik felirat',
                'name' => 'catalog_price_contact_label',
                'type' => 'text',
            ],
            [
                'key' => 'field_fabrika62_catalog_interest_label',
                'label' => 'Kártya: CTA gomb felirat',
                'name' => 'catalog_interest_label',
                'type' => 'text',
            ],
            [
                'key' => 'field_fabrika62_catalog_empty_filtered_label',
                'label' => 'Üres találat (szűrt) szöveg',
                'name' => 'catalog_empty_filtered_label',
                'type' => 'text',
            ],
            [
                'key' => 'field_fabrika62_catalog_show_all_button_label',
                'label' => 'Üres találat: összes gomb',
                'name' => 'catalog_show_all_button_label',
                'type' => 'text',
            ],
            [
                'key' => 'field_fabrika62_catalog_empty_label',
                'label' => 'Üres találat (nincs termék) szöveg',
                'name' => 'catalog_empty_label',
                'type' => 'text',
            ],
            [
                'key' => 'field_fabrika62_catalog_cta_title',
                'label' => 'Alsó CTA cím',
                'name' => 'catalog_cta_title',
                'type' => 'text',
            ],
            [
                'key' => 'field_fabrika62_catalog_cta_text',
                'label' => 'Alsó CTA leírás',
                'name' => 'catalog_cta_text',
                'type' => 'textarea',
                'rows' => 2,
                'new_lines' => 'br',
            ],
            [
                'key' => 'field_fabrika62_catalog_cta_button_label',
                'label' => 'Alsó CTA gomb felirat',
                'name' => 'catalog_cta_button_label',
                'type' => 'text',
            ],

            [
                'key' => 'field_fabrika62_tab_products',
                'label' => 'Termékkategóriák',
                'type' => 'tab',
                'placement' => 'top',
            ],
            [
                'key' => 'field_fabrika62_products_title',
                'label' => 'Szekció cím',
                'name' => 'products_title',
                'type' => 'text',
            ],
            [
                'key' => 'field_fabrika62_product_categories',
                'label' => 'Kategóriák',
                'name' => 'product_categories',
                'type' => 'repeater',
                'min' => 0,
                'layout' => 'row',
                'button_label' => 'Új kártya',
                'sub_fields' => [
                    [
                        'key' => 'field_fabrika62_product_cat_image',
                        'label' => 'Kép',
                        'name' => 'image',
                        'type' => 'image',
                        'return_format' => 'array',
                        'preview_size' => 'medium',
                        'library' => 'all',
                    ],
                    [
                        'key' => 'field_fabrika62_product_cat_title',
                        'label' => 'Cím',
                        'name' => 'title',
                        'type' => 'text',
                    ],
                    [
                        'key' => 'field_fabrika62_product_cat_desc',
                        'label' => 'Leírás',
                        'name' => 'description',
                        'type' => 'textarea',
                        'rows' => 2,
                        'new_lines' => 'br',
                    ],
                ],
            ],

            [
                'key' => 'field_fabrika62_tab_gallery',
                'label' => 'Galéria',
                'type' => 'tab',
                'placement' => 'top',
            ],
            [
                'key' => 'field_fabrika62_gallery_title',
                'label' => 'Szekció cím',
                'name' => 'gallery_title',
                'type' => 'text',
            ],
            [
                'key' => 'field_fabrika62_gallery_items',
                'label' => 'Képek',
                'name' => 'gallery_items',
                'type' => 'repeater',
                'min' => 0,
                'layout' => 'row',
                'button_label' => 'Új kép',
                'sub_fields' => [
                    [
                        'key' => 'field_fabrika62_gallery_image',
                        'label' => 'Kép',
                        'name' => 'image',
                        'type' => 'image',
                        'return_format' => 'array',
                        'preview_size' => 'medium',
                        'library' => 'all',
                    ],
                    [
                        'key' => 'field_fabrika62_gallery_alt',
                        'label' => 'Alt szöveg',
                        'name' => 'alt',
                        'type' => 'text',
                    ],
                ],
            ],

            [
                'key' => 'field_fabrika62_tab_order',
                'label' => 'Rendelés',
                'type' => 'tab',
                'placement' => 'top',
            ],
            [
                'key' => 'field_fabrika62_order_title',
                'label' => 'Szekció cím',
                'name' => 'order_title',
                'type' => 'text',
            ],
            [
                'key' => 'field_fabrika62_order_steps',
                'label' => 'Lépések',
                'name' => 'order_steps',
                'type' => 'repeater',
                'min' => 0,
                'layout' => 'row',
                'button_label' => 'Új lépés',
                'sub_fields' => [
                    [
                        'key' => 'field_fabrika62_order_step_title',
                        'label' => 'Cím',
                        'name' => 'title',
                        'type' => 'text',
                    ],
                    [
                        'key' => 'field_fabrika62_order_step_desc',
                        'label' => 'Leírás',
                        'name' => 'description',
                        'type' => 'textarea',
                        'rows' => 2,
                        'new_lines' => 'br',
                    ],
                ],
            ],

            [
                'key' => 'field_fabrika62_tab_gifts',
                'label' => 'Ajándékötletek',
                'type' => 'tab',
                'placement' => 'top',
            ],
            [
                'key' => 'field_fabrika62_gifts_title',
                'label' => 'Szekció cím',
                'name' => 'gifts_title',
                'type' => 'text',
            ],
            [
                'key' => 'field_fabrika62_gift_ideas',
                'label' => 'Ötletek',
                'name' => 'gift_ideas',
                'type' => 'repeater',
                'min' => 0,
                'layout' => 'row',
                'button_label' => 'Új ötlet',
                'sub_fields' => [
                    [
                        'key' => 'field_fabrika62_gift_icon',
                        'label' => 'Ikon',
                        'name' => 'icon',
                        'type' => 'select',
                        'choices' => [
                            'gift' => 'Ajándék',
                            'heart' => 'Szív',
                            'user' => 'Ember',
                            'sparkles' => 'Csillag',
                            'smile' => 'Mosoly',
                        ],
                        'default_value' => 'gift',
                        'ui' => 1,
                    ],
                    [
                        'key' => 'field_fabrika62_gift_title',
                        'label' => 'Cím',
                        'name' => 'title',
                        'type' => 'text',
                    ],
                    [
                        'key' => 'field_fabrika62_gift_desc',
                        'label' => 'Leírás',
                        'name' => 'description',
                        'type' => 'textarea',
                        'rows' => 2,
                        'new_lines' => 'br',
                    ],
                ],
            ],

            [
                'key' => 'field_fabrika62_tab_market',
                'label' => 'Piaci megjelenés',
                'type' => 'tab',
                'placement' => 'top',
            ],
            [
                'key' => 'field_fabrika62_market_title',
                'label' => 'Szekció cím',
                'name' => 'market_title',
                'type' => 'text',
            ],
            [
                'key' => 'field_fabrika62_market_lead',
                'label' => 'Fő szöveg',
                'name' => 'market_lead',
                'type' => 'text',
            ],
            [
                'key' => 'field_fabrika62_market_sub',
                'label' => 'Másodlagos szöveg',
                'name' => 'market_sub',
                'type' => 'text',
            ],

            [
                'key' => 'field_fabrika62_tab_contact',
                'label' => 'Kapcsolat',
                'type' => 'tab',
                'placement' => 'top',
            ],
            [
                'key' => 'field_fabrika62_contact_title',
                'label' => 'Szekció cím',
                'name' => 'contact_title',
                'type' => 'text',
            ],
            [
                'key' => 'field_fabrika62_contact_form_shortcode',
                'label' => 'Űrlap shortcode',
                'name' => 'contact_form_shortcode',
                'type' => 'text',
                'instructions' => 'Pl. [fluentform id="1"]',
            ],
            [
                'key' => 'field_fabrika62_contact_email',
                'label' => 'Email',
                'name' => 'contact_email',
                'type' => 'text',
            ],
            [
                'key' => 'field_fabrika62_contact_facebook_label',
                'label' => 'Facebook szöveg',
                'name' => 'contact_facebook_label',
                'type' => 'text',
            ],
            [
                'key' => 'field_fabrika62_contact_facebook_url',
                'label' => 'Facebook URL',
                'name' => 'contact_facebook_url',
                'type' => 'url',
            ],
            [
                'key' => 'field_fabrika62_contact_instagram',
                'label' => 'Instagram (szöveg vagy @handle)',
                'name' => 'contact_instagram',
                'type' => 'text',
            ],
            [
                'key' => 'field_fabrika62_contact_instagram_url',
                'label' => 'Instagram URL',
                'name' => 'contact_instagram_url',
                'type' => 'url',
            ],
            [
                'key' => 'field_fabrika62_contact_viber',
                'label' => 'Viber/Telefon',
                'name' => 'contact_viber',
                'type' => 'text',
            ],

            [
                'key' => 'field_fabrika62_tab_faq',
                'label' => 'GYIK',
                'type' => 'tab',
                'placement' => 'top',
            ],
            [
                'key' => 'field_fabrika62_faq_title',
                'label' => 'Szekció cím',
                'name' => 'faq_title',
                'type' => 'text',
            ],
            [
                'key' => 'field_fabrika62_faq_items',
                'label' => 'Kérdések',
                'name' => 'faq_items',
                'type' => 'repeater',
                'min' => 0,
                'layout' => 'row',
                'button_label' => 'Új kérdés',
                'sub_fields' => [
                    [
                        'key' => 'field_fabrika62_faq_q',
                        'label' => 'Kérdés',
                        'name' => 'question',
                        'type' => 'text',
                    ],
                    [
                        'key' => 'field_fabrika62_faq_a',
                        'label' => 'Válasz',
                        'name' => 'answer',
                        'type' => 'textarea',
                        'rows' => 2,
                        'new_lines' => 'br',
                    ],
                ],
            ],

            [
                'key' => 'field_fabrika62_tab_footer',
                'label' => 'Footer',
                'type' => 'tab',
                'placement' => 'top',
            ],
            [
                'key' => 'field_fabrika62_footer_location',
                'label' => 'Helyszín',
                'name' => 'footer_location',
                'type' => 'text',
            ],
            [
                'key' => 'field_fabrika62_footer_facebook_url',
                'label' => 'Facebook URL',
                'name' => 'footer_facebook_url',
                'type' => 'url',
            ],
            [
                'key' => 'field_fabrika62_footer_instagram_url',
                'label' => 'Instagram URL',
                'name' => 'footer_instagram_url',
                'type' => 'url',
            ],
        ],
        'location' => [
            [
                [
                    'param' => 'options_page',
                    'operator' => '==',
                    'value' => 'fabrika62-home',
                ],
            ],
        ],
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen' => [],
        'active' => true,
    ]);
}
add_action('acf/init', 'fabrika62_register_acf_options');

function fabrika62_acf_requirements_notice(): void
{
    if (!is_admin() || !current_user_can('manage_options')) {
        return;
    }

    // ACF not active at all.
    if (!function_exists('acf_add_local_field_group')) {
        echo '<div class="notice notice-warning"><p><strong>Fabrika 6-2:</strong> Az ACF plugin nincs aktivva. A "Fabrika Kezdolap" beallitasokhoz telepitsd es aktivald az ACF-et.</p></div>';
        return;
    }

    // ACF Free is active, but Options Page requires Pro.
    if (!function_exists('acf_add_options_page')) {
        echo '<div class="notice notice-warning"><p><strong>Fabrika 6-2:</strong> ACF Free van aktivan. A "Fabrika Kezdolap" menuhoz ACF Pro szukseges (advanced-custom-fields-pro).</p></div>';
    }
}
add_action('admin_notices', 'fabrika62_acf_requirements_notice');
