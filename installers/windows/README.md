# Windows Installer — Building the .exe

The easiest way is to download the pre-built `HRIS-Setup.exe` from the [Releases page](https://github.com/hansputera-lab/tugas-pweb-kuliah-crudphpbasic-w-ai/releases).

To build it yourself, follow the steps below.

## Prerequisites

1. **Inno Setup 6.x** — Download from https://jrsoftware.org/isinfo.php
2. **Portable runtimes** (download and place in `bundle/`):

### Required Downloads

Extract each into its respective folder under `bundle/`:

| Component | Download From | Extract To |
|-----------|-------------|------------|
| Apache 2.4 (VS17 x64) | https://www.apachelounge.com/download/ | `bundle/apache/` |
| PHP 8.2 TS (x64) | https://windows.php.net/download#php-8.2 | `bundle/php/` |
| MariaDB 11.x (ZIP) | https://mariadb.org/download/?t=mariadb&p=mariadb | `bundle/mariadb/` |

**MariaDB notes:**
- Compatible with MariaDB 10.4 through 11.x. Avoid MariaDB 12+ (binary rename breakage).
- Extract so that `bin/mariadbd.exe` (or `bin/mysqld.exe`) is directly under `bundle/mariadb/bin/`.

**PHP notes:**
- Download the **Thread Safe** (TS) version
- Required extensions already bundled: `php_pdo_mysql.dll`, `php_mbstring.dll`, `php_openssl.dll`, `php_gd.dll`, `php_curl.dll`, `php_fileinfo.dll`, `php_xml.dll`, `php_bcmath.dll`, `php_tokenizer.dll`

**HRIS app:**
- Run `npm run build` on the project first, then copy the entire project into `bundle/hris/`
- The `public/build/` directory with compiled assets must be included

## Build Steps

```powershell
# 1. Build the Laravel assets first
cd \path\to\hris
npm install && npm run build

# 2. Copy the project to the bundle folder (skips installers/, .git, etc.)
robocopy . installers\windows\bundle\hris\ /E /XD installers .git node_modules vendor

# 3. Open hris-installer.iss in Inno Setup
# 4. Click Build → Compile (or press Ctrl+F9)
```

Output: `installers/windows/output/HRIS-Setup-1.0.exe`

## What the installer does

1. Extracts Apache, PHP, MariaDB, and the HRIS app to the user's chosen directory
2. Initializes MariaDB data directory
3. Generates a random 24-char database password
4. Creates the `hris` database and user
5. Configures `.env` with database credentials
6. Writes Apache `httpd.conf` pointing to the HRIS public directory
7. Configures PHP with required extensions
8. Runs `php artisan key:generate`, `migrate --seed`, `storage:link`
9. Installs Apache and MariaDB as Windows services
10. Creates Start Menu and Desktop shortcuts
