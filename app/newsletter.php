<?php

/**
 * Newsletter ("Nhận bản tin & ưu đãi") handler.
 *
 * Form ở footer (resources/views/sections/footer.blade.php) gửi email qua AJAX
 * tới admin-ajax.php?action=nep_newsletter. Luồng xử lý:
 *   1. Validate nonce + email.
 *   2. Lưu subscriber vào CPT `nep_subscriber` (chống trùng email).
 *   3. Gửi mail thông báo cho admin.
 *   4. Gửi mail HTML xác nhận cho khách (autoresponder).
 *   5. Bắn action `nep_newsletter_subscribed` để đẩy sang dịch vụ ngoài
 *      (Mailchimp tích hợp sẵn nếu khai báo NEP_MAILCHIMP_API_KEY + LIST_ID).
 *
 * Danh sách email xem tại: WP Admin → Bản tin.
 */

namespace App;

use function add_action;

/* ---------------------------------------------------------------------------
 | 1. CPT lưu subscriber (ẩn, chỉ xem trong admin)
 * ------------------------------------------------------------------------- */
add_action('init', function () {
    register_post_type('nep_subscriber', [
        'labels' => [
            'name'          => 'Bản tin',
            'singular_name' => 'Người đăng ký',
            'menu_name'     => 'Bản tin',
            'all_items'     => 'Danh sách đăng ký',
        ],
        'public'             => false,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'menu_icon'          => 'dashicons-email-alt',
        'capability_type'    => 'post',
        'capabilities'       => ['create_posts' => 'do_not_allow'], // chỉ đăng ký từ frontend
        'map_meta_cap'       => true,
        'supports'           => ['title'],
        'has_archive'        => false,
        'exclude_from_search' => true,
        'publicly_queryable' => false,
    ]);
});

/* ---------------------------------------------------------------------------
 | 2. AJAX endpoint
 * ------------------------------------------------------------------------- */
add_action('wp_ajax_nep_newsletter', __NAMESPACE__ . '\\handle_newsletter');
add_action('wp_ajax_nopriv_nep_newsletter', __NAMESPACE__ . '\\handle_newsletter');

function handle_newsletter(): void
{
    // Nonce.
    if (! isset($_POST['nonce']) || ! wp_verify_nonce($_POST['nonce'], 'nep_newsletter')) {
        wp_send_json_error(['message' => 'Phiên không hợp lệ, vui lòng tải lại trang.'], 400);
    }

    $email = sanitize_email($_POST['email'] ?? '');
    if (! $email || ! is_email($email)) {
        wp_send_json_error(['message' => 'Email không hợp lệ.'], 422);
    }

    // Chống trùng: đã đăng ký rồi thì báo nhẹ nhàng, không gửi lại mail.
    $existing = get_posts([
        'post_type'      => 'nep_subscriber',
        'title'          => $email,
        'post_status'    => 'publish',
        'posts_per_page' => 1,
        'fields'         => 'ids',
        'no_found_rows'  => true,
    ]);
    if ($existing) {
        wp_send_json_success(['message' => 'Bạn đã đăng ký nhận bản tin rồi. Cảm ơn bạn!']);
    }

    // 2. Lưu DB.
    $post_id = wp_insert_post([
        'post_type'   => 'nep_subscriber',
        'post_title'  => $email,
        'post_status' => 'publish',
    ], true);

    if (is_wp_error($post_id)) {
        wp_send_json_error(['message' => 'Có lỗi xảy ra, vui lòng thử lại sau.'], 500);
    }

    update_post_meta($post_id, 'nep_source', sanitize_text_field($_POST['source'] ?? 'footer'));
    update_post_meta($post_id, 'nep_ip', sanitize_text_field($_SERVER['REMOTE_ADDR'] ?? ''));

    // 3. Báo admin.
    notify_admin_newsletter($email);

    // 4. Mail xác nhận cho khách.
    send_newsletter_welcome($email);

    // 5. Đẩy sang dịch vụ ngoài (Mailchimp built-in + hook tuỳ biến).
    do_action('nep_newsletter_subscribed', $email, $post_id);

    wp_send_json_success(['message' => 'Đăng ký thành công! Cảm ơn bạn đã quan tâm tới NẾP.']);
}

/* ---------------------------------------------------------------------------
 | 3. Mail báo admin
 * ------------------------------------------------------------------------- */
function notify_admin_newsletter(string $email): void
{
    $body = "Có người vừa đăng ký nhận bản tin trên website NẾP:\n\n"
        . "Email: {$email}\n"
        . 'Thời gian: ' . current_time('d/m/Y H:i') . "\n";

    wp_mail(
        get_option('admin_email'),
        '[NẾP] Đăng ký bản tin mới: ' . $email,
        $body,
        ['Reply-To: ' . $email]
    );
}

/* ---------------------------------------------------------------------------
 | 4. Mail HTML xác nhận cho khách
 * ------------------------------------------------------------------------- */
