@props([
  'placeholder' => 'Tìm sản phẩm, dự án, bài viết…',
  'autofocus' => false,
])

{{-- NẾP · Search form — WP default ?s= query (kết quả ở search.blade.php) +
     gợi ý realtime qua AJAX nep_search_suggest (app/search.php). --}}
<div class="nep-search-box" data-suggest="{{ admin_url('admin-ajax.php') }}">
  <form role="search" method="get" action="{{ home_url('/') }}" {{ $attributes->merge(['class' => 'nep-search-form']) }}>
    <x-icon name="search" :size="18" color="var(--text-muted)" />
    <input
      type="search"
      name="s"
      class="nep-search-form__input"
      placeholder="{{ $placeholder }}"
      value="{{ get_search_query() }}"
      aria-label="Tìm kiếm"
      autocomplete="off"
      @if($autofocus) data-search-autofocus @endif
    >
    <button type="submit" class="nep-search-form__submit">Tìm</button>
  </form>
  <div class="nep-search-suggest" role="listbox" hidden></div>
</div>
