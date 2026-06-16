/**
 * NẾP — sliders (Swiper). Slider trượt ngang cho lưới Dự án (front page + archive).
 * Cảm giác "calm luxury": trượt mượt, chậm, peek nhẹ slide kế tiếp.
 */
import Swiper from 'swiper'
import { Navigation, Pagination, Autoplay, Keyboard, A11y } from 'swiper/modules'
import 'swiper/css'
import 'swiper/css/pagination'

export function initProjectSliders() {
  const sliders = document.querySelectorAll('.nep-proj-swiper')
  sliders.forEach((el) => {
    const slides = el.querySelectorAll('.swiper-slide').length

    // eslint-disable-next-line no-new
    new Swiper(el, {
      modules: [Navigation, Pagination, Autoplay, Keyboard, A11y],
      slidesPerView: 1.1,
      spaceBetween: 18,
      speed: 750,
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
        640: { slidesPerView: 1.5, spaceBetween: 24 },
        900: { slidesPerView: 2.2, spaceBetween: 28 },
        1280: { slidesPerView: 2.5, spaceBetween: 32 },
      },
    })
  })
}
