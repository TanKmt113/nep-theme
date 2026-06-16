@extends('layouts.app')

@section('content')
  {{-- Hero --}}
  <section style="background:var(--olive-900);color:#fff;padding-top:140px;padding-bottom:var(--space-9)">
    <x-container>
      <x-eyebrow rule color="var(--moss)">Catalog</x-eyebrow>
      <h1 style="color:#fff;font-size:var(--text-display-lg);margin-top:14px;max-width:20ch;text-wrap:balance">Catalogue &amp; bảng giá</h1>
      <p style="color:rgba(244,242,236,.78);font-size:var(--text-lg);margin-top:14px;max-width:52ch">Xem trước và tải về các ấn phẩm catalogue, bảng màu, hồ sơ năng lực của NẾP.</p>
    </x-container>
  </section>

  {{-- Listing --}}
  <section style="padding-top:var(--section-y);padding-bottom:var(--section-y);background:var(--cream)">
    <x-container>
      @if(have_posts())
        <div class="nep-grid-3" style="display:grid;grid-template-columns:repeat(3,1fr);gap:var(--space-6)">
          @while(have_posts())
            @php
              the_post();
              $cover = function_exists('get_field') ? get_field('catalog_cover') : '';
              if (! $cover) { $cover = get_the_post_thumbnail_url(get_the_ID(), 'nep_card') ?: ''; }
              $excerpt = function_exists('get_field') ? get_field('catalog_excerpt') : '';
              if (! $excerpt) { $excerpt = get_the_excerpt(); }
              $pdf = function_exists('get_field') ? get_field('catalog_pdf') : null;
              $has_pdf = is_array($pdf) ? ! empty($pdf['url']) : ! empty($pdf);
            @endphp
            <a href="{{ get_permalink() }}" style="display:flex;flex-direction:column;background:var(--paper);border-radius:var(--radius-lg);border:1px solid var(--border-soft);overflow:hidden;text-decoration:none;box-shadow:var(--shadow-sm)">
              <div style="position:relative;aspect-ratio:3/4;overflow:hidden;background:var(--beige)">
                @if($cover)
                  <img src="{{ $cover }}" alt="{{ get_the_title() }}" style="width:100%;height:100%;object-fit:cover">
                @else
                  <span style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center"><x-icon name="file-text" :size="48" color="var(--olive-300)" /></span>
                @endif
                @if($has_pdf)
                  <span style="position:absolute;top:12px;left:12px;display:inline-flex;align-items:center;gap:6px;background:var(--glass-bg);backdrop-filter:blur(10px);padding:5px 10px;border-radius:var(--radius-pill);font-size:var(--text-2xs);font-weight:700;color:var(--olive-700)"><x-icon name="file-text" :size="13" color="var(--olive-700)" /> PDF</span>
                @endif
              </div>
              <div style="padding:var(--space-5);display:flex;flex-direction:column;gap:8px;flex:1">
                <h3 style="font-size:var(--text-h3);font-weight:600;color:var(--text-strong)">{{ get_the_title() }}</h3>
                @if($excerpt)
                  <p style="font-size:var(--text-sm);color:var(--text-muted);line-height:1.6;margin:0">{{ $excerpt }}</p>
                @endif
                <span style="margin-top:auto;display:inline-flex;align-items:center;gap:6px;font-size:var(--text-sm);font-weight:600;color:var(--text-brand)">Xem catalog <x-icon name="arrow-right" :size="16" color="var(--brand)" /></span>
              </div>
            </a>
          @endwhile
        </div>

        @php(the_posts_pagination(['mid_size' => 1, 'prev_text' => '←', 'next_text' => '→']))
      @else
        <div style="text-align:center;padding:var(--space-10) 0;color:var(--text-muted)">
          <x-icon name="folder-open" :size="40" color="var(--text-muted)" />
          <p style="margin-top:12px">Chưa có catalog nào. Thêm tại <strong>Catalog → Thêm mới</strong> trong trang quản trị.</p>
        </div>
      @endif
    </x-container>
  </section>

  @include('sections.final-cta')
@endsection
