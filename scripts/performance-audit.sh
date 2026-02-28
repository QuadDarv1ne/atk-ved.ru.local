#!/bin/bash

###############################################################################
# Performance Audit Script
# Запускает различные проверки производительности
###############################################################################

set -e

# Цвета
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
BLUE='\033[0;34m'
NC='\033[0m'

# URL для тестирования
URL="${1:-http://localhost:8080}"

echo -e "${BLUE}🚀 Performance Audit for: $URL${NC}"
echo "========================================"

# 1. Lighthouse audit
echo ""
echo -e "${YELLOW}1. Running Lighthouse audit...${NC}"
if command -v lighthouse &> /dev/null; then
    lighthouse "$URL" \
        --output=html \
        --output=json \
        --output-path=./reports/lighthouse \
        --chrome-flags="--headless" \
        --quiet
    
    echo -e "${GREEN}✓ Lighthouse report saved to: reports/lighthouse.html${NC}"
else
    echo -e "${RED}✗ Lighthouse not installed. Install: npm install -g lighthouse${NC}"
fi

# 2. Web Vitals check
echo ""
echo -e "${YELLOW}2. Checking Web Vitals...${NC}"
if command -v node &> /dev/null; then
    node -e "
    const https = require('https');
    const url = '$URL';
    
    console.log('Measuring page load time...');
    const start = Date.now();
    
    https.get(url, (res) => {
        let data = '';
        res.on('data', chunk => data += chunk);
        res.on('end', () => {
            const loadTime = Date.now() - start;
            console.log('Load time: ' + loadTime + 'ms');
            
            if (loadTime < 1000) {
                console.log('✓ Excellent (<1s)');
            } else if (loadTime < 2500) {
                console.log('✓ Good (<2.5s)');
            } else {
                console.log('⚠ Needs improvement (>2.5s)');
            }
        });
    }).on('error', (err) => {
        console.error('Error:', err.message);
    });
    "
fi

# 3. Image optimization check
echo ""
echo -e "${YELLOW}3. Checking image optimization...${NC}"

IMAGE_DIRS=(
    "wp-content/themes/atk-ved/images"
    "wp-content/themes/atk-ved/assets/images"
)

large_images=0
total_images=0

for dir in "${IMAGE_DIRS[@]}"; do
    if [ -d "$dir" ]; then
        while IFS= read -r -d '' file; do
            size=$(stat -f%z "$file" 2>/dev/null || stat -c%s "$file")
            ((total_images++))
            
            # Проверка размера (> 500KB)
            if [ $size -gt 512000 ]; then
                ((large_images++))
                echo -e "${RED}  ⚠ Large image: $(basename "$file") ($(($size / 1024))KB)${NC}"
            fi
        done < <(find "$dir" -type f \( -iname "*.jpg" -o -iname "*.jpeg" -o -iname "*.png" \) -print0 2>/dev/null)
    fi
done

if [ $large_images -eq 0 ]; then
    echo -e "${GREEN}✓ All images are optimized (<500KB)${NC}"
else
    echo -e "${YELLOW}⚠ Found $large_images large images (>500KB)${NC}"
    echo "  Run: ./scripts/optimize-images.sh"
fi

# 4. CSS/JS minification check
echo ""
echo -e "${YELLOW}4. Checking CSS/JS minification...${NC}"

CSS_DIR="wp-content/themes/atk-ved/css"
JS_DIR="wp-content/themes/atk-ved/js"

unminified_css=0
unminified_js=0

if [ -d "$CSS_DIR" ]; then
    while IFS= read -r -d '' file; do
        if [[ ! "$file" =~ \.min\.css$ ]]; then
            minified="${file%.css}.min.css"
            if [ ! -f "$minified" ]; then
                ((unminified_css++))
                echo -e "${YELLOW}  ⚠ No minified version: $(basename "$file")${NC}"
            fi
        fi
    done < <(find "$CSS_DIR" -type f -name "*.css" -print0 2>/dev/null)
fi

if [ -d "$JS_DIR" ]; then
    while IFS= read -r -d '' file; do
        if [[ ! "$file" =~ \.min\.js$ ]]; then
            minified="${file%.js}.min.js"
            if [ ! -f "$minified" ]; then
                ((unminified_js++))
                echo -e "${YELLOW}  ⚠ No minified version: $(basename "$file")${NC}"
            fi
        fi
    done < <(find "$JS_DIR" -type f -name "*.js" -print0 2>/dev/null)
fi

if [ $unminified_css -eq 0 ] && [ $unminified_js -eq 0 ]; then
    echo -e "${GREEN}✓ All CSS/JS files are minified${NC}"
else
    echo -e "${YELLOW}⚠ Found $unminified_css CSS and $unminified_js JS files without minified versions${NC}"
    echo "  Run: npm run build"
fi

# 5. Database optimization check
echo ""
echo -e "${YELLOW}5. Database optimization recommendations...${NC}"
echo "  • Clean up post revisions (keep last 5)"
echo "  • Remove spam comments"
echo "  • Optimize database tables"
echo "  • Remove transients older than 30 days"
echo ""
echo "  Run WP-CLI commands:"
echo "    wp post delete \$(wp post list --post_type='revision' --format=ids)"
echo "    wp transient delete --all"
echo "    wp db optimize"

# 6. Caching check
echo ""
echo -e "${YELLOW}6. Checking caching configuration...${NC}"

if [ -f ".htaccess" ]; then
    if grep -q "mod_expires" .htaccess; then
        echo -e "${GREEN}✓ Browser caching configured${NC}"
    else
        echo -e "${YELLOW}⚠ Browser caching not configured${NC}"
    fi
    
    if grep -q "mod_deflate\|mod_gzip" .htaccess; then
        echo -e "${GREEN}✓ GZIP compression configured${NC}"
    else
        echo -e "${YELLOW}⚠ GZIP compression not configured${NC}"
    fi
else
    echo -e "${YELLOW}⚠ .htaccess file not found${NC}"
fi

# 7. Security headers check
echo ""
echo -e "${YELLOW}7. Checking security headers...${NC}"

if command -v curl &> /dev/null; then
    headers=$(curl -s -I "$URL" 2>/dev/null || echo "")
    
    check_header() {
        local header=$1
        if echo "$headers" | grep -qi "$header"; then
            echo -e "${GREEN}✓ $header present${NC}"
        else
            echo -e "${RED}✗ $header missing${NC}"
        fi
    }
    
    check_header "X-Content-Type-Options"
    check_header "X-Frame-Options"
    check_header "X-XSS-Protection"
    check_header "Strict-Transport-Security"
else
    echo -e "${YELLOW}⚠ curl not available${NC}"
fi

# Summary
echo ""
echo "========================================"
echo -e "${BLUE}📊 Audit Summary${NC}"
echo "========================================"
echo "Total images checked: $total_images"
echo "Large images found: $large_images"
echo "Unminified CSS: $unminified_css"
echo "Unminified JS: $unminified_js"
echo ""
echo -e "${GREEN}✓ Audit complete!${NC}"
echo "Check reports/lighthouse.html for detailed results"
