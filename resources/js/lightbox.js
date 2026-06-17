/**
 * NẾP — lightbox thư viện ảnh (không phụ thuộc thư viện ngoài).
 *
 * Bắt click trên mọi link ảnh trong [data-lightbox] (vd .nep-da-gallery),
 * mở overlay xem ảnh full kèm điều hướng trước/sau, đếm số, đóng bằng
 * nút × / phím Esc / click nền. Phím ← → để chuyển ảnh.
 */
export function initLightbox(selector = '[data-lightbox]') {
  const groups = document.querySelectorAll(selector)
  if (!groups.length) return

  let overlay, imgEl, capEl, counterEl
  let items = []   // [{ href, caption }]
  let index = 0

  function build() {
    if (overlay) return
    overlay = document.createElement('div')
    overlay.className = 'nep-lb'
    overlay.setAttribute('role', 'dialog')
    overlay.setAttribute('aria-modal', 'true')
    overlay.setAttribute('aria-label', 'Thư viện hình ảnh')
    overlay.innerHTML = `
      <button class="nep-lb__close" type="button" aria-label="Đóng">&times;</button>
      <button class="nep-lb__nav nep-lb__prev" type="button" aria-label="Ảnh trước">&#8249;</button>
      <figure class="nep-lb__stage">
        <img class="nep-lb__img" alt="">
        <figcaption class="nep-lb__cap"></figcaption>
      </figure>
      <button class="nep-lb__nav nep-lb__next" type="button" aria-label="Ảnh sau">&#8250;</button>
      <span class="nep-lb__counter"></span>
    `
    document.body.appendChild(overlay)
    imgEl = overlay.querySelector('.nep-lb__img')
    capEl = overlay.querySelector('.nep-lb__cap')
    counterEl = overlay.querySelector('.nep-lb__counter')

    overlay.querySelector('.nep-lb__close').addEventListener('click', close)
    overlay.querySelector('.nep-lb__prev').addEventListener('click', () => go(-1))
    overlay.querySelector('.nep-lb__next').addEventListener('click', () => go(1))
    // Click ra nền (không phải ảnh/nút) thì đóng.
    overlay.addEventListener('click', (e) => {
      if (e.target === overlay || e.target.classList.contains('nep-lb__stage')) close()
    })
    document.addEventListener('keydown', onKey)
  }

  function render() {
    const it = items[index]
    if (!it) return
    imgEl.src = it.href
    imgEl.alt = it.caption || ''
    capEl.textContent = it.caption || ''
    capEl.style.display = it.caption ? '' : 'none'
    counterEl.textContent = `${index + 1} / ${items.length}`
    const multi = items.length > 1
    overlay.querySelector('.nep-lb__prev').style.display = multi ? '' : 'none'
    overlay.querySelector('.nep-lb__next').style.display = multi ? '' : 'none'
    counterEl.style.display = multi ? '' : 'none'
  }

  function go(step) {
    index = (index + step + items.length) % items.length
    render()
  }

  function open(groupItems, start) {
    build()
    items = groupItems
    index = start
    render()
    overlay.classList.add('is-open')
    document.documentElement.style.overflow = 'hidden'
  }

  function close() {
    overlay.classList.remove('is-open')
    document.documentElement.style.overflow = ''
    imgEl.src = ''
  }

  function onKey(e) {
    if (!overlay.classList.contains('is-open')) return
    if (e.key === 'Escape') close()
    else if (e.key === 'ArrowLeft') go(-1)
    else if (e.key === 'ArrowRight') go(1)
  }

  groups.forEach((group) => {
    const links = [...group.querySelectorAll('a[href]')]
    const groupItems = links.map((a) => ({
      href: a.getAttribute('href'),
      caption: a.dataset.caption || a.querySelector('img')?.alt || '',
    }))
    links.forEach((a, i) => {
      a.addEventListener('click', (e) => {
        e.preventDefault()
        open(groupItems, i)
      })
    })
  })
}
