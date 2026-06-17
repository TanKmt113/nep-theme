{{--
  Template Name: Xưởng thêu (Embroidery)
--}}
@extends('layouts.app')

@php
  use function App\page_field;
  use function App\page_rows;

  $hero = page_field('emb_hero_image', 'https://images.unsplash.com/photo-1452860606245-08befc0ff44b?w=1400&q=80');
  $stats = page_rows('emb_stats', [['20+','Máy thêu vi tính'],['12 đầu','Thêu đa kim cùng lúc'],['50.000','Sản phẩm / tháng'],['48h','Giao hàng nhanh']], fn ($r) => [$r['value'] ?? '', $r['label'] ?? '']);
  $cap_img1 = page_field('emb_cap_img1', 'https://images.unsplash.com/photo-1620799139507-2a76f79a2f4d?w=700&q=80');
  $cap_img2 = page_field('emb_cap_img2', 'https://images.unsplash.com/photo-1574180566232-aaad1b5b8450?w=700&q=80');
  $caps = page_rows('emb_caps', [
    'Số hoá file thêu miễn phí từ logo của bạn',
    'Thêu trên mọi chất liệu: cotton, kaki, dạ, nỉ',
    'Cam kết màu chỉ chuẩn, đường thêu không bung',
  ], fn ($r) => $r['text'] ?? '');
  $services = page_rows('emb_services', [
    ['Thêu logo','Logo doanh nghiệp sắc nét trên mọi chất liệu.','scan-line','https://images.unsplash.com/photo-1620799139507-2a76f79a2f4d?w=800&q=80'],
    ['Thêu đồng phục','Đồng phục công ty, trường học số lượng lớn.','shirt','https://images.unsplash.com/photo-1574180566232-aaad1b5b8450?w=800&q=80'],
    ['Thêu áo polo','Áo polo quà tặng, sự kiện, team building.','shirt','https://images.unsplash.com/photo-1576566588028-4147f3842f27?w=800&q=80'],
    ['Thêu mũ','Mũ lưỡi trai, nón kết thêu nổi bền đẹp.','hard-hat','https://images.unsplash.com/photo-1521577352947-9bb58764b69a?w=800&q=80'],
    ['Thêu khăn','Khăn tắm, khăn mặt khách sạn cao cấp.','layers','https://images.unsplash.com/photo-1558769132-cb1aea458c5e?w=800&q=80'],
    ['Gia công OEM','Nhận gia công thêu số lượng lớn theo yêu cầu.','factory','https://images.unsplash.com/photo-1489987707025-afc232f7ea0f?w=800&q=80'],
  ], fn ($r) => [$r['title'] ?? '', $r['desc'] ?? '', $r['icon'] ?? 'shirt', $r['image'] ?? '']);
@endphp

