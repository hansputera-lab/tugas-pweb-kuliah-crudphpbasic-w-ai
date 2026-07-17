#!/usr/bin/env bash
# detect-distro.sh — Identifies the Linux distribution and sets package manager variables
#
# Output variables:
#   DISTRO_NAME      — e.g. "ubuntu", "centos", "fedora", "arch", "opensuse"
#   DISTRO_VERSION   — e.g. "22.04", "8", "38"
#   PKG_MANAGER      — e.g. "apt", "dnf", "yum", "pacman", "zypper"
#   PKG_UPDATE       — update command (e.g. "apt update")
#   PKG_INSTALL      — install command (e.g. "apt install -y")
#   WEB_USER         — web server user (e.g. "www-data", "nginx", "http", "wwwrun")
#   PHP_VERSION      — available PHP version from repos
#   PHP_PACKAGES     — list of PHP extension packages to install
#   MYSQL_PACKAGE    — MySQL/MariaDB package name
#   WS_PACKAGE       — web server package (nginx or apache2/httpd)
#   ConfDir          — web server config directory

set -euo pipefail

RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

detect_distro() {
    if [ -f /etc/os-release ]; then
        . /etc/os-release
        DISTRO_NAME=$(echo "$ID" | tr '[:upper:]' '[:lower:]')
        DISTRO_VERSION="$VERSION_ID"
    elif command -v lsb_release &>/dev/null; then
        DISTRO_NAME=$(lsb_release -is | tr '[:upper:]' '[:lower:]')
        DISTRO_VERSION=$(lsb_release -rs)
    elif [ -f /etc/centos-release ]; then
        DISTRO_NAME="centos"
        DISTRO_VERSION=$(rpm -q centos-release | grep -oP '\d+')
    else
        echo -e "${RED}Unable to detect distribution.${NC}"
        exit 1
    fi

    case "$DISTRO_NAME" in
        ubuntu|debian|linuxmint|pop|elementary|kali)
            PKG_MANAGER="apt"
            PKG_UPDATE="apt update"
            PKG_INSTALL="apt install -y"
            WEB_USER="www-data"
            PHP_VERSION="8.2"
            PHP_PACKAGES="php8.2 php8.2-fpm php8.2-mysql php8.2-mbstring php8.2-xml php8.2-curl php8.2-bcmath php8.2-gd php8.2-zip php8.2-intl php8.2-cli php8.2-common"
            MYSQL_PACKAGE="mysql-server"
            WS_PACKAGE_NGINX="nginx"
            WS_PACKAGE_APACHE="apache2"
            ConfDir="/etc/nginx"
            ConfDirApache="/etc/apache2"
            REPO_NEEDED=true
            ;;
        centos|rhel|almalinux|rocky)
            PKG_MANAGER="dnf"
            PKG_UPDATE="dnf check-update || true"
            PKG_INSTALL="dnf install -y"
            WEB_USER="apache"
            PHP_VERSION="8.2"
            PHP_PACKAGES="php php-fpm php-mysqlnd php-mbstring php-xml php-curl php-bcmath php-gd php-zip php-intl php-cli php-common"
            MYSQL_PACKAGE="mysql-server"
            WS_PACKAGE_NGINX="nginx"
            WS_PACKAGE_APACHE="httpd"
            ConfDir="/etc/nginx"
            ConfDirApache="/etc/httpd"
            REPO_NEEDED=true
            ;;
        fedora)
            PKG_MANAGER="dnf"
            PKG_UPDATE="dnf check-update || true"
            PKG_INSTALL="dnf install -y"
            WEB_USER="apache"
            PHP_VERSION="8.2"
            PHP_PACKAGES="php php-fpm php-mysqlnd php-mbstring php-xml php-curl php-bcmath php-gd php-zip php-intl php-cli php-common"
            MYSQL_PACKAGE="mariadb-server"
            WS_PACKAGE_NGINX="nginx"
            WS_PACKAGE_APACHE="httpd"
            ConfDir="/etc/nginx"
            ConfDirApache="/etc/httpd"
            REPO_NEEDED=false
            ;;
        arch|manjaro|endeavouros)
            PKG_MANAGER="pacman"
            PKG_UPDATE="pacman -Sy"
            PKG_INSTALL="pacman -S --noconfirm"
            WEB_USER="http"
            PHP_VERSION="8.2"
            PHP_PACKAGES="php php-fpm php-mysql php-mbstring php-xml php-curl php-bcmath php-gd php-zip php-intl php-cli php-common"
            MYSQL_PACKAGE="mariadb"
            WS_PACKAGE_NGINX="nginx"
            WS_PACKAGE_APACHE="apache"
            ConfDir="/etc/nginx"
            ConfDirApache="/etc/httpd"
            REPO_NEEDED=false
            ;;
        opensuse*|suse)
            PKG_MANAGER="zypper"
            PKG_UPDATE="zypper refresh"
            PKG_INSTALL="zypper install -y"
            WEB_USER="wwwrun"
            PHP_VERSION="8.2"
            PHP_PACKAGES="php8 php8-fpm php8-mysql php8-mbstring php8-xml php8-curl php8-bcmath php8-gd php8-zip php8-intl php8-cli php8-common"
            MYSQL_PACKAGE="mariadb"
            WS_PACKAGE_NGINX="nginx"
            WS_PACKAGE_APACHE="apache2"
            ConfDir="/etc/nginx"
            ConfDirApache="/etc/apache2"
            REPO_NEEDED=false
            ;;
        *)
            echo -e "${RED}Unsupported distribution: $DISTRO_NAME${NC}"
            echo -e "${YELLOW}Supported: Ubuntu, Debian, CentOS, RHEL, Fedora, Arch, openSUSE${NC}"
            exit 1
            ;;
    esac

    echo -e "${GREEN}Detected: $DISTRO_NAME $DISTRO_VERSION (pkg: $PKG_MANAGER)${NC}"
}

