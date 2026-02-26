#!/bin/bash

# ============================================
# Тест производительности для АТК ВЭД
# ============================================
# Использование: ./test-performance.sh [url]
# ============================================

URL="${1:-http://localhost:8080}"
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${YELLOW}🚀 Тест производительности для ${URL}${NC}\n"

# Проверка доступности сайта
echo -e "${YELLOW}📡 Проверка доступности сайта...${NC}"
if curl -s -o /dev/null -w "%{http_code}" "$URL" | grep -q "200"; then
    echo -e "${GREEN}✅ Сайт доступен${NC}\n"
else
    echo -e "${RED}❌ Сайт недоступен${NC}\n"
    exit 1
fi

# Тест 1: Время загрузки страницы
echo -e "${YELLOW}⏱️  Время загрузки страницы...${NC}"
TIME_TOTAL=$(curl -s -o /dev/null -w "%{time_total}" "$URL")
TIME_TTFB=$(curl -s -o /dev/null -w "%{time_starttransfer}" "$URL")
SIZE=$(curl -s -o /dev/null -w "%{size_download}" "$URL")

echo "   TTFB: ${TIME_TTFB}s"
echo "   Total: ${TIME_TOTAL}s"
echo "   Size: $((SIZE / 1024))KB"

if (( $(echo "$TIME_TTFB < 0.5" | bc -l) )); then
    echo -e "   ${GREEN}✅ TTFB отличный${NC}"
elif (( $(echo "$TIME_TTFB < 1.0" | bc -l) )); then
    echo -e "   ${YELLOW}⚠️  TTFB приемлемый${NC}"
else
    echo -e "   ${RED}❌ TTFB слишком большой${NC}"
fi
echo ""

# Тест 2: Проверка сжатия Gzip
echo -e "${YELLOW}🗜️  Проверка Gzip сжатия...${NC}"
GZIP=$(curl -s -I -H "Accept-Encoding: gzip" "$URL" | grep -i "content-encoding" | grep -i "gzip")
if [ -n "$GZIP" ]; then
    echo -e "${GREEN}✅ Gzip включён${NC}\n"
else
    echo -e "${RED}❌ Gzip не включён${NC}\n"
fi

# Тест 3: Проверка кэширования
echo -e "${YELLOW}📦 Проверка кэширования...${NC}"
CACHE_CONTROL=$(curl -s -I "$URL" | grep -i "cache-control")
if [ -n "$CACHE_CONTROL" ]; then
    echo "   $CACHE_CONTROL"
    echo -e "${GREEN}✅ Заголовки кэширования присутствуют${NC}\n"
else
    echo -e "${RED}❌ Заголовки кэширования отсутствуют${NC}\n"
fi

# Тест 4: Проверка WebP
echo -e "${YELLOW}🖼️  Проверка WebP поддержки...${NC}"
WEBP_TEST=$(curl -s -I -H "Accept: image/webp" "$URL/images/" 2>/dev/null | grep -i "content-type" | grep -i "webp")
if [ -n "$WEBP_TEST" ]; then
    echo -e "${GREEN}✅ WebP поддерживается${NC}\n"
else
    echo -e "${YELLOW}⚠️  WebP не обнаружен (возможно нет изображений на главной)${NC}\n"
fi

# Тест 5: Проверка HTTPS
echo -e "${YELLOW}🔒 Проверка HTTPS...${NC}"
if [[ "$URL" == https://* ]]; then
    echo -e "${GREEN}✅ HTTPS используется${NC}\n"
else
    echo -e "${YELLOW}⚠️  HTTPS не используется (рекомендуется для продакшена)${NC}\n"
fi

# Тест 6: Проверка HTTP/2
echo -e "${YELLOW}🌐 Проверка HTTP/2...${NC}"
HTTP_VERSION=$(curl -s -I --http2 "$URL" 2>&1 | head -n 1)
if [[ "$HTTP_VERSION" == *"HTTP/2"* ]]; then
    echo -e "${GREEN}✅ HTTP/2 поддерживается${NC}\n"
else
    echo -e "${YELLOW}⚠️  HTTP/2 не обнаружен${NC}\n"
fi

# Тест 7: Количество запросов
echo -e "${YELLOW}📊 Анализ ресурсов...${NC}"
RESOURCES=$(curl -s "$URL" | grep -oE '(src|href)="[^"]+"' | wc -l)
echo "   Количество ресурсов: $RESOURCES"

if [ $RESOURCES -lt 50 ]; then
    echo -e "   ${GREEN}✅ Хорошее количество запросов${NC}\n"
else
    echo -e "   ${YELLOW}⚠️  Много запросов, рассмотрите объединение файлов${NC}\n"
fi

# Тест 8: Проверка минификации CSS/JS
echo -e "${YELLOW}📐 Проверка минификации...${NC}"
CSS_MIN=$(curl -s "$URL" | grep -oE 'href="[^"]+\.css"' | grep -v "\.min\.css" | wc -l)
JS_MIN=$(curl -s "$URL" | grep -oE 'src="[^"]+\.js"' | grep -v "\.min\.js" | wc -l)

echo "   Не минифицированных CSS: $CSS_MIN"
echo "   Не минифицированных JS: $JS_MIN"

if [ $CSS_MIN -eq 0 ] && [ $JS_MIN -eq 0 ]; then
    echo -e "   ${GREEN}✅ Все файлы минифицированы${NC}\n"
else
    echo -e "   ${YELLOW}⚠️  Есть не минифицированные файлы${NC}\n"
fi

# Итоговый отчёт
echo -e "${YELLOW}========================================${NC}"
echo -e "${YELLOW}📋 ИТОГОВЫЙ ОТЧЁТ${NC}"
echo -e "${YELLOW}========================================${NC}"

SCORE=100

# Штрафы
(( $(echo "$TIME_TTFB > 1.0" | bc -l) )) && SCORE=$((SCORE - 20))
[ -z "$GZIP" ] && SCORE=$((SCORE - 15))
[ -z "$CACHE_CONTROL" ] && SCORE=$((SCORE - 15))
[ $RESOURCES -gt 80 ] && SCORE=$((SCORE - 10))
[ $CSS_MIN -gt 0 ] && SCORE=$((SCORE - 10))
[ $JS_MIN -gt 0 ] && SCORE=$((SCORE - 10))
[[ "$URL" != https://* ]] && SCORE=$((SCORE - 5))

echo ""
if [ $SCORE -ge 90 ]; then
    echo -e "${GREEN}✅ Оценка: ${SCORE}/100 — Отлично!${NC}"
elif [ $SCORE -ge 70 ]; then
    echo -e "${YELLOW}⚠️  Оценка: ${SCORE}/100 — Хорошо, но есть куда улучшать${NC}"
else
    echo -e "${RED}❌ Оценка: ${SCORE}/100 — Требуется оптимизация${NC}"
fi

echo ""
echo -e "${YELLOW}Рекомендации:${NC}"
[ $(echo "$TIME_TTFB > 0.5" | bc -l) ] && echo "   • Включить кэширование Redis"
[ -z "$GZIP" ] && echo "   • Включить Gzip сжатие"
[ -z "$CACHE_CONTROL" ] && echo "   • Настроить заголовки кэширования"
[ $CSS_MIN -gt 0 ] && echo "   • Запустить 'npm run build' для минификации"
[ $JS_MIN -gt 0 ] && echo "   • Запустить 'npm run build' для минификации"
[ $RESOURCES -gt 80 ] && echo "   • Объединить CSS/JS файлы"

echo ""
echo -e "${YELLOW}========================================${NC}"
