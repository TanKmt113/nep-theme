<!DOCTYPE html>
<html @php(language_attributes())>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/svg+xml" href="{{ get_theme_file_uri('public/images/logo-mark.svg') }}">
    <meta name="theme-color" content="#6E764F">
    @php(wp_head())
  </head>

  <body @php(body_class())>
    @php(do_action('get_header'))

    @include('sections.header')

    <main id="app">
      @yield('content')
    </main>

    @include('sections.footer')

    {{-- Scroll/entrance animations đã chuyển sang GSAP (resources/js/animations.js). --}}

    @php(do_action('get_footer'))
    @php(wp_footer())
  </body>
</html>
