@props([
  'value' => '',
  'label' => '',
  'align' => 'start',   // start | center
  'tone' => 'ink',      // ink | light
])

@php
  $color = $tone === 'light' ? 'var(--text-on-dark)' : 'var(--text-strong)';
  $labelColor = $tone === 'light' ? 'rgba(244,242,236,.72)' : 'var(--text-muted)';
  $items = $align === 'center' ? 'center' : 'flex-start';
@endphp

{{-- NẾP · Stat — display-serif number + label. --}}
<div style="display:flex;flex-direction:column;gap:4px;text-align:{{ $align }};align-items:{{ $items }}" {{ $attributes }}>
  <span style="font-family:var(--font-display);font-weight:600;font-size:var(--text-display-md);line-height:1;letter-spacing:-.01em;color:{{ $color }}">{{ $value }}</span>
  <span style="font-family:var(--font-sans);font-size:var(--text-sm);font-weight:500;color:{{ $labelColor }};letter-spacing:.01em">{{ $label }}</span>
</div>
