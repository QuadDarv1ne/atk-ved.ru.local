#!/usr/bin/env bash
# =============================================================================
# ATK VED Theme - Install Git Hooks
# =============================================================================
# Установка pre-commit хуков для проверок кода
#
# Использование:
#   ./install-hooks.sh              # Установить хуки
#   ./install-hooks.sh --uninstall  # Удалить хуки
# =============================================================================

set -e

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
GITHOOKS_DIR="$ROOT_DIR/.githooks"
GIT_HOOKS_DIR="$ROOT_DIR/.git/hooks"

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
CYAN='\033[0;36m'
NC='\033[0m'

log_info() {
    echo -e "${CYAN}ℹ $1${NC}"
}

log_success() {
    echo -e "${GREEN}✓ $1${NC}"
}

log_error() {
    echo -e "${RED}✗ $1${NC}"
}

# Check if .git directory exists
if [ ! -d "$ROOT_DIR/.git" ]; then
    log_error "Директория .git не найдена. Это не git репозиторий."
    exit 1
fi

# Check if .githooks directory exists
if [ ! -d "$GITHOOKS_DIR" ]; then
    log_error "Директория .githooks не найдена."
    exit 1
fi

# Parse arguments
if [ "$1" == "--uninstall" ]; then
    # Uninstall hooks
    log_info "Удаление git хуков..."
    
    if [ -f "$GIT_HOOKS_DIR/pre-commit" ]; then
        rm "$GIT_HOOKS_DIR/pre-commit"
        log_success "pre-commit хук удалён"
    fi
    
    if [ -f "$GIT_HOOKS_DIR/commit-msg" ]; then
        rm "$GIT_HOOKS_DIR/commit-msg"
        log_success "commit-msg хук удалён"
    fi
    
    # Remove git config
    git config --unset core.hooksPath 2>/dev/null || true
    log_success "Git хуки удалены"
    
    exit 0
fi

# Install hooks
echo ""
echo "╔═══════════════════════════════════════════════════════════╗"
echo "║         ATK VED - Install Git Hooks                       ║"
echo "╚═══════════════════════════════════════════════════════════╝"
echo ""

# Method 1: Use core.hooksPath (Git 2.9+)
log_info "Настройка git hooksPath..."
git config core.hooksPath .githooks
log_success "Git настроен на использование .githooks"

# Method 2: Copy hooks (for older git versions)
log_info "Копирование хуков в .git/hooks..."

# Create hooks directory if it doesn't exist
mkdir -p "$GIT_HOOKS_DIR"

# Copy pre-commit hook
if [ -f "$GITHOOKS_DIR/pre-commit" ]; then
    cp "$GITHOOKS_DIR/pre-commit" "$GIT_HOOKS_DIR/pre-commit"
    chmod +x "$GIT_HOOKS_DIR/pre-commit"
    log_success "pre-commit хук установлен"
fi

# Make hooks executable
chmod +x "$GITHOOKS_DIR"/* 2>/dev/null || true
log_success "Хуки сделаны исполняемыми"

# Verify installation
echo ""
log_info "Проверка установки..."

if [ "$(git config core.hooksPath)" == ".githooks" ]; then
    log_success "core.hooksPath настроен правильно"
else
    log_warning "core.hooksPath не настроен"
fi

if [ -f "$GIT_HOOKS_DIR/pre-commit" ]; then
    log_success "pre-commit файл существует"
else
    log_warning "pre-commit файл не найден"
fi

# Summary
echo ""
echo "═══════════════════════════════════════════════════════════"
log_success "Git хуки успешно установлены!"
echo ""
log_info "Теперь перед каждым коммитом будут выполняться:"
echo "  - Проверка синтаксиса PHP"
echo "  - PHP_CodeSniffer (WordPress Coding Standards)"
echo "  - PHPStan (статический анализ)"
echo "  - ESLint (JavaScript)"
echo "  - Stylelint (CSS/SCSS)"
echo "  - Проверка на секретные ключи"
echo "  - Проверка размера файлов"
echo ""
log_info "Для удаления хуков выполните:"
echo "  ./install-hooks.sh --uninstall"
echo ""
