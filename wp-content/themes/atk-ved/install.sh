#!/bin/bash

# Installation script for ATK VED theme
# Automates setup process

set -e

echo "🚀 Installing ATK VED Theme..."

# Colors
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m'

# Check if running in theme directory
if [ ! -f "functions.php" ]; then
    echo -e "${RED}❌ Please run this script from the theme directory${NC}"
    exit 1
fi

# Check PHP version
if ! command -v php &> /dev/null; then
    echo -e "${RED}❌ PHP is not installed${NC}"
    exit 1
fi

PHP_VERSION=$(php -v | head -n 1 | cut -d " " -f 2 | cut -d "." -f 1,2)
if (( $(echo "$PHP_VERSION < 8.1" | bc -l) )); then
    echo -e "${RED}❌ PHP 8.1 or higher is required (current: $PHP_VERSION)${NC}"
    exit 1
fi

echo -e "${GREEN}✅ PHP version: $PHP_VERSION${NC}"

# Check Composer
if ! command -v composer &> /dev/null; then
    echo -e "${YELLOW}⚠️  Composer not found. Installing...${NC}"
    curl -sS https://getcomposer.org/installer | php
    mv composer.phar /usr/local/bin/composer
fi

echo -e "${GREEN}✅ Composer found${NC}"

# Install PHP dependencies
echo -e "${YELLOW}📦 Installing PHP dependencies...${NC}"
composer install --no-interaction --prefer-dist

# Check Node.js
if ! command -v node &> /dev/null; then
    echo -e "${YELLOW}⚠️  Node.js not found${NC}"
else
    NODE_VERSION=$(node -v)
    echo -e "${GREEN}✅ Node.js version: $NODE_VERSION${NC}"
    
    # Install NPM dependencies
    echo -e "${YELLOW}📦 Installing NPM dependencies...${NC}"
    npm install
fi

# Check if Docker is available
if command -v docker &> /dev/null && command -v docker-compose &> /dev/null; then
    echo -e "${GREEN}✅ Docker found${NC}"
    
    read -p "Do you want to start Docker containers? (y/n) " -n 1 -r
    echo
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        echo -e "${YELLOW}🐳 Starting Docker containers...${NC}"
        docker-compose up -d
        
        echo -e "${YELLOW}⏳ Waiting for MySQL...${NC}"
        sleep 10
        
        echo -e "${YELLOW}📝 Installing WordPress...${NC}"
        docker-compose exec wordpress wp core download --locale=ru_RU --force
        docker-compose exec wordpress wp config create \
            --dbname=wordpress \
            --dbuser=wordpress \
            --dbpass=wordpress \
            --dbhost=mysql \
            --force
        docker-compose exec wordpress wp core install \
            --url=localhost:8080 \
            --title="АТК ВЭД" \
            --admin_user=admin \
            --admin_password=admin \
            --admin_email=admin@atk-ved.ru \
            --skip-email
        docker-compose exec wordpress wp theme activate atk-ved
        
        echo -e "${GREEN}✅ WordPress installed${NC}"
    fi
fi

# Create .env file
if [ ! -f ".env" ]; then
    echo -e "${YELLOW}📝 Creating .env file...${NC}"
    cp .env.example .env 2>/dev/null || echo "No .env.example found"
fi

# Set up Git hooks
if [ -d ".git" ]; then
    echo -e "${YELLOW}🔧 Setting up Git hooks...${NC}"
    cp .git/hooks/pre-commit .git/hooks/pre-commit.backup 2>/dev/null || true
    cp scripts/pre-commit .git/hooks/pre-commit 2>/dev/null || true
    chmod +x .git/hooks/pre-commit
fi

# Final checks
echo ""
echo -e "${GREEN}========================================${NC}"
echo -e "${GREEN}✅ Installation complete!${NC}"
echo -e "${GREEN}========================================${NC}"
echo ""
echo -e "${YELLOW}Next steps:${NC}"
echo "1. Start development server: npm run dev"
echo "2. Open site: http://localhost:8080"
echo "3. Run tests: composer test"
echo ""
echo -e "${YELLOW}Documentation:${NC}"
echo "- Developer Guide: DEVELOPER_GUIDE.md"
echo "- Docker Setup: DOCKER.md"
echo "- CI/CD Setup: CI_CD_SETUP.md"
echo ""
