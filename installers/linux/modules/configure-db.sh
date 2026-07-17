#!/usr/bin/env bash
# configure-db.sh — Creates MySQL database and user with random password

set -euo pipefail

RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

configure_db() {
    local HRIS_DIR="$1"
    local DB_NAME="${2:-hris}"
    local DB_USER="${3:-hris}"
    local DB_PASS=""

    # Generate random 24-char password
    if command -v openssl &>/dev/null; then
        DB_PASS=$(openssl rand -base64 18 | tr -d '/+=' | cut -c1-24)
    else
        DB_PASS=$(tr -dc 'A-Za-z0-9' < /dev/urandom | head -c 24)
    fi

    echo -e "${YELLOW}Creating database '$DB_NAME' and user '$DB_USER'...${NC}"

    # Try sudo mysql first, fall back to mysql as root
    local MYSQL_CMD=""
    if sudo mysql -e "SELECT 1" &>/dev/null; then
        MYSQL_CMD="sudo mysql"
    elif mysql -u root -e "SELECT 1" &>/dev/null; then
        MYSQL_CMD="mysql -u root"
    else
        # Prompt for MySQL root password
        echo -e "${YELLOW}MySQL root password required:${NC}"
        read -s -p "Enter MySQL root password: " MYSQL_ROOT_PASS
        echo
        MYSQL_CMD="mysql -u root -p\"$MYSQL_ROOT_PASS\""
    fi

    # Create database and user
    eval $MYSQL_CMD <<SQL
CREATE DATABASE IF NOT EXISTS \`$DB_NAME\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS '$DB_USER'@'localhost' IDENTIFIED BY '$DB_PASS';
GRANT ALL PRIVILEGES ON \`$DB_NAME\`.* TO '$DB_USER'@'localhost';
FLUSH PRIVILEGES;
SQL

    echo -e "${GREEN}Database configured.${NC}"

    # Write credentials to .env
    if [ -f "$HRIS_DIR/.env" ]; then
        sed -i "s/DB_DATABASE=.*/DB_DATABASE=$DB_NAME/" "$HRIS_DIR/.env"
        sed -i "s/DB_USERNAME=.*/DB_USERNAME=$DB_USER/" "$HRIS_DIR/.env"
        sed -i "s/DB_PASSWORD=.*/DB_PASSWORD=$DB_PASS/" "$HRIS_DIR/.env"
        sed -i "s|DB_HOST=.*|DB_HOST=127.0.0.1|" "$HRIS_DIR/.env"
        echo -e "${GREEN}.env updated with database credentials.${NC}"
    fi

    # Save credentials to a file
    cat > "$HRIS_DIR/database-credentials.txt" <<CRED
HRIS Database Credentials
=========================
Database: $DB_NAME
Username: $DB_USER
Password: $DB_PASS
CRED
    chmod 600 "$HRIS_DIR/database-credentials.txt"
    echo -e "${GREEN}Credentials saved to database-credentials.txt${NC}"

    echo "$DB_PASS"
}

# If sourced, just define functions; if executed, run with args
if [[ "${BASH_SOURCE[0]}" == "${0}" ]]; then
    if [ $# -lt 1 ]; then
        echo "Usage: $0 <hris-dir> [db-name] [db-user]"
        exit 1
    fi
    configure_db "$@"
fi
