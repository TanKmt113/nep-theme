@extends('layouts.app')

{{-- Fallback list view (blog / search / catch-all). --}}
@section('content')
  <section style="padding-top:140px;padding-bottom:var(--space-8);background:var(--cream)">
    <x-container>
      <h1 style="font-size:var(--text-display-md)">
        @if(is_search()) Kết quả tìm kiếm: {{ get_search_query() }}
        @else {!! get_the_archive_title() ?: 'Bài viết' !!} @endif
      </h1>
    </x-container>
  </section>

  <section style="padding-top:var(--space-7);padding-bottom:var(--section-y);background:var(--paper)">
    <x-container>
      @if(have_posts())
        <div style="display:flex;flex-direction:column;gap:var(--space-6);max-width:var(--container-text);margin:0 auto">
          @while(have_posts()) @php(the_post())
            <article style="border-bottom:1px solid var(--border-soft);padding-bottom:var(--space-6)">
              <h2 style="font-family:var(--font-display);font-size:var(--text-h1)"><a href="{{ get_permalink() }}" style="color:var(--text-strong)">{{ get_the_title() }}</a></h2>
              <div style="font-size:var(--text-base);color:var(--text-body);line-height:1.7;margin-top:8px">{!! get_the_excerpt() !!}</div>
            </article>
          @endwhile
        </div>
        <div style="margin-top:var(--space-8)">@php(the_posts_pagination())</div>
      @else
        <p style="text-align:center;color:var(--text-muted)">Chưa có nội dung.</p>
      @endif
    </x-container>
  </section>
@endsection
