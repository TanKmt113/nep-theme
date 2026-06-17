@extends('layouts.app')

@php
  // Trang một loại rèm (product_cat). Khoá bộ lọc danh mục, vẫn cho lọc chất liệu/sắp xếp.
  $term = get_queried_object();
  $sel_mat = sanitize_text_field($_GET['mat'] ?? '');
  $sort    = sanitize_text_field($_GET['sort'] ?? '');

  $args = [
    'post_type' => 'product', 'posts_per_page' => 24, 'post_status' => 'publish',
    'tax_query' => [['taxonomy' => 'product_cat', 'field' => 'term_id', 'terms' => $term->term_id]],
  ];
  if ($sel_mat) {
    $args['meta_query'] = [['key' => 'material', 'value' => $sel_mat, 'compare' => '=']];
  }
  if ($sort === 'title_asc')  { $args['orderby'] = 'title'; $args['order'] = 'ASC'; }
  if ($sort === 'title_desc') { $args['orderby'] = 'title'; $args['order'] = 'DESC'; }

  $query = new WP_Query($args);
  $icon = function_exists('get_field') ? (get_field('icon', $term) ?: 'blinds') : 'blinds';
@endphp

@section('content')
  <section style="padding-top:140px;padding-bottom:var(--space-8);background:var(--cream)">
    <x-container>
      <div style="font-size:var(--text-sm);color:var(--text-muted);display:flex;gap:8px;align-items:center;margin-bottom:14px">
        <a href="{{ wc_get_page_permalink('shop') }}" style="color:inherit">Sản phẩm</a>
        <x-icon name="chevron-right" :size="14" color="var(--text-muted)" />
        <span style="color:var(--text-strong)">{{ $term->name }}</span>
      </div>
      <div style="display:flex;align-items:center;gap:14px">
        <span style="display:inline-flex;align-items:center;justify-content:center;width:54px;height:54px;border-radius:var(--radius-md);background:var(--olive-50)"><x-icon :name="$icon" :size="26" color="var(--brand)" /></span>
        <h1 style="font-size:var(--text-display-lg)">{{ $term->name }}</h1>
      </div>
      @if($term->description)
        <p style="font-size:var(--text-lg);color:var(--text-body);margin-top:14px;max-width:60ch">{{ $term->description }}</p>
      @endif
    </x-container>
  </section>

  <section style="padding-top:var(--space-7);padding-bottom:var(--section-y);background:var(--paper)">
    <x-container>
      @include('sections.product-listing', ['query' => $query, 'lock_cat' => true])
    </x-container>
  </section>
@endsection
