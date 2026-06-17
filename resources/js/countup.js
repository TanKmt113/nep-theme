/**
 * NẾP — count-up cho các con số thống kê (x-stat).
 * Số chạy từ 0 → giá trị đích khi phần tử cuộn vào màn hình, giữ nguyên
 * tiền tố/hậu tố (vd "5.000+", "10+"). Tôn trọng prefers-reduced-motion.
 */
const DURATION = 1600

function animate(el) {
  const target = parseFloat(el.dataset.countTo)
  if (isNaN(target)) return
  const prefix = el.dataset.countPrefix || ''
  const suffix = el.dataset.countSuffix || ''
  const decimals = parseInt(el.dataset.countDecimals || '0', 10)
  // Có dùng dấu chấm ngăn cách nghìn không (suy ra từ giá trị gốc).
  const group = el.dataset.countGroup === '1'

  const fmt = (n) => {
    let s = n.toFixed(decimals)
    if (group) s = s.replace(/\B(?=(\d{3})+(?!\d))/g, '.')
    return prefix + s + suffix
  }

  const ease = (t) => 1 - Math.pow(1 - t, 3) // easeOutCubic
  const start = performance.now()
  const step = (now) => {
    const p = Math.min(1, (now - start) / DURATION)
    el.textContent = fmt(target * ease(p))
    if (p < 1) requestAnimationFrame(step)
    else el.textContent = fmt(target)
  }
  requestAnimationFrame(step)
}

export function initCountUp() {
  const els = document.querySelectorAll('[data-count-to]')
  if (!els.length) return

  const reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches
  if (reduce || !('IntersectionObserver' in window)) {
    // Hiển thị thẳng giá trị đích, không animate.
    els.forEach((el) => {
      el.textContent =
        (el.dataset.countPrefix || '') + el.dataset.countRaw + (el.dataset.countSuffix || '')
    })
    return
  }

  // Khởi tạo về 0 để tránh nháy số đích trước khi cuộn tới.
  els.forEach((el) => {
    el.textContent = (el.dataset.countPrefix || '') + '0' + (el.dataset.countSuffix || '')
  })

  const io = new IntersectionObserver(
    (entries) => {
      entries.forEach((e) => {
        if (e.isIntersecting) {
          animate(e.target)
          io.unobserve(e.target)
        }
      })
    },
    { threshold: 0.4 }
  )
  els.forEach((el) => io.observe(el))
}
