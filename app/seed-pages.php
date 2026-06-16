<?php

/**
 * Trình tạo dữ liệu mẫu: TRANG, MENU, FRONT PAGE & TUỲ CHỌN THEME.
 *
 * Bổ sung cho app/seed.php (vốn chỉ tạo sản phẩm / dự án / loại rèm).
 * File này lo phần "khung" của site:
 *   • Trang tĩnh + gán Template (Giới thiệu, Xưởng thêu, Liên hệ, Bộ sưu tập…)
 *   • Đặt Trang chủ tĩnh + Trang Tin tức
 *   • Dựng menu (primary_navigation + 3 menu footer) và gán vị trí
 *   • Ghi tuỳ chọn liên hệ (hotline, email, địa chỉ…) vào ACF Options
 *
 * Chạy theo 2 cách:
 *   1) Trang quản trị:  Công cụ → "NẾP · Dữ liệu mẫu" → bấm nút (KHÔNG cần WP-CLI)
 *   2) WP-CLI:          wp nep pages          (chỉ trang + menu + tuỳ chọn)
 *                       wp nep pages --fresh  (xoá các trang đã seed rồi tạo lại)
 *
 * Mọi thao tác đều idempotent: chạy lại nhiều lần không tạo bản trùng.
 */

namespace App;

use function add_action;

/**
 * Bản thiết kế các trang cần tạo.
 *
 * `template` = đường dẫn Blade tương đối trong resources/views (đúng giá trị mà
 * Sage/Acorn lưu vào meta `_wp_page_template`). Để rỗng = dùng template mặc định.
 *
 * @return array<int,array{slug:string,title:string,template:string,content:string,role?:string}>
 */
function nep_pages_blueprint(): array
{
    return [
        [
            'slug'     => 'trang-chu',
            'title'    => 'Trang chủ',
            'template' => '', // front-page.blade.php tự được dùng khi đặt làm trang chủ tĩnh
            'role'     => 'front',
            'content'  => 'NẾP — Xưởng thêu vi tính & rèm thiết kế.',
        ],
        [
            'slug'     => 'tin-tuc',
            'title'    => 'Tin tức',
            'template' => '',
            'role'     => 'blog',
            'content'  => '',
        ],
        [
            'slug'     => 'gioi-thieu',
            'title'    => 'Giới thiệu',
            'template' => 'template-gioi-thieu.blade.php',
            'content'  => 'Câu chuyện thương hiệu NẾP — uy tín tạo nên thương hiệu.',
        ],
        [
            'slug'     => 'xuong-theu',
            'title'    => 'Xưởng thêu',
            'template' => 'template-xuong-theu.blade.php',
            'content'  => 'Xưởng thêu vi tính: logo, đồng phục, quà tặng theo yêu cầu.',
        ],
        [
            'slug'     => 'bo-suu-tap',
            'title'    => 'Bộ sưu tập',
            'template' => 'template-bo-suu-tap.blade.php',
            'content'  => 'Bộ sưu tập rèm & dự án tiêu biểu của NẾP.',
        ],
        [
            'slug'     => 'lien-he',
            'title'    => 'Liên hệ',
            'template' => 'template-lien-he.blade.php',
            'content'  => 'Liên hệ NẾP để được khảo sát và tư vấn miễn phí.',
        ],
    ];
}

/**
 * Tạo hoặc cập nhật một trang theo slug (không tạo trùng).
 *
 * @return int ID trang
 */
function nep_upsert_page(string $slug, string $title, string $content = '', string $template = ''): int
{
    $existing = get_posts([
        'post_type'        => 'page',
        'name'             => $slug,
        'post_status'      => 'any',
        'numberposts'      => 1,
        'suppress_filters' => false,
    ]);

    $args = [
        'post_type'    => 'page',
        'post_title'   => $title,
        'post_name'    => $slug,
        'post_content' => $content,
        'post_status'  => 'publish',
    ];

    if ($existing) {
        $args['ID'] = $existing[0]->ID;
        $id = wp_update_post($args, true);
    } else {
        $id = wp_insert_post($args, true);
    }

    if (is_wp_error($id)) {
        return 0;
    }

    if ($template) {
        update_post_meta($id, '_wp_page_template', $template);
    } else {
        delete_post_meta($id, '_wp_page_template');
    }

    return (int) $id;
}

