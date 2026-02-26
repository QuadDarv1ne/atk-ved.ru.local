#!/bin/bash

# =============================================================================
# ATK VED Theme - Installation Script
# =============================================================================
# Быстрая установка и настройка проекта
#
# Использование:
#   ./install.sh              # Полная установка
#   ./install.sh --skip-npm   # Пропустить npm зависимости
#   ./install.sh --dev        # Режим разработки
#   ./install.sh --prod       # Режим продакшена
# =============================================================================

set -e

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
CYAN='\033[0;36m'
NC='\033[0m' # No Color

# Directories
ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
THEME_DIR="$ROOT_DIR/wp-content/themes/atk-ved"

# Flags
SKIP_NPM=false
MODE="dev"

# =============================================================================
# Helper Functions
# =============================================================================

log_info() {
    echo -e "${CYAN}ℹ $1${NC}"
}

log_success() {
    echo -e "${GREEN}✓ $1${NC}"
}

log_error() {
    echo -e "${RED}✗ $1${NC}"
}

log_warning() {
    echo -e "${YELLOW}⚠ $1${NC}"
}

log_step() {
    echo -e "\n${BLUE}▶ $1${NC}"
}

run_cmd() {
    log_info "Выполнение: $1"
    if eval "$1"; then
        log_success "Выполнено"
        return 0
    else
        log_error "Ошибка при выполнении"
        return 1
    fi
}

# =============================================================================
# Parse Arguments
# =============================================================================

while [[ $# -gt 0 ]]; do
    case $1 in
        --skip-npm)
            SKIP_NPM=true
            shift
            ;;
        --dev)
            MODE="dev"
            shift
            ;;
        --prod)
            MODE="prod"
            shift
            ;;
        --help)
            echo "ATK VED Theme Installation Script"
            echo ""
            echo "Использование:"
            echo "  ./install.sh [OPTIONS]"
            echo ""
            echo "Опции:"
            echo "  --skip-npm    Пропустить установку npm зависимостей"
            echo "  --dev         Режим разработки (по умолчанию)"
            echo "  --prod        Режим продакшена"
            echo "  --help        Показать эту справку"
            exit 0
            ;;
        *)
            log_error "Неизвестная опция: $1"
            exit 1
            ;;
    esac
done

# =============================================================================
# Main Installation
# =============================================================================

echo ""
echo "╔═══════════════════════════════════════════════════════════╗"
echo "║         ATK VED Theme - Installation                      ║"
echo "╚═══════════════════════════════════════════════════════════╝"
echo ""

# Check PHP
log_step "Проверка PHP"
if command -v php &> /dev/null; then
    PHP_VERSION=$(php -r "echo PHP_VERSION;")
    log_success "PHP версия: $PHP_VERSION"
else
    log_error "PHP не найден. Установите PHP 8.1+"
    exit 1
fi

# Check Composer
log_step "Проверка Composer"
if command -v composer &> /dev/null; then
    COMPOSER_VERSION=$(composer --version)
    log_success "$COMPOSER_VERSION"
else
    log_error "Composer не найден. Установите Composer"
    exit 1
fi

# Setup .env
log_step "Настройка .env файла"
if [ -f "$ROOT_DIR/.env" ]; then
    log_warning ".env файл уже существует"
else
    if [ -f "$ROOT_DIR/.env.example" ]; then
        cp "$ROOT_DIR/.env.example" "$ROOT_DIR/.env"
        log_success ".env файл создан"
        log_warning "Не забудьте настроить .env файл!"
    else
        log_error ".env.example не найден"
    fi
fi

# Install Composer dependencies (root)
log_step "Установка PHP зависимостей (root)"
if [ -f "$ROOT_DIR/composer.json" ]; then
    run_cmd "composer install --no-interaction --prefer-dist"
else
    log_warning "composer.json не найден в корне"
fi

# Install Composer dependencies (theme)
log_step "Установка PHP зависимостей (theme)"
if [ -f "$THEME_DIR/composer.json" ]; then
    run_cmd "composer install --no-interaction --prefer-dist --working-dir=$THEME_DIR"
else
    log_warning "composer.json не найден в теме"
fi

# Install Node.js dependencies
if [ "$SKIP_NPM" = false ]; then
    log_step "Установка JavaScript зависимостей"
    if [ -f "$THEME_DIR/package.json" ]; then
        run_cmd "npm install --prefix $THEME_DIR"
    else
        log_warning "package.json не найден"
    fi
fi

# Build assets
log_step "Сборка ассетов"
if [ "$MODE" = "dev" ]; then
    log_info "Режим разработки"
    run_cmd "npm run dev --prefix $THEME_DIR" || log_warning "Сборка не выполнена"
else
    log_info "Режим продакшена"
    run_cmd "npm run build --prefix $THEME_DIR" || log_warning "Сборка не выполнена"
fi

# Create directories
log_step "Создание директорий"
mkdir -p "$ROOT_DIR/wp-content/uploads"
mkdir -p "$ROOT_DIR/wp-content/cache"
mkdir -p "$THEME_DIR/dist"
log_success "Директории созданы"

# Set permissions
log_step "Настройка прав доступа"
chmod -R 755 "$ROOT_DIR/wp-content/uploads"
chmod -R 755 "$ROOT_DIR/wp-content/cache"
log_success "Права настроены"

# =============================================================================
# Summary
# =============================================================================

echo ""
echo "╔═══════════════════════════════════════════════════════════╗"
echo "║              УСТАНОВКА ЗАВЕРШЕНА                          ║"
echo "╚═══════════════════════════════════════════════════════════╝"
echo ""
log_info "Следующие шаги:"
echo "  1. Настройте .env файл (база данных, URL)"
echo "  2. Создайте базу данных"
echo "  3. Запустите WordPress"
echo "  4. Активируйте тему 'АТК ВЭД'"
echo ""
log_info "Полезные команды:"
echo "  composer install          # PHP зависимости"
echo "  npm install               # npm зависимости"
echo "  npm run dev               # Сборка для разработки"
echo "  npm run build             # Сборка для продакшена"
echo "  docker-compose up -d      # Запуск Docker"
echo ""
log_success "Установка завершена!"
echo ""
