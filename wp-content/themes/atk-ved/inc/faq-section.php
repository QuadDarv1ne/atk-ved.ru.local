<?php
/**
 * FAQ секция с улучшенным аккордеоном
 */
declare(strict_types=1);
if (!defined('ABSPATH')) exit;

function atk_ved_faq_section_enhanced_shortcode(array $atts): string {
    $atts = shortcode_atts(['title' => __('Часто задаваемые вопросы', 'atk-ved'), 'subtitle' => __('Ответы на популярные вопросы', 'atk-ved')], $atts);
    $faqs = [
        ['q' => __('Сколько стоит доставка из Китая?', 'atk-ved'), 'a' => __('Стоимость зависит от веса, объёма и способа доставки. Минимальная стоимость — от $2/кг при ЖД доставке.', 'atk-ved')],
        ['q' => __('Какой минимальный объём заказа?', 'atk-ved'), 'a' => __('Мы не устанавливаем минимальный объём заказа. Работаем с партиями от 10 кг.', 'atk-ved')],
        ['q' => __('Сколько времени занимает доставка?', 'atk-ved'), 'a' => __('Авиа: 5-10 дней, ЖД: 15-20 дней, Авто: 15-25 дней, Море: 30-45 дней.', 'atk-ved')],
        ['q' => __('Как происходит оплата?', 'atk-ved'), 'a' => __('70% предоплата, 30% после проверки. Принимаем карты, счета, криптовалюту.', 'atk-ved')],
        ['q' => __('Вы работаете с юр. лицами?', 'atk-ved'), 'a' => __('Да, работаем с ИП и ООО. Предоставляем полный пакет документов.', 'atk-ved')],
        ['q' => __('Как отследить груз?', 'atk-ved'), 'a' => __('После отправки вы получаете трек-номер. Контроль 24/7 в личном кабинете.', 'atk-ved')],
    ];
    ob_start(); ?>
    <section class="faq-section-enhanced" id="faq"><div class="container">
        <div class="section-header"><h2><?php echo esc_html($atts['title']); ?></h2><p><?php echo esc_html($atts['subtitle']); ?></p></div>
        <div class="faq-grid-enhanced"><?php foreach($faqs as $i => $faq): ?>
        <div class="faq-item-enhanced"><button class="faq-question"><span class="faq-text"><?php echo esc_html($faq['q']); ?></span><span class="faq-icon">+</span></button><div class="faq-answer"><div class="faq-answer-content"><?php echo esc_html($faq['a']); ?></div></div></div>
        <?php endforeach; ?></div>
        <div class="faq-cta"><p><?php _e('Не нашли ответ?', 'atk-ved'); ?></p><a href="#contact" class="btn btn-primary"><?php _e('Задать вопрос', 'atk-ved'); ?></a></div>
    </div></section>
    <style>.faq-section-enhanced{padding:80px 0;background:linear-gradient(135deg,#f8f9fa,#fff)}.section-header{text-align:center;margin-bottom:50px}.section-title{font-size:42px;font-weight:800;color:#2c2c2c;margin-bottom:15px}.section-subtitle{font-size:18px;color:#666}.faq-grid-enhanced{max-width:900px;margin:0 auto 40px}.faq-item-enhanced{background:#fff;border-radius:16px;margin-bottom:15px;box-shadow:0 5px 20px rgba(0,0,0,.08);overflow:hidden;transition:all .3s;border:1px solid #e0e0e0}.faq-item-enhanced:hover{transform:translateY(-3px);box-shadow:0 10px 30px rgba(227,30,36,.15)}.faq-question{width:100%;padding:25px 30px;background:#fff;border:none;cursor:pointer;display:flex;justify-content:space-between;align-items:center;font-size:17px;font-weight:600;color:#2c2c2c}.faq-question:hover{background:rgba(227,30,36,.03)}.faq-icon{width:30px;height:30px;background:linear-gradient(135deg,#e31e24,#ff6b6b);border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;font-size:20px}.faq-item-enhanced.active .faq-icon{transform:rotate(45deg)}.faq-answer{max-height:0;overflow:hidden;transition:max-height .4s cubic-bezier(.4,0,.2,1);background:#fafafa}.faq-item-enhanced.active .faq-answer{max-height:300px}.faq-answer-content{padding:20px 30px;font-size:15px;line-height:1.8;color:#666}.faq-cta{text-align:center;padding:30px;background:linear-gradient(135deg,#e31e24,#c01a1f);border-radius:16px;color:#fff}.faq-cta p{font-size:18px;margin-bottom:20px}.faq-cta .btn{background:#fff;color:#e31e24;padding:15px 40px;font-weight:600}@media(max-width: 768px){.faq-section-enhanced{padding:40px 0}.section-title{font-size:28px}.faq-question{padding:20px;font-size:15px}.faq-answer-content{padding:15px 20px;font-size:14px}.faq-cta{padding:25px}}
    </style>
    <script>(function($){$('.faq-question').on('click',function(){const $item=$(this).closest('.faq-item-enhanced'),isActive=$item.hasClass('active');$('.faq-item-enhanced').removeClass('active').find('.faq-question').attr('aria-expanded','false');if(!isActive){$item.addClass('active');$(this).attr('aria-expanded','true');}});})(jQuery);</script>
    <?php return ob_get_clean();
}
add_shortcode('faq_section_enhanced', 'atk_ved_faq_section_enhanced_shortcode');
