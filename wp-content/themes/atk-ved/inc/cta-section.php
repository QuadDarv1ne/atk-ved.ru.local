<?php
/**
 * CTA секция с таймером
 */
declare(strict_types=1);
if (!defined('ABSPATH')) exit;

function atk_ved_cta_timer_shortcode(array $atts): string {
    $atts = shortcode_atts(['title'=>__('Спецпредложение','atk-ved'),'subtitle'=>__('Скидка 15% на первую доставку','atk-ved')], $atts);
    ob_start(); ?>
    <section class="cta-timer-section"><div class="container"><div class="cta-inner">
        <div class="cta-content"><span class="cta-badge">🔥 Ограниченное предложение</span>
            <h2 class="cta-title"><?php echo esc_html($atts['title']); ?></h2>
            <p class="cta-subtitle"><?php echo esc_html($atts['subtitle']); ?></p>
            <div class="cta-timer" id="ctaTimer"><div class="timer-item"><span class="timer-number" id="timerDays">07</span><span class="timer-label">дней</span></div><div class="timer-item"><span class="timer-number" id="timerHours">00</span><span class="timer-label">часов</span></div><div class="timer-item"><span class="timer-number" id="timerMinutes">00</span><span class="timer-label">минут</span></div><div class="timer-item"><span class="timer-number" id="timerSeconds">00</span><span class="timer-label">секунд</span></div></div>
            <div class="cta-benefits"><span>✓ Бесплатная консультация</span><span>✓ Расчёт за 15 минут</span><span>✓ Персональный менеджер</span></div>
        </div>
        <div class="cta-form"><form id="ctaOfferForm" class="cta-offer-form"><h3>Получить скидку</h3>
            <div class="form-group"><input type="text" name="name" placeholder="Ваше имя" required></div>
            <div class="form-group"><input type="tel" name="phone" placeholder="Телефон" required></div>
            <div class="form-group"><input type="email" name="email" placeholder="Email" required></div>
            <button type="submit" class="btn btn-primary btn-full btn-lg">Забрать скидку</button>
            <p class="form-note">🔒 Ваши данные под защитой</p>
        </form></div>
    </div></div></section>
    <style>.cta-timer-section{padding:80px 0;background:linear-gradient(135deg,#e31e24,#c01a1f);position:relative;overflow:hidden}.cta-inner{display:grid;grid-template-columns:1fr 450px;gap:60px;align-items:center;position:relative;z-index:2}.cta-badge{display:inline-block;background:rgba(255,255,255,.2);backdrop-filter:blur(10px);padding:8px 20px;border-radius:30px;font-size:14px;font-weight:600;color:#fff;margin-bottom:20px;animation:pulse 2s infinite}@keyframes pulse{0%,100%{transform:scale(1)}50%{transform:scale(1.05)}}.cta-title{font-size:48px;font-weight:800;color:#fff;margin-bottom:15px;line-height:1.1}.cta-subtitle{font-size:24px;color:rgba(255,255,255,.9);margin-bottom:10px}.cta-timer{display:flex;gap:20px;margin-bottom:30px}.timer-item{text-align:center;background:rgba(255,255,255,.15);backdrop-filter:blur(10px);padding:20px;border-radius:12px;min-width:90px}.timer-number{display:block;font-size:42px;font-weight:800;color:#fff;line-height:1}.timer-label{display:block;font-size:12px;color:rgba(255,255,255,.8);margin-top:8px;text-transform uppercase;letter-spacing:.5px}.cta-benefits{display:flex;flex-direction:column;gap:12px}.cta-benefits span{font-size:16px;color:#fff;font-weight:500}.cta-form{background:#fff;padding:40px;border-radius:20px;box-shadow:0 20px 60px rgba(0,0,0,.3)}.cta-form h3{font-size:24px;font-weight:700;color:#2c2c2c;margin-bottom:25px;text-align:center}.cta-offer-form .form-group{margin-bottom:15px}.cta-offer-form input{width:100%;padding:15px 20px;border:2px solid #e0e0e0;border-radius:10px;font-size:15px;transition:all .3s}.cta-offer-form input:focus{outline:none;border-color:#e31e24;box-shadow:0 0 0 3px rgba(227,30,36,.1)}.cta-offer-form .btn-lg{padding:18px;font-size:16px;margin-top:10px}.form-note{display:flex;align-items:center;justify-content:center;gap:8px;font-size:12px;color:#999;margin-top:15px}@media(max-width:1024px){.cta-inner{grid-template-columns:1fr}.cta-title{font-size:36px}.cta-form{order:-1}}@media(max-width:768px){.cta-timer-section{padding:40px 0}.cta-title{font-size:28px}.cta-subtitle{font-size:18px}.cta-timer{flex-wrap:wrap;justify-content:center}.timer-item{min-width:70px;padding:15px 10px}.timer-number{font-size:28px}.timer-label{font-size:10px}.cta-form{padding:25px}.cta-benefits{flex-direction:row;flex-wrap:wrap}}
    </style>
    <script>(function($){const deadline=new Date().getTime()+(7*24*60*60*1000);function updateTimer(){const now=new Date().getTime(),distance=deadline-now;if(distance<0){$('#ctaTimer').html('<p>Акция завершена</p>');return;}const days=Math.floor(distance/(1000*60*60*24)),hours=Math.floor((distance%(1000*60*60*24))/(1000*60*60)),minutes=Math.floor((distance%(1000*60*60))/(1000*60)),seconds=Math.floor((distance%(1000*60))/1000);$('#timerDays').text(String(days).padStart(2,'0'));$('#timerHours').text(String(hours).padStart(2,'0'));$('#timerMinutes').text(String(minutes).padStart(2,'0'));$('#timerSeconds').text(String(seconds).padStart(2,'0'));}updateTimer();setInterval(updateTimer,1000);$('#ctaOfferForm').on('submit',function(e){e.preventDefault();const $form=$(this),$btn=$form.find('button[type="submit"]'),originalText=$btn.text();$btn.prop('disabled',true).text('Отправка...');$.ajax({url:atkVedData.ajaxUrl,type:'POST',data:$form.serialize()+'&action=atk_ved_cta_offer&nonce='+atkVedData.nonce,success:function(response){if(response.success){$form.html('<div class="success-message"><h3>Заявка отправлена!</h3><p>Менеджер свяжется в течение 15 минут</p></div>');}else{alert(response.data?.message||'Ошибка отправки');$btn.prop('disabled',false).text(originalText);}},error:function(){alert('Ошибка соединения');$btn.prop('disabled',false).text(originalText);}});});})(jQuery);</script>
    <?php return ob_get_clean();
}
add_shortcode('cta_timer', 'atk_ved_cta_timer_shortcode');
