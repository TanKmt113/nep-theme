@php use function App\nep; use function App\nep_tel; @endphp

{{-- NẾP · FinalCTA — full-bleed closing CTA. id="contact" so #contact links land here. --}}
<section id="contact" style="position:relative;padding:var(--space-12) 0;overflow:hidden">
  <img src="{{ nep('cta_image', 'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?w=1800&q=80') }}" alt="{{ nep('cta_heading', 'Nâng tầm không gian sống của bạn') }}" loading="lazy" decoding="async" style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover">
  <div style="position:absolute;inset:0;background:rgba(28,30,20,.66)"></div>
  <x-container :style="'position:relative;text-align:center'">
    <h2 style="color:#fff;font-size:var(--text-display-xl);line-height:1.05;max-width:18ch;margin:0 auto;text-wrap:balance">
      {{ nep('cta_heading', 'Nâng tầm không gian sống của bạn ngay hôm nay') }}
    </h2>
    <div style="display:flex;gap:14px;justify-content:center;margin-top:38px;flex-wrap:wrap">
      <x-button href="{{ nep_tel(nep('hotline')) }}" size="lg" variant="gold"><x-icon name="phone" :size="18" /> {{ nep('cta_btn1_text', 'Gọi ngay') }}</x-button>
      <x-button href="{{ nep('cta_btn2_url') ?: home_url('/lien-he') }}" size="lg" variant="secondary">{{ nep('cta_btn2_text', 'Đăng ký tư vấn') }}</x-button>
    </div>
  </x-container>
</section>
