@extends('layouts.app')

@php use function App\nep_field; @endphp

@section('content')
  @while(have_posts())
    @php
      the_post();
      $id = get_the_ID();
      $gallery = nep_field('gallery', $id, []);
      // Các dòng thông tin hiển thị ở aside — chỉ giữ dòng có giá trị.
      $facts = [];
      foreach ([['Năm','year'],['Diện tích','area'],['Loại','type']] as $row) {
        if ($v = nep_field($row[1], $id)) $facts[] = [$row[0], $v];
      }
      // Hạng mục — bỏ các dòng repeater trống, chỉ giữ item có nội dung.
      $scope = [];
      foreach (nep_field('scope', $id, []) as $s) {
        $item = trim(is_array($s) ? ($s['item'] ?? '') : (string) $s);
        if ($item !== '') $scope[] = $item;
      }
      // Aside chỉ hiện khi có ít nhất một thông tin hoặc hạng mục.
      $hasAside = $facts || $scope;
    @endphp

    {{-- Hero --}}
    <section style="position:relative;min-height:62vh;display:flex;align-items:flex-end;overflow:hidden">
      <img src="{{ get_the_post_thumbnail_url($id, 'full') ?: '' }}" alt="{{ get_the_title() }}" fetchpriority="high" decoding="async" style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover">
      <div style="position:absolute;inset:0;background:linear-gradient(180deg,rgba(20,22,14,.1) 0%,rgba(20,22,14,.72) 100%)"></div>
      <x-container :style="'position:relative;padding-bottom:var(--space-10);padding-top:140px'">
        @if($type = nep_field('type', $id))
          <x-eyebrow rule color="var(--moss)">{{ $type }}</x-eyebrow>
        @endif
        <h1 style="color:#fff;font-size:var(--text-display-xl);margin-top:14px">{{ get_the_title() }}</h1>
        @if($place = nep_field('place', $id))
          <div style="display:flex;align-items:center;gap:8px;color:rgba(255,255,255,.85);margin-top:10px"><x-icon name="map-pin" :size="16" color="#fff" /> {{ $place }}</div>
        @endif
      </x-container>
    </section>

    {{-- Body --}}
    <section style="padding-top:var(--section-y);padding-bottom:var(--section-y);background:var(--paper)">
      <x-container :style="'margin-bottom:var(--space-6)'">
        <x-breadcrumb :items="\App\Seo\breadcrumb_items()" />
      </x-container>
      <x-container class="nep-da-body" :style="'display:grid;grid-template-columns:' . ($hasAside ? '1.6fr 1fr' : '1fr') . ';gap:var(--space-10);align-items:start'">
        <div style="font-size:var(--text-lg);line-height:1.8;color:var(--text-body)">{!! get_the_content() !!}</div>
        @if($hasAside)
          <aside style="background:var(--cream);border-radius:var(--radius-lg);padding:var(--space-7);border:1px solid var(--border-soft)">
            @if($facts)
              <dl style="display:grid;grid-template-columns:auto 1fr;gap:12px 20px;font-size:var(--text-base)">
                @foreach($facts as $f)
                  <dt style="color:var(--text-muted)">{{ $f[0] }}</dt><dd style="font-weight:600;margin:0">{{ $f[1] }}</dd>
                @endforeach
              </dl>
            @endif
            @if($scope)
              <h4 style="font-size:var(--text-sm);text-transform:uppercase;letter-spacing:.06em;color:var(--text-muted);margin:{{ $facts ? '24px' : '0' }} 0 12px">Hạng mục</h4>
              <ul style="list-style:none;margin:0;padding:0;display:flex;flex-direction:column;gap:8px">
                @foreach($scope as $s)
                  <li style="display:flex;align-items:center;gap:8px;font-size:var(--text-base)"><x-icon name="check" :size="16" color="var(--brand)" /> {{ is_array($s) ? ($s['item'] ?? '') : $s }}</li>
                @endforeach
              </ul>
            @endif
          </aside>
        @endif
      </x-container>

      @if($gallery)
        <x-container :style="'margin-top:var(--space-9)'">
          <x-eyebrow rule color="var(--moss)">Thư viện hình ảnh</x-eyebrow>
          <div class="nep-da-gallery" data-lightbox style="margin-top:var(--space-6)">
            @foreach($gallery as $img)
              @php
                $src   = is_array($img) ? ($img['sizes']['large'] ?? $img['url'] ?? '') : wp_get_attachment_image_url($img, 'large');
                $full  = is_array($img) ? ($img['url'] ?? $src) : (wp_get_attachment_image_url($img, 'full') ?: $src);
                $w     = is_array($img) ? (int)($img['width'] ?? 0)  : 0;
                $h     = is_array($img) ? (int)($img['height'] ?? 0) : 0;
                $cap   = is_array($img) ? ($img['caption'] ?: ($img['alt'] ?? '')) : '';
                // Row-span theo tỷ lệ ảnh → masonry gọn (auto-rows 10px, gap 16px).
                // Hệ số thấp + trần thấp = ảnh ngắn hơn, lưới cân đối hơn.
                $span  = ($w && $h) ? max(10, min(30, (int) round(($h / $w) * 20))) : 20;
              @endphp
              <a class="nep-da-gallery__item" href="{{ $full }}" style="grid-row:span {{ $span }}" data-caption="{{ esc_attr($cap) }}">
                <img src="{{ $src }}" alt="{{ $cap ?: get_the_title() }}" loading="lazy" decoding="async">
                @if($cap)<span class="nep-da-gallery__cap">{{ $cap }}</span>@endif
              </a>
            @endforeach
          </div>
        </x-container>
      @endif
    </section>

    @include('sections.final-cta')
  @endwhile
@endsection
