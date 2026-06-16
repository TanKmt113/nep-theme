{{--
  Template Name: Liên hệ (Contact)
--}}
@extends('layouts.app')

@php
  use function App\nep;
  $sent = isset($_GET['sent']) && $_GET['sent'] === '1';
  $subjects = ['Tư vấn rèm cửa', 'Báo giá thêu vi tính', 'Đặt lịch khảo sát', 'Hợp tác / OEM', 'Khác'];

  // Prefill khi tới từ nút "Yêu cầu báo giá" của một sản phẩm (?sp=<id>).
  $sp_id = isset($_GET['sp']) ? (int) $_GET['sp'] : 0;
  $sp_title = $sp_id ? get_the_title($sp_id) : '';
  $prefill_msg = $sp_title ? "Tôi muốn nhận báo giá cho sản phẩm: {$sp_title}.\n(Vui lòng tư vấn theo kích thước không gian của tôi.)" : '';
  $branch = [
    'city' => 'Thái Nguyên',
    'address' => nep('address'),
    'phone' => nep('hotline'),
    'phone2' => nep('hotline_alt'),
  ];
@endphp

@section('content')
  {{-- Banner --}}
  <section style="background:var(--olive-900);color:#fff;padding-top:140px;padding-bottom:var(--space-9)">
    <x-container>
      <div style="font-size:var(--text-sm);color:rgba(255,255,255,.6);display:flex;gap:8px;align-items:center;margin-bottom:16px">
        <a href="{{ home_url('/') }}" style="color:inherit">Trang chủ</a>
        <x-icon name="chevron-right" :size="14" color="rgba(255,255,255,.5)" />
        <span style="color:#fff">Liên hệ</span>
      </div>
      <x-eyebrow rule color="var(--moss)">Liên hệ</x-eyebrow>
      <h1 style="color:#fff;font-size:var(--text-display-lg);margin-top:14px;max-width:20ch;text-wrap:balance">Cùng kiến tạo không gian của bạn</h1>
      <p style="color:rgba(244,242,236,.78);font-size:var(--text-lg);margin-top:14px;max-width:52ch">Để lại thông tin, đội ngũ NẾP sẽ liên hệ tư vấn và đặt lịch khảo sát miễn phí trong vòng 24 giờ.</p>
    </x-container>
  </section>

  {{-- Form + info --}}
  <section style="padding-top:var(--section-y);padding-bottom:var(--space-7);background:var(--cream)">
    <x-container class="nep-contact-grid" :style="'display:grid;grid-template-columns:1.3fr 1fr;gap:var(--space-9);align-items:start'">
      {{-- Form card --}}
      <div style="background:var(--paper);border-radius:var(--radius-xl);border:1px solid var(--border-soft);box-shadow:var(--shadow-md);padding:var(--space-8)">
        @if($sent)
          <div style="text-align:center;padding:var(--space-8) 0">
            <span style="display:inline-flex;align-items:center;justify-content:center;width:72px;height:72px;border-radius:50%;background:var(--success-soft);margin-bottom:20px"><x-icon name="check" :size="34" color="var(--success)" /></span>
            <h3 style="font-size:var(--text-display-md);margin-bottom:10px">Cảm ơn bạn!</h3>
            <p style="font-size:var(--text-lg);color:var(--text-body);max-width:36ch;margin:0 auto 24px">Yêu cầu đã được gửi. Chúng tôi sẽ liên hệ với bạn trong thời gian sớm nhất.</p>
            <x-button href="{{ get_permalink() }}" variant="secondary">Gửi yêu cầu khác</x-button>
          </div>
        @else
          <form method="post" action="{{ admin_url('admin-post.php') }}">
            <input type="hidden" name="action" value="nep_contact">
            <input type="hidden" name="redirect" value="{{ get_permalink() }}">
            @php(wp_nonce_field('nep_contact', 'nep_contact_nonce'))
            <h2 style="font-size:var(--text-h1);font-family:var(--font-display);font-weight:600;margin-bottom:6px">Gửi yêu cầu tư vấn</h2>
            <p style="font-size:var(--text-sm);color:var(--text-muted);margin-bottom:26px">Các trường có dấu * là bắt buộc.</p>
            @if($sp_title)
              <div style="display:flex;align-items:center;gap:10px;background:var(--olive-50);border:1px solid var(--olive-200);border-radius:var(--radius-md);padding:12px 16px;margin-bottom:22px;font-size:var(--text-sm)">
                <x-icon name="blinds" :size="18" color="var(--brand)" />
                <span>Yêu cầu báo giá cho: <strong>{{ $sp_title }}</strong></span>
                <input type="hidden" name="san_pham" value="{{ $sp_title }}">
              </div>
            @endif
            <div class="nep-form-row" style="display:grid;grid-template-columns:1fr 1fr;gap:18px;margin-bottom:18px">
              <x-input label="Họ và tên *" name="ho_ten" placeholder="Nguyễn Văn A" :required="true" />
              <x-input label="Số điện thoại *" name="sdt" placeholder="09xx xxx xxx" :required="true" />
            </div>
            <div class="nep-form-row" style="display:grid;grid-template-columns:1fr 1fr;gap:18px;margin-bottom:18px">
              <x-input label="Email" name="email" type="email" placeholder="email@cua-ban.vn" />
              <x-select label="Chủ đề" name="chu_de" :options="$subjects" />
            </div>
            <div style="display:flex;flex-direction:column;gap:7px;margin-bottom:24px">
              <label for="contact-message" style="font-size:var(--text-sm);font-weight:600;color:var(--text-strong)">Nội dung</label>
              <textarea id="contact-message" name="noi_dung" rows="4" placeholder="Mô tả nhu cầu của bạn (loại rèm, diện tích, không gian...)" class="nep-input" style="height:auto;padding:14px 16px;resize:vertical">{{ $prefill_msg }}</textarea>
            </div>
            <x-button size="lg" full type="submit" variant="primary">Gửi yêu cầu <x-icon name="send" :size="18" /></x-button>
          </form>
        @endif
      </div>

      {{-- Info --}}
      <div>
        <x-eyebrow rule>Showroom</x-eyebrow>
        <h2 style="font-size:var(--text-display-md);margin:12px 0 24px">Ghé thăm chúng tôi</h2>
        <div style="display:flex;flex-direction:column;gap:14px">
          <div style="display:flex;gap:14px;padding:18px 20px;background:var(--olive-50);border:1px solid var(--olive-200);border-radius:var(--radius-lg)">
            <span style="display:inline-flex;align-items:center;justify-content:center;width:44px;height:44px;border-radius:var(--radius-md);background:var(--brand);flex:none"><x-icon name="map-pin" :size="20" color="#fff" /></span>
            <div>
              <div style="display:flex;align-items:center;gap:8px;font-weight:700;color:var(--text-strong)">
                {{ $branch['city'] }}<span style="font-size:var(--text-2xs);font-weight:600;letter-spacing:.06em;text-transform:uppercase;color:var(--brand);background:var(--paper);padding:2px 8px;border-radius:999px">Trụ sở</span>
              </div>
              <div style="font-size:var(--text-sm);color:var(--text-muted);margin-top:3px">{{ $branch['address'] }}</div>
              <div style="display:flex;gap:12px;margin-top:4px;flex-wrap:wrap">
                @foreach([$branch['phone'], $branch['phone2']] as $p)
                  <a href="{{ App\nep_tel($p) }}" style="font-size:var(--text-sm);color:var(--text-brand);font-weight:600">{{ $p }}</a>
                @endforeach
              </div>
            </div>
          </div>
        </div>
        <div style="display:flex;flex-direction:column;gap:10px;margin-top:22px;padding-top:22px;border-top:1px solid var(--border-soft)">
          <a href="mailto:{{ nep('email') }}" style="display:flex;align-items:center;gap:10px;font-size:var(--text-sm);color:var(--text-body)"><x-icon name="mail" :size="16" color="var(--brand)" /> {{ nep('email') }}</a>
          <div style="display:flex;align-items:center;gap:10px;font-size:var(--text-sm);color:var(--text-body)"><x-icon name="clock" :size="16" color="var(--brand)" /> {{ nep('hours') }}</div>
        </div>
      </div>
    </x-container>
  </section>

  {{-- Map --}}
  <section style="background:var(--cream);padding-top:0;padding-bottom:var(--section-y)">
    <x-container>
      <div style="position:relative;height:420px;border-radius:var(--radius-xl);overflow:hidden;border:1px solid var(--border-soft);box-shadow:var(--shadow-md)">
        <iframe title="Bản đồ NẾP" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d232.19111362323318!2d105.87063940464353!3d21.387627964750642!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31351ff4e328658b%3A0x528ff5d209912cc7!2zUsOobSBNaW5oIEjDoA!5e0!3m2!1svi!2s!4v1781590512909!5m2!1svi!2s" width="100%" height="100%" style="border:0;display:block" allowfullscreen loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
      </div>
    </x-container>
  </section>
@endsection
