# HRIS - Human Resource Information System

A lightweight but complete HRIS built with **Laravel 11**, **TailwindCSS**, **Chart.js**, and **Domain-Driven Design** architecture. Designed as a student-level portfolio project that demonstrates enterprise-grade patterns while remaining simple enough to install and run in under 10 minutes.

## Features

- **Role-based Access Control** — 44 granular permissions across 11 modules, 9 roles (super_admin, hr_manager, hr_staff, manager, payroll_specialist, executive, recruiter, it_admin, employee), permission middleware on every route
- **Employee Master Data** — Full CRUD with photo upload, department/position assignment, status management, **manager-subordinate hierarchy** (reports to)
- **Department Management** — CRUD for organizational departments
- **Position Management** — CRUD for job positions with base salary and level
- **Attendance** — Daily check-in/check-out with late detection (configurable grace period)
- **Leave Management** — Request → Approve/Reject workflow, balance tracking, multiple leave types
- **Recruitment** — Job postings → Candidate applications → Screening → Interviews → Offer → Onboarding workflow
- **Payroll** — Auto-calculated salary from attendance (late/absent deductions + overtime), manual component override, period finalize/pay, printable HTML payslips, **payroll document attachments** per payslip line item
- **Reimbursement** — Employee expense claims with receipt upload and configurable multi-level approval (per category)
- **Shift & Overtime** — Shift definitions, employee shift assignment, calendar-style schedule, overtime requests that flow into payroll
- **Performance / KPI** — Weighted KPI appraisals with A/B/C/D/E grading and 360° feedback (grade thresholds admin-configurable)
- **Dashboard** — Real-time stats, Chart.js attendance/leave charts, permission-based visibility
- **Reports** — Employee list, attendance summary, leave reports
- **Activity Logging** — Audit trail for all leave actions
- **Admin Config** — Working hours, grace period, company name, payroll defaults, KPI thresholds (all configurable via UI)

## Tech Stack

| Component | Technology |
|-----------|-----------|
| Backend | Laravel 11 (PHP 8.2+) |
| Frontend | Blade + TailwindCSS + Alpine.js |
| Charts | Chart.js 4.x (CDN) |
| Database | MySQL 8.0+ / MariaDB 10.4+ |
| Auth | Laravel Breeze |
| Architecture | Lightweight DDD (Domain-Driven Design) |
| Build | Vite |

## Prerequisites by Platform

### Docker (cross-platform)
- Docker & Docker Compose

### Windows (XAMPP)
- XAMPP 8.2+ (PHP, MySQL, Apache bundled)
- Composer 2.x
- Node.js 18+ with npm
- Git

### Linux (native)
- PHP 8.2+ with extensions: pdo_mysql, mbstring, openssl, tokenizer, xml, ctype, json, bcmath, gd, fileinfo, zip, intl, curl
- Composer 2.x
- Node.js 18+ with npm
- MySQL 8.0+ or MariaDB 10.4+
- Nginx or Apache2
- Git

---

## Deployment Guide

### Option 1: Docker (Laravel Sail)

This uses the included Laravel Sail Docker setup.

```bash
# 1. Clone the repository
git clone <repository-url> hris
cd hris

# 2. Configure environment
cp .env.example .env
# Edit .env:
#   DB_CONNECTION=mysql
#   DB_HOST=mysql
#   DB_PORT=3306
#   DB_DATABASE=hris
#   DB_USERNAME=sail
#   DB_PASSWORD=password
#   APP_URL=http://localhost

# 3. Install PHP dependencies
docker run --rm -u "$(id -u):$(id -g)" -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    composer:latest composer install --ignore-platform-reqs

# 4. Install Node dependencies & build assets
docker run --rm -u "$(id -u):$(id -g)" -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    node:22 npm install && npm run build

# 5. Generate app key
php artisan key:generate

# 6. Start Sail containers
./vendor/bin/sail up -d

# 7. Create database & run migrations
./vendor/bin/sail artisan migrate --seed

# 8. Create storage symlink
./vendor/bin/sail artisan storage:link

# 9. Open in browser
# http://localhost
```

To stop: `./vendor/bin/sail down`

---

### Option 2: Windows (XAMPP)

