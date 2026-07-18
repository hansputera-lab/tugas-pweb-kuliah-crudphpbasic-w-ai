@echo off
setlocal enabledelayedexpansion

set INSTALL_DIR=%~1
if "%INSTALL_DIR%"=="" set INSTALL_DIR=%CD%

set APACHE_DIR=%INSTALL_DIR%\apache
set PHP_DIR=%INSTALL_DIR%\php
set MARIADB_DIR=%INSTALL_DIR%\mariadb
set HRIS_DIR=%INSTALL_DIR%\hris
set DATA_DIR=%INSTALL_DIR%\data
set LOG_DIR=%INSTALL_DIR%\logs
set LOG_FILE=%INSTALL_DIR%\install.log
set MYSQLD_BIN=mysqld
set MYSQLADMIN_BIN=mysqladmin

if exist "%MARIADB_DIR%\bin\mariadbd.exe" set MYSQLD_BIN=mariadbd
if exist "%MARIADB_DIR%\bin\mariadb-admin.exe" set MYSQLADMIN_BIN=mariadb-admin

echo [HRIS] Post-install setup starting... & echo [HRIS] Post-install setup starting...> "%LOG_FILE%"

if not exist "%DATA_DIR%" mkdir "%DATA_DIR%"
if not exist "%LOG_DIR%" mkdir "%LOG_DIR%"

echo [HRIS] Install dir: %INSTALL_DIR%>> "%LOG_FILE%"

REM ============================================
REM Step 1: Initialize MariaDB
REM ============================================
echo [HRIS] Initializing MariaDB... & echo [HRIS] Initializing MariaDB...>> "%LOG_FILE%"
cd /d "%MARIADB_DIR%"

REM Verify MariaDB binaries exist
if not exist "bin\%MYSQLD_BIN%.exe" if not exist "bin\mysqld.exe" (
    echo [HRIS] ERROR: MariaDB binary not found in bin\. Check bundle/mariadb/ structure.>> "%LOG_FILE%"
    echo [HRIS] ERROR: MariaDB binary not found. Check bundle/mariadb/ structure.
    exit /b 1
)

if not exist "%DATA_DIR%\mysql" (
    call :init_datadir
)
echo [HRIS] MariaDB data directory initialized. & echo [HRIS] MariaDB data directory initialized.>> "%LOG_FILE%"

REM ============================================
REM Step 2: Generate random DB password
REM ============================================
echo [HRIS] Generating database password... & echo [HRIS] Generating database password...>> "%LOG_FILE%"
setlocal
set CHARS=ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789
set DB_PASS=
for /l %%i in (1,1,24) do (
    set /a R=!random! %% 62
    for %%j in (!R!) do set DB_PASS=!DB_PASS!!CHARS:~%%j,1!
)
endlocal & set DB_PASS=%DB_PASS%

REM ============================================
REM Step 3: Start MariaDB
REM ============================================
echo [HRIS] Starting MariaDB... & echo [HRIS] Starting MariaDB...>> "%LOG_FILE%"
cd /d "%MARIADB_DIR%"
bin\%MYSQLD_BIN% --datadir="%DATA_DIR%" --port=3306 --socket=mysql.sock ^
    --skip-grant-tables --console > "%LOG_DIR%\mariadb.log" 2>&1 &
set MYSQL_PID=!ERRORLEVEL!
echo [HRIS] MariaDB PID: !MYSQL_PID! & echo [HRIS] MariaDB PID: !MYSQL_PID!>> "%LOG_FILE%"

REM Wait for MariaDB to be ready
:wait_mysql
bin\%MYSQLADMIN_BIN% ping --silent 2>nul
if errorlevel 1 (
    timeout /t 1 /nobreak >nul
    goto wait_mysql
)
echo [HRIS] MariaDB is ready. & echo [HRIS] MariaDB is ready.>> "%LOG_FILE%"

REM ============================================
REM Step 4: Create database and user
REM ============================================
echo [HRIS] Creating database and user... & echo [HRIS] Creating database and user...>> "%LOG_FILE%"
bin\mysql -u root -e "DROP USER IF EXISTS 'hris'@'localhost';" 2>nul
bin\mysql -u root -e "CREATE USER 'hris'@'localhost' IDENTIFIED BY '%DB_PASS%';"
bin\mysql -u root -e "CREATE DATABASE IF NOT EXISTS hris CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
bin\mysql -u root -e "GRANT ALL PRIVILEGES ON hris.* TO 'hris'@'localhost';"
bin\mysql -u root -e "FLUSH PRIVILEGES;"