/**
 * Tạo toàn bộ trang + đặt trang chủ tĩnh + trang bài viết.
 *
 * @param  callable(string):void  $log
 * @return array<string,int> slug => page ID
 */
function nep_seed_pages(callable $log): array
{
    $ids = [];

    foreach (nep_pages_blueprint() as $page) {
        $id = nep_upsert_page($page['slug'], $page['title'], $page['content'], $page['template']);
        if (! $id) {
            $log("⚠ Không tạo được trang: {$page['title']}");
            continue;
        }
        $ids[$page['slug']] = $id;
        $log("Trang: {$page['title']}  (/{$page['slug']})");

        if (($page['role'] ?? '') === 'front') {
            update_option('show_on_front', 'page');
            update_option('page_on_front', $id);
        }
        if (($page['role'] ?? '') === 'blog') {
            update_option('page_for_posts', $id);
        }
    }

    $log('→ Đã đặt Trang chủ tĩnh & Trang tin tức.');

    return $ids;
}

/**
 * Dựng (hoặc dựng lại) một menu, nạp các mục và gán vào vị trí theme.
 *
 * @param  array<int,array<string,mixed>>  $items  Tham số cho wp_update_nav_menu_item()
 * @param  callable(string):void  $log
 */
function nep_build_menu(string $name, string $location, array $items, callable $log): void
{
    $menu = wp_get_nav_menu_object($name);
    $menu_id = $menu ? (int) $menu->term_id : (int) wp_create_nav_menu($name);

    if (! $menu_id || is_wp_error($menu_id)) {
        $log("⚠ Không tạo được menu: {$name}");
        return;
    }

    // Xoá mục cũ để tránh trùng khi chạy lại.
    foreach ((wp_get_nav_menu_items($menu_id) ?: []) as $item) {
        wp_delete_post($item->ID, true);
    }

    foreach ($items as $item) {
        wp_update_nav_menu_item($menu_id, 0, array_merge(['menu-item-status' => 'publish'], $item));
    }

    $locations = (array) get_theme_mod('nav_menu_locations', []);
    $locations[$location] = $menu_id;
    set_theme_mod('nav_menu_locations', $locations);

    $log("Menu: {$name}  → vị trí “{$location}” (" . count($items) . ' mục)');
}

/**
 * Tạo toàn bộ menu: điều hướng chính + 3 cột footer.
 *
 * @param  array<string,int>  $pages  slug => page ID (từ nep_seed_pages)
 * @param  callable(string):void  $log
 */
