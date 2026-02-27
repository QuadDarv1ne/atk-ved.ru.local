// Viewport Height Fix for iOS
(function(){function setVH(){const vh=window.innerHeight*0.01;document.documentElement.style.setProperty('--vh',vh+'px')}setVH();let t;window.addEventListener('resize',()=>{clearTimeout(t);t=setTimeout(setVH,100)},{passive:true});window.addEventListener('orientationchange',()=>setTimeout(setVH,100))})();
