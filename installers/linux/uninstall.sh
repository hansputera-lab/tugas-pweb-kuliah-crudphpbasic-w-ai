#!/usr/bin/env bash
# HRIS Uninstaller — Removes HRIS application, database, and web server config
#
# Usage:
#   bash uninstall.sh                    # Interactive uninstall
#   bash uninstall.sh --force            # Non-interactive, remove everything
#   bash uninstall.sh --dry-run          # Show what would be removed

set -euo pipefail

RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

INSTALL_DIR="${HRIS_DIR:-/var/www}"
FORCE=false
DRY_RUN=false

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

parse_args() {
    while [ $# -gt 0 ]; do
        case "$1" in
            --force)    FORCE=true ;;
            --dry-run)  DRY_RUN=true ;;
            --help)     echo "Usage: bash uninstall.sh [--force] [--dry-run]"; exit 0 ;;
            *)          echo "Unknown option: $1"; exit 1 ;;
        esac
        shift
    done
}

confirm() {
    if [ "$FORCE" = true ]; then
        return 0
    fi
    local msg="$1"
    echo -e "${YELLOW}$msg [y/N]${NC}"
    read -p "> " response
    case "$response" in
        y|Y) return 0 ;;
        *) return 1 ;;
    esac
}

run() {
    if [ "$DRY_RUN" = true ]; then
        echo -e "  ${YELLOW}[DRY RUN] Would run: $*${NC}"
    else
        echo -e "  Running: $*"
        eval "$@"
    fi
}

main() {
    parse_args "$@"

    echo ""
    echo -e "${YELLOW}HRIS Uninstaller${NC}"
    echo ""

    if ! confirm "Are you sure you want to uninstall HRIS?"; then
        echo "Uninstall cancelled."
        exit 0
    fi

    # 1. Remove Nginx config
    if [ -f /etc/nginx/sites-enabled/hris ]; then
        echo -e "  ${YELLOW}Removing Nginx config...${NC}"
        run sudo rm -f /etc/nginx/sites-available/hris
        run sudo rm -f /etc/nginx/sites-enabled/hris
        run sudo systemctl reload nginx
    fi

    # 2. Remove Apache config
    if [ -f /etc/apache2/sites-enabled/hris.conf ]; then
        echo -e "  ${YELLOW}Removing Apache config...${NC}"
        run sudo a2dissite hris.conf
        run sudo rm -f /etc/apache2/sites-available/hris.conf
        run sudo systemctl reload apache2
    fi
    if [ -f /etc/httpd/conf.d/hris.conf ]; then
        echo -e "  ${YELLOW}Removing Apache config...${NC}"
        run sudo rm -f /etc/httpd/conf.d/hris.conf
        run sudo systemctl reload httpd
    fi

    # 3. Drop database
    if command -v mysql &>/dev/null; then
        echo -e "  ${YELLOW}Dropping HRIS database...${NC}"
        run sudo mysql -e "DROP DATABASE IF EXISTS hris" 2>/dev/null || true
        run sudo mysql -e "DROP USER IF EXISTS 'hris'@'localhost'" 2>/dev/null || true
    fi

    # 4. Remove application files
    if [ -d "$INSTALL_DIR/hris" ]; then
        echo -e "  ${YELLOW}Removing application files...${NC}"
        run sudo rm -rf "$INSTALL_DIR/hris"
    fi

    # 5. Remove composer packages (optional)
    echo ""
    if confirm "Remove Composer and Node.js globally? (installed by install.sh)"; then
        if [ -f /usr/local/bin/composer ]; then
            run sudo rm -f /usr/local/bin/composer
        fi
    fi

    echo ""
    if [ "$DRY_RUN" = true ]; then
        echo -e "${YELLOW}Dry run complete. Nothing was removed.${NC}"
    else
        echo -e "${GREEN}HRIS has been uninstalled.${NC}"
    fi
}

main "$@"
