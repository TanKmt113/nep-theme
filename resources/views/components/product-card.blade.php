@props([
  'image' => '',
  'title' => '',
  'description' => '',
  'meta' => '',
  'price' => '',
  'badge' => '',       // '' | 'new' | 'hot'
  'href' => '#',
  'cta' => 'Xem chi tiết',
])

{{-- NẾP · ProductCard — image with hover zoom + lift (CSS), badge, title, price. --}}
<a href="{{ $href }}" class="nep-product-card" {{ $attributes }}>
  <div class="nep-product-card__frame">
    <img src="{{ $image }}" alt="{{ $title }}" class="nep-product-card__img">
    @if($badge)
      <div style="position:absolute;top:14px;left:14px">
        <x-badge :variant="$badge">{{ $badge === 'new' ? 'Mới' : 'Bán chạy' }}</x-badge>
      </div>
    @endif
  </div>
  <div class="nep-product-card__body">
    @if($meta)
      <div style="font-family:var(--font-sans);font-size:var(--text-2xs);font-weight:600;letter-spacing:var(--tracking-eyebrow);text-transform:uppercase;color:var(--text-muted)">{{ $meta }}</div>
    @endif
    <h3 style="font-family:var(--font-display);font-size:var(--text-h3);font-weight:600;line-height:1.15;color:var(--text-strong)">{{ $title }}</h3>
    @if($description)
      <p style="font-family:var(--font-sans);font-size:var(--text-sm);line-height:1.55;color:var(--text-muted);margin:0">{{ $description }}</p>
    @endif
    <div style="margin-top:auto;padding-top:var(--space-4);display:flex;align-items:center;justify-content:space-between">
      @if($price)
        <span style="font-family:var(--font-sans);font-size:var(--text-base);font-weight:700;color:var(--text-strong)">{{ $price }}</span>
      @endif
      <span class="nep-product-card__cta">{{ $cta }} <span class="nep-product-card__arrow">→</span></span>
    </div>
  </div>
</a>
