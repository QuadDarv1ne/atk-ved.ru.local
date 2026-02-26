<?php
/**
 * Отзывы клиентов - слайдер
 */
declare(strict_types=1);
if (!defined('ABSPATH')) exit;

function atk_ved_reviews_slider_shortcode(array $atts): string {
    $atts = shortcode_atts(['title' => __('Отзывы клиентов', 'atk-ved'), 'subtitle' => __('Реальные истории', 'atk-ved')], $atts);
    $reviews = [
        ['name'=>'Александр П.','company'=>'Wildberries','text'=>'Работаем больше года. Ни одной задержки. Менеджеры всегда на связи.','rating'=>5],
        ['name'=>'Елена С.','company'=>'Ozon','text'=>'Смогла масштабировать бизнес в 5 раз за полгода благодаря АТК ВЭД.','rating'=>5],
        ['name'=>'Дмитрий В.','company'=>'Мегамаркет','text'=>'Проверка качества реально экономит деньги. Нашли брак до отправки.','rating'=>5],
        ['name'=>'Анна К.','company'=>'AliExpress','text'=>'Все товары приходят в целости. Цены адекватные, без скрытых платежей.','rating'=>5],
        ['name'=>'Игорь М.','company'=>'Сеть магазинов','text'=>'За 2 года не было проблем с таможней. Надёжный партнёр.','rating'=>5],
        ['name'=>'Ольга Н.','company'=>'Ozon','text'=>'Персональный менеджер всегда помогает. Сроки соблюдаются.','rating'=>5],
    ];
    ob_start(); ?>
    <section class="reviews-section-enhanced"><div class="container">
        <div class="section-header"><h2><?php echo esc_html($atts['title']); ?></h2><p><?php echo esc_html($atts['subtitle']); ?></p></div>
        <div class="reviews-slider-enhanced"><div class="reviews-track">
        <?php foreach($reviews as $r): ?>
            <div class="review-card-enhanced"><div class="review-header"><div class="review-avatar">👤</div><div class="review-author"><h4><?php echo esc_html($r['name']); ?></h4><p><?php echo esc_html($r['company']); ?></p></div></div>
            <div class="review-rating"><?php echo str_repeat('<span class="star">★</span>',$r['rating']); ?></div>
            <div class="review-text"><?php echo esc_html($r['text']); ?></div>
            <div class="review-meta"><span class="review-date"><?php echo date_i18n('j F Y'); ?></span><span class="review-verified">✓ Проверенный</span></div></div>
        <?php endforeach; ?>
        </div><div class="slider-nav"><button class="slider-btn prev">←</button><div class="slider-dots"></div><button class="slider-btn next">→</button></div></div>
    </div></section>
    <style>.reviews-section-enhanced{padding:80px 0;background:#fff}.section-header{text-align:center;margin-bottom:50px}.reviews-slider-enhanced{position:relative;overflow:hidden;padding:20px 0}.reviews-track{display:flex;gap:30px;transition:transform .5s cubic-bezier(.4,0,.2,1)}.review-card-enhanced{min-width:calc(33.333% - 20px);background:linear-gradient(135deg,#f8f9fa,#fff);border-radius:20px;padding:30px;box-shadow:0 5px 20px rgba(0,0,0,.08);border:1px solid #e0e0e0;transition:all .3s}.review-card-enhanced:hover{transform:translateY(-5px);box-shadow:0 15px 40px rgba(0,0,0,.12)}.review-header{display:flex;align-items:center;gap:15px;margin-bottom:20px}.review-avatar{width:60px;height:60px;background:linear-gradient(135deg,#e31e24,#ff6b6b);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:28px}.review-author h4{font-size:17px;font-weight:700;color:#2c2c2c;margin:0 0 5px}.review-author p{font-size:13px;color:#666;margin:0}.review-rating{display:flex;gap:3px;margin-bottom:15px}.review-rating .star{color:#ffc107;font-size:20px}.review-text{font-size:15px;line-height:1.7;color:#666;margin-bottom:20px;font-style:italic}.review-meta{display:flex;justify-content:space-between;padding-top:15px;border-top:1px solid #e0e0e0;font-size:12px;color:#999}.review-verified{color:#4caf50;font-weight:600}.slider-nav{display:flex;align-items:center;justify-content:center;gap:20px;margin-top:40px}.slider-btn{width:50px;height:50px;border-radius:50%;background:#fff;border:2px solid #e0e0e0;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:all .3s}.slider-btn:hover{background:#e31e24;border-color:#e31e24;color:#fff;transform:scale(1.1)}.slider-dots{display:flex;gap:10px}.slider-dot{width:12px;height:12px;border-radius:50%;background:#e0e0e0;cursor:pointer;transition:all .3s}.slider-dot.active{background:#e31e24;transform:scale(1.2)}@media(max-width:1024px){.review-card-enhanced{min-width:calc(50% - 15px)}}@media(max-width:768px){.reviews-section-enhanced{padding:40px 0}.review-card-enhanced{min-width:100%}.section-title{font-size:28px}}
    </style>
    <script>(function($){const $track=$('#reviewsTrack'),$cards=$('.review-card-enhanced'),$dots=$('#sliderDots');let currentSlide=0,cardsPerView=3;if($(window).width()<=768)cardsPerView=1;else if($(window).width()<=1024)cardsPerView=2;const totalSlides=Math.ceil($cards.length/cardsPerView);for(let i=0;i<totalSlides;i++)$dots.append('<span class="slider-dot'+(i===0?' active':'')+'" data-slide="'+i+'"></span>');function updateSlider(){$track.css('transform','translateX(-'+(currentSlide*$cards.outerWidth(true)*cardsPerView)+'px)');$('.slider-dot').removeClass('active').eq(currentSlide).addClass('active');}$('.slider-btn.prev').on('click',function(){currentSlide=(currentSlide-1+totalSlides)%totalSlides;updateSlider();});$('.slider-btn.next').on('click',function(){currentSlide=(currentSlide+1)%totalSlides;updateSlider();});$dots.on('click','.slider-dot',function(){currentSlide=parseInt($(this).data('slide'));updateSlider();});let autoplay=setInterval(function(){currentSlide=(currentSlide+1)%totalSlides;updateSlider();},5000);$('.reviews-slider-enhanced').on('mouseenter',function(){clearInterval(autoplay);}).on('mouseleave',function(){autoplay=setInterval(function(){currentSlide=(currentSlide+1)%totalSlides;updateSlider();},5000);});$(window).on('resize',function(){if($(window).width()<=768)cardsPerView=1;else if($(window).width()<=1024)cardsPerView=2;else cardsPerView=3;currentSlide=0;updateSlider();});})(jQuery);</script>
    <?php return ob_get_clean();
}
add_shortcode('reviews_slider', 'atk_ved_reviews_slider_shortcode');
