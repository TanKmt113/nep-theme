<!DOCTYPE html>
<html @php(language_attributes())>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/svg+xml" href="{{ get_theme_file_uri('public/images/logo-mark.svg') }}">
    <meta name="theme-color" content="#6E764F">
    <style>
      .nep-skip-link{position:absolute;left:-9999px;top:0;z-index:1000;background:var(--moss,#6E764F);color:#fff;padding:10px 18px;border-radius:0 0 8px 0;font-weight:600;text-decoration:none}
      .nep-skip-link:focus{left:0}
    </style>
    @php(wp_head())
  </head>

  <body @php(body_class())>
    <a class="nep-skip-link" href="#app">Tới nội dung chính</a>

    @php(do_action('get_header'))

    @include('sections.header')

    <main id="app" tabindex="-1">
      @yield('content')
    </main>

    @include('sections.footer')

    {{-- Scroll/entrance animations đã chuyển sang GSAP (resources/js/animations.js). --}}

    @php(do_action('get_footer'))
    @php(wp_footer())
  </body>
</html>
