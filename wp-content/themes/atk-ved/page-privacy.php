<?php
/**
 * Template Name: Политика конфиденциальности
 *
 * @package ATK_VED
 */

defined('ABSPATH') || exit;

get_header();
?>

<main id="main-content" class="privacy-page">
    <div class="container">
        
        <article class="privacy-content">
            
            <header class="privacy-header">
                <h1><?php esc_html_e('Политика конфиденциальности', 'atk-ved'); ?></h1>
                <p class="privacy-updated">
                    <?php esc_html_e('Последнее обновление:', 'atk-ved'); ?> 
                    <?php echo date('d.m.Y'); ?>
                </p>
            </header>

            <div class="privacy-body">
                
                <section class="privacy-section">
                    <h2>1. Общие положения</h2>
                    <p>Настоящая Политика конфиденциальности определяет порядок обработки и защиты персональных данных пользователей сайта <?php echo esc_html(home_url()); ?>.</p>
                    <p>Используя наш сайт, вы соглашаетесь с условиями данной Политики конфиденциальности.</p>
                </section>

                <section class="privacy-section">
                    <h2>2. Какие данные мы собираем</h2>
                    <p>Мы можем собирать следующую информацию:</p>
                    <ul>
                        <li>Имя и фамилия</li>
                        <li>Контактная информация (email, телефон)</li>
                        <li>Название компании и должность</li>
                        <li>IP-адрес и данные браузера</li>
                        <li>Информация о посещениях сайта (cookies)</li>
                    </ul>
                </section>

                <section class="privacy-section">
                    <h2>3. Как мы используем данные</h2>
                    <p>Собранная информация используется для:</p>
                    <ul>
                        <li>Обработки ваших заявок и запросов</li>
                        <li>Предоставления информации о наших услугах</li>
                        <li>Улучшения качества обслуживания</li>
                        <li>Отправки маркетинговых материалов (с вашего согласия)</li>
                        <li>Анализа использования сайта</li>
                    </ul>
                </section>

                <section class="privacy-section">
                    <h2>4. Защита данных</h2>
                    <p>Мы принимаем все необходимые меры для защиты ваших персональных данных:</p>
                    <ul>
                        <li>Использование SSL-шифрования</li>
                        <li>Ограниченный доступ к данным</li>
                        <li>Регулярное резервное копирование</li>
                        <li>Защита от несанкционированного доступа</li>
                    </ul>
                </section>

                <section class="privacy-section">
                    <h2>5. Cookies</h2>
                    <p>Мы используем cookies для улучшения работы сайта. Cookies — это небольшие текстовые файлы, которые сохраняются на вашем устройстве.</p>
                    <p>Вы можете отключить cookies в настройках браузера, но это может ограничить функциональность сайта.</p>
                </section>

                <section class="privacy-section">
                    <h2>6. Передача данных третьим лицам</h2>
                    <p>Мы не продаём и не передаём ваши персональные данные третьим лицам, за исключением случаев:</p>
                    <ul>
                        <li>Когда это необходимо для выполнения наших обязательств перед вами</li>
                        <li>Когда это требуется по закону</li>
                        <li>С вашего явного согласия</li>
                    </ul>
                </section>

                <section class="privacy-section">
                    <h2>7. Ваши права</h2>
                    <p>Вы имеете право:</p>
                    <ul>
                        <li>Получить информацию о ваших персональных данных</li>
                        <li>Исправить неточные данные</li>
                        <li>Удалить ваши данные</li>
                        <li>Отозвать согласие на обработку данных</li>
                        <li>Ограничить обработку данных</li>
                    </ul>
                </section>

                <section class="privacy-section">
                    <h2>8. Контакты</h2>
                    <p>По вопросам, связанным с обработкой персональных данных, вы можете связаться с нами:</p>
                    <ul>
                        <li>Email: <?php echo esc_html(atk_ved_get_company_info()['email'] ?? 'info@atk-ved.ru'); ?></li>
                        <li>Телефон: <?php echo esc_html(atk_ved_get_company_info()['phone'] ?? '+7 (XXX) XXX-XX-XX'); ?></li>
                    </ul>
                </section>

                <section class="privacy-section">
                    <h2>9. Изменения в политике</h2>
                    <p>Мы оставляем за собой право вносить изменения в данную Политику конфиденциальности. Все изменения будут опубликованы на этой странице.</p>
                </section>

            </div>

            <footer class="privacy-footer">
                <a href="<?php echo esc_url(home_url('/')); ?>" class="btn btn-outline">
                    <?php esc_html_e('Вернуться на главную', 'atk-ved'); ?>
                </a>
            </footer>

        </article>

    </div>
</main>

<?php
get_footer();
