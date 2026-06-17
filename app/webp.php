<?php

/**
 * Tự động chuyển ảnh upload sang WebP (không cần plugin).
 *
 * GD trên server đã bật WebP. Khi upload ảnh JPEG/PNG:
 *   1. Ảnh GỐC được thay bằng .webp → mọi URL (kể cả size 'full') trả .webp.
 *   2. Các size con (thumbnail/medium/large + size tuỳ biến của theme) sinh ra .webp.
 *   3. Chất lượng nén ~82 (cân bằng dung lượng / hình ảnh).
 *
 * GIF / SVG / PDF (catalog) KHÔNG bị đụng tới. Ảnh đã upload TRƯỚC khi bật tính
 * năng này vẫn giữ định dạng cũ — muốn đổi thì upload lại hoặc dùng Regenerate.
 */

namespace App\Webp;

use function add_filter;

const QUALITY = 82;

/**
 * Chất lượng nén cho WebP.
 */
add_filter('wp_editor_set_quality', function ($quality, $mime = '') {
    return $mime === 'image/webp' ? QUALITY : $quality;
}, 10, 2);

/**
 * Sinh các size con dưới dạng WebP thay vì JPEG/PNG.
 * (Áp dụng cả khi Regenerate Thumbnails hay chỉnh ảnh trong admin.)
 */
add_filter('image_editor_output_format', function ($formats) {
    $formats['image/jpeg'] = 'image/webp';
    $formats['image/png']  = 'image/webp';

    return $formats;
});

/**
 * Chuyển chính ảnh gốc sang WebP ngay khi upload, để size 'full' cũng là .webp
 * và các size con sinh từ nguồn .webp. Dùng cho cả upload admin
 * (wp_handle_upload) lẫn nạp programmatic (wp_handle_sideload).
 */
$convert = function ($upload) {
    if (empty($upload['file']) || empty($upload['type'])) {
        return $upload;
    }

    if (! in_array($upload['type'], ['image/jpeg', 'image/png'], true)) {
        return $upload; // bỏ qua GIF/SVG/PDF…
    }

    $editor = wp_get_image_editor($upload['file']);
    if (is_wp_error($editor)) {
        return $upload; // không đọc được ảnh → giữ nguyên
    }

    $target = preg_replace('/\.(jpe?g|png)$/i', '.webp', $upload['file']);
    $saved  = $editor->save($target, 'image/webp');

    if (is_wp_error($saved) || empty($saved['path'])) {
        return $upload; // convert lỗi → giữ file gốc
    }

    // Xoá file gốc JPEG/PNG, trỏ upload sang .webp.
    if ($saved['path'] !== $upload['file']) {
        @unlink($upload['file']);
    }

    $upload['file'] = $saved['path'];
    $upload['url']  = preg_replace('/\.(jpe?g|png)$/i', '.webp', $upload['url']);
    $upload['type'] = 'image/webp';

    return $upload;
};

add_filter('wp_handle_upload', $convert);
add_filter('wp_handle_sideload', $convert);
