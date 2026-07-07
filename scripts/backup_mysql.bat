@echo off
setlocal

rem ============================================================
rem  BACKUP DIARIO DE BASE DE DATOS (CS-10)
rem  Uso manual: doble clic o "scripts\backup_mysql.bat"
rem  Uso automatico: registrar en el Programador de tareas de Windows
rem  para que corra 1 vez al dia (ej. 2:00 a.m.).
rem  Lee las credenciales del .env del proyecto (no hardcodeadas).
rem ============================================================

set MYSQL_BIN=C:\xampp\mysql\bin
set PROJECT_DIR=%~dp0..
set BACKUP_DIR=%PROJECT_DIR%\backups
set ENV_FILE=%PROJECT_DIR%\.env

if not exist "%BACKUP_DIR%" mkdir "%BACKUP_DIR%"

rem --- Leer DB_NAME / DB_USER / DB_PASS desde .env ---
set DB_NAME=bienes_raices
set DB_USER=root
set DB_PASS=

for /f "usebackq tokens=1,* delims==" %%A in ("%ENV_FILE%") do (
  if "%%A"=="DB_NAME" set DB_NAME=%%B
  if "%%A"=="DB_USER" set DB_USER=%%B
  if "%%A"=="DB_PASS" set DB_PASS=%%B
)

for /f %%i in ('powershell -NoProfile -Command "Get-Date -Format yyyyMMdd_HHmmss"') do set TS=%%i
set SQL_FILE=%BACKUP_DIR%\%DB_NAME%_%TS%.sql
set ZIP_FILE=%BACKUP_DIR%\%DB_NAME%_%TS%.zip

if "%DB_PASS%"=="" (
  "%MYSQL_BIN%\mysqldump.exe" -u%DB_USER% %DB_NAME% > "%SQL_FILE%"
) else (
  "%MYSQL_BIN%\mysqldump.exe" -u%DB_USER% -p%DB_PASS% %DB_NAME% > "%SQL_FILE%"
)

rem --- Comprimir (Windows no trae gzip nativo; usamos zip vía PowerShell) ---
powershell -NoProfile -Command "Compress-Archive -Path '%SQL_FILE%' -DestinationPath '%ZIP_FILE%' -Force"
del "%SQL_FILE%"

rem --- Retener solo los ultimos 14 backups ---
powershell -NoProfile -Command ^
  "Get-ChildItem '%BACKUP_DIR%\%DB_NAME%_*.zip' | Sort-Object LastWriteTime -Descending | Select-Object -Skip 14 | Remove-Item -Force"

echo Backup creado en: %ZIP_FILE%
endlocal
