@props([
  'label' => '',
  'name' => '',
  'type' => 'text',
  'placeholder' => '',
  'required' => false,
  'value' => '',
])

@php $id = $name ? "nep-{$name}" : 'nep-' . strtolower(preg_replace('/\s+/', '-', $label)); @endphp

{{-- NẾP · Input — label + soft-outline field. --}}
<div style="display:flex;flex-direction:column;gap:7px;width:100%">
  @if($label)
    <label for="{{ $id }}" style="font-family:var(--font-sans);font-size:var(--text-sm);font-weight:600;color:var(--text-strong)">{{ $label }}</label>
  @endif
  <input
    id="{{ $id }}"
    @if($name) name="{{ $name }}" @endif
    type="{{ $type }}"
    placeholder="{{ $placeholder }}"
    value="{{ $value }}"
    @required($required)
    class="nep-input"
    style="height:50px;box-shadow:var(--shadow-xs)"
    {{ $attributes }}
  >
</div>
