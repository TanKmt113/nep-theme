@extends('layouts.app')

@section('content')
  @while(have_posts())
    @php
      the_post();
      $pdf = function_exists('get_field') ? get_field('catalog_pdf') : null;
      $pdf_url  = is_array($pdf) ? ($pdf['url'] ?? '') : (is_string($pdf) ? $pdf : '');
      $pdf_name = is_array($pdf) ? ($pdf['filename'] ?? 'catalog.pdf') : 'catalog.pdf';
    @endphp

    <section style="padding-top:140px;padding-bottom:var(--space-8);background:var(--cream)">
      <x-container>
        <x-eyebrow rule>Catalog</x-eyebrow>
        <h1 style="font-size:var(--text-display-lg);margin-top:12px">{{ get_the_title() }}</h1>
      </x-container>
    </section>

    <section style="padding-top:var(--space-7);padding-bottom:var(--section-y);background:var(--paper)">
      <x-container>
        @if($pdf_url)
          {{-- Thanh công cụ PDF --}}
          <div style="display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;margin-bottom:16px">
            <div style="display:flex;align-items:center;gap:8px;font-size:var(--text-sm);color:var(--text-muted)">
              <x-icon name="file-text" :size="18" color="var(--brand)" /> {{ $pdf_name }}
            </div>
            <div style="display:flex;gap:10px;flex-wrap:wrap">
              <x-button :href="$pdf_url" target="_blank" rel="noopener" variant="secondary" size="sm"><x-icon name="external-link" :size="16" /> Mở toàn màn hình</x-button>
              <x-button :href="$pdf_url" variant="primary" size="sm" download><x-icon name="download" :size="16" /> Tải xuống</x-button>
            </div>
          </div>

          {{-- Flipbook: lật trang như cuốn sách (JS nạp lazy, xem flipbook.js) --}}
          <div class="nep-flipbook-wrap">
            <div class="nep-flipbook" data-pdf="{{ $pdf_url }}"></div>
            <div class="nep-flipbook-loading">Đang tải catalogue…</div>
            <div class="nep-flipbook-bar">
              <button type="button" class="nep-flip-btn" data-flip="prev" aria-label="Trang trước"><x-icon name="chevron-left" :size="20" /></button>
              <span class="nep-flipbook-page">…</span>
              <button type="button" class="nep-flip-btn" data-flip="next" aria-label="Trang sau"><x-icon name="chevron-right" :size="20" /></button>
            </div>
            <noscript>
              <iframe src="{{ $pdf_url }}#view=FitH" title="{{ get_the_title() }}" style="width:100%;height:80vh;border:0;border-radius:var(--radius-lg)"></iframe>
            </noscript>
          </div>
          <p style="font-size:var(--text-sm);color:var(--text-muted);margin-top:12px;text-align:center">
            Mẹo: kéo góc trang để lật, hoặc dùng nút ‹ › / phím mũi tên. Không xem được? <a href="{{ $pdf_url }}" target="_blank" rel="noopener" style="color:var(--text-brand);font-weight:600">Mở PDF trong tab mới</a>.
          </p>
        @elseif(has_post_thumbnail())
          <div style="max-width:var(--container-text);margin:0 auto">
            <img src="{{ get_the_post_thumbnail_url(get_the_ID(), 'full') }}" alt="{{ get_the_title() }}" fetchpriority="high" decoding="async" style="width:100%;border-radius:var(--radius-xl);box-shadow:var(--shadow-md)">
          </div>
        @endif

        @if(trim(get_the_content()))
          <div class="nep-prose" style="max-width:var(--container-text);margin:var(--space-8) auto 0;font-size:var(--text-lg);line-height:1.8;color:var(--text-body)">
            @php(the_content())
          </div>
        @endif
      </x-container>
    </section>

    @include('sections.final-cta')
  @endwhile
@endsection
