<?php
/**
 * Advanced UI Components
 * Расширенные компоненты пользовательского интерфейса
 *
 * @package ATK_VED
 * @since 2.8.0
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Генерация улучшенной карточки услуги
 *
 * @param array $args
 * @return string
 */
function atk_ved_advanced_service_card( array $args ): string {
	$defaults = array(
		'title'       => '',
		'description' => '',
		'icon'        => '',
		'price'       => '',
		'features'    => array(),
		'button_text' => 'Подробнее',
		'button_url'  => '#',
		'class'       => '',
		'id'          => '',
		'delay'       => 0,
	);

	$args = wp_parse_args( $args, $defaults );

	$card_id     = $args['id'] ? 'id="' . esc_attr( $args['id'] ) . '"' : '';
	$card_class  = 'service-card enhanced-card ' . esc_attr( $args['class'] );
	$delay_style = $args['delay'] > 0 ? 'style="animation-delay: ' . $args['delay'] . 'ms"' : '';

	$output  = '<div ' . $card_id . ' class="' . $card_class . '" ' . $delay_style . '>';
	$output .= '<div class="card-inner enhanced-hover">';

	// Иконка
	if ( $args['icon'] ) {
		$output .= '<div class="card-icon">';
		$output .= '<img src="' . esc_url( $args['icon'] ) . '" alt="' . esc_attr( $args['title'] ) . '" loading="lazy">';
		$output .= '</div>';
	}

	// Заголовок
	if ( $args['title'] ) {
		$output .= '<h3 class="card-title">' . esc_html( $args['title'] ) . '</h3>';
	}

	// Описание
	if ( $args['description'] ) {
		$output .= '<p class="card-description">' . esc_html( $args['description'] ) . '</p>';
	}

	// Цена
	if ( $args['price'] ) {
		$output .= '<div class="card-price">' . esc_html( $args['price'] ) . '</div>';
	}

	// Особенности
	if ( ! empty( $args['features'] ) ) {
		$output .= '<ul class="card-features">';
		foreach ( $args['features'] as $feature ) {
			$output .= '<li class="feature-item">' . esc_html( $feature ) . '</li>';
		}
		$output .= '</ul>';
	}

	// Кнопка
	$output .= '<div class="card-button">';
	$output .= '<a href="' . esc_url( $args['button_url'] ) . '" class="cta-button enhanced-button" data-track data-track-category="service" data-track-action="click" data-track-label="' . esc_attr( $args['title'] ) . '">';
	$output .= esc_html( $args['button_text'] );
	$output .= '</a>';
	$output .= '</div>';

	$output .= '</div>';
	$output .= '</div>';

	return $output;
}

/**
 * Генерация улучшенной формы обратной связи
 *
 * @param array $args
 * @return string
 */
