/**
 * Web Share API
 * @package ATK_VED
 */
function sharePage(){if(navigator.share){navigator.share({title:document.title,text:document.querySelector('meta[name="description"]')?.content||'',url:window.location.href}).catch(console.error)}else{navigator.clipboard.writeText(window.location.href);alert('Ссылка скопирована в буфер обмена!')}}
