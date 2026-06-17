<?php

/**
 * NẾP theme bootstrap.
 * Boots Acorn (the Laravel framework inside WordPress that powers Blade,
 * View Composers, etc.) then loads the theme's own setup files.
 */

/*
|--------------------------------------------------------------------------
| Register the autoloader
|--------------------------------------------------------------------------
*/
if (! file_exists($composer = __DIR__ . '/vendor/autoload.php')) {
    wp_die(__('You need to run <code>composer install</code> in the NẾP theme directory.', 'nep'));
}

require $composer;

/*
|--------------------------------------------------------------------------
| Boot Acorn
|--------------------------------------------------------------------------
*/
if (! function_exists('\Roots\bootloader')) {
    wp_die(
        __('You need to install Acorn to use this theme.', 'nep'),
        '',
        ['link_url' => 'https://roots.io/acorn/docs/installation/', 'link_text' => 'Acorn docs']
    );
}

\Roots\bootloader()->boot();

/*
|--------------------------------------------------------------------------
| Load theme files (setup, post types, fields, helpers, seeder)
|--------------------------------------------------------------------------
*/
collect(['setup', 'helpers', 'post-types', 'fields', 'woocommerce', 'seo', 'webp', 'seed', 'seed-pages', 'sync-content', 'contact'])
    ->each(function ($file) {
        if (! locate_template($file = "app/{$file}.php", true, true, [])) {
            wp_die(
                sprintf(__('Error locating <code>app/%s</code> for inclusion.', 'nep'), $file)
            );
        }
    });
