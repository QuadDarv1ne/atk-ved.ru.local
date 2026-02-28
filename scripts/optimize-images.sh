#!/bin/bash

###############################################################################
# Image Optimization Script
# Оптимизирует все изображения в проекте
###############################################################################

set -e

echo "🖼️  Starting image optimization..."

# Цвета для вывода
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Директории с изображениями
IMAGE_DIRS=(
    "wp-content/themes/atk-ved/images"
    "wp-content/themes/atk-ved/assets/images"
    "wp-content/uploads"
)

# Проверка наличия необходимых инструментов
check_dependencies() {
    local missing_deps=()
    
    if ! command -v jpegoptim &> /dev/null; then
        missing_deps+=("jpegoptim")
    fi
    
    if ! command -v optipng &> /dev/null; then
        missing_deps+=("optipng")
    fi
    
    if ! command -v cwebp &> /dev/null; then
        missing_deps+=("webp")
    fi
    
    if [ ${#missing_deps[@]} -ne 0 ]; then
        echo -e "${YELLOW}Missing dependencies: ${missing_deps[*]}${NC}"
        echo "Install them with:"
        echo "  Ubuntu/Debian: sudo apt-get install jpegoptim optipng webp"
        echo "  macOS: brew install jpegoptim optipng webp"
        exit 1
    fi
}

# Оптимизация JPEG
optimize_jpeg() {
    local file="$1"
    local original_size=$(stat -f%z "$file" 2>/dev/null || stat -c%s "$file")
    
    jpegoptim --max=85 --strip-all --preserve --quiet "$file"
    
    local new_size=$(stat -f%z "$file" 2>/dev/null || stat -c%s "$file")
    local saved=$((original_size - new_size))
    local percent=$((saved * 100 / original_size))
    
    echo -e "${GREEN}✓${NC} $(basename "$file"): saved ${saved} bytes (${percent}%)"
}

# Оптимизация PNG
optimize_png() {
    local file="$1"
    local original_size=$(stat -f%z "$file" 2>/dev/null || stat -c%s "$file")
    
    optipng -quiet -o2 "$file"
    
    local new_size=$(stat -f%z "$file" 2>/dev/null || stat -c%s "$file")
    local saved=$((original_size - new_size))
    local percent=$((saved * 100 / original_size))
    
    echo -e "${GREEN}✓${NC} $(basename "$file"): saved ${saved} bytes (${percent}%)"
}

# Конвертация в WebP
convert_to_webp() {
    local file="$1"
    local webp_file="${file%.*}.webp"
    
    if [ ! -f "$webp_file" ]; then
        cwebp -q 85 "$file" -o "$webp_file" -quiet
        echo -e "${GREEN}✓${NC} Created WebP: $(basename "$webp_file")"
    fi
}

# Проверка зависимостей
check_dependencies

# Счетчики
total_jpeg=0
total_png=0
total_webp=0

# Обработка каждой директории
for dir in "${IMAGE_DIRS[@]}"; do
    if [ ! -d "$dir" ]; then
        echo -e "${YELLOW}⚠${NC}  Directory not found: $dir"
        continue
    fi
    
    echo ""
    echo "Processing directory: $dir"
    echo "----------------------------------------"
    
    # Оптимизация JPEG
    while IFS= read -r -d '' file; do
        optimize_jpeg "$file"
        convert_to_webp "$file"
        ((total_jpeg++))
    done < <(find "$dir" -type f \( -iname "*.jpg" -o -iname "*.jpeg" \) -print0)
    
    # Оптимизация PNG
    while IFS= read -r -d '' file; do
        optimize_png "$file"
        convert_to_webp "$file"
        ((total_png++))
    done < <(find "$dir" -type f -iname "*.png" -print0)
done

echo ""
echo "========================================="
echo -e "${GREEN}✓ Optimization complete!${NC}"
echo "  JPEG files optimized: $total_jpeg"
echo "  PNG files optimized: $total_png"
echo "  WebP files created: $((total_jpeg + total_png))"
echo "========================================="
