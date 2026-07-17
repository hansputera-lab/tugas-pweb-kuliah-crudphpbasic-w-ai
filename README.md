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
- **BPJS Calculation** — Automatic BPJS Kesehatan, Ketenagakerjaan (JKK, JKM, JHT, JP) contribution calculation with configurable rates per component, wage caps, and risk-level-based JKK rates
- **PPh 21 Calculation** — Automatic income tax calculation using TER (Tarif Efektif Rata-rata) method per PMK 168/2023, with Pasal 17 progressive tax true-up at year-end, configurable PTKP thresholds, tax brackets, biaya jabatan, and DTP support
- **Reimbursement** — Employee expense claims with receipt upload and configurable multi-level approval (per category)
- **Shift & Overtime** — Shift definitions, employee shift assignment, calendar-style schedule, overtime requests that flow into payroll
- **Performance / KPI** — Weighted KPI appraisals with A/B/C/D/E grading and 360° feedback (grade thresholds admin-configurable)
- **Dashboard** — Real-time stats, Chart.js attendance/leave charts, permission-based visibility
- **Reports** — Employee list, attendance summary, leave reports
- **Activity Logging** — Audit trail for all leave actions
- **Admin Config** — Working hours, grace period, company name, payroll defaults, KPI thresholds (all configurable via UI)
- **Currency Input Formatting** — All monetary input fields auto-format with `toLocaleString('id-ID')` as you type (formatted decimals on blur, raw numbers on focus for editing, auto-stripped on submit)
- **Frontend Form Validation** — All forms validate before submission using HTML5 constraints + custom JS: required fields, email format, min/max ranges, maxlength, radio groups; inline red error messages with styled borders; currency inputs unformatted before validation then reformatted on failure
- **Cross-platform Installers** — Windows GUI installer (bundles portable Apache + PHP + MariaDB, click-click setup) and Linux multi-distro bash installer (supports Ubuntu, Debian, CentOS, RHEL, Fedora, Arch, openSUSE)

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

The recommended way to deploy HRIS is using the automated installers in the [`installers/`](installers/) directory:

| Platform | Installer | Description |
|----------|-----------|-------------|
| **Windows** | `installers/windows/hris-installer.iss` → builds `HRIS-Setup.exe` | GUI wizard; bundles portable Apache + PHP 8.2 + MariaDB 10.4; auto-generates DB credentials; installs services & Start Menu shortcuts |
| **Linux** | `installers/linux/install.sh` | Multi-distro bash script (Ubuntu, Debian, CentOS, RHEL, Fedora, Arch, openSUSE); detects package manager & existing web server; runs `--nginx` or `--apache` |

### Quick Start

**Windows** — Run `HRIS-Setup.exe`, pick a directory, click through the wizard. The installer handles everything: database creation, `.env` setup, migrations, seeding.

**Linux** — One-liner for Nginx:

```bash
bash installers/linux/install.sh --nginx
```

Or interactive (prompts for web server choice):

```bash
bash installers/linux/install.sh
```

See `installers/linux/README.md` for environment variables, uninstall instructions, and distro support details.

---

> **Manual / Docker setup:** If you prefer to set up by hand or use Laravel Sail, refer to the manual steps in the [v1.0.0 tag](https://github.com/your-org/hris/tree/v1.0.0) or the original [project template](https://github.com/laravel/laravel).

## Alternative: Import SQL Dump (skip migration)

Instead of `migrate --seed`, you can import the full database dump:

```bash
# Import schema + all sample data
mysql -u root -p hris < database/hris.sql
```

This creates all tables, permissions, roles, and seed data in one go.

---

## Panduan Deployment (Bahasa Indonesia)

Cara termudah untuk menjalankan HRIS adalah menggunakan installer otomatis di folder [`installers/`](installers/):

| Platform | Installer | Keterangan |
|----------|-----------|------------|
| **Windows** | `installers/windows/hris-installer.iss` → `HRIS-Setup.exe` | Wizard GUI; menyertakan Apache + PHP 8.2 + MariaDB 10.4 portable; generate kredensial database otomatis; memasang service & shortcut Start Menu |
| **Linux** | `installers/linux/install.sh` | Script bash multi-distro (Ubuntu, Debian, CentOS, RHEL, Fedora, Arch, openSUSE); mendeteksi package manager & web server yang sudah terpasang; opsi `--nginx` atau `--apache` |

### Mulai Cepat

**Windows** — Jalankan `HRIS-Setup.exe`, pilih direktori, ikuti wizard. Installer menangani semuanya: pembuatan database, konfigurasi `.env`, migrasi, dan seeding.

**Linux** — Satu baris perintah untuk Nginx:

```bash
bash installers/linux/install.sh --nginx
```

Atau interaktif (pilih web server):

```bash
bash installers/linux/install.sh
```

Lihat `installers/linux/README.md` untuk variabel lingkungan, petunjuk uninstall, dan dukungan distribusi.

---

> **Setup manual / Docker:** Jika ingin melakukan instalasi manual atau menggunakan Laravel Sail, lihat langkah-langkah di [tag v1.0.0](https://github.com/your-org/hris/tree/v1.0.0) atau [project template asli](https://github.com/laravel/laravel).

## Alternatif: Import SQL Dump (lewati migrasi)

Sebagai pengganti `migrate --seed`, Anda bisa mengimpor dump database lengkap:

```bash
# Import skema + semua data contoh
mysql -u root -p hris < database/hris.sql
```

Ini akan membuat semua tabel, permissions, roles, dan data seed sekaligus.

---

## Pemecahan Masalah (Troubleshooting)

### "Class not found"
```bash
composer dump-autoload
```

### "Vite manifest not found" (halaman kosong, tidak ada CSS)
```bash
npm install && npm run build
```

### 500 Server Error setelah deployment
```bash
# Cek log Laravel
tail -f storage/logs/laravel.log

# Perbaiki kepemilikan file (penyebab umum)
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache

# Cache ulang views
php artisan view:cache
```

### "SQLSTATE[42S02] Table not found"
```bash
php artisan migrate --seed --force
```

### "SQLSTATE[HY000] Connection refused"
- Periksa apakah MySQL berjalan: `sudo systemctl status mysql`
- Verifikasi kredensial `.env`
- Pastikan database ada: `mysql -u root -p -e "SHOW DATABASES;"`

### Storage/upload tidak berfungsi
```bash
php artisan storage:link
chmod -R 775 storage/app/public
```

### Permission denied di storage
```bash
sudo chown -R www-data:www-data storage
sudo chmod -R 775 storage
```

### "403 Forbidden" pada route
- User tidak memiliki permission yang diperlukan untuk modul tersebut
- Verifikasi role user telah diberikan permission yang benar

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
