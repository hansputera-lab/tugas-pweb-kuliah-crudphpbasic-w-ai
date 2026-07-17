#!/usr/bin/env bash
# configure-webserver.sh — Configures Nginx or Apache virtual host for HRIS

set -euo pipefail

RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

configure_nginx() {
    local HRIS_DIR="$1"
    local DOMAIN="${2:-localhost}"
    local NGINX_CONF="$ConfDir/sites-available/hris"

    echo -e "${YELLOW}Configuring Nginx for $DOMAIN...${NC}"

    sudo tee "$NGINX_CONF" > /dev/null <<NGINX
server {
    listen 80;
    server_name $DOMAIN;
    root $HRIS_DIR/public;
    index index.php;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location ~ \.php\$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php${PHP_VERSION}-fpm.sock;
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(ht|git|env) {
        deny all;
    }

    location ~ ^/(app|bootstrap|config|database|resources|routes|storage|tests|vendor)/ {
        deny all;
    }

    location ~* \.(jpg|jpeg|png|gif|ico|css|js|woff|woff2|ttf|svg|eot)\$ {
        expires 30d;
        add_header Cache-Control "public, immutable";
    }

    location ~* /uploads/.*\.php\$ {
        deny all;
    }

    access_log /var/log/nginx/hris_access.log;
    error_log /var/log/nginx/hris_error.log;
}
NGINX

    # Enable site
    if [ -d "$ConfDir/sites-enabled" ]; then
        sudo ln -sf "$NGINX_CONF" "$ConfDir/sites-enabled/hris"
    fi

    # Remove default site if it conflicts
    if [ -f "$ConfDir/sites-enabled/default" ]; then
        sudo rm -f "$ConfDir/sites-enabled/default"
    fi

    echo -e "${GREEN}Nginx configured.${NC}"
}

configure_apache() {
    local HRIS_DIR="$1"
    local DOMAIN="${2:-localhost}"
    local APACHE_CONF=""

    case "$DISTRO_NAME" in
        ubuntu|debian|linuxmint|pop)
            APACHE_CONF="$ConfDirApache/sites-available/hris.conf"
            sudo a2enmod rewrite 2>/dev/null || true
            ;;
        centos|rhel|fedora|almalinux|rocky)
            APACHE_CONF="$ConfDirApache/conf.d/hris.conf"
            ;;
        arch)
            APACHE_CONF="$ConfDirApache/conf/extra/hris.conf"
            ;;
        opensuse*)
            APACHE_CONF="$ConfDirApache/conf.d/hris.conf"
            ;;
        *)
            APACHE_CONF="$ConfDirApache/conf.d/hris.conf"
            ;;
    esac

    echo -e "${YELLOW}Configuring Apache for $DOMAIN...${NC}"

    sudo tee "$APACHE_CONF" > /dev/null <<APACHE
<VirtualHost *:80>
    ServerName $DOMAIN
    DocumentRoot $HRIS_DIR/public

    <Directory $HRIS_DIR/public>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog \${APACHE_LOG_DIR}/hris_error.log
    CustomLog \${APACHE_LOG_DIR}/hris_access.log combined
</VirtualHost>
APACHE

    # Enable site
    case "$DISTRO_NAME" in
        ubuntu|debian)
            sudo a2ensite hris.conf 2>/dev/null || true
            sudo a2dissite 000-default.conf 2>/dev/null || true
            ;;
    esac

    echo -e "${GREEN}Apache configured.${NC}"
}

reload_webserver() {
    local WEBSERVER="$1"

    echo -e "${YELLOW}Reloading $WEBSERVER...${NC}"
    case "$WEBSERVER" in
        nginx)
            sudo nginx -t && sudo systemctl reload nginx
            ;;
        apache)
            case "$DISTRO_NAME" in
                ubuntu|debian)
                    sudo apache2ctl configtest && sudo systemctl reload apache2
                    ;;
                centos|rhel|fedora)
                    sudo apachectl configtest && sudo systemctl reload httpd
                    ;;
                arch)
                    sudo apachectl configtest && sudo systemctl reload httpd
                    ;;
                opensuse*)
                    sudo apache2ctl configtest && sudo systemctl reload apache2
                    ;;
            esac
            ;;
    esac
    echo -e "${GREEN}$WEBSERVER reloaded.${NC}"
}

configure_webserver() {
    local WEBSERVER="$1"
    local HRIS_DIR="$2"
    local DOMAIN="${3:-localhost}"

    case "$WEBSERVER" in
        nginx)
            configure_nginx "$HRIS_DIR" "$DOMAIN"
            ;;
        apache)
            configure_apache "$HRIS_DIR" "$DOMAIN"
            ;;
        *)
            echo -e "${RED}Unknown web server: $WEBSERVER. Use 'nginx' or 'apache'.${NC}"
            exit 1
            ;;
    esac

    reload_webserver "$WEBSERVER"
}

# If sourced, just define functions; if executed, run with args
if [[ "${BASH_SOURCE[0]}" == "${0}" ]]; then
    if [ $# -lt 2 ]; then
        echo "Usage: $0 {nginx|apache} <hris-dir> [domain]"
        exit 1
    fi
    SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
    source "$SCRIPT_DIR/detect-distro.sh"
    detect_distro
    configure_webserver "$@"
fi
