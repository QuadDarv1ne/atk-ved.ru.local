#!/bin/bash

# Image Optimization Script
# Оптимизирует PNG и JPG изображения в папке images/
# Требует: imagemagick, optipng, jpegoptim

set -e

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
THEME_DIR="$(dirname "$SCRIPT_DIR")"
IMAGES_DIR="$THEME_DIR/images"

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "  Оптимизация изображений"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""

# Проверка наличия утилит
check_tool() {
    if ! command -v $1 &> /dev/null; then
        echo "❌ $1 не установлен"
        echo "   Установите: $2"
        return 1
    fi
    echo "✓ $1 установлен"
    return 0
}

echo "Проверка утилит:"
HAS_IMAGEMAGICK=$(check_tool "convert" "sudo apt-get install imagemagick" && echo 1 || echo 0)
HAS_OPTIPNG=$(check_tool "optipng" "sudo apt-get install optipng" && echo 1 || echo 0)
HAS_JPEGOPTIM=$(check_tool "jpegoptim" "sudo apt-get install jpegoptim" && echo 1 || echo 0)
echo ""

if [ $HAS_IMAGEMAGICK -eq 0 ] && [ $HAS_OPTIPNG -eq 0 ] && [ $HAS_JPEGOPTIM -eq 0 ]; then
    echo "❌ Не установлены необходимые утилиты"
    exit 1
fi

# Счётчики
PNG_COUNT=0
JPG_COUNT=0
TOTAL_BEFORE=0
TOTAL_AFTER=0

# Оптимизация PNG
if [ $HAS_OPTIPNG -eq 1 ]; then
    echo "Оптимизация PNG файлов..."
    while IFS= read -r -d '' file; do
        BEFORE=$(stat -f%z "$file" 2>/dev/null || stat -c%s "$file")
        optipng -quiet -o7 "$file"
        AFTER=$(stat -f%z "$file" 2>/dev/null || stat -c%s "$file")
        
        SAVED=$((BEFORE - AFTER))
        if [ $SAVED -gt 0 ]; then
            PERCENT=$(awk "BEGIN {printf \"%.1f\", ($SAVED/$BEFORE)*100}")
            echo "  ✓ $(basename "$file"): -${SAVED} байт (-${PERCENT}%)"
        fi
        
        PNG_COUNT=$((PNG_COUNT + 1))
        TOTAL_BEFORE=$((TOTAL_BEFORE + BEFORE))
        TOTAL_AFTER=$((TOTAL_AFTER + AFTER))
    done < <(find "$IMAGES_DIR" -type f -name "*.png" -print0)
    echo ""
fi

# Оптимизация JPG
if [ $HAS_JPEGOPTIM -eq 1 ]; then
    echo "Оптимизация JPG файлов..."
    while IFS= read -r -d '' file; do
        BEFORE=$(stat -f%z "$file" 2>/dev/null || stat -c%s "$file")
        jpegoptim --quiet --strip-all --max=85 "$file"
        AFTER=$(stat -f%z "$file" 2>/dev/null || stat -c%s "$file")
        
        SAVED=$((BEFORE - AFTER))
        if [ $SAVED -gt 0 ]; then
            PERCENT=$(awk "BEGIN {printf \"%.1f\", ($SAVED/$BEFORE)*100}")
            echo "  ✓ $(basename "$file"): -${SAVED} байт (-${PERCENT}%)"
        fi
        
        JPG_COUNT=$((JPG_COUNT + 1))
        TOTAL_BEFORE=$((TOTAL_BEFORE + BEFORE))
        TOTAL_AFTER=$((TOTAL_AFTER + AFTER))
    done < <(find "$IMAGES_DIR" -type f \( -name "*.jpg" -o -name "*.jpeg" \) -print0)
    echo ""
fi

# Конвертация в WebP (если установлен ImageMagick)
if [ $HAS_IMAGEMAGICK -eq 1 ]; then
    echo "Создание WebP версий..."
    WEBP_COUNT=0
    
    while IFS= read -r -d '' file; do
        WEBP_FILE="${file%.*}.webp"
        if [ ! -f "$WEBP_FILE" ]; then
            convert "$file" -quality 85 "$WEBP_FILE"
            echo "  ✓ Создан: $(basename "$WEBP_FILE")"
            WEBP_COUNT=$((WEBP_COUNT + 1))
        fi
    done < <(find "$IMAGES_DIR" -type f \( -name "*.jpg" -o -name "*.jpeg" -o -name "*.png" \) -print0)
    
    echo "  Создано WebP файлов: $WEBP_COUNT"
    echo ""
fi

# Итоги
TOTAL_SAVED=$((TOTAL_BEFORE - TOTAL_AFTER))
if [ $TOTAL_BEFORE -gt 0 ]; then
    TOTAL_PERCENT=$(awk "BEGIN {printf \"%.1f\", ($TOTAL_SAVED/$TOTAL_BEFORE)*100}")
else
    TOTAL_PERCENT=0
fi

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "ИТОГО:"
echo "  PNG файлов: $PNG_COUNT"
echo "  JPG файлов: $JPG_COUNT"
echo "  Размер до: $(numfmt --to=iec-i --suffix=B $TOTAL_BEFORE 2>/dev/null || echo "$TOTAL_BEFORE байт")"
echo "  Размер после: $(numfmt --to=iec-i --suffix=B $TOTAL_AFTER 2>/dev/null || echo "$TOTAL_AFTER байт")"
echo "  Сэкономлено: $(numfmt --to=iec-i --suffix=B $TOTAL_SAVED 2>/dev/null || echo "$TOTAL_SAVED байт") ($TOTAL_PERCENT%)"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
