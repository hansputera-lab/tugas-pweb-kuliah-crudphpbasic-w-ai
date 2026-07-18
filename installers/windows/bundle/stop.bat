@echo off
title HRIS - Stopping Services

echo Stopping HRIS services...
echo.

echo [1/2] Stopping Apache...
net stop "HRIS Apache" 2>nul
echo   Apache stopped.

echo [2/2] Stopping MariaDB...
net stop "HRIS MariaDB" 2>nul
echo   MariaDB stopped.

echo.
echo All HRIS services stopped.
echo.
