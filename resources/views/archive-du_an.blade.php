@extends('layouts.app')

@php use function App\nep_field; @endphp

{{-- Archive: Dự án (CPT du_an). Mirrors the project grid on the front page. --}}
@section('content')
  <section style="padding-top:140px;padding-bottom:var(--space-8);background:var(--cream)">
    <x-container>
      <x-eyebrow rule>Dự án</x-eyebrow>
      <h1 style="font-size:var(--text-display-lg);margin-top:12px">Không gian đã hoàn thiện</h1>
    </x-container>
  </section>

  <section style="padding-top:var(--space-7);padding-bottom:var(--section-y);background:var(--paper)">
    <x-container>
      @php $projects = $GLOBALS['wp_query']->posts ?? []; @endphp
      @if($projects)
        @include('sections.project-slider', ['projects' => $projects, 'showType' => true])
      @else
        <p style="text-align:center;color:var(--text-muted)">Chưa có dự án nào.</p>
      @endif
    </x-container>
  </section>

  @include('sections.final-cta')
@endsection
