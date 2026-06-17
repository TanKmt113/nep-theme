@php
  use function App\nep;
  // 3 cột link: lấy từ menu (Giao diện → Menu), fallback danh sách mặc định.
  $footer_cols = [
    ['title' => nep('footer_products_title', 'Sản phẩm'), 'loc' => 'footer_products', 'items' => ['Rèm vải', 'Rèm cuốn', 'Rèm Roman', 'Rèm gỗ', 'Rèm tổ ong']],
    ['title' => nep('footer_services_title', 'Dịch vụ'),  'loc' => 'footer_services', 'items' => ['Thi công rèm', 'Thêu vi tính', 'Thêu đồng phục', 'Gia công OEM']],
    ['title' => nep('footer_company_title', 'Công ty'),   'loc' => 'footer_company',  'items' => ['Về NẾP', 'Dự án', 'Blog', 'Tuyển dụng', 'Liên hệ']],
  ];
  $footer_ul = 'list-style:none;margin:0;padding:0;display:flex;flex-direction:column;gap:11px';
  $socials = [
    ['url' => nep('footer_facebook'),  'label' => 'facebook'],
    ['url' => nep('footer_instagram'), 'label' => 'instagram'],
    ['url' => nep('footer_youtube'),   'label' => 'youtube'],
  ];
@endphp

