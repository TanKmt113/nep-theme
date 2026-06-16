<?php

/**
 * Custom Post Types & Taxonomies.
 *
 * Sản phẩm = `product` của WooCommerce (xem app/woocommerce.php).
 * Loại rèm = taxonomy `product_cat` của WooCommerce.
 * File này chỉ đăng ký các CPT KHÔNG thuộc WooCommerce:
 *   projects[]  → CPT `du_an`
 *   catalog     → CPT `catalog`
 *
 * Per-item fields (material, color, …) được gắn qua ACF trong fields.php.
 */

namespace App;

use function add_action;
use function register_post_type;

add_action('init', function () {

    // ---- CPT: Dự án (projects) -----------------------------------------
    register_post_type('du_an', [
        'labels' => [
            'name'          => 'Dự án',
            'singular_name' => 'Dự án',
            'add_new_item'  => 'Thêm dự án',
            'menu_name'     => 'Dự án',
        ],
        'public'        => true,
        'has_archive'   => true,
        'menu_icon'     => 'dashicons-building',
        'show_in_rest'  => true,
        'supports'      => ['title', 'editor', 'thumbnail'],
        'rewrite'       => ['slug' => 'du-an'],
    ]);

    // ---- CPT: Catalog --------------------------------------------------
    register_post_type('catalog', [
        'labels' => [
            'name'          => 'Catalog',
            'singular_name' => 'Catalog',
            'menu_name'     => 'Catalog',
        ],
        'public'        => true,
        'has_archive'   => true,
        'menu_icon'     => 'dashicons-book',
        'show_in_rest'  => true,
        'supports'      => ['title', 'editor', 'thumbnail'],
        'rewrite'       => ['slug' => 'catalog'],
    ]);
});