function atk_ved_advanced_contact_form( array $args ): string {
	$defaults = array(
		'title'        => 'Связаться с нами',
		'description'  => 'Оставьте заявку и мы свяжемся с вами в течение 15 минут',
		'form_id'      => 'advanced-contact-form',
		'class'        => '',
		'show_phone'   => true,
		'show_email'   => true,
		'show_message' => true,
		'show_file'    => false,
		'show_consent' => true,
	);

	$args = wp_parse_args( $args, $defaults );

	$form_class = 'advanced-contact-form enhanced-form ' . esc_attr( $args['class'] );

	$output  = '<div class="contact-form-wrapper enhanced-hover">';
	$output .= '<div class="form-header">';

	if ( $args['title'] ) {
		$output .= '<h3 class="form-title">' . esc_html( $args['title'] ) . '</h3>';
	}

	if ( $args['description'] ) {
		$output .= '<p class="form-description">' . esc_html( $args['description'] ) . '</p>';
	}

	$output .= '</div>';

	$output .= '<form id="' . esc_attr( $args['form_id'] ) . '" class="' . $form_class . '" method="post" action="">';

	// Скрытое поле для безопасности
	$output .= wp_nonce_field( 'atk_ved_contact_form', 'atk_ved_nonce', true, false );
	$output .= '<input type="hidden" name="action" value="atk_ved_advanced_contact">';

	// Имя
	$output .= '<div class="form-group">';
	$output .= '<label for="advanced_name">Имя *</label>';
	$output .= '<input type="text" id="advanced_name" name="name" required minlength="2" maxlength="50" class="form-input enhanced-input" placeholder="Ваше имя">';
	$output .= '<span class="input-icon"><svg><use href="#user-icon"></use></svg></span>';
	$output .= '</div>';

	// Телефон
	if ( $args['show_phone'] ) {
		$output .= '<div class="form-group">';
		$output .= '<label for="advanced_phone">Телефон *</label>';
		$output .= '<input type="tel" id="advanced_phone" name="phone" required pattern="[\+]?[0-9\s\-\(\)]{10,20}" class="form-input enhanced-input" placeholder="+7 (999) 123-45-67">';
		$output .= '<span class="input-icon"><svg><use href="#phone-icon"></use></svg></span>';
		$output .= '</div>';
	}

	// Email
	if ( $args['show_email'] ) {
		$output .= '<div class="form-group">';
		$output .= '<label for="advanced_email">Email</label>';
		$output .= '<input type="email" id="advanced_email" name="email" class="form-input enhanced-input" placeholder="your@email.com">';
		$output .= '<span class="input-icon"><svg><use href="#email-icon"></use></svg></span>';
		$output .= '</div>';
	}

	// Сообщение
	if ( $args['show_message'] ) {
		$output .= '<div class="form-group">';
		$output .= '<label for="advanced_message">Сообщение</label>';
		$output .= '<textarea id="advanced_message" name="message" rows="4" maxlength="1000" class="form-input enhanced-input" placeholder="Расскажите о вашем проекте..."></textarea>';
		$output .= '<span class="input-icon textarea-icon"><svg><use href="#message-icon"></use></svg></span>';
		$output .= '</div>';
	}

	// Файл
	if ( $args['show_file'] ) {
		$output .= '<div class="form-group">';
		$output .= '<label for="advanced_file">Прикрепить файл</label>';
		$output .= '<input type="file" id="advanced_file" name="attachment" accept=".pdf,.doc,.docx,.jpg,.png" class="form-input enhanced-input file-input">';
		$output .= '<span class="input-icon"><svg><use href="#file-icon"></use></svg></span>';
		$output .= '<small class="file-hint">PDF, DOC, DOCX, JPG, PNG (до 10 МБ)</small>';
		$output .= '</div>';
	}

	// Согласие на обработку данных
	if ( $args['show_consent'] ) {
		$output .= '<div class="form-group consent-group">';
		$output .= '<label class="checkbox-label">';
		$output .= '<input type="checkbox" name="consent" required class="form-checkbox">';
		$output .= '<span class="checkmark"></span>';
		$output .= 'Я согласен на <a href="/privacy-policy" target="_blank">обработку персональных данных</a>';
		$output .= '</label>';
		$output .= '</div>';
	}

	// Кнопка отправки
	$output .= '<div class="form-actions">';
	$output .= '<button type="submit" class="submit-button cta-button enhanced-button">';
	$output .= '<span class="button-text">Отправить заявку</span>';
	$output .= '<span class="button-spinner"></span>';
	$output .= '</button>';
	$output .= '<div class="form-security">🔒 Ваши данные защищены</div>';
	$output .= '</div>';

	$output .= '</form>';
	$output .= '</div>';

	return $output;
}

/**
 * Генерация улучшенного слайдера отзывов
 *
 * @param array $args
 * @return string
 */
