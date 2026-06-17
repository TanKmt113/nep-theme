<?php

/**
 * Theme setup — assets, supports, menus, image sizes.
 */

namespace App;

use function add_action;
use function add_filter;
use function add_theme_support;
use function register_nav_menus;

/**
 * Read the Vite build manifest (public/build/manifest.json) and return the
 * resolved CSS/JS URLs for a given entry. Returns null when the theme hasn't
 * been built yet (run `npm run build`).
 *
 * @return array{js: ?string, css: string[]}|null
 */
function vite_entry(string $entry): ?array
{
    static $manifest = null;

    if ($manifest === null) {
        $path = get_theme_file_path('public/build/manifest.json');
        $manifest = is_readable($path)
            ? (json_decode(file_get_contents($path), true) ?: [])
            : [];
    }

    if (empty($manifest[$entry])) {
        return null;
    }

    $base = get_theme_file_uri('public/build');
    $chunk = $manifest[$entry];

    return [
        'js'  => isset($chunk['file']) ? "{$base}/{$chunk['file']}" : null,
        'css' => array_map(fn ($f) => "{$base}/{$f}", $chunk['css'] ?? []),
    ];
}

/**
 * Enqueue the Vite-built CSS/JS bundle (resources/css/app.css + resources/js/app.js).
 */
add_action('wp_enqueue_scripts', function () {
    // Icons giờ là inline SVG (app/icons.php) — không còn nạp Lucide từ CDN.
    $app = vite_entry('resources/js/app.js');
    if (! $app) {
        return;
    }

    foreach ($app['css'] as $i => $href) {
        wp_enqueue_style('nep-app' . ($i ? "-{$i}" : ''), $href, [], null);
    }

    if ($app['js']) {
        wp_enqueue_script('nep-app', $app['js'], [], null, true);
    }
}, 100);

/**
 * Vite outputs ES modules — mark the bundle <script> as type="module".
 */
add_filter('script_loader_tag', function ($tag, $handle, $src) {
    if ($handle !== 'nep-app') {
        return $tag;
    }

    return sprintf('<script type="module" src="%s"></script>' . "\n", esc_url($src));
}, 10, 3);

/**
 * Editor assets (Gutenberg) — reuse the same design tokens.
 */
add_action('enqueue_block_editor_assets', function () {
    $app = vite_entry('resources/js/app.js');
    if (! $app) {
        return;
    }

    foreach ($app['css'] as $i => $href) {
        wp_enqueue_style('nep-app-editor' . ($i ? "-{$i}" : ''), $href, [], null);
    }
}, 100);

/**
 * Theme supports.
 */
add_action('after_setup_theme', function () {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', ['search-form', 'gallery', 'caption', 'style', 'script']);
    add_theme_support('responsive-embeds');
    add_theme_support('editor-styles');

    // Primary nav (mirrors NEP_DATA.nav). Manage at Appearance → Menus.
    register_nav_menus([
        'primary_navigation' => __('Primary Navigation', 'nep'),
        'footer_products'    => __('Footer — Sản phẩm', 'nep'),
        'footer_services'    => __('Footer — Dịch vụ', 'nep'),
        'footer_company'     => __('Footer — Công ty', 'nep'),
    ]);
}, 20);

/**
 * Menu 2 cấp: tự thêm các loại rèm (product_cat) làm menu con dưới mục "Sản phẩm"
 * (trỏ tới trang Shop). Nhờ vậy header có dropdown ngay mà không cần thêm tay trong
 * Giao diện → Menu. Nếu admin đã tự thêm menu con thì giữ nguyên (không đụng vào).
 */
