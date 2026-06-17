@php
  use function App\nep;
  use function App\nep_tel;
  $transparent = is_front_page();
  $hotline = nep('hotline');
  $logo = nep('logo') ?: get_theme_file_uri('public/images/logo.svg');
  $logoLight = nep('logo_light') ?: get_theme_file_uri('public/images/logo-light.svg');
@endphp

{{-- NẾP · Header — fixed, glass-on-scroll. State handled in app.js (.is-solid / .is-open). --}}
<header class="nep-header" data-transparent="{{ $transparent ? 'true' : 'false' }}">
  <x-container :style="'display:flex;align-items:center;justify-content:space-between;height:78px'">
    <a href="{{ home_url('/') }}" class="nep-header__logo" style="flex:1">
      <img src="{{ $logo }}" alt="NẾP" class="nep-logo-dark" style="height:38px">
      <img src="{{ $logoLight }}" alt="NẾP" class="nep-logo-light" style="height:38px">
    </a>

    {{-- Desktop nav --}}
    <nav class="nep-nav nep-desktop-only">
      @if(has_nav_menu('primary_navigation'))
        {!! wp_nav_menu(['theme_location' => 'primary_navigation', 'echo' => false, 'container' => false, 'menu_class' => 'nep-nav__list', 'fallback_cb' => false]) !!}
      @else
        <a href="{{ home_url('/') }}">Trang chủ</a>
        <a href="{{ home_url('/gioi-thieu') }}">Giới thiệu</a>
        <a href="{{ App\nep_shop_url() }}">Sản phẩm</a>
        <a href="{{ get_post_type_archive_link('du_an') }}">Dự án</a>
        <a href="{{ home_url('/xuong-theu') }}">Xưởng thêu</a>
        <a href="{{ home_url('/lien-he') }}">Liên hệ</a>
      @endif
    </nav>

    {{-- Desktop hotline + CTA --}}
    <div class="nep-desktop-only" style="display:flex;align-items:center;justify-content:flex-end;gap:16px;flex:1">
      <button class="nep-search-toggle" aria-label="Tìm kiếm" aria-expanded="false" type="button">
        <x-icon name="search" :size="20" />
      </button>
      <a href="{{ nep_tel($hotline) }}" class="nep-header__tel" style="display:flex;align-items:center;gap:8px;font-size:var(--text-sm);font-weight:700;white-space:nowrap">
        <x-icon name="phone" :size="16" color="var(--brand)" /> {{ $hotline }}
      </a>
      <x-button href="{{ home_url('/lien-he') }}" size="sm" variant="primary">Nhận tư vấn</x-button>
    </div>

    {{-- Mobile toggle --}}
    <button class="nep-burger nep-mobile-only" aria-label="Mở menu" aria-expanded="false" type="button">
      <span></span><span></span><span></span>
    </button>
  </x-container>

  {{-- Desktop search overlay (slide-down) --}}
  <div class="nep-search-panel" hidden>
    <x-container>
      <x-search-form :autofocus="true" />
    </x-container>
  </div>

  {{-- Mobile dropdown panel --}}
  <div class="nep-menu-panel" hidden>
    <x-search-form class="nep-menu-panel__search" />
    <nav class="nep-menu-panel__nav">
      @if(has_nav_menu('primary_navigation'))
        {!! wp_nav_menu(['theme_location' => 'primary_navigation', 'echo' => false, 'container' => false, 'menu_class' => 'nep-menu-panel__list', 'fallback_cb' => false]) !!}
      @endif
    </nav>
    <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;margin-top:var(--space-5)">
      <a href="{{ nep_tel($hotline) }}" style="display:flex;align-items:center;gap:8px;font-size:var(--text-sm);font-weight:700;color:var(--text-strong)">
        <x-icon name="phone" :size="16" color="var(--brand)" /> {{ $hotline }}
      </a>
      <x-button href="{{ home_url('/lien-he') }}" size="sm" variant="primary">Nhận tư vấn</x-button>
    </div>
  </div>
</header>
