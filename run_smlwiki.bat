@echo off
title SMLWiki Local Server
cd /d "%~dp0"

where php >nul 2>nul
if errorlevel 1 (
    echo.
    echo PHP was not found on this computer.
    echo Install PHP or XAMPP, then run this file again.
    echo.
    pause
    exit /b 1
)

echo Starting SMLWiki with PHP support...
echo Site: http://localhost:8000/
echo.
start "" "http://localhost:8000/"
php -S 127.0.0.1:8000 -t "%~dp0"

pause