function atk_ved_advanced_testimonials_slider( array $args ): string {
	$defaults = array(
		'title'        => 'Отзывы клиентов',
		'testimonials' => array(),
		'autoplay'     => true,
		'interval'     => 5000,
		'show_rating'  => true,
		'class'        => '',
	);

	$args = wp_parse_args( $args, $defaults );

	if ( empty( $args['testimonials'] ) ) {
		return '';
	}

	$slider_class  = 'testimonials-slider enhanced-slider ' . esc_attr( $args['class'] );
	$autoplay_attr = $args['autoplay'] ? 'data-autoplay="true" data-interval="' . $args['interval'] . '"' : '';

	$output = '<div class="testimonials-section">';

	if ( $args['title'] ) {
		$output .= '<h2 class="section-title">' . esc_html( $args['title'] ) . '</h2>';
	}

	$output .= '<div class="' . $slider_class . '" ' . $autoplay_attr . '>';
	$output .= '<div class="slider-container">';
	$output .= '<div class="slider-wrapper">';

	foreach ( $args['testimonials'] as $index => $testimonial ) {
		$output .= '<div class="slider-slide' . ( $index === 0 ? ' active' : '' ) . '">';
		$output .= '<div class="testimonial-card enhanced-card enhanced-hover">';

		// Рейтинг
		if ( $args['show_rating'] && ! empty( $testimonial['rating'] ) ) {
			$output .= '<div class="testimonial-rating">';
			for ( $i = 1; $i <= 5; $i++ ) {
				$star_class = $i <= $testimonial['rating'] ? 'filled' : 'empty';
				$output    .= '<span class="star ' . $star_class . '">★</span>';
			}
			$output .= '</div>';
		}

		// Текст отзыва
		if ( ! empty( $testimonial['text'] ) ) {
			$output .= '<p class="testimonial-text">' . esc_html( $testimonial['text'] ) . '</p>';
		}

		// Автор
		$output .= '<div class="testimonial-author">';
		if ( ! empty( $testimonial['author'] ) ) {
			$output .= '<div class="author-name">' . esc_html( $testimonial['author'] ) . '</div>';
		}
		if ( ! empty( $testimonial['position'] ) ) {
			$output .= '<div class="author-position">' . esc_html( $testimonial['position'] ) . '</div>';
		}
		if ( ! empty( $testimonial['company'] ) ) {
			$output .= '<div class="author-company">' . esc_html( $testimonial['company'] ) . '</div>';
		}
		$output .= '</div>';

		$output .= '</div>';
		$output .= '</div>';
	}

	$output .= '</div>';

	// Навигация
	$output .= '<button class="slider-nav slider-prev" aria-label="Предыдущий отзыв">';
	$output .= '<svg><use href="#arrow-left"></use></svg>';
	$output .= '</button>';
	$output .= '<button class="slider-nav slider-next" aria-label="Следующий отзыв">';
	$output .= '<svg><use href="#arrow-right"></use></svg>';
	$output .= '</button>';

	// Индикаторы
	$output .= '<div class="slider-indicators">';
	foreach ( $args['testimonials'] as $index => $testimonial ) {
		$active_class = $index === 0 ? ' active' : '';
		$output      .= '<button class="indicator' . $active_class . '" data-slide="' . $index . '" aria-label="Перейти к слайду ' . ( $index + 1 ) . '"></button>';
	}
	$output .= '</div>';

	$output .= '</div>';
	$output .= '</div>';

	return $output;
}

/**
 * Генерация улучшенного счетчика статистики
 *
 * @param array $args
 * @return string
 */
function atk_ved_advanced_statistics( array $args ): string {
	$defaults = array(
		'title'  => 'Наша статистика',
		'stats'  => array(),
		'class'  => '',
		'layout' => 'grid', // grid, horizontal
	);

	$args = wp_parse_args( $args, $defaults );

	if ( empty( $args['stats'] ) ) {
		return '';
	}

	$section_class = 'statistics-section enhanced-section layout-' . esc_attr( $args['layout'] ) . ' ' . esc_attr( $args['class'] );

	$output = '<div class="' . $section_class . '">';

	if ( $args['title'] ) {
		$output .= '<h2 class="section-title">' . esc_html( $args['title'] ) . '</h2>';
	}

	$output .= '<div class="stats-container optimized-grid">';

	foreach ( $args['stats'] as $stat ) {
		$output .= '<div class="stat-item enhanced-card enhanced-hover" data-count="' . esc_attr( $stat['number'] ) . '">';
		$output .= '<div class="stat-icon">';
		if ( ! empty( $stat['icon'] ) ) {
			$output .= '<img src="' . esc_url( $stat['icon'] ) . '" alt="" loading="lazy">';
		}
		$output .= '</div>';
		$output .= '<div class="stat-content">';
		$output .= '<div class="stat-number" data-target="' . esc_attr( $stat['number'] ) . '">0</div>';
		$output .= '<div class="stat-label">' . esc_html( $stat['label'] ) . '</div>';
		if ( ! empty( $stat['description'] ) ) {
			$output .= '<div class="stat-description">' . esc_html( $stat['description'] ) . '</div>';
		}
		$output .= '</div>';
		$output .= '</div>';
	}

	$output .= '</div>';
	$output .= '</div>';

	return $output;
}

