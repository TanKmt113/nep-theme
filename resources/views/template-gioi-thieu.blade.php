{{--
  Template Name: Giới thiệu (About)
--}}
@extends('layouts.app')

@php
  use function App\page_field;
  use function App\page_rows;

  // Nội dung biên tập qua ACF ("Nội dung trang Giới thiệu"), fallback về mặc định.
  $hero = page_field('about_hero_image', 'https://images.unsplash.com/photo-1524758631624-e2822e304c36?w=1800&q=80');
  $lead = page_field('about_lead', 'NẾP ra đời năm 2014 từ một xưởng may rèm nhỏ ở Sài Gòn, với niềm tin rằng một tấm rèm đẹp có thể thay đổi cả cảm xúc của một căn nhà.');
  $story = page_rows('about_story', [
    'Khởi đầu là một xưởng gia đình, chúng tôi nhận từng đơn đo may rèm cho hàng xóm, người quen. Mỗi tấm rèm được cắt, may và treo bằng tay — và chính sự tỉ mỉ ấy đã giữ chân khách hàng suốt một thập kỷ.',
    'Hôm nay, NẾP là sự kết hợp giữa nghề thủ công và công nghệ: showroom rèm cửa cao cấp song hành cùng xưởng thêu vi tính đa kim, phục vụ cả gia đình lẫn doanh nghiệp trên toàn quốc.',
  ], fn ($r) => $r['text'] ?? '');
  $story_img1 = page_field('about_story_img1', 'https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?w=700&q=80');
  $story_img2 = page_field('about_story_img2', 'https://images.unsplash.com/photo-1452860606245-08befc0ff44b?w=700&q=80');
  $stats = page_rows('about_stats', [['10+','Năm kinh nghiệm'],['5000+','Khách hàng'],['100+','Dự án doanh nghiệp'],['40+','Thành viên đội ngũ']], fn ($r) => [$r['value'] ?? '', $r['label'] ?? '']);
  $values = page_rows('about_values', [
    ['gem','Chất lượng cao cấp','Chỉ chọn chất liệu nhập khẩu, an toàn và bền màu theo thời gian.'],
    ['heart-handshake','Tận tâm','Đồng hành từ khảo sát đến bảo hành, lắng nghe từng nhu cầu nhỏ.'],
    ['ruler','Tỉ mỉ','Đo may theo kích thước thực tế, chăm chút từng đường chỉ nếp gấp.'],
    ['leaf','Bền vững','Ưu tiên vật liệu tự nhiên và quy trình sản xuất ít lãng phí.'],
  ], fn ($r) => [$r['icon'] ?? 'gem', $r['title'] ?? '', $r['desc'] ?? '']);
  $milestones = page_rows('about_milestones', [
    ['2014','Thành lập xưởng may rèm đầu tiên tại Quận Phú Nhuận.'],
    ['2017','Khai trương showroom rèm cửa cao cấp 200m².'],
    ['2020','Đầu tư hệ thống máy thêu vi tính, mở rộng sang mảng thêu.'],
    ['2023','Cán mốc 5.000 khách hàng và 100+ dự án doanh nghiệp.'],
    ['2026','Phủ sóng thi công toàn quốc với đội ngũ hơn 40 người.'],
  ], fn ($r) => [$r['year'] ?? '', $r['text'] ?? '']);
  $team = page_rows('about_team', [
    ['Nguyễn Thành Nam','Nhà sáng lập','https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=500&q=80'],
    ['Trần Khánh Vy','Giám đốc thiết kế','https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=500&q=80'],
    ['Lê Minh Quân','Trưởng xưởng thêu','https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=500&q=80'],
    ['Phạm Thu Hà','Tư vấn nội thất','https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=500&q=80'],
  ], fn ($r) => [$r['name'] ?? '', $r['role'] ?? '', $r['image'] ?? '']);
@endphp

