@echo off
echo ===============================================
echo    SQL DATABASE IMPORT SCRIPT
echo ===============================================
echo.

echo [1/4] Testing MySQL connection...
mysql -u root -h 127.0.0.1 -e "SELECT 'Connection successful' as status;" 2>nul
if %errorlevel% neq 0 (
    echo ERROR: Cannot connect to MySQL server
    echo Please make sure MySQL is running and accessible
    pause
    exit /b 1
)
echo ✅ MySQL connection successful

echo.
echo [2/4] Creating database if not exists...
mysql -u root -h 127.0.0.1 -e "CREATE DATABASE IF NOT EXISTS apotek_parahyangan_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" 2>nul
if %errorlevel% neq 0 (
    echo ERROR: Cannot create database
    pause
    exit /b 1
)
echo ✅ Database apotek_parahyangan_db ready

echo.
echo [3/4] Importing SQL file...
echo This may take a few minutes depending on file size...
mysql -u root -h 127.0.0.1 apotek_parahyangan_db < "temp\apotek_parahyangan_db.sql" 2>nul
if %errorlevel% neq 0 (
    echo ERROR: SQL import failed
    echo Please check the SQL file format and content
    pause
    exit /b 1
)
echo ✅ SQL file imported successfully

echo.
echo [4/4] Showing database tables and record counts...
echo.
echo ===============================================
echo    DATABASE TABLES AND RECORD COUNTS
echo ===============================================

mysql -u root -h 127.0.0.1 apotek_parahyangan_db -e "SELECT TABLE_NAME as 'Table Name', TABLE_ROWS as 'Estimated Rows' FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = 'apotek_parahyangan_db' ORDER BY TABLE_NAME;" 2>nul

echo.
echo ===============================================
echo    IMPORT COMPLETED SUCCESSFULLY!
echo ===============================================
echo.
echo Database: apotek_parahyangan_db
echo Host: 127.0.0.1:3306
echo Username: root
echo.
pause