/**
 * Генерация улучшенного аккордеона FAQ
 *
 * @param array $args
 * @return string
 */
function atk_ved_advanced_faq_accordion( array $args ): string {
	$defaults = array(
		'title'          => 'Часто задаваемые вопросы',
		'questions'      => array(),
		'class'          => '',
		'allow_multiple' => false,
	);

	$args = wp_parse_args( $args, $defaults );

	if ( empty( $args['questions'] ) ) {
		return '';
	}

	$accordion_class = 'faq-accordion enhanced-accordion ' . esc_attr( $args['class'] );
	$multiple_attr   = $args['allow_multiple'] ? 'data-multiple="true"' : '';

	$output = '<div class="faq-section enhanced-section">';

	if ( $args['title'] ) {
		$output .= '<h2 class="section-title">' . esc_html( $args['title'] ) . '</h2>';
	}

	$output .= '<div class="' . $accordion_class . '" ' . $multiple_attr . '>';

	foreach ( $args['questions'] as $index => $faq ) {
		$item_id = 'faq-item-' . $index;
		$output .= '<div class="faq-item enhanced-card">';
		$output .= '<button class="faq-question enhanced-button" aria-expanded="false" aria-controls="' . $item_id . '">';
		$output .= '<span class="question-text">' . esc_html( $faq['question'] ) . '</span>';
		$output .= '<span class="faq-toggle">';
		$output .= '<svg class="icon-plus"><use href="#plus-icon"></use></svg>';
		$output .= '<svg class="icon-minus"><use href="#minus-icon"></use></svg>';
		$output .= '</span>';
		$output .= '</button>';
		$output .= '<div id="' . $item_id . '" class="faq-answer" role="region" aria-hidden="true">';
		$output .= '<div class="answer-content">' . wp_kses_post( $faq['answer'] ) . '</div>';
		$output .= '</div>';
		$output .= '</div>';
	}

	$output .= '</div>';
	$output .= '</div>';

	return $output;
}

/**
 * Генерация улучшенной карточки команды
 *
 * @param array $args
 * @return string
 */
function atk_ved_advanced_team_member( array $args ): string {
	$defaults = array(
		'name'         => '',
		'position'     => '',
		'photo'        => '',
		'bio'          => '',
		'social_links' => array(),
		'class'        => '',
		'id'           => '',
	);

	$args = wp_parse_args( $args, $defaults );

	$member_id    = $args['id'] ? 'id="' . esc_attr( $args['id'] ) . '"' : '';
	$member_class = 'team-member enhanced-card enhanced-hover ' . esc_attr( $args['class'] );

	$output = '<div ' . $member_id . ' class="' . $member_class . '">';

	// Фото
	if ( $args['photo'] ) {
		$output .= '<div class="member-photo">';
		$output .= '<img src="' . esc_url( $args['photo'] ) . '" alt="' . esc_attr( $args['name'] ) . '" loading="lazy">';
		$output .= '<div class="photo-overlay">';
		$output .= '<div class="social-links">';

		foreach ( $args['social_links'] as $social ) {
			$output .= '<a href="' . esc_url( $social['url'] ) . '" target="_blank" rel="noopener" class="social-link" aria-label="' . esc_attr( $social['name'] ) . '">';
			$output .= '<svg><use href="#' . esc_attr( $social['icon'] ) . '"></use></svg>';
			$output .= '</a>';
		}

		$output .= '</div>';
		$output .= '</div>';
		$output .= '</div>';
	}

	// Информация
	$output .= '<div class="member-info">';
	if ( $args['name'] ) {
		$output .= '<h3 class="member-name">' . esc_html( $args['name'] ) . '</h3>';
	}
	if ( $args['position'] ) {
		$output .= '<div class="member-position">' . esc_html( $args['position'] ) . '</div>';
	}
	if ( $args['bio'] ) {
		$output .= '<p class="member-bio">' . esc_html( $args['bio'] ) . '</p>';
	}
	$output .= '</div>';

	$output .= '</div>';

	return $output;
}