function send_newsletter_welcome(string $email): void
{
    $site   = get_bloginfo('name') ?: 'NẾP';
    $home   = home_url('/');
    $logo   = nep('logo_light') ?: nep('logo');
    $hotline = nep('hotline');
    $address = nep('address');

    $subject = "Cảm ơn bạn đã đăng ký nhận bản tin từ {$site}";

    ob_start(); ?>
<!DOCTYPE html>
<html lang="vi">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"></head>
<body style="margin:0;padding:0;background:#F4F2EC;font-family:Arial,Helvetica,sans-serif;color:#2b2b2b">
  <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#F4F2EC;padding:32px 0">
    <tr><td align="center">
      <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;background:#ffffff;border-radius:12px;overflow:hidden">
        <tr><td style="background:#2e3422;padding:32px 40px;text-align:center">
          <?php if ($logo): ?>
            <img src="<?php echo esc_url($logo); ?>" alt="<?php echo esc_attr($site); ?>" style="height:40px">
          <?php else: ?>
            <span style="color:#fff;font-size:24px;font-weight:700;letter-spacing:.04em"><?php echo esc_html($site); ?></span>
          <?php endif; ?>
        </td></tr>
        <tr><td style="padding:40px">
          <h1 style="margin:0 0 16px;font-size:22px;color:#2e3422">Cảm ơn bạn đã đăng ký! 🌿</h1>
          <p style="margin:0 0 16px;font-size:15px;line-height:1.7;color:#4a4a4a">
            Bạn đã đăng ký nhận bản tin &amp; ưu đãi từ <strong><?php echo esc_html($site); ?></strong>.
            Từ giờ bạn sẽ là người đầu tiên nhận thông tin về bộ sưu tập rèm mới, mẫu thêu độc quyền
            và các chương trình ưu đãi của chúng tôi.
          </p>
          <p style="margin:0 0 28px;font-size:15px;line-height:1.7;color:#4a4a4a">
            Trong lúc chờ đợi, mời bạn ghé thăm xưởng rèm &amp; thêu của NẾP.
          </p>
          <p style="margin:0 0 32px">
            <a href="<?php echo esc_url($home); ?>" style="display:inline-block;background:#c9a24b;color:#2b2b2b;text-decoration:none;font-weight:700;padding:13px 28px;border-radius:8px;font-size:15px">Khám phá NẾP</a>
          </p>
          <hr style="border:none;border-top:1px solid #ececec;margin:0 0 20px">
          <?php if ($hotline): ?><p style="margin:0 0 6px;font-size:13px;color:#888">Hotline: <?php echo esc_html($hotline); ?></p><?php endif; ?>
          <?php if ($address): ?><p style="margin:0;font-size:13px;color:#888"><?php echo esc_html($address); ?></p><?php endif; ?>
        </td></tr>
        <tr><td style="background:#f7f6f1;padding:20px 40px;text-align:center;font-size:12px;color:#999">
          © <?php echo esc_html(date('Y') . ' ' . $site); ?> — Rèm &amp; Thêu.<br>
          Bạn nhận được email này vì đã đăng ký tại <?php echo esc_html(parse_url($home, PHP_URL_HOST)); ?>.
        </td></tr>
      </table>
    </td></tr>
  </table>
</body>
</html>
    <?php
    $html = ob_get_clean();

    wp_mail($email, $subject, $html, ['Content-Type: text/html; charset=UTF-8']);
}

/* ---------------------------------------------------------------------------
 | 5. Mailchimp (tích hợp sẵn — bật khi có constant trong wp-config.php)
 |
 |   define('NEP_MAILCHIMP_API_KEY', 'xxxxxxxx-us21');
 |   define('NEP_MAILCHIMP_LIST_ID', 'abc123');
 |
 | Muốn dùng dịch vụ khác (MailerLite, Brevo…) chỉ cần hook vào
 | action 'nep_newsletter_subscribed' ở plugin/file riêng.
 * ------------------------------------------------------------------------- */
add_action('nep_newsletter_subscribed', __NAMESPACE__ . '\\push_to_mailchimp', 10, 1);

function push_to_mailchimp(string $email): void
{
    if (! defined('NEP_MAILCHIMP_API_KEY') || ! defined('NEP_MAILCHIMP_LIST_ID')) {
        return; // chưa cấu hình → bỏ qua, các bước khác vẫn chạy.
    }

    $api_key = NEP_MAILCHIMP_API_KEY;
    $list_id = NEP_MAILCHIMP_LIST_ID;
    $dc      = substr(strrchr($api_key, '-'), 1); // datacenter, vd "us21"
    if (! $dc) {
        return;
    }

    $url = "https://{$dc}.api.mailchimp.com/3.0/lists/{$list_id}/members/" . md5(strtolower($email));

    wp_remote_request($url, [
        'method'  => 'PUT', // upsert
        'timeout' => 15,
        'headers' => [
            'Authorization' => 'apikey ' . $api_key,
            'Content-Type'  => 'application/json',
        ],
        'body' => wp_json_encode([
            'email_address' => $email,
            'status_if_new' => 'subscribed',
            'status'        => 'subscribed',
        ]),
    ]);
}
