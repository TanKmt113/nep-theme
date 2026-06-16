@extends('layouts.app')

@php use function App\nep_field; use function App\nep_quote_url; @endphp

@section('content')
  <style>.nep-breadcrumb a{color:var(--text-muted);text-decoration:none;transition:color .2s}.nep-breadcrumb a:hover{color:var(--text-brand)}.nep-breadcrumb svg{color:var(--text-muted);opacity:.55;flex:none}</style>
  @while(have_posts())
    @php
      the_post();
      $id = get_the_ID();
      $product = function_exists('wc_get_product') ? wc_get_product($id) : null;
      $cats = get_the_terms($id, 'product_cat');
      $cat_name = (!is_wp_error($cats) && $cats) ? $cats[0]->name : '';
      // Gallery: featured + WC gallery images
      $gallery_ids = $product ? $product->get_gallery_image_ids() : [];
      $main = get_the_post_thumbnail_url($id, 'large') ?: (function_exists('wc_placeholder_img_src') ? wc_placeholder_img_src('large') : '');
    @endphp

    <section style="padding-top:140px;padding-bottom:var(--section-y);background:var(--cream)">
      <x-container>
        {{-- Breadcrumb: Trang chủ › Sản phẩm › Tên sản phẩm --}}
        <nav class="nep-breadcrumb" aria-label="Breadcrumb" style="display:flex;flex-wrap:wrap;align-items:center;gap:8px;font-size:var(--text-sm);color:var(--text-muted);margin-bottom:var(--space-7)">
          <a href="{{ home_url('/') }}">Trang chủ</a>
          <x-icon name="chevron-right" :size="15" />
          <a href="{{ wc_get_page_permalink('shop') }}">Sản phẩm</a>
          <x-icon name="chevron-right" :size="15" />
          <span aria-current="page" style="color:var(--text-strong);font-weight:600">{{ get_the_title() }}</span>
        </nav>

        <div class="nep-contact-grid" style="display:grid;grid-template-columns:1fr 1fr;gap:var(--space-10);align-items:start">
        {{-- Gallery --}}
        <div>
          <img id="nep-pdp-main" src="{{ $main }}" alt="{{ get_the_title() }}" style="width:100%;aspect-ratio:4/5;object-fit:cover;border-radius:var(--radius-xl);box-shadow:var(--shadow-lg)">
          @if($gallery_ids)
            <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:10px;margin-top:12px">
              @foreach(array_merge([get_post_thumbnail_id($id)], $gallery_ids) as $gid)
                @php $thumb = wp_get_attachment_image_url($gid, 'nep_card'); @endphp
                @if($thumb)
                  <button type="button" class="nep-pdp-thumb" data-full="{{ wp_get_attachment_image_url($gid, 'large') }}" style="border:1px solid var(--border-soft);border-radius:var(--radius-md);overflow:hidden;padding:0;cursor:pointer;aspect-ratio:1/1;background:none">
                    <img src="{{ $thumb }}" alt="" style="width:100%;height:100%;object-fit:cover">
                  </button>
                @endif
              @endforeach
            </div>
          @endif
        </div>

        {{-- Summary --}}
        <div>
          @if($cat_name)<x-eyebrow rule>{{ $cat_name }}</x-eyebrow>@endif
          <h1 style="font-size:var(--text-display-md);margin:12px 0 14px">{{ get_the_title() }}</h1>

          {{-- RFQ: giá ẩn, thay bằng dòng mời báo giá --}}
          <div style="display:inline-flex;align-items:center;gap:8px;background:var(--olive-50);color:var(--olive-800);padding:8px 16px;border-radius:var(--radius-pill);font-weight:600;font-size:var(--text-sm);margin-bottom:22px">
            <x-icon name="messages-square" :size="16" color="var(--brand)" /> Liên hệ để nhận báo giá theo kích thước
          </div>

          <div style="font-size:var(--text-lg);line-height:1.7;color:var(--text-body);margin-bottom:28px">{!! get_the_content() ?: get_the_excerpt() !!}</div>

          <dl style="display:grid;grid-template-columns:auto 1fr;gap:10px 24px;font-size:var(--text-base);margin-bottom:30px">
            @foreach([['Chất liệu','material'],['Màu sắc','color']] as $row)
              @if($v = nep_field($row[1], $id))
                <dt style="color:var(--text-muted)">{{ $row[0] }}</dt><dd style="font-weight:600;margin:0">{{ $v }}</dd>
              @endif
            @endforeach
          </dl>

          <div style="display:flex;gap:12px;flex-wrap:wrap">
            <x-button href="{{ nep_quote_url($id) }}" size="lg" variant="primary">Yêu cầu báo giá <x-icon name="arrow-right" :size="18" /></x-button>
            <x-button href="{{ App\nep_tel(App\nep('hotline')) }}" size="lg" variant="secondary"><x-icon name="phone" :size="18" /> Gọi tư vấn</x-button>
          </div>
        </div>
        </div>
      </x-container>
    </section>

    {{-- Sản phẩm liên quan (cùng loại) --}}
    @php
      $rel = $cats && !is_wp_error($cats) ? get_posts(['post_type' => 'product', 'numberposts' => 4, 'post__not_in' => [$id], 'tax_query' => [['taxonomy' => 'product_cat', 'field' => 'term_id', 'terms' => $cats[0]->term_id]]]) : [];
    @endphp
    @if($rel)
      <section style="padding-top:var(--section-y);padding-bottom:var(--section-y);background:var(--paper)">
        <x-container>
          <x-eyebrow rule>Cùng loại</x-eyebrow>
          <h2 style="font-size:var(--text-display-md);margin:12px 0 var(--space-7)">Có thể bạn quan tâm</h2>
          <div class="nep-grid-4" style="display:grid;grid-template-columns:repeat(4,1fr);gap:var(--space-5)">
            @foreach($rel as $p)
              <x-product-card
                :image="get_the_post_thumbnail_url($p, 'nep_card') ?: ''"
                :meta="$cat_name"
                :title="get_the_title($p)"
                :description="get_the_excerpt($p)"
                :badge="nep_field('badge', $p->ID, '')"
                :href="get_permalink($p)"
                cta="Xem & báo giá"
              />
            @endforeach
          </div>
        </x-container>
      </section>
    @endif

    @include('sections.final-cta')
  @endwhile

  {{-- Đổi ảnh lớn khi bấm thumbnail --}}
  <script>
    document.querySelectorAll('.nep-pdp-thumb').forEach(function (b) {
      b.addEventListener('click', function () {
        var main = document.getElementById('nep-pdp-main');
        if (main && b.dataset.full) main.src = b.dataset.full;
      });
    });
  </script>
@endsection
