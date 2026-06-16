<?php

/**
 * Small theme helpers used across Blade views.
 */

namespace App;

/**
 * Read an ACF Options-page setting with a sensible fallback.
 * Usage in Blade: {{ App\nep('hotline') }}
 */
function nep(string $key, $default = '')
{
    if (function_exists('get_field')) {
        $value = get_field($key, 'option');
        if (! empty($value)) {
            return $value;
        }
    }

    $defaults = [
        'hotline'     => '083.888.5005',
        'hotline_alt' => '084.888.5005',
        'slogan'      => 'Uy tín tạo nên thương hiệu',
        'brand_line'  => 'Xưởng thêu vi tính – Rèm thiết kế',
        'email'       => 'xinchao@nep.vn',
        'address'     => 'Ngã 3 Tân Hương, Phổ Yên, Thái Nguyên',
        'hours'       => '8:00 – 20:00, Thứ 2 – Chủ nhật',
    ];

    return $defaults[$key] ?? $default;
}

/**
 * Permalink of the WooCommerce shop (product archive). Falls back gracefully.
 */
function nep_shop_url(): string
{
    if (function_exists('wc_get_page_permalink')) {
        $url = wc_get_page_permalink('shop');
        if ($url) {
            return $url;
        }
    }
    return function_exists('get_post_type_archive_link')
        ? (get_post_type_archive_link('product') ?: home_url('/'))
        : home_url('/');
}

/**
 * tel: href from a display phone number ("083.888.5005" → "tel:0838885005").
 */
function nep_tel(string $phone): string
{
    return 'tel:' . preg_replace('/[\s.]+/', '', $phone);
}

/**
 * ACF field with fallback (works whether or not ACF is active).
 */
function nep_field(string $name, $post_id = null, $default = '')
{
    if (function_exists('get_field')) {
        $v = get_field($name, $post_id);
        return $v !== null && $v !== '' ? $v : $default;
    }
    return $default;
}
