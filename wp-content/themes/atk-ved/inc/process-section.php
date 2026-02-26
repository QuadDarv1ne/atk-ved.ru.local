<?php
/**
 * Процесс работы (шаги)
 */
declare(strict_types=1);
if (!defined('ABSPATH')) exit;

function atk_ved_process_steps_shortcode(array $atts): string {
    $atts = shortcode_atts(['title'=>__('Как мы работаем','atk-ved'),'subtitle'=>__('7 шагов к успеху','atk-ved')], $atts);
    $steps = [['num'=>'01','title'=>__('Заявка','atk-ved'),'text'=>__('Оставляете заявку или звоните','atk-ved'),'icon'=>'📞'],['num'=>'02','title'=>__('Расчёт','atk-ved'),'text'=>__('Рассчитываем стоимость и сроки','atk-ved'),'icon'=>'📊'],['num'=>'03','title'=>__('Договор','atk-ved'),'text'=>__('Заключаем официальный договор','atk-ved'),'icon'=>'📋'],['num'=>'04','title'=>__('Поиск','atk-ved'),'text'=>__('Находим поставщика, выкупаем товар','atk-ved'),'icon'=>'🔍'],['num'=>'05','title'=>__('Доставка','atk-ved'),'text'=>__('Упаковываем и отправляем','atk-ved'),'icon'=>'🚢'],['num'=>'06','title'=>__('Таможня','atk-ved'),'text'=>__('Проходим таможенное оформление','atk-ved'),'icon'=>'✓'],['num'=>'07','title'=>__('Получение','atk-ved'),'text'=>__('Доставляем до вашего склада','atk-ved'),'icon'=>'📦']];
    ob_start(); ?>
    <section class="process-steps-section" id="process"><div class="container">
        <div class="section-header"><h2 class="section-title"><?php echo esc_html($atts['title']); ?></h2><p class="section-subtitle"><?php echo esc_html($atts['subtitle']); ?></p></div>
        <div class="process-timeline">
        <?php foreach($steps as $s): ?>
            <div class="process-step"><div class="step-marker"><span class="step-num"><?php echo $s['num']; ?></span></div><div class="step-content"><div class="step-icon"><?php echo $s['icon']; ?></div><h3 class="step-title"><?php echo esc_html($s['title']); ?></h3><p class="step-text"><?php echo esc_html($s['text']); ?></p></div></div>
        <?php endforeach; ?>
        </div>
    </div></section>
    <style>.process-steps-section{padding:80px 0;background:linear-gradient(135deg,#fff,#f8f9fa)}.section-header{text-align:center;margin-bottom:60px}.section-title{font-size:42px;font-weight:800;color:#2c2c2c;margin-bottom:15px}.section-subtitle{font-size:18px;color:#666}.process-timeline{position:relative;max-width:1000px;margin:0 auto}.process-timeline::before{content:'';position:absolute;left:50px;top:0;bottom:0;width:3px;background:linear-gradient(180deg,#e31e24,#ff6b6b)}.process-step{display:flex;gap:30px;margin-bottom:40px;position:relative;opacity:0;transform:translateY(30px);transition:all .6s cubic-bezier(.4,0,.2,1)}.process-step.visible{opacity:1;transform:translateY(0)}.step-marker{flex-shrink:0;width:100px;text-align:center;position:relative;z-index:2}.step-num{display:inline-flex;align-items:center;justify-content:center;width:100px;height:100px;background:linear-gradient(135deg,#e31e24,#ff6b6b);border-radius:50%;font-size:32px;font-weight:800;color:#fff;box-shadow:0 10px 30px rgba(227,30,36,.3)}.step-content{flex:1;background:#fff;padding:30px;border-radius:16px;box-shadow:0 5px 20px rgba(0,0,0,.08);border:1px solid #e0e0e0;transition:all .3s}.process-step:hover .step-content{transform:translateX(10px);box-shadow:0 10px 40px rgba(0,0,0,.12);border-color:rgba(227,30,36,.3)}.step-icon{font-size:40px;margin-bottom:15px}.step-title{font-size:22px;font-weight:700;color:#2c2c2c;margin-bottom:12px}.step-text{font-size:15px;line-height:1.7;color:#666}@media(max-width:768px){.process-steps-section{padding:40px 0}.section-title{font-size:28px}.process-timeline::before{left:35px}.process-step{flex-direction:column}.step-marker{width:70px}.step-num{width:70px;height:70px;font-size:24px}.step-content{margin-left:20px;padding:20px}.step-title{font-size:18px}.step-text{font-size:14px}}
    </style>
    <script>(function($){const observer=new IntersectionObserver(entries=>{entries.forEach(entry=>{if(entry.isIntersecting){entry.target.classList.add('visible');observer.unobserve(entry.target);}});},{threshold:0.2});$('.process-step').each((i,el)=>{observer.observe(el);});})(jQuery);</script>
    <?php return ob_get_clean();
}
add_shortcode('process_steps', 'atk_ved_process_steps_shortcode');