<style>.nep-footer__menu a{font-size:var(--text-sm);color:rgba(244,242,236,.72);text-decoration:none}.nep-footer__menu a:hover{color:#fff}.nep-footer__legal a{color:inherit;text-decoration:none}.nep-footer__legal a:hover{color:#fff}</style>

{{-- NẾP · Footer — dark olive, 4 columns + newsletter + contact. --}}
<footer id="footer" style="background:var(--olive-900);color:var(--text-on-dark);padding-top:var(--space-11);padding-bottom:var(--space-7)">
  <x-container>
    <div class="nep-footer__cols" style="display:grid;grid-template-columns:1.6fr 1fr 1fr 1fr;gap:var(--space-8);padding-bottom:var(--space-9)">
      <div>
        <img src="{{ nep('logo_light') ?: get_theme_file_uri('public/images/logo-light.svg') }}" alt="NẾP" loading="lazy" decoding="async" style="height:42px;margin-bottom:18px">
        <p style="font-size:var(--text-sm);line-height:1.7;color:rgba(244,242,236,.66);max-width:34ch;margin:0">
          {{ nep('footer_about', 'Xưởng thêu vi tính & rèm thiết kế.') }} {{ nep('slogan') }}.
        </p>
        <div style="display:flex;gap:10px;margin-top:22px">
          {{-- Lucide dropped brand glyphs, so these are inline SVGs (ported from Footer.jsx). --}}
          <a href="{{ nep('footer_facebook') ?: '#' }}" aria-label="facebook" style="display:inline-flex;align-items:center;justify-content:center;width:40px;height:40px;border-radius:50%;background:rgba(255,255,255,.08);color:#F4F2EC">
            <svg width="18" height="18" viewBox="0 0 24 24" aria-hidden="true"><path d="M13.5 21v-7h2.3l.4-2.9h-2.7V9.2c0-.8.3-1.4 1.5-1.4h1.4V5.2C16.6 5.1 15.8 5 14.9 5c-2 0-3.4 1.2-3.4 3.5v2.6H9v2.9h2.5V21h2z" fill="currentColor"/></svg>
          </a>
          <a href="{{ nep('footer_instagram') ?: '#' }}" aria-label="instagram" style="display:inline-flex;align-items:center;justify-content:center;width:40px;height:40px;border-radius:50%;background:rgba(255,255,255,.08);color:#F4F2EC">
            <svg width="18" height="18" viewBox="0 0 24 24" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="1.7"><rect x="3.5" y="3.5" width="17" height="17" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17" cy="7" r="0.6" fill="currentColor" stroke="none"/></svg>
          </a>
          <a href="{{ nep('footer_youtube') ?: '#' }}" aria-label="youtube" style="display:inline-flex;align-items:center;justify-content:center;width:40px;height:40px;border-radius:50%;background:rgba(255,255,255,.08);color:#F4F2EC">
            <svg width="18" height="18" viewBox="0 0 24 24" aria-hidden="true"><path d="M21.6 7.6c-.2-.9-.9-1.6-1.8-1.8C18.2 5.4 12 5.4 12 5.4s-6.2 0-7.8.4c-.9.2-1.6.9-1.8 1.8C2 9.2 2 12 2 12s0 2.8.4 4.4c.2.9.9 1.6 1.8 1.8 1.6.4 7.8.4 7.8.4s6.2 0 7.8-.4c.9-.2 1.6-.9 1.8-1.8.4-1.6.4-4.4.4-4.4s0-2.8-.4-4.4zM10 15V9l5 3-5 3z" fill="currentColor"/></svg>
          </a>
        </div>
      </div>
      @foreach($footer_cols as $c)
        <div>
          <h4 style="font-family:var(--font-sans);font-size:var(--text-sm);font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:var(--moss);margin-bottom:16px">{{ $c['title'] }}</h4>
          @if(has_nav_menu($c['loc']))
            {!! wp_nav_menu([
              'theme_location' => $c['loc'],
              'container'      => false,
              'echo'           => false,
              'depth'          => 1,
              'fallback_cb'    => false,
              'items_wrap'     => '<ul class="nep-footer__menu" style="' . $footer_ul . '">%3$s</ul>',
            ]) !!}
          @else
            <ul class="nep-footer__menu" style="{{ $footer_ul }}">
              @foreach($c['items'] as $it)
                <li><a href="#">{{ $it }}</a></li>
              @endforeach
            </ul>
          @endif
        </div>
      @endforeach
    </div>

    {{-- Newsletter + contact --}}
    <div class="nep-footer__mid" style="display:grid;grid-template-columns:1fr 1fr;gap:var(--space-8);padding:var(--space-7) 0;border-top:1px solid rgba(255,255,255,.10);border-bottom:1px solid rgba(255,255,255,.10)">
      <div>
        <h4 style="font-family:var(--font-display);font-size:var(--text-h3);color:#fff;margin-bottom:8px">{{ nep('footer_newsletter_title', 'Nhận bản tin & ưu đãi') }}</h4>
        <form id="nep-newsletter-form" style="display:flex;gap:10px;max-width:420px"
              data-action="{{ admin_url('admin-ajax.php') }}" data-nonce="{{ wp_create_nonce('nep_newsletter') }}">
          <input type="email" name="email" placeholder="Email của bạn" aria-label="Email của bạn" class="nep-input" style="flex:1" required>
          <x-button variant="gold" type="submit">Đăng ký</x-button>
        </form>
        <p id="nep-newsletter-msg" role="status" aria-live="polite" style="margin:10px 0 0;font-size:var(--text-sm);min-height:1em;display:none"></p>
      </div>
      <div style="display:flex;flex-direction:column;gap:10px">
        <div style="display:flex;align-items:center;gap:10px;font-size:var(--text-sm);color:rgba(244,242,236,.82)"><x-icon name="map-pin" :size="16" color="var(--moss)" /> {{ nep('address') }}</div>
        <div style="display:flex;align-items:center;gap:10px;font-size:var(--text-sm);color:rgba(244,242,236,.82)"><x-icon name="phone" :size="16" color="var(--moss)" /> {{ nep('hotline') }} · {{ nep('hotline_alt') }}</div>
        <div style="display:flex;align-items:center;gap:10px;font-size:var(--text-sm);color:rgba(244,242,236,.82)"><x-icon name="clock" :size="16" color="var(--moss)" /> {{ nep('hours') }}</div>
      </div>
    </div>

    <div class="nep-footer__legal" style="display:flex;justify-content:space-between;padding-top:var(--space-6);font-size:var(--text-xs);color:rgba(244,242,236,.5)">
      <span>© {{ date('Y') }} NẾP — Rèm &amp; Thêu. Bảo lưu mọi quyền.</span>
      <span style="display:flex;gap:10px">
        <a href="{{ get_permalink(get_page_by_path('chinh-sach-bao-mat')) }}">Chính sách bảo mật</a>
        <span aria-hidden="true">·</span>
        <a href="{{ get_permalink(get_page_by_path('dieu-khoan-su-dung')) }}">Điều khoản</a>
      </span>
    </div>
  </x-container>

  <script>
  (function () {
    var form = document.getElementById('nep-newsletter-form');
    if (!form) return;
    var msg = document.getElementById('nep-newsletter-msg');
    var btn = form.querySelector('button, [type="submit"]');
    function show(text, ok) {
      msg.textContent = text;
      msg.style.display = 'block';
      msg.style.color = ok ? 'var(--moss, #9caa7b)' : '#e9a8a8';
    }
    form.addEventListener('submit', function (e) {
      e.preventDefault();
      var email = (form.email.value || '').trim();
      if (!email) return;
      var data = new FormData();
      data.append('action', 'nep_newsletter');
      data.append('nonce', form.dataset.nonce);
      data.append('email', email);
      data.append('source', 'footer');
      if (btn) { btn.disabled = true; btn.style.opacity = '.6'; }
      fetch(form.dataset.action, { method: 'POST', body: data, credentials: 'same-origin' })
        .then(function (r) { return r.json(); })
        .then(function (res) {
          show((res.data && res.data.message) || (res.success ? 'Đăng ký thành công!' : 'Có lỗi xảy ra.'), !!res.success);
          if (res.success) form.reset();
        })
        .catch(function () { show('Không kết nối được máy chủ, vui lòng thử lại.', false); })
        .finally(function () { if (btn) { btn.disabled = false; btn.style.opacity = ''; } });
    });
  })();
  </script>
</footer>