```bash
# 1. Start XAMPP
# Open XAMPP Control Panel → Start Apache & MySQL

# 2. Clone to htdocs
cd C:\xampp\htdocs
git clone <repository-url> hris
cd hris

# 3. Install PHP dependencies
composer install

# 4. Install Node dependencies & build assets
npm install
npm run build

# 5. Configure environment
copy .env.example .env
# Edit .env with Notepad:
#   DB_CONNECTION=mysql
#   DB_HOST=127.0.0.1
#   DB_PORT=3306
#   DB_DATABASE=hris
#   DB_USERNAME=root
#   DB_PASSWORD=         (leave empty for XAMPP default)
#   APP_URL=http://localhost/hris/public
#   ASSET_URL=http://localhost/hris/public

# 6. Generate app key
php artisan key:generate

# 7. Create database
# Open phpMyAdmin (http://localhost/phpmyadmin)
# → New → Database name: hris → utf8mb4_unicode_ci → Create

# 8. Run migrations & seed
php artisan migrate --seed

# 9. Create storage symlink (run PowerShell as Admin)
php artisan storage:link

# 10. Set permissions (PowerShell as Admin)
icacls storage /grant "Everyone:(OI)(CI)M" /T
icacls public/uploads /grant "Everyone:(OI)(CI)M" /T

# 11. Serve the app
php artisan serve
# Or access via http://localhost/hris/public/public/index.php
# (For proper URL rewriting, configure Apache virtual host)
```

**XAMPP Apache Virtual Host (optional but recommended):**

Create `C:\xampp\apache\conf\extra\httpd-vhosts.conf` entry:

```apache
<VirtualHost *:80>
    DocumentRoot "C:/xampp/htdocs/hris/public"
    ServerName hris.local

    <Directory "C:/xampp/htdocs/hris/public">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

Add to `C:\Windows\System32\drivers\etc\hosts`:
```
127.0.0.1 hris.local
```

Restart Apache → open http://hris.local

---

### Option 3: Linux Native (Ubuntu/Debian)

#### Using Nginx

```bash
# 1. Install required packages
sudo apt update
sudo apt install -y nginx php8.2-fpm php8.2-mysql php8.2-mbstring \
    php8.2-xml php8.2-curl php8.2-bcmath php8.2-gd php8.2-zip \
    php8.2-intl php8.2-cli composer mysql-server git

# 2. Install Node.js (if not installed)
curl -fsSL https://deb.nodesource.com/setup_22.x | sudo -E bash -
sudo apt install -y nodejs

# 3. Clone the project
sudo mkdir -p /var/www
sudo chown $USER:$USER /var/www
git clone <repository-url> /var/www/hris
cd /var/www/hris

# 4. Install dependencies
composer install
npm install && npm run build

# 5. Configure environment
cp .env.example .env
# Edit .env:
#   DB_CONNECTION=mysql
#   DB_HOST=127.0.0.1
#   DB_PORT=3306
#   DB_DATABASE=hris
#   DB_USERNAME=root
#   DB_PASSWORD=your_password
#   APP_URL=https://your-domain.com

# 6. Generate app key
php artisan key:generate

# 7. Configure MySQL
sudo mysql -e "CREATE DATABASE IF NOT EXISTS hris CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
sudo mysql -e "ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY 'your_password'; FLUSH PRIVILEGES;"

# 8. Run migrations & seed
php artisan migrate --seed

# 9. Create storage symlink
php artisan storage:link

# 10. Set proper permissions
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache

# 11. Configure Nginx
sudo tee /etc/nginx/sites-available/hris << 'NGINX'
server {
    listen 80;
    server_name your-domain.com;
    root /var/www/hris/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(ht|git|env) {
        deny all;
    }

    location ~ ^/(app|bootstrap|config|database|resources|routes|storage|tests|vendor)/ {
        deny all;
    }

    location ~* \.(jpg|jpeg|png|gif|ico|css|js|woff|woff2|ttf|svg|eot)$ {
        expires 30d;
        add_header Cache-Control "public, immutable";
    }

    location ~* /uploads/.*\.php$ {
        deny all;
    }

    access_log /var/log/nginx/hris_access.log;
    error_log /var/log/nginx/hris_error.log;
}
NGINX

sudo ln -sf /etc/nginx/sites-available/hris /etc/nginx/sites-enabled/
sudo rm -f /etc/nginx/sites-enabled/default
sudo nginx -t && sudo systemctl reload nginx

# 12. (Optional) SSL with Let's Encrypt
sudo apt install -y certbot python3-certbot-nginx
sudo certbot --nginx -d your-domain.com

