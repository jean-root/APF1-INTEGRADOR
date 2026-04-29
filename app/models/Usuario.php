<?php
// ============================================================
//  MODEL: Usuario
// ============================================================
require_once APP_ROOT . '/core/Model.php';

class Usuario extends Model {
    protected string $table = 'usuarios';

    // Buscar usuario por email (para el login)
    public function findByEmail(string $email): object|false {
        return $this->findOneWhere('email', $email);
    }

    // Verificar contraseña con bcrypt
    public function verifyPassword(string $input, string $hash): bool {
        return password_verify($input, $hash);
    }
}
