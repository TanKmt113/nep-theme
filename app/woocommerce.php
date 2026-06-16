<?php

/**
 * WooCommerce integration — "Request a Quote" (RFQ) mode.
 *
 * NẾP bán rèm đo may theo yêu cầu, nên dùng WooCommerce để quản lý sản phẩm +
 * biến thể (chất liệu/màu) nhưng KHÔNG bán giỏ hàng:
 *   - Sản phẩm không thể mua (non-purchasable) → ẩn nút "Thêm vào giỏ".
 *   - Ẩn toàn bộ giá hiển thị.
 *   - Thay bằng nút "Yêu cầu báo giá" dẫn tới trang Liên hệ (prefill tên sản phẩm).
 *
 * Loại rèm = taxonomy `product_cat` của WooCommerce.
 * Các template Blade (archive-product, single-product, taxonomy-product_cat)
 * tự kế thừa layout của theme nên giữ nguyên header/footer/design NẾP.
 */

namespace App;

use function add_action;
use function add_filter;
use function add_theme_support;
use function remove_action;

// Bỏ qua nếu chưa kích hoạt WooCommerce.
add_action('after_setup_theme', function () {
    if (! class_exists('WooCommerce')) {
        return;
    }

    add_theme_support('woocommerce');
    add_theme_support('wc-product-gallery-zoom');
    add_theme_support('wc-product-gallery-lightbox');
    add_theme_support('wc-product-gallery-slider');
}, 20);

/**
 * RFQ: sản phẩm không thể mua → WooCommerce tự ẩn add-to-cart + giá.
 */
add_filter('woocommerce_is_purchasable', '__return_false');

/**
 * Ẩn mọi giá hiển thị (loop + single).
 */
add_filter('woocommerce_get_price_html', '__return_empty_string');
add_filter('woocommerce_variable_sale_price_html', '__return_empty_string');
add_filter('woocommerce_variable_price_html', '__return_empty_string');

/**
 * Dọn các nút/giá còn sót trong template mặc định của WooCommerce.
 */
add_action('init', function () {
    if (! class_exists('WooCommerce')) {
        return;
    }
    // Loop
    remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10);
    remove_action('woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10);
    // Single
    remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_price', 10);
    remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30);
});

/**
 * Ẩn các menu/khu vực không dùng tới (giỏ hàng, thanh toán) khỏi giao diện.
 * Chuyển hướng cart/checkout về trang chủ.
 */
add_action('template_redirect', function () {
    if (! class_exists('WooCommerce') || is_admin()) {
        return;
    }
    if (function_exists('is_cart') && function_exists('is_checkout') && (is_cart() || is_checkout())) {
        wp_safe_redirect(home_url('/'));
        exit;
    }
});

/**
 * URL "Yêu cầu báo giá" cho một sản phẩm → trang Liên hệ kèm ?sp=<id>.
 * Dùng trong Blade: {{ App\nep_quote_url($product_id) }}
 */
function nep_quote_url($product_id = 0): string
{
    $url = home_url('/lien-he');
    return $product_id ? add_query_arg('sp', (int) $product_id, $url) : $url;
}

/**
 * Số sản phẩm mỗi trang ở archive.
 */
add_filter('loop_shop_per_page', fn () => 24);
