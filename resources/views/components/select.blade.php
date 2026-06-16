@props([
  'label' => '',
  'name' => '',
  'options' => [],   // array of strings
])

@php $id = $name ? "nep-{$name}" : 'nep-' . strtolower(preg_replace('/\s+/', '-', $label)); @endphp

{{-- NẾP · Select — styled native select with chevron. --}}
<div style="display:flex;flex-direction:column;gap:7px;width:100%">
  @if($label)
    <label for="{{ $id }}" style="font-family:var(--font-sans);font-size:var(--text-sm);font-weight:600;color:var(--text-strong)">{{ $label }}</label>
  @endif
  <div style="position:relative;display:flex;align-items:center">
    <select id="{{ $id }}" @if($name) name="{{ $name }}" @endif class="nep-input" style="width:100%;height:50px;padding:0 44px 0 16px;appearance:none;-webkit-appearance:none;cursor:pointer;box-shadow:var(--shadow-xs)" {{ $attributes }}>
      @foreach($options as $o)
        <option value="{{ $o }}">{{ $o }}</option>
      @endforeach
    </select>
    <span style="position:absolute;right:16px;pointer-events:none;color:var(--text-muted);display:inline-flex" aria-hidden="true">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
    </span>
  </div>
</div>
