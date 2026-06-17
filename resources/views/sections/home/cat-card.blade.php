@php
  // $term is a product_cat WP_Term. Image + icon stored as term meta (ACF, optional).
  $img = function_exists('get_field') ? get_field('img', $term) : '';
  $icon = function_exists('get_field') ? (get_field('icon', $term) ?: 'blinds') : 'blinds';
  $link = get_term_link($term);
@endphp

<a href="{{ $link }}" class="nep-cat-card" style="position:relative;border-radius:var(--radius-lg);overflow:hidden;aspect-ratio:1/1;display:block">
  <img src="{{ $img }}" alt="{{ $term->name }}" loading="lazy" decoding="async" style="width:100%;height:100%;object-fit:cover">
  <div class="nep-cat-card__overlay"></div>
  <div style="position:absolute;left:18px;right:18px;bottom:16px;color:#fff;display:flex;align-items:center;justify-content:space-between">
    <div>
      <div style="display:flex;align-items:center;gap:8px">
        <x-icon :name="$icon" :size="18" color="#fff" />
        <span style="font-family:var(--font-display);font-size:var(--text-h3);font-weight:600">{{ $term->name }}</span>
      </div>
      <span style="font-size:var(--text-xs);opacity:.82">{{ $term->count }} mẫu</span>
    </div>
    <span class="nep-cat-card__arrow"><x-icon name="arrow-up-right" :size="20" color="#fff" /></span>
  </div>
</a>
