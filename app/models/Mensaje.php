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
}
