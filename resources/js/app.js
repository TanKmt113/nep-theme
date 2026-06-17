/**
 * NẾP — theme entry. Loads the design-system CSS and wires up the two bits
 * of interactivity that React handled in the Astro build:
 *   1. Lucide icons (replaces the React <Icon> registry) — loaded via CDN (see setup.php)
 *   2. Header: solidify on scroll + mobile menu toggle (was Header.jsx state)
 */
import '../css/app.css'
import { initAnimations } from './animations'
import { initProjectSliders } from './sliders'
import { initCountUp } from './countup'
import { initSearchSuggest } from './search-suggest'

const onReady = (fn) =>
  document.readyState !== 'loading' ? fn() : document.addEventListener('DOMContentLoaded', fn)

onReady(() => {
  // 0. Sliders (Swiper).
  initProjectSliders()

  // Count-up cho con số thống kê (x-stat).
  initCountUp()

  // Gợi ý tìm kiếm realtime (mọi ô .nep-search-box).
  initSearchSuggest()

  // Icons giờ là inline SVG render sẵn từ PHP (app/icons.php) — không cần JS.

  // Lightbox thư viện ảnh — nạp lazy, chỉ khi trang có [data-lightbox].
  if (document.querySelector('[data-lightbox]')) {
    import('./lightbox')
      .then((m) => m.initLightbox())
      .catch((e) => console.error('[NẾP] lightbox load failed:', e))
  }

  // GSAP entrance + scroll animations (https://gsap.com). Runs before the
  // header early-return below so it works on pages without a header too.
  initAnimations()

  // Flipbook PDF (trang Catalog) — nạp lazy, chỉ tải khi có .nep-flipbook.
  if (document.querySelector('.nep-flipbook')) {
    import('./flipbook')
      .then((m) => m.initFlipbook())
      .catch((e) => console.error('[NẾP] flipbook load failed:', e))
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
    // Submenu con: chèn nút caret để mở/đóng từng nhánh trên mobile.
    panel.querySelectorAll('.menu-item-has-children').forEach((li) => {
      const toggle = document.createElement('button')
      toggle.type = 'button'
      toggle.className = 'nep-submenu-toggle'
      toggle.setAttribute('aria-label', 'Mở menu con')
      toggle.setAttribute('aria-expanded', 'false')
      li.appendChild(toggle)
      toggle.addEventListener('click', (e) => {
        e.preventDefault()
        e.stopPropagation()
        const open = li.classList.toggle('is-expanded')
        toggle.setAttribute('aria-expanded', String(open))
        toggle.setAttribute('aria-label', open ? 'Đóng menu con' : 'Mở menu con')
      })
    })
    // Close panel when a link is tapped.
    panel.querySelectorAll('a').forEach((a) =>
      a.addEventListener('click', () => {
        header.classList.remove('is-open')
        panel.hidden = true
      })
    )
  }

  // 3. Search overlay (desktop) — bật/tắt + focus ô nhập.
  const searchToggle = header.querySelector('.nep-search-toggle')
  const searchPanel = header.querySelector('.nep-search-panel')
  if (searchToggle && searchPanel) {
    const setSearch = (open) => {
      searchPanel.hidden = !open
      searchToggle.setAttribute('aria-expanded', String(open))
      if (open) searchPanel.querySelector('[data-search-autofocus]')?.focus()
    }
    searchToggle.addEventListener('click', () => setSearch(searchPanel.hidden))
    // Đóng khi nhấn Esc hoặc click ra ngoài.
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && !searchPanel.hidden) setSearch(false)
    })
    document.addEventListener('click', (e) => {
      if (!searchPanel.hidden && !searchPanel.contains(e.target) && !searchToggle.contains(e.target))
        setSearch(false)
    })
  }
})