add_php_repo() {
    if [ "$REPO_NEEDED" = false ]; then
        return 0
    fi

    case "$DISTRO_NAME" in
        ubuntu|debian|linuxmint|pop|elementary|kali)
            if ! command -v add-apt-repository &>/dev/null; then
                $PKG_INSTALL software-properties-common
            fi
            if ! apt policy 2>/dev/null | grep -q "ondrej/php"; then
                echo -e "${YELLOW}Adding ondrej/php PPA for PHP $PHP_VERSION...${NC}"
                add-apt-repository -y ppa:ondrej/php
                $PKG_UPDATE
            fi
            ;;
        centos|rhel|almalinux|rocky)
            if ! rpm -q epel-release &>/dev/null; then
                echo -e "${YELLOW}Installing EPEL and REMI repos...${NC}"
                $PKG_INSTALL epel-release
                $PKG_INSTALL https://rpms.remirepo.net/enterprise/remi-release-$DISTRO_VERSION.rpm
                $PKG_INSTALL dnf-utils
                dnf module reset php -y
                dnf module enable php:remi-8.2 -y
            fi
            ;;
    esac
}

install_composer() {
    if command -v composer &>/dev/null; then
        return 0
    fi
    echo -e "${YELLOW}Installing Composer...${NC}"
    EXPECTED_CHECKSUM="$(php -r 'copy("https://composer.github.io/installer.sig", "php://stdout");')"
    php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
    ACTUAL_CHECKSUM="$(php -r "echo hash_file('sha384', 'composer-setup.php');")"
    if [ "$EXPECTED_CHECKSUM" != "$ACTUAL_CHECKSUM" ]; then
        echo -e "${RED}Composer installer corrupt.${NC}"
        rm composer-setup.php
        return 1
    fi
    php composer-setup.php --quiet
    rm composer-setup.php
    mv composer.phar /usr/local/bin/composer
    echo -e "${GREEN}Composer installed.${NC}"
}

install_nodejs() {
    if command -v node &>/dev/null; then
        local NODE_VER=$(node -v | sed 's/v//' | cut -d. -f1)
        if [ "$NODE_VER" -ge 18 ]; then
            return 0
        fi
    fi
    echo -e "${YELLOW}Installing Node.js 22.x...${NC}"
    case "$DISTRO_NAME" in
        ubuntu|debian|linuxmint|pop)
            curl -fsSL https://deb.nodesource.com/setup_22.x | bash -
            $PKG_INSTALL nodejs
            ;;
        centos|rhel|fedora|almalinux|rocky)
            curl -fsSL https://rpm.nodesource.com/setup_22.x | bash -
            $PKG_INSTALL nodejs
            ;;
        arch|manjaro)
            pacman -S --noconfirm nodejs npm
            ;;
        opensuse*)
            zypper install -y nodejs22 npm
            ;;
    esac
    echo -e "${GREEN}Node.js $(node -v) installed.${NC}"
}

# If sourced, just define functions; if executed, run detection
if [[ "${BASH_SOURCE[0]}" == "${0}" ]]; then
    detect_distro
fi
