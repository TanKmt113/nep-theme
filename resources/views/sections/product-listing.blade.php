@php
  use function App\nep_field;
  // Inputs: $query (WP_Query of products), $current_cat (slug, '' = all / locked off on taxonomy).
  $query = $query ?? null;
  $current_cat = $current_cat ?? '';
  $lock_cat = $lock_cat ?? false;   // true on a product_cat archive (đã ở trong danh mục)

  $sel_mat = sanitize_text_field($_GET['mat'] ?? '');
  $sort    = sanitize_text_field($_GET['sort'] ?? '');
  $cats = get_terms(['taxonomy' => 'product_cat', 'hide_empty' => false]);
  $materials = ['Linen', 'Cotton', 'Polyester', 'Nhung', 'Blackout', 'Gỗ tre', 'Nhôm'];
  $shop = function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : home_url('/cua-hang');
@endphp

{{-- Filter bar --}}
<form method="get" class="nep-filter-bar" style="display:flex;gap:12px;flex-wrap:wrap;margin-bottom:var(--space-7)">
  @unless($lock_cat)
    <select name="loai" class="nep-input" onchange="this.form.submit()">
      <option value="">Tất cả loại rèm</option>
      @foreach($cats as $t)
        <option value="{{ $t->slug }}" @selected($current_cat === $t->slug)>{{ $t->name }} ({{ $t->count }})</option>
      @endforeach
    </select>
  @endunless
  <select name="mat" class="nep-input" onchange="this.form.submit()">
    <option value="">Tất cả chất liệu</option>
    @foreach($materials as $m)
      <option value="{{ $m }}" @selected($sel_mat === $m)>{{ $m }}</option>
    @endforeach
  </select>
  <select name="sort" class="nep-input" onchange="this.form.submit()">
    <option value="">Mới nhất</option>
    <option value="title_asc" @selected($sort === 'title_asc')>Tên A→Z</option>
    <option value="title_desc" @selected($sort === 'title_desc')>Tên Z→A</option>
  </select>
  @if($sel_mat || $sort || (!$lock_cat && $current_cat))
    <a href="{{ $lock_cat ? get_term_link(get_queried_object()) : $shop }}" class="nep-btn nep-btn--ghost nep-btn--sm">Xoá lọc</a>
  @endif
</form>

@if($query && $query->have_posts())
  <div class="nep-grid-4" style="display:grid;grid-template-columns:repeat(4,1fr);gap:var(--space-5)">
    @while($query->have_posts())
      @php
        $query->the_post();
        $cat = get_the_terms(get_the_ID(), 'product_cat');
      @endphp
      <x-product-card
        :image="get_the_post_thumbnail_url(get_the_ID(), 'nep_card') ?: wc_placeholder_img_src('nep_card')"
        :meta="(!is_wp_error($cat) && $cat ? $cat[0]->name : '')"
        :title="get_the_title()"
        :description="get_the_excerpt()"
        :badge="nep_field('badge', get_the_ID(), '')"
        :href="get_permalink()"
        cta="Xem & báo giá"
      />
    @endwhile
  </div>
@else
  <div style="text-align:center;padding:var(--space-10) 0;color:var(--text-muted)">
    <x-icon name="search-x" :size="40" color="var(--text-muted)" />
    <p style="margin-top:12px">Không tìm thấy sản phẩm phù hợp.</p>
  </div>
@endif
@php(wp_reset_postdata())
