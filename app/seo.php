<?php

/**
 * SEO — meta description, Open Graph, Twitter Card, canonical và JSON-LD.
 *
 * Theme tự xử lý SEO không cần plugin (Yoast/Rank Math). Tất cả thẻ được in
 * vào <head> qua hook wp_head. Nếu sau này cài plugin SEO, hãy bỏ 'seo' khỏi
 * danh sách nạp trong functions.php để tránh trùng thẻ.
 */

namespace App\Seo;

use WC_Product;

use function add_action;
use function add_filter;

/**
 * Đọc một ô SEF nhập tay (ACF group "SEO") của bài/trang hiện tại.
 * Trả về '' nếu không phải trang đơn, chưa cài ACF, hoặc ô để trống.
 */
function override(string $key): string
{
    if (! is_singular() || ! function_exists('get_field')) {
        return '';
    }

    $value = get_field($key, get_queried_object_id());

    return is_string($value) ? trim($value) : '';
}

/**
 * Cắt chuỗi thành mô tả meta sạch (gỡ HTML, shortcode, xuống dòng).
 */
function clean_description(string $text, int $max = 160): string
{
    $text = wp_strip_all_tags(strip_shortcodes($text), true);
    $text = trim(preg_replace('/\s+/', ' ', $text));

    if (mb_strlen($text) <= $max) {
        return $text;
    }

    $cut = mb_substr($text, 0, $max);
    // cắt ở khoảng trắng cuối để không đứt giữa từ
    $space = mb_strrpos($cut, ' ');

    return rtrim($space ? mb_substr($cut, 0, $space) : $cut, " ,.;:-") . '…';
}

/**
 * Mô tả phù hợp ngữ cảnh trang hiện tại.
 */
function description(): string
{
    // Ưu tiên mô tả nhập tay trong box SEO.
    if ($manual = override('seo_description')) {
        return clean_description($manual, 165);
    }

    if (is_singular('product') && function_exists('wc_get_product')) {
        $product = wc_get_product(get_queried_object_id());

        if ($product instanceof WC_Product) {
            $short = $product->get_short_description() ?: $product->get_description();

            if ($short) {
                return clean_description($short);
            }
        }
    }

    if (is_singular()) {
        $post = get_queried_object();

        if (has_excerpt($post)) {
            return clean_description(get_the_excerpt($post));
        }

        if (! empty($post->post_content)) {
            return clean_description($post->post_content);
        }
    }

    if (is_tax() || is_category() || is_tag()) {
        $term = get_queried_object();

        if (! empty($term->description)) {
            return clean_description($term->description);
        }
    }

    return clean_description(get_bloginfo('description'));
}

/**
 * Ảnh đại diện trang kèm kích thước thật (cho og:image:width/height/type).
 * Thứ tự: ảnh SEO nhập tay → ảnh đại diện → ảnh OG mặc định → logo SVG.
 *
 * @return array{url: string, width: int, height: int, type: string}
 */
function og_image(): array
{
    // Phân giải kích thước/MIME từ URL ảnh trong Thư viện (nếu có).
    $resolve = function (string $url): array {
        $id = attachment_url_to_postid($url);

        if ($id) {
            $meta = wp_get_attachment_metadata($id);

            return [
                'url'    => $url,
                'width'  => (int) ($meta['width'] ?? 0),
                'height' => (int) ($meta['height'] ?? 0),
                'type'   => (string) get_post_mime_type($id),
            ];
        }

        return ['url' => $url, 'width' => 0, 'height' => 0, 'type' => ''];
    };

    // Ưu tiên ảnh chia sẻ nhập tay trong box SEO.
    if ($manual = override('seo_og_image')) {
        return $resolve($manual);
    }

    if (is_singular() && has_post_thumbnail()) {
        $src = wp_get_attachment_image_src(get_post_thumbnail_id(), 'large');

        if ($src) {
            return [
                'url'    => $src[0],
                'width'  => (int) $src[1],
                'height' => (int) $src[2],
                'type'   => (string) get_post_mime_type(get_post_thumbnail_id()),
            ];
        }
    }

    // Ảnh chia sẻ mặc định cấu hình ở Cài đặt NẾP (nên là JPG/PNG 1200×630).
    $default = function_exists('App\\nep') ? \App\nep('og_default') : '';
    if (is_string($default) && $default !== '') {
        return $resolve($default);
    }

    // Cuối cùng mới dùng logo SVG (mạng xã hội có thể không render SVG).
    return [
        'url'    => get_theme_file_uri('public/images/logo-mark.svg'),
        'width'  => 0,
        'height' => 0,
        'type'   => 'image/svg+xml',
    ];
}

