@extends('layouts.app')

@php
  use function App\nep_shop_url;
  global $wp_query;
  $q = get_search_query();
  $total = (int) ($wp_query->found_posts ?? 0);
@endphp

@section('content')
  {{-- Hero --}}
  <section style="padding-top:140px;padding-bottom:var(--space-8);background:var(--cream)">
    <x-container>
      <x-eyebrow rule>Tìm kiếm</x-eyebrow>
      <h1 style="font-size:var(--text-display-md);margin-top:12px;color:var(--text-strong)">
        @if($q)Kết quả cho “{{ $q }}”@else Tìm kiếm @endif
      </h1>
      @if($q)
        <p style="margin-top:10px;color:var(--text-muted);font-size:var(--text-base)">
          @if($total) Tìm thấy <strong style="color:var(--text-strong)">{{ $total }}</strong> kết quả phù hợp.
          @else Không có kết quả nào khớp với từ khoá này. @endif
        </p>
      @endif
      <div style="max-width:560px;margin-top:var(--space-5)">
        <x-search-form placeholder="Nhập lại từ khoá…" />
      </div>
    </x-container>
  </section>

  {{-- Results --}}
  <section style="padding-top:var(--space-7);padding-bottom:var(--section-y);background:var(--paper)">
    <x-container>
      @if(have_posts())
        <div class="nep-search-results">
          @while(have_posts())
            @php
              the_post();
              $img = get_the_post_thumbnail_url(get_the_ID(), 'nep_card');
              $pt = get_post_type_object(get_post_type());
              $label = $pt ? $pt->labels->singular_name : '';
            @endphp
            <a href="{{ get_permalink() }}" class="nep-search-card">
              <div class="nep-search-card__media">
                @if($img)
                  <img src="{{ $img }}" alt="{{ get_the_title() }}" loading="lazy" decoding="async">
                @else
                  <x-icon name="file-text" :size="30" color="var(--text-muted)" />
                @endif
              </div>
              <div class="nep-search-card__body">
                @if($label)<span class="nep-search-card__meta">{{ $label }}</span>@endif
                <h2 class="nep-search-card__title">{{ get_the_title() }}</h2>
                <p class="nep-search-card__excerpt">{{ wp_trim_words(get_the_excerpt(), 26) }}</p>
                <span class="nep-search-card__cta">Xem chi tiết <span aria-hidden="true">→</span></span>
              </div>
            </a>
          @endwhile
        </div>
        <div style="margin-top:var(--space-8)">@php(the_posts_pagination(['mid_size' => 1, 'prev_text' => '←', 'next_text' => '→']))</div>
      @else
        {{-- Empty state --}}
        <div class="nep-search-empty">
          <span class="nep-search-empty__icon"><x-icon name="search-x" :size="40" color="var(--text-muted)" /></span>
          <h2 class="nep-search-empty__title">Không tìm thấy kết quả</h2>
          <p class="nep-search-empty__text">Thử dùng từ khoá ngắn gọn hơn, hoặc khám phá các mục dưới đây.</p>
          <div class="nep-search-empty__links">
            <x-button :href="nep_shop_url()" size="sm" variant="primary">Sản phẩm</x-button>
            <x-button :href="get_post_type_archive_link('du_an')" size="sm" variant="ghost">Dự án</x-button>
            <x-button :href="home_url('/xuong-theu')" size="sm" variant="ghost">Xưởng thêu</x-button>
            <x-button :href="home_url('/lien-he')" size="sm" variant="ghost">Liên hệ</x-button>
          </div>
        </div>
      @endif
    </x-container>
  </section>
@endsection
