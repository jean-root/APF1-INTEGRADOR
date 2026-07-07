<?php
// ============================================================
//  MODEL: Vendedor
// ============================================================
require_once APP_ROOT . '/core/Model.php';

class Vendedor extends Model {
    protected string $table = 'vendedores';

    // Obtener vendedores para selects
    public function listaParaSelect(): array {
        return $this->findAll('nombre ASC');
    }

    // Top N vendedores por cantidad de propiedades activas asignadas (para gráfica del dashboard, HU-26)
    public function topPorPropiedades(int $limit = 5): array {
        $sql = "SELECT v.nombre, v.apellido, COUNT(p.id) AS total
                FROM vendedores v
                LEFT JOIN propiedades p ON p.vendedor_id = v.id AND p.activo = 1
                GROUP BY v.id, v.nombre, v.apellido
                ORDER BY total DESC
                LIMIT ?";
        return $this->raw($sql, [$limit]);
    }

    // Buscar el vendedor vinculado a una cuenta de usuario (login propio, HU-27)
    public function porUsuarioId(int $usuarioId): object|false {
        return $this->findOneWhere('usuario_id', $usuarioId);
    }
}
