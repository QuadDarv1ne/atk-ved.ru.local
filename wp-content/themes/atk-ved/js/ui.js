/**
 * UI Components - Объединённый файл
 * Modals, Tabs, Accordions
 * @package ATK_VED
 * @version 3.3.1
 */

(function($) {
    'use strict';

    $(document).ready(function() {
        initModals();
        initTabs();
        initAccordions();
    });

    // === MODALS ===
    function initModals() {
        $('[data-modal-open]').on('click', function(e) {
            e.preventDefault();
            openModal($(this).data('modal-open'));
        });

        $('[data-modal-close]').on('click', function() {
            closeModal($(this).data('modal-close'));
        });

        $('.modal').on('click', function(e) {
            if ($(e.target).hasClass('modal-backdrop') || $(e.target).hasClass('modal')) {
                closeModal($(this).attr('id'));
            }
        });

        $(document).on('keydown', function(e) {
            if (e.key === 'Escape') {
                const $open = $('.modal.is-open').first();
                if ($open.length) closeModal($open.attr('id'));
            }
        });
    }

    function openModal(id) {
        const $modal = $('#' + id);
        if (!$modal.length) return;

        $modal.addClass('is-open').css('display', 'flex');
        $('body').addClass('modal-open');
        
        setTimeout(() => {
            $modal.find('.modal-content').focus();
        }, 100);
    }

    function closeModal(id) {
        const $modal = id ? $('#' + id) : $('.modal.is-open');
        
        $modal.removeClass('is-open');
        setTimeout(() => {
            $modal.css('display', 'none');
            if (!$('.modal.is-open').length) {
                $('body').removeClass('modal-open');
            }
        }, 300);
    }

    window.atkOpenModal = openModal;
    window.atkCloseModal = closeModal;

    // === TABS ===
    function initTabs() {
        $('.tab-button').on('click', function() {
            const $btn = $(this);
            const $tabs = $btn.closest('.tabs');
            const target = $btn.data('tab');

            $tabs.find('.tab-button').removeClass('is-active').attr('aria-selected', 'false');
            $tabs.find('.tab-panel').removeClass('is-active');

            $btn.addClass('is-active').attr('aria-selected', 'true');
            $('#' + target).addClass('is-active');
        });

        // Keyboard navigation
        $('.tabs[data-keyboard-navigation="true"] .tab-button').on('keydown', function(e) {
            const $btns = $(this).closest('.tabs-header').find('.tab-button');
            const idx = $btns.index(this);

            if (e.key === 'ArrowRight' || e.key === 'ArrowDown') {
                e.preventDefault();
                $btns.eq((idx + 1) % $btns.length).focus().click();
            } else if (e.key === 'ArrowLeft' || e.key === 'ArrowUp') {
                e.preventDefault();
                $btns.eq((idx - 1 + $btns.length) % $btns.length).focus().click();
            }
        });
    }

    // === ACCORDIONS ===
    function initAccordions() {
        $('.accordion-header').on('click', function() {
            const $item = $(this).closest('.accordion-item');
            const $accordion = $item.closest('.accordion');
            const isExclusive = $accordion.hasClass('accordion-exclusive');

            if (isExclusive) {
                $accordion.find('.accordion-item').not($item).removeClass('is-active');
                $accordion.find('.accordion-body').not($item.find('.accordion-body')).slideUp(300);
            }

            $item.toggleClass('is-active');
            $item.find('.accordion-body').slideToggle(300);
        });
    }

})(jQuery);
