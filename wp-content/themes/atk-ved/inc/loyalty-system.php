<?php
/**
 * Loyalty Points & Rewards System
 * Система лояльности с баллами и наградами
 *
 * @package ATK_VED
 * @since 3.4.0
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Создание таблиц системы лояльности
 */
function atk_ved_create_loyalty_tables(): void {
	global $wpdb;

	$charset_collate = $wpdb->get_charset_collate();

	// Таблица балансов пользователей
	$balance_table = $wpdb->prefix . 'atk_ved_loyalty_balance';

	$sql_balance = "CREATE TABLE IF NOT EXISTS {$balance_table} (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        user_id bigint(20) NOT NULL UNIQUE,
        balance bigint(20) DEFAULT 0,
        lifetime_earned bigint(20) DEFAULT 0,
        lifetime_spent bigint(20) DEFAULT 0,
        tier varchar(20) DEFAULT 'bronze',
        last_updated datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY user_id (user_id),
        KEY tier (tier)
    ) {$charset_collate};";

	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	dbDelta( $sql_balance );

	// Таблица истории операций
	$history_table = $wpdb->prefix . 'atk_ved_loyalty_history';

	$sql_history = "CREATE TABLE IF NOT EXISTS {$history_table} (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        user_id bigint(20) NOT NULL,
        points bigint(20) NOT NULL,
        type varchar(20) NOT NULL,
        description text,
        reference_type varchar(50) DEFAULT '',
        reference_id bigint(20) DEFAULT 0,
        balance_after bigint(20) DEFAULT 0,
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY user_id (user_id),
        KEY type (type),
        KEY created_at (created_at)
    ) {$charset_collate};";

	dbDelta( $sql_history );

	// Таблица уровней лояльности
	$tiers_table = $wpdb->prefix . 'atk_ved_loyalty_tiers';

	$sql_tiers = "CREATE TABLE IF NOT EXISTS {$tiers_table} (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        name varchar(50) NOT NULL,
        slug varchar(50) NOT NULL UNIQUE,
        min_points bigint(20) DEFAULT 0,
        discount_percent decimal(5,2) DEFAULT 0,
        benefits text,
        PRIMARY KEY (id),
        KEY min_points (min_points)
    ) {$charset_collate};";

	dbDelta( $sql_tiers );

	// Создаем уровни по умолчанию
	$existing_tiers = $wpdb->get_var( "SELECT COUNT(*) FROM {$tiers_table}" );

	if ( $existing_tiers == 0 ) {
		$wpdb->insert(
			$tiers_table,
			array(
				'name'             => 'Бронзовый',
				'slug'             => 'bronze',
				'min_points'       => 0,
				'discount_percent' => 0,
				'benefits'         => 'Базовый уровень',
			)
		);
		$wpdb->insert(
			$tiers_table,
			array(
				'name'             => 'Серебряный',
				'slug'             => 'silver',
				'min_points'       => 1000,
				'discount_percent' => 3,
				'benefits'         => 'Скидка 3% на все товары',
			)
		);
		$wpdb->insert(
			$tiers_table,
			array(
				'name'             => 'Золотой',
				'slug'             => 'gold',
				'min_points'       => 5000,
				'discount_percent' => 5,
				'benefits'         => 'Скидка 5% + бесплатная доставка',
			)
		);
		$wpdb->insert(
			$tiers_table,
			array(
				'name'             => 'Платиновый',
				'slug'             => 'platinum',
				'min_points'       => 10000,
				'discount_percent' => 10,
				'benefits'         => 'Скидка 10% + приоритетная поддержка',
			)
		);
	}
}
register_activation_hook( __FILE__, 'atk_ved_create_loyalty_tables' );
add_action( 'plugins_loaded', 'atk_ved_create_loyalty_tables' );

/**
 * Получение баланса пользователя
 */
