@echo off
echo Starting SQL import...
echo.

echo Checking MySQL connection...
mysql -u root -h 127.0.0.1 -e "SELECT 'MySQL connection successful' as status;"

if %errorlevel% neq 0 (
    echo ERROR: Cannot connect to MySQL
    pause
    exit /b 1
)

echo.
echo Creating/using database apotek_parahyangan_db...
mysql -u root -h 127.0.0.1 -e "CREATE DATABASE IF NOT EXISTS apotek_parahyangan_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

echo.
echo Importing SQL file...
mysql -u root -h 127.0.0.1 apotek_parahyangan_db < "temp\apotek_parahyangan_db.sql"

if %errorlevel% neq 0 (
    echo ERROR: SQL import failed
    pause
    exit /b 1
)

echo.
echo SUCCESS: SQL import completed!
echo.

echo Showing tables in database...
mysql -u root -h 127.0.0.1 apotek_parahyangan_db -e "SHOW TABLES;"

echo.
echo Import completed successfully!
pause