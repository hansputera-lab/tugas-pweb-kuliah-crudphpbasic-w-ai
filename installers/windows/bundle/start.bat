@echo off
title HRIS - Starting Services

set INSTALL_DIR=%~dp0

echo Starting HRIS services...
echo.

REM Detect MariaDB binary name (mariadbd for 11+, mysqld for 10.x)
set MYSQLD_BIN=mysqld
if exist "%INSTALL_DIR%mariadb\bin\mariadbd.exe" set MYSQLD_BIN=mariadbd

echo [1/2] Starting MariaDB...
net start "HRIS MariaDB" 2>nul
if errorlevel 1 (
    "%INSTALL_DIR%mariadb\bin\%MYSQLD_BIN%" --datadir="%INSTALL_DIR%data" --port=3306 --console
)
echo   MariaDB is running.

echo [2/2] Starting Apache...
net start "HRIS Apache" 2>nul
if errorlevel 1 (
    "%INSTALL_DIR%apache\bin\httpd.exe" -k start -n "HRIS Apache"
)
echo   Apache is running.

echo.
echo HRIS is now available at http://localhost
echo.
echo Press any key to open HRIS in your browser...
pause >nul
start http://localhost
