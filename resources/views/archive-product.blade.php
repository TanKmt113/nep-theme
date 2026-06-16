@extends('layouts.app')

@php
  // Shop archive (tất cả sản phẩm). Lọc theo product_cat + chất liệu + sắp xếp.
  $sel_cat = sanitize_text_field($_GET['loai'] ?? '');
  $sel_mat = sanitize_text_field($_GET['mat'] ?? '');
  $sort    = sanitize_text_field($_GET['sort'] ?? '');

  $args = ['post_type' => 'product', 'posts_per_page' => 24, 'post_status' => 'publish'];
  $tax = [];
  if ($sel_cat) {
    $tax[] = ['taxonomy' => 'product_cat', 'field' => 'slug', 'terms' => $sel_cat];
  }
  if ($tax) $args['tax_query'] = $tax;
  if ($sel_mat) {
    $args['meta_query'] = [['key' => 'material', 'value' => $sel_mat, 'compare' => '=']];
  }
  if ($sort === 'title_asc')  { $args['orderby'] = 'title'; $args['order'] = 'ASC'; }
  if ($sort === 'title_desc') { $args['orderby'] = 'title'; $args['order'] = 'DESC'; }

  $query = new WP_Query($args);
@endphp

@section('content')
  <section style="padding-top:140px;padding-bottom:var(--space-8);background:var(--cream)">
    <x-container>
      <x-eyebrow rule>{{ App\nep('arch_product_eyebrow', 'Sản phẩm') }}</x-eyebrow>
      <h1 style="font-size:var(--text-display-lg);margin-top:12px">{{ App\nep('arch_product_heading', 'Bộ sưu tập rèm cửa') }}</h1>
    </x-container>
  </section>

  <section style="padding-top:var(--space-7);padding-bottom:var(--section-y);background:var(--paper)">
    <x-container>
      @include('sections.product-listing', ['query' => $query, 'current_cat' => $sel_cat])
    </x-container>
  </section>
@endsection
