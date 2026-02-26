@echo off
REM =============================================================================
REM Запуск MySQL в OSPanel
REM =============================================================================

echo ╔═══════════════════════════════════════════════════════════╗
echo ║         OSPanel - Запуск MySQL                            ║
echo ╚═══════════════════════════════════════════════════════════╝
echo.

REM Проверяем, существует ли OSPanel
if not exist "M:\OSPanel" (
    echo [ERROR] OSPanel не найден в M:\OSPanel
    pause
    exit /b 1
)

REM Проверяем, запущен ли MySQL
netstat -an | findstr ":3306" >nul 2>nul
if %errorlevel% equ 0 (
    echo [OK] MySQL уже запущен на порту 3306
    echo.
    echo Откройте сайт: http://atk-ved.ru.local
    pause
    exit /b 0
)

echo [INFO] MySQL не запущен. Запускаем OSPanel...
echo.

REM Запускаем OSPanel
start "" "M:\OSPanel\OSPanel.exe"

echo.
echo ╔═══════════════════════════════════════════════════════════╗
echo ║  ВНИМАНИЕ:                                                ║
echo ║  1. Откройте OSPanel (флажок в трее возле часов)          ║
echo ║  2. Нажмите: Запустить → Перезапустить                    ║
echo ║  3. Дождитесь зелёного флажка                             ║
echo ║  4. Обновите страницу сайта                               ║
echo ╚═══════════════════════════════════════════════════════════╝
echo.
echo Сайт: http://atk-ved.ru.local
echo phpMyAdmin: http://localhost/phpmyadmin
echo.
pause
