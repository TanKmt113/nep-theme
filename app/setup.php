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
    // Lucide icons từ CDN (UMD) — render <i data-lucide> trong app.js.
    // In ở footer trước bundle module nên window.lucide sẵn sàng khi app.js chạy.
    wp_enqueue_script('lucide', 'https://unpkg.com/lucide@0.544.0/dist/umd/lucide.min.js', [], '0.544.0', true);

    $app = vite_entry('resources/js/app.js');
    if (! $app) {
        return;
    }

    foreach ($app['css'] as $i => $href) {
        wp_enqueue_style('nep-app' . ($i ? "-{$i}" : ''), $href, [], null);
    }

    if ($app['js']) {
        wp_enqueue_script('nep-app', $app['js'], ['lucide'], null, true);
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
