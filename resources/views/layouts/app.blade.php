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

    {{-- Calm scroll-reveal — ported from Layout.astro --}}
    <script>
      (function () {
        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
        var run = function () {
          var secs = Array.prototype.slice.call(document.querySelectorAll('main section'));
          var targets = secs.slice(1);
          targets.forEach(function (el) {
            el.style.opacity = '0';
            el.style.transform = 'translateY(30px)';
            el.style.transition = 'opacity .7s cubic-bezier(.16,1,.3,1), transform .7s cubic-bezier(.16,1,.3,1)';
          });
          var io = new IntersectionObserver(function (entries) {
            entries.forEach(function (e) {
              if (e.isIntersecting) { e.target.style.opacity = '1'; e.target.style.transform = 'none'; io.unobserve(e.target); }
            });
          }, { threshold: .1, rootMargin: '0px 0px -6% 0px' });
          targets.forEach(function (el) { io.observe(el); });
          setTimeout(function () { targets.forEach(function (el) { el.style.opacity = '1'; el.style.transform = 'none'; }); }, 2500);
        };
        setTimeout(run, 80);
      })();
    </script>

    @php(do_action('get_footer'))
    @php(wp_footer())
  </body>
</html>
