<?php
// ============================================================
//  MODEL – Active Record Base
//  Todos los modelos extienden esta clase.
//  Usa PDO con Prepared Statements para prevenir SQL Injection (R1).
// ============================================================
abstract class Model {

    protected Database $db;
    protected string   $table  = '';   // name de la tabla en MySQL
    protected string   $pk     = 'id'; // Primary key (por defecto 'id')

    public function __construct() {
        $this->db = Database::getInstance();
    }

    // ----------------------------------------------------------
    //  SELECT – Obtener todos los registros
    // ----------------------------------------------------------
    public function findAll(string $orderBy = 'id DESC'): array {
        $sql  = "SELECT * FROM `{$this->table}` ORDER BY {$orderBy}";
        return $this->db->query($sql)->fetchAll();
    }

    // ----------------------------------------------------------
    //  SELECT – Obtener un registro por PK
    // ----------------------------------------------------------
    public function findById(int $id): object|false {
        $sql = "SELECT * FROM `{$this->table}` WHERE `{$this->pk}` = ? LIMIT 1";
        return $this->db->query($sql, [$id])->fetch();
    }

    // ----------------------------------------------------------
    //  SELECT – Buscar por columna/valor con JOIN opcional
    // ----------------------------------------------------------
    public function findWhere(string $column, mixed $value): array {
        $sql = "SELECT * FROM `{$this->table}` WHERE `{$column}` = ?";
        return $this->db->query($sql, [$value])->fetchAll();
    }

    public function findOneWhere(string $column, mixed $value): object|false {
        $sql = "SELECT * FROM `{$this->table}` WHERE `{$column}` = ? LIMIT 1";
        return $this->db->query($sql, [$value])->fetch();
    }

    // ----------------------------------------------------------
    //  INSERT – Insertar un nuevo registro
    //  $data = ['campo' => 'valor', ...]
    // ----------------------------------------------------------
    public function insert(array $data): string {
        $cols   = implode('`, `', array_keys($data));
        $marks  = implode(', ', array_fill(0, count($data), '?'));
        $sql    = "INSERT INTO `{$this->table}` (`{$cols}`) VALUES ({$marks})";
        $this->db->query($sql, array_values($data));
        return $this->db->lastInsertId();
    }

    // ----------------------------------------------------------
    //  UPDATE – Actualizar registro por PK
    // ----------------------------------------------------------
    public function update(int $id, array $data): bool {
        $setParts = array_map(fn($col) => "`{$col}` = ?", array_keys($data));
        $setStr   = implode(', ', $setParts);
        $sql      = "UPDATE `{$this->table}` SET {$setStr} WHERE `{$this->pk}` = ?";
        $params   = array_merge(array_values($data), [$id]);
        $this->db->query($sql, $params);
        return true;
    }

    // ----------------------------------------------------------
    //  DELETE – Eliminar registro por PK
    // ----------------------------------------------------------
    public function delete(int $id): bool {
        $sql = "DELETE FROM `{$this->table}` WHERE `{$this->pk}` = ?";
        $this->db->query($sql, [$id]);
        return true;
    }

    // ----------------------------------------------------------
    //  COUNT – Contar registros
    // ----------------------------------------------------------
    public function count(string $where = '', array $params = []): int {
        $sql  = "SELECT COUNT(*) as total FROM `{$this->table}`";
        $sql .= $where ? " WHERE {$where}" : '';
        return (int) $this->db->query($sql, $params)->fetch()->total;
    }

    // ----------------------------------------------------------
    //  RAW – Query personalizado (para JOINs, etc.)
    // ----------------------------------------------------------
    public function raw(string $sql, array $params = []): array {
        return $this->db->query($sql, $params)->fetchAll();
    }

    public function rawOne(string $sql, array $params = []): object|false {
        return $this->db->query($sql, $params)->fetch();
    }
}
