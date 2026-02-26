/**
 * Lazy Loading для изображений с blur placeholder
 * @package ATK_VED
 */
(function(){'use strict';if(!('IntersectionObserver'in window))return;const obs=new IntersectionObserver((entries,observer)=>{entries.forEach(e=>{if(e.isIntersecting){const img=e.target;const src=img.dataset.src;const srcset=img.dataset.srcset;if(src){img.src=src;img.removeAttribute('data-src')}if(srcset){img.srcset=srcset;img.removeAttribute('data-srcset')}img.addEventListener('load',()=>{img.classList.add('loaded');img.classList.remove('lazy')},{ once:true});observer.unobserve(img)}})},{rootMargin:'50px'});document.querySelectorAll('img.lazy').forEach(img=>obs.observe(img))})();