function nep_seed_menus(array $pages, callable $log): void
{
    $page = fn (string $slug, string $title) => [
        'menu-item-title'     => $title,
        'menu-item-object'    => 'page',
        'menu-item-object-id' => $pages[$slug] ?? 0,
        'menu-item-type'      => 'post_type',
    ];
    $link = fn (string $title, string $url) => [
        'menu-item-title' => $title,
        'menu-item-url'   => $url,
        'menu-item-type'  => 'custom',
    ];

    // --- Điều hướng chính (khớp fallback trong sections/header.blade.php) ---
    nep_build_menu('NẾP — Điều hướng chính', 'primary_navigation', [
        $page('trang-chu', 'Trang chủ'),
        $page('gioi-thieu', 'Giới thiệu'),
        $link('Sản phẩm', nep_shop_url()),
        [
            'menu-item-title'  => 'Dự án',
            'menu-item-type'   => 'post_type_archive',
            'menu-item-object' => 'du_an',
        ],
        $page('xuong-theu', 'Xưởng thêu'),
        $page('bo-suu-tap', 'Bộ sưu tập'),
        $page('lien-he', 'Liên hệ'),
    ], $log);

    // --- Footer: Sản phẩm (link tới các loại rèm) ---
    $cat_items = [];
    foreach (['Rèm vải', 'Rèm cầu vồng', 'Rèm cuốn', 'Rèm gỗ'] as $cat_name) {
        $term = get_term_by('name', $cat_name, 'product_cat');
        $cat_items[] = $term
            ? [
                'menu-item-title'     => $cat_name,
                'menu-item-object'    => 'product_cat',
                'menu-item-object-id' => $term->term_id,
                'menu-item-type'      => 'taxonomy',
            ]
            : $link($cat_name, nep_shop_url());
    }
    nep_build_menu('NẾP — Footer Sản phẩm', 'footer_products', $cat_items, $log);

    // --- Footer: Dịch vụ ---
    nep_build_menu('NẾP — Footer Dịch vụ', 'footer_services', [
        $link('Tư vấn thiết kế', home_url('/lien-he')),
        $link('Thi công lắp đặt', home_url('/lien-he')),
        $page('xuong-theu', 'Thêu vi tính'),
        $page('bo-suu-tap', 'Bộ sưu tập'),
    ], $log);

    // --- Footer: Công ty ---
    nep_build_menu('NẾP — Footer Công ty', 'footer_company', [
        $page('gioi-thieu', 'Giới thiệu'),
        [
            'menu-item-title'  => 'Dự án',
            'menu-item-type'   => 'post_type_archive',
            'menu-item-object' => 'du_an',
        ],
        $page('bo-suu-tap', 'Bộ sưu tập'),
        $page('lien-he', 'Liên hệ'),
    ], $log);
}

/**
 * Ghi thông tin liên hệ vào ACF Options (nếu có ACF). Dùng đúng default trong
 * helper App\nep() để trang Liên hệ / footer hiển thị ngay.
 *
 * @param  callable(string):void  $log
 */
function nep_seed_options(callable $log): void
{
    if (! function_exists('update_field')) {
        $log('… Bỏ qua tuỳ chọn liên hệ (chưa kích hoạt ACF).');
        return;
    }

    $options = [
        'hotline'     => '083.888.5005',
        'hotline_alt' => '084.888.5005',
        'slogan'      => 'Uy tín tạo nên thương hiệu',
        'brand_line'  => 'Xưởng thêu vi tính – Rèm thiết kế',
        'email'       => 'xinchao@nep.vn',
        'address'     => 'Ngã 3 Tân Hương, Phổ Yên, Thái Nguyên',
        'hours'       => '8:00 – 20:00, Thứ 2 – Chủ nhật',
    ];

    foreach ($options as $key => $value) {
        update_field($key, $value, 'option');
    }

    $log('Tuỳ chọn liên hệ: đã ghi vào ACF Options.');
}

/**
 * Orchestrator: tạo trang → menu → tuỳ chọn. Trả về log dạng mảng dòng.
 *
 * @return string[]
 */
function nep_seed_structure(): array
{
    $lines = [];
    $log = function (string $line) use (&$lines) {
        $lines[] = $line;
    };

    $pages = nep_seed_pages($log);
    nep_seed_menus($pages, $log);
    nep_seed_options($log);

    // Permalink có thể cần làm mới để URL trang/CPT hoạt động.
    flush_rewrite_rules(false);
    $log('→ Đã làm mới permalink.');

    return $lines;
}

/* ====================================================================== *
 *  GIAO DIỆN QUẢN TRỊ  —  Công cụ → "NẾP · Dữ liệu mẫu"
 * ====================================================================== */

add_action('admin_menu', function () {
    add_management_page(
        'NẾP · Dữ liệu mẫu',
        'NẾP · Dữ liệu mẫu',
        'manage_options',
        'nep-seed',
        __NAMESPACE__ . '\\nep_seed_admin_page'
    );
});