/**
 * URL ảnh đại diện trang (og:image, twitter:image). Giữ chữ ký cũ cho JSON-LD.
 */
function image_url(): string
{
    return og_image()['url'];
}

/**
 * Bản ảnh an toàn cho mạng xã hội. Vì ảnh đã chuyển sang WebP, một số nền tảng
 * (Zalo, MXH cũ) không hiện preview WebP — nên nếu og:image là .webp, sinh
 * (1 lần, cache trên đĩa) bản .jpg cạnh nó. Dùng GD trực tiếp để KHÔNG bị filter
 * image_editor_output_format (app/webp.php) ép ngược về WebP.
 *
 * @param  array{url: string, width: int, height: int, type: string}  $img
 * @return array{url: string, width: int, height: int, type: string}
 */
function social_image(array $img): array
{
    $url = $img['url'];

    if (! preg_match('/\.webp$/i', $url) || ! function_exists('imagecreatefromwebp')) {
        return $img; // jpg/png/svg đã ổn cho MXH
    }

    $up = wp_get_upload_dir();
    if (strpos($url, $up['baseurl']) !== 0) {
        return $img; // ngoài thư mục uploads (vd logo theme) — bỏ qua
    }

    $path    = $up['basedir'] . substr($url, strlen($up['baseurl']));
    $jpgPath = preg_replace('/\.webp$/i', '-og.jpg', $path);
    $jpgUrl  = preg_replace('/\.webp$/i', '-og.jpg', $url);

    if (! file_exists($jpgPath)) {
        if (! is_file($path)) {
            return $img;
        }

        $src = @imagecreatefromwebp($path);
        if (! $src) {
            return $img;
        }

        $w  = imagesx($src);
        $h  = imagesy($src);
        $bg = imagecreatetruecolor($w, $h);
        imagefilledrectangle($bg, 0, 0, $w, $h, imagecolorallocate($bg, 255, 255, 255));
        imagecopy($bg, $src, 0, 0, 0, 0, $w, $h);
        imagejpeg($bg, $jpgPath, 85);
        imagedestroy($src);
        imagedestroy($bg);

        if (! file_exists($jpgPath)) {
            return $img;
        }
    }

    return ['url' => $jpgUrl, 'width' => $img['width'], 'height' => $img['height'], 'type' => 'image/jpeg'];
}

/**
 * Chỉ thị robots cho trang hiện tại ('' = để mặc định, cho index).
 */
function robots_directive(): string
{
    // Trang tìm kiếm nội bộ: không index (nội dung mỏng, vô số biến thể).
    if (is_search()) {
        return 'noindex, follow';
    }

    // Biên tập viên bật noindex trong box SEO.
    if (is_singular() && function_exists('get_field') && get_field('seo_noindex', get_queried_object_id())) {
        return 'noindex, follow';
    }

    // Trang phân trang 2, 3… của archive: không index để tránh trùng lặp.
    if (is_paged()) {
        return 'noindex, follow';
    }

    return '';
}

/**
 * Các nấc breadcrumb [tên => url] theo loại trang đơn hiện tại.
 * Trả về [] nếu không cần breadcrumb (vd trang chủ).
 *
 * @return array<int, array{name: string, url: string}>
 */
