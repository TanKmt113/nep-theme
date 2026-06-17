@extends('layouts.app')

@section('content')
  <section style="padding-top:180px;padding-bottom:var(--section-y);background:var(--cream);text-align:center">
    <x-container narrow>
      <div style="font-family:var(--font-display);font-size:96px;font-weight:600;color:var(--olive-200);line-height:1">404</div>
      <h1 style="font-size:var(--text-display-md);margin:12px 0 14px">Không tìm thấy trang</h1>
      <p style="font-size:var(--text-lg);color:var(--text-body);margin-bottom:28px">Trang bạn tìm có thể đã được di chuyển hoặc không tồn tại.</p>
      <x-button href="{{ home_url('/') }}" size="lg" variant="primary">Về trang chủ</x-button>
    </x-container>
  </section>
@endsection
