<?php
// ============================================================
//  MODEL: Auditoria — registro generado por triggers de MySQL
//  (tabla auditoria, migración 005). Complementa a LogAccion.
// ============================================================
require_once APP_ROOT . '/core/Model.php';

class Auditoria extends Model {
    protected string $table = 'auditoria';

    // Últimos N registros generados por los triggers, más recientes primero.
    public function recientes(int $limit = 100): array {
        $sql = "SELECT * FROM auditoria ORDER BY fecha_hora DESC LIMIT ?";
        return $this->raw($sql, [$limit]);
    }

    // Historial (a nivel de BD) de un registro puntual de una tabla.
    public function historialDeRegistro(string $tabla, int $registroId): array {
        $sql = "SELECT * FROM auditoria WHERE tabla_afectada = ? AND registro_id = ? ORDER BY fecha_hora ASC";
        return $this->raw($sql, [$tabla, $registroId]);
    }
}
