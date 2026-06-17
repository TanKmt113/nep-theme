@extends('layouts.app')

@php use function App\nep_field; use function App\nep_quote_url; @endphp

@section('content')
  <style>
    .nep-breadcrumb a{color:var(--text-muted);text-decoration:none;transition:color .2s}.nep-breadcrumb a:hover{color:var(--text-brand)}.nep-breadcrumb svg{color:var(--text-muted);opacity:.55;flex:none}
    /* Tabs sản phẩm */
    .nep-tabs{display:flex;flex-wrap:wrap;gap:4px;border-bottom:1px solid var(--border-soft);margin-bottom:var(--space-7)}
    .nep-tab{appearance:none;background:none;border:none;cursor:pointer;padding:14px 20px;font-family:var(--font-sans);font-size:var(--text-base);font-weight:600;color:var(--text-muted);position:relative;white-space:nowrap;transition:color .2s}
    .nep-tab:hover{color:var(--text-strong)}
    .nep-tab.is-active{color:var(--text-brand)}
    .nep-tab.is-active::after{content:"";position:absolute;left:14px;right:14px;bottom:-1px;height:2px;background:var(--brand);border-radius:2px}
    .nep-tab__count{display:inline-flex;align-items:center;justify-content:center;min-width:20px;height:20px;padding:0 6px;margin-left:6px;border-radius:999px;background:var(--olive-50);color:var(--olive-800);font-size:var(--text-2xs);font-weight:700}
    .nep-tab-panel{display:none;animation:nepFade .25s ease}
    .nep-tab-panel.is-active{display:block}
    @keyframes nepFade{from{opacity:0;transform:translateY(4px)}to{opacity:1;transform:none}}
    .nep-rte{font-size:var(--text-base);line-height:1.8;color:var(--text-body);max-width:72ch}
    .nep-rte h2,.nep-rte h3{color:var(--text-strong);margin:1.4em 0 .5em}
    .nep-rte p{margin:0 0 1em}.nep-rte ul,.nep-rte ol{margin:0 0 1em;padding-left:1.4em}.nep-rte li{margin:.3em 0}
    .nep-rte img{border-radius:var(--radius-md);margin:1em 0}
    /* ----- Tab Mô tả ----- */
    .nep-desc__body{max-width:760px}
    .nep-desc__body>p:first-of-type{font-size:var(--text-lg);color:var(--text-strong);line-height:1.75}
    /* ----- Tab Thông số: lưới thẻ với đường kẻ mảnh ----- */
    .nep-specs{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:0 var(--space-9);margin:0;max-width:780px}
    .nep-specs__item{display:flex;justify-content:space-between;align-items:baseline;gap:16px;padding:15px 2px;border-bottom:1px solid var(--border-soft)}
    .nep-specs__item dt{color:var(--text-muted);font-size:var(--text-sm);font-weight:600}
    .nep-specs__item dd{margin:0;font-weight:700;color:var(--text-strong);text-align:right}
    @media (max-width:640px){.nep-specs{grid-template-columns:1fr}}
    .nep-empty{color:var(--text-muted);background:var(--cream);border:1px dashed var(--border-soft);border-radius:var(--radius-lg);padding:18px 22px;margin:0}
    /* ===== Gallery: ảnh chính (zoom) + thumbnail slider ===== */
    .nep-pdp-stage{display:block;width:100%;min-width:0;max-width:100%;padding:0;border:none;background:none;cursor:zoom-in;position:relative;border-radius:var(--radius-xl);overflow:hidden;box-shadow:var(--shadow-lg)}
    .nep-pdp-stage img{display:block;width:100%;max-width:100%;aspect-ratio:4/5;object-fit:cover}
    .nep-pdp-zoom{position:absolute;right:14px;bottom:14px;width:40px;height:40px;display:inline-flex;align-items:center;justify-content:center;border-radius:50%;background:rgba(20,22,14,.55);color:#fff;backdrop-filter:blur(4px);opacity:0;transition:opacity .2s,transform .2s}
    .nep-pdp-stage:hover .nep-pdp-zoom{opacity:1}
    .nep-pdp-slider{display:flex;align-items:center;gap:8px;margin-top:12px}
    .nep-pdp-track{display:flex;gap:10px;overflow-x:auto;scroll-snap-type:x proximity;scroll-behavior:smooth;flex:1;-ms-overflow-style:none;scrollbar-width:none;padding-bottom:2px;cursor:grab;touch-action:pan-x;-webkit-overflow-scrolling:touch}
    .nep-pdp-track::-webkit-scrollbar{display:none}
    .nep-pdp-thumb{flex:0 0 84px;width:84px;aspect-ratio:1/1;scroll-snap-align:start;border:1px solid var(--border-soft);border-radius:var(--radius-md);overflow:hidden;padding:0;cursor:pointer;background:none;transition:border-color .2s}
    .nep-pdp-thumb img{width:100%;height:100%;object-fit:cover;display:block}
    .nep-pdp-thumb:hover{border-color:var(--olive-300)}
    .nep-pdp-thumb.is-active{border-color:var(--brand);box-shadow:0 0 0 1px var(--brand)}
    .nep-pdp-nav{flex:none;width:36px;height:36px;display:inline-flex;align-items:center;justify-content:center;border:1px solid var(--border-strong);background:var(--paper);color:var(--text-strong);border-radius:50%;cursor:pointer;transition:background .2s,border-color .2s,opacity .2s}
    .nep-pdp-nav:hover{background:var(--cream);border-color:var(--brand);color:var(--text-brand)}
    .nep-pdp-nav[disabled]{opacity:.35;cursor:default}
    /* Lightbox */
    .nep-lightbox{position:fixed;inset:0;z-index:9999;display:flex;align-items:center;justify-content:center;background:rgba(18,20,14,.92);padding:24px;opacity:0;transition:opacity .22s ease}
    .nep-lightbox[hidden]{display:none}
    .nep-lightbox.is-open{opacity:1}
    .nep-lightbox__img{max-width:90vw;max-height:90vh;object-fit:contain;border-radius:var(--radius-md);cursor:zoom-in;transition:transform .25s ease;will-change:transform}
    .nep-lightbox__img.is-zoomed{cursor:zoom-out;transition:none}
    .nep-lightbox__btn{position:absolute;display:inline-flex;align-items:center;justify-content:center;border:none;background:rgba(255,255,255,.12);color:#fff;border-radius:50%;cursor:pointer;backdrop-filter:blur(4px);transition:background .2s}
    .nep-lightbox__btn:hover{background:rgba(255,255,255,.25)}
    .nep-lightbox__close{top:18px;right:18px;width:46px;height:46px}
    .nep-lightbox__prev,.nep-lightbox__next{top:50%;transform:translateY(-50%);width:50px;height:50px}
    .nep-lightbox__prev{left:18px}.nep-lightbox__next{right:18px}
    @media (max-width:640px){.nep-lightbox__prev{left:8px}.nep-lightbox__next{right:8px}.nep-pdp-thumb{flex-basis:68px;width:68px}}
    /* ===== Popup Yêu cầu báo giá ===== */
    .nep-modal{position:fixed;inset:0;z-index:10000;display:flex;align-items:center;justify-content:center;background:rgba(18,20,14,.6);backdrop-filter:blur(3px);padding:20px;opacity:0;transition:opacity .22s ease}
    .nep-modal[hidden]{display:none}
    .nep-modal.is-open{opacity:1}
    .nep-modal__panel{position:relative;width:100%;max-width:460px;max-height:92vh;overflow:auto;background:var(--paper);border-radius:var(--radius-xl);box-shadow:var(--shadow-xl);padding:32px;transform:translateY(12px) scale(.98);transition:transform .25s ease}
    .nep-modal.is-open .nep-modal__panel{transform:none}
    .nep-modal__close{position:absolute;top:16px;right:16px;width:40px;height:40px;display:inline-flex;align-items:center;justify-content:center;border:none;background:var(--cream);color:var(--text-strong);border-radius:50%;cursor:pointer;transition:background .2s}
    .nep-modal__close:hover{background:var(--beige)}
    .nep-modal__eyebrow{display:block;font-size:var(--text-2xs);font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--text-brand)}
    .nep-modal__title{font-family:var(--font-display);font-size:var(--text-h2);font-weight:600;color:var(--text-strong);margin:6px 0 6px}
    .nep-modal__sub{font-size:var(--text-sm);color:var(--text-muted);line-height:1.6;margin:0 0 20px}
    .nep-modal__sub strong{color:var(--text-strong)}
    .nep-modal__form{display:flex;flex-direction:column;gap:14px}
    .nep-modal__row{display:grid;grid-template-columns:1fr 1fr;gap:14px}
    .nep-modal__form label{display:flex;flex-direction:column;gap:6px;min-width:0;font-size:var(--text-sm);font-weight:600;color:var(--text-strong)}
    .nep-modal__form input,.nep-modal__form textarea{width:100%;min-width:0;box-sizing:border-box;font-family:var(--font-sans);font-size:var(--text-base);font-weight:400;color:var(--text-strong);background:var(--paper);border:1px solid var(--border-strong);border-radius:var(--radius-md);padding:11px 13px;outline:none;transition:border-color .2s,box-shadow .2s}
    .nep-modal__form input:focus,.nep-modal__form textarea:focus{border-color:var(--brand);box-shadow:0 0 0 3px var(--olive-50)}
    .nep-modal__form textarea{resize:vertical}
    .nep-modal__msg{margin:0;padding:11px 14px;border-radius:var(--radius-md);font-size:var(--text-sm);font-weight:600}
    .nep-modal__msg.is-error{background:#fdecec;color:#b3261e}
    .nep-modal__msg.is-success{background:var(--olive-50);color:var(--olive-800)}
    .nep-modal__form button[disabled]{opacity:.6;cursor:default}
    @media (max-width:480px){.nep-modal__row{grid-template-columns:1fr}.nep-modal__panel{padding:24px}}
    /* ===== Tab Đánh giá (WooCommerce reviews) — khoác giao diện NẾP ===== */
    .nep-woo-reviews{max-width:760px}
    .nep-woo-reviews .woocommerce-Reviews-title,.nep-woo-reviews .comment-reply-title{font-family:var(--font-display);font-size:var(--text-h3);font-weight:600;color:var(--text-strong);margin:0 0 18px}
    .nep-woo-reviews #comments{margin-bottom:var(--space-8)}
    .nep-woo-reviews .woocommerce-noreviews{display:flex;align-items:center;gap:10px;color:var(--text-muted);background:var(--cream);border:1px dashed var(--border-soft);border-radius:var(--radius-lg);padding:18px 22px;margin:0}
    /* Danh sách review */
    .nep-woo-reviews ol.commentlist{list-style:none;margin:0 0 8px;padding:0;display:flex;flex-direction:column;gap:16px}
    .nep-woo-reviews ol.commentlist li.review,.nep-woo-reviews ol.commentlist li.comment{background:var(--paper);border:1px solid var(--border-soft);border-radius:var(--radius-lg);padding:20px 22px;box-shadow:var(--shadow-xs)}
    .nep-woo-reviews .comment_container{display:grid;grid-template-columns:auto 1fr;gap:16px;align-items:start}
    .nep-woo-reviews .comment_container img.avatar{width:48px;height:48px;border-radius:50%;margin:0;float:none;background:var(--beige)}
    .nep-woo-reviews .comment-text{margin:0;border:none;padding:0}
    .nep-woo-reviews .comment-text .star-rating{margin:0 0 6px;color:var(--accent-gold);font-size:.95em}
    .nep-woo-reviews .comment-text p.meta{font-size:var(--text-sm);color:var(--text-muted);margin:0 0 8px}
    .nep-woo-reviews .comment-text p.meta strong.woocommerce-review__author{color:var(--text-strong);font-weight:700;font-size:var(--text-base)}
    .nep-woo-reviews .comment-text .description p{margin:0;line-height:1.7;color:var(--text-body)}
    /* Form gửi đánh giá */
    .nep-woo-reviews #review_form_wrapper{background:var(--cream);border:1px solid var(--border-soft);border-radius:var(--radius-xl);padding:26px}
    .nep-woo-reviews .comment-notes,.nep-woo-reviews .email-notes{font-size:var(--text-sm);color:var(--text-muted);margin:0 0 16px}
    .nep-woo-reviews .comment-form{display:flex;flex-direction:column;gap:16px}
    .nep-woo-reviews .comment-form>p{margin:0}
    .nep-woo-reviews .comment-form-author,.nep-woo-reviews .comment-form-email{display:inline-block;width:calc(50% - 8px)}
    .nep-woo-reviews .comment-form-author{margin-right:12px}
    .nep-woo-reviews .comment-form label{display:block;font-size:var(--text-sm);font-weight:600;color:var(--text-strong);margin-bottom:6px}
    .nep-woo-reviews .comment-form input[type=text],.nep-woo-reviews .comment-form input[type=email],.nep-woo-reviews .comment-form textarea{width:100%;font-family:var(--font-sans);font-size:var(--text-base);color:var(--text-strong);background:var(--paper);border:1px solid var(--border-strong);border-radius:var(--radius-md);padding:12px 14px;outline:none;transition:border-color .2s,box-shadow .2s}
    .nep-woo-reviews .comment-form input:focus,.nep-woo-reviews .comment-form textarea:focus{border-color:var(--brand);box-shadow:0 0 0 3px var(--olive-50)}
    .nep-woo-reviews .comment-form-rating{margin:0}
    .nep-woo-reviews .comment-form-rating label{display:block;font-weight:600;color:var(--text-strong);margin-bottom:8px}
    .nep-woo-reviews p.stars{margin:0;display:inline-flex;gap:2px}
    .nep-woo-reviews p.stars a{color:var(--accent-gold)!important;font-size:1.25em}
    .nep-woo-reviews p.stars a:hover{color:var(--champagne)}
    .nep-woo-reviews .comment-form-cookies-consent{display:flex!important;align-items:center;gap:8px;font-size:var(--text-sm);color:var(--text-muted)}
    .nep-woo-reviews .comment-form-cookies-consent input{margin:0}
    .nep-woo-reviews .form-submit{margin:4px 0 0}
    .nep-woo-reviews .form-submit input.submit{appearance:none;cursor:pointer;border:none;background:var(--brand);color:var(--text-on-olive);font-family:var(--font-sans);font-weight:700;font-size:var(--text-base);padding:13px 30px;border-radius:var(--radius-pill);transition:background .2s,transform .15s,box-shadow .2s}
    .nep-woo-reviews .form-submit input.submit:hover{background:var(--olive-700);transform:translateY(-1px);box-shadow:var(--shadow-sm)}
  </style>
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

        <div class="nep-contact-grid" style="display:grid;grid-template-columns:minmax(0,1fr) minmax(0,1fr);gap:var(--space-10);align-items:start">
        {{-- Gallery --}}
        @php $thumb_ids = array_values(array_filter(array_merge([get_post_thumbnail_id($id)], $gallery_ids))); @endphp
        <div>
          {{-- Ảnh chính: bấm để phóng to --}}
          <button type="button" class="nep-pdp-stage" aria-label="Phóng to ảnh">
            <img id="nep-pdp-main" src="{{ $main }}" alt="{{ get_the_title() }}" fetchpriority="high" decoding="async">
            <span class="nep-pdp-zoom" aria-hidden="true">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/><path d="M11 8v6"/><path d="M8 11h6"/></svg>
            </span>
          </button>

          @if(count($thumb_ids) > 1)
            <div class="nep-pdp-slider">
              <button type="button" class="nep-pdp-nav nep-pdp-nav--prev" aria-label="Ảnh trước">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
              </button>
              <div class="nep-pdp-track" id="nep-pdp-track">
                @foreach($thumb_ids as $i => $gid)
                  @php $thumb = wp_get_attachment_image_url($gid, 'nep_card'); $full = wp_get_attachment_image_url($gid, 'large'); @endphp
                  @if($thumb)
                    <button type="button" class="nep-pdp-thumb {{ $i === 0 ? 'is-active' : '' }}" data-full="{{ $full }}">
                      <img src="{{ $thumb }}" alt="{{ get_the_title() }}" loading="lazy" decoding="async">
                    </button>
                  @endif
                @endforeach
              </div>
              <button type="button" class="nep-pdp-nav nep-pdp-nav--next" aria-label="Ảnh sau">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
              </button>
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

          <div style="font-size:var(--text-lg);line-height:1.7;color:var(--text-body);margin-bottom:28px">{{ get_the_excerpt() }}</div>

          <dl style="display:grid;grid-template-columns:auto 1fr;gap:10px 24px;font-size:var(--text-base);margin-bottom:30px">
            @foreach([['Chất liệu','material'],['Màu sắc','color']] as $row)
              @if($v = nep_field($row[1], $id))
                <dt style="color:var(--text-muted)">{{ $row[0] }}</dt><dd style="font-weight:600;margin:0">{{ $v }}</dd>
              @endif
            @endforeach
          </dl>

          <div style="display:flex;gap:12px;flex-wrap:wrap">
            <x-button href="{{ nep_quote_url($id) }}" size="lg" variant="primary" data-quote-open>Yêu cầu báo giá <x-icon name="arrow-right" :size="18" /></x-button>
            <x-button href="{{ App\nep_tel(App\nep('hotline')) }}" size="lg" variant="secondary"><x-icon name="phone" :size="18" /> Gọi tư vấn</x-button>
          </div>
        </div>
        </div>
      </x-container>
    </section>

    {{-- Lightbox phóng to ảnh --}}
    <div class="nep-lightbox" id="nep-lightbox" hidden aria-hidden="true">
      <button type="button" class="nep-lightbox__btn nep-lightbox__close" aria-label="Đóng">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
      </button>
      <button type="button" class="nep-lightbox__btn nep-lightbox__prev" aria-label="Ảnh trước">
        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
      </button>
      <img class="nep-lightbox__img" id="nep-lightbox-img" src="" alt="{{ get_the_title() }}">
      <button type="button" class="nep-lightbox__btn nep-lightbox__next" aria-label="Ảnh sau">
        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
      </button>
    </div>

    {{-- Popup "Yêu cầu báo giá" --}}
    <div class="nep-modal" id="nep-quote-modal" hidden aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="nep-quote-title">
      <div class="nep-modal__panel">
        <button type="button" class="nep-modal__close" aria-label="Đóng">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
        </button>
        <span class="nep-modal__eyebrow">Nhận báo giá</span>
        <h3 class="nep-modal__title" id="nep-quote-title">Yêu cầu báo giá</h3>
        <p class="nep-modal__sub">Sản phẩm: <strong>{{ get_the_title() }}</strong>. Để lại thông tin, NẾP sẽ liên hệ báo giá theo kích thước của bạn.</p>

        <form class="nep-modal__form" id="nep-quote-form">
          <input type="hidden" name="action" value="nep_quote">
          <input type="hidden" name="nonce" value="{{ wp_create_nonce('nep_quote') }}">
          <input type="hidden" name="product_id" value="{{ $id }}">
          <input type="hidden" name="san_pham" value="{{ get_the_title() }}">
          <div class="nep-modal__row">
            <label>Họ và tên *<input type="text" name="ho_ten" required placeholder="Nguyễn Văn A"></label>
            <label>Số điện thoại *<input type="tel" name="sdt" required placeholder="09xx xxx xxx"></label>
          </div>
          <label>Email<input type="email" name="email" placeholder="email@cua-ban.vn"></label>
          <label>Nội dung<textarea name="noi_dung" rows="3" placeholder="Kích thước, không gian, số lượng cửa…"></textarea></label>
          <p class="nep-modal__msg" id="nep-quote-msg" hidden></p>
          <button type="submit" class="nep-btn nep-btn--primary nep-btn--lg nep-btn--full" id="nep-quote-submit">Gửi yêu cầu</button>
        </form>
      </div>
    </div>

    {{-- ===== Tabs dùng chung: Mô tả · Thông số · Hướng dẫn · Đánh giá ===== --}}
    @php
      // Thông số: gộp Chất liệu/Màu (tab Thuộc tính) + bảng thông số (repeater).
      $spec_rows = [];
      if ($v = nep_field('material', $id)) { $spec_rows[] = ['Chất liệu', $v]; }
      if ($v = nep_field('color', $id))    { $spec_rows[] = ['Màu sắc', $v]; }
      foreach ((array) nep_field('specs', $id, []) as $r) {
        if (!empty($r['k'])) { $spec_rows[] = [$r['k'], $r['v'] ?? '']; }
      }
      $guide = nep_field('guide', $id);
      $review_count = (int) get_comments_number($id);
    @endphp
    <section style="padding-top:var(--section-y);padding-bottom:var(--section-y);background:var(--paper)">
      <x-container>
        <div class="nep-tabs" role="tablist" aria-label="Thông tin sản phẩm">
          <button class="nep-tab is-active" type="button" role="tab" aria-selected="true" data-tab="mota">Mô tả</button>
          <button class="nep-tab" type="button" role="tab" aria-selected="false" data-tab="thongso">Thông số</button>
          @if($guide)<button class="nep-tab" type="button" role="tab" aria-selected="false" data-tab="huongdan">Hướng dẫn</button>@endif
          <button class="nep-tab" type="button" role="tab" aria-selected="false" data-tab="danhgia">Đánh giá @if($review_count)<span class="nep-tab__count">{{ $review_count }}</span>@endif</button>
        </div>

        {{-- Mô tả --}}
        <div class="nep-tab-panel is-active" data-panel="mota">
          <div class="nep-desc__body nep-rte">
            @php the_content(); @endphp
          </div>
        </div>

        {{-- Thông số --}}
        <div class="nep-tab-panel" data-panel="thongso">
          @if($spec_rows)
            <dl class="nep-specs">
              @foreach($spec_rows as $row)
                <div class="nep-specs__item">
                  <dt>{{ $row[0] }}</dt>
                  <dd>{{ $row[1] }}</dd>
                </div>
              @endforeach
            </dl>
          @else
            <p class="nep-empty">Đang cập nhật thông số sản phẩm.</p>
          @endif
        </div>

        {{-- Hướng dẫn --}}
        @if($guide)
          <div class="nep-tab-panel" data-panel="huongdan">
            <div class="nep-rte">{!! $guide !!}</div>
          </div>
        @endif

        {{-- Đánh giá: dùng hệ thống review gốc của WooCommerce (khách gửi, admin duyệt) --}}
        <div class="nep-tab-panel" data-panel="danhgia">
          <div class="nep-rte nep-woo-reviews">
            @php comments_template(); @endphp
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

  {{-- Gallery: đổi ảnh chính · slider thumbnail · lightbox phóng to --}}
  <script>
    (function () {
      var main   = document.getElementById('nep-pdp-main');
      var thumbs = Array.prototype.slice.call(document.querySelectorAll('.nep-pdp-thumb'));
      var track  = document.getElementById('nep-pdp-track');
      var current = 0;

      function imgs() { return thumbs.map(function (t) { return t.dataset.full; }); }

      function setActive(i) {
        if (!thumbs.length) return;
        current = (i + thumbs.length) % thumbs.length;
        thumbs.forEach(function (t, k) { t.classList.toggle('is-active', k === current); });
        var full = thumbs[current].dataset.full;
        if (main && full) main.src = full;
        thumbs[current].scrollIntoView({ block: 'nearest', inline: 'nearest' });
      }

      thumbs.forEach(function (b, i) {
        b.addEventListener('click', function () { setActive(i); });
      });

      // Nút trái/phải của slider thumbnail + cập nhật trạng thái bật/tắt
      var prev = document.querySelector('.nep-pdp-nav--prev');
      var next = document.querySelector('.nep-pdp-nav--next');
      function syncNav() {
        if (!track) return;
        var max = track.scrollWidth - track.clientWidth - 1;
        if (prev) prev.disabled = track.scrollLeft <= 0;
        if (next) next.disabled = track.scrollLeft >= max;
      }
      if (prev) prev.addEventListener('click', function () { if (track) track.scrollBy({ left: -track.clientWidth * 0.8, behavior: 'smooth' }); });
      if (next) next.addEventListener('click', function () { if (track) track.scrollBy({ left: track.clientWidth * 0.8, behavior: 'smooth' }); });

      if (track) {
        track.addEventListener('scroll', syncNav, { passive: true });
        window.addEventListener('resize', syncNav);
        syncNav();

        // Cuộn ngang bằng wheel (lăn chuột dọc → trượt ngang)
        track.addEventListener('wheel', function (e) {
          if (Math.abs(e.deltaY) > Math.abs(e.deltaX)) { track.scrollLeft += e.deltaY; e.preventDefault(); }
        }, { passive: false });

        // Kéo-thả bằng chuột để trượt (drag-to-scroll)
        var down = false, startX = 0, startLeft = 0, moved = false;
        track.addEventListener('pointerdown', function (e) {
          down = true; moved = false; startX = e.clientX; startLeft = track.scrollLeft;
          track.style.cursor = 'grabbing';
        });
        window.addEventListener('pointermove', function (e) {
          if (!down) return;
          var dx = e.clientX - startX;
          if (Math.abs(dx) > 4) moved = true;
          track.scrollLeft = startLeft - dx;
        });
        window.addEventListener('pointerup', function () {
          down = false; track.style.cursor = '';
        });
        // Sau khi kéo thì chặn click chọn ảnh (tránh nhảy ảnh ngoài ý muốn)
        track.addEventListener('click', function (e) {
          if (moved) { e.stopPropagation(); e.preventDefault(); moved = false; }
        }, true);
      }

      // ---- Lightbox ----
      var lb     = document.getElementById('nep-lightbox');
      var lbImg  = document.getElementById('nep-lightbox-img');
      var stage  = document.querySelector('.nep-pdp-stage');
      var lbIndex = 0;

      function openLightbox(i) {
        if (!lb || !lbImg) return;
        lbIndex = i;
        lbImg.src = (thumbs.length ? imgs()[i] : (main ? main.src : ''));
        lbImg.classList.remove('is-zoomed');
        lbImg.style.transform = '';
        lb.hidden = false;
        lb.setAttribute('aria-hidden', 'false');
        requestAnimationFrame(function () { lb.classList.add('is-open'); });
        document.body.style.overflow = 'hidden';
      }
      function closeLightbox() {
        if (!lb) return;
        lb.classList.remove('is-open');
        lb.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
        setTimeout(function () { lb.hidden = true; }, 220);
      }
      function lbGo(d) {
        if (!thumbs.length) return;
        lbIndex = (lbIndex + d + thumbs.length) % thumbs.length;
        lbImg.classList.remove('is-zoomed');
        lbImg.style.transform = '';
        lbImg.src = imgs()[lbIndex];
        setActive(lbIndex);
      }

      if (stage) stage.addEventListener('click', function () { openLightbox(current); });
      if (lb) {
        lb.querySelector('.nep-lightbox__close').addEventListener('click', closeLightbox);
        lb.querySelector('.nep-lightbox__prev').addEventListener('click', function (e) { e.stopPropagation(); lbGo(-1); });
        lb.querySelector('.nep-lightbox__next').addEventListener('click', function (e) { e.stopPropagation(); lbGo(1); });
        lb.addEventListener('click', function (e) { if (e.target === lb) closeLightbox(); });

        // Click ảnh = bật/tắt zoom; rê chuột để pan khi đã zoom
        lbImg.addEventListener('click', function (e) {
          e.stopPropagation();
          lbImg.classList.toggle('is-zoomed');
          if (!lbImg.classList.contains('is-zoomed')) lbImg.style.transform = '';
        });
        lbImg.addEventListener('mousemove', function (e) {
          if (!lbImg.classList.contains('is-zoomed')) return;
          var r = lbImg.getBoundingClientRect();
          var x = ((e.clientX - r.left) / r.width) * 100;
          var y = ((e.clientY - r.top) / r.height) * 100;
          lbImg.style.transformOrigin = x + '% ' + y + '%';
          lbImg.style.transform = 'scale(2.2)';
        });

        // Bàn phím: Esc đóng, ← → chuyển ảnh
        document.addEventListener('keydown', function (e) {
          if (lb.hidden) return;
          if (e.key === 'Escape') closeLightbox();
          else if (e.key === 'ArrowLeft') lbGo(-1);
          else if (e.key === 'ArrowRight') lbGo(1);
        });

        // Hide nút điều hướng nếu chỉ có 1 ảnh
        if (thumbs.length <= 1) {
          lb.querySelector('.nep-lightbox__prev').style.display = 'none';
          lb.querySelector('.nep-lightbox__next').style.display = 'none';
        }
      }
    })();

    {{-- Popup Yêu cầu báo giá --}}
    (function () {
      var modal = document.getElementById('nep-quote-modal');
      if (!modal) return;
      var form = document.getElementById('nep-quote-form');
      var msg = document.getElementById('nep-quote-msg');
      var submit = document.getElementById('nep-quote-submit');
      var ajaxUrl = '{{ admin_url('admin-ajax.php') }}';
      var lastFocus = null;

      function openModal(e) {
        if (e) e.preventDefault();
        lastFocus = document.activeElement;
        modal.hidden = false;
        modal.setAttribute('aria-hidden', 'false');
        requestAnimationFrame(function () { modal.classList.add('is-open'); });
        document.body.style.overflow = 'hidden';
        var f = form.querySelector('input[name="ho_ten"]'); if (f) f.focus();
      }
      function closeModal() {
        modal.classList.remove('is-open');
        modal.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
        setTimeout(function () { modal.hidden = true; }, 220);
        if (lastFocus && lastFocus.focus) lastFocus.focus();
      }

      document.querySelectorAll('[data-quote-open]').forEach(function (b) {
        b.addEventListener('click', openModal);
      });
      modal.querySelector('.nep-modal__close').addEventListener('click', closeModal);
      modal.addEventListener('click', function (e) { if (e.target === modal) closeModal(); });
      document.addEventListener('keydown', function (e) { if (!modal.hidden && e.key === 'Escape') closeModal(); });

      form.addEventListener('submit', function (e) {
        e.preventDefault();
        msg.hidden = true; msg.className = 'nep-modal__msg';
        var label = submit.textContent;
        submit.disabled = true; submit.textContent = 'Đang gửi…';
        fetch(ajaxUrl, { method: 'POST', body: new FormData(form), credentials: 'same-origin' })
          .then(function (r) { return r.json(); })
          .then(function (res) {
            msg.hidden = false;
            if (res && res.success) {
              msg.classList.add('is-success');
              msg.textContent = (res.data && res.data.message) || 'Đã gửi thành công!';
              form.reset();
            } else {
              msg.classList.add('is-error');
              msg.textContent = (res && res.data && res.data.message) || 'Có lỗi xảy ra, vui lòng thử lại.';
            }
          })
          .catch(function () {
            msg.hidden = false; msg.classList.add('is-error');
            msg.textContent = 'Không kết nối được máy chủ, vui lòng thử lại.';
          })
          .finally(function () { submit.disabled = false; submit.textContent = label; });
      });
    })();

    {{-- Chuyển tab Mô tả / Thông số / Hướng dẫn / Đánh giá --}}
    (function () {
      var tabs = document.querySelectorAll('.nep-tab');
      var panels = document.querySelectorAll('.nep-tab-panel');
      tabs.forEach(function (t) {
        t.addEventListener('click', function () {
          var name = t.dataset.tab;
          tabs.forEach(function (x) { var on = x === t; x.classList.toggle('is-active', on); x.setAttribute('aria-selected', on ? 'true' : 'false'); });
          panels.forEach(function (p) { p.classList.toggle('is-active', p.dataset.panel === name); });
        });
      });
      {{-- Mở thẳng tab Đánh giá khi tới từ link #reviews / #tab-danhgia --}}
      if (/danhgia|reviews|comment/i.test(location.hash)) {
        var rt = document.querySelector('.nep-tab[data-tab="danhgia"]');
        if (rt) rt.click();
      }
    })();
  </script>
@endsection
