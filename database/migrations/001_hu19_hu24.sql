-- ============================================================
--  MIGRACIÓN: HU-19 (zona/comisión de vendedor) y HU-24 (propiedad destacada)
--  Ejecutar UNA VEZ sobre la base de datos existente (phpMyAdmin o cliente MySQL).
--  Es seguro de re-ejecutar gracias a los checks de IF NOT EXISTS / nombres únicos.
-- ============================================================

ALTER TABLE `vendedores`
  ADD COLUMN IF NOT EXISTS `zona` VARCHAR(100) NULL AFTER `telefono`,
  ADD COLUMN IF NOT EXISTS `comision` DECIMAL(5,2) NULL DEFAULT 3.00 AFTER `zona`;

ALTER TABLE `propiedades`
  ADD COLUMN IF NOT EXISTS `destacado` TINYINT(1) NOT NULL DEFAULT 0 AFTER `activo`;

CREATE INDEX IF NOT EXISTS `idx_propiedades_destacado` ON `propiedades` (`destacado`);
