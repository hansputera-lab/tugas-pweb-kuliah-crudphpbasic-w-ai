@echo off
title HRIS - Stopping HRIS

set INSTALL_DIR=%~dp0

echo Stopping HRIS services...
echo.

echo [1/4] Stopping Apache...
net stop "HRIS Apache" 2>nul
echo   Apache stopped.

echo [2/4] Removing Apache service...
"%INSTALL_DIR%apache\bin\httpd.exe" -k stop -n "HRIS Apache" 2>nul
"%INSTALL_DIR%apache\bin\httpd.exe" -k uninstall -n "HRIS Apache" 2>nul
echo   Apache service removed.

echo [3/4] Stopping MariaDB...
net stop "HRIS MariaDB" 2>nul
echo   MariaDB stopped.

echo [4/4] Removing MariaDB service...
set MYSQLD_BIN=mysqld
if exist "%INSTALL_DIR%mariadb\bin\mariadbd.exe" set MYSQLD_BIN=mariadbd
"%INSTALL_DIR%mariadb\bin\%MYSQLD_BIN%" --remove "HRIS MariaDB" 2>nul
echo   MariaDB service removed.

echo.
echo All HRIS services stopped and removed.
