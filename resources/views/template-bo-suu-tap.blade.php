{{--
  Template Name: Bộ sưu tập (Catalogue / Lookbook)
--}}
@extends('layouts.app')

@php
  use function App\nep_field;
  $hero = 'https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?w=1800&q=80';
  $large = ['name' => 'Bộ sưu tập Linen', 'tagline' => 'Mộc mạc & tự nhiên', 'count' => 24, 'img' => 'https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?w=1100&q=80'];
  $smalls = [
    ['name' => 'Velvet Noir', 'tagline' => 'Nhung sang trọng', 'count' => 12, 'img' => 'https://images.unsplash.com/photo-1604014237800-1c9102c219da?w=900&q=80'],
    ['name' => 'Nan gỗ tự nhiên', 'tagline' => 'Ấm áp & bền bỉ', 'count' => 18, 'img' => 'https://images.unsplash.com/photo-1540574163026-643ea20ade25?w=900&q=80'],
  ];
  $cats = get_terms(['taxonomy' => 'product_cat', 'hide_empty' => false]);
  $featured = get_posts(['post_type' => 'product', 'numberposts' => 4, 'offset' => 4, 'post_status' => 'publish']);
  $listing = App\nep_shop_url();
@endphp

@section('content')
  {{-- Hero --}}
  <section style="position:relative;min-height:64vh;display:flex;align-items:flex-end;overflow:hidden">
    <img src="{{ $hero }}" alt="" style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover">
    <div style="position:absolute;inset:0;background:linear-gradient(180deg,rgba(20,22,14,.30) 0%,rgba(20,22,14,.12) 45%,rgba(20,22,14,.72) 100%)"></div>
    <x-container :style="'position:relative;padding-bottom:var(--space-9);padding-top:150px'">
      <x-eyebrow rule color="var(--moss)">Catalogue 2026</x-eyebrow>
      <h1 style="color:#fff;font-size:var(--text-display-xl);line-height:1.05;margin:16px 0 0;max-width:16ch;text-wrap:balance">
        Bộ sưu tập <span style="font-style:italic;color:var(--moss-soft)">rèm cửa</span>
      </h1>
      <p style="color:rgba(255,255,255,.88);font-size:var(--text-lg);line-height:1.6;max-width:50ch;margin-top:18px">
        Từ linen mộc mạc đến nhung sang trọng — khám phá những bộ sưu tập được tuyển chọn cho mọi phong cách không gian.
      </p>
    </x-container>
  </section>

  {{-- Lookbook --}}
  <section style="padding-top:var(--section-y);padding-bottom:var(--section-y);background:var(--cream)">
    <x-container>
      <div style="margin-bottom:var(--space-8)">
        <x-eyebrow rule>Lookbook</x-eyebrow>
        <h2 style="font-size:var(--text-display-md);margin-top:12px">Bộ sưu tập nổi bật</h2>
      </div>
      <div class="nep-lookbook" style="display:grid;grid-template-columns:1.5fr 1fr;gap:var(--space-5)">
        {{-- large --}}
        <a href="{{ $listing }}" class="nep-look-card" style="position:relative;border-radius:var(--radius-xl);overflow:hidden;display:block;min-height:520px">
          <img src="{{ $large['img'] }}" alt="{{ $large['name'] }}" style="width:100%;height:100%;object-fit:cover;position:absolute;inset:0">
          <div style="position:absolute;inset:0;background:linear-gradient(180deg,rgba(20,22,14,0) 40%,rgba(20,22,14,.74) 100%)"></div>
          <div style="position:absolute;left:26px;right:26px;bottom:24px;color:#fff">
            <div style="font-size:var(--text-2xs);font-weight:600;letter-spacing:var(--tracking-eyebrow);text-transform:uppercase;color:var(--moss-soft);margin-bottom:8px">{{ $large['count'] }} mẫu</div>
            <div style="font-family:var(--font-display);font-size:var(--text-display-md);font-weight:600;line-height:1.05">{{ $large['name'] }}</div>
            <div style="font-size:var(--text-sm);opacity:.86;margin-top:4px">{{ $large['tagline'] }}</div>
          </div>
        </a>
        <div style="display:grid;grid-template-rows:1fr 1fr;gap:var(--space-5)">
          @foreach($smalls as $l)
            <a href="{{ $listing }}" class="nep-look-card" style="position:relative;border-radius:var(--radius-xl);overflow:hidden;display:block;min-height:0;height:100%">
              <img src="{{ $l['img'] }}" alt="{{ $l['name'] }}" style="width:100%;height:100%;object-fit:cover;position:absolute;inset:0">
              <div style="position:absolute;inset:0;background:linear-gradient(180deg,rgba(20,22,14,0) 40%,rgba(20,22,14,.74) 100%)"></div>
              <div style="position:absolute;left:26px;right:26px;bottom:24px;color:#fff">
                <div style="font-size:var(--text-2xs);font-weight:600;letter-spacing:var(--tracking-eyebrow);text-transform:uppercase;color:var(--moss-soft);margin-bottom:8px">{{ $l['count'] }} mẫu</div>
                <div style="font-family:var(--font-display);font-size:var(--text-h2);font-weight:600;line-height:1.05">{{ $l['name'] }}</div>
                <div style="font-size:var(--text-sm);opacity:.86;margin-top:4px">{{ $l['tagline'] }}</div>
              </div>
            </a>
          @endforeach
        </div>
      </div>
    </x-container>
  </section>

  {{-- Categories --}}
  <section style="padding-top:var(--section-y);padding-bottom:var(--section-y);background:var(--paper)">
    <x-container>
      <div style="display:flex;align-items:flex-end;justify-content:space-between;margin-bottom:var(--space-8)">
        <div><x-eyebrow rule>Danh mục</x-eyebrow><h2 style="font-size:var(--text-display-md);margin-top:12px">Tất cả loại rèm</h2></div>
        <x-button href="{{ $listing }}" variant="ghost">Xem dạng lưới <x-icon name="arrow-right" :size="16" /></x-button>
      </div>
      <div class="nep-grid-4" style="display:grid;grid-template-columns:repeat(4,1fr);gap:var(--space-5)">
        @foreach($cats as $c)
          @php $img = function_exists('get_field') ? get_field('img', $c) : ''; $icon = function_exists('get_field') ? (get_field('icon', $c) ?: 'blinds') : 'blinds'; @endphp
          <a href="{{ get_term_link($c) }}" style="display:block;background:var(--cream);border-radius:var(--radius-lg);border:1px solid var(--border-soft);overflow:hidden;text-decoration:none">
            <div style="aspect-ratio:16/11;overflow:hidden"><img src="{{ $img }}" alt="{{ $c->name }}" style="width:100%;height:100%;object-fit:cover"></div>
            <div style="padding:16px 18px;display:flex;align-items:center;justify-content:space-between">
              <div style="display:flex;align-items:center;gap:10px">
                <x-icon :name="$icon" :size="20" color="var(--brand)" />
                <span style="font-weight:600;color:var(--text-strong);font-size:var(--text-base)">{{ $c->name }}</span>
              </div>
              <span style="font-size:var(--text-xs);color:var(--text-muted)">{{ $c->count }}</span>
            </div>
          </a>
        @endforeach
      </div>
    </x-container>
  </section>

  {{-- Featured --}}
  <section style="padding-top:var(--section-y);padding-bottom:var(--section-y);background:var(--beige)">
    <x-container>
      <div style="display:flex;align-items:flex-end;justify-content:space-between;margin-bottom:var(--space-7)">
        <div><x-eyebrow rule>Tuyển chọn</x-eyebrow><h2 style="font-size:var(--text-display-md);margin-top:12px">Sản phẩm tiêu biểu</h2></div>
      </div>
      <div class="nep-grid-4" style="display:grid;grid-template-columns:repeat(4,1fr);gap:var(--space-5)">
        @foreach($featured as $p)
          <x-product-card
            :image="get_the_post_thumbnail_url($p, 'nep_card') ?: ''"
            :meta="(get_the_terms($p, 'product_cat')[0]->name ?? '')"
            :title="get_the_title($p)"
            :description="get_the_excerpt($p)"
            :badge="nep_field('badge', $p->ID, '')"
            :href="get_permalink($p)"
            cta="Xem & báo giá"
          />
        @endforeach
      </div>
      <div style="text-align:center;margin-top:var(--space-8)">
        <x-button href="{{ $listing }}" size="lg">Xem toàn bộ sản phẩm <x-icon name="arrow-right" :size="18" /></x-button>
      </div>
    </x-container>
  </section>
@endsection
