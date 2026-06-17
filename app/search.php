<?php

/**
 * Live search suggestions ("gợi ý khi gõ").
 *
 * Ô tìm kiếm (resources/views/components/search-form.blade.php) gọi
 * admin-ajax.php?action=nep_search_suggest&q=... khi người dùng gõ.
 * Trả về tối đa 6 kết quả khớp (sản phẩm, dự án, bài viết, trang) dạng JSON
 * để JS render dropdown gợi ý. Chỉ đọc dữ liệu công khai nên không cần nonce.
 */

namespace App;

use WP_Query;

use function add_action;

add_action('wp_ajax_nep_search_suggest', __NAMESPACE__ . '\\handle_search_suggest');
add_action('wp_ajax_nopriv_nep_search_suggest', __NAMESPACE__ . '\\handle_search_suggest');

function handle_search_suggest(): void
{
    $q = sanitize_text_field(wp_unslash($_GET['q'] ?? ''));

    // Quá ngắn → không gợi ý (tránh trả cả site).
    if (mb_strlen($q) < 2) {
        wp_send_json_success(['query' => $q, 'items' => []]);
    }

    $query = new WP_Query([
        's'                   => $q,
        'post_type'           => ['product', 'du_an', 'post', 'page'],
        'post_status'         => 'publish',
        'posts_per_page'      => 6,
        'no_found_rows'       => true,
        'ignore_sticky_posts' => true,
    ]);

    $items = [];
    foreach ($query->posts as $p) {
        $pt    = get_post_type_object($p->post_type);
        $thumb = get_the_post_thumbnail_url($p->ID, 'thumbnail');
        $items[] = [
            'title' => html_entity_decode(get_the_title($p), ENT_QUOTES),
            'url'   => get_permalink($p),
            'type'  => $pt ? $pt->labels->singular_name : '',
            'thumb' => $thumb ?: '',
        ];
    }
    wp_reset_postdata();

    wp_send_json_success(['query' => $q, 'items' => $items]);
}
