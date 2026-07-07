<?php
// ============================================================
//  MODEL: LogAccion — Bitácora de auditoría (HU-25)
// ============================================================
require_once APP_ROOT . '/core/Model.php';

class LogAccion extends Model {
    protected string $table = 'log_acciones';

    // Registra una acción (crear/editar/eliminar) sobre una entidad.
    // Nunca interrumpe el flujo del controlador si falla (best-effort).
    public static function registrar(string $accion, string $entidad, ?int $entidadId, string $detalle = ''): void {
        try {
            $log = new self();
            $log->insert([
                'usuario_id'     => $_SESSION['usuario_id'] ?? null,
                'usuario_nombre' => $_SESSION['usuario_nombre'] ?? 'desconocido',
                'accion'         => $accion,
                'entidad'        => $entidad,
                'entidad_id'     => $entidadId,
                'detalle'        => $detalle !== '' ? substr($detalle, 0, 255) : null,
                'ip'             => $_SERVER['REMOTE_ADDR'] ?? null,
            ]);
        } catch (Throwable $e) {
            error_log('[LogAccion] No se pudo registrar la acción: ' . $e->getMessage());
        }
    }

    // Últimos N registros con datos legibles, más recientes primero.
    public function recientes(int $limit = 100): array {
        $sql = "SELECT * FROM log_acciones ORDER BY created_at DESC LIMIT ?";
        return $this->raw($sql, [$limit]);
    }

    // Historial de una entidad puntual (ej. un mensaje/lead), orden cronológico
    // ascendente — da la "trazabilidad de actividades" observada por el docente.
    public function historialDeEntidad(string $entidad, int $entidadId): array {
        $sql = "SELECT * FROM log_acciones WHERE entidad = ? AND entidad_id = ? ORDER BY created_at ASC";
        return $this->raw($sql, [$entidad, $entidadId]);
    }
}
