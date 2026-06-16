<?php

/**
 * Đồng bộ DỮ LIỆU MẪU vào các ô ACF của tất cả các trang.
 *
 * Template hiển thị có fallback mặc định nên front-end luôn có nội dung; nhưng
 * trong trang admin các ô ACF (nhất là repeater) lại trống. File này ghi đúng
 * bộ dữ liệu mẫu đó VÀO các ô ACF để biên tập viên thấy & sửa trực tiếp.
 *
 * Ảnh demo (Unsplash) được tải vào Thư viện và gán bằng attachment ID.
 *
 * Chạy:  wp nep sync        hoặc   Công cụ → "NẾP · Dữ liệu mẫu" → nút Đồng bộ.
 */

namespace App;

require_once __DIR__ . '/nep-data.php';

use function add_action;

/** Sideload URL ảnh → attachment ID (cache theo URL, an toàn khi chạy lại). */
function nep_sideload_img(string $url, int $parent = 0): int
{
    static $cache = [];
    if ($url === '') {
        return 0;
    }
    if (isset($cache[$url])) {
        return $cache[$url];
    }
    require_once ABSPATH . 'wp-admin/includes/media.php';
    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/image.php';

    $tmp = download_url($url, 30);
    if (is_wp_error($tmp)) {
        return $cache[$url] = 0;
    }
    $file = ['name' => 'nep-' . substr(md5($url), 0, 12) . '.jpg', 'tmp_name' => $tmp];
    $id = media_handle_sideload($file, $parent);
    if (is_wp_error($id)) {
        @unlink($tmp);
        return $cache[$url] = 0;
    }
    return $cache[$url] = (int) $id;
}

/** Lấy ID trang theo slug. */
function nep_page_id(string $slug): int
{
    $p = get_posts(['post_type' => 'page', 'name' => $slug, 'numberposts' => 1, 'post_status' => 'any']);
    return $p ? (int) $p[0]->ID : 0;
}

/**
 * Ghi toàn bộ dữ liệu mẫu vào ACF.
 *
 * @param  callable(string):void  $log
 * @throws \RuntimeException nếu chưa cài ACF.
 */
