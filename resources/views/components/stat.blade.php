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

  // Tách số để count-up: tiền tố (vd "~") + số (có thể có . , ngăn cách nghìn) + hậu tố (vd "+", "%").
  $count = null;
  if (preg_match('/^(\D*)([\d.,]+)(\D*)$/u', trim((string) $value), $m)) {
    $digits = str_replace([',', '.'], '', $m[2]);
    if ($digits !== '') {
      $count = [
        'prefix' => $m[1],
        'suffix' => $m[3],
        'to'     => $digits,
        'group'  => (bool) preg_match('/[.,]/', $m[2]),
        'raw'    => $m[2],
      ];
    }
  }
@endphp

{{-- NẾP · Stat — display-serif number + label. --}}
<div style="display:flex;flex-direction:column;gap:4px;text-align:{{ $align }};align-items:{{ $items }}" {{ $attributes }}>
  <span style="font-family:var(--font-display);font-weight:600;font-size:var(--text-display-md);line-height:1;letter-spacing:-.01em;color:{{ $color }}"
    @if($count) data-count-to="{{ $count['to'] }}" data-count-prefix="{{ $count['prefix'] }}" data-count-suffix="{{ $count['suffix'] }}" data-count-group="{{ $count['group'] ? '1' : '0' }}" data-count-raw="{{ $count['raw'] }}" @endif>{{ $value }}</span>
  <span style="font-family:var(--font-sans);font-size:var(--text-sm);font-weight:500;color:{{ $labelColor }};letter-spacing:.01em">{{ $label }}</span>
</div>
