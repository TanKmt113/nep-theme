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
            'home'       => $this->home(),
        ];
    }

    /**
     * Editable homepage content (ACF field group "Nội dung Trang chủ" gắn vào
     * trang đặt làm Trang chủ). Mỗi giá trị có fallback về text mặc định, nên
     * trang vẫn hiển thị đầy đủ kể cả khi ACF trống hoặc chưa cài.
     */
    protected function home(): array
    {
        $id  = (int) get_option('page_on_front');
        $get = function (string $name, $default = '') use ($id) {
            if ($id && function_exists('get_field')) {
                $v = get_field($name, $id);
                if ($v !== null && $v !== '' && $v !== false && $v !== []) {
                    return $v;
                }
            }
            return $default;
        };

        return [
            // Hero
            'hero_eyebrow'      => $get('hero_eyebrow', 'Rèm cửa cao cấp · Từ 2014'),
            'hero_title'        => $get('hero_title', 'Kiến tạo không gian sống'),
            'hero_title_accent' => $get('hero_title_accent', 'đẳng cấp'),
            'hero_desc'         => $get('hero_desc', 'Thiết kế, thi công và lắp đặt rèm cửa cao cấp cùng xưởng thêu vi tính — chăm chút trong từng đường nét.'),
            'hero_image'        => $get('hero_image', 'https://images.unsplash.com/photo-1616594039964-ae9021a400a0?w=1800&q=80'),
            'hero_btn1_text'    => $get('hero_btn1_text', 'Xem bộ sưu tập'),
            'hero_btn1_url'     => $get('hero_btn1_url', \App\nep_shop_url()),
            'hero_btn2_text'    => $get('hero_btn2_text', 'Nhận báo giá'),
            'hero_btn2_url'     => $get('hero_btn2_url', home_url('/lien-he')),
            'stats'             => $get('stats', [
                ['value' => '10+',   'label' => 'Năm kinh nghiệm'],
                ['value' => '5000+', 'label' => 'Khách hàng'],
                ['value' => '100+',  'label' => 'Mẫu rèm'],
            ]),

            // Intro / Về NẾP
            'intro_eyebrow'     => $get('intro_eyebrow', 'Về NẾP'),
            'intro_heading'     => $get('intro_heading', 'Nghề rèm, thêu — chăm chút từng nếp gấp'),
            'intro_text'        => $get('intro_text', 'Suốt một thập kỷ, NẾP đồng hành cùng hàng nghìn gia đình và doanh nghiệp Việt — mang đến những bộ rèm và sản phẩm thêu bền đẹp, tinh tế và đậm dấu ấn riêng.'),
            'intro_image'       => $get('intro_image', 'https://images.unsplash.com/photo-1524758631624-e2822e304c36?w=1100&q=80'),
            'intro_badge_value' => $get('intro_badge_value', '4.9/5'),
            'intro_badge_label' => $get('intro_badge_label', '1.200+ đánh giá'),

            // Tiêu đề các mục
            'cat_eyebrow'       => $get('cat_eyebrow', 'Danh mục'),
            'cat_heading'       => $get('cat_heading', 'Bộ sưu tập rèm cửa'),
            'featured_eyebrow'  => $get('featured_eyebrow', 'Nổi bật'),
            'featured_heading'  => $get('featured_heading', 'Được yêu thích nhất'),
            'process_eyebrow'   => $get('process_eyebrow', 'Quy trình'),
            'process_heading'   => $get('process_heading', '6 bước, trọn vẹn an tâm'),
            'projects_eyebrow'  => $get('projects_eyebrow', 'Dự án'),
            'projects_heading'  => $get('projects_heading', 'Không gian đã hoàn thiện'),
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
