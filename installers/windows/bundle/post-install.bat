@echo off
setlocal enabledelayedexpansion

REM ============================================================
REM  HRIS Post-Install Setup
REM  Runs after file extraction to configure MariaDB, Apache,
REM  PHP, .env, and install Windows services.
REM ============================================================

set INSTALL_DIR=%~1
if "%INSTALL_DIR%"=="" set INSTALL_DIR=%CD%

set APACHE_DIR=%INSTALL_DIR%\apache
set PHP_DIR=%INSTALL_DIR%\php
set MARIADB_DIR=%INSTALL_DIR%\mariadb
set HRIS_DIR=%INSTALL_DIR%\hris
set DATA_DIR=%INSTALL_DIR%\data
set LOG_DIR=%INSTALL_DIR%\logs
set LOG_FILE=%INSTALL_DIR%\install.log
set HTTP_PORT=7774
set DB_PORT=7775

REM Detect MariaDB binary (mariadbd for 11.x, mysqld for 10.x)
set MYSQLD_BIN=mysqld
if exist "%MARIADB_DIR%\bin\mariadbd.exe" set MYSQLD_BIN=mariadbd
set MYSQLADMIN_BIN=mysqladmin
if exist "%MARIADB_DIR%\bin\mariadb-admin.exe" set MYSQLADMIN_BIN=mariadb-admin

echo [HRIS] Post-install setup starting...
echo [HRIS] Install dir: %INSTALL_DIR%> "%LOG_FILE%"

goto :main

REM ============================================================
REM  Subroutines
REM ============================================================

:log
echo [HRIS] %*>> "%LOG_FILE%"
echo [HRIS] %*
exit /b 0

:dump_log
if exist "%LOG_DIR%\mariadb.log" (
    echo.
    type "%LOG_DIR%\mariadb.log"
    echo.>> "%LOG_FILE%"
    echo --- mariadb.log --->> "%LOG_FILE%"
    type "%LOG_DIR%\mariadb.log" >> "%LOG_FILE%"
)
exit /b 0

:wait_for_mysql
set TRY_COUNT=0
:wait_loop
set /a TRY_COUNT+=1
if !TRY_COUNT! gtr 60 (
    call :dump_log
    exit /b 1
)
set /a REMAINDER=!TRY_COUNT! %% 5
if !REMAINDER! equ 0 echo [HRIS] Waiting for MariaDB... (!TRY_COUNT!s)
netstat -an | findstr /C:":%DB_PORT% " >nul
if errorlevel 1 (
    timeout /t 1 /nobreak >nul
    goto wait_loop
)
exit /b 0

:check_process
tasklist /FI "IMAGENAME eq %MYSQLD_BIN%.exe" /NH 2>nul | findstr /I "%MYSQLD_BIN%" >nul
if errorlevel 1 (
    tasklist /FI "IMAGENAME eq mysqld.exe" /NH 2>nul | findstr /I "mysqld" >nul
)
exit /b %errorlevel%

:init_datadir
call :log "Trying mariadb-install-db..."
bin\mariadb-install-db.exe --datadir="%DATA_DIR%"
if not errorlevel 1 exit /b 0
call :log "Trying mysql_install_db..."
bin\mysql_install_db.exe --datadir="%DATA_DIR%"
if not errorlevel 1 exit /b 0
call :log "Trying %MYSQLD_BIN% --initialize-insecure..."
bin\%MYSQLD_BIN% --initialize-insecure --datadir="%DATA_DIR%"
if not errorlevel 1 exit /b 0
call :log "Trying %MYSQLD_BIN% --initialize..."
bin\%MYSQLD_BIN% --initialize --datadir="%DATA_DIR%"
if not errorlevel 1 exit /b 0
call :log "ERROR: All data directory initialization methods failed."
exit /b 1

REM ============================================================
:main
REM ============================================================

REM 1. Create directories
if not exist "%DATA_DIR%" mkdir "%DATA_DIR%"
if not exist "%LOG_DIR%" mkdir "%LOG_DIR%"

REM 2. Generate my.ini for reliable MariaDB configuration
call :log "Generating my.ini..."
(
echo [client]
echo port=%DB_PORT%
echo.
echo [mysqld]
echo port=%DB_PORT%
echo bind-address=127.0.0.1
echo datadir="%DATA_DIR%"
echo skip-networking=0
) > "%MARIADB_DIR%\my.ini"

