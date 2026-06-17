@extends('layouts.app')

{{-- Generic single post (bài viết thường). --}}
@section('content')
  @while(have_posts()) @php(the_post())
    <section style="padding-top:140px;padding-bottom:var(--space-7);background:var(--cream)">
      <x-container narrow>
        <div style="margin-bottom:16px"><x-breadcrumb :items="\App\Seo\breadcrumb_items()" /></div>
        <x-eyebrow rule>{{ get_the_date() }}</x-eyebrow>
        <h1 style="font-size:var(--text-display-md);margin-top:12px">{{ get_the_title() }}</h1>
      </x-container>
    </section>
    <section style="padding-top:var(--space-7);padding-bottom:var(--section-y);background:var(--paper)">
      <x-container narrow>
        @if(has_post_thumbnail())
          <img src="{{ get_the_post_thumbnail_url(get_the_ID(), 'large') }}" alt="{{ get_the_title() }}" fetchpriority="high" decoding="async" style="width:100%;border-radius:var(--radius-xl);margin-bottom:var(--space-7)">
        @endif
        <div class="nep-prose" style="font-size:var(--text-lg);line-height:1.8;color:var(--text-body)">@php(the_content())</div>
      </x-container>
    </section>
  @endwhile
@endsection
