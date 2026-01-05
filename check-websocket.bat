@echo off
echo Checking WebSocket server status...
echo.

REM Check if port 6001 is in use
netstat -an | findstr ":6001" >nul
if %errorlevel% == 0 (
    echo [OK] Port 6001 is in use - WebSocket server might be running
    echo.
    echo To verify, check if you see "php artisan websockets:serve" in your running processes
) else (
    echo [ERROR] Port 6001 is not in use - WebSocket server is NOT running
    echo.
    echo To start the WebSocket server, run:
    echo   php artisan websockets:serve --host=0.0.0.0 --port=6001
)

echo.
echo Press any key to exit...
pause >nul

