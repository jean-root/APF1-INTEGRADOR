@echo off
setlocal

rem Configuracion basica
set MYSQL_BIN=C:\xampp\mysql\bin
set DB_NAME=bienes_raices
set DB_USER=root
set DB_PASS=

if "%~1"=="" (
  echo Uso: restore_mysql.bat ruta\al\backup.sql
  exit /b 1
)

set BACKUP_FILE=%~1

if not exist "%BACKUP_FILE%" (
  echo El archivo no existe: %BACKUP_FILE%
  exit /b 1
)

if "%DB_PASS%"=="" (
  "%MYSQL_BIN%\mysql.exe" -u%DB_USER% %DB_NAME% < "%BACKUP_FILE%"
) else (
  "%MYSQL_BIN%\mysql.exe" -u%DB_USER% -p%DB_PASS% %DB_NAME% < "%BACKUP_FILE%"
)

echo Base restaurada desde: %BACKUP_FILE%
endlocal