# 13. Open in browser
# http://your-domain.com or https://your-domain.com
```

#### Using Apache2

```bash
# 1. Install required packages (swap nginx for apache2)
sudo apt update
sudo apt install -y apache2 php8.2 php8.2-mysql php8.2-mbstring \
    php8.2-xml php8.2-curl php8.2-bcmath php8.2-gd php8.2-zip \
    php8.2-intl php8.2-cli libapache2-mod-php8.2 composer mysql-server git

# 2-9. Same as Nginx steps 2-10 above

# 10. Enable mod_rewrite
sudo a2enmod rewrite

# 11. Configure Apache virtual host
sudo tee /etc/apache2/sites-available/hris.conf << 'APACHE'
<VirtualHost *:80>
    ServerName your-domain.com
    DocumentRoot /var/www/hris/public

    <Directory /var/www/hris/public>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/hris_error.log
    CustomLog ${APACHE_LOG_DIR}/hris_access.log combined
</VirtualHost>
APACHE

sudo a2dissite 000-default.conf
sudo a2ensite hris.conf
sudo apache2ctl configtest && sudo systemctl reload apache2

# 12. (Optional) SSL with Let's Encrypt
sudo apt install -y certbot python3-certbot-apache
sudo certbot --apache -d your-domain.com
```

---

## Alternative: Import SQL Dump (skip migration)

Instead of `migrate --seed`, you can import the full database dump:

```bash
# Import schema + all sample data
mysql -u root -p hris < database/hris.sql
```

This creates all tables, permissions, roles, and seed data in one go.

---

## Default Login Credentials

| Role | Email | Password |
|------|-------|----------|
| Super Admin | admin@hris.test | password |
| HR Manager | hr@hris.test | password |
| Manager | manager@hris.test | password |
| Payroll Specialist | payroll@hris.test | password |
| Executive | executive@hris.test | password |
| Recruiter | recruiter@hris.test | password |
| IT Admin | itadmin@hris.test | password |
| Employee (Rina) | rina@hris.test | password |
| Employee (Budi) | budi@hris.test | password |
| Employee | employee@hris.test | password |

> **Note:** All accounts use `password` as the default. Change these in production.

## Project Structure (DDD Architecture)

```
hris/
├── app/
│   ├── Domains/                      # Business logic (vertical slices)
│   │   ├── Auth/                     # Roles & Permissions
│   │   ├── Employee/                 # Employee module (manager hierarchy)
│   │   ├── Department/               # Department module
│   │   ├── Position/                 # Position module
│   │   ├── Attendance/               # Attendance module
│   │   ├── Leave/                    # Leave module
│   │   ├── Recruitment/              # Job postings → Candidates → Interviews → Onboarding
│   │   ├── Payroll/                  # Payroll + documents
│   │   ├── Reimbursement/            # Claims, approval levels
│   │   ├── Shift/                    # Shifts, overtime
│   │   ├── Performance/              # KPIs, appraisals, 360 feedback
│   │   ├── ActivityLog/              # Audit trail
│   │   └── Settings/                 # App configuration
│   ├── Http/                         # Controllers, Middleware, Requests
│   ├── Models/                       # User model
│   └── Providers/                    # Service Providers
├── config/
├── database/                         # Migrations, seeders, SQL dump
├── resources/views/                  # Blade templates
├── routes/                           # Route definitions
└── public/
```

## Troubleshooting

### "Class not found"
```bash
composer dump-autoload
```

### "Vite manifest not found" (blank page, no CSS)
```bash
npm install && npm run build
```

### 500 Server Error after deployment
```bash
# Check Laravel log
tail -f storage/logs/laravel.log

# Fix file ownership (common cause)
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache

# Re-cache views
php artisan view:cache
```

### "SQLSTATE[42S02] Table not found"
```bash
php artisan migrate --seed --force
```

### "SQLSTATE[HY000] Connection refused"
- Check MySQL is running: `sudo systemctl status mysql`
- Verify `.env` credentials
- Confirm database exists: `mysql -u root -p -e "SHOW DATABASES;"`

### Storage/upload not working
```bash
php artisan storage:link
chmod -R 775 storage/app/public
```

### Permission denied in storage
```bash
sudo chown -R www-data:www-data storage
sudo chmod -R 775 storage
```

### "403 Forbidden" on routes
- User lacks the required permission for that module
- Verify user's role has the correct permissions assigned

## License

MIT
