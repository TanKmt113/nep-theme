@extends('layouts.app')

{{-- Blog / search / catch-all list — NẾP editorial style. --}}
@section('content')
  <style>
    /* ===== Blog archive — thẻ bài viết theo phong cách NẾP ===== */
    .nep-blog-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(min(100%,340px),1fr));gap:var(--space-6)}
    .nep-blog-card{display:flex;flex-direction:column;background:var(--surface-card);border:1px solid var(--border-soft);border-radius:var(--radius-lg);overflow:hidden;box-shadow:var(--shadow-sm);text-decoration:none;transition:var(--t-card)}
    .nep-blog-card:hover{box-shadow:var(--shadow-lg);transform:translateY(var(--hover-lift))}
    .nep-blog-card__frame{position:relative;aspect-ratio:16/10;overflow:hidden;background:var(--beige)}
    .nep-blog-card__img{width:100%;height:100%;object-fit:cover;transition:transform var(--dur-reveal) var(--ease-entrance)}
    .nep-blog-card:hover .nep-blog-card__img{transform:scale(var(--hover-zoom))}
    .nep-blog-card__ph{position:absolute;inset:0;display:flex;align-items:center;justify-content:center;background:linear-gradient(135deg,var(--olive-100),var(--cream));color:var(--olive-400)}
    .nep-blog-card__cat{position:absolute;top:14px;left:14px;display:inline-flex;align-items:center;padding:5px 12px;border-radius:var(--radius-pill);background:rgba(20,22,14,.55);backdrop-filter:blur(4px);color:#fff;font-size:var(--text-2xs);font-weight:700;letter-spacing:.06em;text-transform:uppercase}
    .nep-blog-card__body{display:flex;flex-direction:column;gap:10px;padding:var(--space-5);flex:1}
    .nep-blog-card__meta{display:flex;align-items:center;gap:8px;font-size:var(--text-2xs);font-weight:600;letter-spacing:var(--tracking-eyebrow);text-transform:uppercase;color:var(--text-muted)}
    .nep-blog-card__meta svg{opacity:.6;flex:none}
    .nep-blog-card__title{font-family:var(--font-display);font-size:var(--text-h3);font-weight:600;line-height:1.2;color:var(--text-strong);transition:color .2s}
    .nep-blog-card:hover .nep-blog-card__title{color:var(--text-brand)}
    .nep-blog-card__excerpt{font-size:var(--text-sm);line-height:1.6;color:var(--text-muted);margin:0;display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;overflow:hidden}
    .nep-blog-card__cta{margin-top:auto;padding-top:var(--space-4);font-size:var(--text-sm);font-weight:600;color:var(--text-brand);display:inline-flex;align-items:center;gap:6px}
    .nep-blog-card__arrow{transition:var(--t-transform)}
    .nep-blog-card:hover .nep-blog-card__arrow{transform:translateX(4px)}
    /* Bài đầu tiên nổi bật: chiếm trọn hàng, ảnh trái + nội dung phải */
    @media (min-width:861px){
      .nep-blog-card--feature{grid-column:1/-1;flex-direction:row}
      .nep-blog-card--feature .nep-blog-card__frame{flex:0 0 56%;aspect-ratio:auto}
      .nep-blog-card--feature .nep-blog-card__body{justify-content:center;padding:var(--space-9)}
      .nep-blog-card--feature .nep-blog-card__title{font-size:var(--text-h1)}
      .nep-blog-card--feature .nep-blog-card__excerpt{font-size:var(--text-base);-webkit-line-clamp:4}
    }
  </style>

  @php
    $is_search    = is_search();
    $posts_page   = (int) get_option('page_for_posts');
    $heading      = $is_search
      ? 'Kết quả tìm kiếm'
      : ($posts_page ? get_the_title($posts_page) : (wp_strip_all_tags(get_the_archive_title()) ?: 'Tin tức'));
    $eyebrow      = $is_search ? 'Tìm kiếm' : 'Tin tức & Cảm hứng';
    $lead = '';
    if ($is_search) {
      global $wp_query;
      $lead = 'Tìm thấy ' . (int) $wp_query->found_posts . ' kết quả cho “' . esc_html(get_search_query()) . '”.';
    } elseif ($posts_page && ($d = get_post_field('post_content', $posts_page))) {
      $lead = wp_trim_words(wp_strip_all_tags($d), 32);
    }
  @endphp

  <section style="padding-top:140px;padding-bottom:var(--space-8);background:var(--cream)">
    <x-container>
      <x-eyebrow rule>{{ $eyebrow }}</x-eyebrow>
      <h1 style="font-size:var(--text-display-lg);margin-top:12px">{{ $heading }}</h1>
      @if($lead)
        <p style="font-size:var(--text-lg);line-height:1.7;color:var(--text-body);max-width:60ch;margin-top:16px">{{ $lead }}</p>
      @endif
    </x-container>
  </section>

  <section style="padding-top:var(--space-7);padding-bottom:var(--section-y);background:var(--paper)">
    <x-container>
      @if(have_posts())
        <div class="nep-blog-grid">
          @php $i = 0; @endphp
          @while(have_posts())
            @php
              the_post();
              $id      = get_the_ID();
              $img     = get_the_post_thumbnail_url($id, 'large');
              $cats    = get_the_category($id);
              $cat     = (!is_wp_error($cats) && $cats) ? $cats[0]->name : '';
              $feature = ($i === 0 && !$is_search && !is_paged());
            @endphp
            <a href="{{ get_permalink($id) }}" class="nep-blog-card {{ $feature ? 'nep-blog-card--feature' : '' }}">
              <div class="nep-blog-card__frame">
                @if($img)
                  <img src="{{ $img }}" alt="{{ get_the_title() }}" loading="{{ $i === 0 ? 'eager' : 'lazy' }}" decoding="async" class="nep-blog-card__img">
                @else
                  <div class="nep-blog-card__ph"><x-icon name="file-text" :size="48" /></div>
                @endif
                @if($cat)<span class="nep-blog-card__cat">{{ $cat }}</span>@endif
              </div>
              <div class="nep-blog-card__body">
                <div class="nep-blog-card__meta">
                  <x-icon name="clock" :size="13" /> {{ get_the_date('d/m/Y', $id) }}
                </div>
                <h2 class="nep-blog-card__title">{{ get_the_title() }}</h2>
                <p class="nep-blog-card__excerpt">{{ wp_trim_words(get_the_excerpt(), $feature ? 42 : 22) }}</p>
                <span class="nep-blog-card__cta">Đọc tiếp <span class="nep-blog-card__arrow">→</span></span>
              </div>
            </a>
            @php $i++; @endphp
          @endwhile
        </div>

        <div class="nep-pagination" style="margin-top:var(--space-9)">
          @php(the_posts_pagination(['mid_size' => 1, 'prev_text' => '←', 'next_text' => '→']))
        </div>
      @else
        <div style="text-align:center;padding:var(--space-10) 0">
          <p style="color:var(--text-muted);font-size:var(--text-lg)">Chưa có bài viết nào.</p>
        </div>
      @endif
    </x-container>
  </section>

  @include('sections.final-cta')
@endsection