function breadcrumb_items(): array
{
    if (! is_singular() || is_front_page()) {
        return [];
    }

    $post  = get_queried_object();
    $items = [['name' => 'Trang chủ', 'url' => home_url('/')]];

    $section = null;
    if (is_singular('product')) {
        $url = function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : get_post_type_archive_link('product');
        $section = ['name' => 'Sản phẩm', 'url' => $url];
    } elseif (is_singular('du_an')) {
        $section = ['name' => 'Dự án', 'url' => get_post_type_archive_link('du_an')];
    } elseif (is_singular('catalog')) {
        $section = ['name' => 'Catalog', 'url' => get_post_type_archive_link('catalog')];
    } elseif (is_singular('post')) {
        $blog = get_option('page_for_posts');
        $section = ['name' => 'Tin tức', 'url' => $blog ? get_permalink($blog) : home_url('/')];
    }

    if ($section && ! empty($section['url'])) {
        $items[] = $section;
    }

    $items[] = ['name' => get_the_title($post), 'url' => (string) get_permalink($post)];

    return $items;
}

/**
 * URL canonical của trang hiện tại.
 */
function canonical_url(): string
{
    if (is_singular()) {
        return (string) get_permalink();
    }

    if (is_tax() || is_category() || is_tag()) {
        $link = get_term_link(get_queried_object());

        return is_wp_error($link) ? home_url(add_query_arg([], null)) : (string) $link;
    }

    if (is_post_type_archive()) {
        return (string) get_post_type_archive_link(get_query_var('post_type') ?: get_post_type());
    }

    if (is_front_page() || is_home()) {
        return home_url('/');
    }

    return home_url(add_query_arg([], $GLOBALS['wp']->request ? "/{$GLOBALS['wp']->request}/" : '/'));
}

/**
 * Loại og:type.
 */
function og_type(): string
{
    if (is_singular('product')) {
        return 'product';
    }

    if (is_singular(['post', 'du_an'])) {
        return 'article';
    }

    return 'website';
}

/**
 * In meta description, Open Graph, Twitter Card, canonical.
 */
function meta_tags(): void
{
    $title = wp_get_document_title();
    $desc  = description();
    $url   = canonical_url();
    $img   = social_image(og_image());
    $name  = get_bloginfo('name');

    $tags = [
        ['name', 'description', $desc],
        ['property', 'og:locale', str_replace('-', '_', get_bloginfo('language'))],
        ['property', 'og:type', og_type()],
        ['property', 'og:site_name', $name],
        ['property', 'og:title', $title],
        ['property', 'og:description', $desc],
        ['property', 'og:url', $url],
        ['property', 'og:image', $img['url']],
        ['property', 'og:image:alt', $title],
        ['property', 'og:image:type', $img['type']],
        ['property', 'og:image:width', $img['width'] ?: ''],
        ['property', 'og:image:height', $img['height'] ?: ''],
        ['name', 'twitter:card', 'summary_large_image'],
        ['name', 'twitter:title', $title],
        ['name', 'twitter:description', $desc],
        ['name', 'twitter:image', $img['url']],
    ];

    echo "\n";

    // Xác minh Google Search Console (chỉ in ở trang chủ, khi đã nhập mã).
    $verify = function_exists('App\\nep') ? trim((string) \App\nep('google_verify')) : '';
    if ($verify !== '' && (is_front_page() || is_home())) {
        printf("    <meta name=\"google-site-verification\" content=\"%s\">\n", esc_attr($verify));
    }

    // Robots (noindex cho search / phân trang / bài bật noindex).
    if ($robots = robots_directive()) {
        printf("    <meta name=\"robots\" content=\"%s\">\n", esc_attr($robots));
    }

    foreach ($tags as [$attr, $key, $value]) {
        if ($value === '') {
            continue;
        }

        printf(
            "    <meta %s=\"%s\" content=\"%s\">\n",
            $attr,
            esc_attr($key),
            esc_attr((string) $value)
        );
    }

    printf("    <link rel=\"canonical\" href=\"%s\">\n", esc_url($url));
}

/**
 * Ghi đè <title> bằng "Tiêu đề SEO" nhập tay (nếu có).
 */
function filter_title(string $title): string
{
    $manual = override('seo_title');

    return $manual !== '' ? $manual : $title;
}

/**
 * In JSON-LD structured data (Organization + WebSite sitewide,
 * Article cho bài viết/dự án, Product cho sản phẩm WooCommerce).
 */
