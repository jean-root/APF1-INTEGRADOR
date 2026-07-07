<?php
// ============================================================
//  MODEL: Mensaje (formulario de contacto)
// ============================================================
require_once APP_ROOT . '/core/Model.php';

class Mensaje extends Model {
    protected string $table = 'mensajes';

    public function noLeidos(): int {
        return $this->count('leido = 0');
    }

    public function marcarLeido(int $id): bool {
        return $this->update($id, ['leido' => 1]);
    }

    // Cantidad de mensajes por mes, agrupados como 'YYYY-MM' (para gráfica del dashboard, HU-26)
    public function contarPorMes(): array {
        $sql = "SELECT DATE_FORMAT(created_at, '%Y-%m') AS mes, COUNT(*) AS total
                FROM mensajes
                GROUP BY mes
                ORDER BY mes ASC";
        return $this->raw($sql);
    }

    // ── Pipeline de ventas (HU-28) ──────────────────────────────
    // Estados válidos del flujo de un lead, en orden de avance.
    public static function estadosValidos(): array {
        return ['nuevo', 'contactado', 'visita_agendada', 'cerrado', 'perdido'];
    }

    public static function etiquetaEstado(string $estado): string {
        return match ($estado) {
            'nuevo'            => 'Nuevo',
            'contactado'       => 'Contactado',
            'visita_agendada'  => 'Visita agendada',
            'cerrado'          => 'Cerrado (venta)',
            'perdido'          => 'Perdido',
            default            => ucfirst($estado),
        };
    }

    // Asignar un lead a un vendedor (lo hace el supervisor desde la bandeja)
    public function asignar(int $id, int $vendedorId): bool {
        return $this->update($id, ['vendedor_id' => $vendedorId]);
    }

    // Cambiar el estado del pipeline (lo hace el vendedor desde su panel)
    public function cambiarEstado(int $id, string $estado): bool {
        if (!in_array($estado, self::estadosValidos(), true)) {
            return false;
        }
        return $this->update($id, ['estado' => $estado]);
    }

    // Leads asignados a un vendedor específico (para su panel, HU-27)
    public function porVendedor(int $vendedorId): array {
        $sql = "SELECT * FROM mensajes WHERE vendedor_id = ? ORDER BY created_at DESC";
        return $this->raw($sql, [$vendedorId]);
    }

    // Mensajes con datos del vendedor asignado (para la bandeja del supervisor)
    public function todosConVendedor(): array {
        $sql = "SELECT m.*, v.nombre AS vendedor_nombre, v.apellido AS vendedor_apellido
                FROM mensajes m
                LEFT JOIN vendedores v ON m.vendedor_id = v.id
                ORDER BY m.created_at DESC";
        return $this->raw($sql);
    }
}