function nep_sync_content(callable $log): void
{
    if (! function_exists('update_field')) {
        throw new \RuntimeException('Cần kích hoạt ACF để đồng bộ dữ liệu mẫu.');
    }

    // Ghi 1 field ảnh: chỉ set khi tải được (id>0), tránh ghi đè bằng 0.
    $setImg = function (string $field, string $url, int $pid) {
        $id = nep_sideload_img($url, $pid);
        if ($id) {
            update_field($field, $id, $pid);
        }
        return $id;
    };

    // ===== TRANG CHỦ =====
    $home = (int) get_option('page_on_front');
    if ($home) {
        update_field('hero_eyebrow', 'Rèm cửa cao cấp · Từ 2014', $home);
        update_field('hero_title', 'Kiến tạo không gian sống', $home);
        update_field('hero_title_accent', 'đẳng cấp', $home);
        update_field('hero_desc', 'Thiết kế, thi công và lắp đặt rèm cửa cao cấp cùng xưởng thêu vi tính — chăm chút trong từng đường nét.', $home);
        $setImg('hero_image', 'https://images.unsplash.com/photo-1616594039964-ae9021a400a0?w=1800&q=80', $home);
        update_field('hero_btn1_text', 'Xem bộ sưu tập', $home);
        update_field('hero_btn1_url', nep_shop_url(), $home);
        update_field('hero_btn2_text', 'Nhận báo giá', $home);
        update_field('hero_btn2_url', home_url('/lien-he'), $home);
        update_field('stats', [
            ['value' => '10+', 'label' => 'Năm kinh nghiệm'],
            ['value' => '5000+', 'label' => 'Khách hàng'],
            ['value' => '100+', 'label' => 'Mẫu rèm'],
        ], $home);
        update_field('intro_eyebrow', 'Về NẾP', $home);
        update_field('intro_heading', 'Nghề rèm, thêu — chăm chút từng nếp gấp', $home);
        update_field('intro_text', 'Suốt một thập kỷ, NẾP đồng hành cùng hàng nghìn gia đình và doanh nghiệp Việt — mang đến những bộ rèm và sản phẩm thêu bền đẹp, tinh tế và đậm dấu ấn riêng.', $home);
        $setImg('intro_image', 'https://images.unsplash.com/photo-1524758631624-e2822e304c36?w=1100&q=80', $home);
        update_field('intro_badge_value', '4.9/5', $home);
        update_field('intro_badge_label', '1.200+ đánh giá', $home);
        update_field('cat_eyebrow', 'Danh mục', $home);
        update_field('cat_heading', 'Bộ sưu tập rèm cửa', $home);
        update_field('featured_eyebrow', 'Nổi bật', $home);
        update_field('featured_heading', 'Được yêu thích nhất', $home);
        update_field('process_eyebrow', 'Quy trình', $home);
        update_field('process_heading', '6 bước, trọn vẹn an tâm', $home);
        update_field('projects_eyebrow', 'Dự án', $home);
        update_field('projects_heading', 'Không gian đã hoàn thiện', $home);
        $log('Trang chủ: xong.');
    }

    // ===== GIỚI THIỆU =====
    if ($id = nep_page_id('gioi-thieu')) {
        update_field('about_hero_eyebrow', 'Về chúng tôi', $id);
        update_field('about_hero_title', 'Nghề rèm, thêu —', $id);
        update_field('about_hero_accent', 'chăm chút từng nếp gấp', $id);
        update_field('about_lead', 'NẾP ra đời năm 2014 từ một xưởng may rèm nhỏ ở Sài Gòn, với niềm tin rằng một tấm rèm đẹp có thể thay đổi cả cảm xúc của một căn nhà.', $id);
        $setImg('about_hero_image', 'https://images.unsplash.com/photo-1524758631624-e2822e304c36?w=1800&q=80', $id);
        update_field('about_story_eyebrow', 'Câu chuyện NẾP', $id);
        update_field('about_story_heading', 'Từ một xưởng nhỏ đến thương hiệu được tin yêu', $id);
        update_field('about_story', [
            ['text' => 'Khởi đầu là một xưởng gia đình, chúng tôi nhận từng đơn đo may rèm cho hàng xóm, người quen. Mỗi tấm rèm được cắt, may và treo bằng tay — và chính sự tỉ mỉ ấy đã giữ chân khách hàng suốt một thập kỷ.'],
            ['text' => 'Hôm nay, NẾP là sự kết hợp giữa nghề thủ công và công nghệ: showroom rèm cửa cao cấp song hành cùng xưởng thêu vi tính đa kim, phục vụ cả gia đình lẫn doanh nghiệp trên toàn quốc.'],
        ], $id);
        $setImg('about_story_img1', 'https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?w=700&q=80', $id);
        $setImg('about_story_img2', 'https://images.unsplash.com/photo-1452860606245-08befc0ff44b?w=700&q=80', $id);
        update_field('about_stats', [
            ['value' => '10+', 'label' => 'Năm kinh nghiệm'],
            ['value' => '5000+', 'label' => 'Khách hàng'],
            ['value' => '100+', 'label' => 'Dự án doanh nghiệp'],
            ['value' => '40+', 'label' => 'Thành viên đội ngũ'],
        ], $id);
        update_field('about_values_eyebrow', 'Giá trị cốt lõi', $id);
        update_field('about_values_heading', 'Điều chúng tôi luôn giữ', $id);
        update_field('about_values', [
            ['icon' => 'gem', 'title' => 'Chất lượng cao cấp', 'desc' => 'Chỉ chọn chất liệu nhập khẩu, an toàn và bền màu theo thời gian.'],
            ['icon' => 'heart-handshake', 'title' => 'Tận tâm', 'desc' => 'Đồng hành từ khảo sát đến bảo hành, lắng nghe từng nhu cầu nhỏ.'],
            ['icon' => 'ruler', 'title' => 'Tỉ mỉ', 'desc' => 'Đo may theo kích thước thực tế, chăm chút từng đường chỉ nếp gấp.'],
            ['icon' => 'leaf', 'title' => 'Bền vững', 'desc' => 'Ưu tiên vật liệu tự nhiên và quy trình sản xuất ít lãng phí.'],
        ], $id);
        update_field('about_timeline_eyebrow', 'Hành trình', $id);
        update_field('about_timeline_heading', 'Những cột mốc đáng nhớ', $id);
        update_field('about_milestones', [
            ['year' => '2014', 'text' => 'Thành lập xưởng may rèm đầu tiên tại Quận Phú Nhuận.'],
            ['year' => '2017', 'text' => 'Khai trương showroom rèm cửa cao cấp 200m².'],
            ['year' => '2020', 'text' => 'Đầu tư hệ thống máy thêu vi tính, mở rộng sang mảng thêu.'],
            ['year' => '2023', 'text' => 'Cán mốc 5.000 khách hàng và 100+ dự án doanh nghiệp.'],
            ['year' => '2026', 'text' => 'Phủ sóng thi công toàn quốc với đội ngũ hơn 40 người.'],
        ], $id);
        update_field('about_team_eyebrow', 'Đội ngũ', $id);
        update_field('about_team_heading', 'Những con người làm nên NẾP', $id);
        update_field('about_team', [
            ['name' => 'Nguyễn Thành Nam', 'role' => 'Nhà sáng lập', 'image' => nep_sideload_img('https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=500&q=80', $id)],
            ['name' => 'Trần Khánh Vy', 'role' => 'Giám đốc thiết kế', 'image' => nep_sideload_img('https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=500&q=80', $id)],
            ['name' => 'Lê Minh Quân', 'role' => 'Trưởng xưởng thêu', 'image' => nep_sideload_img('https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=500&q=80', $id)],
            ['name' => 'Phạm Thu Hà', 'role' => 'Tư vấn nội thất', 'image' => nep_sideload_img('https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=500&q=80', $id)],
        ], $id);
        $log('Giới thiệu: xong.');
    }

    // ===== XƯỞNG THÊU =====
    if ($id = nep_page_id('xuong-theu')) {
        update_field('emb_hero_eyebrow', 'Xưởng thêu vi tính', $id);
        update_field('emb_hero_title', 'Logo, đồng phục & quà tặng', $id);
        update_field('emb_hero_accent', 'thêu sắc nét', $id);
        update_field('emb_hero_desc', 'Hệ thống máy thêu vi tính đa kim hiện đại — nhận gia công số lượng lớn cho doanh nghiệp, trường học và sự kiện.', $id);
        $setImg('emb_hero_image', 'https://images.unsplash.com/photo-1452860606245-08befc0ff44b?w=1400&q=80', $id);
        update_field('emb_btn1_text', 'Xem máy thêu hoạt động', $id);
        update_field('emb_btn1_url', home_url('/lien-he'), $id);
        update_field('emb_btn2_text', 'Báo giá gia công', $id);
        update_field('emb_btn2_url', home_url('/lien-he'), $id);
        update_field('emb_stats', [
            ['value' => '20+', 'label' => 'Máy thêu vi tính'],
            ['value' => '12 đầu', 'label' => 'Thêu đa kim cùng lúc'],
            ['value' => '50.000', 'label' => 'Sản phẩm / tháng'],
            ['value' => '48h', 'label' => 'Giao hàng nhanh'],
        ], $id);
        update_field('emb_cap_eyebrow', 'Năng lực sản xuất', $id);
        update_field('emb_cap_heading', 'Máy móc hiện đại, sản lượng lớn', $id);
        update_field('emb_cap_text', 'Hệ thống hơn 20 máy thêu vi tính đa kim cho phép chúng tôi xử lý đơn hàng lớn với chất lượng đồng đều và thời gian giao hàng nhanh.', $id);
        $setImg('emb_cap_img1', 'https://images.unsplash.com/photo-1620799139507-2a76f79a2f4d?w=700&q=80', $id);
        $setImg('emb_cap_img2', 'https://images.unsplash.com/photo-1574180566232-aaad1b5b8450?w=700&q=80', $id);
        update_field('emb_caps', [
            ['text' => 'Số hoá file thêu miễn phí từ logo của bạn'],
            ['text' => 'Thêu trên mọi chất liệu: cotton, kaki, dạ, nỉ'],
            ['text' => 'Cam kết màu chỉ chuẩn, đường thêu không bung'],
        ], $id);
        update_field('emb_svc_eyebrow', 'Dịch vụ thêu', $id);
        update_field('emb_svc_heading', 'Chúng tôi nhận thêu gì?', $id);
        update_field('emb_services', [
            ['title' => 'Thêu logo', 'desc' => 'Logo doanh nghiệp sắc nét trên mọi chất liệu.', 'icon' => 'scan-line', 'image' => nep_sideload_img('https://images.unsplash.com/photo-1620799139507-2a76f79a2f4d?w=800&q=80', $id)],
            ['title' => 'Thêu đồng phục', 'desc' => 'Đồng phục công ty, trường học số lượng lớn.', 'icon' => 'shirt', 'image' => nep_sideload_img('https://images.unsplash.com/photo-1574180566232-aaad1b5b8450?w=800&q=80', $id)],
            ['title' => 'Thêu áo polo', 'desc' => 'Áo polo quà tặng, sự kiện, team building.', 'icon' => 'shirt', 'image' => nep_sideload_img('https://images.unsplash.com/photo-1576566588028-4147f3842f27?w=800&q=80', $id)],
            ['title' => 'Thêu mũ', 'desc' => 'Mũ lưỡi trai, nón kết thêu nổi bền đẹp.', 'icon' => 'hard-hat', 'image' => nep_sideload_img('https://images.unsplash.com/photo-1521577352947-9bb58764b69a?w=800&q=80', $id)],
            ['title' => 'Thêu khăn', 'desc' => 'Khăn tắm, khăn mặt khách sạn cao cấp.', 'icon' => 'layers', 'image' => nep_sideload_img('https://images.unsplash.com/photo-1558769132-cb1aea458c5e?w=800&q=80', $id)],
            ['title' => 'Gia công OEM', 'desc' => 'Nhận gia công thêu số lượng lớn theo yêu cầu.', 'icon' => 'factory', 'image' => nep_sideload_img('https://images.unsplash.com/photo-1489987707025-afc232f7ea0f?w=800&q=80', $id)],
        ], $id);
        update_field('emb_cta_heading', 'Gửi logo — nhận mẫu thêu trong 24 giờ', $id);
        update_field('emb_cta_text', 'Đội ngũ tư vấn sẽ báo giá và gửi mẫu số hoá miễn phí cho đơn hàng của bạn.', $id);
        update_field('emb_cta_btn1_text', 'Gửi logo báo giá', $id);
        update_field('emb_cta_btn1_url', home_url('/lien-he'), $id);
        update_field('emb_cta_btn2_text', 'Xem sản phẩm rèm', $id);
        update_field('emb_cta_btn2_url', nep_shop_url(), $id);
        $log('Xưởng thêu: xong.');
    }

    // ===== LIÊN HỆ =====
    if ($id = nep_page_id('lien-he')) {
        update_field('contact_eyebrow', 'Liên hệ', $id);
        update_field('contact_heading', 'Cùng kiến tạo không gian của bạn', $id);
        update_field('contact_desc', 'Để lại thông tin, đội ngũ NẾP sẽ liên hệ tư vấn và đặt lịch khảo sát miễn phí trong vòng 24 giờ.', $id);
        update_field('contact_form_heading', 'Gửi yêu cầu tư vấn', $id);
        update_field('contact_subjects', [
            ['label' => 'Tư vấn rèm cửa'],
            ['label' => 'Báo giá thêu vi tính'],
            ['label' => 'Đặt lịch khảo sát'],
            ['label' => 'Hợp tác / OEM'],
            ['label' => 'Khác'],
        ], $id);
        update_field('contact_info_eyebrow', 'Showroom', $id);
        update_field('contact_info_heading', 'Ghé thăm chúng tôi', $id);
        update_field('contact_branch_city', 'Thái Nguyên', $id);
        update_field('contact_branch_badge', 'Trụ sở', $id);
        update_field('contact_map_embed', 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d232.19111362323318!2d105.87063940464353!3d21.387627964750642!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31351ff4e328658b%3A0x528ff5d209912cc7!2zUsOobSBNaW5oIEjDoA!5e0!3m2!1svi!2s!4v1781590512909!5m2!1svi!2s', $id);
        $log('Liên hệ: xong.');
    }

    // ===== BỘ SƯU TẬP =====
    if ($id = nep_page_id('bo-suu-tap')) {
        update_field('look_hero_eyebrow', 'Catalogue 2026', $id);
        update_field('look_hero_title', 'Bộ sưu tập', $id);
        update_field('look_hero_accent', 'rèm cửa', $id);
        update_field('look_hero_desc', 'Từ linen mộc mạc đến nhung sang trọng — khám phá những bộ sưu tập được tuyển chọn cho mọi phong cách không gian.', $id);
        $setImg('look_hero_image', 'https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?w=1800&q=80', $id);
        update_field('look_eyebrow', 'Lookbook', $id);
        update_field('look_heading', 'Bộ sưu tập nổi bật', $id);
        update_field('look_large_name', 'Bộ sưu tập Linen', $id);
        update_field('look_large_tagline', 'Mộc mạc & tự nhiên', $id);
        update_field('look_large_count', '24', $id);
        $setImg('look_large_img', 'https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?w=1100&q=80', $id);
        update_field('look_smalls', [
            ['name' => 'Velvet Noir', 'tagline' => 'Nhung sang trọng', 'count' => '12', 'image' => nep_sideload_img('https://images.unsplash.com/photo-1604014237800-1c9102c219da?w=900&q=80', $id)],
            ['name' => 'Nan gỗ tự nhiên', 'tagline' => 'Ấm áp & bền bỉ', 'count' => '18', 'image' => nep_sideload_img('https://images.unsplash.com/photo-1540574163026-643ea20ade25?w=900&q=80', $id)],
        ], $id);
        update_field('look_cat_eyebrow', 'Danh mục', $id);
        update_field('look_cat_heading', 'Tất cả loại rèm', $id);
        update_field('look_feat_eyebrow', 'Tuyển chọn', $id);
        update_field('look_feat_heading', 'Sản phẩm tiêu biểu', $id);
        $log('Bộ sưu tập: xong.');
    }

    // ===== TUỲ CHỌN CHUNG (Cài đặt NẾP) =====
    update_field('cta_heading', 'Nâng tầm không gian sống của bạn ngay hôm nay', 'option');
    $cta_img = nep_sideload_img('https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?w=1800&q=80');
    if ($cta_img) {
        update_field('cta_image', $cta_img, 'option');
    }
    update_field('cta_btn1_text', 'Gọi ngay', 'option');
    update_field('cta_btn2_text', 'Đăng ký tư vấn', 'option');
    update_field('cta_btn2_url', home_url('/lien-he'), 'option');
    update_field('footer_about', 'Xưởng thêu vi tính & rèm thiết kế.', 'option');
    update_field('footer_products_title', 'Sản phẩm', 'option');
    update_field('footer_services_title', 'Dịch vụ', 'option');
    update_field('footer_company_title', 'Công ty', 'option');
    update_field('footer_newsletter_title', 'Nhận bản tin & ưu đãi', 'option');
    update_field('footer_legal', 'Chính sách bảo mật · Điều khoản', 'option');

    // Quy trình + Lý do chọn (dùng ở trang chủ).
    $data = nep_seed_data();
    update_field('process', $data['process'], 'option');
    update_field('features', $data['features'], 'option');
    $log('Cài đặt NẾP (CTA, footer, quy trình, lý do): xong.');
}

/* ====================================================================== *
 *  WP-CLI  —  wp nep sync
 * ====================================================================== */
add_action('cli_init', function () {
    if (! class_exists('WP_CLI')) {
        return;
    }
    \WP_CLI::add_command('nep sync', function () {
        try {
            nep_sync_content(fn ($line) => \WP_CLI::log($line));
        } catch (\RuntimeException $e) {
            \WP_CLI::error($e->getMessage());
        }
        \WP_CLI::success('Đã đồng bộ dữ liệu mẫu vào ACF.');
    });
});
