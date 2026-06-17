@props([
  'name' => '',
  'size' => 20,
  'color' => 'currentColor',
  'stroke' => 1.75,
])

{{--
  NẾP · Icon — Lucide outline icons, INLINE SVG (self-host, không cần JS).
  SVG lấy từ app/icons.php qua App\icon_inner(). Không còn <i data-lucide>
  nên hết request CDN + hết layout-shift do JS swap.
--}}
@php($inner = \App\icon_inner($name))
@if($inner)
  <svg xmlns="http://www.w3.org/2000/svg" width="{{ $size }}" height="{{ $size }}" viewBox="0 0 24 24" fill="none" stroke="{{ $color }}" stroke-width="{{ $stroke }}" stroke-linecap="round" stroke-linejoin="round" style="display:inline-flex;flex:none;vertical-align:middle" aria-hidden="true" focusable="false">{!! $inner !!}</svg>
@endif
