/**
 * NẾP — Flipbook PDF (xem catalogue kiểu lật trang như cuốn sách).
 *
 * PDF.js render từng trang ra ảnh → StPageFlip dựng hiệu ứng lật.
 * Module này được nạp lazy (chỉ ở trang Catalog có .nep-flipbook) qua app.js.
 */

import * as pdfjsLib from 'pdfjs-dist'
import workerUrl from 'pdfjs-dist/build/pdf.worker.min.mjs?url'
import { PageFlip } from 'page-flip/dist/js/page-flip.module.js'

/**
 * Một số server (vd: nginx chưa khai báo MIME cho .mjs) trả file worker là
 * application/octet-stream → trình duyệt từ chối nạp module worker và PDF.js
 * không chạy được. Ta tự fetch worker rồi đưa cho PDF.js một Blob URL cùng
 * origin với MIME do mình kiểm soát, nên nạp được bất kể cấu hình server.
 */
let workerSrcPromise
function resolveWorkerSrc() {
  workerSrcPromise ||= fetch(workerUrl)
    .then((res) => res.text())
    .then((code) => URL.createObjectURL(new Blob([code], { type: 'text/javascript' })))
    .catch(() => workerUrl) // không fetch được → dùng URL trực tiếp
  return workerSrcPromise
}

export async function initFlipbook() {
  const books = document.querySelectorAll('.nep-flipbook')
  if (!books.length) return
  pdfjsLib.GlobalWorkerOptions.workerSrc = await resolveWorkerSrc()
  for (const el of books) {
    try {
      await buildBook(el)
    } catch (err) {
      // eslint-disable-next-line no-console
      console.error('[NẾP] flipbook error:', err)
      fallbackToIframe(el)
    }
  }
}

async function buildBook(el) {
  const url = el.dataset.pdf
  if (!url) return

  const wrap = el.closest('.nep-flipbook-wrap')
  const loading = wrap?.querySelector('.nep-flipbook-loading')

  // 1. Tải PDF + render từng trang ra ảnh JPEG.
  const pdf = await pdfjsLib.getDocument({ url }).promise
  const total = pdf.numPages
  const images = []
  let ratio = 1.414 // A4 mặc định (cao/rộng)

  for (let i = 1; i <= total; i++) {
    const page = await pdf.getPage(i)
    const viewport = page.getViewport({ scale: 1.5 })
    if (i === 1) ratio = viewport.height / viewport.width

    const canvas = document.createElement('canvas')
    canvas.width = viewport.width
    canvas.height = viewport.height
    await page.render({ canvasContext: canvas.getContext('2d'), viewport }).promise
    images.push(canvas.toDataURL('image/jpeg', 0.85))

    if (loading) loading.textContent = `Đang tải catalogue… ${i}/${total}`
  }

  // 2. Kích thước trang sách (responsive qua size:'stretch').
  const wrapWidth = el.clientWidth || 900
  const pageW = Math.max(300, Math.min(560, Math.floor(wrapWidth / 2)))
  const pageH = Math.round(pageW * ratio)

  const flip = new PageFlip(el, {
    width: pageW,
    height: pageH,
    size: 'stretch',
    minWidth: 300,
    maxWidth: 1000,
    minHeight: 420,
    maxHeight: 1500,
    drawShadow: true,
    flippingTime: 700,
    usePortrait: true,
    showCover: true,
    maxShadowOpacity: 0.5,
    mobileScrollSupport: false,
  })

  flip.loadFromImages(images)
  loading?.remove()

  // 3. Điều khiển: prev / next / số trang + phím mũi tên.
  const counter = wrap?.querySelector('.nep-flipbook-page')
  const prev = wrap?.querySelector('[data-flip="prev"]')
  const next = wrap?.querySelector('[data-flip="next"]')

  const update = () => {
    if (counter) counter.textContent = `${flip.getCurrentPageIndex() + 1} / ${total}`
  }
  prev?.addEventListener('click', () => flip.flipPrev())
  next?.addEventListener('click', () => flip.flipNext())
  flip.on('flip', update)
  update()

  // Phím ← → khi con trỏ trong vùng sách.
  el.setAttribute('tabindex', '0')
  el.addEventListener('keydown', (e) => {
    if (e.key === 'ArrowLeft') flip.flipPrev()
    if (e.key === 'ArrowRight') flip.flipNext()
  })
}

/** Nếu flipbook lỗi → quay về trình xem PDF nhúng cơ bản. */
function fallbackToIframe(el) {
  const url = el.dataset.pdf
  const wrap = el.closest('.nep-flipbook-wrap') || el
  if (!url) return
  wrap.innerHTML =
    `<iframe src="${url}#view=FitH" title="PDF" ` +
    `style="width:100%;height:80vh;min-height:520px;border:0;border-radius:var(--radius-lg);box-shadow:var(--shadow-md)"></iframe>`
}
