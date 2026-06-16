@props([
  'name' => '',
  'size' => 20,
  'color' => 'currentColor',
  'stroke' => 1.75,
])

{{--
  NẾP · Icon — Lucide outline icons.
  Renders a <i data-lucide> placeholder that lucide.createIcons() (loaded in app.js)
  swaps for an inline SVG. Mirrors the kebab-case names used by the React Icon registry.
--}}
<i
  data-lucide="{{ $name }}"
  width="{{ $size }}"
  height="{{ $size }}"
  stroke-width="{{ $stroke }}"
  style="display:inline-flex;color:{{ $color }};width:{{ $size }}px;height:{{ $size }}px"
  aria-hidden="true"
></i>