REM Now restart MariaDB with auth enabled
bin\%MYSQLADMIN_BIN% -u root shutdown
timeout /t 2 /nobreak >nul
bin\%MYSQLD_BIN% --datadir="%DATA_DIR%" --port=3306 --socket=mysql.sock ^
    --console > "%LOG_DIR%\mariadb.log" 2>&1 &

:wait_mysql2
bin\%MYSQLADMIN_BIN% ping --silent 2>nul
if errorlevel 1 (
    timeout /t 1 /nobreak >nul
    goto wait_mysql2
)

echo [HRIS] Database configured. & echo [HRIS] Database configured.>> "%LOG_FILE%"

REM ============================================
REM Step 5: Configure .env
REM ============================================
echo [HRIS] Configuring .env... & echo [HRIS] Configuring .env...>> "%LOG_FILE%"
cd /d "%HRIS_DIR%"
if exist .env.example (
    copy /Y .env.example .env >nul
)

REM Update .env with database credentials
powershell -Command "(Get-Content .env) -replace 'DB_DATABASE=.*', 'DB_DATABASE=hris' | Set-Content .env"
powershell -Command "(Get-Content .env) -replace 'DB_USERNAME=.*', 'DB_USERNAME=hris' | Set-Content .env"
powershell -Command "(Get-Content .env) -replace 'DB_PASSWORD=.*', 'DB_PASSWORD=%DB_PASS%' | Set-Content .env"
powershell -Command "(Get-Content .env) -replace 'DB_HOST=.*', 'DB_HOST=127.0.0.1' | Set-Content .env"
powershell -Command "(Get-Content .env) -replace 'APP_URL=.*', 'APP_URL=http://localhost' | Set-Content .env"

REM ============================================
REM Step 6: Configure Apache
REM ============================================
echo [HRIS] Configuring Apache... & echo [HRIS] Configuring Apache...>> "%LOG_FILE%"
cd /d "%APACHE_DIR%"

REM Generate httpd.conf
(
echo ServerRoot "%APACHE_DIR:\=\\%"
echo ServerName localhost:80
echo Listen 80
echo DocumentRoot "%HRIS_DIR:\=\\%\\public"
echo ^<Directory "%HRIS_DIR:\=\\%\\public"^>
echo     Options Indexes FollowSymLinks
echo     AllowOverride All
echo     Require all granted
echo ^</Directory^>
echo LoadModule dir_module modules/mod_dir.so
echo LoadModule log_config_module modules/mod_log_config.so
echo LoadModule mime_module modules/mod_mime.so
echo LoadModule rewrite_module modules/mod_rewrite.so
echo LoadModule php_module "%PHP_DIR:\=\\%\\php8apache2_4.dll"
echo PHPIniDir "%PHP_DIR:\=\\%"
echo ^<FilesMatch \.php$^>
echo     SetHandler application/x-httpd-php
echo ^</FilesMatch^>
echo DirectoryIndex index.php index.html
echo ErrorLog "%LOG_DIR:\=\\%\\apache_error.log"
echo CustomLog "%LOG_DIR:\=\\%\\apache_access.log" common
echo TypesConfig conf/mime.types
) > "%APACHE_DIR%\conf\httpd.conf"

REM ============================================
REM Step 7: Configure PHP
REM ============================================
echo [HRIS] Configuring PHP... & echo [HRIS] Configuring PHP...>> "%LOG_FILE%"
cd /d "%PHP_DIR%"
if exist php.ini-production (
    copy /Y php.ini-production php.ini >nul
)

REM Enable required extensions
powershell -Command "(Get-Content php.ini) -replace ';extension=mbstring', 'extension=mbstring' | Set-Content php.ini"
powershell -Command "(Get-Content php.ini) -replace ';extension=openssl', 'extension=openssl' | Set-Content php.ini"
powershell -Command "(Get-Content php.ini) -replace ';extension=pdo_mysql', 'extension=pdo_mysql' | Set-Content php.ini"
powershell -Command "(Get-Content php.ini) -replace ';extension=gd', 'extension=gd' | Set-Content php.ini"
powershell -Command "(Get-Content php.ini) -replace ';extension=curl', 'extension=curl' | Set-Content php.ini"
powershell -Command "(Get-Content php.ini) -replace ';extension=fileinfo', 'extension=fileinfo' | Set-Content php.ini"
powershell -Command "(Get-Content php.ini) -replace 'extension_dir=\"ext\"', 'extension_dir=\"%PHP_DIR%\ext\"' | Set-Content php.ini"

