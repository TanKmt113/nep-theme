@props([
  'variant' => 'primary',
  'size' => 'md',
  'as' => 'button',
  'href' => null,
  'full' => false,
])

{{-- NẾP · Button — pill-shaped. Hover/press handled in components.css. --}}
@php
  $tag = $href ? 'a' : $as;
  $classes = "nep-btn nep-btn--{$variant} nep-btn--{$size}" . ($full ? ' nep-btn--full' : '');
@endphp

<{{ $tag }}
  class="{{ $classes }}"
  @if($href) href="{{ $href }}" @endif
  {{ $attributes }}
>
  {{ $slot }}
</{{ $tag }}>
