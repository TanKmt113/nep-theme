@extends('layouts.app')

{{-- Generic page (trang không gán Page Template riêng). --}}
@section('content')
  @while(have_posts()) @php(the_post())
    <section style="padding-top:140px;padding-bottom:var(--space-7);background:var(--cream)">
      <x-container narrow>
        <h1 style="font-size:var(--text-display-md)">{{ get_the_title() }}</h1>
      </x-container>
    </section>
    <section style="padding-top:var(--space-7);padding-bottom:var(--section-y);background:var(--paper)">
      <x-container narrow>
        <div class="nep-prose" style="font-size:var(--text-lg);line-height:1.8;color:var(--text-body)">@php(the_content())</div>
      </x-container>
    </section>
  @endwhile
@endsection