@section('content')
  {{-- Hero --}}
  <section style="position:relative;min-height:82vh;display:flex;align-items:center;overflow:hidden;background:var(--olive-900)">
    <img src="{{ $hero }}" alt="Xưởng thêu" fetchpriority="high" decoding="async" style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;opacity:.5">
    <div style="position:absolute;inset:0;background:linear-gradient(90deg,rgba(28,30,20,.86) 0%,rgba(28,30,20,.5) 60%,rgba(28,30,20,.3) 100%)"></div>
    <x-container :style="'position:relative;padding-top:100px'">
      <div style="max-width:640px">
        <x-eyebrow rule color="var(--moss)">{{ page_field('emb_hero_eyebrow', 'Xưởng thêu vi tính') }}</x-eyebrow>
        <h1 style="color:#fff;font-size:var(--text-display-xl);line-height:1.04;margin:20px 0 0;text-wrap:balance">
          {!! e(page_field('emb_hero_title', 'Logo, đồng phục & quà tặng')) !!}@php($a = page_field('emb_hero_accent', 'thêu sắc nét'))@if($a) <span style="font-style:italic;color:var(--moss-soft)">{{ $a }}</span>@endif
        </h1>
        <p style="color:rgba(255,255,255,.85);font-size:var(--text-lg);line-height:1.6;max-width:46ch;margin-top:22px">
          {{ page_field('emb_hero_desc', 'Hệ thống máy thêu vi tính đa kim hiện đại — nhận gia công số lượng lớn cho doanh nghiệp, trường học và sự kiện.') }}
        </p>
        <div style="display:flex;gap:14px;margin-top:32px;flex-wrap:wrap">
          @php($b1 = page_field('emb_btn1_text', 'Xem máy thêu hoạt động'))@if($b1)<x-button href="{{ page_field('emb_btn1_url', home_url('/lien-he')) }}" size="lg" variant="gold"><x-icon name="play" :size="18" /> {{ $b1 }}</x-button>@endif
          @php($b2 = page_field('emb_btn2_text', 'Báo giá gia công'))@if($b2)<x-button href="{{ page_field('emb_btn2_url', home_url('/lien-he')) }}" size="lg" variant="secondary">{{ $b2 }}</x-button>@endif
        </div>
      </div>
    </x-container>
  </section>

  {{-- Stats --}}
  <section style="background:var(--champagne-faint);padding:var(--space-8) 0">
    <x-container class="nep-grid-4" :style="'display:grid;grid-template-columns:repeat(4,1fr);gap:var(--space-6)'">
      @foreach($stats as $s)
        <x-stat :value="$s[0]" :label="$s[1]" align="center" />
      @endforeach
    </x-container>
  </section>

  {{-- Capability --}}
  <section style="padding-top:var(--section-y);padding-bottom:var(--section-y);background:var(--cream)">
    <x-container :style="'display:grid;grid-template-columns:1fr 1fr;gap:var(--space-10);align-items:center'">
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
        <img src="{{ $cap_img1 }}" alt="{{ page_field('emb_cap_heading', 'Năng lực sản xuất') }}" loading="lazy" decoding="async" style="width:100%;aspect-ratio:3/4;object-fit:cover;border-radius:var(--radius-lg);box-shadow:var(--shadow-md)">
        <img src="{{ $cap_img2 }}" alt="{{ page_field('emb_cap_heading', 'Năng lực sản xuất') }}" loading="lazy" decoding="async" style="width:100%;aspect-ratio:3/4;object-fit:cover;border-radius:var(--radius-lg);margin-top:32px;box-shadow:var(--shadow-md)">
      </div>
      <div>
        <x-eyebrow rule>{{ page_field('emb_cap_eyebrow', 'Năng lực sản xuất') }}</x-eyebrow>
        <h2 style="font-size:var(--text-display-lg);margin:16px 0 18px;max-width:15ch">{{ page_field('emb_cap_heading', 'Máy móc hiện đại, sản lượng lớn') }}</h2>
        <p style="font-size:var(--text-lg);line-height:1.7;color:var(--text-body);max-width:46ch">
          {{ page_field('emb_cap_text', 'Hệ thống hơn 20 máy thêu vi tính đa kim cho phép chúng tôi xử lý đơn hàng lớn với chất lượng đồng đều và thời gian giao hàng nhanh.') }}
        </p>
        <div style="display:flex;flex-direction:column;gap:14px;margin-top:28px">
          @foreach($caps as $t)
            <div style="display:flex;gap:12px;align-items:center;font-size:var(--text-base);color:var(--text-body)">
              <span style="display:inline-flex;align-items:center;justify-content:center;width:26px;height:26px;border-radius:50%;background:var(--olive-100);flex:none"><x-icon name="check" :size="15" color="var(--brand)" /></span>
              {{ $t }}
            </div>
          @endforeach
        </div>
      </div>
    </x-container>
  </section>

  {{-- Services --}}
  <section style="padding-top:var(--section-y);padding-bottom:var(--section-y);background:var(--paper)">
    <x-container>
      <div style="text-align:center;margin-bottom:var(--space-8)">
        <x-eyebrow rule center>{{ page_field('emb_svc_eyebrow', 'Dịch vụ thêu') }}</x-eyebrow>
        <h2 style="font-size:var(--text-display-md);margin-top:12px">{{ page_field('emb_svc_heading', 'Chúng tôi nhận thêu gì?') }}</h2>
      </div>
      <div class="nep-grid-3" style="display:grid;grid-template-columns:repeat(3,1fr);gap:var(--space-5)">
        @foreach($services as $s)
          <div class="nep-emb-card" style="border-radius:var(--radius-lg);overflow:hidden;background:var(--paper);border:1px solid var(--border-soft);box-shadow:var(--shadow-sm)">
            <div style="position:relative;aspect-ratio:16/11;overflow:hidden;background:var(--beige)">
              <img src="{{ $s[3] }}" alt="{{ $s[0] }}" loading="lazy" decoding="async" style="width:100%;height:100%;object-fit:cover">
              <span style="position:absolute;top:14px;left:14px;display:inline-flex;align-items:center;justify-content:center;width:44px;height:44px;border-radius:var(--radius-md);background:var(--glass-bg);backdrop-filter:blur(10px)"><x-icon :name="$s[2]" :size="20" color="var(--olive-700)" /></span>
            </div>
            <div style="padding:var(--space-5)">
              <h3 style="font-size:var(--text-h3);font-weight:600;margin-bottom:6px">{{ $s[0] }}</h3>
              <p style="font-size:var(--text-sm);color:var(--text-muted);line-height:1.55;margin:0">{{ $s[1] }}</p>
            </div>
          </div>
        @endforeach
      </div>
    </x-container>
  </section>

  {{-- CTA --}}
  <section style="padding-top:var(--section-y);padding-bottom:var(--section-y);background:var(--olive-900);color:#fff">
    <x-container narrow :style="'text-align:center'">
      <h2 style="color:#fff;font-size:var(--text-display-lg);line-height:1.1;max-width:18ch;margin:0 auto;text-wrap:balance">{{ page_field('emb_cta_heading', 'Gửi logo — nhận mẫu thêu trong 24 giờ') }}</h2>
      <p style="color:rgba(244,242,236,.78);font-size:var(--text-lg);margin-top:16px;max-width:44ch;margin-inline:auto">{{ page_field('emb_cta_text', 'Đội ngũ tư vấn sẽ báo giá và gửi mẫu số hoá miễn phí cho đơn hàng của bạn.') }}</p>
      <div style="display:flex;gap:14px;justify-content:center;margin-top:32px;flex-wrap:wrap">
        @php($c1 = page_field('emb_cta_btn1_text', 'Gửi logo báo giá'))@if($c1)<x-button href="{{ page_field('emb_cta_btn1_url', home_url('/lien-he')) }}" size="lg" variant="gold"><x-icon name="upload" :size="18" /> {{ $c1 }}</x-button>@endif
        @php($c2 = page_field('emb_cta_btn2_text', 'Xem sản phẩm rèm'))@if($c2)<x-button href="{{ page_field('emb_cta_btn2_url', App\nep_shop_url()) }}" size="lg" variant="secondary">{{ $c2 }}</x-button>@endif
      </div>
    </x-container>
  </section>
@endsection
