@props([
  'narrow' => false,
  'as' => 'div',
  'style' => '',
])

{{-- NẾP · Container — centered content column. --}}
<{{ $as }}
  {{ $attributes }}
  style="width:100%;max-width:{{ $narrow ? 'var(--container-text)' : 'var(--container)' }};margin:0 auto;padding-inline:var(--gutter);{{ $style }}"
>
  {{ $slot }}
</{{ $as }}>
