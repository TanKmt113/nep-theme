/**
 * NẾP — sliders (Swiper). Slider trượt ngang cho lưới Dự án (front page + archive).
 * Cảm giác "calm luxury": trượt mượt, chậm, peek nhẹ slide kế tiếp.
 */
import Swiper from 'swiper'
import { Navigation, Pagination, Autoplay, Keyboard, A11y } from 'swiper/modules'
import 'swiper/css'
import 'swiper/css/pagination'

export function initProjectSliders() {
  const reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches
  const sliders = document.querySelectorAll('.nep-proj-swiper')
  sliders.forEach((el) => {
    const slides = el.querySelectorAll('.swiper-slide').length

    const swiper = new Swiper(el, {
      modules: [Navigation, Pagination, Autoplay, Keyboard, A11y],
      slidesPerView: 1.15,
      spaceBetween: 16,
      speed: 800,
      grabCursor: true,
      loop: slides > 3,
      keyboard: { enabled: true },
      autoplay: { delay: 4500, disableOnInteraction: false, pauseOnMouseEnter: true },
      pagination: {
        el: el.querySelector('.nep-proj-swiper__dots'),
        clickable: true,
      },
      navigation: {
        nextEl: el.querySelector('.nep-proj-swiper__next'),
        prevEl: el.querySelector('.nep-proj-swiper__prev'),
      },
      breakpoints: {
        640: { slidesPerView: 2, spaceBetween: 24 },
        1024: { slidesPerView: 3, spaceBetween: 30 },
        1440: { slidesPerView: 3, spaceBetween: 36 },
      },
    })

    // Khi cuộn tới section lần đầu: trượt 1 phát rồi mới chạy auto-trượt.
    if (reduce || slides < 2) return
    swiper.autoplay.stop()
    let revealed = false
    const reveal = () => {
      if (revealed) return
      revealed = true
      setTimeout(() => swiper.slideNext(), 520)
      swiper.autoplay.start()
    }
    if ('IntersectionObserver' in window) {
      const io = new IntersectionObserver((entries) => {
        entries.forEach((e) => {
          if (e.isIntersecting) {
            reveal()
            io.disconnect()
          }
        })
      }, { threshold: 0.35 })
      io.observe(el)
    } else {
      reveal()
    }
  })
}