REM 3. Initialize MariaDB data directory
call :log "Initializing MariaDB data directory..."
cd /d "%MARIADB_DIR%"
if not exist "%DATA_DIR%\mysql" (
    call :init_datadir
    if errorlevel 1 (
        call :dump_log
        call :log "ERROR: Could not initialize MariaDB data directory."
        exit /b 1
    )
)
call :log "Data directory ready."

REM 4. Generate random database password (24 chars)
setlocal
set CHARS=ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789
set DB_PASS=
for /l %%i in (1,1,24) do (
    set /a R=!random! %% 62
    for %%j in (!R!) do set DB_PASS=!DB_PASS!!CHARS:~%%j,1!
)
endlocal & set DB_PASS=%DB_PASS%

REM 5. Start MariaDB with skip-grant-tables for initial setup
call :log "Starting MariaDB (skip-grant-tables)..."
cd /d "%MARIADB_DIR%"
if exist "%LOG_DIR%\mariadb.log" del "%LOG_DIR%\mariadb.log"

bin\%MYSQLD_BIN% --defaults-file="%MARIADB_DIR%\my.ini" --skip-grant-tables --console > "%LOG_DIR%\mariadb.log" 2>&1 &

timeout /t 2 /nobreak >nul
call :check_process
if errorlevel 1 (
    call :dump_log
    call :log "ERROR: MariaDB process died immediately after launch."
    exit /b 1
)

call :wait_for_mysql
if errorlevel 1 (
    call :log "ERROR: Timed out waiting for MariaDB TCP port %DB_PORT%."
    exit /b 1
)
call :log "MariaDB is running."

REM 6. Create database user, database, and grant privileges
call :log "Creating database and user..."
cd /d "%MARIADB_DIR%"
bin\mysql -u root --protocol=tcp --port=%DB_PORT% -e "DROP USER IF EXISTS 'hris'@'localhost';" 2>nul
bin\mysql -u root --protocol=tcp --port=%DB_PORT% -e "CREATE USER 'hris'@'localhost' IDENTIFIED BY '%DB_PASS%';"
if errorlevel 1 (
    call :dump_log
    call :log "ERROR: Failed to create database user."
    exit /b 1
)
bin\mysql -u root --protocol=tcp --port=%DB_PORT% -e "CREATE DATABASE IF NOT EXISTS hris CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
if errorlevel 1 (
    call :log "ERROR: Failed to create database."
    exit /b 1
)
bin\mysql -u root --protocol=tcp --port=%DB_PORT% -e "GRANT ALL PRIVILEGES ON hris.* TO 'hris'@'localhost';"
bin\mysql -u root --protocol=tcp --port=%DB_PORT% -e "FLUSH PRIVILEGES;"

REM 7. Restart MariaDB with authentication enabled
call :log "Restarting MariaDB without skip-grant-tables..."
cd /d "%MARIADB_DIR%"
bin\%MYSQLADMIN_BIN% -u root --protocol=tcp --port=%DB_PORT% shutdown
timeout /t 3 /nobreak >nul

bin\%MYSQLD_BIN% --defaults-file="%MARIADB_DIR%\my.ini" --console > "%LOG_DIR%\mariadb.log" 2>&1 &

timeout /t 2 /nobreak >nul
call :check_process
if errorlevel 1 (
    call :dump_log
    call :log "ERROR: MariaDB process died after restart."
    exit /b 1
)

call :wait_for_mysql
if errorlevel 1 (
    call :log "ERROR: Timed out waiting for MariaDB TCP port after restart."
    exit /b 1
)
call :log "Database configured."

REM 8. Configure .env file
call :log "Configuring .env..."
cd /d "%HRIS_DIR%"
if exist .env.example (
    copy /Y .env.example .env >nul
)

