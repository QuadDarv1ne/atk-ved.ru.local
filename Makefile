# =============================================================================
# ATK VED Theme - Makefile
# =============================================================================
# Удобные команды для разработки
#
# Использование:
#   make install          # Установить зависимости
#   make dev              # Запуск для разработки
#   make build            # Сборка для продакшена
#   make test             # Запустить тесты
#   make lint             # Проверка кода
#   make docker-up        # Запуск Docker
#   make clean            # Очистка
# =============================================================================

.PHONY: help install install-composer install-npm dev build test lint phpcs phpstan eslint stylelint format docker-up docker-down docker-restart docker-logs clean hooks hooks-install hooks-uninstall

# =============================================================================
# Variables
# =============================================================================

SHELL := /bin/bash
ROOT_DIR := $(shell pwd)
THEME_DIR := $(ROOT_DIR)/wp-content/themes/atk-ved
VENDOR_DIR := $(ROOT_DIR)/vendor
NODE_MODULES := $(THEME_DIR)/node_modules

# Colors for output
COLOR_RESET := \033[0m
COLOR_GREEN := \033[32m
COLOR_YELLOW := \033[33m
COLOR_BLUE := \033[34m
COLOR_CYAN := \033[36m

# =============================================================================
# Default Target
# =============================================================================

help:
	@echo ""
	@echo "╔═══════════════════════════════════════════════════════════╗"
	@echo "║         ATK VED Theme - Available Commands                ║"
	@echo "╚═══════════════════════════════════════════════════════════╝"
	@echo ""
	@echo "  $(COLOR_CYAN)make install$(COLOR_RESET)         Установить все зависимости"
	@echo "  $(COLOR_CYAN)make dev$(COLOR_RESET)             Запуск для разработки (Vite watch)"
	@echo "  $(COLOR_CYAN)make build$(COLOR_RESET)           Сборка для продакшена"
	@echo "  $(COLOR_CYAN)make watch$(COLOR_RESET)           Сборка в режиме watch"
	@echo "  $(COLOR_CYAN)make test$(COLOR_RESET)            Запустить тесты"
	@echo "  $(COLOR_CYAN)make lint$(COLOR_RESET)            Проверка кода (phpcs + phpstan)"
	@echo "  $(COLOR_CYAN)make fix$(COLOR_RESET)             Исправить стиль кода"
	@echo "  $(COLOR_CYAN)make clean$(COLOR_RESET)           Очистка кэша и сборок"
	@echo ""
	@echo "  $(COLOR_CYAN)make docker-up$(COLOR_RESET)       Запуск Docker контейнеров"
	@echo "  $(COLOR_CYAN)make docker-down$(COLOR_RESET)     Остановка Docker контейнеров"
	@echo "  $(COLOR_CYAN)make docker-restart$(COLOR_RESET)  Перезапуск Docker"
	@echo "  $(COLOR_CYAN)make docker-logs$(COLOR_RESET)     Просмотр логов Docker"
	@echo ""
	@echo "  $(COLOR_CYAN)make hooks-install$(COLOR_RESET)   Установить git хуки"
	@echo "  $(COLOR_CYAN)make hooks-uninstall$(COLOR_RESET) Удалить git хуки"
	@echo ""
	@echo "  $(COLOR_CYAN)make setup$(COLOR_RESET)           Полная настройка проекта"
	@echo ""

# =============================================================================
# Installation
# =============================================================================

install: install-composer install-npm
	@echo -e "$(COLOR_GREEN)✓ Установка завершена$(COLOR_RESET)"

install-composer:
	@echo -e "$(COLOR_BLUE)▶ Установка PHP зависимостей...$(COLOR_RESET)"
	@composer install --no-interaction --prefer-dist
	@echo -e "$(COLOR_GREEN)✓ Composer зависимости установлены$(COLOR_RESET)"

install-npm:
	@echo -e "$(COLOR_BLUE)▶ Установка JavaScript зависимостей...$(COLOR_RESET)"
	@npm install --prefix $(THEME_DIR)
	@echo -e "$(COLOR_GREEN)✓ NPM зависимости установлены$(COLOR_RESET)"

# =============================================================================
# Development
# =============================================================================

dev:
	@echo -e "$(COLOR_BLUE)▶ Запуск режима разработки...$(COLOR_RESET)"
	@npm run dev --prefix $(THEME_DIR)

watch:
	@echo -e "$(COLOR_BLUE)▶ Запуск режима watch...$(COLOR_RESET)"
	@npm run watch --prefix $(THEME_DIR)

build:
	@echo -e "$(COLOR_BLUE)▶ Сборка для продакшена...$(COLOR_RESET)"
	@npm run build --prefix $(THEME_DIR)
	@echo -e "$(COLOR_GREEN)✓ Сборка завершена$(COLOR_RESET)"

# =============================================================================
# Testing
# =============================================================================

test:
	@echo -e "$(COLOR_BLUE)▶ Запуск тестов...$(COLOR_RESET)"
	@if [ -f "$(THEME_DIR)/vendor/bin/phpunit" ]; then \
		$(THEME_DIR)/vendor/bin/phpunit --configuration=$(THEME_DIR)/phpunit.xml; \
	else \
		echo -e "$(COLOR_YELLOW)⚠ PHPUnit не установлен. Установите зависимости: make install$(COLOR_RESET)"; \
	fi

# =============================================================================
# Code Quality
# =============================================================================

lint: phpcs phpstan
	@echo -e "$(COLOR_GREEN)✓ Все проверки пройдены$(COLOR_RESET)"

phpcs:
	@echo -e "$(COLOR_BLUE)▶ Проверка PHPCS...$(COLOR_RESET)"
	@if [ -f "$(THEME_DIR)/vendor/bin/phpcs" ]; then \
		$(THEME_DIR)/vendor/bin/phpcs --standard=WordPress $(THEME_DIR)/src/; \
	else \
		echo -e "$(COLOR_YELLOW)⚠ PHPCS не установлен. Установите зависимости: make install$(COLOR_RESET)"; \
	fi

phpstan:
	@echo -e "$(COLOR_BLUE)▶ Проверка PHPStan...$(COLOR_RESET)"
	@if [ -f "$(THEME_DIR)/vendor/bin/phpstan" ]; then \
		$(THEME_DIR)/vendor/bin/phpstan analyse --configuration=$(THEME_DIR)/phpstan.neon; \
	else \
		echo -e "$(COLOR_YELLOW)⚠ PHPStan не установлен. Установите зависимости: make install$(COLOR_RESET)"; \
	fi

eslint:
	@echo -e "$(COLOR_BLUE)▶ Проверка ESLint...$(COLOR_RESET)"
	@if [ -f "$(THEME_DIR)/node_modules/.bin/eslint" ]; then \
		$(THEME_DIR)/node_modules/.bin/eslint $(THEME_DIR)/src/js/; \
	else \
		echo -e "$(COLOR_YELLOW)⚠ ESLint не установлен. Установите npm зависимости: make install-npm$(COLOR_RESET)"; \
	fi

stylelint:
	@echo -e "$(COLOR_BLUE)▶ Проверка Stylelint...$(COLOR_RESET)"
	@if [ -f "$(THEME_DIR)/node_modules/.bin/stylelint" ]; then \
		$(THEME_DIR)/node_modules/.bin/stylelint $(THEME_DIR)/src/css/; \
	else \
		echo -e "$(COLOR_YELLOW)⚠ Stylelint не установлен. Установите npm зависимости: make install-npm$(COLOR_RESET)"; \
	fi

format:
	@echo -e "$(COLOR_BLUE)▶ Исправление стиля кода...$(COLOR_RESET)"
	@if [ -f "$(THEME_DIR)/vendor/bin/phpcbf" ]; then \
		$(THEME_DIR)/vendor/bin/phpcbf --standard=WordPress $(THEME_DIR)/src/; \
		echo -e "$(COLOR_GREEN)✓ PHP код отформатирован$(COLOR_RESET)"; \
	else \
		echo -e "$(COLOR_YELLOW)⚠ PHPCBF не установлен$(COLOR_RESET)"; \
	fi

# =============================================================================
# Docker
# =============================================================================

docker-up:
	@echo -e "$(COLOR_BLUE)▶ Запуск Docker контейнеров...$(COLOR_RESET)"
	@docker-compose up -d
	@echo -e "$(COLOR_GREEN)✓ Docker запущен$(COLOR_RESET)"
	@echo -e "$(COLOR_CYAN)  Сайт: http://localhost:8080$(COLOR_RESET)"
	@echo -e "$(COLOR_CYAN)  phpMyAdmin: http://localhost:8081$(COLOR_RESET)"

docker-down:
	@echo -e "$(COLOR_BLUE)▶ Остановка Docker контейнеров...$(COLOR_RESET)"
	@docker-compose down
	@echo -e "$(COLOR_GREEN)✓ Docker остановлен$(COLOR_RESET)"

docker-restart: docker-down docker-up
	@echo -e "$(COLOR_GREEN)✓ Docker перезапущен$(COLOR_RESET)"

docker-logs:
	@echo -e "$(COLOR_BLUE)▶ Логи Docker контейнеров...$(COLOR_RESET)"
	@docker-compose logs -f

docker-clean:
	@echo -e "$(COLOR_BLUE)▶ Очистка Docker...$(COLOR_RESET)"
	@docker-compose down -v
	@echo -e "$(COLOR_GREEN)✓ Docker очищен$(COLOR_RESET)"

# =============================================================================
# Git Hooks
# =============================================================================

hooks-install:
	@echo -e "$(COLOR_BLUE)▶ Установка git хуков...$(COLOR_RESET)"
	@bash $(ROOT_DIR)/install-hooks.sh
	@echo -e "$(COLOR_GREEN)✓ Git хуки установлены$(COLOR_RESET)"

hooks-uninstall:
	@echo -e "$(COLOR_BLUE)▶ Удаление git хуков...$(COLOR_RESET)"
	@bash $(ROOT_DIR)/install-hooks.sh --uninstall
	@echo -e "$(COLOR_GREEN)✓ Git хуки удалены$(COLOR_RESET)"

hooks: hooks-install

# =============================================================================
# Cleanup
# =============================================================================

clean:
	@echo -e "$(COLOR_BLUE)▶ Очистка...$(COLOR_RESET)"
	@rm -rf $(THEME_DIR)/dist
	@rm -rf $(THEME_DIR)/.vite
	@rm -rf $(ROOT_DIR)/wp-content/cache/*
	@rm -rf $(ROOT_DIR)/wp-content/uploads/cache/*
	@find $(ROOT_DIR) -name "*.log" -type f -delete
	@find $(ROOT_DIR) -name ".DS_Store" -type f -delete
	@find $(ROOT_DIR) -name "Thumbs.db" -type f -delete
	@echo -e "$(COLOR_GREEN)✓ Очистка завершена$(COLOR_RESET)"

clean-all: clean
	@echo -e "$(COLOR_BLUE)▶ Полная очистка...$(COLOR_RESET)"
	@rm -rf $(ROOT_DIR)/vendor
	@rm -rf $(THEME_DIR)/vendor
	@rm -rf $(THEME_DIR)/node_modules
	@echo -e "$(COLOR_GREEN)✓ Полная очистка завершена$(COLOR_RESET)"

# =============================================================================
# Setup
# =============================================================================

setup:
	@echo -e "$(COLOR_BLUE)▶ Полная настройка проекта...$(COLOR_RESET)"
	@bash $(ROOT_DIR)/install.sh
	@make hooks-install
	@echo ""
	@echo -e "$(COLOR_GREEN)╔═══════════════════════════════════════════════════════════╗$(COLOR_RESET)"
	@echo -e "$(COLOR_GREEN)║         НАСТРОЙКА ЗАВЕРШЕНА                               ║$(COLOR_RESET)"
	@echo -e "$(COLOR_GREEN)╚═══════════════════════════════════════════════════════════╝$(COLOR_RESET)"
	@echo ""
	@echo -e "$(COLOR_CYAN)Следующие шаги:$(COLOR_RESET)"
	@echo "  1. Настройте .env файл"
	@echo "  2. Создайте базу данных"
	@echo "  3. Запустите: make docker-up"
	@echo "  4. Откройте: http://localhost:8080"
	@echo ""

# =============================================================================
# Version
# =============================================================================

version:
	@echo "ATK VED Theme v3.3.0"
