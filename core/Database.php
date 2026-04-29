<?php
// ============================================================
//  DATABASE – Conexión PDO Singleton con Sentencias Preparadas
// ============================================================
class Database {

    private static ?Database $instance = null;
    private PDO $pdo;

    private function __construct() {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        try {
            $this->pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            die('<div style="font-family:sans-serif;padding:2rem;background:#fee;color:#c00;border-radius:8px">
                <h3>⚠️ Error de conexión a la base de datos</h3>
                <p>' . $e->getMessage() . '</p>
                <p>Verifica que MySQL esté corriendo en XAMPP y que la base <strong>' . DB_NAME . '</strong> exista.</p>
                </div>');
        }
    }

    // Obtener la instancia única (Singleton)
    public static function getInstance(): Database {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    // Ejecutar query con parámetros (Prepared Statement)
    public function query(string $sql, array $params = []): PDOStatement {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public function lastInsertId(): string {
        return $this->pdo->lastInsertId();
    }

    // Evitar clonación y deserialización del Singleton
    private function __clone() {}
    public function __wakeup() { throw new \Exception("Cannot unserialize Singleton"); }
}
