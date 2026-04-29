@echo off
setlocal

rem Configuracion basica
set MYSQL_BIN=C:\xampp\mysql\bin
set DB_NAME=bienes_raices
set DB_USER=root
set DB_PASS=
set BACKUP_DIR=%~dp0..\backups

if not exist "%BACKUP_DIR%" mkdir "%BACKUP_DIR%"

for /f %%i in ('powershell -NoProfile -Command "Get-Date -Format yyyyMMdd_HHmmss"') do set TS=%%i
set BACKUP_FILE=%BACKUP_DIR%\%DB_NAME%_%TS%.sql

if "%DB_PASS%"=="" (
  "%MYSQL_BIN%\mysqldump.exe" -u%DB_USER% %DB_NAME% > "%BACKUP_FILE%"
) else (
  "%MYSQL_BIN%\mysqldump.exe" -u%DB_USER% -p%DB_PASS% %DB_NAME% > "%BACKUP_FILE%"
)

echo Backup creado en: %BACKUP_FILE%
endlocal