function atk_ved_get_loyalty_balance( int $user_id ): array {
	global $wpdb;

	$table_name = $wpdb->prefix . 'atk_ved_loyalty_balance';

	$balance = $wpdb->get_row(
		$wpdb->prepare(
			"SELECT * FROM {$table_name} WHERE user_id = %d",
			$user_id
		)
	);

	if ( ! $balance ) {
		// Создаём новый баланс
		$wpdb->insert(
			$table_name,
			array(
				'user_id'         => $user_id,
				'balance'         => 0,
				'lifetime_earned' => 0,
				'lifetime_spent'  => 0,
				'tier'            => 'bronze',
			)
		);

		return array(
			'balance'          => 0,
			'lifetime_earned'  => 0,
			'lifetime_spent'   => 0,
			'tier'             => 'bronze',
			'tier_name'        => 'Бронзовый',
			'next_tier_points' => 1000,
		);
	}

	// Получаем информацию о следующем уровне
	$tiers_table = $wpdb->prefix . 'atk_ved_loyalty_tiers';
	$next_tier   = $wpdb->get_row(
		$wpdb->prepare(
			"SELECT * FROM {$tiers_table} WHERE min_points > %d ORDER BY min_points ASC LIMIT 1",
			$balance->lifetime_earned
		)
	);

	$tier_info = $wpdb->get_row(
		$wpdb->prepare(
			"SELECT * FROM {$tiers_table} WHERE slug = %s",
			$balance->tier
		)
	);

	return array(
		'balance'             => (int) $balance->balance,
		'lifetime_earned'     => (int) $balance->lifetime_earned,
		'lifetime_spent'      => (int) $balance->lifetime_spent,
		'tier'                => $balance->tier,
		'tier_name'           => $tier_info ? $tier_info->name : 'Бронзовый',
		'discount_percent'    => $tier_info ? (float) $tier_info->discount_percent : 0,
		'next_tier_points'    => $next_tier ? (int) $next_tier->min_points : null,
		'points_to_next_tier' => $next_tier ? (int) $next_tier->min_points - (int) $balance->lifetime_earned : 0,
	);
}

/**
 * Начисление баллов
 */
function atk_ved_add_loyalty_points( int $user_id, int $points, string $type, string $description = '', string $ref_type = '', int $ref_id = 0 ): void {
	if ( $points <= 0 ) {
		return;
	}

	global $wpdb;

	$balance_table = $wpdb->prefix . 'atk_ved_loyalty_balance';
	$history_table = $wpdb->prefix . 'atk_ved_loyalty_history';
	$tiers_table   = $wpdb->prefix . 'atk_ved_loyalty_tiers';

	// Получаем текущий баланс
	$balance = $wpdb->get_row(
		$wpdb->prepare(
			"SELECT * FROM {$balance_table} WHERE user_id = %d",
			$user_id
		)
	);

	if ( ! $balance ) {
		atk_ved_get_loyalty_balance( $user_id ); // Создаём баланс
		$balance = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT * FROM {$balance_table} WHERE user_id = %d",
				$user_id
			)
		);
	}

	$new_balance  = (int) $balance->balance + $points;
	$new_lifetime = (int) $balance->lifetime_earned + $points;

	// Обновляем баланс
	$wpdb->update(
		$balance_table,
		array(
			'balance'         => $new_balance,
			'lifetime_earned' => $new_lifetime,
		),
		array( 'user_id' => $user_id )
	);

	// Проверяем и обновляем уровень
	$new_tier = $wpdb->get_row(
		$wpdb->prepare(
			"SELECT * FROM {$tiers_table} WHERE min_points <= %d ORDER BY min_points DESC LIMIT 1",
			$new_lifetime
		)
	);

	if ( $new_tier && $new_tier->slug !== $balance->tier ) {
		$wpdb->update( $balance_table, array( 'tier' => $new_tier->slug ), array( 'user_id' => $user_id ) );

		// Уведомление о повышении уровня
		atk_ved_notify_tier_upgrade( $user_id, $balance->tier, $new_tier->slug );
	}

	// Записываем историю
	$wpdb->insert(
		$history_table,
		array(
			'user_id'        => $user_id,
			'points'         => $points,
			'type'           => $type,
			'description'    => $description,
			'reference_type' => $ref_type,
			'reference_id'   => $ref_id,
			'balance_after'  => $new_balance,
		)
	);
}

/**
 * Списание баллов
 */
function atk_ved_spend_loyalty_points( int $user_id, int $points, string $type, string $description = '', string $ref_type = '', int $ref_id = 0 ): bool {
	global $wpdb;

	$balance_table = $wpdb->prefix . 'atk_ved_loyalty_balance';
	$history_table = $wpdb->prefix . 'atk_ved_loyalty_history';

	$balance = $wpdb->get_row(
		$wpdb->prepare(
			"SELECT * FROM {$balance_table} WHERE user_id = %d",
			$user_id
		)
	);

	if ( ! $balance || $balance->balance < $points ) {
		return false;
	}

	$new_balance        = (int) $balance->balance - $points;
	$new_lifetime_spent = (int) $balance->lifetime_spent + $points;

	$wpdb->update(
		$balance_table,
		array(
			'balance'        => $new_balance,
			'lifetime_spent' => $new_lifetime_spent,
		),
		array( 'user_id' => $user_id )
	);

	$wpdb->insert(
		$history_table,
		array(
			'user_id'        => $user_id,
			'points'         => -$points,
			'type'           => $type,
			'description'    => $description,
			'reference_type' => $ref_type,
			'reference_id'   => $ref_id,
			'balance_after'  => $new_balance,
		)
	);

	return true;
}

