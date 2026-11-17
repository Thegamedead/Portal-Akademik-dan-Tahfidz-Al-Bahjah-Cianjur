@echo off
title DATABASE BACKUP - Sistem Informasi Tahfidz SMP
echo.
echo =========================================
echo   DATABASE BACKUP UTILITY
echo   Sistem Informasi Tahfidz SMP
echo =========================================
echo.

REM Set variables from .env file
set DB_NAME=tahfidz_absensi_test
set DB_USER=root
set DB_PASSWORD=
set DB_HOST=127.0.0.1
set DB_PORT=3306

REM Create backup directory
set BACKUP_DIR=%~dp0backups
if not exist "%BACKUP_DIR%" mkdir "%BACKUP_DIR%"

REM Generate timestamp
for /f "tokens=2 delims==" %%a in ('wmic OS Get localdatetime /value') do set "dt=%%a"
set "YY=%dt:~2,2%" & set "YYYY=%dt:~0,4%" & set "MM=%dt:~4,2%" & set "DD=%dt:~6,2%"
set "HH=%dt:~8,2%" & set "Min=%dt:~10,2%" & set "Sec=%dt:~12,2%"
set "timestamp=%YYYY%%MM%%DD%_%HH%%Min%%Sec%"

REM Set backup filename
set BACKUP_FILE=%BACKUP_DIR%\tahfidz_backup_%timestamp%.sql

echo Starting database backup...
echo Database: %DB_NAME%
echo Backup file: %BACKUP_FILE%
echo.

REM Find MySQL path (try common Laragon locations)
set MYSQL_PATH=
if exist "C:\laragon\bin\mysql\mysql-8.4.3-winx64\bin\mysqldump.exe" set MYSQL_PATH=C:\laragon\bin\mysql\mysql-8.4.3-winx64\bin
if exist "C:\laragon\bin\mysql\mysql-8.0.30\bin\mysqldump.exe" set MYSQL_PATH=C:\laragon\bin\mysql\mysql-8.0.30\bin
if exist "C:\laragon\bin\mysql\mysql-8.0.33\bin\mysqldump.exe" set MYSQL_PATH=C:\laragon\bin\mysql\mysql-8.0.33\bin
if exist "C:\laragon\bin\mysql\mysql-8.2.0\bin\mysqldump.exe" set MYSQL_PATH=C:\laragon\bin\mysql\mysql-8.2.0\bin

REM Check if MySQL path found
if "%MYSQL_PATH%"=="" (
    echo ERROR: MySQL not found in Laragon directories!
    echo Please check your Laragon installation.
    echo Looking for mysqldump.exe in:
    echo   C:\laragon\bin\mysql\mysql-X.X.XX\bin\
    echo.
    pause
    exit /b 1
)

echo Found MySQL at: %MYSQL_PATH%
echo.

REM Execute backup with error handling
echo Executing backup command...
"%MYSQL_PATH%\mysqldump.exe" --host=%DB_HOST% --port=%DB_PORT% --user=%DB_USER% --password=%DB_PASSWORD% --single-transaction --routines --triggers --complete-insert --extended-insert --comments --hex-blob %DB_NAME% > "%BACKUP_FILE%" 2>&1

REM Check if backup was successful
if %ERRORLEVEL% EQU 0 (
    echo.
    echo ========================================
    echo   BACKUP SUCCESSFUL!
    echo ========================================
    echo.
    echo Backup file created: %BACKUP_FILE%

    REM Get file size
    for %%A in ("%BACKUP_FILE%") do set FILESIZE=%%~zA
    echo File size: %FILESIZE% bytes

    if %FILESIZE% GTR 1000 (
        echo Status: SUCCESS - Database backup completed
        echo.
        echo IMPORTANT:
        echo 1. Copy this file to external drive/cloud storage
        echo 2. Test the backup by importing to another database
        echo 3. Keep multiple backup copies for safety
    ) else (
        echo WARNING: Backup file seems too small ^(%FILESIZE% bytes^)
        echo This might indicate an empty database or backup error
        echo Please check the database content manually
    )
) else (
    echo.
    echo ========================================
    echo   BACKUP FAILED!
    echo ========================================
    echo.
    echo Error code: %ERRORLEVEL%
    echo Please check:
    echo 1. Is MySQL/Laragon running?
    echo 2. Does database '%DB_NAME%' exist?
    echo 3. Are database credentials correct?
    echo.
    echo Try manual backup using phpMyAdmin instead
)

echo.
echo Backup location: %BACKUP_DIR%
echo.
pause
