# 🚀 GitHub Actions CI/CD Setup

## Настройка

### 1. Секреты GitHub

Добавьте в репозиторий **Settings → Secrets and variables → Actions**:

```bash
# Для деплоя на WordPress.org (если тема публикуется)
SVN_USERNAME: your_username
SVN_PASSWORD: your_password

# Для деплоя на хостинг
FTP_HOST: ftp.example.com
FTP_USERNAME: user
FTP_PASSWORD: password
FTP_DIR: /public_html/wp-content/themes/atk-ved/

# Для отправки уведомлений
SLACK_WEBHOOK: https://hooks.slack.com/...
TELEGRAM_BOT_TOKEN: token
TELEGRAM_CHAT_ID: chat_id
```

### 2. Ветки

- `main` — продакшен версия
- `develop` — разработка
- `feature/*` — новые функции

### 3. Теги

```bash
# Создать тег
git tag -a v3.3.0 -m "Release v3.3.0"
git push origin v3.3.0

# Автоматически создаст релиз на GitHub
```

---

## Workflow процессы

### При push в main/develop

1. ✅ PHP Quality Checks (PHPStan, PHPCS)
2. ✅ JavaScript Quality Checks (ESLint, Stylelint)
3. ✅ PHPUnit Tests
4. ✅ Build Theme

### При создании тега

1. ✅ Все проверки из push
2. ✅ Создание релиза на GitHub
3. ✅ Публикация zip архива

---

## Локальное тестирование

```bash
# Запустить все тесты
composer test

# Только PHPStan
composer phpstan

# Только PHPUnit
composer phpunit

# Только PHPCS
composer phpcs
```

---

## Статусы

| Статус | Значение |
|--------|----------|
| 🟢 | Все тесты пройдены |
| 🟡 | Есть предупреждения |
| 🔴 | Тесты не пройдены |

---

## Troubleshooting

### Тесты не запускаются

```bash
# Проверить версию PHP
php -v

# Переустановить зависимости
composer install

# Запустить тесты локально
composer test
```

### Ошибки сборки

```bash
# Очистить кэш npm
npm cache clean --force

# Переустановить node_modules
rm -rf node_modules package-lock.json
npm install

# Запустить сборку
npm run build
```