/**
 * Начисление баллов за заказ
 */
function atk_ved_loyalty_order_completed( int $order_id ): void {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return;
	}

	$order = wc_get_order( $order_id );

	if ( ! $order ) {
		return;
	}

	$user_id = $order->get_user_id();

	if ( $user_id === 0 ) {
		return;
	}

	$order_total = $order->get_total();

	// 1 балл за каждые 100 рублей
	$points_to_award = floor( $order_total / 100 );

	if ( $points_to_award > 0 ) {
		atk_ved_add_loyalty_points(
			$user_id,
			$points_to_award,
			'order',
			sprintf( __( 'Заказ #%d', 'atk-ved' ), $order_id ),
			'order',
			$order_id
		);
	}
}
add_action( 'woocommerce_order_status_completed', 'atk_ved_loyalty_order_completed' );

/**
 * Уведомление о повышении уровня
 */
function atk_ved_notify_tier_upgrade( int $user_id, string $old_tier, string $new_tier ): void {
	$user = get_user_by( 'ID', $user_id );

	if ( ! $user ) {
		return;
	}

	$tier_names = array(
		'bronze'   => 'Бронзовый',
		'silver'   => 'Серебряный',
		'gold'     => 'Золотой',
		'platinum' => 'Платиновый',
	);

	$subject = sprintf( __( 'Поздравляем! Ваш новый уровень: %s', 'atk-ved' ), $tier_names[ $new_tier ] ?? $new_tier );

	$message = sprintf(
		__(
			'Здравствуйте, %1$s!

Ваш уровень лояльности повышен с %2$s до %3$s!

Теперь вам доступна персональная скидка на все товары.

Спасибо, что вы с нами!',
			'atk-ved'
		),
		$user->display_name,
		$tier_names[ $old_tier ] ?? $old_tier,
		$tier_names[ $new_tier ] ?? $new_tier
	);

	wp_mail( $user->user_email, $subject, $message );
}

/**
 * Шорткод: Личный кабинет лояльности
 */
