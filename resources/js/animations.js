/**
 * NẾP — chuyển động bằng GSAP (https://gsap.com).
 *
 * Triết lý: "calm luxury" — vào nhẹ, mượt, không màu mè. Tất cả trạng thái ẩn
 * ban đầu được đặt BẰNG JS (gsap.from/gsap.set), không bao giờ ẩn bằng CSS —
 * nên nếu JS lỗi hoặc người dùng tắt JS, nội dung vẫn hiển thị đầy đủ.
 *
 * Tôn trọng prefers-reduced-motion: bỏ qua toàn bộ hiệu ứng.
 */

import { gsap } from 'gsap'
import { ScrollTrigger } from 'gsap/ScrollTrigger'

gsap.registerPlugin(ScrollTrigger)

const REDUCED = window.matchMedia('(prefers-reduced-motion: reduce)').matches
const EASE = 'power3.out'
const GRID_SELECTOR = '.nep-cat-grid, .nep-grid-4, .nep-grid-3, .nep-proj-grid'

/** Lấy các "khối" nội dung cấp 1 bên trong một section (bỏ qua phần tử rỗng). */
function contentBlocks(section) {
  // Container do <x-container> render là <div> bọc nội dung — thường là con đầu tiên.
  const wrapper = section.querySelector(':scope > div') || section
  return Array.from(wrapper.children).filter(
    (el) => el.offsetParent !== null || el.getClientRects().length
  )
}

/** Hero (section đầu): timeline vào khi tải trang + parallax ảnh nền. */
function animateHero(hero) {
  const heading = hero.querySelector('h1')
  const block = heading ? heading.parentElement : null
  const items = block ? Array.from(block.children) : []

  if (items.length) {
    gsap.timeline({ defaults: { ease: EASE, duration: 0.9 } }).from(items, {
      y: 30,
      opacity: 0,
      stagger: 0.12,
      delay: 0.1,
    })
  }

  // Parallax nhẹ cho ảnh nền hero (nếu có).
  const bg = hero.querySelector(':scope > img')
  if (bg) {
    gsap.to(bg, {
      yPercent: 12,
      ease: 'none',
      scrollTrigger: {
        trigger: hero,
        start: 'top top',
        end: 'bottom top',
        scrub: true,
      },
    })
  }
}

/** Các section còn lại: reveal tiêu đề/đoạn văn + stagger lưới khi cuộn tới. */
function animateSection(section) {
  const grids = Array.from(section.querySelectorAll(GRID_SELECTOR))

  // Reveal các khối KHÔNG phải lưới (eyebrow, tiêu đề, mô tả, ảnh, CTA…).
  const blocks = contentBlocks(section).filter(
    (b) => !grids.some((g) => b === g || b.contains(g))
  )
  if (blocks.length) {
    gsap.from(blocks, {
      y: 36,
      opacity: 0,
      duration: 0.9,
      ease: EASE,
      stagger: 0.12,
      scrollTrigger: { trigger: section, start: 'top 80%' },
    })
  }

  // Mỗi lưới: stagger từng thẻ con.
  grids.forEach((grid) => {
    const cards = Array.from(grid.children)
    if (!cards.length) return
    gsap.from(cards, {
      y: 28,
      opacity: 0,
      duration: 0.7,
      ease: EASE,
      stagger: 0.08,
      scrollTrigger: { trigger: grid, start: 'top 85%' },
    })
  })
}

export function initAnimations() {
  // Tôn trọng người dùng muốn giảm chuyển động — không đụng gì, nội dung hiện sẵn.
  if (REDUCED) return

  const main = document.querySelector('main')
  if (!main) return

  const sections = Array.from(main.querySelectorAll(':scope > section'))
  if (!sections.length) return

  try {
    animateHero(sections[0])
    sections.slice(1).forEach(animateSection)

    // Tính lại vị trí sau khi ảnh tải xong để ScrollTrigger không lệch.
    window.addEventListener('load', () => ScrollTrigger.refresh())
  } catch (e) {
    // An toàn: nếu có lỗi, gỡ mọi trạng thái ẩn để không che mất nội dung.
    gsap.set('main section *', { clearProps: 'opacity,transform' })
    // eslint-disable-next-line no-console
    console.error('[NẾP] GSAP init error:', e)
  }
}
