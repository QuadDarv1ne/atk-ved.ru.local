/**
 * Gutenberg Blocks for ATK VED UI Components
 * 
 * @package ATK_VED
 * @since 2.2.0
 */

(function() {
    const { registerBlockType } = wp.blocks;
    const { createElement: h, Fragment } = wp.element;
    const { RichText, InspectorControls, BlockControls } = wp.blockEditor;
    const { PanelBody, SelectControl, ToggleControl, TextControl } = wp.components;
    const { ToolbarGroup, ToolbarButton } = wp.blockEditor;

    // Иконки для блоков
    const icons = {
        modal: h('svg', { width: 20, height: 20, viewBox: '0 0 20 20', fill: 'currentColor' },
            h('path', { d: 'M4 4a2 2 0 012-2h8a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 0v12h8V4H6z' })
        ),
        tabs: h('svg', { width: 20, height: 20, viewBox: '0 0 20 20', fill: 'currentColor' },
            h('path', { d: 'M2 4a2 2 0 012-2h12a2 2 0 012 2v12a2 2 0 01-2 2H4a2 2 0 01-2-2V4zm14 0H4v12h12V4z' }),
            h('path', { d: 'M4 6h12v2H4V6zm0 4h5v4H4v-4z' })
        ),
        accordion: h('svg', { width: 20, height: 20, viewBox: '0 0 20 20', fill: 'currentColor' },
            h('path', { d: 'M2 5a2 2 0 012-2h12a2 2 0 012 2v2H2V5zm0 4h16v2H2V9zm0 4h16v2a2 2 0 01-2 2H4a2 2 0 01-2-2v-2z' })
        ),
        button: h('svg', { width: 20, height: 20, viewBox: '0 0 20 20', fill: 'currentColor' },
            h('rect', { x: '2', y: '6', width: '16', height: '8', rx: '2' })
        ),
        alert: h('svg', { width: 20, height: 20, viewBox: '0 0 20 20', fill: 'currentColor' },
            h('path', { d: 'M10 2a8 8 0 100 16 8 8 0 000-16zM9 13a1 1 0 112 0 1 1 0 01-2 0zm1-9a1 1 0 00-1 1v5a1 1 0 102 0V5a1 1 0 00-1-1z' })
        ),
        progress: h('svg', { width: 20, height: 20, viewBox: '0 0 20 20', fill: 'currentColor' },
            h('rect', { x: '2', y: '8', width: '16', height: '4', rx: '2', fillOpacity: '0.3' }),
            h('rect', { x: '2', y: '8', width: '10', height: '4', rx: '2' })
        )
    };

    /* ==========================================================================
       MODAL BLOCK
       ========================================================================== */

    registerBlockType('atk-ved/modal', {
        title: 'Модальное окно',
        description: 'Модальное окно с заголовком и содержимым',
        icon: icons.modal,
        category: 'atk-ved',
        keywords: ['modal', 'popup', 'окно'],
        attributes: {
            modalId: { type: 'string', default: 'modal-' + Date.now() },
            triggerText: { type: 'string', default: 'Открыть модальное окно' },
            modalTitle: { type: 'string', default: 'Заголовок' },
            size: { type: 'string', default: 'md' },
            position: { type: 'string', default: 'center' },
            showClose: { type: 'boolean', default: true },
            buttonText: { type: 'string', default: 'OK' },
            buttonStyle: { type: 'string', default: 'primary' },
        },
        edit: function(props) {
            const { attributes, setAttributes } = props;
            const { modalId, triggerText, modalTitle, size, position, showClose, buttonText, buttonStyle } = attributes;

            return h(Fragment, null,
                h(InspectorControls, null,
                    h(PanelBody, { title: 'Настройки модального окна', initialOpen: true },
                        h(TextControl, {
                            label: 'ID модального окна',
                            value: modalId,
                            onChange: (value) => setAttributes({ modalId: value })
                        }),
                        h(SelectControl, {
                            label: 'Размер',
                            value: size,
                            options: [
                                { label: 'Маленький (sm)', value: 'sm' },
                                { label: 'Средний (md)', value: 'md' },
                                { label: 'Большой (lg)', value: 'lg' },
                                { label: 'Огромный (xl)', value: 'xl' },
                                { label: 'На весь экран (full)', value: 'full' },
                            ],
                            onChange: (value) => setAttributes({ size: value })
                        }),
                        h(SelectControl, {
                            label: 'Позиция',
                            value: position,
                            options: [
                                { label: 'По центру', value: 'center' },
                                { label: 'Сверху', value: 'top' },
                                { label: 'Снизу', value: 'bottom' },
                                { label: 'Слева', value: 'left' },
                                { label: 'Справа', value: 'right' },
                            ],
                            onChange: (value) => setAttributes({ position: value })
                        }),
                        h(ToggleControl, {
                            label: 'Показывать кнопку закрытия',
                            checked: showClose,
                            onChange: (value) => setAttributes({ showClose: value })
                        })
                    )
                ),
                h(BlockControls, null,
                    h(ToolbarGroup, null,
                        h(ToolbarButton, {
                            icon: 'admin-generic',
                            label: 'Изменить ID',
                            onClick: () => setAttributes({ modalId: 'modal-' + Date.now() })
                        })
                    )
                ),
                h('div', { className: 'atk-modal-preview' },
                    h('button', { 
                        className: `btn btn-${buttonStyle}`,
                        onClick: (e) => e.preventDefault()
                    }, triggerText),
                    h('div', { className: 'atk-modal-content-preview' },
                        h('div', { className: 'modal-header' },
                            h(RichText, {
                                tagName: 'h3',
                                className: 'modal-title',
                                value: modalTitle,
                                onChange: (value) => setAttributes({ modalTitle: value }),
                                placeholder: 'Заголовок модального окна'
                            })
                        ),
                        h(RichText, {
                            tagName: 'div',
                            className: 'modal-body',
                            value: props.children,
                            onChange: (value) => setAttributes({ content: value }),
                            placeholder: 'Содержимое модального окна'
                        })
                    )
                )
            );
        },
        save: function(props) {
            const { attributes } = props;
            const { modalId, triggerText, modalTitle, size, position, showClose } = attributes;

            return h('div', null,
                h('button', {
                    'data-modal-open': modalId,
                    className: 'btn btn-primary'
                }, triggerText),
                h('div', {
                    id: modalId,
                    className: `modal modal-${position} modal-${size}`,
                    'data-static-backdrop': showClose ? 'false' : 'true'
                },
                    h('div', { className: 'modal-backdrop' }),
                    h('div', { className: 'modal-content' },
                        h('div', { className: 'modal-header' },
                            h('h3', { className: 'modal-title' }, modalTitle),
                            showClose && h('button', {
                                className: 'modal-close',
                                'data-modal-close': modalId,
                                'aria-label': 'Закрыть'
                            }, '×')
                        ),
                        h('div', { className: 'modal-body' }, props.children),
                        h('div', { className: 'modal-footer' },
                            h('button', {
                                className: 'btn btn-secondary',
                                'data-modal-close': modalId
                            }, 'Отмена'),
                            h('button', { className: 'btn btn-primary' }, 'OK')
                        )
                    )
                )
            );
        }
    });

    /* ==========================================================================
       TABS BLOCK
       ========================================================================== */

    registerBlockType('atk-ved/tabs', {
        title: 'Табы',
        description: 'Вкладки для переключения контента',
        icon: icons.tabs,
        category: 'atk-ved',
        keywords: ['tabs', 'вкладки', 'переключение'],
        attributes: {
            tabsId: { type: 'string', default: 'tabs-' + Date.now() },
            style: { type: 'string', default: 'default' },
            vertical: { type: 'boolean', default: false },
            activeTab: { type: 'number', default: 0 },
        },
        edit: function(props) {
            const { attributes, setAttributes } = props;
            const { tabsId, style, vertical, activeTab } = attributes;

            return h(Fragment, null,
                h(InspectorControls, null,
                    h(PanelBody, { title: 'Настройки табов', initialOpen: true },
                        h(SelectControl, {
                            label: 'Стиль',
                            value: style,
                            options: [
                                { label: 'По умолчанию', value: 'default' },
                                { label: 'Pill (закруглённые)', value: 'pill' },
                            ],
                            onChange: (value) => setAttributes({ style: value })
                        }),
                        h(ToggleControl, {
                            label: 'Вертикальное расположение',
                            checked: vertical,
                            onChange: (value) => setAttributes({ vertical: value })
                        }),
                        h(TextControl, {
                            label: 'Активный таб (индекс)',
                            type: 'number',
                            value: activeTab,
                            onChange: (value) => setAttributes({ activeTab: parseInt(value) || 0 })
                        })
                    )
                ),
                h('div', { className: `tabs tabs-${style} ${vertical ? 'tabs-vertical' : ''}` },
                    h('div', { className: 'tabs-header' },
                        h('button', { className: 'tab-button is-active' }, 'Таб 1'),
                        h('button', { className: 'tab-button' }, 'Таб 2'),
                        h('button', { className: 'tab-button' }, 'Таб 3')
                    ),
                    h('div', { className: 'tabs-content' },
                        h(RichText, {
                            tagName: 'div',
                            className: 'tab-panel is-active',
                            value: props.children,
                            onChange: (value) => setAttributes({ content: value }),
                            placeholder: 'Содержимое таба 1'
                        })
                    )
                )
            );
        },
        save: function(props) {
            return h('div', { className: 'tabs' },
                h('div', { className: 'tabs-header' },
                    h('button', { className: 'tab-button is-active', role: 'tab' }, 'Таб 1'),
                    h('button', { className: 'tab-button', role: 'tab' }, 'Таб 2')
                ),
                h('div', { className: 'tabs-content' }, props.children)
            );
        }
    });

    /* ==========================================================================
       ACCORDION BLOCK
       ========================================================================== */

    registerBlockType('atk-ved/accordion', {
        title: 'Аккордеон',
        description: 'Раскрывающийся список (FAQ)',
        icon: icons.accordion,
        category: 'atk-ved',
        keywords: ['accordion', 'аккордеон', 'faq', 'вопросы'],
        attributes: {
            accordionId: { type: 'string', default: 'accordion-' + Date.now() },
            exclusive: { type: 'boolean', default: false },
            seamless: { type: 'boolean', default: false },
            title: { type: 'string', default: 'Заголовок вопроса' },
            content: { type: 'string', default: 'Ответ на вопрос' },
        },
        edit: function(props) {
            const { attributes, setAttributes } = props;
            const { accordionId, exclusive, seamless, title, content } = attributes;

            return h(Fragment, null,
                h(InspectorControls, null,
                    h(PanelBody, { title: 'Настройки аккордеона', initialOpen: true },
                        h(ToggleControl, {
                            label: 'Только один открытый элемент',
                            checked: exclusive,
                            onChange: (value) => setAttributes({ exclusive: value })
                        }),
                        h(ToggleControl, {
                            label: 'Бесшовный стиль',
                            checked: seamless,
                            onChange: (value) => setAttributes({ seamless: value })
                        })
                    )
                ),
                h('div', { className: `accordion ${exclusive ? 'accordion-exclusive' : ''} ${seamless ? 'accordion-seamless' : ''}` },
                    h('div', { className: 'accordion-item' },
                        h('button', { className: 'accordion-header' },
                            h(RichText, {
                                tagName: 'span',
                                className: 'accordion-title',
                                value: title,
                                onChange: (value) => setAttributes({ title: value }),
                                placeholder: 'Вопрос'
                            }),
                            h('span', { className: 'accordion-icon' }, '▼')
                        ),
                        h('div', { className: 'accordion-body' },
                            h('div', { className: 'accordion-content' },
                                h(RichText, {
                                    tagName: 'div',
                                    value: content,
                                    onChange: (value) => setAttributes({ content: value }),
                                    placeholder: 'Ответ'
                                })
                            )
                        )
                    )
                )
            );
        },
        save: function(props) {
            const { attributes } = props;
            const { title, content } = attributes;

            return h('div', { className: 'accordion' },
                h('div', { className: 'accordion-item' },
                    h('button', { className: 'accordion-header', 'aria-expanded': 'false' },
                        h('span', { className: 'accordion-title' }, title),
                        h('span', { className: 'accordion-icon' },
                            h('svg', { width: 20, height: 20, viewBox: '0 0 24 24', fill: 'none', stroke: 'currentColor', 'strokeWidth': 2 },
                                h('polyline', { points: '6 9 12 15 18 9' })
                            )
                        )
                    ),
                    h('div', { className: 'accordion-body' },
                        h('div', { className: 'accordion-content' }, content)
                    )
                )
            );
        }
    });

    /* ==========================================================================
       ALERT BLOCK
       ========================================================================== */

    registerBlockType('atk-ved/alert', {
        title: 'Уведомление (Alert)',
        description: 'Блок уведомления с иконкой',
        icon: icons.alert,
        category: 'atk-ved',
        keywords: ['alert', 'уведомление', 'сообщение'],
        attributes: {
            type: { type: 'string', default: 'info' },
            title: { type: 'string', default: 'Заголовок' },
            content: { type: 'string', default: 'Текст уведомления' },
            dismissible: { type: 'boolean', default: true },
        },
        edit: function(props) {
            const { attributes, setAttributes } = props;
            const { type, title, content, dismissible } = attributes;

            return h(Fragment, null,
                h(InspectorControls, null,
                    h(PanelBody, { title: 'Настройки уведомления', initialOpen: true },
                        h(SelectControl, {
                            label: 'Тип',
                            value: type,
                            options: [
                                { label: 'Информация (info)', value: 'info' },
                                { label: 'Успех (success)', value: 'success' },
                                { label: 'Предупреждение (warning)', value: 'warning' },
                                { label: 'Ошибка (error)', value: 'error' },
                            ],
                            onChange: (value) => setAttributes({ type: value })
                        }),
                        h(ToggleControl, {
                            label: 'Можно закрыть',
                            checked: dismissible,
                            onChange: (value) => setAttributes({ dismissible: value })
                        })
                    )
                ),
                h('div', { className: `alert alert-${type} ${dismissible ? 'alert-dismissible' : ''}` },
                    h('div', { className: 'alert-icon' }, getAlertIcon(type)),
                    h('div', { className: 'alert-content' },
                        h(RichText, {
                            tagName: 'strong',
                            className: 'alert-title',
                            value: title,
                            onChange: (value) => setAttributes({ title: value }),
                            placeholder: 'Заголовок'
                        }),
                        h(RichText, {
                            tagName: 'p',
                            className: 'alert-message',
                            value: content,
                            onChange: (value) => setAttributes({ content: value }),
                            placeholder: 'Текст уведомления'
                        })
                    ),
                    dismissible && h('button', { className: 'alert-close' }, '×')
                )
            );
        },
        save: function(props) {
            const { attributes } = props;
            const { type, title, content, dismissible } = attributes;

            return h('div', { 
                className: `alert alert-${type} ${dismissible ? 'alert-dismissible' : ''}`,
                role: 'alert'
            },
                h('div', { className: 'alert-icon' }, getAlertIcon(type)),
                h('div', { className: 'alert-content' },
                    h('strong', { className: 'alert-title' }, title),
                    h('p', { className: 'alert-message' }, content)
                ),
                dismissible && h('button', { 
                    className: 'alert-close', 
                    'aria-label': 'Закрыть' 
                }, '×')
            );
        }
    });

    function getAlertIcon(type) {
        const icons = {
            info: 'ℹ️',
            success: '✅',
            warning: '⚠️',
            error: '❌'
        };
        return icons[type] || icons.info;
    }

    /* ==========================================================================
       PROGRESS BAR BLOCK
       ========================================================================== */

    registerBlockType('atk-ved/progress', {
        title: 'Прогресс бар',
        description: 'Индикатор прогресса',
        icon: icons.progress,
        category: 'atk-ved',
        keywords: ['progress', 'прогресс', 'загрузка'],
        attributes: {
            value: { type: 'number', default: 50 },
            max: { type: 'number', default: 100 },
            showLabel: { type: 'boolean', default: true },
            striped: { type: 'boolean', default: false },
            animated: { type: 'boolean', default: false },
            color: { type: 'string', default: 'primary' },
        },
        edit: function(props) {
            const { attributes, setAttributes } = props;
            const { value, max, showLabel, striped, animated, color } = attributes;
            const percentage = Math.round((value / max) * 100);

            return h(Fragment, null,
                h(InspectorControls, null,
                    h(PanelBody, { title: 'Настройки прогресса', initialOpen: true },
                        h(TextControl, {
                            label: 'Значение',
                            type: 'number',
                            value: value,
                            onChange: (value) => setAttributes({ value: parseInt(value) || 0 })
                        }),
                        h(TextControl, {
                            label: 'Максимум',
                            type: 'number',
                            value: max,
                            onChange: (value) => setAttributes({ max: parseInt(value) || 100 })
                        }),
                        h(ToggleControl, {
                            label: 'Показывать процент',
                            checked: showLabel,
                            onChange: (value) => setAttributes({ showLabel: value })
                        }),
                        h(SelectControl, {
                            label: 'Цвет',
                            value: color,
                            options: [
                                { label: 'Красный (primary)', value: 'primary' },
                                { label: 'Зелёный (success)', value: 'success' },
                                { label: 'Оранжевый (warning)', value: 'warning' },
                                { label: 'Синий (info)', value: 'info' },
                            ],
                            onChange: (value) => setAttributes({ color: value })
                        }),
                        h(ToggleControl, {
                            label: 'Полосатый',
                            checked: striped,
                            onChange: (value) => setAttributes({ striped: value })
                        }),
                        h(ToggleControl, {
                            label: 'Анимированный',
                            checked: animated,
                            onChange: (value) => setAttributes({ animated: value })
                        })
                    )
                ),
                h('div', { className: 'progress-bar' },
                    showLabel && h('div', { className: 'progress-label' },
                        percentage + '%'
                    ),
                    h('div', { 
                        className: `progress-fill progress-${color} ${striped ? 'progress-striped' : ''} ${animated ? 'progress-animated' : ''}`,
                        style: { width: percentage + '%' }
                    })
                )
            );
        },
        save: function(props) {
            const { attributes } = props;
            const { value, max, showLabel, striped, animated, color } = attributes;
            const percentage = Math.round((value / max) * 100);

            return h('div', { className: 'progress-bar' },
                showLabel && h('div', { className: 'progress-label' }, percentage + '%'),
                h('div', { 
                    className: `progress-fill progress-${color} ${striped ? 'progress-striped' : ''} ${animated ? 'progress-animated' : ''}`,
                    style: { width: percentage + '%' },
                    role: 'progressbar',
                    'aria-valuenow': value,
                    'aria-valuemin': 0,
                    'aria-valuemax': max
                })
            );
        }
    });

})();
