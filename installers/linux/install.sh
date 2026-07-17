#!/usr/bin/env bash
# HRIS Linux Installer — Multi-distro installation script
#
# Usage:
#   bash install.sh                    # Interactive mode
#   bash install.sh --nginx            # Install with Nginx
#   bash install.sh --apache           # Install with Apache
#   bash install.sh --uninstall        # Remove HRIS
#   bash install.sh --help             # Show help
#
# Environment variables:
#   HRIS_REPO      — Git repository URL (default: https://github.com/your-org/hris.git)
#   HRIS_DIR       — Installation directory (default: /var/www/hris)
#   HRIS_DOMAIN    — Domain name (default: localhost)

set -euo pipefail

RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
CYAN='\033[0;36m'
NC='\033[0m'

VERSION="1.0"
REPO_URL="${HRIS_REPO:-}"
INSTALL_DIR="${HRIS_DIR:-/var/www}"
APP_URL="${HRIS_DOMAIN:-http://localhost}"
WEBSERVER=""
UNINSTALL=false

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
source "$SCRIPT_DIR/modules/detect-distro.sh"
source "$SCRIPT_DIR/modules/install-deps.sh"
source "$SCRIPT_DIR/modules/configure-db.sh"
source "$SCRIPT_DIR/modules/setup-laravel.sh"
source "$SCRIPT_DIR/modules/configure-webserver.sh"

show_help() {
    cat << HELP
HRIS Installer v$VERSION

Usage: bash install.sh [OPTIONS]

Options:
  --nginx            Install with Nginx web server
  --apache           Install with Apache web server
  --uninstall        Remove HRIS and all its configuration
  --help             Show this help message

Environment variables:
  HRIS_REPO          Git repository URL
  HRIS_DIR           Installation directory (default: /var/www)
  HRIS_DOMAIN        Domain name (default: localhost)

Examples:
  bash install.sh --nginx
  HRIS_REPO=https://github.com/me/hris.git bash install.sh --apache
HELP
    exit 0
}

show_banner() {
    cat << BANNER
${CYAN}
╔══════════════════════════════════════════╗
║         HRIS Installer v$VERSION           ║
║  Human Resource Information System        ║
╚══════════════════════════════════════════╝
${NC}
BANNER
}

check_root() {
    if [ "$(id -u)" -ne 0 ] && ! command -v sudo &>/dev/null; then
        echo -e "${RED}This installer requires root privileges. Run with sudo or as root.${NC}"
        exit 1
    fi
}

detect_existing_webserver() {
    if command -v nginx &>/dev/null; then
        echo "nginx"
    elif command -v apache2 &>/dev/null || command -v httpd &>/dev/null; then
        echo "apache"
    else
        echo ""
    fi
}

pick_webserver() {
    local EXISTING
    EXISTING=$(detect_existing_webserver)

    if [ -n "$EXISTING" ]; then
        echo -e "${GREEN}Detected existing web server: $EXISTING${NC}"
        WEBSERVER="$EXISTING"
        return
    fi

    echo ""
    echo "Select web server:"
    echo "  1) Nginx"
    echo "  2) Apache"
    echo ""
    read -p "Choice [1]: " choice
    case "${choice:-1}" in
        1) WEBSERVER="nginx" ;;
        2) WEBSERVER="apache" ;;
        *) WEBSERVER="nginx" ;;
    esac
}

get_repo_url() {
    if [ -n "$REPO_URL" ]; then
        return
    fi
    echo ""
    echo "Enter the HRIS Git repository URL:"
    echo "(Press Enter to use the current directory)"
    read -p "URL: " input
    if [ -n "$input" ]; then
        REPO_URL="$input"
    fi
}

