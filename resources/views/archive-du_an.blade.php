@extends('layouts.app')

@php use function App\nep_field; @endphp

{{-- Archive: Dự án (CPT du_an). Mirrors the project grid on the front page. --}}
@section('content')
  <section style="padding-top:140px;padding-bottom:var(--space-8);background:var(--cream)">
    <x-container>
      <x-eyebrow rule>{{ App\nep('arch_project_eyebrow', 'Dự án') }}</x-eyebrow>
      <h1 style="font-size:var(--text-display-lg);margin-top:12px">{{ App\nep('arch_project_heading', 'Không gian đã hoàn thiện') }}</h1>
    </x-container>
  </section>

  <section style="padding-top:var(--space-7);padding-bottom:var(--section-y);background:var(--paper)">
    <x-container>
      @if(have_posts())
        <div class="nep-proj-archive-grid">
          @while(have_posts())
            @php the_post(); $id = get_the_ID(); @endphp
            <a href="{{ get_permalink($id) }}" class="nep-proj-card nep-proj-archive-card">
              <img src="{{ get_the_post_thumbnail_url($id, 'nep_wide') ?: '' }}" alt="{{ get_the_title() }}" loading="lazy">
              <div class="nep-proj-card__overlay"></div>
              <div class="nep-proj-archive-card__cap">
                @if($type = nep_field('type', $id))
                  <div class="nep-proj-archive-card__type">{{ $type }}</div>
                @endif
                <div class="nep-proj-archive-card__title">{{ get_the_title() }}</div>
                @if($place = nep_field('place', $id))
                  <div class="nep-proj-archive-card__place"><x-icon name="map-pin" :size="14" color="#fff" /> {{ $place }}</div>
                @endif
              </div>
            </a>
          @endwhile
        </div>

        <div class="nep-pagination" style="margin-top:var(--space-8)">@php(the_posts_pagination(['mid_size' => 1, 'prev_text' => '←', 'next_text' => '→']))</div>
      @else
        <p style="text-align:center;color:var(--text-muted)">Chưa có dự án nào.</p>
      @endif
    </x-container>
  </section>

  @include('sections.final-cta')
@endsection
