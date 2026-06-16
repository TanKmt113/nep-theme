<?php

/**
 * Trình tạo NỘI DUNG mẫu (loại rèm + sản phẩm WooCommerce + dự án + options).
 *
 * Phần "khung" của site (trang, menu, trang chủ tĩnh) nằm ở app/seed-pages.php.
 *
 * Chạy:
 *     wp nep seed              # tạo loại rèm, sản phẩm, dự án, tuỳ chọn
 *     wp nep seed --fresh      # xoá nội dung đã seed rồi tạo lại
 * Hoặc: Công cụ → "NẾP · Dữ liệu mẫu" → "Tạo TẤT CẢ".
 *
 * Sideload ảnh demo từ Unsplash vào Thư viện và đặt làm ảnh đại diện.
 * Xoá file này (và require trong functions.php) khi đã có nội dung thật.
 */

namespace App;

require_once __DIR__ . '/nep-data.php';

use function add_action;

/**
 * Tìm post theo tiêu đề chính xác (để upsert, tránh tạo trùng khi chạy lại).
 */
function nep_find_post_by_title(string $type, string $title): int
{
    $hit = get_posts([
        'post_type'   => $type,
        'title'       => $title,
        'numberposts' => 1,
        'post_status' => 'any',
        'fields'      => 'ids',
    ]);

    return $hit ? (int) $hit[0] : 0;
}

/**
 * Tạo loại rèm + sản phẩm + dự án + tuỳ chọn. Dùng chung cho WP-CLI và admin.
 *
 * @param  bool  $fresh  Xoá product/du_an cũ trước khi tạo lại.
 * @param  callable(string):void  $log  Hàm ghi log (một dòng).
 *
 * @throws \RuntimeException Khi chưa kích hoạt WooCommerce.
 */
function nep_seed_content(bool $fresh, callable $log): void
{
    $data = nep_seed_data();

    require_once ABSPATH . 'wp-admin/includes/media.php';
    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/image.php';

    if (! class_exists('WooCommerce')) {
        throw new \RuntimeException('Cần kích hoạt WooCommerce trước khi seed sản phẩm.');
    }

    if ($fresh) {
        foreach (['product', 'du_an'] as $pt) {
            foreach (get_posts(['post_type' => $pt, 'numberposts' => -1, 'post_status' => 'any']) as $p) {
                wp_delete_post($p->ID, true);
            }
        }
        $log('Đã xoá nội dung cũ.');
    }

    // Helper: sideload một URL ảnh → attachment ID (cache theo URL).
    // Dùng download_url + media_handle_sideload thay vì media_sideload_image()
    // vì hàm đó từ WP 5.x từ chối URL không có đuôi ảnh (Unsplash kết thúc
    // bằng "?w=900&q=80" nên bị coi là "Invalid image URL").
    $sideload = function (string $url, int $parent = 0) use ($log) {
        static $cache = [];
        if (isset($cache[$url])) {
            return $cache[$url];
        }

        $tmp = download_url($url, 30);
        if (is_wp_error($tmp)) {
            $log("⚠ Tải ảnh lỗi ({$tmp->get_error_message()}): {$url}");
            return $cache[$url] = 0;
        }

        $file_array = [
            'name'     => 'nep-' . substr(md5($url), 0, 12) . '.jpg',
            'tmp_name' => $tmp,
        ];

        $id = media_handle_sideload($file_array, $parent);
        if (is_wp_error($id)) {
            @unlink($tmp);
            $log("⚠ Lưu ảnh lỗi ({$id->get_error_message()}): {$url}");
            return $cache[$url] = 0;
        }

        return $cache[$url] = (int) $id;
    };

    // ---- Taxonomy terms (loại rèm = product_cat) + ảnh/icon --------
    foreach ($data['categories'] as $cat) {
        $term = term_exists($cat['name'], 'product_cat');
        if (! $term) {
            $term = wp_insert_term($cat['name'], 'product_cat');
        }
        $term_id = is_array($term) ? (int) $term['term_id'] : (int) $term;
        if ($term_id && function_exists('update_field')) {
            update_field('img', $cat['img'], 'product_cat_' . $term_id);
            update_field('icon', $cat['icon'], 'product_cat_' . $term_id);
        }
    }
    $log('Loại rèm (product_cat): xong.');

    // ---- Products (WooCommerce) ------------------------------------
    foreach ($data['products'] as $p) {
        $id = nep_find_post_by_title('product', $p['name']);
        $args = [
            'post_type'    => 'product',
            'post_title'   => $p['name'],
            'post_content' => $p['desc'],
            'post_excerpt' => $p['desc'],
            'post_status'  => 'publish',
        ];
        $id = $id ? (wp_update_post(['ID' => $id] + $args) ?: $id) : wp_insert_post($args);
        // Loại sản phẩm WooCommerce + danh mục.
        wp_set_object_terms($id, 'simple', 'product_type');
        wp_set_object_terms($id, $p['cat'], 'product_cat');
        // Giá thật (admin dùng nội bộ; đang ẩn ở chế độ RFQ).
        update_post_meta($id, '_regular_price', (string) $p['price_val']);
        update_post_meta($id, '_price', (string) $p['price_val']);
        update_post_meta($id, '_visibility', 'visible');
        // Thuộc tính hiển thị NẾP (ACF).
        if (function_exists('update_field')) {
            update_field('material', $p['material'], $id);
            update_field('color', $p['color'], $id);
            update_field('color_hex', $p['color_hex'], $id);
            update_field('badge', $p['badge'], $id);
        }
        // Chỉ tải ảnh khi sản phẩm chưa có ảnh đại diện (an toàn khi chạy lại).
        if (! get_post_thumbnail_id($id) && ($att = $sideload($p['img'], $id))) {
            set_post_thumbnail($id, $att);
        }
        $log("Sản phẩm: {$p['name']}");
    }

    // ---- Projects --------------------------------------------------
    foreach ($data['projects'] as $pr) {
        $id = nep_find_post_by_title('du_an', $pr['name']);
        $args = [
            'post_type'    => 'du_an',
            'post_title'   => $pr['name'],
            'post_content' => $pr['desc'],
            'post_status'  => 'publish',
        ];
        $id = $id ? (wp_update_post(['ID' => $id] + $args) ?: $id) : wp_insert_post($args);
        if (function_exists('update_field')) {
            update_field('place', $pr['place'], $id);
            update_field('year', $pr['year'], $id);
            update_field('area', $pr['area'], $id);
            update_field('type', $pr['type'], $id);
            update_field('span', $pr['span'], $id);
            update_field('scope', array_map(fn ($s) => ['item' => $s], $pr['scope']), $id);
        }
        if ($att = $sideload($pr['img'], $id)) {
            set_post_thumbnail($id, $att);
        }
        $log("Dự án: {$pr['name']}");
    }

    // ---- Options (process + features) ------------------------------
    if (function_exists('update_field')) {
        update_field('process', $data['process'], 'option');
        update_field('features', $data['features'], 'option');
    }
}

add_action('cli_init', function () {
    if (! class_exists('WP_CLI')) {
        return;
    }

    \WP_CLI::add_command('nep seed', function ($args, $assoc) {
        try {
            nep_seed_content(isset($assoc['fresh']), fn ($line) => \WP_CLI::log($line));
        } catch (\RuntimeException $e) {
            \WP_CLI::error($e->getMessage());
        }

        \WP_CLI::success('Seed hoàn tất. Vào /san-pham để kiểm tra.');
    });
});