function nep_seed_admin_page(): void
{
    if (! current_user_can('manage_options')) {
        return;
    }

    $log = [];
    $did = '';

    if (! empty($_POST['nep_seed_action']) && check_admin_referer('nep_seed')) {
        $did = sanitize_key($_POST['nep_seed_action']);

        if ($did === 'structure' || $did === 'all') {
            $log = array_merge($log, nep_seed_structure());
        }

        if ($did === 'all') {
            // Nội dung (sản phẩm/dự án/loại rèm) — chỉ khi có WooCommerce.
            if (function_exists(__NAMESPACE__ . '\\nep_seed_content')) {
                $log[] = '— Đang tạo loại rèm / sản phẩm / dự án…';
                try {
                    nep_seed_content(false, function (string $line) use (&$log) {
                        $log[] = $line;
                    });
                    $log[] = 'Sản phẩm / dự án / loại rèm: xong.';
                } catch (\Throwable $e) {
                    $log[] = '⚠ ' . $e->getMessage();
                }
            }
        }
    }

    $has_woo = class_exists('WooCommerce');
    ?>
    <div class="wrap">
        <h1>NẾP · Tạo dữ liệu mẫu</h1>
        <p>Tự động tạo <strong>trang</strong>, <strong>menu</strong>, <strong>trang chủ tĩnh</strong> và
           <strong>thông tin liên hệ</strong> cho theme. Các thao tác an toàn để chạy lại — không tạo bản trùng.</p>

        <?php if ($log) : ?>
            <div class="notice notice-success">
                <p><strong>Hoàn tất!</strong></p>
                <pre style="white-space:pre-wrap;margin:0 0 12px;padding:8px 12px;background:#fff;border-left:4px solid #46b450;max-height:340px;overflow:auto"><?php
                    echo esc_html(implode("\n", $log));
                ?></pre>
            </div>
        <?php endif; ?>

        <table class="form-table" role="presentation"><tbody>
            <tr>
                <th scope="row">Khung site</th>
                <td>
                    <form method="post">
                        <?php wp_nonce_field('nep_seed'); ?>
                        <input type="hidden" name="nep_seed_action" value="structure">
                        <?php submit_button('Tạo Trang + Menu + Tuỳ chọn', 'secondary', 'submit', false); ?>
                        <p class="description">Trang (Giới thiệu, Xưởng thêu, Liên hệ, Bộ sưu tập, Trang chủ, Tin tức), 4 menu và thông tin liên hệ.</p>
                    </form>
                </td>
            </tr>
            <tr>
                <th scope="row">Toàn bộ</th>
                <td>
                    <form method="post">
                        <?php wp_nonce_field('nep_seed'); ?>
                        <input type="hidden" name="nep_seed_action" value="all">
                        <?php submit_button('Tạo TẤT CẢ (khung + sản phẩm/dự án)', 'primary', 'submit', false, $has_woo ? [] : ['disabled' => 'disabled']); ?>
                        <p class="description">
                            <?php echo $has_woo
                                ? 'Bao gồm tải ảnh demo từ Unsplash về Thư viện (cần mạng, có thể mất 1–2 phút).'
                                : '<em>Cần kích hoạt WooCommerce để tạo sản phẩm/dự án.</em>'; ?>
                        </p>
                    </form>
                </td>
            </tr>
        </tbody></table>
    </div>
    <?php
}

/* ====================================================================== *
 *  WP-CLI  —  wp nep pages [--fresh]
 * ====================================================================== */

add_action('cli_init', function () {
    if (! class_exists('WP_CLI')) {
        return;
    }

    \WP_CLI::add_command('nep pages', function ($args, $assoc) {
        if (! empty($assoc['fresh'])) {
            foreach (nep_pages_blueprint() as $page) {
                foreach (get_posts(['post_type' => 'page', 'name' => $page['slug'], 'post_status' => 'any', 'numberposts' => 1]) as $p) {
                    wp_delete_post($p->ID, true);
                }
            }
            \WP_CLI::log('Đã xoá các trang seed cũ.');
        }

        foreach (nep_seed_structure() as $line) {
            \WP_CLI::log($line);
        }

        \WP_CLI::success('Đã tạo trang, menu và tuỳ chọn. Kiểm tra ngoài trang chủ.');
    });
});
