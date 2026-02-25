/**
 * JavaScript для отслеживания грузов
 * 
 * @package ATK_VED
 * @since 2.0.0
 */

(function($) {
    'use strict';

    $(document).ready(function() {
        initTracking();
    });

    function initTracking() {
        const $tracking = $('.shipment-tracking');
        if (!$tracking.length) return;

        const $form = $('#trackingForm');
        const $result = $('#trackingResult');
        const $resultContent = $result.find('.tracking-result-content');
        const $error = $('#trackingError');
        const $loader = $result.find('.tracking-loader');

        $form.on('submit', function(e) {
            e.preventDefault();
            
            const trackingNumber = $('#trackingNumber').val().trim();
            
            if (!trackingNumber) {
                showError('Введите номер отслеживания');
                return;
            }

            // Сброс
            $error.hide();
            $result.show();
            $loader.show();
            $resultContent.hide();

            // Отправка
            $.ajax({
                url: atkVedData.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'atk_ved_track_shipment',
                    nonce: $tracking.data('nonce'),
                    tracking_number: trackingNumber
                },
                success: function(response) {
                    $loader.hide();
                    
                    if (response.success) {
                        displayTrackingInfo(response.data);
                    } else {
                        showError(response.data.message || 'Груз не найден');
                    }
                },
                error: function() {
                    $loader.hide();
                    showError('Ошибка соединения. Попробуйте позже.');
                }
            });
        });

        function displayTrackingInfo(data) {
            let html = '';

            // Info card
            html += '<div class="tracking-info-card">';
            
            // Header
            html += '<div class="tracking-info-header">';
            html += '<div>';
            html += '<div class="tracking-number">' + escapeHtml(data.tracking_number) + '</div>';
            html += '</div>';
            html += '<span class="tracking-status" style="background: ' + data.status_color + ';">';
            html += '<span class="tracking-status-dot"></span>';
            html += escapeHtml(data.status_name);
            html += '</span>';
            html += '</div>';

            // Route
            html += '<div class="tracking-route">';
            html += '<div class="route-point">';
            html += '<div class="route-point-label">Откуда</div>';
            html += '<div class="route-point-city">' + escapeHtml(data.origin) + '</div>';
            html += '</div>';
            
            html += '<div class="route-line">';
            html += '<div class="route-line-progress" style="width: ' + data.progress + '%;"></div>';
            html += '</div>';
            
            html += '<div class="route-point">';
            html += '<div class="route-point-label">Куда</div>';
            html += '<div class="route-point-city">' + escapeHtml(data.destination) + '</div>';
            html += '</div>';
            html += '</div>';

            // Details
            html += '<div class="tracking-details">';
            html += '<div class="tracking-detail">';
            html += '<div class="tracking-detail-label">Вес</div>';
            html += '<div class="tracking-detail-value">' + data.weight + ' кг</div>';
            html += '</div>';
            
            html += '<div class="tracking-detail">';
            html += '<div class="tracking-detail-label">Прогресс</div>';
            html += '<div class="tracking-detail-value">' + data.progress + '%</div>';
            html += '</div>';
            
            html += '<div class="tracking-detail">';
            html += '<div class="tracking-detail-label">Доставка</div>';
            html += '<div class="tracking-detail-value">' + formatDate(data.estimated_delivery) + '</div>';
            html += '</div>';
            html += '</div>';
            
            html += '</div>';

            // Progress bar
            html += '<div class="tracking-progress">';
            html += '<div class="progress-bar">';
            html += '<div class="progress-bar-fill" style="width: ' + data.progress + '%;"></div>';
            html += '</div>';
            
            html += '<div class="progress-steps">';
            const steps = [
                {key: 'created', label: 'Заявка'},
                {key: 'pickup', label: 'Забран'},
                {key: 'in_transit', label: 'В пути'},
                {key: 'customs', label: 'Таможня'},
                {key: 'delivered', label: 'Доставлен'}
            ];
            
            const currentProgress = data.progress;
            const stepProgress = {created: 10, pickup: 30, in_transit: 60, customs: 80, delivered: 100};
            
            steps.forEach(function(step) {
                let className = 'progress-step';
                if (currentProgress >= stepProgress[step.key]) {
                    className += ' completed';
                }
                if (currentProgress >= stepProgress[step.key] && currentProgress < stepProgress[step.key] + 20) {
                    className += ' active';
                }
                
                html += '<div class="' + className + '">';
                html += '<div class="progress-step-label">' + step.label + '</div>';
                html += '</div>';
            });
            
            html += '</div>';
            html += '</div>';

            // History
            if (data.history && data.history.length > 0) {
                html += '<div class="tracking-history">';
                html += '<h4>История перемещений</h4>';
                html += '<div class="history-timeline">';
                
                data.history.forEach(function(item, index) {
                    const isCompleted = index < data.history.length - 1 || data.status === 'delivered';
                    
                    html += '<div class="history-item' + (isCompleted ? ' completed' : '') + '">';
                    html += '<div class="history-item-content">';
                    html += '<div class="history-item-status">' + escapeHtml(item.status_name) + '</div>';
                    if (item.location) {
                        html += '<div class="history-item-location">📍 ' + escapeHtml(item.location) + '</div>';
                    }
                    if (item.description) {
                        html += '<div class="history-item-description">' + escapeHtml(item.description) + '</div>';
                    }
                    html += '<div class="history-item-date">' + formatDateTime(item.created_at) + '</div>';
                    html += '</div>';
                    html += '</div>';
                });
                
                html += '</div>';
                html += '</div>';
            }

            $resultContent.html(html).fadeIn();
            
            // Analytics
            if (typeof ym !== 'undefined') {
                ym(atkVedData.metrikaId || 0, 'reachGoal', 'tracking_used');
            }
            if (typeof gtag !== 'undefined') {
                gtag('event', 'tracking_used', {
                    'event_category': 'Tracking',
                    'event_label': 'Shipment Tracking'
                });
            }
        }

        function showError(message) {
            $error.text(message).fadeIn();
            $result.hide();
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function formatDate(dateString) {
            if (!dateString) return '—';
            const date = new Date(dateString);
            return date.toLocaleDateString('ru-RU', {day: 'numeric', month: 'long', year: 'numeric'});
        }

        function formatDateTime(dateTimeString) {
            if (!dateTimeString) return '';
            const date = new Date(dateTimeString);
            return date.toLocaleString('ru-RU', {
                day: 'numeric',
                month: 'long',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        }
    }

})(jQuery);
