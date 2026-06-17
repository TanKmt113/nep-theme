@props([
  'rule' => false,
  'center' => false,
  'color' => 'var(--text-brand)',
])

{{-- NẾP · Eyebrow — small uppercase label, optional leading rule. --}}
<div
  class="nep-eyebrow"
  style="display:inline-flex;align-items:center;gap:10px;font-family:var(--font-sans);font-size:var(--text-xs);font-weight:700;letter-spacing:var(--tracking-eyebrow);text-transform:uppercase;color:{{ $color }};{{ $center ? 'justify-content:center;' : '' }}"
  {{ $attributes }}
>
  @if($rule)
    <span style="display:inline-block;width:28px;height:1.5px;background:currentColor;opacity:.6"></span>
  @endif
  {{ $slot }}
</div>
