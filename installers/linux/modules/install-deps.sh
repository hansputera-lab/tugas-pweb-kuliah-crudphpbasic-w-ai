#!/usr/bin/env bash
# install-deps.sh — Installs all system dependencies for HRIS

set -euo pipefail

RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

install_deps() {
    local WEBSERVER="$1"  # "nginx" or "apache"

    echo -e "${YELLOW}Updating package lists...${NC}"
    case "$PKG_MANAGER" in
        apt)    apt update ;;
        dnf)    dnf check-update || true ;;
        pacman) pacman -Sy ;;
        zypper) zypper refresh ;;
    esac

    echo -e "${YELLOW}Installing system packages...${NC}"
    local WS_PKG=""
    if [ "$WEBSERVER" = "nginx" ]; then
        WS_PKG="$WS_PACKAGE_NGINX"
    else
        WS_PKG="$WS_PACKAGE_APACHE"
    fi

    case "$PKG_MANAGER" in
        apt)
            $PKG_INSTALL $WS_PKG $PHP_PACKAGES $MYSQL_PACKAGE git curl unzip git
            ;;
        dnf)
            $PKG_INSTALL $WS_PKG $PHP_PACKAGES $MYSQL_PACKAGE git curl unzip git
            ;;
        pacman)
            $PKG_INSTALL $WS_PKG $PHP_PACKAGES $MYSQL_PACKAGE git curl unzip
            ;;
        zypper)
            $PKG_INSTALL $WS_PKG $PHP_PACKAGES $MYSQL_PACKAGE git curl unzip git
            ;;
    esac

    echo -e "${GREEN}System packages installed.${NC}"

    install_composer
    install_nodejs

    echo -e "${GREEN}All dependencies installed successfully.${NC}"
}

enable_services() {
    local WEBSERVER="$1"

    echo -e "${YELLOW}Enabling and starting services...${NC}"

    case "$WEBSERVER" in
        nginx)
            systemctl enable nginx 2>/dev/null || true
            systemctl start nginx 2>/dev/null || true
            ;;
        apache)
            case "$DISTRO_NAME" in
                ubuntu|debian)
                    systemctl enable apache2 2>/dev/null || true
                    systemctl start apache2 2>/dev/null || true
                    ;;
                centos|rhel|fedora)
                    systemctl enable httpd 2>/dev/null || true
                    systemctl start httpd 2>/dev/null || true
                    ;;
                arch)
                    systemctl enable httpd 2>/dev/null || true
                    systemctl start httpd 2>/dev/null || true
                    ;;
                opensuse*)
                    systemctl enable apache2 2>/dev/null || true
                    systemctl start apache2 2>/dev/null || true
                    ;;
            esac
            ;;
    esac

    # Enable PHP-FPM
    case "$DISTRO_NAME" in
        ubuntu|debian)
            systemctl enable php8.2-fpm 2>/dev/null || true
            systemctl start php8.2-fpm 2>/dev/null || true
            ;;
        centos|rhel|fedora)
            systemctl enable php-fpm 2>/dev/null || true
            systemctl start php-fpm 2>/dev/null || true
            ;;
        arch)
            systemctl enable php-fpm 2>/dev/null || true
            systemctl start php-fpm 2>/dev/null || true
            ;;
        opensuse*)
            systemctl enable php-fpm 2>/dev/null || true
            systemctl start php-fpm 2>/dev/null || true
            ;;
    esac

    # Enable MySQL/MariaDB
    local MYSQL_SERVICE="mysql"
    case "$DISTRO_NAME" in
        fedora|arch|opensuse*) MYSQL_SERVICE="mariadb" ;;
    esac
    systemctl enable "$MYSQL_SERVICE" 2>/dev/null || true
    systemctl start "$MYSQL_SERVICE" 2>/dev/null || true

    echo -e "${GREEN}Services started.${NC}"
}

# If sourced, just define functions; if executed, run with args
if [[ "${BASH_SOURCE[0]}" == "${0}" ]]; then
    if [ $# -lt 1 ]; then
        echo "Usage: $0 {nginx|apache}"
        exit 1
    fi
    SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
    source "$SCRIPT_DIR/detect-distro.sh"
    detect_distro
    add_php_repo
    install_deps "$1"
    enable_services "$1"
fi