add_filter('wp_nav_menu_objects', function ($items, $args) {
    if (($args->theme_location ?? '') !== 'primary_navigation' || ! \function_exists('wc_get_page_permalink')) {
        return $items;
    }

    // Tìm mục cha: link tới trang Shop, hoặc tiêu đề "Sản phẩm".
    $shop = \untrailingslashit((string) \wc_get_page_permalink('shop'));
    $parent = null;
    foreach ($items as $it) {
        $title = \function_exists('mb_strtolower') ? \mb_strtolower(\trim($it->title)) : \strtolower(\trim($it->title));
        if (($shop && \untrailingslashit($it->url) === $shop) || $title === 'sản phẩm') {
            $parent = $it;
            break;
        }
    }
    if (! $parent) {
        return $items;
    }

    // Đã có menu con (admin tự thêm) → không tự đổ nữa.
    foreach ($items as $it) {
        if ((int) $it->menu_item_parent === (int) $parent->ID) {
            return $items;
        }
    }

    $terms = \get_terms([
        'taxonomy'   => 'product_cat',
        'hide_empty' => false,
        'parent'     => 0,
        'orderby'    => 'name',
        'exclude'    => \array_filter([(int) \get_option('default_product_cat')]), // bỏ "Chưa phân loại"
    ]);
    if (\is_wp_error($terms) || ! $terms) {
        return $items;
    }

    if (! \in_array('menu-item-has-children', (array) $parent->classes, true)) {
        $parent->classes[] = 'menu-item-has-children';
    }

    foreach ($terms as $term) {
        $link = \get_term_link($term);
        if (\is_wp_error($link)) {
            continue;
        }
        $child = clone $parent;                 // giữ nguyên cấu trúc object menu item
        $child->ID = $child->db_id = 900000 + $term->term_id;
        $child->title = $term->name;
        $child->url = $link;
        $child->menu_item_parent = (string) $parent->ID;
        $child->classes = ['menu-item', 'menu-item-type-taxonomy'];
        $child->current = $child->current_item_ancestor = $child->current_item_parent = false;
        $items[] = $child;
    }

    return $items;
}, 10, 2);

/**
 * Custom image sizes used by the product/project cards (4:5 portrait crop).
 */
add_action('after_setup_theme', function () {
    add_image_size('nep_card', 900, 1125, true);     // 4:5 product card
    add_image_size('nep_wide', 1800, 1000, true);    // hero / wide
});

/**
 * Drop the default "Lưu trữ:" / "Archive:" prefix from archive titles.
 */
add_filter('get_the_archive_title_prefix', '__return_empty_string');

/**
 * Project archive: 12 per page (grid + pagination scales when projects grow).
 */
add_action('pre_get_posts', function ($q) {
    if (! is_admin() && $q->is_main_query() && $q->is_post_type_archive('du_an')) {
        $q->set('posts_per_page', 12);
    }
});

/**
 * Auto-purge nginx full-page cache (nginx-helper) on demand.
 *
 * Mỗi lần `npm run build` đổi hash asset và xoá file cũ; các trang còn trong
 * nginx full-page cache vẫn trỏ tới hash CSS/JS cũ → 404 → mất CSS. Endpoint này
 * cho phép xoá toàn bộ cache đúng quyền (chạy dưới php-fpm = user www).
 *
 * Gọi: GET /?nep_purge=<token>  (token = NEP_PURGE_TOKEN, mặc định bên dưới).
 * Được dùng tự động bởi npm script `postbuild` trong package.json.
 */
add_action('init', function () {
    if (empty($_GET['nep_purge'])) {
        return;
    }
    $token = defined('NEP_PURGE_TOKEN') ? NEP_PURGE_TOKEN : 'nep-build-purge';
    if (! hash_equals($token, (string) $_GET['nep_purge'])) {
        status_header(403);
        exit('forbidden');
    }

    $done = [];
    if (has_action('rt_nginx_helper_purge_all')) {
        do_action('rt_nginx_helper_purge_all');
        $done[] = 'nginx-helper';
    }
    global $nginx_purger;
    if (isset($nginx_purger) && method_exists($nginx_purger, 'purge_all')) {
        $nginx_purger->purge_all();
        $done[] = 'purger';
    }

    nocache_headers();
    header('Content-Type: text/plain');
    exit('purged: ' . ($done ? implode(',', array_unique($done)) : 'NONE'));
});

/**
 * HIỆU SUẤT: chỉ nạp CSS của một block khi block đó THỰC SỰ xuất hiện trên trang
 * (thay vì mặc định nạp CSS mọi block ở mọi trang). Nhờ vậy `wc-blocks.css` và CSS
 * các block lõi không còn tải ở trang chủ / trang không dùng block đó → giảm CSS
 * chặn hiển thị.
 */
add_filter('should_load_separate_core_block_assets', '__return_true');
