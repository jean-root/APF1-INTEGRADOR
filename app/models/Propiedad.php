<?php
// ============================================================
//  MODEL: Propiedad
// ============================================================
require_once APP_ROOT . '/core/Model.php';

class Propiedad extends Model {
    protected string $table = 'propiedades';

    // Propiedades activas con datos del vendedor (JOIN)
    public function todasActivas(): array {
        $sql = "SELECT p.*, v.nombre AS vendedor_nombre, v.apellido AS vendedor_apellido,
                       v.telefono AS vendedor_telefono, v.foto AS vendedor_foto
                FROM propiedades p
                LEFT JOIN vendedores v ON p.vendedor_id = v.id
                WHERE p.activo = 1
                ORDER BY p.created_at DESC";
        return $this->raw($sql);
    }

    // Detalle de una propiedad con vendedor
    public function detalleConVendedor(int $id): object|false {
        $sql = "SELECT p.*, v.nombre AS vendedor_nombre, v.apellido AS vendedor_apellido,
                       v.telefono AS vendedor_telefono, v.email AS vendedor_email, v.foto AS vendedor_foto
                FROM propiedades p
                LEFT JOIN vendedores v ON p.vendedor_id = v.id
                WHERE p.id = ? LIMIT 1";
        return $this->rawOne($sql, [$id]);
    }

    // Propiedades por tipo
    public function porTipo(string $tipo): array {
        $sql = "SELECT p.*, v.nombre AS vendedor_nombre, v.apellido AS vendedor_apellido
                FROM propiedades p
                LEFT JOIN vendedores v ON p.vendedor_id = v.id
                WHERE p.activo = 1 AND p.tipo = ?
                ORDER BY p.created_at DESC";
        return $this->raw($sql, [$tipo]);
    }

    // Últimas N propiedades para el home
    public function ultimas(int $limit = 6): array {
        $sql = "SELECT p.*, v.nombre AS vendedor_nombre, v.apellido AS vendedor_apellido
                FROM propiedades p
                LEFT JOIN vendedores v ON p.vendedor_id = v.id
                WHERE p.activo = 1
                ORDER BY p.created_at DESC
                LIMIT ?";
        return $this->raw($sql, [$limit]);
    }

    // Subir imagen al servidor (previene R4)
    public function subirImagen(array $file): string|false {
        $permitidos = ['image/jpeg', 'image/png', 'image/webp'];
        $maxSize    = 5 * 1024 * 1024; // 5 MB

        // Validar tipo MIME real (no solo extensión)
        $finfo    = finfo_open(FILEINFO_MIME_TYPE);
        $mimeReal = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mimeReal, $permitidos)) return false;
        if ($file['size'] > $maxSize)          return false;

        $extension = match($mimeReal) {
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/webp' => 'webp',
        };

        $nombreArchivo = uniqid('prop_', true) . '.' . $extension;
        $destino       = UPLOAD_DIR . $nombreArchivo;

        if (!is_dir(UPLOAD_DIR)) {
            mkdir(UPLOAD_DIR, 0755, true);
        }

        if (move_uploaded_file($file['tmp_name'], $destino)) {
            return $nombreArchivo;
        }
        return false;
    }

    // Formatear precio en soles con separador de miles
    public static function formatearPrecio(float $precio): string {
        return 'S/ ' . number_format($precio, 0, '.', ',');
    }
}
