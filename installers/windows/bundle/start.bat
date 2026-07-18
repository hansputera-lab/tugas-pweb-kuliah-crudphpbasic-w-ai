@echo off
title HRIS - Starting Services

set INSTALL_DIR=%~dp0
set HTTP_PORT=7774

echo Starting HRIS services...
echo.

echo [1/2] Starting MariaDB...
net start "HRIS MariaDB" 2>nul
if errorlevel 1 (
    set MYSQLD_BIN=mysqld
    if exist "%INSTALL_DIR%mariadb\bin\mariadbd.exe" set MYSQLD_BIN=mariadbd
    "%INSTALL_DIR%mariadb\bin\%MYSQLD_BIN%" --defaults-file="%INSTALL_DIR%mariadb\my.ini" --console
)
echo   MariaDB is running.

echo [2/2] Starting Apache...
net start "HRIS Apache" 2>nul
if errorlevel 1 (
    "%INSTALL_DIR%apache\bin\httpd.exe" -k start -n "HRIS Apache"
)
echo   Apache is running.

echo.
echo HRIS is now available at http://localhost:%HTTP_PORT%
echo.
echo Press any key to open HRIS in your browser...
pause >nul
start http://localhost:%HTTP_PORT%
