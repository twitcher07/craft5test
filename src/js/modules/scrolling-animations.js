import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
gsap.registerPlugin(ScrollTrigger);


// Default options
let options = {
    once: false
}
let scroll_animations = [];
const scroll_animation_items = document.querySelectorAll('[data-scroll-animation]');

function getMutationObserver() {
  return window.MutationObserver ||
    window.WebKitMutationObserver ||
    window.MozMutationObserver;
}

function isSupported() {
  return !!getMutationObserver();
}

function create(settings) {
    options = Object.assign(options, settings);

    scroll_animation_items.forEach((element, index) => {

        let default_animation_start = { autoAlpha: 0 };
        let default_animation_end = { autoAlpha: 1, duration: 0.65, ease: 'back.out(1.4)', clearProps: 'all' };
        let animation_start = {};
        let animation_end = {};

        const type = element.getAttribute('data-scroll-animation').trim();
        const duration = element.getAttribute('data-scroll-animation-duration');
        const offset = element.getAttribute('data-scroll-animation-offset');
        const delay = element.getAttribute('data-scroll-animation-delay');
        const once = element.getAttribute('data-scroll-animation-once');
        const clamo = element.getAttribute('data-scroll-animation-clamp');
        const trigger = element.getAttribute('data-scroll-animation-trigger') || element;


        if(type === 'fade-right') {
            animation_start = { x: '-50px' };
            animation_end = { x: 0 };
        }

        if(type === 'fade-left') {
            animation_start = { x: '50px' };
            animation_end = { x: 0 };
        }

        if(type === 'fade-up') {
            animation_start = { y: '50px' };
            animation_end = { y: 0 };
        }

        if(type === 'fade-down') {
            animation_start = { y: '-50px' };
            animation_end = { y: 0 };
        }

        if(duration) {
            default_animation_end.duration = duration / 1000; // duration in milliseconds
        }

        if(delay) {
            default_animation_end.delay = delay / 1000; // delay in milliseconds
        }

        const tl = gsap.timeline({
            smoothChildTiming: true,
            scrollTrigger: {
                id: `scroll-animation-${index}`,
                trigger: trigger,
                start: (/%$/.test(offset)) ? `${offset} bottom` : offset ? `top bottom-=${offset}px` : 'top bottom-=100px',
                end: 'bottom bottom-=100px',
                // markers: true,
                toggleActions: once || options.once ? 'play none none none' : 'play none none reverse',
            }
        });

        tl.fromTo(element, { ...animation_start, ...default_animation_start }, {
            ...animation_end,
            ...default_animation_end,
        });

        // console.log('scrolltrigger scroll animation:', tl, offset)

        scroll_animations.push(tl);

    });

    window.scroll_animations = scroll_animations;

}

export default { create };
