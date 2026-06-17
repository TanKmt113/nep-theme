{{--
  Template Name: Bộ sưu tập (Catalogue / Lookbook)
--}}
@extends('layouts.app')

@php
  use function App\nep_field;
  use function App\page_field;
  use function App\page_rows;
  $hero = page_field('look_hero_image', 'https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?w=1800&q=80');
  $hero_accent = page_field('look_hero_accent', 'rèm cửa');
  $large = [
    'name' => page_field('look_large_name', 'Bộ sưu tập Linen'),
    'tagline' => page_field('look_large_tagline', 'Mộc mạc & tự nhiên'),
    'count' => page_field('look_large_count', 24),
    'img' => page_field('look_large_img', 'https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?w=1100&q=80'),
  ];
  $smalls = page_rows('look_smalls', [
    ['name' => 'Velvet Noir', 'tagline' => 'Nhung sang trọng', 'count' => 12, 'img' => 'https://images.unsplash.com/photo-1604014237800-1c9102c219da?w=900&q=80'],
    ['name' => 'Nan gỗ tự nhiên', 'tagline' => 'Ấm áp & bền bỉ', 'count' => 18, 'img' => 'https://images.unsplash.com/photo-1540574163026-643ea20ade25?w=900&q=80'],
  ], fn ($r) => ['name' => $r['name'] ?? '', 'tagline' => $r['tagline'] ?? '', 'count' => $r['count'] ?? '', 'img' => $r['image'] ?? '']);
  $pickedCats = page_field('look_categories', []);
  $catArgs = ['taxonomy' => 'product_cat', 'hide_empty' => false];
  if (! empty($pickedCats) && is_array($pickedCats)) {
    $catArgs['include'] = array_map('intval', $pickedCats);
    $catArgs['orderby'] = 'include';
  }
  $cats = get_terms($catArgs);
  if (is_wp_error($cats)) $cats = [];
  $featIds = function_exists('wc_get_featured_product_ids') ? wc_get_featured_product_ids() : [];
  $featured = ! empty($featIds)
    ? get_posts(['post_type' => 'product', 'post__in' => $featIds, 'orderby' => 'post__in', 'numberposts' => 4, 'post_status' => 'publish'])
    : get_posts(['post_type' => 'product', 'numberposts' => 4, 'post_status' => 'publish']);
  $listing = App\nep_shop_url();
@endphp

