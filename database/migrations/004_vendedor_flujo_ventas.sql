-- ============================================================
--  MIGRACIÓN 004 — Flujo de vendedor y pipeline de ventas
--  Cierra la observación del docente en APF3 (nota 13/20):
--  "No se incluyen flujos de vendedor ni trazabilidad sobre las
--  actividades que este debe realizar. El sistema no da soporte
--  al flujo de ventas ni a lo esperado en los formularios de
--  contacto."
--
--  Agrega:
--   1) rol 'vendedor' en usuarios (login propio para vendedores)
--   2) vínculo vendedores.usuario_id -> usuarios.id
--   3) pipeline de estados en mensajes (nuevo/contactado/
--      visita_agendada/cerrado/perdido) + asignación a vendedor
-- ============================================================

ALTER TABLE `usuarios`
  MODIFY COLUMN `rol` ENUM('admin','supervisor','vendedor') NOT NULL DEFAULT 'supervisor';

ALTER TABLE `vendedores`
  ADD COLUMN `usuario_id` INT UNSIGNED NULL AFTER `comision`,
  ADD CONSTRAINT `fk_vendedores_usuario`
    FOREIGN KEY (`usuario_id`) REFERENCES `usuarios`(`id`) ON DELETE SET NULL;

ALTER TABLE `mensajes`
  ADD COLUMN `vendedor_id` INT UNSIGNED NULL AFTER `telefono`,
  ADD COLUMN `estado` ENUM('nuevo','contactado','visita_agendada','cerrado','perdido')
    NOT NULL DEFAULT 'nuevo' AFTER `leido`,
  ADD CONSTRAINT `fk_mensajes_vendedor`
    FOREIGN KEY (`vendedor_id`) REFERENCES `vendedores`(`id`) ON DELETE SET NULL;

CREATE INDEX `idx_vendedores_usuario`  ON `vendedores` (`usuario_id`);
CREATE INDEX `idx_mensajes_vendedor`   ON `mensajes` (`vendedor_id`);
CREATE INDEX `idx_mensajes_estado`     ON `mensajes` (`estado`);
