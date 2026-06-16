<?php

/**
 * ACF field groups (registered in PHP so they live in version control).
 * Requires Advanced Custom Fields (Pro recommended for Repeater + Options page).
 *
 * Maps the per-item keys from NEP_DATA onto post meta:
 *   product: material, color, color_hex, price, price_val, badge
 *   project: place, year, area, type, scope[], gallery[]
 *   options: hotline, hotline_alt, slogan, brand_line, process[], features[]
 */

namespace App;

use function add_action;
use function function_exists;

add_action('acf/init', function () {
    if (! function_exists('acf_add_local_field_group')) {
        return; // ACF not installed — admin notice handled below.
    }

    // ---- Site settings (Options page) ----------------------------------
    if (function_exists('acf_add_options_page')) {
        acf_add_options_page([
            'page_title' => 'Cài đặt NẾP',
            'menu_title' => 'Cài đặt NẾP',
            'menu_slug'  => 'nep-settings',
            'capability' => 'edit_theme_options',
            'icon_url'   => 'dashicons-admin-customizer',
            'position'   => 2,
        ]);
    }

    acf_add_local_field_group([
        'key'    => 'group_nep_settings',
        'title'  => 'Cài đặt NẾP',
        'fields' => [
            ['key' => 'f_hotline',    'label' => 'Hotline',      'name' => 'hotline',     'type' => 'text', 'default_value' => '083.888.5005'],
            ['key' => 'f_hotline2',   'label' => 'Hotline phụ',  'name' => 'hotline_alt', 'type' => 'text', 'default_value' => '084.888.5005'],
            ['key' => 'f_slogan',     'label' => 'Slogan',       'name' => 'slogan',      'type' => 'text', 'default_value' => 'Uy tín tạo nên thương hiệu'],
            ['key' => 'f_brandline',  'label' => 'Brand line',   'name' => 'brand_line',  'type' => 'text', 'default_value' => 'Xưởng thêu vi tính – Rèm thiết kế'],
            ['key' => 'f_email',      'label' => 'Email',        'name' => 'email',       'type' => 'email', 'default_value' => 'xinchao@nep.vn'],
            ['key' => 'f_address',    'label' => 'Địa chỉ',      'name' => 'address',     'type' => 'text', 'default_value' => 'Ngã 3 Tân Hương, Phổ Yên, Thái Nguyên'],
            ['key' => 'f_hours',      'label' => 'Giờ làm việc', 'name' => 'hours',       'type' => 'text', 'default_value' => '8:00 – 20:00, Thứ 2 – Chủ nhật'],
            [
                'key' => 'f_process', 'label' => 'Quy trình (6 bước)', 'name' => 'process', 'type' => 'repeater',
                'layout' => 'block', 'button_label' => 'Thêm bước',
                'sub_fields' => [
                    ['key' => 'f_proc_n',     'label' => 'Số',     'name' => 'n',     'type' => 'text'],
                    ['key' => 'f_proc_title', 'label' => 'Tiêu đề', 'name' => 'title', 'type' => 'text'],
                    ['key' => 'f_proc_desc',  'label' => 'Mô tả',  'name' => 'desc',  'type' => 'textarea', 'rows' => 2],
                    ['key' => 'f_proc_icon',  'label' => 'Icon (lucide)', 'name' => 'icon', 'type' => 'text'],
                ],
            ],
            [
                'key' => 'f_features', 'label' => 'Lý do chọn NẾP', 'name' => 'features', 'type' => 'repeater',
                'layout' => 'block', 'button_label' => 'Thêm mục',
                'sub_fields' => [
                    ['key' => 'f_feat_title', 'label' => 'Tiêu đề', 'name' => 'title', 'type' => 'text'],
                    ['key' => 'f_feat_desc',  'label' => 'Mô tả',  'name' => 'desc',  'type' => 'textarea', 'rows' => 2],
                    ['key' => 'f_feat_icon',  'label' => 'Icon (lucide)', 'name' => 'icon', 'type' => 'text'],
                ],
            ],
        ],
        'location' => [[['param' => 'options_page', 'operator' => '==', 'value' => 'nep-settings']]],
    ]);

    // ---- Taxonomy fields (loại rèm = product_cat: ảnh + icon) ----------
    acf_add_local_field_group([
        'key'    => 'group_product_cat',
        'title'  => 'Hình ảnh loại rèm',
        'fields' => [
            ['key' => 'f_lr_img',  'label' => 'Ảnh (URL)', 'name' => 'img',  'type' => 'url', 'instructions' => 'URL ảnh đại diện cho card danh mục. (Có thể dùng kèm ảnh danh mục mặc định của WooCommerce.)'],
            ['key' => 'f_lr_icon', 'label' => 'Icon (lucide)', 'name' => 'icon', 'type' => 'text', 'default_value' => 'blinds'],
        ],
        'location' => [[['param' => 'taxonomy', 'operator' => '==', 'value' => 'product_cat']]],
    ]);

    // ---- Product fields (gắn lên product của WooCommerce) --------------
    // Giá thật do WooCommerce quản lý (đang ẩn ở chế độ RFQ). Các field dưới
    // chỉ là thuộc tính hiển thị; có thể thay bằng WooCommerce attributes nếu cần biến thể.
    acf_add_local_field_group([
        'key'    => 'group_product_nep',
        'title'  => 'Thông tin rèm (NẾP)',
        'fields' => [
            ['key' => 'f_sp_material', 'label' => 'Chất liệu', 'name' => 'material',  'type' => 'text'],
            ['key' => 'f_sp_color',    'label' => 'Màu',       'name' => 'color',     'type' => 'text'],
            ['key' => 'f_sp_colorhex', 'label' => 'Mã màu',    'name' => 'color_hex', 'type' => 'color_picker'],
            [
                'key' => 'f_sp_badge', 'label' => 'Nhãn', 'name' => 'badge', 'type' => 'select',
                'choices' => ['' => '— Không —', 'new' => 'Mới', 'hot' => 'Bán chạy'], 'allow_null' => 1,
            ],
        ],
        'location' => [[['param' => 'post_type', 'operator' => '==', 'value' => 'product']]],
    ]);

    // ---- Project fields ------------------------------------------------
    acf_add_local_field_group([
        'key'    => 'group_du_an',
        'title'  => 'Thông tin dự án',
        'fields' => [
            ['key' => 'f_da_place', 'label' => 'Địa điểm', 'name' => 'place', 'type' => 'text'],
            ['key' => 'f_da_year',  'label' => 'Năm',      'name' => 'year',  'type' => 'text'],
            ['key' => 'f_da_area',  'label' => 'Diện tích', 'name' => 'area', 'type' => 'text'],
            ['key' => 'f_da_type',  'label' => 'Loại',     'name' => 'type',  'type' => 'text'],
            ['key' => 'f_da_span',  'label' => 'Span (1 hoặc 2 ô lưới)', 'name' => 'span', 'type' => 'number', 'default_value' => 1, 'min' => 1, 'max' => 2],
            [
                'key' => 'f_da_scope', 'label' => 'Hạng mục', 'name' => 'scope', 'type' => 'repeater',
                'layout' => 'table', 'button_label' => 'Thêm hạng mục',
                'sub_fields' => [
                    ['key' => 'f_da_scope_item', 'label' => 'Hạng mục', 'name' => 'item', 'type' => 'text'],
                ],
            ],
            ['key' => 'f_da_gallery', 'label' => 'Thư viện ảnh', 'name' => 'gallery', 'type' => 'gallery'],
        ],
        'location' => [[['param' => 'post_type', 'operator' => '==', 'value' => 'du_an']]],
    ]);
});

/**
 * Friendly admin notice if ACF is missing.
 */
add_action('admin_notices', function () {
    if (function_exists('acf_add_local_field_group')) {
        return;
    }
    echo '<div class="notice notice-warning"><p><strong>NẾP:</strong> Theme cần plugin <a href="https://www.advancedcustomfields.com/" target="_blank">Advanced Custom Fields</a> (khuyến nghị bản Pro) để quản lý dữ liệu sản phẩm/dự án.</p></div>';
});
