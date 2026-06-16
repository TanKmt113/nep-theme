{{-- Reusable project slider (Swiper). Pass: $projects (array of WP_Post), $showType (bool). --}}
@php use function App\nep_field; $showType = $showType ?? false; @endphp

<div class="nep-proj-swiper swiper">
  <div class="swiper-wrapper">
    @foreach($projects as $p)
      <div class="swiper-slide">
        <a href="{{ get_permalink($p) }}" class="nep-proj-card" style="position:relative;display:block;height:100%;border-radius:var(--radius-lg);overflow:hidden">
          <img src="{{ get_the_post_thumbnail_url($p, 'nep_wide') ?: '' }}" alt="{{ get_the_title($p) }}" loading="lazy" style="width:100%;height:100%;object-fit:cover">
          <div class="nep-proj-card__overlay"></div>
          <div style="position:absolute;left:24px;right:24px;bottom:22px;color:#fff">
            @if($showType && ($type = nep_field('type', $p->ID)))
              <div style="font-size:var(--text-xs);text-transform:uppercase;letter-spacing:.08em;opacity:.85;margin-bottom:2px">{{ $type }}</div>
            @endif
            <div style="font-family:var(--font-display);font-size:var(--text-h2);font-weight:600">{{ get_the_title($p) }}</div>
            @if($place = nep_field('place', $p->ID))
              <div style="font-size:var(--text-sm);opacity:.85;display:flex;align-items:center;gap:6px;margin-top:4px"><x-icon name="map-pin" :size="14" color="#fff" /> {{ $place }}</div>
            @endif
          </div>
        </a>
      </div>
    @endforeach
  </div>

  <div class="nep-proj-swiper__controls">
    <button class="nep-proj-swiper__btn nep-proj-swiper__prev" type="button" aria-label="Dự án trước"><x-icon name="arrow-left" :size="20" /></button>
    <div class="nep-proj-swiper__dots"></div>
    <button class="nep-proj-swiper__btn nep-proj-swiper__next" type="button" aria-label="Dự án tiếp theo"><x-icon name="arrow-right" :size="20" /></button>
  </div>
</div>
