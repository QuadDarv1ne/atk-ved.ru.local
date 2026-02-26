# =============================================================================
# Запуск OSPanel и проверка MySQL
# =============================================================================

Write-Host "╔═══════════════════════════════════════════════════════════╗" -ForegroundColor Cyan
Write-Host "║         OSPanel - Запуск MySQL                            ║" -ForegroundColor Cyan
Write-Host "╚═══════════════════════════════════════════════════════════╝" -ForegroundColor Cyan
Write-Host ""

# Проверка существования OSPanel
$ospanelPath = "M:\OSPanel"
if (!(Test-Path $ospanelPath)) {
    Write-Host "[ERROR] OSPanel не найден в $ospanelPath" -ForegroundColor Red
    Read-Host "Нажмите Enter для выхода"
    exit 1
}

# Проверка порта 3306
Write-Host "[INFO] Проверка порта 3306..." -ForegroundColor Yellow
$port3306 = Get-NetTCPConnection -LocalPort 3306 -ErrorAction SilentlyContinue

if ($port3306) {
    Write-Host "[OK] MySQL уже запущен на порту 3306" -ForegroundColor Green
    Write-Host ""
    Write-Host "Откройте сайт: http://atk-ved.ru.local" -ForegroundColor Cyan
    Read-Host "Нажмите Enter для выхода"
    exit 0
}

# Запуск OSPanel
Write-Host "[INFO] Запуск OSPanel..." -ForegroundColor Yellow
Start-Process "$ospanelPath\OSPanel.exe"

Write-Host ""
Write-Host "╔═══════════════════════════════════════════════════════════╗" -ForegroundColor Yellow
Write-Host "║  ВНИМАНИЕ:                                                ║" -ForegroundColor Yellow
Write-Host "║  1. Откройте OSPanel (флажок в трее возле часов)          ║" -ForegroundColor White
Write-Host "║  2. Нажмите: Запустить → Перезапустить                    ║" -ForegroundColor White
Write-Host "║  3. Дождитесь зелёного флажка                             ║" -ForegroundColor White
Write-Host "║  4. Обновите страницу сайта                               ║" -ForegroundColor White
Write-Host "╚═══════════════════════════════════════════════════════════╝" -ForegroundColor Yellow
Write-Host ""
Write-Host "Сайт: http://atk-ved.ru.local" -ForegroundColor Cyan
Write-Host "phpMyAdmin: http://localhost/phpmyadmin" -ForegroundColor Cyan
Write-Host ""
Write-Host "Нажмите Enter для продолжения..." -ForegroundColor Gray
Read-Host

# Повторная проверка через 10 секунд
Write-Host ""
Write-Host "[INFO] Ожидание запуска MySQL (10 секунд)..." -ForegroundColor Yellow
Start-Sleep -Seconds 10

Write-Host "[INFO] Повторная проверка..." -ForegroundColor Yellow
$port3306 = Get-NetTCPConnection -LocalPort 3306 -ErrorAction SilentlyContinue

if ($port3306) {
    Write-Host "[OK] MySQL запущен!" -ForegroundColor Green
    Write-Host ""
    Write-Host "Откройте сайт: http://atk-ved.ru.local" -ForegroundColor Cyan
} else {
    Write-Host "[WARNING] MySQL всё ещё не запущен" -ForegroundColor Yellow
    Write-Host ""
    Write-Host "Попробуйте:" -ForegroundColor Yellow
    Write-Host "  1. Запустить OSPanel от имени администратора" -ForegroundColor White
    Write-Host "  2. Проверить логи: M:\OSPanel\logs\" -ForegroundColor White
    Write-Host "  3. Перезапустить компьютер" -ForegroundColor White
}

Write-Host ""
Read-Host "Нажмите Enter для выхода"