function json_ld(): void
{
    $graph = [];

    $org = [
        '@type' => 'Organization',
        '@id'   => home_url('/#organization'),
        'name'  => get_bloginfo('name'),
        'url'   => home_url('/'),
        'logo'  => get_theme_file_uri('public/images/logo-mark.svg'),
    ];
    $graph[] = $org;

    $graph[] = [
        '@type'     => 'WebSite',
        '@id'       => home_url('/#website'),
        'url'       => home_url('/'),
        'name'      => get_bloginfo('name'),
        'publisher' => ['@id' => home_url('/#organization')],
    ];

    // ---- LocalBusiness: doanh nghiệp địa phương (rất quan trọng cho SEO local).
    $nep = fn (string $k) => function_exists('App\\nep') ? (string) \App\nep($k) : '';
    $address = array_filter([
        '@type'          => 'PostalAddress',
        'streetAddress'  => $nep('address'),
        'addressCountry' => 'VN',
    ]);
    $biz = array_filter([
        '@type'     => 'LocalBusiness',
        '@id'       => home_url('/#localbusiness'),
        'name'      => get_bloginfo('name'),
        'url'       => home_url('/'),
        'image'     => image_url(),
        'telephone' => $nep('hotline'),
        'email'     => $nep('email'),
        'address'   => count($address) > 1 ? $address : null,
    ], fn ($v) => $v !== null && $v !== '');
    $graph[] = $biz;

    // ---- BreadcrumbList trên trang đơn.
    if ($crumbs = breadcrumb_items()) {
        $graph[] = [
            '@type'           => 'BreadcrumbList',
            'itemListElement' => array_map(fn ($c, $i) => [
                '@type'    => 'ListItem',
                'position' => $i + 1,
                'name'     => $c['name'],
                'item'     => $c['url'],
            ], $crumbs, array_keys($crumbs)),
        ];
    }

    if (is_singular('product') && function_exists('wc_get_product')) {
        $product = wc_get_product(get_queried_object_id());

        if ($product instanceof WC_Product) {
            $node = [
                '@type'       => 'Product',
                'name'        => $product->get_name(),
                'description' => description(),
                'url'         => get_permalink($product->get_id()),
                'image'       => image_url(),
                'sku'         => $product->get_sku() ?: null,
            ];

            if ($price = $product->get_price()) {
                $node['offers'] = [
                    '@type'         => 'Offer',
                    'price'         => $price,
                    'priceCurrency' => get_woocommerce_currency(),
                    'availability'  => $product->is_in_stock()
                        ? 'https://schema.org/InStock'
                        : 'https://schema.org/OutOfStock',
                    'url'           => get_permalink($product->get_id()),
                ];
            }

            $graph[] = array_filter($node, fn ($v) => $v !== null);
        }
    } elseif (is_singular(['post', 'du_an'])) {
        $post = get_queried_object();

        $node = [
            '@type'         => 'Article',
            'headline'      => get_the_title($post),
            'description'   => description(),
            'url'           => get_permalink($post),
            'datePublished' => get_the_date('c', $post),
            'dateModified'  => get_the_modified_date('c', $post),
            'author'        => ['@type' => 'Person', 'name' => get_the_author_meta('display_name', $post->post_author)],
            'publisher'     => ['@id' => home_url('/#organization')],
        ];

        if (has_post_thumbnail($post)) {
            $node['image'] = image_url();
        }

        $graph[] = $node;
    }

    $data = ['@context' => 'https://schema.org', '@graph' => $graph];

    printf(
        "    <script type=\"application/ld+json\">%s</script>\n",
        wp_json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
    );
}

add_action('wp_head', __NAMESPACE__ . '\\meta_tags', 5);
add_action('wp_head', __NAMESPACE__ . '\\json_ld', 6);
add_filter('pre_get_document_title', __NAMESPACE__ . '\\filter_title', 20);

// Gỡ canonical mặc định của WP core để tránh trùng với thẻ canonical ở trên.
remove_action('wp_head', 'rel_canonical');

// Bỏ sitemap tác giả (wp-sitemap-users-1.xml) khỏi sitemap core của WP.
// Trang /author/* không có giá trị SEO với site catalog và lộ slug tác giả.
add_filter('wp_sitemaps_add_provider', function ($provider, $name) {
    return $name === 'users' ? false : $provider;
}, 10, 2);