powershell -Command "(Get-Content .env) -replace 'DB_CONNECTION=sqlite', 'DB_CONNECTION=mysql' | Set-Content .env"
powershell -Command "(Get-Content .env) -replace '# DB_HOST=.*', 'DB_HOST=127.0.0.1' | Set-Content .env"
powershell -Command "(Get-Content .env) -replace '# DB_PORT=.*', 'DB_PORT=%DB_PORT%' | Set-Content .env"
powershell -Command "(Get-Content .env) -replace '# DB_DATABASE=.*', 'DB_DATABASE=hris' | Set-Content .env"
powershell -Command "(Get-Content .env) -replace '# DB_USERNAME=.*', 'DB_USERNAME=hris' | Set-Content .env"
powershell -Command "(Get-Content .env) -replace '# DB_PASSWORD=.*', 'DB_PASSWORD=%DB_PASS%' | Set-Content .env"
powershell -Command "(Get-Content .env) -replace 'APP_URL=.*', 'APP_URL=http://localhost:%HTTP_PORT%' | Set-Content .env"

REM 9. Configure Apache httpd.conf
call :log "Configuring Apache..."
(
echo ServerRoot "%APACHE_DIR:\=\\%"
echo ServerName localhost:%HTTP_PORT%
echo Listen %HTTP_PORT%
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

REM 10. Configure PHP
call :log "Configuring PHP..."
cd /d "%PHP_DIR%"
if exist php.ini-production (
    copy /Y php.ini-production php.ini >nul
)

powershell -Command "(Get-Content php.ini) -replace ';extension=mbstring', 'extension=mbstring' | Set-Content php.ini"
powershell -Command "(Get-Content php.ini) -replace ';extension=openssl', 'extension=openssl' | Set-Content php.ini"
powershell -Command "(Get-Content php.ini) -replace ';extension=pdo_mysql', 'extension=pdo_mysql' | Set-Content php.ini"
powershell -Command "(Get-Content php.ini) -replace ';extension=gd', 'extension=gd' | Set-Content php.ini"
powershell -Command "(Get-Content php.ini) -replace ';extension=curl', 'extension=curl' | Set-Content php.ini"
powershell -Command "(Get-Content php.ini) -replace ';extension=fileinfo', 'extension=fileinfo' | Set-Content php.ini"
powershell -Command "(Get-Content php.ini) -replace 'extension_dir=\"ext\"', 'extension_dir=\"%PHP_DIR:\=\\%\\ext\"' | Set-Content php.ini"

REM 11. Laravel setup (key, migrate, storage link, permissions)
call :log "Running Laravel setup..."
cd /d "%HRIS_DIR%"

php artisan key:generate --force
if errorlevel 1 (
    call :log "ERROR: php artisan key:generate failed."
    exit /b 1
)
call :log "App key generated."

php artisan migrate --seed --force
if errorlevel 1 (
    call :log "ERROR: php artisan migrate --seed failed."
    exit /b 1
)
call :log "Migrations complete."

php artisan storage:link --force
call :log "Storage link created."

icacls storage /grant "Everyone:(OI)(CI)M" /T /Q
icacls bootstrap/cache /grant "Everyone:(OI)(CI)M" /T /Q
icacls public/uploads /grant "Everyone:(OI)(CI)M" /T /Q
call :log "Permissions set."

REM 12. Install Windows services
call :log "Installing Apache service..."
"%APACHE_DIR%\bin\httpd.exe" -k install -n "HRIS Apache" 2>nul
"%APACHE_DIR%\bin\httpd.exe" -k start -n "HRIS Apache"

call :log "Installing MariaDB service..."
"%MARIADB_DIR%\bin\%MYSQLD_BIN%" --install "HRIS MariaDB" --defaults-file="%MARIADB_DIR%\my.ini" 2>nul
net start "HRIS MariaDB" >nul 2>&1

REM 13. Shutdown temporary MariaDB instance (service takes over)
"%MARIADB_DIR%\bin\%MYSQLADMIN_BIN%" -u root --protocol=tcp --port=%DB_PORT% shutdown 2>nul

REM 14. Save credentials and print summary
(
echo HRIS Installation Summary
echo ========================
echo.
echo URL: http://localhost:%HTTP_PORT%
echo Database: hris
echo Username: hris
echo Password: %DB_PASS%
echo Database Port: %DB_PORT%
echo.
echo Default login: admin@hris.test / password
) > "%INSTALL_DIR%\credentials.txt"

call :log "========================================"
call :log "HRIS Setup Complete!"
call :log ""
call :log "URL:      http://localhost:%HTTP_PORT%"
call :log "Database: hris / hris / %DB_PASS%"
call :log ""
call :log "Default login: admin@hris.test / password"
call :log "========================================"

exit /b 0
