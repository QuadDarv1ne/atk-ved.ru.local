/**
 * Core JavaScript - Vanilla JS (без jQuery)
 * @package ATK_VED
 */
(function(){'use strict';const $=(s,c)=>(c||document).querySelector(s);const $$=(s,c)=>Array.from((c||document).querySelectorAll(s));const on=(el,ev,fn)=>el.addEventListener(ev,fn);const off=(el,ev,fn)=>el.removeEventListener(ev,fn);const addClass=(el,cls)=>el.classList.add(cls);const removeClass=(el,cls)=>el.classList.remove(cls);const toggleClass=(el,cls)=>el.classList.toggle(cls);const hasClass=(el,cls)=>el.classList.contains(cls);function smoothScroll(target,offset=70){const el=typeof target==='string'?$(target):target;if(!el)return;const top=el.getBoundingClientRect().top+window.pageYOffset-offset;window.scrollTo({top:top,behavior:'smooth'})}on(document,'click',e=>{const link=e.target.closest('a[href^="#"]');if(!link)return;e.preventDefault();const href=link.getAttribute('href');if(href==='#')return;smoothScroll(href);$$('.main-nav, .menu-toggle').forEach(el=>removeClass(el,'active'))});on(document,'click',e=>{const question=e.target.closest('.faq-question');if(!question)return;const item=question.closest('.faq-item');const answer=question.nextElementSibling;if(hasClass(item,'active')){answer.style.maxHeight='0';removeClass(item,'active')}else{$$('.faq-answer').forEach(a=>a.style.maxHeight='0');$$('.faq-item').forEach(i=>removeClass(i,'active'));answer.style.maxHeight=answer.scrollHeight+'px';addClass(item,'active')}});let ticking=false;function updateHeader(){const header=$('.site-header');if(!header)return;toggleClass(header,'scrolled',window.pageYOffset>50);ticking=false}on(window,'scroll',()=>{if(!ticking){requestAnimationFrame(updateHeader);ticking=true}});if('IntersectionObserver'in window){const observer=new IntersectionObserver(entries=>{entries.forEach(entry=>{if(entry.isIntersecting){addClass(entry.target,'visible');observer.unobserve(entry.target)}})},{threshold:.1,rootMargin:'0px 0px -50px 0px'});$$('.service-card,.step-card,.review-card,.method-card').forEach(el=>observer.observe(el))}const toggle=$('.menu-toggle');const nav=$('.main-nav');if(toggle&&nav){on(toggle,'click',()=>{toggleClass(toggle,'active');toggleClass(nav,'active');toggleClass(document.body,'menu-open')})}on(document,'click',e=>{if(!e.target.closest('.main-nav, .menu-toggle')){$$('.main-nav, .menu-toggle').forEach(el=>removeClass(el,'active'));removeClass(document.body,'menu-open')}});const scrollBtn=document.createElement('button');scrollBtn.className='scroll-to-top';scrollBtn.setAttribute('aria-label','Наверх');scrollBtn.textContent='↑';document.body.appendChild(scrollBtn);on(window,'scroll',()=>toggleClass(scrollBtn,'visible',window.pageYOffset>300));on(scrollBtn,'click',()=>window.scrollTo({top:0,behavior:'smooth'}))})();


// Sticky Header
(function() {
    const header = document.querySelector('.site-header');
    if (!header) return;

    let lastScroll = 0;
    const scrollThreshold = 100;

    window.addEventListener('scroll', () => {
        const currentScroll = window.pageYOffset;

        if (currentScroll > scrollThreshold) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }

        lastScroll = currentScroll;
    }, { passive: true });
})();
