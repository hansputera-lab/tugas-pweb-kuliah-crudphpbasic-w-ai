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
- **Cross-platform Installers** — Windows GUI installer (bundles portable Apache + PHP + MariaDB, click-click setup), Linux multi-distro bash installer (supports Ubuntu, Debian, CentOS, RHEL, Fedora, Arch, openSUSE), and Docker via Laravel Sail (auto-seed on start)

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

## Cara Instalasi (Installation Guide)

Panduan ini dibuat khusus untuk pemula. Setiap langkah dijelaskan secara detail.

### A. Yang Harus Disiapkan

Download dan install 4 aplikasi berikut (install dengan pengaturan default, next-next saja):

| Aplikasi | Kegunaan | Link Download |
|----------|----------|---------------|
| **XAMPP 8.2+** | Web server + database + PHP (semua dalam satu paket) | [apachefriends.org](https://www.apachefriends.org/download.html) |
| **Composer** | Menginstall library PHP | [getcomposer.org](https://getcomposer.org/download/) |
| **Node.js** (versi LTS) | Menginstall library JavaScript/CSS | [nodejs.org](https://nodejs.org/) |
| **Git** | Mengambil project dari GitHub | [git-scm.com](https://git-scm.com/downloads) |

### B. Langkah-Langkah Instalasi

#### Langkah 1: Start XAMPP

1. Buka **XAMPP Control Panel** (cari di Start Menu)
2. Klik **Start** pada **Apache** — tunggu sampai hijau
3. Klik **Start** pada **MySQL** — tunggu sampai hijau

> **Fungsinya apa?** Apache = web server yang menjalankan aplikasi. MySQL = database yang menyimpan data (karyawan, absensi, gaji, dll).

#### Langkah 2: Download Project

Buka **Command Prompt (CMD)** atau Terminal, lalu ketik:

```bash
cd C:\Users\%USERNAME%\Documents
git clone https://github.com/hansputera-lab/tugas-pweb-kuliah-crudphpbasic-w-ai.git hris
cd hris
```

> **Alternatif:** Klik "Code" → "Download ZIP" di halaman GitHub, lalu extract ke folder Documents.

#### Langkah 3: Install Library PHP (Composer)

```bash
composer install
```

Tunggu 2–5 menit. Composer akan mendownload semua library yang dibutuhkan Laravel secara otomatis.

#### Langkah 4: Install Library JavaScript (Node.js)

```bash
npm install
npm run build
```

`npm install` mendownload library (TailwindCSS, Alpine.js, dll).  
`npm run build` mengubahnya menjadi file CSS/JS yang bisa dibaca browser.

#### Langkah 5: Setup Database

1. Buka browser, ketik: `http://localhost/phpmyadmin`
2. Klik tab **Databases**, ketik `hris`, pilih **utf8mb4_general_ci**, klik **Create**
3. Klik tab **Import**, klik **Choose File**, pilih file `database/hris.sql` (dalam folder project)
4. Klik **Go** (scroll ke bawah)

Tunggu sampai muncul "Import has been successfully finished".

> File `hris.sql` sudah berisi semua tabel + data contoh, jadi tidak perlu membuat tabel satu per satu.

#### Langkah 6: Buat File .env

```bash
copy .env.example .env
```

Buka file `.env` dengan **Notepad** atau **VS Code**. Cari baris berikut dan ubah:

```
DB_CONNECTION=mysql         (ganti dari sqlite menjadi mysql)
DB_HOST=127.0.0.1           (hapus tanda # di depan)
DB_PORT=3306                (hapus tanda # di depan)
DB_DATABASE=hris            (hapus #, ganti laravel jadi hris)
DB_USERNAME=root            (hapus tanda # di depan)
DB_PASSWORD=                (hapus tanda # di depan, biarkan kosong)
```

#### Langkah 7: Generate App Key

```bash
php artisan key:generate
```

Membuat kunci pengaman untuk session dan cookies aplikasi.

#### Langkah 8: Jalankan Aplikasi

```bash
php artisan serve
```

Akan muncul: `Starting Laravel development server: http://localhost:8000`

Buka browser, ketik `http://localhost:8000`. HRIS siap digunakan!

> **Penting:** Jangan tutup jendela CMD selama ingin menggunakan aplikasi. Tekan `Ctrl + C` untuk menghentikan.

### C. Login

Setelah berhasil menjalankan, login dengan akun berikut:

| Role | Email | Password |
|------|-------|----------|
| Super Admin (paling lengkap) | admin@hris.test | password |
| HR Manager | hr@hris.test | password |
| Karyawan biasa | employee@hris.test | password |

> Login sebagai **Super Admin** untuk melihat semua fitur.

---

## Cara Lain (Alternatif)

### Docker (Laravel Sail)

**Yang dibutuhkan:**
- [Docker Desktop](https://www.docker.com/products/docker-desktop/) (Windows/Mac) atau Docker Engine (Linux)
- Git

**Langkah-langkah:**

#### 1. Install Docker Desktop

Download dari [docker.com](https://www.docker.com/products/docker-desktop/), install, lalu jalankan.  
Tunggu sampai icon Docker di taskbar/show system icons sudah stabil (tidak animasi).

#### 2. Download Project

```bash
git clone https://github.com/hansputera-lab/tugas-pweb-kuliah-crudphpbasic-w-ai.git hris
cd hris
```

#### 3. Jalankan Container

```bash
./sail up -d
```

Perintah ini akan mendownload image Docker (PHP 8.5, MariaDB 11) dan menjalankan aplikasi.  
Mungkin butuh 5–10 menit tergantung koneksi internet.

#### 4. Generate App Key (cukup sekali)

```bash
./sail artisan key:generate
```

#### 5. Buka Aplikasi

Akses `http://localhost:7774` di browser.  
Database bisa diakses di `localhost:7775` (user: `sail`, password: `password`).

> **Catatan:** Migrasi dan seeder berjalan **otomatis** setiap container menyala. Jadi tidak perlu menjalankan `migrate --seed` manual.

#### Perintah Docker yang Sering Dipakai

| Perintah | Fungsinya |
|----------|-----------|
| `./sail up -d` | Menjalankan container di background |
| `./sail stop` | Menghentikan container |
| `./sail down` | Menghentikan dan menghapus container |
| `./sail artisan ...` | Menjalankan perintah Laravel (contoh: `./sail artisan route:list`) |
| `./sail composer ...` | Menjalankan Composer di dalam container |
| `./sail npm ...` | Menjalankan npm di dalam container |
| `./sail logs` | Melihat log aplikasi |

### Windows Installer

Download `HRIS-Setup.exe` dari [Releases](https://github.com/hansputera-lab/tugas-pweb-kuliah-crudphpbasic-w-ai/releases). Install seperti aplikasi biasa, semua sudah include.

### Linux Installer

Satu perintah, otomatis:
```bash
curl -fsSL https://raw.githubusercontent.com/hansputera-lab/tugas-pweb-kuliah-crudphpbasic-w-ai/main/installers/linux/install.sh | bash -s -- --nginx
```

---

## Pemecahan Masalah (Troubleshooting)

### "php" atau "composer" tidak dikenal (command not found)
- Buka XAMPP Control Panel, klik **Config** → **PHP (php.ini)** — pastikan path PHP sudah terdaftar
- Alternatif: gunakan path lengkap, contoh: `C:\xampp\php\php.exe artisan key:generate`

### "SQLSTATE[HY000] Connection refused"
- Pastikan **MySQL** sudah **Start** di XAMPP Control Panel (warna hijau)
- Cek file `.env` — pastikan `DB_HOST=127.0.0.1` dan `DB_PORT=3306`
- Coba buka `http://localhost/phpmyadmin` — jika bisa, berarti MySQL berjalan normal

### "Vite manifest not found" (tampilan berantakan, tanpa CSS)
```bash
npm install && npm run build
```

### "Class not found"
```bash
composer dump-autoload
```

### 500 Server Error
```bash
# Cek log error Laravel (buka file ini di Notepad)
storage/logs/laravel.log

# Hapus cache
php artisan view:cache
php artisan config:cache
```

### "SQLSTATE[42S02] Table not found"
- Buka `http://localhost/phpmyadmin`, pilih database `hris`, cek apakah ada tabel-tabel di dalamnya
- Jika kosong, import ulang file `database/hris.sql`

### Storage/upload tidak berfungsi
```bash
php artisan storage:link
```

### "403 Forbidden" saat membuka halaman
- Akun yang kamu pakai mungkin tidak memiliki akses ke fitur tersebut
- Coba login sebagai **admin@hris.test** (Super Admin) yang memiliki akses penuh

## License

MIT
