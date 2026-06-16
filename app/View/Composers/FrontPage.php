<?php

namespace App\View\Composers;

use Roots\Acorn\View\Composer;

/**
 * Supplies the homepage sections with data (queried from WordPress),
 * mirroring what Home.jsx pulled from NEP_DATA.
 */
class FrontPage extends Composer
{
    protected static $views = [
        'front-page',
    ];

    public function with()
    {
        return [
            'featured'   => $this->featured(),
            'categories' => $this->categories(),
            'projects'   => $this->projects(),
            'process'    => function_exists('get_field') ? (get_field('process', 'option') ?: []) : [],
            'features'   => function_exists('get_field') ? (get_field('features', 'option') ?: []) : [],
        ];
    }

    /** Up to 4 featured products (WooCommerce). */
    protected function featured(): array
    {
        return get_posts([
            'post_type'   => 'product',
            'numberposts' => 4,
            'post_status' => 'publish',
        ]);
    }

    /** Curtain categories (product_cat terms) with their thumbnail + count. */
    protected function categories(): array
    {
        $terms = get_terms(['taxonomy' => 'product_cat', 'hide_empty' => false]);
        return is_wp_error($terms) ? [] : $terms;
    }

    /** All projects. */
    protected function projects(): array
    {
        return get_posts([
            'post_type'   => 'du_an',
            'numberposts' => -1,
        ]);
    }
}
