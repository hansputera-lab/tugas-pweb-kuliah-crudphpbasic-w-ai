#!/usr/bin/env bash
# setup-laravel.sh — Sets up the HRIS Laravel application

set -euo pipefail

RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

setup_laravel() {
    local INSTALL_DIR="$1"
    local REPO_URL="$2"
    local APP_URL="${3:-http://localhost}"

    mkdir -p "$INSTALL_DIR"

    if [ -d "$INSTALL_DIR/hris" ] && [ -f "$INSTALL_DIR/hris/artisan" ]; then
        echo -e "${YELLOW}HRIS application already exists at $INSTALL_DIR/hris${NC}"
        echo -e "${YELLOW}Updating existing installation...${NC}"
        cd "$INSTALL_DIR/hris"
        git pull origin main 2>/dev/null || true
    else
        echo -e "${YELLOW}Cloning HRIS repository...${NC}"
        if [ -n "$REPO_URL" ]; then
            git clone "$REPO_URL" "$INSTALL_DIR/hris"
        else
            echo -e "${RED}No repository URL provided.${NC}"
            exit 1
        fi
        cd "$INSTALL_DIR/hris"
    fi

    echo -e "${YELLOW}Installing PHP dependencies...${NC}"
    composer install --no-dev --optimize-autoloader --no-interaction

    echo -e "${YELLOW}Installing Node dependencies and building assets...${NC}"
    if [ -f package.json ]; then
        npm install --no-audit --no-fund 2>/dev/null || true
        npm run build 2>/dev/null || true
    fi

    echo -e "${YELLOW}Configuring environment...${NC}"
    if [ ! -f .env ]; then
        cp .env.example .env
    fi

    # Update APP_URL
    sed -i "s|APP_URL=.*|APP_URL=$APP_URL|" .env
    sed -i "s|APP_ENV=.*|APP_ENV=production|" .env
    sed -i "s|APP_DEBUG=.*|APP_DEBUG=false|" .env

    # Generate app key
    php artisan key:generate --force
    echo -e "${GREEN}App key generated.${NC}"

    echo -e "${YELLOW}Running database migrations...${NC}"
    php artisan migrate --seed --force
    echo -e "${GREEN}Database migrated and seeded.${NC}"

    echo -e "${YELLOW}Creating storage link...${NC}"
    php artisan storage:link --force

    echo -e "${YELLOW}Setting permissions...${NC}"
    chown -R "$WEB_USER":"$WEB_USER" storage bootstrap/cache public/uploads 2>/dev/null || true
    chmod -R 775 storage bootstrap/cache public/uploads 2>/dev/null || true

    echo -e "${GREEN}HRIS application setup complete.${NC}"
}

# If sourced, just define functions; if executed, run with args
if [[ "${BASH_SOURCE[0]}" == "${0}" ]]; then
    if [ $# -lt 1 ]; then
        echo "Usage: $0 <install-dir> [repo-url] [app-url]"
        exit 1
    fi
    SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
    source "$SCRIPT_DIR/detect-distro.sh"
    detect_distro
    setup_laravel "$@"
fi
