@extends('layouts.app')

@php use function App\nep_field; use function App\nep_image; @endphp

@section('content')

  {{-- ===== Hero ===== --}}
  <section style="position:relative;min-height:92vh;display:flex;align-items:flex-end;overflow:hidden">
    {!! nep_image($home['hero_image'], $home['hero_title'], ['fetchpriority' => 'high', 'decoding' => 'async', 'sizes' => '100vw', 'style' => 'position:absolute;inset:0;width:100%;height:100%;object-fit:cover'], 'full') !!}
    <div style="position:absolute;inset:0;background:linear-gradient(180deg,rgba(20,22,14,.42) 0%,rgba(20,22,14,.12) 40%,rgba(20,22,14,.72) 100%)"></div>
    <x-container :style="'position:relative;padding-bottom:var(--space-11);padding-top:140px'">
      <div style="max-width:760px">
        @if($home['hero_eyebrow'])<x-eyebrow rule color="var(--moss)">{{ $home['hero_eyebrow'] }}</x-eyebrow>@endif
        <h1 style="color:#fff;font-size:var(--text-display-2xl);line-height:var(--leading-tight);margin:20px 0 0;text-wrap:balance">
          {{ $home['hero_title'] }}@if($home['hero_title_accent']) <span style="font-style:italic;color:var(--moss-soft)">{{ $home['hero_title_accent'] }}</span>@endif
        </h1>
        <p style="color:rgba(255,255,255,.86);font-size:var(--text-lg);line-height:1.6;max-width:52ch;margin-top:22px">
          {{ $home['hero_desc'] }}
        </p>
        <div style="display:flex;gap:14px;margin-top:34px;flex-wrap:wrap">
          @if($home['hero_btn1_text'])<x-button href="{{ $home['hero_btn1_url'] }}" size="lg" variant="gold">{{ $home['hero_btn1_text'] }} <x-icon name="arrow-right" :size="18" /></x-button>@endif
          @if($home['hero_btn2_text'])<x-button href="{{ $home['hero_btn2_url'] }}" size="lg" variant="secondary">{{ $home['hero_btn2_text'] }}</x-button>@endif
        </div>
        <div style="display:flex;gap:48px;margin-top:56px;color:#fff">
          @foreach($home['stats'] as $s)
            <div>
              <div style="font-family:var(--font-display);font-size:40px;font-weight:600;line-height:1">{{ $s['value'] }}</div>
              <div style="font-size:var(--text-sm);opacity:.8;margin-top:4px">{{ $s['label'] }}</div>
            </div>
          @endforeach
        </div>
      </div>
    </x-container>
  </section>

  {{-- ===== Intro / Về NẾP ===== --}}
  <section style="padding-top:var(--section-y);padding-bottom:var(--section-y);background:var(--cream)">
    <x-container :style="'display:grid;grid-template-columns:1fr 1fr;gap:var(--space-10);align-items:center'">
      <div style="position:relative">
        {!! nep_image($home['intro_image'], $home['intro_heading'], ['loading' => 'lazy', 'decoding' => 'async', 'sizes' => '(max-width: 980px) 100vw, 50vw', 'style' => 'width:100%;aspect-ratio:4/5;object-fit:cover;border-radius:var(--radius-xl);box-shadow:var(--shadow-lg)'], 'large') !!}
        @if($home['intro_badge_value'] || $home['intro_badge_label'])
        <div style="position:absolute;right:-24px;bottom:-24px;background:var(--olive-500);color:var(--text-on-olive);border-radius:var(--radius-lg);padding:22px 26px;box-shadow:var(--shadow-xl)">
          <div style="font-family:var(--font-display);font-size:40px;font-weight:600;line-height:1">{{ $home['intro_badge_value'] }}</div>
          <div style="font-size:var(--text-xs);opacity:.85;margin-top:4px">{{ $home['intro_badge_label'] }}</div>
        </div>
        @endif
      </div>
      <div>
        @if($home['intro_eyebrow'])<x-eyebrow rule>{{ $home['intro_eyebrow'] }}</x-eyebrow>@endif
        <h2 style="font-size:var(--text-display-lg);margin:16px 0 18px;max-width:16ch">{{ $home['intro_heading'] }}</h2>
        <p style="font-size:var(--text-lg);line-height:1.7;color:var(--text-body);max-width:48ch">
          {!! nl2br(e($home['intro_text'])) !!}
        </p>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:18px;margin-top:32px">
          @foreach($features as $f)
            <div style="display:flex;gap:14px;align-items:flex-start">
              <span style="display:inline-flex;align-items:center;justify-content:center;width:44px;height:44px;border-radius:var(--radius-md);background:var(--olive-50);flex:none">
                <x-icon :name="$f['icon']" :size="20" color="var(--brand)" />
              </span>
              <div>
                <div style="font-weight:700;color:var(--text-strong);font-size:var(--text-base)">{{ $f['title'] }}</div>
                <div style="font-size:var(--text-sm);color:var(--text-muted);line-height:1.5;margin-top:2px">{{ $f['desc'] }}</div>
              </div>
            </div>
          @endforeach
        </div>
      </div>
    </x-container>
  </section>

  {{-- ===== Categories / Danh mục ===== --}}
  <section style="padding-top:var(--section-y);padding-bottom:var(--section-y);background:var(--paper)">
    <x-container>
      <div style="display:flex;align-items:flex-end;justify-content:space-between;margin-bottom:var(--space-8)">
        <div>
          <x-eyebrow rule>{{ $home['cat_eyebrow'] }}</x-eyebrow>
          <h2 style="font-size:var(--text-display-md);margin-top:12px">{{ $home['cat_heading'] }}</h2>
        </div>
        <x-button href="{{ App\nep_shop_url() }}" variant="ghost">Xem tất cả <x-icon name="arrow-right" :size="16" /></x-button>
      </div>
      <div class="nep-cat-grid" style="display:grid;grid-template-columns:repeat(4,1fr);gap:var(--space-5)">
        @foreach($categories as $term)
          @include('sections.home.cat-card', ['term' => $term])
        @endforeach
      </div>
    </x-container>
  </section>

  {{-- ===== Featured / Nổi bật ===== --}}
  <section style="padding-top:var(--section-y);padding-bottom:var(--section-y);background:var(--beige)">
    <x-container>
      <div style="text-align:center;margin-bottom:var(--space-8)">
        <x-eyebrow rule center>{{ $home['featured_eyebrow'] }}</x-eyebrow>
        <h2 style="font-size:var(--text-display-md);margin-top:12px">{{ $home['featured_heading'] }}</h2>
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
    </x-container>
  </section>

  {{-- ===== Process / Quy trình ===== --}}
  <section id="process" style="padding-top:var(--section-y);padding-bottom:var(--section-y);background:var(--cream)">
    <x-container>
      <div style="text-align:center;margin-bottom:var(--space-9)">
        <x-eyebrow rule center>{{ $home['process_eyebrow'] }}</x-eyebrow>
        <h2 style="font-size:var(--text-display-md);margin-top:12px">{{ $home['process_heading'] }}</h2>
      </div>
      <div class="nep-grid-3" style="display:grid;grid-template-columns:repeat(3,1fr);gap:var(--space-6)">
        @foreach($process as $s)
          <div style="position:relative;padding:var(--space-6);background:var(--paper);border-radius:var(--radius-lg);border:1px solid var(--border-soft);box-shadow:var(--shadow-sm)">
            <span style="position:absolute;top:22px;right:24px;font-family:var(--font-display);font-size:52px;font-weight:600;color:var(--olive-100);line-height:1">{{ $s['n'] }}</span>
            <span style="display:inline-flex;align-items:center;justify-content:center;width:52px;height:52px;border-radius:var(--radius-md);background:var(--olive-500);margin-bottom:18px">
              <x-icon :name="$s['icon']" :size="24" color="#fff" />
            </span>
            <h3 style="font-size:var(--text-h3);font-weight:600;margin-bottom:8px">{{ $s['title'] }}</h3>
            <p style="font-size:var(--text-sm);color:var(--text-muted);line-height:1.6;margin:0;max-width:30ch">{{ $s['desc'] }}</p>
          </div>
        @endforeach
      </div>
    </x-container>
  </section>

  {{-- ===== Projects / Dự án ===== --}}
  <section id="projects" style="padding-top:var(--section-y);padding-bottom:var(--section-y);background:var(--paper)">
    <x-container>
      <div style="display:flex;align-items:flex-end;justify-content:space-between;gap:var(--space-5);margin-bottom:var(--space-8);flex-wrap:wrap">
        <div><x-eyebrow rule>{{ $home['projects_eyebrow'] }}</x-eyebrow><h2 style="font-size:var(--text-display-md);margin-top:12px">{{ $home['projects_heading'] }}</h2></div>
        @if($link = get_post_type_archive_link('du_an'))
          <x-button href="{{ $link }}" variant="ghost">Xem tất cả dự án <x-icon name="arrow-right" :size="16" /></x-button>
        @endif
      </div>
      @include('sections.project-slider', ['projects' => $projects, 'showType' => false])
    </x-container>
  </section>

  {{-- ===== Embroidery teaser ===== --}}
  <section style="padding-top:var(--section-y);padding-bottom:var(--section-y);background:var(--cream)">
    <x-container>
      <div class="nep-teaser" style="display:grid;grid-template-columns:1.1fr 1fr;border-radius:var(--radius-2xl);overflow:hidden;box-shadow:var(--shadow-lg);background:var(--paper)">
        <div style="padding:var(--space-10);display:flex;flex-direction:column;justify-content:center">
          <x-eyebrow rule>Xưởng thêu vi tính</x-eyebrow>
          <h2 style="font-size:var(--text-display-lg);margin:16px 0;max-width:14ch">Thêu logo &amp; đồng phục theo yêu cầu</h2>
          <p style="font-size:var(--text-lg);line-height:1.7;color:var(--text-body);max-width:42ch">
            Hệ thống máy thêu vi tính hiện đại, nhận gia công logo, đồng phục, quà tặng doanh nghiệp số lượng lớn — sắc nét và đúng tiến độ.
          </p>
          <div style="margin-top:30px">
            <x-button href="{{ home_url('/xuong-theu') }}" size="lg">Khám phá xưởng thêu <x-icon name="arrow-right" :size="18" /></x-button>
          </div>
        </div>
        <div style="min-height:420px">
          <img src="https://images.unsplash.com/photo-1620799139507-2a76f79a2f4d?w=1000&q=80" alt="Thêu vi tính" loading="lazy" decoding="async" style="width:100%;height:100%;object-fit:cover">
        </div>
      </div>
    </x-container>
  </section>

  @include('sections.final-cta')

@endsection