REM ============================================
REM Step 8: Run Laravel setup
REM ============================================
echo [HRIS] Running Laravel setup... & echo [HRIS] Running Laravel setup...>> "%LOG_FILE%"
cd /d "%HRIS_DIR%"

REM Generate app key
php artisan key:generate --force
echo [HRIS] App key generated. & echo [HRIS] App key generated.>> "%LOG_FILE%"

REM Run migrations
php artisan migrate --seed --force
echo [HRIS] Migrations complete. & echo [HRIS] Migrations complete.>> "%LOG_FILE%"

REM Create storage link
php artisan storage:link --force
echo [HRIS] Storage link created. & echo [HRIS] Storage link created.>> "%LOG_FILE%"

REM Set permissions
icacls storage /grant "Everyone:(OI)(CI)M" /T /Q
icacls bootstrap/cache /grant "Everyone:(OI)(CI)M" /T /Q
icacls public/uploads /grant "Everyone:(OI)(CI)M" /T /Q
echo [HRIS] Permissions set. & echo [HRIS] Permissions set.>> "%LOG_FILE%"

REM ============================================
REM Step 9: Install services
REM ============================================
echo [HRIS] Installing Apache service... & echo [HRIS] Installing Apache service...>> "%LOG_FILE%"
"%APACHE_DIR%\bin\httpd.exe" -k install -n "HRIS Apache"
"%APACHE_DIR%\bin\httpd.exe" -k start -n "HRIS Apache"

echo [HRIS] Installing MariaDB service... & echo [HRIS] Installing MariaDB service...>> "%LOG_FILE%"
"%MARIADB_DIR%\bin\%MYSQLD_BIN%" --install "HRIS MariaDB" --datadir="%DATA_DIR%" --port=3306
net start "HRIS MariaDB" >nul 2>&1

REM ============================================
REM Step 10: Cleanup
REM ============================================
REM Stop the temporary MariaDB instance and let the service take over
"%MARIADB_DIR%\bin\%MYSQLADMIN_BIN%" -u root shutdown 2>nul

echo [HRIS] Setup complete! & echo [HRIS] Setup complete!>> "%LOG_FILE%"
echo [HRIS] You can now access HRIS at http://localhost & echo [HRIS] You can now access HRIS at http://localhost>> "%LOG_FILE%"
echo [HRIS] Database password: %DB_PASS% & echo [HRIS] Database password: %DB_PASS%>> "%LOG_FILE%"

REM Save credentials to a note file
(
echo HRIS Installation Summary
echo ========================
echo.
echo URL: http://localhost
echo Database: hris
echo Username: hris
echo Password: %DB_PASS%
echo.
echo Default login: admin@hris.test / password
) > "%INSTALL_DIR%\credentials.txt"
exit /b 0

:init_datadir
echo [HRIS] Initializing MariaDB data directory...
echo [HRIS] Trying mariadb-install-db...>> "%LOG_FILE%"
bin\mariadb-install-db.exe --datadir="%DATA_DIR%"
if not errorlevel 1 goto :eof

echo [HRIS] Trying mysql_install_db...>> "%LOG_FILE%"
bin\mysql_install_db.exe --datadir="%DATA_DIR%"
if not errorlevel 1 goto :eof

echo [HRIS] Trying %MYSQLD_BIN% --initialize-insecure...>> "%LOG_FILE%"
bin\%MYSQLD_BIN% --initialize-insecure --datadir="%DATA_DIR%"
if not errorlevel 1 goto :eof

echo [HRIS] Trying %MYSQLD_BIN% --initialize...>> "%LOG_FILE%"
bin\%MYSQLD_BIN% --initialize --datadir="%DATA_DIR%"
if not errorlevel 1 goto :eof

echo [HRIS] ERROR: All data directory initialization methods failed.>> "%LOG_FILE%"
echo [HRIS] ERROR: Could not initialize MariaDB data directory. See install.log for details.
exit /b 1
