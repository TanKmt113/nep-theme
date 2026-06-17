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
            // ===== Tab: Liên hệ =====
            ['key' => 'f_tab_contact', 'label' => 'Liên hệ', 'type' => 'tab', 'placement' => 'top'],
            ['key' => 'f_hotline',    'label' => 'Hotline',      'name' => 'hotline',     'type' => 'text', 'default_value' => '083.888.5005', 'wrapper' => ['width' => 50]],
            ['key' => 'f_hotline2',   'label' => 'Hotline phụ',  'name' => 'hotline_alt', 'type' => 'text', 'default_value' => '084.888.5005', 'wrapper' => ['width' => 50]],
            ['key' => 'f_email',      'label' => 'Email',        'name' => 'email',       'type' => 'email', 'default_value' => 'xinchao@nep.vn', 'wrapper' => ['width' => 50]],
            ['key' => 'f_hours',      'label' => 'Giờ làm việc', 'name' => 'hours',       'type' => 'text', 'default_value' => '8:00 – 20:00, Thứ 2 – Chủ nhật', 'wrapper' => ['width' => 50]],
            ['key' => 'f_address',    'label' => 'Địa chỉ',      'name' => 'address',     'type' => 'text', 'default_value' => 'Ngã 3 Tân Hương, Phổ Yên, Thái Nguyên'],

            // ===== Tab: Thương hiệu =====
            ['key' => 'f_tab_brand', 'label' => 'Thương hiệu', 'type' => 'tab', 'placement' => 'top'],
            ['key' => 'f_logo',       'label' => 'Logo chính',   'name' => 'logo',       'type' => 'image', 'return_format' => 'url', 'preview_size' => 'medium', 'library' => 'all', 'instructions' => 'Hiện ở header (nền sáng) & khi cuộn. Nên dùng PNG/SVG nền trong suốt. Trống = logo mặc định của theme.', 'wrapper' => ['width' => 50]],
            ['key' => 'f_logo_light', 'label' => 'Logo nền tối', 'name' => 'logo_light', 'type' => 'image', 'return_format' => 'url', 'preview_size' => 'medium', 'library' => 'all', 'instructions' => 'Hiện ở header trong suốt trên ảnh hero & ở footer. Nên là phiên bản logo màu sáng/trắng. Trống = logo mặc định.', 'wrapper' => ['width' => 50]],
            ['key' => 'f_slogan',     'label' => 'Slogan',       'name' => 'slogan',      'type' => 'text', 'default_value' => 'Uy tín tạo nên thương hiệu', 'wrapper' => ['width' => 50]],
            ['key' => 'f_brandline',  'label' => 'Brand line',   'name' => 'brand_line',  'type' => 'text', 'default_value' => 'Xưởng thêu vi tính – Rèm thiết kế', 'wrapper' => ['width' => 50]],
            ['key' => 'f_og_default', 'label' => 'Ảnh chia sẻ mặc định (OG)', 'name' => 'og_default', 'type' => 'image', 'return_format' => 'url', 'preview_size' => 'medium', 'library' => 'all', 'instructions' => 'Ảnh hiện khi chia sẻ link lên Facebook/Zalo cho các trang KHÔNG có ảnh riêng. Nên dùng JPG/PNG 1200×630px (đừng dùng SVG). Trống = logo theme.'],
            ['key' => 'f_google_verify', 'label' => 'Mã xác minh Google Search Console', 'name' => 'google_verify', 'type' => 'text', 'instructions' => 'Dán phần mã trong thuộc tính content của thẻ google-site-verification mà Search Console cung cấp (chỉ phần mã, không cần cả thẻ). Trống = không in.'],

            // ===== Tab: CTA cuối trang (khối "Nâng tầm không gian…") =====
            ['key' => 'f_cta_tab', 'label' => 'CTA cuối trang', 'type' => 'tab', 'placement' => 'top'],
            ['key' => 'f_cta_heading', 'label' => 'Tiêu đề', 'name' => 'cta_heading', 'type' => 'text', 'default_value' => 'Nâng tầm không gian sống của bạn ngay hôm nay'],
            ['key' => 'f_cta_image', 'label' => 'Ảnh nền', 'name' => 'cta_image', 'type' => 'image', 'return_format' => 'url', 'preview_size' => 'medium'],
            ['key' => 'f_cta_b1', 'label' => 'Nút 1 — chữ (gọi hotline)', 'name' => 'cta_btn1_text', 'type' => 'text', 'default_value' => 'Gọi ngay', 'wrapper' => ['width' => 50]],
            ['key' => 'f_cta_b2t', 'label' => 'Nút 2 — chữ', 'name' => 'cta_btn2_text', 'type' => 'text', 'default_value' => 'Đăng ký tư vấn', 'wrapper' => ['width' => 50]],
            ['key' => 'f_cta_b2u', 'label' => 'Nút 2 — liên kết', 'name' => 'cta_btn2_url', 'type' => 'text', 'instructions' => 'Trống = trang Liên hệ.'],

            // ===== Tab: Trang lưu trữ (tiêu đề các trang danh sách) =====
            ['key' => 'f_arch_tab', 'label' => 'Trang lưu trữ', 'type' => 'tab', 'placement' => 'top'],
            ['key' => 'f_arch_msg', 'label' => '', 'type' => 'message', 'message' => 'Tiêu đề các trang danh sách. Để trống sẽ dùng tiêu đề mặc định.'],
            ['key' => 'f_arch_prod_eb', 'label' => 'Sản phẩm — eyebrow', 'name' => 'arch_product_eyebrow', 'type' => 'text', 'default_value' => 'Sản phẩm', 'wrapper' => ['width' => 50]],
            ['key' => 'f_arch_prod_h',  'label' => 'Sản phẩm — tiêu đề', 'name' => 'arch_product_heading', 'type' => 'text', 'default_value' => 'Bộ sưu tập rèm cửa', 'wrapper' => ['width' => 50]],
            ['key' => 'f_arch_proj_eb', 'label' => 'Dự án — eyebrow', 'name' => 'arch_project_eyebrow', 'type' => 'text', 'default_value' => 'Dự án', 'wrapper' => ['width' => 50]],
            ['key' => 'f_arch_proj_h',  'label' => 'Dự án — tiêu đề', 'name' => 'arch_project_heading', 'type' => 'text', 'default_value' => 'Không gian đã hoàn thiện', 'wrapper' => ['width' => 50]],
            ['key' => 'f_arch_cat_eb',  'label' => 'Catalog — eyebrow', 'name' => 'arch_catalog_eyebrow', 'type' => 'text', 'default_value' => 'Catalog', 'wrapper' => ['width' => 50]],
            ['key' => 'f_arch_cat_h',   'label' => 'Catalog — tiêu đề', 'name' => 'arch_catalog_heading', 'type' => 'text', 'default_value' => 'Catalogue & bảng giá', 'wrapper' => ['width' => 50]],
            ['key' => 'f_arch_cat_desc','label' => 'Catalog — mô tả', 'name' => 'arch_catalog_desc', 'type' => 'textarea', 'rows' => 2, 'default_value' => 'Xem trước và tải về các ấn phẩm catalogue, bảng màu, hồ sơ năng lực của NẾP.'],

            // ===== Tab: Footer =====
            ['key' => 'f_ft_tab', 'label' => 'Footer', 'type' => 'tab', 'placement' => 'top'],
            ['key' => 'f_ft_about', 'label' => 'Giới thiệu ngắn', 'name' => 'footer_about', 'type' => 'textarea', 'rows' => 2, 'default_value' => 'Xưởng thêu vi tính & rèm thiết kế.'],
            ['key' => 'f_ft_c1', 'label' => 'Tiêu đề cột 1', 'name' => 'footer_products_title', 'type' => 'text', 'default_value' => 'Sản phẩm', 'wrapper' => ['width' => 33]],
            ['key' => 'f_ft_c2', 'label' => 'Tiêu đề cột 2', 'name' => 'footer_services_title', 'type' => 'text', 'default_value' => 'Dịch vụ', 'wrapper' => ['width' => 33]],
            ['key' => 'f_ft_c3', 'label' => 'Tiêu đề cột 3', 'name' => 'footer_company_title', 'type' => 'text', 'default_value' => 'Công ty', 'wrapper' => ['width' => 34]],
            ['key' => 'f_ft_links_note', 'label' => 'Liên kết các cột', 'type' => 'message', 'message' => 'Sửa link 3 cột tại <strong>Giao diện → Menu</strong> (vị trí: Footer — Sản phẩm / Dịch vụ / Công ty).'],
            ['key' => 'f_ft_fb', 'label' => 'Facebook URL', 'name' => 'footer_facebook', 'type' => 'text', 'wrapper' => ['width' => 33]],
            ['key' => 'f_ft_ig', 'label' => 'Instagram URL', 'name' => 'footer_instagram', 'type' => 'text', 'wrapper' => ['width' => 33]],
            ['key' => 'f_ft_yt', 'label' => 'YouTube URL', 'name' => 'footer_youtube', 'type' => 'text', 'wrapper' => ['width' => 34]],
            ['key' => 'f_ft_news', 'label' => 'Tiêu đề bản tin', 'name' => 'footer_newsletter_title', 'type' => 'text', 'default_value' => 'Nhận bản tin & ưu đãi'],
            ['key' => 'f_ft_legal', 'label' => 'Dòng pháp lý (góc phải)', 'name' => 'footer_legal', 'type' => 'text', 'default_value' => 'Chính sách bảo mật · Điều khoản'],
        ],
        'location' => [[['param' => 'options_page', 'operator' => '==', 'value' => 'nep-settings']]],
    ]);

    // ---- Trang chủ (gắn trực tiếp vào trang đặt làm Trang chủ) ----------
    // Mở: Trang → Trang chủ → các ô bên dưới khung soạn thảo.
    acf_add_local_field_group([
        'key'    => 'group_home',
        'title'  => 'Nội dung Trang chủ',
        'fields' => [
            // ===== Tab: Ẩn / Hiện section =====
            ['key' => 'f_home_tab_show', 'label' => 'Ẩn / Hiện', 'type' => 'tab', 'placement' => 'top'],
            ['key' => 'f_home_show_msg', 'label' => '', 'type' => 'message', 'message' => 'Bật/tắt hiển thị từng section trên Trang chủ. Tắt = ẩn hẳn section đó khỏi trang.'],
            ['key' => 'f_home_show_intro',    'label' => 'Giới thiệu (Về NẾP)', 'name' => 'home_show_intro',    'type' => 'true_false', 'ui' => 1, 'default_value' => 1, 'wrapper' => ['width' => 33]],
            ['key' => 'f_home_show_cat',      'label' => 'Danh mục',           'name' => 'home_show_cat',      'type' => 'true_false', 'ui' => 1, 'default_value' => 1, 'wrapper' => ['width' => 33]],
            ['key' => 'f_home_show_featured', 'label' => 'Sản phẩm nổi bật',   'name' => 'home_show_featured', 'type' => 'true_false', 'ui' => 1, 'default_value' => 1, 'wrapper' => ['width' => 33]],
            ['key' => 'f_home_show_process',  'label' => 'Quy trình',          'name' => 'home_show_process',  'type' => 'true_false', 'ui' => 1, 'default_value' => 1, 'wrapper' => ['width' => 33]],
            ['key' => 'f_home_show_projects', 'label' => 'Dự án',              'name' => 'home_show_projects', 'type' => 'true_false', 'ui' => 1, 'default_value' => 1, 'wrapper' => ['width' => 33]],
            ['key' => 'f_home_show_emb',      'label' => 'Teaser xưởng thêu',  'name' => 'home_show_emb',      'type' => 'true_false', 'ui' => 1, 'default_value' => 1, 'wrapper' => ['width' => 33]],
            ['key' => 'f_home_show_cta',      'label' => 'CTA cuối trang',     'name' => 'home_show_cta',      'type' => 'true_false', 'ui' => 1, 'default_value' => 1, 'wrapper' => ['width' => 33]],

            // ===== Tab: Hero =====
            ['key' => 'f_home_tab_hero', 'label' => 'Hero', 'type' => 'tab', 'placement' => 'top'],
            ['key' => 'f_home_hero_eyebrow', 'label' => 'Eyebrow (dòng nhỏ phía trên)', 'name' => 'hero_eyebrow', 'type' => 'text', 'default_value' => 'Rèm cửa cao cấp · Từ 2014'],
            ['key' => 'f_home_hero_title', 'label' => 'Tiêu đề', 'name' => 'hero_title', 'type' => 'text', 'default_value' => 'Kiến tạo không gian sống'],
            ['key' => 'f_home_hero_accent', 'label' => 'Từ nhấn (in nghiêng, màu rêu)', 'name' => 'hero_title_accent', 'type' => 'text', 'default_value' => 'đẳng cấp', 'instructions' => 'Để trống nếu không cần phần in nghiêng.'],
            ['key' => 'f_home_hero_desc', 'label' => 'Mô tả', 'name' => 'hero_desc', 'type' => 'textarea', 'rows' => 3, 'default_value' => 'Thiết kế, thi công và lắp đặt rèm cửa cao cấp cùng xưởng thêu vi tính — chăm chút trong từng đường nét.'],
            ['key' => 'f_home_hero_image', 'label' => 'Ảnh nền hero', 'name' => 'hero_image', 'type' => 'image', 'return_format' => 'url', 'preview_size' => 'medium', 'instructions' => 'Để trống sẽ dùng ảnh mặc định.'],
            ['key' => 'f_home_hero_btn1_text', 'label' => 'Nút 1 — chữ', 'name' => 'hero_btn1_text', 'type' => 'text', 'default_value' => 'Xem bộ sưu tập', 'wrapper' => ['width' => 50]],
            ['key' => 'f_home_hero_btn1_url', 'label' => 'Nút 1 — liên kết', 'name' => 'hero_btn1_url', 'type' => 'text', 'instructions' => 'Để trống = trang Sản phẩm.', 'wrapper' => ['width' => 50]],
            ['key' => 'f_home_hero_btn2_text', 'label' => 'Nút 2 — chữ', 'name' => 'hero_btn2_text', 'type' => 'text', 'default_value' => 'Nhận báo giá', 'wrapper' => ['width' => 50]],
            ['key' => 'f_home_hero_btn2_url', 'label' => 'Nút 2 — liên kết', 'name' => 'hero_btn2_url', 'type' => 'text', 'instructions' => 'Để trống = trang Liên hệ.', 'wrapper' => ['width' => 50]],
            [
                'key' => 'f_home_stats', 'label' => 'Số liệu (dải dưới hero)', 'name' => 'stats', 'type' => 'repeater',
                'layout' => 'table', 'button_label' => 'Thêm số liệu',
                'sub_fields' => [
                    ['key' => 'f_home_stat_value', 'label' => 'Số', 'name' => 'value', 'type' => 'text'],
                    ['key' => 'f_home_stat_label', 'label' => 'Nhãn', 'name' => 'label', 'type' => 'text'],
                ],
            ],

            // ===== Tab: Giới thiệu =====
            ['key' => 'f_home_tab_intro', 'label' => 'Giới thiệu', 'type' => 'tab', 'placement' => 'top'],
            ['key' => 'f_home_intro_eyebrow', 'label' => 'Eyebrow', 'name' => 'intro_eyebrow', 'type' => 'text', 'default_value' => 'Về NẾP'],
            ['key' => 'f_home_intro_heading', 'label' => 'Tiêu đề', 'name' => 'intro_heading', 'type' => 'text', 'default_value' => 'Nghề rèm, thêu — chăm chút từng nếp gấp'],
            ['key' => 'f_home_intro_text', 'label' => 'Đoạn văn', 'name' => 'intro_text', 'type' => 'textarea', 'rows' => 4, 'default_value' => 'Suốt một thập kỷ, NẾP đồng hành cùng hàng nghìn gia đình và doanh nghiệp Việt — mang đến những bộ rèm và sản phẩm thêu bền đẹp, tinh tế và đậm dấu ấn riêng.'],
            ['key' => 'f_home_intro_image', 'label' => 'Ảnh', 'name' => 'intro_image', 'type' => 'image', 'return_format' => 'url', 'preview_size' => 'medium', 'instructions' => 'Để trống sẽ dùng ảnh mặc định.'],
            ['key' => 'f_home_intro_badge_value', 'label' => 'Badge — số', 'name' => 'intro_badge_value', 'type' => 'text', 'default_value' => '4.9/5', 'wrapper' => ['width' => 50]],
            ['key' => 'f_home_intro_badge_label', 'label' => 'Badge — nhãn', 'name' => 'intro_badge_label', 'type' => 'text', 'default_value' => '1.200+ đánh giá', 'wrapper' => ['width' => 50]],

            // ===== Tab: Danh mục =====
            ['key' => 'f_home_tab_cat', 'label' => 'Danh mục', 'type' => 'tab', 'placement' => 'top'],
            ['key' => 'f_home_cat_eyebrow', 'label' => 'Eyebrow (dòng nhỏ)', 'name' => 'cat_eyebrow', 'type' => 'text', 'default_value' => 'Danh mục', 'wrapper' => ['width' => 50]],
            ['key' => 'f_home_cat_heading', 'label' => 'Tiêu đề', 'name' => 'cat_heading', 'type' => 'text', 'default_value' => 'Bộ sưu tập rèm cửa', 'wrapper' => ['width' => 50]],
            ['key' => 'f_home_cat_pick', 'label' => 'Danh mục hiển thị', 'name' => 'home_categories', 'type' => 'taxonomy', 'taxonomy' => 'product_cat', 'field_type' => 'multi_select', 'add_term' => 0, 'save_terms' => 0, 'load_terms' => 0, 'return_format' => 'id', 'instructions' => 'Chọn & sắp xếp danh mục muốn hiện trên trang chủ. Để trống = hiện tất cả. (Ảnh/tên danh mục sửa tại Sản phẩm → Danh mục.)'],

            // ===== Tab: Nổi bật =====
            ['key' => 'f_home_tab_feat', 'label' => 'Nổi bật', 'type' => 'tab', 'placement' => 'top'],
            ['key' => 'f_home_feat_eyebrow', 'label' => 'Eyebrow (dòng nhỏ)', 'name' => 'featured_eyebrow', 'type' => 'text', 'default_value' => 'Nổi bật', 'wrapper' => ['width' => 50]],
            ['key' => 'f_home_feat_heading', 'label' => 'Tiêu đề', 'name' => 'featured_heading', 'type' => 'text', 'default_value' => 'Được yêu thích nhất', 'wrapper' => ['width' => 50]],
            ['key' => 'f_home_feat_note', 'label' => 'Chọn sản phẩm nổi bật', 'type' => 'message', 'message' => 'Sản phẩm hiện ở mục này lấy theo cờ <strong>“Sản phẩm nổi bật”</strong> của WooCommerce. Vào <strong>Sản phẩm</strong> → bật ngôi sao ⭐ ở sản phẩm muốn hiện (hoặc trong trang sửa sản phẩm → Xuất bản → Hiển thị → Sản phẩm nổi bật). Chưa đánh dấu = tự hiện 4 sản phẩm mới nhất.'],

            // ===== Tab: Dự án =====
            ['key' => 'f_home_tab_proj', 'label' => 'Dự án', 'type' => 'tab', 'placement' => 'top'],
            ['key' => 'f_home_proj_eyebrow', 'label' => 'Eyebrow (dòng nhỏ)', 'name' => 'projects_eyebrow', 'type' => 'text', 'default_value' => 'Dự án', 'wrapper' => ['width' => 50]],
            ['key' => 'f_home_proj_heading', 'label' => 'Tiêu đề', 'name' => 'projects_heading', 'type' => 'text', 'default_value' => 'Không gian đã hoàn thiện', 'wrapper' => ['width' => 50]],
            ['key' => 'f_home_proj_pick', 'label' => 'Dự án hiển thị', 'name' => 'home_projects', 'type' => 'relationship', 'post_type' => ['du_an'], 'filters' => ['search'], 'return_format' => 'id', 'instructions' => 'Chọn & sắp xếp dự án hiện trong slider trang chủ. Để trống = 8 dự án mới nhất.'],

            // ===== Tab: Quy trình (khối "6 bước…" trên trang chủ) =====
            ['key' => 'f_home_tab_process', 'label' => 'Quy trình', 'type' => 'tab', 'placement' => 'top'],
            ['key' => 'f_home_proc_eyebrow', 'label' => 'Eyebrow (dòng nhỏ)', 'name' => 'process_eyebrow', 'type' => 'text', 'default_value' => 'Quy trình', 'wrapper' => ['width' => 50]],
            ['key' => 'f_home_proc_heading', 'label' => 'Tiêu đề', 'name' => 'process_heading', 'type' => 'text', 'default_value' => '6 bước, trọn vẹn an tâm', 'wrapper' => ['width' => 50]],
            [
                'key' => 'f_process', 'label' => 'Quy trình (6 bước)', 'name' => 'process', 'type' => 'repeater',
                'layout' => 'block', 'button_label' => 'Thêm bước',
                'sub_fields' => [
                    ['key' => 'f_proc_n',     'label' => 'Số',     'name' => 'n',     'type' => 'text', 'wrapper' => ['width' => 20]],
                    ['key' => 'f_proc_title', 'label' => 'Tiêu đề', 'name' => 'title', 'type' => 'text', 'wrapper' => ['width' => 40]],
                    ['key' => 'f_proc_icon',  'label' => 'Icon (lucide)', 'name' => 'icon', 'type' => 'text', 'wrapper' => ['width' => 40]],
                    ['key' => 'f_proc_desc',  'label' => 'Mô tả',  'name' => 'desc',  'type' => 'textarea', 'rows' => 2],
                ],
            ],

            // ===== Tab: Lý do chọn NẾP (khối lý do trên trang chủ) =====
            ['key' => 'f_home_tab_features', 'label' => 'Lý do chọn', 'type' => 'tab', 'placement' => 'top'],
            [
                'key' => 'f_features', 'label' => 'Lý do chọn NẾP', 'name' => 'features', 'type' => 'repeater',
                'layout' => 'block', 'button_label' => 'Thêm mục',
                'sub_fields' => [
                    ['key' => 'f_feat_title', 'label' => 'Tiêu đề', 'name' => 'title', 'type' => 'text', 'wrapper' => ['width' => 60]],
                    ['key' => 'f_feat_icon',  'label' => 'Icon (lucide)', 'name' => 'icon', 'type' => 'text', 'wrapper' => ['width' => 40]],
                    ['key' => 'f_feat_desc',  'label' => 'Mô tả',  'name' => 'desc',  'type' => 'textarea', 'rows' => 2],
                ],
            ],
        ],
        'location'   => [[['param' => 'page_type', 'operator' => '==', 'value' => 'front_page']]],
        'menu_order' => 0,
        'position'   => 'normal',
        'style'      => 'default',
    ]);

    // ---- Trang "Giới thiệu" (template-gioi-thieu) ----------------------
    acf_add_local_field_group([
        'key'    => 'group_page_about',
        'title'  => 'Nội dung trang Giới thiệu',
        'fields' => [
            ['key' => 'f_ab_tab_show', 'label' => 'Ẩn / Hiện', 'type' => 'tab'],
            ['key' => 'f_ab_show_msg', 'label' => '', 'type' => 'message', 'message' => 'Bật/tắt hiển thị từng section. Tắt = ẩn hẳn section khỏi trang.'],
            ['key' => 'f_ab_show_story',    'label' => 'Câu chuyện', 'name' => 'about_show_story',    'type' => 'true_false', 'ui' => 1, 'default_value' => 1, 'wrapper' => ['width' => 33]],
            ['key' => 'f_ab_show_stats',    'label' => 'Số liệu',    'name' => 'about_show_stats',    'type' => 'true_false', 'ui' => 1, 'default_value' => 1, 'wrapper' => ['width' => 33]],
            ['key' => 'f_ab_show_values',   'label' => 'Giá trị cốt lõi', 'name' => 'about_show_values', 'type' => 'true_false', 'ui' => 1, 'default_value' => 1, 'wrapper' => ['width' => 33]],
            ['key' => 'f_ab_show_timeline', 'label' => 'Hành trình', 'name' => 'about_show_timeline', 'type' => 'true_false', 'ui' => 1, 'default_value' => 1, 'wrapper' => ['width' => 33]],
            ['key' => 'f_ab_show_team',     'label' => 'Đội ngũ',    'name' => 'about_show_team',     'type' => 'true_false', 'ui' => 1, 'default_value' => 1, 'wrapper' => ['width' => 33]],
            ['key' => 'f_ab_show_cta',      'label' => 'CTA cuối trang', 'name' => 'about_show_cta',   'type' => 'true_false', 'ui' => 1, 'default_value' => 1, 'wrapper' => ['width' => 33]],

            ['key' => 'f_ab_tab_hero', 'label' => 'Hero', 'type' => 'tab'],
            ['key' => 'f_ab_eyebrow', 'label' => 'Eyebrow', 'name' => 'about_hero_eyebrow', 'type' => 'text', 'default_value' => 'Về chúng tôi'],
            ['key' => 'f_ab_title', 'label' => 'Tiêu đề', 'name' => 'about_hero_title', 'type' => 'text', 'default_value' => 'Nghề rèm, thêu —'],
            ['key' => 'f_ab_accent', 'label' => 'Từ nhấn (in nghiêng)', 'name' => 'about_hero_accent', 'type' => 'text', 'default_value' => 'chăm chút từng nếp gấp'],
            ['key' => 'f_ab_lead', 'label' => 'Mô tả', 'name' => 'about_lead', 'type' => 'textarea', 'rows' => 3, 'default_value' => 'NẾP ra đời năm 2014 từ một xưởng may rèm nhỏ ở Sài Gòn, với niềm tin rằng một tấm rèm đẹp có thể thay đổi cả cảm xúc của một căn nhà.'],
            ['key' => 'f_ab_hero_img', 'label' => 'Ảnh nền hero', 'name' => 'about_hero_image', 'type' => 'image', 'return_format' => 'url', 'preview_size' => 'medium'],

            ['key' => 'f_ab_tab_story', 'label' => 'Câu chuyện', 'type' => 'tab'],
            ['key' => 'f_ab_story_eyebrow', 'label' => 'Eyebrow', 'name' => 'about_story_eyebrow', 'type' => 'text', 'default_value' => 'Câu chuyện NẾP'],
            ['key' => 'f_ab_story_heading', 'label' => 'Tiêu đề', 'name' => 'about_story_heading', 'type' => 'text', 'default_value' => 'Từ một xưởng nhỏ đến thương hiệu được tin yêu'],
            ['key' => 'f_ab_story', 'label' => 'Đoạn văn', 'name' => 'about_story', 'type' => 'repeater', 'layout' => 'block', 'button_label' => 'Thêm đoạn', 'sub_fields' => [
                ['key' => 'f_ab_story_text', 'label' => 'Nội dung', 'name' => 'text', 'type' => 'textarea', 'rows' => 3],
            ]],
            ['key' => 'f_ab_story_img1', 'label' => 'Ảnh 1', 'name' => 'about_story_img1', 'type' => 'image', 'return_format' => 'url', 'preview_size' => 'medium', 'wrapper' => ['width' => 50]],
            ['key' => 'f_ab_story_img2', 'label' => 'Ảnh 2', 'name' => 'about_story_img2', 'type' => 'image', 'return_format' => 'url', 'preview_size' => 'medium', 'wrapper' => ['width' => 50]],

            ['key' => 'f_ab_tab_stats', 'label' => 'Số liệu', 'type' => 'tab'],
            ['key' => 'f_ab_stats', 'label' => 'Số liệu', 'name' => 'about_stats', 'type' => 'repeater', 'layout' => 'table', 'button_label' => 'Thêm số liệu', 'sub_fields' => [
                ['key' => 'f_ab_stat_v', 'label' => 'Số', 'name' => 'value', 'type' => 'text'],
                ['key' => 'f_ab_stat_l', 'label' => 'Nhãn', 'name' => 'label', 'type' => 'text'],
            ]],

            ['key' => 'f_ab_tab_values', 'label' => 'Giá trị', 'type' => 'tab'],
            ['key' => 'f_ab_val_eyebrow', 'label' => 'Eyebrow', 'name' => 'about_values_eyebrow', 'type' => 'text', 'default_value' => 'Giá trị cốt lõi'],
            ['key' => 'f_ab_val_heading', 'label' => 'Tiêu đề', 'name' => 'about_values_heading', 'type' => 'text', 'default_value' => 'Điều chúng tôi luôn giữ'],
            ['key' => 'f_ab_values', 'label' => 'Giá trị', 'name' => 'about_values', 'type' => 'repeater', 'layout' => 'block', 'button_label' => 'Thêm giá trị', 'sub_fields' => [
                ['key' => 'f_ab_val_icon', 'label' => 'Icon (lucide)', 'name' => 'icon', 'type' => 'text'],
                ['key' => 'f_ab_val_title', 'label' => 'Tiêu đề', 'name' => 'title', 'type' => 'text'],
                ['key' => 'f_ab_val_desc', 'label' => 'Mô tả', 'name' => 'desc', 'type' => 'textarea', 'rows' => 2],
            ]],

            ['key' => 'f_ab_tab_time', 'label' => 'Hành trình', 'type' => 'tab'],
            ['key' => 'f_ab_time_eyebrow', 'label' => 'Eyebrow', 'name' => 'about_timeline_eyebrow', 'type' => 'text', 'default_value' => 'Hành trình'],
            ['key' => 'f_ab_time_heading', 'label' => 'Tiêu đề', 'name' => 'about_timeline_heading', 'type' => 'text', 'default_value' => 'Những cột mốc đáng nhớ'],
            ['key' => 'f_ab_milestones', 'label' => 'Cột mốc', 'name' => 'about_milestones', 'type' => 'repeater', 'layout' => 'table', 'button_label' => 'Thêm cột mốc', 'sub_fields' => [
                ['key' => 'f_ab_ms_year', 'label' => 'Năm', 'name' => 'year', 'type' => 'text'],
                ['key' => 'f_ab_ms_text', 'label' => 'Mô tả', 'name' => 'text', 'type' => 'textarea', 'rows' => 2],
            ]],

            ['key' => 'f_ab_tab_team', 'label' => 'Đội ngũ', 'type' => 'tab'],
            ['key' => 'f_ab_team_eyebrow', 'label' => 'Eyebrow', 'name' => 'about_team_eyebrow', 'type' => 'text', 'default_value' => 'Đội ngũ'],
            ['key' => 'f_ab_team_heading', 'label' => 'Tiêu đề', 'name' => 'about_team_heading', 'type' => 'text', 'default_value' => 'Những con người làm nên NẾP'],
            ['key' => 'f_ab_team', 'label' => 'Thành viên', 'name' => 'about_team', 'type' => 'repeater', 'layout' => 'block', 'button_label' => 'Thêm thành viên', 'sub_fields' => [
                ['key' => 'f_ab_team_name', 'label' => 'Tên', 'name' => 'name', 'type' => 'text'],
                ['key' => 'f_ab_team_role', 'label' => 'Chức danh', 'name' => 'role', 'type' => 'text'],
                ['key' => 'f_ab_team_img', 'label' => 'Ảnh', 'name' => 'image', 'type' => 'image', 'return_format' => 'url', 'preview_size' => 'thumbnail'],
            ]],
        ],
        'location'   => [[['param' => 'page_template', 'operator' => '==', 'value' => 'template-gioi-thieu.blade.php']]],
        'menu_order' => 0,
    ]);

    // ---- Trang "Xưởng thêu" (template-xuong-theu) ---------------------
    acf_add_local_field_group([
        'key'    => 'group_page_emb',
        'title'  => 'Nội dung trang Xưởng thêu',
        'fields' => [
            ['key' => 'f_em_tab_show', 'label' => 'Ẩn / Hiện', 'type' => 'tab'],
            ['key' => 'f_em_show_msg', 'label' => '', 'type' => 'message', 'message' => 'Bật/tắt hiển thị từng section. Tắt = ẩn hẳn section khỏi trang.'],
            ['key' => 'f_em_show_stats',    'label' => 'Số liệu',   'name' => 'emb_show_stats',    'type' => 'true_false', 'ui' => 1, 'default_value' => 1, 'wrapper' => ['width' => 25]],
            ['key' => 'f_em_show_cap',      'label' => 'Năng lực',  'name' => 'emb_show_cap',      'type' => 'true_false', 'ui' => 1, 'default_value' => 1, 'wrapper' => ['width' => 25]],
            ['key' => 'f_em_show_services', 'label' => 'Dịch vụ thêu', 'name' => 'emb_show_services', 'type' => 'true_false', 'ui' => 1, 'default_value' => 1, 'wrapper' => ['width' => 25]],
            ['key' => 'f_em_show_cta',      'label' => 'CTA cuối',  'name' => 'emb_show_cta',      'type' => 'true_false', 'ui' => 1, 'default_value' => 1, 'wrapper' => ['width' => 25]],

            ['key' => 'f_em_tab_hero', 'label' => 'Hero', 'type' => 'tab'],
            ['key' => 'f_em_eyebrow', 'label' => 'Eyebrow', 'name' => 'emb_hero_eyebrow', 'type' => 'text', 'default_value' => 'Xưởng thêu vi tính'],
            ['key' => 'f_em_title', 'label' => 'Tiêu đề', 'name' => 'emb_hero_title', 'type' => 'text', 'default_value' => 'Logo, đồng phục & quà tặng'],
            ['key' => 'f_em_accent', 'label' => 'Từ nhấn (in nghiêng)', 'name' => 'emb_hero_accent', 'type' => 'text', 'default_value' => 'thêu sắc nét'],
            ['key' => 'f_em_desc', 'label' => 'Mô tả', 'name' => 'emb_hero_desc', 'type' => 'textarea', 'rows' => 3, 'default_value' => 'Hệ thống máy thêu vi tính đa kim hiện đại — nhận gia công số lượng lớn cho doanh nghiệp, trường học và sự kiện.'],
            ['key' => 'f_em_hero_img', 'label' => 'Ảnh nền hero', 'name' => 'emb_hero_image', 'type' => 'image', 'return_format' => 'url', 'preview_size' => 'medium'],
            ['key' => 'f_em_b1t', 'label' => 'Nút 1 — chữ', 'name' => 'emb_btn1_text', 'type' => 'text', 'default_value' => 'Xem máy thêu hoạt động', 'wrapper' => ['width' => 50]],
            ['key' => 'f_em_b1u', 'label' => 'Nút 1 — liên kết', 'name' => 'emb_btn1_url', 'type' => 'text', 'instructions' => 'Trống = trang Liên hệ.', 'wrapper' => ['width' => 50]],
            ['key' => 'f_em_b2t', 'label' => 'Nút 2 — chữ', 'name' => 'emb_btn2_text', 'type' => 'text', 'default_value' => 'Báo giá gia công', 'wrapper' => ['width' => 50]],
            ['key' => 'f_em_b2u', 'label' => 'Nút 2 — liên kết', 'name' => 'emb_btn2_url', 'type' => 'text', 'instructions' => 'Trống = trang Liên hệ.', 'wrapper' => ['width' => 50]],

            ['key' => 'f_em_tab_stats', 'label' => 'Số liệu', 'type' => 'tab'],
            ['key' => 'f_em_stats', 'label' => 'Số liệu', 'name' => 'emb_stats', 'type' => 'repeater', 'layout' => 'table', 'button_label' => 'Thêm số liệu', 'sub_fields' => [
                ['key' => 'f_em_stat_v', 'label' => 'Số', 'name' => 'value', 'type' => 'text'],
                ['key' => 'f_em_stat_l', 'label' => 'Nhãn', 'name' => 'label', 'type' => 'text'],
            ]],

            ['key' => 'f_em_tab_cap', 'label' => 'Năng lực', 'type' => 'tab'],
            ['key' => 'f_em_cap_eyebrow', 'label' => 'Eyebrow', 'name' => 'emb_cap_eyebrow', 'type' => 'text', 'default_value' => 'Năng lực sản xuất'],
            ['key' => 'f_em_cap_heading', 'label' => 'Tiêu đề', 'name' => 'emb_cap_heading', 'type' => 'text', 'default_value' => 'Máy móc hiện đại, sản lượng lớn'],
            ['key' => 'f_em_cap_text', 'label' => 'Mô tả', 'name' => 'emb_cap_text', 'type' => 'textarea', 'rows' => 3, 'default_value' => 'Hệ thống hơn 20 máy thêu vi tính đa kim cho phép chúng tôi xử lý đơn hàng lớn với chất lượng đồng đều và thời gian giao hàng nhanh.'],
            ['key' => 'f_em_cap_img1', 'label' => 'Ảnh 1', 'name' => 'emb_cap_img1', 'type' => 'image', 'return_format' => 'url', 'preview_size' => 'medium', 'wrapper' => ['width' => 50]],
            ['key' => 'f_em_cap_img2', 'label' => 'Ảnh 2', 'name' => 'emb_cap_img2', 'type' => 'image', 'return_format' => 'url', 'preview_size' => 'medium', 'wrapper' => ['width' => 50]],
            ['key' => 'f_em_caps', 'label' => 'Điểm năng lực', 'name' => 'emb_caps', 'type' => 'repeater', 'layout' => 'table', 'button_label' => 'Thêm điểm', 'sub_fields' => [
                ['key' => 'f_em_cap_item', 'label' => 'Nội dung', 'name' => 'text', 'type' => 'text'],
            ]],

            ['key' => 'f_em_tab_svc', 'label' => 'Dịch vụ', 'type' => 'tab'],
            ['key' => 'f_em_svc_eyebrow', 'label' => 'Eyebrow', 'name' => 'emb_svc_eyebrow', 'type' => 'text', 'default_value' => 'Dịch vụ thêu'],
            ['key' => 'f_em_svc_heading', 'label' => 'Tiêu đề', 'name' => 'emb_svc_heading', 'type' => 'text', 'default_value' => 'Chúng tôi nhận thêu gì?'],
            ['key' => 'f_em_services', 'label' => 'Dịch vụ', 'name' => 'emb_services', 'type' => 'repeater', 'layout' => 'block', 'button_label' => 'Thêm dịch vụ', 'sub_fields' => [
                ['key' => 'f_em_svc_title', 'label' => 'Tiêu đề', 'name' => 'title', 'type' => 'text'],
                ['key' => 'f_em_svc_desc', 'label' => 'Mô tả', 'name' => 'desc', 'type' => 'textarea', 'rows' => 2],
                ['key' => 'f_em_svc_icon', 'label' => 'Icon (lucide)', 'name' => 'icon', 'type' => 'text'],
                ['key' => 'f_em_svc_img', 'label' => 'Ảnh', 'name' => 'image', 'type' => 'image', 'return_format' => 'url', 'preview_size' => 'thumbnail'],
            ]],

            ['key' => 'f_em_tab_cta', 'label' => 'CTA cuối', 'type' => 'tab'],
            ['key' => 'f_em_cta_image', 'label' => 'Ảnh nền CTA', 'name' => 'emb_cta_image', 'type' => 'image', 'return_format' => 'url', 'preview_size' => 'medium', 'instructions' => 'Ảnh nền cho khối CTA cuối trang (sẽ phủ lớp tối lên trên). Trống = dùng ảnh mặc định.'],
            ['key' => 'f_em_cta_heading', 'label' => 'Tiêu đề', 'name' => 'emb_cta_heading', 'type' => 'text', 'default_value' => 'Gửi logo — nhận mẫu thêu trong 24 giờ'],
            ['key' => 'f_em_cta_text', 'label' => 'Mô tả', 'name' => 'emb_cta_text', 'type' => 'textarea', 'rows' => 2, 'default_value' => 'Đội ngũ tư vấn sẽ báo giá và gửi mẫu số hoá miễn phí cho đơn hàng của bạn.'],
            ['key' => 'f_em_cta_b1t', 'label' => 'Nút 1 — chữ', 'name' => 'emb_cta_btn1_text', 'type' => 'text', 'default_value' => 'Gửi logo báo giá', 'wrapper' => ['width' => 50]],
            ['key' => 'f_em_cta_b1u', 'label' => 'Nút 1 — liên kết', 'name' => 'emb_cta_btn1_url', 'type' => 'text', 'instructions' => 'Trống = trang Liên hệ.', 'wrapper' => ['width' => 50]],
            ['key' => 'f_em_cta_b2t', 'label' => 'Nút 2 — chữ', 'name' => 'emb_cta_btn2_text', 'type' => 'text', 'default_value' => 'Xem sản phẩm rèm', 'wrapper' => ['width' => 50]],
            ['key' => 'f_em_cta_b2u', 'label' => 'Nút 2 — liên kết', 'name' => 'emb_cta_btn2_url', 'type' => 'text', 'instructions' => 'Trống = trang Sản phẩm.', 'wrapper' => ['width' => 50]],
        ],
        'location'   => [[['param' => 'page_template', 'operator' => '==', 'value' => 'template-xuong-theu.blade.php']]],
        'menu_order' => 0,
    ]);

    // ---- Trang "Liên hệ" (template-lien-he) ---------------------------
    // Địa chỉ/điện thoại/email/giờ lấy từ "Cài đặt NẾP" (Options).
    acf_add_local_field_group([
        'key'    => 'group_page_contact',
        'title'  => 'Nội dung trang Liên hệ',
        'fields' => [
            ['key' => 'f_ct_banner_img', 'label' => 'Banner — ảnh nền', 'name' => 'contact_banner_image', 'type' => 'image', 'return_format' => 'url', 'preview_size' => 'medium', 'instructions' => 'Ảnh nền cho banner đầu trang (sẽ phủ lớp tối lên trên cho dễ đọc chữ). Để trống = dùng ảnh mặc định.'],
            ['key' => 'f_ct_eyebrow', 'label' => 'Banner — eyebrow', 'name' => 'contact_eyebrow', 'type' => 'text', 'default_value' => 'Liên hệ'],
            ['key' => 'f_ct_heading', 'label' => 'Banner — tiêu đề', 'name' => 'contact_heading', 'type' => 'text', 'default_value' => 'Cùng kiến tạo không gian của bạn'],
            ['key' => 'f_ct_desc', 'label' => 'Banner — mô tả', 'name' => 'contact_desc', 'type' => 'textarea', 'rows' => 2, 'default_value' => 'Để lại thông tin, đội ngũ NẾP sẽ liên hệ tư vấn và đặt lịch khảo sát miễn phí trong vòng 24 giờ.'],
            ['key' => 'f_ct_form_heading', 'label' => 'Tiêu đề form', 'name' => 'contact_form_heading', 'type' => 'text', 'default_value' => 'Gửi yêu cầu tư vấn'],
            ['key' => 'f_ct_subjects', 'label' => 'Chủ đề (dropdown)', 'name' => 'contact_subjects', 'type' => 'repeater', 'layout' => 'table', 'button_label' => 'Thêm chủ đề', 'sub_fields' => [
                ['key' => 'f_ct_subject', 'label' => 'Chủ đề', 'name' => 'label', 'type' => 'text'],
            ]],
            ['key' => 'f_ct_info_eyebrow', 'label' => 'Thông tin — eyebrow', 'name' => 'contact_info_eyebrow', 'type' => 'text', 'default_value' => 'Showroom'],
            ['key' => 'f_ct_info_heading', 'label' => 'Thông tin — tiêu đề', 'name' => 'contact_info_heading', 'type' => 'text', 'default_value' => 'Ghé thăm chúng tôi'],
            ['key' => 'f_ct_city', 'label' => 'Tên chi nhánh', 'name' => 'contact_branch_city', 'type' => 'text', 'default_value' => 'Thái Nguyên', 'wrapper' => ['width' => 50]],
            ['key' => 'f_ct_badge', 'label' => 'Nhãn chi nhánh', 'name' => 'contact_branch_badge', 'type' => 'text', 'default_value' => 'Trụ sở', 'wrapper' => ['width' => 50]],
            ['key' => 'f_ct_show_map', 'label' => 'Hiện section bản đồ', 'name' => 'contact_show_map', 'type' => 'true_false', 'ui' => 1, 'default_value' => 1, 'instructions' => 'Tắt = ẩn hẳn khối bản đồ cuối trang.'],
            ['key' => 'f_ct_map', 'label' => 'Google Maps — embed src', 'name' => 'contact_map_embed', 'type' => 'textarea', 'rows' => 3, 'instructions' => 'Dán URL trong thuộc tính src của iframe nhúng Google Maps.'],
        ],
        'location'   => [[['param' => 'page_template', 'operator' => '==', 'value' => 'template-lien-he.blade.php']]],
        'menu_order' => 0,
    ]);

    // ---- Trang "Bộ sưu tập" (template-bo-suu-tap) --------------------
    acf_add_local_field_group([
        'key'    => 'group_page_lookbook',
        'title'  => 'Nội dung trang Bộ sưu tập',
        'fields' => [
            ['key' => 'f_lb_tab_show', 'label' => 'Ẩn / Hiện', 'type' => 'tab'],
            ['key' => 'f_lb_show_msg', 'label' => '', 'type' => 'message', 'message' => 'Bật/tắt hiển thị từng section. Tắt = ẩn hẳn section khỏi trang.'],
            ['key' => 'f_lb_show_look',     'label' => 'Lookbook',  'name' => 'look_show_lookbook', 'type' => 'true_false', 'ui' => 1, 'default_value' => 1, 'wrapper' => ['width' => 33]],
            ['key' => 'f_lb_show_cat',      'label' => 'Danh mục',  'name' => 'look_show_cat',      'type' => 'true_false', 'ui' => 1, 'default_value' => 1, 'wrapper' => ['width' => 33]],
            ['key' => 'f_lb_show_featured', 'label' => 'Sản phẩm tiêu biểu', 'name' => 'look_show_featured', 'type' => 'true_false', 'ui' => 1, 'default_value' => 1, 'wrapper' => ['width' => 33]],

            ['key' => 'f_lb_tab_hero', 'label' => 'Hero', 'type' => 'tab'],
            ['key' => 'f_lb_eyebrow', 'label' => 'Eyebrow', 'name' => 'look_hero_eyebrow', 'type' => 'text', 'default_value' => 'Catalogue 2026'],
            ['key' => 'f_lb_title', 'label' => 'Tiêu đề', 'name' => 'look_hero_title', 'type' => 'text', 'default_value' => 'Bộ sưu tập'],
            ['key' => 'f_lb_accent', 'label' => 'Từ nhấn (in nghiêng)', 'name' => 'look_hero_accent', 'type' => 'text', 'default_value' => 'rèm cửa'],
            ['key' => 'f_lb_desc', 'label' => 'Mô tả', 'name' => 'look_hero_desc', 'type' => 'textarea', 'rows' => 3, 'default_value' => 'Từ linen mộc mạc đến nhung sang trọng — khám phá những bộ sưu tập được tuyển chọn cho mọi phong cách không gian.'],
            ['key' => 'f_lb_hero_img', 'label' => 'Ảnh nền hero', 'name' => 'look_hero_image', 'type' => 'image', 'return_format' => 'url', 'preview_size' => 'medium'],

            ['key' => 'f_lb_tab_look', 'label' => 'Lookbook', 'type' => 'tab'],
            ['key' => 'f_lb_look_eyebrow', 'label' => 'Eyebrow', 'name' => 'look_eyebrow', 'type' => 'text', 'default_value' => 'Lookbook'],
            ['key' => 'f_lb_look_heading', 'label' => 'Tiêu đề', 'name' => 'look_heading', 'type' => 'text', 'default_value' => 'Bộ sưu tập nổi bật'],
            ['key' => 'f_lb_large_name', 'label' => 'Thẻ lớn — tên', 'name' => 'look_large_name', 'type' => 'text', 'default_value' => 'Bộ sưu tập Linen', 'wrapper' => ['width' => 40]],
            ['key' => 'f_lb_large_tag', 'label' => 'Thẻ lớn — mô tả', 'name' => 'look_large_tagline', 'type' => 'text', 'default_value' => 'Mộc mạc & tự nhiên', 'wrapper' => ['width' => 40]],
            ['key' => 'f_lb_large_count', 'label' => 'Thẻ lớn — số mẫu', 'name' => 'look_large_count', 'type' => 'text', 'default_value' => '24', 'wrapper' => ['width' => 20]],
            ['key' => 'f_lb_large_img', 'label' => 'Thẻ lớn — ảnh', 'name' => 'look_large_img', 'type' => 'image', 'return_format' => 'url', 'preview_size' => 'medium'],
            ['key' => 'f_lb_smalls', 'label' => 'Thẻ nhỏ', 'name' => 'look_smalls', 'type' => 'repeater', 'layout' => 'block', 'button_label' => 'Thêm thẻ', 'sub_fields' => [
                ['key' => 'f_lb_sm_name', 'label' => 'Tên', 'name' => 'name', 'type' => 'text'],
                ['key' => 'f_lb_sm_tag', 'label' => 'Mô tả', 'name' => 'tagline', 'type' => 'text'],
                ['key' => 'f_lb_sm_count', 'label' => 'Số mẫu', 'name' => 'count', 'type' => 'text'],
                ['key' => 'f_lb_sm_img', 'label' => 'Ảnh', 'name' => 'image', 'type' => 'image', 'return_format' => 'url', 'preview_size' => 'thumbnail'],
            ]],

            ['key' => 'f_lb_tab_titles', 'label' => 'Tiêu đề mục', 'type' => 'tab'],
            ['key' => 'f_lb_cat_eyebrow', 'label' => 'Danh mục — eyebrow', 'name' => 'look_cat_eyebrow', 'type' => 'text', 'default_value' => 'Danh mục', 'wrapper' => ['width' => 50]],
            ['key' => 'f_lb_cat_heading', 'label' => 'Danh mục — tiêu đề', 'name' => 'look_cat_heading', 'type' => 'text', 'default_value' => 'Tất cả loại rèm', 'wrapper' => ['width' => 50]],
            ['key' => 'f_lb_cat_pick', 'label' => 'Danh mục hiển thị', 'name' => 'look_categories', 'type' => 'taxonomy', 'taxonomy' => 'product_cat', 'field_type' => 'multi_select', 'add_term' => 0, 'save_terms' => 0, 'load_terms' => 0, 'return_format' => 'id', 'instructions' => 'Chọn & sắp xếp danh mục muốn hiện. Để trống = hiện tất cả.'],
            ['key' => 'f_lb_feat_eyebrow', 'label' => 'Tuyển chọn — eyebrow', 'name' => 'look_feat_eyebrow', 'type' => 'text', 'default_value' => 'Tuyển chọn', 'wrapper' => ['width' => 50]],
            ['key' => 'f_lb_feat_heading', 'label' => 'Tuyển chọn — tiêu đề', 'name' => 'look_feat_heading', 'type' => 'text', 'default_value' => 'Sản phẩm tiêu biểu', 'wrapper' => ['width' => 50]],
            ['key' => 'f_lb_feat_note', 'label' => 'Chọn sản phẩm tiêu biểu', 'type' => 'message', 'message' => 'Sản phẩm ở mục này lấy theo cờ <strong>“Sản phẩm nổi bật”</strong> của WooCommerce (bật ngôi sao ⭐ ở Sản phẩm). Chưa đánh dấu = tự hiện 4 sản phẩm mới nhất.'],
        ],
        'location'   => [[['param' => 'page_template', 'operator' => '==', 'value' => 'template-bo-suu-tap.blade.php']]],
        'menu_order' => 0,
    ]);

    // ---- Catalog (CPT catalog: file PDF + ảnh bìa) --------------------
    acf_add_local_field_group([
        'key'    => 'group_catalog',
        'title'  => 'Catalog (PDF)',
        'fields' => [
            ['key' => 'f_cat_pdf', 'label' => 'File PDF', 'name' => 'catalog_pdf', 'type' => 'file', 'return_format' => 'array', 'mime_types' => 'pdf', 'instructions' => 'Tải lên file PDF để hiển thị bản xem trước trên trang.'],
            ['key' => 'f_cat_cover', 'label' => 'Ảnh bìa', 'name' => 'catalog_cover', 'type' => 'image', 'return_format' => 'url', 'preview_size' => 'medium', 'instructions' => 'Hiển thị ở trang danh sách Catalog. Trống = dùng Ảnh đại diện.'],
            ['key' => 'f_cat_excerpt', 'label' => 'Mô tả ngắn', 'name' => 'catalog_excerpt', 'type' => 'textarea', 'rows' => 2],
        ],
        'location'   => [[['param' => 'post_type', 'operator' => '==', 'value' => 'catalog']]],
        'menu_order' => 0,
        'position'   => 'acf_after_title',
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
            ['key' => 'f_sp_tab_attr', 'label' => 'Thuộc tính', 'type' => 'tab', 'placement' => 'top'],
            ['key' => 'f_sp_material', 'label' => 'Chất liệu', 'name' => 'material',  'type' => 'text', 'wrapper' => ['width' => 50]],
            ['key' => 'f_sp_color',    'label' => 'Màu',       'name' => 'color',     'type' => 'text', 'wrapper' => ['width' => 50]],
            ['key' => 'f_sp_colorhex', 'label' => 'Mã màu',    'name' => 'color_hex', 'type' => 'color_picker', 'wrapper' => ['width' => 50]],
            [
                'key' => 'f_sp_badge', 'label' => 'Nhãn', 'name' => 'badge', 'type' => 'select',
                'choices' => ['' => '— Không —', 'new' => 'Mới', 'hot' => 'Bán chạy'], 'allow_null' => 1,
                'wrapper' => ['width' => 50],
            ],

            // ---- Tab "Thông số": bảng thông số kỹ thuật (dùng chung mọi sản phẩm) ----
            ['key' => 'f_sp_tab_specs', 'label' => 'Thông số', 'type' => 'tab', 'placement' => 'top'],
            [
                'key' => 'f_sp_specs', 'label' => 'Bảng thông số', 'name' => 'specs', 'type' => 'repeater',
                'layout' => 'table', 'button_label' => 'Thêm dòng',
                'instructions' => 'Mỗi dòng = một thông số (vd: Độ dày · 280gsm). Chất liệu/Màu ở tab Thuộc tính sẽ tự thêm vào đầu bảng.',
                'sub_fields' => [
                    ['key' => 'f_sp_specs_k', 'label' => 'Tên thông số', 'name' => 'k', 'type' => 'text', 'wrapper' => ['width' => 40]],
                    ['key' => 'f_sp_specs_v', 'label' => 'Giá trị',      'name' => 'v', 'type' => 'text', 'wrapper' => ['width' => 60]],
                ],
            ],

            // ---- Tab "Hướng dẫn": sử dụng / bảo quản (WYSIWYG) ----
            ['key' => 'f_sp_tab_guide', 'label' => 'Hướng dẫn', 'type' => 'tab', 'placement' => 'top'],
            [
                'key' => 'f_sp_guide', 'label' => 'Hướng dẫn sử dụng & bảo quản', 'name' => 'guide', 'type' => 'wysiwyg',
                'tabs' => 'all', 'media_upload' => 1, 'toolbar' => 'full',
                'instructions' => 'Cách lắp đặt, vệ sinh, bảo quản… Hiển thị ở tab "Hướng dẫn" trên trang sản phẩm.',
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
 * ---- SEO box (mọi trang / bài / sản phẩm / dự án / catalog) -------------
 * Cho phép biên tập viên ghi đè tiêu đề, mô tả, ảnh chia sẻ, từ khoá và
 * noindex cho từng bài. Để trống = theme tự sinh (xem app/seo.php).
 */
add_action('acf/init', function () {
    if (! function_exists('acf_add_local_field_group')) {
        return;
    }

    acf_add_local_field_group([
        'key'    => 'group_nep_seo',
        'title'  => 'SEO',
        'fields' => [
            ['key' => 'f_seo_title', 'label' => 'Tiêu đề SEO', 'name' => 'seo_title', 'type' => 'text', 'maxlength' => 70, 'instructions' => 'Hiện trên Google & khi chia sẻ. Trống = tiêu đề bài + tên site. Nên ≤ 60 ký tự.'],
            ['key' => 'f_seo_desc', 'label' => 'Mô tả SEO', 'name' => 'seo_description', 'type' => 'textarea', 'rows' => 3, 'maxlength' => 165, 'instructions' => 'Đoạn mô tả dưới tiêu đề trên Google. Trống = tự lấy từ tóm tắt/nội dung. Nên 120–160 ký tự.'],
            ['key' => 'f_seo_kw', 'label' => 'Từ khoá', 'name' => 'seo_focus_keyword', 'type' => 'text', 'instructions' => 'Ghi chú nội bộ. Nhập 1 hoặc nhiều từ khoá, cách nhau bằng dấu phẩy (vd: thêu tay, xưởng thêu, thêu logo). Không in ra trang.'],
            ['key' => 'f_seo_img', 'label' => 'Ảnh chia sẻ (OG image)', 'name' => 'seo_og_image', 'type' => 'image', 'return_format' => 'url', 'preview_size' => 'medium', 'instructions' => 'Ảnh hiện khi share Facebook/Zalo. Nên 1200×630px, JPG/PNG (không dùng SVG). Trống = ảnh đại diện bài.'],
            ['key' => 'f_seo_noindex', 'label' => 'Ẩn khỏi công cụ tìm kiếm', 'name' => 'seo_noindex', 'type' => 'true_false', 'ui' => 1, 'instructions' => 'Bật = thêm noindex, Google sẽ không lập chỉ mục trang này.'],
        ],
        'location' => [
            [['param' => 'post_type', 'operator' => '==', 'value' => 'post']],
            [['param' => 'post_type', 'operator' => '==', 'value' => 'page']],
            [['param' => 'post_type', 'operator' => '==', 'value' => 'product']],
            [['param' => 'post_type', 'operator' => '==', 'value' => 'du_an']],
            [['param' => 'post_type', 'operator' => '==', 'value' => 'catalog']],
        ],
        'menu_order'            => 20,
        'position'              => 'normal',
        'style'                 => 'default',
        'label_placement'       => 'top',
        'hide_on_screen'        => '',
        'active'                => true,
        'description'           => 'Ghi đè SEO cho bài này (để trống = tự động).',
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

/**
 * Polish the "Cài đặt NẾP" options page layout (brand accent + readable widths).
 * Scoped to that screen only via the body class WordPress adds for it.
 */
add_action('admin_head', function () {
    $screen = function_exists('get_current_screen') ? get_current_screen() : null;
    if (! $screen || strpos((string) $screen->id, 'nep-settings') === false) {
        return;
    }
    ?>
    <style>
      .acf-admin-page #wpbody-content > .wrap { max-width: 1040px; }
      .acf-admin-page .postbox { border-color: #E7E0D5; border-radius: 10px; overflow: hidden; }
      .acf-admin-page .postbox > .postbox-header { background: #F8F6F3; border-bottom: 1px solid #E7E0D5; }
      .acf-admin-page .postbox > .postbox-header h2 { font-size: 14px; }
      /* Tab bar */
      .acf-tab-wrap .acf-tab-group { border-bottom: 1px solid #E7E0D5; }
      .acf-tab-group li a.acf-tab-button { color: #6b6b66; border-radius: 8px 8px 0 0; }
      .acf-tab-group li.active a.acf-tab-button,
      .acf-tab-group li a.acf-tab-button:focus { color: #4C5334; box-shadow: inset 0 -2px 0 #6E764F; }
      /* Fields: comfortable spacing + readable input width */
      .acf-fields > .acf-field { padding: 16px 20px; }
      .acf-field input[type="text"], .acf-field input[type="email"],
      .acf-field input[type="url"], .acf-field textarea { max-width: 620px; }
      /* Primary save button in brand olive */
      .acf-admin-page #publishing-action #publish,
      .acf-admin-page .button-primary { background: #6E764F !important; border-color: #5D6440 !important; box-shadow: none !important; text-shadow: none !important; }
      .acf-admin-page #publishing-action #publish:hover,
      .acf-admin-page .button-primary:hover { background: #5D6440 !important; }
    </style>
    <?php
});
