@echo off
REM =============================================================================
REM ATK VED Theme - Installation Script (Windows)
REM =============================================================================
REM Быстрая установка и настройка проекта
REM
REM Использование:
REM   install.bat              # Полная установка
REM   install.bat --skip-npm   # Пропустить npm зависимости
REM   install.bat --dev        # Режим разработки (по умолчанию)
REM   install.bat --prod       # Режим продакшена
REM =============================================================================

setlocal enabledelayedexpansion

REM Colors (ANSI escape codes)
set "RED=[31m"
set "GREEN=[32m"
set "YELLOW=[33m"
set "BLUE=[34m"
set "CYAN=[36m"
set "NC=[0m"

REM Get script directory
set "ROOT_DIR=%~dp0"
set "THEME_DIR=%ROOT_DIR%wp-content\themes\atk-ved"

REM Flags
set "SKIP_NPM=false"
set "MODE=dev"

REM =============================================================================
REM Parse Arguments
REM =============================================================================

:parse_args
if "%~1"=="" goto :end_parse_args
if /i "%~1"=="--skip-npm" (
    set "SKIP_NPM=true"
    shift
    goto :parse_args
)
if /i "%~1"=="--dev" (
    set "MODE=dev"
    shift
    goto :parse_args
)
if /i "%~1"=="--prod" (
    set "MODE=prod"
    shift
    goto :parse_args
)
if /i "%~1"=="--help" (
    echo ATK VED Theme Installation Script (Windows)
    echo.
    echo Использование:
    echo   install.bat [OPTIONS]
    echo.
    echo Опции:
    echo   --skip-npm    Пропустить установку npm зависимостей
    echo   --dev         Режим разработки (по умолчанию)
    echo   --prod        Режим продакшена
    echo   --help        Показать эту справку
    exit /b 0
)
shift
goto :parse_args

:end_parse_args

REM =============================================================================
REM Helper Functions
REM =============================================================================

:log_info
echo %CYAN%ℹ %~1%NC%
goto :eof

:log_success
echo %GREEN%✓ %~1%NC%
goto :eof

:log_error
echo %RED%✗ %~1%NC%
goto :eof

:log_warning
echo %YELLOW%⚠ %~1%NC%
goto :eof

:log_step
echo.
echo %BLUE%▶ %~1%NC%
goto :eof

REM =============================================================================
REM Main Installation
REM =============================================================================

echo.
echo ╔═══════════════════════════════════════════════════════════╗
echo ║         ATK VED Theme - Installation (Windows)            ║
echo ╚═══════════════════════════════════════════════════════════╝
echo.

REM Check PHP
call :log_step "Проверка PHP"
where php >nul 2>nul
if %errorlevel% equ 0 (
    for /f "tokens=*" %%i in ('php -r "echo PHP_VERSION;"') do set PHP_VERSION=%%i
    call :log_success "PHP версия: !PHP_VERSION!"
) else (
    call :log_error "PHP не найден. Установите PHP 8.1+"
    exit /b 1
)

REM Check Composer
call :log_step "Проверка Composer"
where composer >nul 2>nul
if %errorlevel% equ 0 (
    for /f "tokens=*" %%i in ('composer --version') do set COMPOSER_VERSION=%%i
    call :log_success "!COMPOSER_VERSION!"
) else (
    call :log_error "Composer не найден. Установите Composer"
    exit /b 1
)

REM Setup .env
call :log_step "Настройка .env файла"
if exist "%ROOT_DIR%.env" (
    call :log_warning ".env файл уже существует"
) else (
    if exist "%ROOT_DIR%.env.example" (
        copy "%ROOT_DIR%.env.example" "%ROOT_DIR%.env" >nul
        call :log_success ".env файл создан"
        call :log_warning "Не забудьте настроить .env файл!"
    ) else (
        call :log_error ".env.example не найден"
    )
)

REM Install Composer dependencies (root)
call :log_step "Установка PHP зависимостей (root)"
if exist "%ROOT_DIR%composer.json" (
    call :log_info "Выполнение: composer install"
    composer install --no-interaction --prefer-dist
    if %errorlevel% equ 0 (
        call :log_success "Выполнено"
    ) else (
        call :log_error "Ошибка при выполнении"
    )
) else (
    call :log_warning "composer.json не найден в корне"
)

REM Install Composer dependencies (theme)
call :log_step "Установка PHP зависимостей (theme)"
if exist "%THEME_DIR%\composer.json" (
    call :log_info "Выполнение: composer install (theme)"
    composer install --no-interaction --prefer-dist --working-dir=%THEME_DIR%
    if %errorlevel% equ 0 (
        call :log_success "Выполнено"
    ) else (
        call :log_error "Ошибка при выполнении"
    )
) else (
    call :log_warning "composer.json не найден в теме"
)

REM Install Node.js dependencies
if "%SKIP_NPM%"=="false" (
    call :log_step "Установка JavaScript зависимостей"
    if exist "%THEME_DIR%\package.json" (
        call :log_info "Выполнение: npm install"
        npm install --prefix %THEME_DIR%
        if %errorlevel% equ 0 (
            call :log_success "Выполнено"
        ) else (
            call :log_error "Ошибка при выполнении"
        )
    ) else (
        call :log_warning "package.json не найден"
    )
)

REM Build assets
call :log_step "Сборка ассетов"
if "%MODE%"=="dev" (
    call :log_info "Режим разработки"
    call :log_info "Выполнение: npm run dev"
    npm run dev --prefix %THEME_DIR%
) else (
    call :log_info "Режим продакшена"
    call :log_info "Выполнение: npm run build"
    npm run build --prefix %THEME_DIR%
)

REM Create directories
call :log_step "Создание директорий"
if not exist "%ROOT_DIR%wp-content\uploads" mkdir "%ROOT_DIR%wp-content\uploads"
if not exist "%ROOT_DIR%wp-content\cache" mkdir "%ROOT_DIR%wp-content\cache"
if not exist "%THEME_DIR%\dist" mkdir "%THEME_DIR%\dist"
call :log_success "Директории созданы"

REM =============================================================================
REM Summary
REM =============================================================================

echo.
echo ╔═══════════════════════════════════════════════════════════╗
echo ║              УСТАНОВКА ЗАВЕРШЕНА                          ║
echo ╚═══════════════════════════════════════════════════════════╝
echo.
call :log_info "Следующие шаги:"
echo   1. Настройте .env файл (база данных, URL)
echo   2. Создайте базу данных
echo   3. Запустите WordPress
echo   4. Активируйте тему 'АТК ВЭД'
echo.
call :log_info "Полезные команды:"
echo   composer install          PHP зависимости
echo   npm install               npm зависимости
echo   npm run dev               Сборка для разработки
echo   npm run build             Сборка для продакшена
echo   docker-compose up -d      Запуск Docker
echo.
call :log_success "Установка завершена!"
echo.

endlocal
