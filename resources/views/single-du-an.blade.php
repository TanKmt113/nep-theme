@extends('layouts.app')

@php use function App\nep_field; @endphp

@section('content')
  @while(have_posts()) @php(the_post())
    @php
      $id = get_the_ID();
      $scope = nep_field('scope', $id, []);
      $gallery = nep_field('gallery', $id, []);
    @endphp

    {{-- Hero --}}
    <section style="position:relative;min-height:62vh;display:flex;align-items:flex-end;overflow:hidden">
      <img src="{{ get_the_post_thumbnail_url($id, 'full') ?: '' }}" alt="{{ get_the_title() }}" style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover">
      <div style="position:absolute;inset:0;background:linear-gradient(180deg,rgba(20,22,14,.1) 0%,rgba(20,22,14,.72) 100%)"></div>
      <x-container :style="'position:relative;padding-bottom:var(--space-10);padding-top:140px'">
        <x-eyebrow rule color="var(--moss)">{{ nep_field('type', $id) }}</x-eyebrow>
        <h1 style="color:#fff;font-size:var(--text-display-xl);margin-top:14px">{{ get_the_title() }}</h1>
        <div style="display:flex;align-items:center;gap:8px;color:rgba(255,255,255,.85);margin-top:10px"><x-icon name="map-pin" :size="16" color="#fff" /> {{ nep_field('place', $id) }}</div>
      </x-container>
    </section>

    {{-- Body --}}
    <section style="padding-top:var(--section-y);padding-bottom:var(--section-y);background:var(--paper)">
      <x-container :style="'display:grid;grid-template-columns:1.6fr 1fr;gap:var(--space-10);align-items:start'">
        <div style="font-size:var(--text-lg);line-height:1.8;color:var(--text-body)">{!! get_the_content() !!}</div>
        <aside style="background:var(--cream);border-radius:var(--radius-lg);padding:var(--space-7);border:1px solid var(--border-soft)">
          <dl style="display:grid;grid-template-columns:auto 1fr;gap:12px 20px;font-size:var(--text-base)">
            @foreach([['Năm','year'],['Diện tích','area'],['Loại','type']] as $row)
              @if($v = nep_field($row[1], $id))
                <dt style="color:var(--text-muted)">{{ $row[0] }}</dt><dd style="font-weight:600;margin:0">{{ $v }}</dd>
              @endif
            @endforeach
          </dl>
          @if($scope)
            <h4 style="font-size:var(--text-sm);text-transform:uppercase;letter-spacing:.06em;color:var(--text-muted);margin:24px 0 12px">Hạng mục</h4>
            <ul style="list-style:none;margin:0;padding:0;display:flex;flex-direction:column;gap:8px">
              @foreach($scope as $s)
                <li style="display:flex;align-items:center;gap:8px;font-size:var(--text-base)"><x-icon name="check" :size="16" color="var(--brand)" /> {{ is_array($s) ? ($s['item'] ?? '') : $s }}</li>
              @endforeach
            </ul>
          @endif
        </aside>
      </x-container>

      @if($gallery)
        <x-container :style="'margin-top:var(--space-9)'">
          <div class="nep-grid-3" style="display:grid;grid-template-columns:repeat(3,1fr);gap:var(--space-5)">
            @foreach($gallery as $img)
              @php $src = is_array($img) ? ($img['sizes']['large'] ?? $img['url'] ?? '') : wp_get_attachment_image_url($img, 'large'); @endphp
              <img src="{{ $src }}" alt="" style="width:100%;aspect-ratio:4/3;object-fit:cover;border-radius:var(--radius-lg)">
            @endforeach
          </div>
        </x-container>
      @endif
    </section>

    @include('sections.final-cta')
  @endwhile
@endsection