function atk_ved_loyalty_dashboard_shortcode(): string {
	if ( ! is_user_logged_in() ) {
		return '<p>' . __( 'Пожалуйста, войдите в личный кабинет', 'atk-ved' ) . '</p>';
	}

	$user_id = get_current_user_id();
	$balance = atk_ved_get_loyalty_balance( $user_id );

	global $wpdb;
	$history_table = $wpdb->prefix . 'atk_ved_loyalty_history';
	$history       = $wpdb->get_results(
		$wpdb->prepare(
			"SELECT * FROM {$history_table} WHERE user_id = %d ORDER BY created_at DESC LIMIT 10",
			$user_id
		)
	);

	ob_start();
	?>
	<div class="loyalty-dashboard">
		<h2><?php _e( 'Программа лояльности', 'atk-ved' ); ?></h2>
		
		<div class="loyalty-stats">
			<div class="loyalty-stat-card">
				<div class="stat-icon">🎯</div>
				<div class="stat-value"><?php echo number_format( $balance['balance'] ); ?></div>
				<div class="stat-label"><?php _e( 'Баллов на счету', 'atk-ved' ); ?></div>
			</div>
			
			<div class="loyalty-stat-card">
				<div class="stat-icon">📈</div>
				<div class="stat-value"><?php echo number_format( $balance['lifetime_earned'] ); ?></div>
				<div class="stat-label"><?php _e( 'Всего заработано', 'atk-ved' ); ?></div>
			</div>
			
			<div class="loyalty-stat-card">
				<div class="stat-icon">🏆</div>
				<div class="stat-value"><?php echo $balance['tier_name']; ?></div>
				<div class="stat-label"><?php _e( 'Ваш уровень', 'atk-ved' ); ?></div>
			</div>
			
			<?php if ( $balance['discount_percent'] > 0 ) : ?>
			<div class="loyalty-stat-card highlight">
				<div class="stat-icon">💰</div>
				<div class="stat-value"><?php echo $balance['discount_percent']; ?>%</div>
				<div class="stat-label"><?php _e( 'Ваша скидка', 'atk-ved' ); ?></div>
			</div>
			<?php endif; ?>
		</div>
		
		<?php if ( $balance['next_tier_points'] ) : ?>
		<div class="loyalty-progress">
			<h3><?php _e( 'Прогресс до следующего уровня', 'atk-ved' ); ?></h3>
			<div class="progress-bar">
				<div class="progress-fill" style="width: <?php echo min( 100, ( $balance['lifetime_earned'] / $balance['next_tier_points'] ) * 100 ); ?>%"></div>
			</div>
			<p>
				<?php
				if ( $balance['points_to_next_tier'] > 0 ) {
					printf(
						__( 'Ещё <strong>%1$d баллов</strong> до уровня %2$s', 'atk-ved' ),
						$balance['points_to_next_tier'],
						$balance['next_tier_points']
					);
				} else {
					_e( 'Поздравляем! Вы достигли максимального уровня!', 'atk-ved' );
				}
				?>
			</p>
		</div>
		<?php endif; ?>
		
		<div class="loyalty-history">
			<h3><?php _e( 'История операций', 'atk-ved' ); ?></h3>
			<table class="loyalty-history-table">
				<thead>
					<tr>
						<th><?php _e( 'Дата', 'atk-ved' ); ?></th>
						<th><?php _e( 'Описание', 'atk-ved' ); ?></th>
						<th><?php _e( 'Баллы', 'atk-ved' ); ?></th>
						<th><?php _e( 'Баланс', 'atk-ved' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $history as $item ) : ?>
					<tr>
						<td><?php echo date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $item->created_at ) ); ?></td>
						<td><?php echo esc_html( $item->description ); ?></td>
						<td class="<?php echo $item->points > 0 ? 'positive' : 'negative'; ?>">
							<?php echo $item->points > 0 ? '+' : ''; ?><?php echo number_format( $item->points ); ?>
						</td>
						<td><?php echo number_format( $item->balance_after ); ?></td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
		
		<div class="loyalty-actions">
			<button type="button" class="btn-spend-points" onclick="jQuery('#spendPointsModal').show();">
				💳 <?php _e( 'Потратить баллы', 'atk-ved' ); ?>
			</button>
		</div>
	</div>
	
	<style>
	.loyalty-dashboard { max-width: 1000px; margin: 0 auto; padding: 20px; }
	.loyalty-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin: 30px 0; }
	.loyalty-stat-card { background: #fff; padding: 25px; border-radius: 12px; box-shadow: 0 2px 15px rgba(0,0,0,0.08); text-align: center; }
	.loyalty-stat-card.highlight { border: 2px solid #e31e24; }
	.stat-icon { font-size: 40px; margin-bottom: 10px; }
	.stat-value { font-size: 32px; font-weight: 700; color: #e31e24; margin-bottom: 5px; }
	.stat-label { font-size: 14px; color: #666; }
	.loyalty-progress { background: #fff; padding: 25px; border-radius: 12px; margin-bottom: 30px; }
	.progress-bar { height: 20px; background: #f0f0f0; border-radius: 10px; overflow: hidden; margin: 15px 0; }
	.progress-fill { height: 100%; background: linear-gradient(90deg, #e31e24, #ff4d4f); transition: width 0.5s; }
	.loyalty-history { background: #fff; border-radius: 12px; overflow: hidden; }
	.loyalty-history h3 { padding: 20px 25px; margin: 0; border-bottom: 1px solid #e0e0e0; }
	.loyalty-history-table { width: 100%; border-collapse: collapse; }
	.loyalty-history-table th, .loyalty-history-table td { padding: 15px 25px; text-align: left; border-bottom: 1px solid #f0f0f0; }
	.loyalty-history-table th { background: #f8f9fa; font-weight: 600; }
	.loyalty-history-table .positive { color: #4CAF50; font-weight: 600; }
	.loyalty-history-table .negative { color: #f44336; font-weight: 600; }
	.loyalty-actions { margin-top: 30px; text-align: center; }
	.btn-spend-points { padding: 16px 40px; background: #e31e24; color: #fff; border: none; border-radius: 8px; font-size: 16px; font-weight: 600; cursor: pointer; }
	</style>
	<?php
	return ob_get_clean();
}
add_shortcode( 'loyalty_dashboard', 'atk_ved_loyalty_dashboard_shortcode' );

/**
 * Интеграция скидки уровня с WooCommerce
 */
function atk_ved_loyalty_discount( $cart ) {
	if ( ! is_user_logged_in() ) {
		return;
	}

	$user_id = get_current_user_id();
	$balance = atk_ved_get_loyalty_balance( $user_id );

	if ( $balance['discount_percent'] > 0 ) {
		$discount = ( $cart->get_subtotal() * $balance['discount_percent'] ) / 100;
		$cart->add_discount( $discount );
	}
}
// add_action('woocommerce_cart_calculate_fees', 'atk_ved_loyalty_discount');
