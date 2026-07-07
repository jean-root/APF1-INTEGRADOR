-- ============================================================
--  MIGRACIÓN 003 — Bitácora de auditoría (HU-25)
--  Registra creación/edición/eliminación de propiedades,
--  vendedores y usuarios. Cierra la referencia a "log_acciones"
--  ya mencionada en el Capítulo VIII del informe (Tabla 8.3).
-- ============================================================

CREATE TABLE IF NOT EXISTS `log_acciones` (
  `id`             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `usuario_id`     INT UNSIGNED NULL,
  `usuario_nombre` VARCHAR(100) NOT NULL,
  `accion`         ENUM('crear','editar','eliminar') NOT NULL,
  `entidad`        VARCHAR(50) NOT NULL,
  `entidad_id`     INT UNSIGNED NULL,
  `detalle`        VARCHAR(255) NULL,
  `ip`             VARCHAR(45) NULL,
  `created_at`     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`usuario_id`) REFERENCES `usuarios`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE INDEX `idx_log_acciones_created_at` ON `log_acciones` (`created_at`);
CREATE INDEX `idx_log_acciones_entidad`    ON `log_acciones` (`entidad`);