@section('content')
  {{-- Hero --}}
  <section style="position:relative;min-height:70vh;display:flex;align-items:flex-end;overflow:hidden">
    <img src="{{ $hero }}" alt="{{ page_field('about_hero_title', 'Về NẾP') }}" fetchpriority="high" decoding="async" style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover">
    <div style="position:absolute;inset:0;background:linear-gradient(180deg,rgba(20,22,14,.36) 0%,rgba(20,22,14,.18) 45%,rgba(20,22,14,.74) 100%)"></div>
    <x-container :style="'position:relative;padding-bottom:var(--space-10);padding-top:150px'">
      <x-eyebrow rule color="var(--moss)">{{ page_field('about_hero_eyebrow', 'Về chúng tôi') }}</x-eyebrow>
      <h1 style="color:#fff;font-size:var(--text-display-xl);line-height:1.05;margin:18px 0 0;max-width:16ch;text-wrap:balance">
        {{ page_field('about_hero_title', 'Nghề rèm, thêu —') }}@php($a = page_field('about_hero_accent', 'chăm chút từng nếp gấp'))@if($a) <span style="font-style:italic;color:var(--moss-soft)">{{ $a }}</span>@endif
      </h1>
      <p style="color:rgba(255,255,255,.88);font-size:var(--text-lg);line-height:1.65;max-width:56ch;margin-top:20px">{{ $lead }}</p>
    </x-container>
  </section>

  @if(App\page_show('about_show_story'))
  {{-- Story --}}
  <section style="padding-top:var(--section-y);padding-bottom:var(--section-y);background:var(--cream)">
    <x-container :style="'display:grid;grid-template-columns:1fr 1fr;gap:var(--space-10);align-items:center'">
      <div>
        <x-eyebrow rule>{{ page_field('about_story_eyebrow', 'Câu chuyện NẾP') }}</x-eyebrow>
        <h2 style="font-size:var(--text-display-md);margin:16px 0 20px;max-width:16ch">{{ page_field('about_story_heading', 'Từ một xưởng nhỏ đến thương hiệu được tin yêu') }}</h2>
        @foreach($story as $p)
          <p style="font-size:var(--text-lg);line-height:1.75;color:var(--text-body);margin-bottom:18px;max-width:50ch">{{ $p }}</p>
        @endforeach
      </div>
      <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:16px">
        <img src="{{ $story_img1 }}" alt="{{ page_field('about_story_heading', 'Câu chuyện NẾP') }}" loading="lazy" decoding="async" style="width:100%;aspect-ratio:3/4;object-fit:cover;border-radius:var(--radius-lg);box-shadow:var(--shadow-md)">
        <img src="{{ $story_img2 }}" alt="{{ page_field('about_story_heading', 'Câu chuyện NẾP') }}" loading="lazy" decoding="async" style="width:100%;aspect-ratio:3/4;object-fit:cover;border-radius:var(--radius-lg);margin-top:36px;box-shadow:var(--shadow-md)">
      </div>
    </x-container>
  </section>

  @endif

  @if(App\page_show('about_show_stats'))
  {{-- Stats --}}
  <section style="background:var(--olive-900);padding:var(--space-9) 0">
    <x-container :style="'display:grid;grid-template-columns:repeat(4,1fr);gap:var(--space-6)'" class="nep-grid-4">
      @foreach($stats as $s)
        <x-stat :value="$s[0]" :label="$s[1]" align="center" tone="light" />
      @endforeach
    </x-container>
  </section>

  @endif

  @if(App\page_show('about_show_values'))
  {{-- Values --}}
  <section style="padding-top:var(--section-y);padding-bottom:var(--section-y);background:var(--paper)">
    <x-container>
      <div style="text-align:center;margin-bottom:var(--space-8)">
        <x-eyebrow rule center>{{ page_field('about_values_eyebrow', 'Giá trị cốt lõi') }}</x-eyebrow>
        <h2 style="font-size:var(--text-display-md);margin-top:12px">{{ page_field('about_values_heading', 'Điều chúng tôi luôn giữ') }}</h2>
      </div>
      <div class="nep-grid-4" style="display:grid;grid-template-columns:repeat(4,1fr);gap:var(--space-5)">
        @foreach($values as $v)
          <div style="padding:var(--space-6);background:var(--cream);border-radius:var(--radius-lg);border:1px solid var(--border-soft)">
            <span style="display:inline-flex;align-items:center;justify-content:center;width:52px;height:52px;border-radius:var(--radius-md);background:var(--olive-50);margin-bottom:18px"><x-icon :name="$v[0]" :size="24" color="var(--brand)" /></span>
            <h3 style="font-size:var(--text-h3);font-weight:600;margin-bottom:8px">{{ $v[1] }}</h3>
            <p style="font-size:var(--text-sm);color:var(--text-muted);line-height:1.6;margin:0">{{ $v[2] }}</p>
          </div>
        @endforeach
      </div>
    </x-container>
  </section>

  @endif

  @if(App\page_show('about_show_timeline'))
  {{-- Timeline --}}
  <section style="padding-top:var(--section-y);padding-bottom:var(--section-y);background:var(--beige)">
    <x-container>
      <div style="margin-bottom:var(--space-8)">
        <x-eyebrow rule>{{ page_field('about_timeline_eyebrow', 'Hành trình') }}</x-eyebrow>
        <h2 style="font-size:var(--text-display-md);margin-top:12px">{{ page_field('about_timeline_heading', 'Những cột mốc đáng nhớ') }}</h2>
      </div>
      <div style="position:relative">
        <div class="nep-timeline-line" style="position:absolute;left:0;right:0;top:26px;height:2px;background:var(--olive-200)"></div>
        <div class="nep-grid-5" style="display:grid;grid-template-columns:repeat(5,1fr);gap:var(--space-5);position:relative">
          @foreach($milestones as $m)
            <div>
              <div style="width:14px;height:14px;border-radius:50%;background:var(--brand);border:4px solid var(--beige);margin-bottom:22px;margin-left:20px"></div>
              <div style="font-family:var(--font-display);font-size:var(--text-h1);font-weight:600;color:var(--brand-press);line-height:1">{{ $m[0] }}</div>
              <p style="font-size:var(--text-sm);color:var(--text-body);line-height:1.6;margin-top:10px;max-width:22ch">{{ $m[1] }}</p>
            </div>
          @endforeach
        </div>
      </div>
    </x-container>
  </section>

  @endif

  @if(App\page_show('about_show_team'))
  {{-- Team --}}
  <section style="padding-top:var(--section-y);padding-bottom:var(--section-y);background:var(--paper)">
    <x-container>
      <div style="text-align:center;margin-bottom:var(--space-8)">
        <x-eyebrow rule center>{{ page_field('about_team_eyebrow', 'Đội ngũ') }}</x-eyebrow>
        <h2 style="font-size:var(--text-display-md);margin-top:12px">{{ page_field('about_team_heading', 'Những con người làm nên NẾP') }}</h2>
      </div>
      <div class="nep-grid-4" style="display:grid;grid-template-columns:repeat(4,1fr);gap:var(--space-5)">
        @foreach($team as $t)
          <div style="text-align:center">
            <div style="aspect-ratio:1/1;border-radius:var(--radius-lg);overflow:hidden;margin-bottom:16px;box-shadow:var(--shadow-sm)">
              <img src="{{ $t[2] }}" alt="{{ $t[0] }}" loading="lazy" decoding="async" style="width:100%;height:100%;object-fit:cover">
            </div>
            <div style="font-family:var(--font-display);font-size:var(--text-h3);font-weight:600;color:var(--text-strong)">{{ $t[0] }}</div>
            <div style="font-size:var(--text-sm);color:var(--text-muted);margin-top:2px">{{ $t[1] }}</div>
          </div>
        @endforeach
      </div>
    </x-container>
  </section>

  @endif

  @if(App\page_show('about_show_cta'))
  @include('sections.final-cta')
  @endif
@endsection
