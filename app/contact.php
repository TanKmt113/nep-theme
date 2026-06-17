<?php

/**
 * Contact form handler.
 * The form on the Liên hệ page posts here (admin-post.php?action=nep_contact).
 * Validates a nonce, emails the site admin, then redirects back with ?sent=1.
 *
 * For richer needs (spam protection, storage, autoresponders) swap this for a
 * plugin like Contact Form 7 / WPForms and replace the <form> in the template.
 */

namespace App;

use function add_action;

add_action('admin_post_nopriv_nep_contact', __NAMESPACE__ . '\\handle_contact');
add_action('admin_post_nep_contact', __NAMESPACE__ . '\\handle_contact');

function handle_contact(): void
{
    $redirect = isset($_POST['redirect']) ? esc_url_raw($_POST['redirect']) : home_url('/');

    // Verify nonce.
    if (! isset($_POST['nep_contact_nonce']) || ! wp_verify_nonce($_POST['nep_contact_nonce'], 'nep_contact')) {
        wp_safe_redirect(add_query_arg('sent', '0', $redirect));
        exit;
    }

    $name    = sanitize_text_field($_POST['ho_ten'] ?? '');
    $phone   = sanitize_text_field($_POST['sdt'] ?? '');
    $email   = sanitize_email($_POST['email'] ?? '');
    $subject = sanitize_text_field($_POST['chu_de'] ?? '');
    $product = sanitize_text_field($_POST['san_pham'] ?? '');
    $message = sanitize_textarea_field($_POST['noi_dung'] ?? '');

    if (! $name || ! $phone) {
        wp_safe_redirect(add_query_arg('sent', '0', $redirect));
        exit;
    }

    $body = "Yêu cầu tư vấn mới từ website NẾP:\n\n"
        . "Họ tên: {$name}\n"
        . "Điện thoại: {$phone}\n"
        . "Email: {$email}\n"
        . "Chủ đề: {$subject}\n"
        . ($product ? "Sản phẩm quan tâm: {$product}\n" : '')
        . "\nNội dung:\n{$message}\n";

    $headers = $email ? ['Reply-To: ' . $email] : [];

    wp_mail(
        get_option('admin_email'),
        '[NẾP] Yêu cầu tư vấn: ' . ($subject ?: 'Liên hệ'),
        $body,
        $headers
    );

    wp_safe_redirect(add_query_arg('sent', '1', $redirect));
    exit;
}