@section('content')
  {{-- Hero --}}
  <section style="position:relative;min-height:64vh;display:flex;align-items:flex-end;overflow:hidden">
    <img src="{{ $hero }}" alt="{{ page_field('look_hero_title', 'Bộ sưu tập') }}" fetchpriority="high" decoding="async" style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover">
    <div style="position:absolute;inset:0;background:linear-gradient(180deg,rgba(20,22,14,.30) 0%,rgba(20,22,14,.12) 45%,rgba(20,22,14,.72) 100%)"></div>
    <x-container :style="'position:relative;padding-bottom:var(--space-9);padding-top:150px'">
      <x-eyebrow rule color="var(--moss)">{{ page_field('look_hero_eyebrow', 'Catalogue 2026') }}</x-eyebrow>
      <h1 style="color:#fff;font-size:var(--text-display-xl);line-height:1.05;margin:16px 0 0;max-width:16ch;text-wrap:balance">
        {{ page_field('look_hero_title', 'Bộ sưu tập') }}@if($hero_accent) <span style="font-style:italic;color:var(--moss-soft)">{{ $hero_accent }}</span>@endif
      </h1>
      <p style="color:rgba(255,255,255,.88);font-size:var(--text-lg);line-height:1.6;max-width:50ch;margin-top:18px">
        {{ page_field('look_hero_desc', 'Từ linen mộc mạc đến nhung sang trọng — khám phá những bộ sưu tập được tuyển chọn cho mọi phong cách không gian.') }}
      </p>
    </x-container>
  </section>

  @if(App\page_show('look_show_lookbook'))
  {{-- Lookbook — full-bleed horizontal slider --}}
  @php
    $slides = array_merge([$large], $smalls);
  @endphp
  <section class="nep-lb" style="padding-top:var(--section-y);padding-bottom:var(--section-y);background:var(--cream);overflow:hidden">
    <x-container>
      <div style="display:flex;align-items:flex-end;justify-content:space-between;gap:24px;margin-bottom:var(--space-7)">
        <div>
          <x-eyebrow rule>{{ page_field('look_eyebrow', 'Lookbook') }}</x-eyebrow>
          <h2 style="font-size:var(--text-display-md);margin-top:12px">{{ page_field('look_heading', 'Bộ sưu tập nổi bật') }}</h2>
        </div>
        <div class="nep-lb__nav" aria-hidden="true">
          <button type="button" class="nep-lb__arrow" data-dir="-1" aria-label="Ảnh trước"><x-icon name="arrow-left" :size="20" /></button>
          <button type="button" class="nep-lb__arrow" data-dir="1" aria-label="Ảnh tiếp theo"><x-icon name="arrow-right" :size="20" /></button>
        </div>
      </div>
    </x-container>

    <div class="nep-lb__viewport" style="--lb-peek:clamp(16px,7vw,140px)">
      <div class="nep-lb__track" role="list">
        @foreach($slides as $i => $l)
          <a href="{{ $listing }}" class="nep-lb__slide" role="listitem" style="--i:{{ $i }}">
            <img src="{{ $l['img'] }}" alt="{{ $l['name'] }}" loading="{{ $i === 0 ? 'eager' : 'lazy' }}" decoding="async" class="nep-lb__img">
            <span class="nep-lb__shade"></span>
            <span class="nep-lb__cap">
              @if(!empty($l['count']))<span class="nep-lb__count">{{ $l['count'] }} mẫu</span>@endif
              <span class="nep-lb__name">{{ $l['name'] }}</span>
              @if(!empty($l['tagline']))<span class="nep-lb__tag">{{ $l['tagline'] }}</span>@endif
              <span class="nep-lb__cta">Xem bộ sưu tập <x-icon name="arrow-right" :size="16" /></span>
            </span>
          </a>
        @endforeach
      </div>
    </div>

    <x-container>
      <div class="nep-lb__dots" role="tablist" aria-label="Chọn ảnh">
        @foreach($slides as $i => $l)
          <button type="button" class="nep-lb__dot{{ $i === 0 ? ' is-active' : '' }}" data-i="{{ $i }}" aria-label="Ảnh {{ $i + 1 }}"></button>
        @endforeach
      </div>
    </x-container>
  </section>

  <style>
    .nep-lb__nav{display:flex;gap:12px}
    .nep-lb__arrow{display:inline-flex;align-items:center;justify-content:center;width:50px;height:50px;border-radius:999px;border:1px solid var(--border-soft);background:#fff;color:var(--text-strong);cursor:pointer;transition:background .2s,color .2s,transform .2s,opacity .2s}
    .nep-lb__arrow:hover{background:var(--olive-900);color:#fff;transform:translateY(-2px)}
    .nep-lb__arrow:disabled{opacity:.35;cursor:default;transform:none}
    .nep-lb__viewport{margin-top:8px}
    .nep-lb__track{display:flex;gap:clamp(14px,1.6vw,26px);overflow-x:auto;scroll-snap-type:x mandatory;scroll-behavior:smooth;-webkit-overflow-scrolling:touch;scrollbar-width:none;padding:6px var(--lb-peek);scroll-padding-left:var(--lb-peek)}
    .nep-lb__track::-webkit-scrollbar{display:none}
    .nep-lb__slide{position:relative;flex:0 0 calc(100% - var(--lb-peek)*2);scroll-snap-align:center;display:block;border-radius:var(--radius-2xl);overflow:hidden;aspect-ratio:21/9;text-decoration:none;background:var(--moss-faint);box-shadow:0 30px 60px -30px rgba(20,22,14,.5)}
    .nep-lb__img{position:absolute;inset:0;width:100%;height:100%;object-fit:cover;transition:transform 1.1s cubic-bezier(.2,.7,.2,1)}
    .nep-lb__slide:hover .nep-lb__img{transform:scale(1.05)}
    .nep-lb__shade{position:absolute;inset:0;background:linear-gradient(180deg,rgba(20,22,14,0) 38%,rgba(20,22,14,.30) 64%,rgba(20,22,14,.82) 100%)}
    .nep-lb__cap{position:absolute;left:clamp(22px,4vw,56px);right:clamp(22px,4vw,56px);bottom:clamp(22px,4vw,48px);display:flex;flex-direction:column;color:#fff}
    .nep-lb__count{font-size:var(--text-2xs);font-weight:600;letter-spacing:var(--tracking-eyebrow);text-transform:uppercase;color:var(--moss-soft);margin-bottom:10px}
    .nep-lb__name{font-family:var(--font-display);font-size:var(--text-display-md);font-weight:600;line-height:1.04}
    .nep-lb__tag{font-size:var(--text-base);opacity:.88;margin-top:6px}
    .nep-lb__cta{display:inline-flex;align-items:center;gap:8px;width:fit-content;margin-top:18px;font-size:var(--text-sm);font-weight:600;color:#fff;opacity:0;transform:translateY(8px);transition:opacity .35s,transform .35s}
    .nep-lb__slide:hover .nep-lb__cta,.nep-lb__slide:focus-visible .nep-lb__cta{opacity:1;transform:none}
    .nep-lb__dots{display:flex;justify-content:center;gap:10px;margin-top:var(--space-6)}
    .nep-lb__dot{width:9px;height:9px;padding:0;border:0;border-radius:999px;background:var(--moss);opacity:.4;cursor:pointer;transition:width .3s,opacity .3s,background .3s}
    .nep-lb__dot.is-active{width:30px;opacity:1;background:var(--olive-700)}
    @media (max-width:860px){
      .nep-lb__nav{display:none}
      .nep-lb__viewport{--lb-peek:16px}
      .nep-lb__slide{flex:0 0 calc(100% - 28px);aspect-ratio:4/5}
      .nep-lb__name{font-size:var(--text-h1)}
    }
    @media (prefers-reduced-motion:reduce){.nep-lb__img,.nep-lb__cta,.nep-lb__track{transition:none;scroll-behavior:auto}}
  </style>

  <script>
  (function(){
    var root=document.querySelector('.nep-lb');if(!root)return;
    var track=root.querySelector('.nep-lb__track'),
        slides=root.querySelectorAll('.nep-lb__slide'),
        dots=root.querySelectorAll('.nep-lb__dot'),
        arrows=root.querySelectorAll('.nep-lb__arrow');
    if(!track||!slides.length)return;
    var cur=0,
        reduce=window.matchMedia&&window.matchMedia('(prefers-reduced-motion:reduce)').matches;
    function go(i){i=(i+slides.length)%slides.length;var s=slides[i];track.scrollTo({left:s.offsetLeft-(track.clientWidth-s.clientWidth)/2,behavior:reduce?'auto':'smooth'});}
    function sync(){
      var c=track.scrollLeft+track.clientWidth/2,best=0,min=Infinity;
      slides.forEach(function(s,i){var d=Math.abs(s.offsetLeft+s.clientWidth/2-c);if(d<min){min=d;best=i;}});
      cur=best;
      dots.forEach(function(d,i){d.classList.toggle('is-active',i===best);});
    }
    arrows.forEach(function(a){a.addEventListener('click',function(){stop();go(cur+ +a.dataset.dir);});});
    dots.forEach(function(d){d.addEventListener('click',function(){stop();go(+d.dataset.i);});});
    var t;track.addEventListener('scroll',function(){clearTimeout(t);t=setTimeout(sync,80);},{passive:true});

    // Auto-trượt (tạm dừng khi hover/chạm hoặc tab ẩn)
    var timer=null,DELAY=4500;
    function tick(){go(cur+1);}
    function start(){if(!timer&&!reduce&&slides.length>1)timer=setInterval(tick,DELAY);}
    function stop(){if(timer){clearInterval(timer);timer=null;}}
    root.addEventListener('mouseenter',stop);
    root.addEventListener('mouseleave',start);
    track.addEventListener('touchstart',stop,{passive:true});
    document.addEventListener('visibilitychange',function(){document.hidden?stop():start();});
    sync();

    // Khi cuộn tới section lần đầu: trượt 1 phát rồi mới bật auto-trượt
    var revealed=false;
    function reveal(){if(revealed)return;revealed=true;if(!reduce)setTimeout(function(){go(cur+1);},520);start();}
    if('IntersectionObserver'in window){
      var io=new IntersectionObserver(function(es){es.forEach(function(e){if(e.isIntersecting){reveal();io.disconnect();}});},{threshold:.35});
      io.observe(root);
    }else{reveal();}
  })();
  </script>

  @endif

  @if(App\page_show('look_show_cat'))
  {{-- Categories --}}
  <section style="padding-top:var(--section-y);padding-bottom:var(--section-y);background:var(--paper)">
    <x-container>
      <div style="display:flex;align-items:flex-end;justify-content:space-between;margin-bottom:var(--space-8)">
        <div><x-eyebrow rule>{{ page_field('look_cat_eyebrow', 'Danh mục') }}</x-eyebrow><h2 style="font-size:var(--text-display-md);margin-top:12px">{{ page_field('look_cat_heading', 'Tất cả loại rèm') }}</h2></div>
        <x-button href="{{ $listing }}" variant="ghost">Xem dạng lưới <x-icon name="arrow-right" :size="16" /></x-button>
      </div>
      <div class="nep-grid-4" style="display:grid;grid-template-columns:repeat(4,1fr);gap:var(--space-5)">
        @foreach($cats as $c)
          @php $img = function_exists('get_field') ? get_field('img', $c) : ''; $icon = function_exists('get_field') ? (get_field('icon', $c) ?: 'blinds') : 'blinds'; @endphp
          <a href="{{ get_term_link($c) }}" style="display:block;background:var(--cream);border-radius:var(--radius-lg);border:1px solid var(--border-soft);overflow:hidden;text-decoration:none">
            <div style="aspect-ratio:16/11;overflow:hidden"><img src="{{ $img }}" alt="{{ $c->name }}" loading="lazy" decoding="async" style="width:100%;height:100%;object-fit:cover"></div>
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

  @endif

  @if(App\page_show('look_show_featured'))
  {{-- Featured --}}
  <section style="padding-top:var(--section-y);padding-bottom:var(--section-y);background:var(--beige)">
    <x-container>
      <div style="display:flex;align-items:flex-end;justify-content:space-between;margin-bottom:var(--space-7)">
        <div><x-eyebrow rule>{{ page_field('look_feat_eyebrow', 'Tuyển chọn') }}</x-eyebrow><h2 style="font-size:var(--text-display-md);margin-top:12px">{{ page_field('look_feat_heading', 'Sản phẩm tiêu biểu') }}</h2></div>
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
  @endif
@endsection
