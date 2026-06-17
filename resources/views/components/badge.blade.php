@props([
  'variant' => 'neutral',
  'size' => 'md',
])

{{-- NẾP · Badge — small status / promo pill. --}}
<span class="nep-badge nep-badge--{{ $variant }} nep-badge--{{ $size }}" {{ $attributes }}>
  {{ $slot }}
</span>
