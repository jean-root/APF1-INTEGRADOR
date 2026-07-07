#!/bin/bash
# ============================================================
#  BACKUP DIARIO DE BASE DE DATOS (CS-10) — entorno Linux/CleverCloud
#  Genera bienes_raices_YYYYMMDD_HHMMSS.sql.gz
#
#  Programación: CleverCloud no ejecuta cron por defecto en el plan
#  base; para automatizar esto en producción hay que usar el add-on
#  "Cron" de CleverCloud (o un cron externo, ej. cron-job.org, que
#  llame a un endpoint protegido que dispare este script).
#  Mientras tanto, se puede correr manualmente vía consola SSH.
# ============================================================
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_DIR="$(dirname "$SCRIPT_DIR")"
ENV_FILE="$PROJECT_DIR/.env"
BACKUP_DIR="$PROJECT_DIR/backups"

mkdir -p "$BACKUP_DIR"

# Cargar variables del .env (o del entorno real si ya existen, ej. CleverCloud)
if [ -f "$ENV_FILE" ]; then
  set -a
  # shellcheck disable=SC1090
  source <(grep -v '^#' "$ENV_FILE" | sed 's/\r$//')
  set +a
fi

DB_NAME="${DB_NAME:-bienes_raices}"
DB_USER="${DB_USER:-root}"
DB_PASS="${DB_PASS:-}"
DB_HOST="${DB_HOST:-localhost}"

TIMESTAMP=$(date +%Y%m%d_%H%M%S)
FILENAME="${DB_NAME}_${TIMESTAMP}.sql.gz"

mysqldump -h "$DB_HOST" -u "$DB_USER" ${DB_PASS:+-p"$DB_PASS"} "$DB_NAME" | gzip > "$BACKUP_DIR/$FILENAME"

# Retener solo los últimos 14 backups
ls -1t "$BACKUP_DIR"/"${DB_NAME}"_*.sql.gz 2>/dev/null | tail -n +15 | xargs -r rm --

echo "Backup creado en: $BACKUP_DIR/$FILENAME"
