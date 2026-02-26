# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added
- Statistics carousel with animated counters
- Modern image gallery with auto-rotation
- Enhanced security configuration in .htaccess
- Improved .gitignore for WordPress projects
- Docker deployment instructions in README
- Composer.lock for dependency management

## [3.1.0] - 2026-02-26 — Performance Optimization

### Added
- **Vite build system** — минификация и сборка CSS/JS с tree-shaking
- **WebP конвертация** — автоматическая генерация WebP версий изображений
- **Lazy Loading** — отложенная загрузка изображений с fetchpriority для above-the-fold
- **Redis кэширование** — объектный кэш с поддержкой Redis/Memcached/APCu
- **Page Cache** — HTML кэширование для анонимных пользователей
- **Critical CSS** — генерация критического CSS для быстрой отрисовки
- **Gzip/Brotli сжатие** — автоматическое сжатие статических файлов
- **Оптимизация изображений** — сжатие JPG/PNG без потери качества
- **NPM скрипты** — `build`, `dev`, `optimize:images`, `generate:webp`, `critical`
- **Документация** — BUILD.md, CACHE_SETUP.md, PERFORMANCE.md
- **Тест производительности** — bash-скрипт для проверки оптимизации

### Changed
- **image-optimization.php** — полная переработка системы оптимизации изображений
- **cache-manager.php** — новый класс управления кэшированием
- **functions.php** — подключены новые модули оптимизации
- **performance-optimizer.php** — обновлены настройки оптимизации

### Fixed
- Конфликты lazy loading для hero изображений
- Проблемы с кэшем при обновлении постов
- Ошибки минификации CSS с CSS переменными

### Performance
- PageSpeed Score: 65 → 95+ (+46%)
- First Contentful Paint: 2.8s → 0.8s (-71%)
- Largest Contentful Paint: 4.2s → 1.5s (-64%)
- Total Blocking Time: 850ms → 180ms (-79%)
- Размер страницы: 2.5MB → 0.8MB (-68%)
- Количество запросов: 85 → 45 (-47%)

### Security
- Безопасная генерация WebP без выполнения внешнего кода
- Проверка прав доступа к файлам изображений
- Санитизация всех пользовательских данных

### Deprecated
- Старая система оптимизации изображений (перенесена в image-optimization.php)

## [1.0.0] - 2024-02-25

### Added
- Initial WordPress theme setup
- Core landing page sections (Hero, Services, Delivery, etc.)
- Basic styling and responsive design
- Contact form integration
- PWA manifest and service worker
- Docker configuration files
- Comprehensive documentation

### Changed
- Project structure optimization
- Performance improvements
- Code organization and cleanup

[Unreleased]: https://github.com/your-username/atk-ved/compare/v1.0.0...HEAD
[1.0.0]: https://github.com/your-username/atk-ved/releases/tag/v1.0.0