run_uninstall() {
    echo -e "${YELLOW}Uninstalling HRIS...${NC}"

    local HRIS_APP_DIR="$INSTALL_DIR/hris"

    # Remove web server config
    if [ -f /etc/nginx/sites-enabled/hris ]; then
        sudo rm -f /etc/nginx/sites-enabled/hris
        sudo systemctl reload nginx 2>/dev/null || true
    fi
    if [ -f /etc/apache2/sites-enabled/hris.conf ]; then
        sudo a2dissite hris.conf 2>/dev/null || true
        sudo systemctl reload apache2 2>/dev/null || true
    fi

    # Drop database
    if command -v mysql &>/dev/null; then
        echo -e "${YELLOW}Drop database 'hris'? [y/N]${NC}"
        read -p "> " confirm
        if [ "$confirm" = "y" ] || [ "$confirm" = "Y" ]; then
            sudo mysql -e "DROP DATABASE IF EXISTS hris" 2>/dev/null || true
            sudo mysql -e "DROP USER IF EXISTS 'hris'@'localhost'" 2>/dev/null || true
            echo -e "${GREEN}Database dropped.${NC}"
        fi
    fi

    # Remove application files
    if [ -d "$HRIS_APP_DIR" ]; then
        echo -e "${YELLOW}Remove application directory $HRIS_APP_DIR? [y/N]${NC}"
        read -p "> " confirm
        if [ "$confirm" = "y" ] || [ "$confirm" = "Y" ]; then
            sudo rm -rf "$HRIS_APP_DIR"
            echo -e "${GREEN}Application directory removed.${NC}"
        fi
    fi

    echo -e "${GREEN}Uninstall complete.${NC}"
    exit 0
}

parse_args() {
    while [ $# -gt 0 ]; do
        case "$1" in
            --nginx)    WEBSERVER="nginx" ;;
            --apache)   WEBSERVER="apache" ;;
            --uninstall) UNINSTALL=true ;;
            --help)     show_help ;;
            *)          echo -e "${RED}Unknown option: $1${NC}"; show_help ;;
        esac
        shift
    done
}

main() {
    show_banner
    check_root
    detect_distro

    if [ "$UNINSTALL" = true ]; then
        run_uninstall
    fi

    if [ -z "$WEBSERVER" ]; then
        pick_webserver
    fi
    get_repo_url

    echo ""
    echo -e "${CYAN}Installation Summary:${NC}"
    echo -e "  Distribution:  ${GREEN}$DISTRO_NAME $DISTRO_VERSION${NC}"
    echo -e "  Web Server:    ${GREEN}$WEBSERVER${NC}"
    echo -e "  Install Dir:   ${GREEN}$INSTALL_DIR/hris${NC}"
    echo -e "  App URL:       ${GREEN}$APP_URL${NC}"
    echo ""
    echo -e "${YELLOW}Proceed with installation? [Y/n]${NC}"
    read -p "> " confirm
    if [ "$confirm" = "n" ] || [ "$confirm" = "N" ]; then
        echo "Installation cancelled."
        exit 0
    fi

    # Step 1: Add repos and install dependencies
    echo ""
    echo -e "${CYAN}[1/5] Installing system dependencies...${NC}"
    add_php_repo
    install_deps "$WEBSERVER"
    enable_services "$WEBSERVER"

    # Step 2: Setup Laravel application
    echo ""
    echo -e "${CYAN}[2/5] Setting up HRIS application...${NC}"
    setup_laravel "$INSTALL_DIR" "$REPO_URL" "$APP_URL"

    # Step 3: Configure database
    echo ""
    echo -e "${CYAN}[3/5] Configuring database...${NC}"
    configure_db "$INSTALL_DIR/hris"

    # Step 4: Configure web server
    echo ""
    echo -e "${CYAN}[4/5] Configuring web server...${NC}"
    configure_webserver "$WEBSERVER" "$INSTALL_DIR/hris" "${HRIS_DOMAIN:-localhost}"

    # Step 5: Final setup
    echo ""
    echo -e "${CYAN}[5/5] Finalizing...${NC}"
    cd "$INSTALL_DIR/hris"

    # Clear caches for production
    php artisan config:cache --force 2>/dev/null || true
    php artisan route:cache --force 2>/dev/null || true
    php artisan view:cache --force 2>/dev/null || true

    echo ""
    echo -e "${GREEN}╔══════════════════════════════════════════╗${NC}"
    echo -e "${GREEN}║        Installation Complete!            ║${NC}"
    echo -e "${GREEN}╚══════════════════════════════════════════╝${NC}"
    echo ""
    echo -e "  HRIS is now available at: ${CYAN}$APP_URL${NC}"
    echo ""
    echo -e "  Default login:"
    echo -e "    Email:    ${YELLOW}admin@hris.test${NC}"
    echo -e "    Password: ${YELLOW}password${NC}"
    echo ""
    echo -e "  Credentials file: ${YELLOW}$INSTALL_DIR/hris/database-credentials.txt${NC}"
    echo ""
}

parse_args "$@"
main
