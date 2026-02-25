/**
 * UI Components JavaScript: Modals, Tabs, Accordions
 * 
 * @package ATK_VED
 * @since 2.1.0
 */

(function($) {
    'use strict';

    // Инициализация при загрузке DOM
    $(document).ready(function() {
        initModals();
        initTabs();
        initAccordions();
    });

    /* ==========================================================================
       MODAL COMPONENT
       ========================================================================== */

    function initModals() {
        // Открытие по кнопке
        $('[data-modal-open]').on('click', function(e) {
            e.preventDefault();
            const modalId = $(this).data('modal-open');
            openModal(modalId);
        });

        // Закрытие по кнопке
        $('[data-modal-close]').on('click', function() {
            const modalId = $(this).data('modal-close');
            closeModal(modalId);
        });

        // Закрытие по клику на backdrop
        $('.modal').on('click', function(e) {
            if ($(e.target).hasClass('modal-backdrop') || 
                $(e.target).hasClass('modal')) {
                closeModal($(this).attr('id'));
            }
        });

        // Закрытие по Escape
        $(document).on('keydown', function(e) {
            if (e.key === 'Escape') {
                const openModal = $('.modal.is-open').first();
                if (openModal.length) {
                    closeModal(openModal.attr('id'));
                }
            }
        });

        // Предотвращение прокрутки body при открытом модальном окне
        $('.modal').on('openModal', function() {
            $('body').css('overflow', 'hidden');
        });

        $('.modal').on('closeModal', function() {
            if ($('.modal.is-open').length === 0) {
                $('body').css('overflow', '');
            }
        });
    }

    /**
     * Открыть модальное окно
     * @param {string} modalId - ID модального окна
     */
    function openModal(modalId) {
        const $modal = $('#' + modalId);
        if (!$modal.length) return;

        $modal.addClass('is-open');
        $modal.trigger('openModal');

        // Фокус на первый интерактивный элемент
        setTimeout(function() {
            $modal.find('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])')
                .first()
                .focus();
        }, 100);

        // Событие для аналитики
        if (typeof atkVedData !== 'undefined' && typeof ym !== 'undefined') {
            ym(atkVedData.metrikaId || 0, 'reachGoal', 'modal_open', {modal: modalId});
        }
    }

    /**
     * Закрыть модальное окно
     * @param {string} modalId - ID модального окна
     */
    function closeModal(modalId) {
        const $modal = $('#' + modalId);
        if (!$modal.length) return;

        $modal.removeClass('is-open');
        $modal.trigger('closeModal');

        // Возврат фокуса на кнопку открытия
        const $trigger = $('[data-modal-open="' + modalId + '"]');
        if ($trigger.length) {
            setTimeout(function() {
                $trigger.focus();
            }, 300);
        }
    }

    /**
     * Глобальные функции (доступны из window)
     */
    window.atkOpenModal = openModal;
    window.atkCloseModal = closeModal;

    /* ==========================================================================
       TABS COMPONENT
       ========================================================================== */

    function initTabs() {
        $('.tabs').each(function() {
            const $tabs = $(this);
            const $tabButtons = $tabs.find('.tab-button');
            const $tabPanels = $tabs.find('.tab-panel');

            // Клик по кнопке таба
            $tabButtons.on('click', function() {
                const index = $tabButtons.index(this);
                activateTab($tabs, index);
            });

            // Keyboard navigation
            $tabButtons.on('keydown', function(e) {
                let newIndex = $tabButtons.index(this);

                switch(e.key) {
                    case 'ArrowLeft':
                    case 'ArrowUp':
                        e.preventDefault();
                        newIndex = newIndex > 0 ? newIndex - 1 : $tabButtons.length - 1;
                        $tabButtons.eq(newIndex).focus();
                        activateTab($tabs, newIndex);
                        break;
                    case 'ArrowRight':
                    case 'ArrowDown':
                        e.preventDefault();
                        newIndex = newIndex < $tabButtons.length - 1 ? newIndex + 1 : 0;
                        $tabButtons.eq(newIndex).focus();
                        activateTab($tabs, newIndex);
                        break;
                    case 'Home':
                        e.preventDefault();
                        $tabButtons.first().focus();
                        activateTab($tabs, 0);
                        break;
                    case 'End':
                        e.preventDefault();
                        $tabButtons.last().focus();
                        activateTab($tabs, $tabButtons.length - 1);
                        break;
                }
            });

            // Активация таба по URL hash
            const hash = window.location.hash;
            if (hash) {
                const $targetPanel = $tabs.find(hash);
                if ($targetPanel.length && $targetPanel.hasClass('tab-panel')) {
                    const index = $tabPanels.index($targetPanel);
                    activateTab($tabs, index);
                }
            }
        });
    }

    /**
     * Активировать таб по индексу
     * @param {jQuery} $tabs - Контейнер табов
     * @param {number} index - Индекс таба
     */
    function activateTab($tabs, index) {
        const $tabButtons = $tabs.find('.tab-button');
        const $tabPanels = $tabs.find('.tab-panel');

        $tabButtons.removeClass('is-active');
        $tabPanels.removeClass('is-active');

        $tabButtons.eq(index).addClass('is-active');
        $tabPanels.eq(index).addClass('is-active');

        // Обновление URL hash
        const panelId = $tabPanels.eq(index).attr('id');
        if (panelId && history.pushState) {
            history.pushState(null, null, '#' + panelId);
        }

        // Событие
        $tabs.trigger('tabChange', {index: index, panelId: panelId});

        // Аналитика
        if (typeof atkVedData !== 'undefined' && typeof ym !== 'undefined') {
            ym(atkVedData.metrikaId || 0, 'reachGoal', 'tab_click', {tab: panelId});
        }
    }

    /**
     * Глобальная функция для активации таба
     * @param {string} tabsSelector - Селектор контейнера табов
     * @param {number|string} indexOrId - Индекс или ID таба
     */
    window.atkActivateTab = function(tabsSelector, indexOrId) {
        const $tabs = $(tabsSelector);
        if (!$tabs.length) return;

        if (typeof indexOrId === 'number') {
            activateTab($tabs, indexOrId);
        } else if (typeof indexOrId === 'string') {
            const $targetPanel = $tabs.find(indexOrId);
            if ($targetPanel.length) {
                const index = $tabs.find('.tab-panel').index($targetPanel);
                activateTab($tabs, index);
            }
        }
    };

    /* ==========================================================================
       ACCORDION COMPONENT
       ========================================================================== */

    function initAccordions() {
        $('.accordion').each(function() {
            const $accordion = $(this);
            const $items = $accordion.find('.accordion-item');
            const isExclusive = $accordion.hasClass('accordion-exclusive');

            $items.each(function(index) {
                const $item = $(this);
                const $header = $item.find('.accordion-header');
                const $body = $item.find('.accordion-body');

                // Инициализация высоты
                if ($item.hasClass('is-active')) {
                    $body.css('max-height', $body[0].scrollHeight + 'px');
                }

                // Клик по заголовку
                $header.on('click', function() {
                    const isActive = $item.hasClass('is-active');

                    // Если эксклюзивный аккордеон, закрыть остальные
                    if (isExclusive && !isActive) {
                        $items.each(function() {
                            const $otherItem = $(this);
                            const $otherBody = $otherItem.find('.accordion-body');
                            $otherItem.removeClass('is-active');
                            $otherBody.css('max-height', '');
                        });
                    }

                    // Переключение текущего
                    $item.toggleClass('is-active');

                    if ($item.hasClass('is-active')) {
                        $body.css('max-height', $body[0].scrollHeight + 'px');
                    } else {
                        $body.css('max-height', '');
                    }

                    // Событие
                    $accordion.trigger('accordionChange', {
                        index: index,
                        isActive: !isActive
                    });

                    // Аналитика
                    if (typeof atkVedData !== 'undefined' && typeof ym !== 'undefined') {
                        ym(atkVedData.metrikaId || 0, 'reachGoal', 'accordion_toggle', {
                            accordion: $accordion.attr('id') || 'unknown',
                            index: index,
                            expanded: !isActive
                        });
                    }
                });

                // Keyboard navigation
                $header.on('keydown', function(e) {
                    switch(e.key) {
                        case 'Enter':
                        case ' ':
                            e.preventDefault();
                            $header.click();
                            break;
                        case 'ArrowDown':
                            e.preventDefault();
                            const $nextItem = $item.next('.accordion-item');
                            if ($nextItem.length) {
                                $nextItem.find('.accordion-header').focus();
                            }
                            break;
                        case 'ArrowUp':
                            e.preventDefault();
                            const $prevItem = $item.prev('.accordion-item');
                            if ($prevItem.length) {
                                $prevItem.find('.accordion-header').focus();
                            }
                            break;
                        case 'Home':
                            e.preventDefault();
                            $items.first().find('.accordion-header').focus();
                            break;
                        case 'End':
                            e.preventDefault();
                            $items.last().find('.accordion-header').focus();
                            break;
                    }
                });
            });
        });
    }

    /**
     * Глобальная функция для управления аккордеоном
     * @param {string} accordionSelector - Селектор аккордеона
     * @param {number} index - Индекс элемента
     * @param {boolean} expand - Раскрыть или свернуть
     */
    window.atkToggleAccordion = function(accordionSelector, index, expand) {
        const $accordion = $(accordionSelector);
        if (!$accordion.length) return;

        const $item = $accordion.find('.accordion-item').eq(index);
        const $body = $item.find('.accordion-body');

        if (expand === true) {
            $item.addClass('is-active');
            $body.css('max-height', $body[0].scrollHeight + 'px');
        } else if (expand === false) {
            $item.removeClass('is-active');
            $body.css('max-height', '');
        } else {
            $item.toggleClass('is-active');
            if ($item.hasClass('is-active')) {
                $body.css('max-height', $body[0].scrollHeight + 'px');
            } else {
                $body.css('max-height', '');
            }
        }
    };

    /**
     * Раскрыть все элементы аккордеона
     */
    window.atkExpandAllAccordions = function(accordionSelector) {
        $(accordionSelector).find('.accordion-item').each(function() {
            const $item = $(this);
            const $body = $item.find('.accordion-body');
            $item.addClass('is-active');
            $body.css('max-height', $body[0].scrollHeight + 'px');
        });
    };

    /**
     * Свернуть все элементы аккордеона
     */
    window.atkCollapseAllAccordions = function(accordionSelector) {
        $(accordionSelector).find('.accordion-item').each(function() {
            const $item = $(this);
            const $body = $item.find('.accordion-body');
            $item.removeClass('is-active');
            $body.css('max-height', '');
        });
    };

})(jQuery);
