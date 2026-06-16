/**
 * NẾP — theme entry. Loads the design-system CSS and wires up the two bits
 * of interactivity that React handled in the Astro build:
 *   1. Lucide icons (replaces the React <Icon> registry) — loaded via CDN (see setup.php)
 *   2. Header: solidify on scroll + mobile menu toggle (was Header.jsx state)
 */
import '../css/app.css'

const onReady = (fn) =>
  document.readyState !== 'loading' ? fn() : document.addEventListener('DOMContentLoaded', fn)

onReady(() => {
  // 1. Render all <i data-lucide="..."> placeholders (lucide loaded from CDN).
  if (window.lucide && typeof window.lucide.createIcons === 'function') {
    window.lucide.createIcons()
  }

  // 2. Header behaviour.
  const header = document.querySelector('.nep-header')
  if (!header) return

  const onScroll = () => header.classList.toggle('is-solid', window.scrollY > 40)
  window.addEventListener('scroll', onScroll, { passive: true })
  onScroll()

  const burger = header.querySelector('.nep-burger')
  const panel = header.querySelector('.nep-menu-panel')
  if (burger && panel) {
    burger.addEventListener('click', () => {
      const open = header.classList.toggle('is-open')
      panel.hidden = !open
      burger.setAttribute('aria-expanded', String(open))
      burger.setAttribute('aria-label', open ? 'Đóng menu' : 'Mở menu')
    })
    // Close panel when a link is tapped.
    panel.querySelectorAll('a').forEach((a) =>
      a.addEventListener('click', () => {
        header.classList.remove('is-open')
        panel.hidden = true
      })
    )
  }
})
