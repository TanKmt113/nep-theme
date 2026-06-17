/**
 * NẾP — gợi ý tìm kiếm realtime.
 * Mỗi .nep-search-box gọi admin-ajax (action=nep_search_suggest) khi gõ,
 * render dropdown gợi ý. Có debounce, điều hướng bàn phím (↑/↓/Enter/Esc).
 */
export function initSearchSuggest() {
  document.querySelectorAll('.nep-search-box').forEach(setup)
}

function setup(box) {
  const input = box.querySelector('.nep-search-form__input')
  const panel = box.querySelector('.nep-search-suggest')
  const endpoint = box.dataset.suggest
  if (!input || !panel || !endpoint) return

  let timer = null
  let active = -1
  let items = []
  let lastQ = ''

  const close = () => {
    panel.hidden = true
    panel.innerHTML = ''
    active = -1
    items = []
    input.setAttribute('aria-expanded', 'false')
  }

  const render = (list, q) => {
    if (!list.length) {
      panel.innerHTML = `<div class="nep-search-suggest__empty">Không có gợi ý cho “${esc(q)}”.</div>`
      panel.hidden = false
      return
    }
    panel.innerHTML =
      list
        .map(
          (it, i) => `
        <a class="nep-search-suggest__item" role="option" href="${esc(it.url)}" data-i="${i}">
          <span class="nep-search-suggest__thumb">${
            it.thumb ? `<img src="${esc(it.thumb)}" alt="" loading="lazy">` : ''
          }</span>
          <span class="nep-search-suggest__text">
            <span class="nep-search-suggest__title">${esc(it.title)}</span>
            ${it.type ? `<span class="nep-search-suggest__type">${esc(it.type)}</span>` : ''}
          </span>
        </a>`
        )
        .join('') +
      `<button type="button" class="nep-search-suggest__all" data-all>Xem tất cả kết quả cho “${esc(q)}” →</button>`
    panel.hidden = false
    input.setAttribute('aria-expanded', 'true')
  }

  const fetchSuggest = (q) => {
    const url = `${endpoint}?action=nep_search_suggest&q=${encodeURIComponent(q)}`
    fetch(url, { credentials: 'same-origin' })
      .then((r) => r.json())
      .then((res) => {
        if (input.value.trim() !== q) return // người dùng đã gõ tiếp
        items = (res && res.data && res.data.items) || []
        active = -1
        render(items, q)
      })
      .catch(() => close())
  }

  input.addEventListener('input', () => {
    const q = input.value.trim()
    if (q === lastQ) return
    lastQ = q
    clearTimeout(timer)
    if (q.length < 2) return close()
    timer = setTimeout(() => fetchSuggest(q), 220)
  })

  input.addEventListener('keydown', (e) => {
    const links = panel.querySelectorAll('.nep-search-suggest__item')
    if (panel.hidden || !links.length) return
    if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
      e.preventDefault()
      active = e.key === 'ArrowDown' ? (active + 1) % links.length : (active - 1 + links.length) % links.length
      links.forEach((l, i) => l.classList.toggle('is-active', i === active))
      links[active].scrollIntoView({ block: 'nearest' })
    } else if (e.key === 'Enter') {
      if (active >= 0 && links[active]) {
        e.preventDefault()
        window.location.href = links[active].href
      }
    } else if (e.key === 'Escape') {
      close()
    }
  })

  // Click "Xem tất cả" → submit form thường.
  panel.addEventListener('click', (e) => {
    if (e.target.closest('[data-all]')) box.querySelector('form')?.submit()
  })

  // Đóng khi click ra ngoài / blur.
  document.addEventListener('click', (e) => {
    if (!box.contains(e.target)) close()
  })
}

function esc(s) {
  return String(s).replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]))
}
