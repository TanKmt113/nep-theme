/**
 * NẾP — Flipbook PDF (xem catalogue kiểu lật trang như cuốn sách).
 *
 * PDF.js render từng trang ra ảnh → StPageFlip dựng hiệu ứng lật.
 * Module này được nạp lazy (chỉ ở trang Catalog có .nep-flipbook) qua app.js.
 *
 * Render LAZY: dựng sách ngay với trang trống, chỉ render trang nào người dùng
 * sắp xem. Tránh treo trình duyệt với catalogue nặng (vd 96 trang ~68MB) — nếu
 * render hết mọi trang ra ảnh ngay từ đầu thì RAM/CPU sẽ quá tải.
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

  // 1. Mở PDF (PDF.js tự dùng range-request nên KHÔNG tải hết file ngay).
  const pdf = await pdfjsLib.getDocument({ url }).promise
  const total = pdf.numPages

  // Tỉ lệ trang lấy từ trang 1 để dựng khung sách.
  const firstPage = await pdf.getPage(1)
  const baseViewport = firstPage.getViewport({ scale: 1 })
  const ratio = baseViewport.height / baseViewport.width

  // 2. Kích thước trang sách (responsive qua size:'stretch').
  const wrapWidth = el.clientWidth || 900
  const pageW = Math.max(300, Math.min(560, Math.floor(wrapWidth / 2)))
  const pageH = Math.round(pageW * ratio)
  // Render sắc nét trên màn retina nhưng vẫn nhẹ (giới hạn 2x).
  const dpr = Math.min(2, window.devicePixelRatio || 1)
  const renderScale = (pageW * dpr) / baseViewport.width

  // 3. Dựng các trang TRỐNG trước → sách hiện ngay, render ảnh sau.
  const pageEls = []
  for (let i = 1; i <= total; i++) {
    const d = document.createElement('div')
    d.className = 'nep-page'
    pageEls.push(d)
    el.appendChild(d)
  }

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

  flip.loadFromHTML(pageEls)
  loading?.remove()

  // 4. Render trang theo nhu cầu (lazy) + prefetch quanh trang hiện tại.
  const rendered = new Set()
  const renderPage = async (n) => {
    if (n < 1 || n > total || rendered.has(n)) return
    rendered.add(n)
    try {
      const page = await pdf.getPage(n)
      const viewport = page.getViewport({ scale: renderScale })
      const canvas = document.createElement('canvas')
      canvas.width = viewport.width
      canvas.height = viewport.height
      await page.render({ canvasContext: canvas.getContext('2d'), viewport }).promise
      const img = new Image()
      img.alt = `Trang ${n}`
      img.src = canvas.toDataURL('image/jpeg', 0.82)
      pageEls[n - 1].replaceChildren(img)
    } catch (err) {
      rendered.delete(n) // cho phép thử lại lần sau
      // eslint-disable-next-line no-console
      console.error(`[NẾP] render trang ${n} lỗi:`, err)
    }
  }
  // Render quanh vị trí hiện tại (xem trước vài trang phía sau).
  const renderAround = (idx) => {
    for (let d = -2; d <= 3; d++) renderPage(idx + 1 + d)
  }

  // 5. Điều khiển: prev / next / số trang + phím mũi tên.
  const counter = wrap?.querySelector('.nep-flipbook-page')
  const prev = wrap?.querySelector('[data-flip="prev"]')
  const next = wrap?.querySelector('[data-flip="next"]')

  // Khi mở dạng 2 trang, trang bìa (và trang cuối lẻ) đứng một mình ở nửa
  // phải/trái → dịch sách lại để trang đơn nằm giữa khung.
  const centerSinglePage = () => {
    const idx = flip.getCurrentPageIndex()
    const landscape = flip.getOrientation?.() === 'landscape'
    el.classList.toggle('is-cover', landscape && idx === 0)
    el.classList.toggle('is-back', landscape && idx === total - 1)
  }

  const update = () => {
    const idx = flip.getCurrentPageIndex()
    if (counter) counter.textContent = `${idx + 1} / ${total}`
    renderAround(idx)
    centerSinglePage()
  }
  prev?.addEventListener('click', () => flip.flipPrev())
  next?.addEventListener('click', () => flip.flipNext())
  flip.on('flip', update)
  flip.on('changeOrientation', centerSinglePage)
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
