@props(['items' => []])

{{-- Breadcrumb hiển thị. $items = [['name','url'], ...] (phần tử cuối = trang hiện tại). --}}
@if(is_array($items) && count($items) > 1)
  <nav class="nep-breadcrumb" aria-label="Breadcrumb" style="display:flex;flex-wrap:wrap;align-items:center;gap:8px;font-size:var(--text-sm);color:var(--text-muted)">
    @foreach($items as $i => $c)
      @if($i === count($items) - 1)
        <span aria-current="page" style="color:var(--text-strong);font-weight:600">{{ $c['name'] }}</span>
      @else
        <a href="{{ $c['url'] }}" style="color:var(--text-muted);text-decoration:none">{{ $c['name'] }}</a>
        <x-icon name="chevron-right" :size="15" />
      @endif
    @endforeach
  </nav>
@endif
