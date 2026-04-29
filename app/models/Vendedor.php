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
}
