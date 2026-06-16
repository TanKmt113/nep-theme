@extends('layouts.app')

@section('content')
  @while(have_posts()) @php(the_post())
    <section style="padding-top:140px;padding-bottom:var(--space-8);background:var(--cream)">
      <x-container>
        <x-eyebrow rule>Catalog</x-eyebrow>
        <h1 style="font-size:var(--text-display-lg);margin-top:12px">{{ get_the_title() }}</h1>
      </x-container>
    </section>

    <section style="padding-top:var(--space-7);padding-bottom:var(--section-y);background:var(--paper)">
      <x-container narrow>
        @if(has_post_thumbnail())
          <img src="{{ get_the_post_thumbnail_url(get_the_ID(), 'full') }}" alt="{{ get_the_title() }}" style="width:100%;border-radius:var(--radius-xl);box-shadow:var(--shadow-md);margin-bottom:var(--space-8)">
        @endif
        <div class="nep-prose" style="font-size:var(--text-lg);line-height:1.8;color:var(--text-body)">
          @php(the_content())
        </div>
      </x-container>
    </section>

    @include('sections.final-cta')
  @endwhile
@endsection
