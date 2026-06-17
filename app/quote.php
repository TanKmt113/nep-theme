<?php

/**
 * YÊU CẦU BÁO GIÁ (popup) — AJAX + lưu lead vào admin.
 *
 * Nút "Yêu cầu báo giá" trên trang sản phẩm mở popup; form gửi AJAX tới action
 * `nep_quote`. Mỗi yêu cầu vừa được EMAIL cho admin, vừa được LƯU lại dưới dạng
 * CPT `nep_lead` để không mất khách kể cả khi email/SMTP lỗi.
 */

namespace App;

// CPT lưu lead (riêng tư, chỉ hiện trong admin).
add_action('init', function () {
    register_post_type('nep_lead', [
        'labels' => [
            'name'          => 'Yêu cầu báo giá',
            'singular_name' => 'Yêu cầu báo giá',
            'menu_name'     => 'Yêu cầu báo giá',
        ],
        'public'              => false,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'menu_icon'           => 'dashicons-money-alt',
        'menu_position'       => 26,
        'capability_type'     => 'post',
        'capabilities'        => ['create_posts' => 'do_not_allow'], // chỉ tạo từ front-end
        'map_meta_cap'        => true,
        'supports'            => ['title'],
        'has_archive'         => false,
        'rewrite'             => false,
        'exclude_from_search' => true,
    ]);
});

add_action('wp_ajax_nopriv_nep_quote', __NAMESPACE__ . '\\handle_quote');
add_action('wp_ajax_nep_quote', __NAMESPACE__ . '\\handle_quote');

function handle_quote(): void
{
    if (! check_ajax_referer('nep_quote', 'nonce', false)) {
        wp_send_json_error(['message' => 'Phiên đã hết hạn, vui lòng tải lại trang và thử lại.'], 400);
    }

    $name    = sanitize_text_field($_POST['ho_ten'] ?? '');
    $phone   = sanitize_text_field($_POST['sdt'] ?? '');
    $email   = sanitize_email($_POST['email'] ?? '');
    $product = sanitize_text_field($_POST['san_pham'] ?? '');
    $pid     = absint($_POST['product_id'] ?? 0);
    $message = sanitize_textarea_field($_POST['noi_dung'] ?? '');

    if (! $name || ! $phone) {
        wp_send_json_error(['message' => 'Vui lòng nhập Họ tên và Số điện thoại.'], 422);
    }

    // 1) Lưu lead vào admin (luôn chạy, không phụ thuộc email).
    $lead_id = wp_insert_post([
        'post_type'   => 'nep_lead',
        'post_status' => 'publish',
        'post_title'  => sprintf('%s — %s (%s)', $name, $product ?: 'Sản phẩm', $phone),
    ], true);
    if (! is_wp_error($lead_id)) {
        update_post_meta($lead_id, 'ho_ten', $name);
        update_post_meta($lead_id, 'sdt', $phone);
        update_post_meta($lead_id, 'email', $email);
        update_post_meta($lead_id, 'san_pham', $product);
        update_post_meta($lead_id, 'product_id', $pid);
        update_post_meta($lead_id, 'noi_dung', $message);
    }

    // 2) Email cho admin.
    $body = "Yêu cầu báo giá mới từ website NẾP:\n\n"
        . 'Sản phẩm: ' . ($product ?: '—') . ($pid ? " (#{$pid})" : '') . "\n"
        . "Họ tên: {$name}\n"
        . "Điện thoại: {$phone}\n"
        . 'Email: ' . ($email ?: '—') . "\n"
        . ($message ? "\nNội dung:\n{$message}\n" : '');
    $headers = $email ? ['Reply-To: ' . $email] : [];
    wp_mail(get_option('admin_email'), '[NẾP] Yêu cầu báo giá: ' . ($product ?: 'Sản phẩm'), $body, $headers);

    wp_send_json_success(['message' => 'Cảm ơn bạn! NẾP sẽ liên hệ báo giá trong thời gian sớm nhất.']);
}

// Cột hiển thị nhanh trong danh sách lead.
add_filter('manage_nep_lead_posts_columns', function ($cols) {
    return [
        'cb'       => $cols['cb'] ?? '',
        'title'    => 'Khách / SP',
        'sdt'      => 'Điện thoại',
        'san_pham' => 'Sản phẩm',
        'noidung'  => 'Nội dung',
        'date'     => 'Ngày',
    ];
});
add_action('manage_nep_lead_posts_custom_column', function ($col, $post_id) {
    if ($col === 'sdt')      { echo esc_html(get_post_meta($post_id, 'sdt', true)); }
    if ($col === 'san_pham') { echo esc_html(get_post_meta($post_id, 'san_pham', true)); }
    if ($col === 'noidung')  { echo esc_html(wp_trim_words((string) get_post_meta($post_id, 'noi_dung', true), 14)); }
}, 10, 2);
