-- ============================================================
--  MIGRACIÓN 005: Auditoría a nivel de base de datos (tabla auditoria + triggers)
--  ------------------------------------------------------------
--  Requisito de la Semana 18: "Auditoría (tabla auditor con trigger)".
--
--  Esta auditoría es COMPLEMENTARIA a la bitácora de aplicación
--  (tabla log_acciones, HU-25): log_acciones registra QUIÉN (usuario de
--  la sesión PHP) hizo la acción; esta tabla `auditoria` registra a
--  nivel de motor de base de datos CUALQUIER cambio de datos en las
--  tablas críticas (propiedades, vendedores, usuarios), incluso si
--  ocurriera fuera de la aplicación (por ejemplo, directo desde
--  phpMyAdmin), porque el trigger se dispara en el propio MySQL.
--
--  Ejecutar UNA VEZ sobre la base de datos existente (phpMyAdmin,
--  pestaña SQL, o cliente MySQL). Es seguro de re-ejecutar.
-- ============================================================

CREATE TABLE IF NOT EXISTS `auditoria` (
  `id`              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `tabla_afectada`  VARCHAR(50)  NOT NULL,
  `operacion`       ENUM('INSERT','UPDATE','DELETE') NOT NULL,
  `registro_id`     INT UNSIGNED NOT NULL,
  `datos_anteriores` JSON NULL,
  `datos_nuevos`     JSON NULL,
  `usuario_bd`      VARCHAR(150) NOT NULL,
  `fecha_hora`      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_auditoria_tabla` (`tabla_afectada`),
  INDEX `idx_auditoria_registro` (`registro_id`),
  INDEX `idx_auditoria_fecha` (`fecha_hora`)
) ENGINE=InnoDB;

-- ──────────────────────────────────────────
--  TRIGGERS: propiedades
-- ──────────────────────────────────────────
DROP TRIGGER IF EXISTS `trg_propiedades_after_insert`;
DROP TRIGGER IF EXISTS `trg_propiedades_after_update`;
DROP TRIGGER IF EXISTS `trg_propiedades_after_delete`;

DELIMITER $$

CREATE TRIGGER `trg_propiedades_after_insert`
AFTER INSERT ON `propiedades`
FOR EACH ROW
BEGIN
  INSERT INTO auditoria (tabla_afectada, operacion, registro_id, datos_anteriores, datos_nuevos, usuario_bd)
  VALUES ('propiedades', 'INSERT', NEW.id, NULL,
    JSON_OBJECT('titulo', NEW.titulo, 'precio', NEW.precio, 'tipo', NEW.tipo,
                 'vendedor_id', NEW.vendedor_id, 'activo', NEW.activo, 'destacado', NEW.destacado),
    CURRENT_USER());
END$$

CREATE TRIGGER `trg_propiedades_after_update`
AFTER UPDATE ON `propiedades`
FOR EACH ROW
BEGIN
  INSERT INTO auditoria (tabla_afectada, operacion, registro_id, datos_anteriores, datos_nuevos, usuario_bd)
  VALUES ('propiedades', 'UPDATE', NEW.id,
    JSON_OBJECT('titulo', OLD.titulo, 'precio', OLD.precio, 'tipo', OLD.tipo,
                 'vendedor_id', OLD.vendedor_id, 'activo', OLD.activo, 'destacado', OLD.destacado),
    JSON_OBJECT('titulo', NEW.titulo, 'precio', NEW.precio, 'tipo', NEW.tipo,
                 'vendedor_id', NEW.vendedor_id, 'activo', NEW.activo, 'destacado', NEW.destacado),
    CURRENT_USER());
END$$

CREATE TRIGGER `trg_propiedades_after_delete`
AFTER DELETE ON `propiedades`
FOR EACH ROW
BEGIN
  INSERT INTO auditoria (tabla_afectada, operacion, registro_id, datos_anteriores, datos_nuevos, usuario_bd)
  VALUES ('propiedades', 'DELETE', OLD.id,
    JSON_OBJECT('titulo', OLD.titulo, 'precio', OLD.precio, 'tipo', OLD.tipo,
                 'vendedor_id', OLD.vendedor_id, 'activo', OLD.activo, 'destacado', OLD.destacado),
    NULL, CURRENT_USER());
END$$

DELIMITER ;

-- ──────────────────────────────────────────
--  TRIGGERS: vendedores
-- ──────────────────────────────────────────
DROP TRIGGER IF EXISTS `trg_vendedores_after_insert`;
DROP TRIGGER IF EXISTS `trg_vendedores_after_update`;
DROP TRIGGER IF EXISTS `trg_vendedores_after_delete`;

DELIMITER $$

CREATE TRIGGER `trg_vendedores_after_insert`
AFTER INSERT ON `vendedores`
FOR EACH ROW
BEGIN
  INSERT INTO auditoria (tabla_afectada, operacion, registro_id, datos_anteriores, datos_nuevos, usuario_bd)
  VALUES ('vendedores', 'INSERT', NEW.id, NULL,
    JSON_OBJECT('nombre', NEW.nombre, 'apellido', NEW.apellido, 'email', NEW.email,
                 'zona', NEW.zona, 'comision', NEW.comision, 'usuario_id', NEW.usuario_id),
    CURRENT_USER());
END$$

CREATE TRIGGER `trg_vendedores_after_update`
AFTER UPDATE ON `vendedores`
FOR EACH ROW
BEGIN
  INSERT INTO auditoria (tabla_afectada, operacion, registro_id, datos_anteriores, datos_nuevos, usuario_bd)
  VALUES ('vendedores', 'UPDATE', NEW.id,
    JSON_OBJECT('nombre', OLD.nombre, 'apellido', OLD.apellido, 'email', OLD.email,
                 'zona', OLD.zona, 'comision', OLD.comision, 'usuario_id', OLD.usuario_id),
    JSON_OBJECT('nombre', NEW.nombre, 'apellido', NEW.apellido, 'email', NEW.email,
                 'zona', NEW.zona, 'comision', NEW.comision, 'usuario_id', NEW.usuario_id),
    CURRENT_USER());
END$$

CREATE TRIGGER `trg_vendedores_after_delete`
AFTER DELETE ON `vendedores`
FOR EACH ROW
BEGIN
  INSERT INTO auditoria (tabla_afectada, operacion, registro_id, datos_anteriores, datos_nuevos, usuario_bd)
  VALUES ('vendedores', 'DELETE', OLD.id,
    JSON_OBJECT('nombre', OLD.nombre, 'apellido', OLD.apellido, 'email', OLD.email,
                 'zona', OLD.zona, 'comision', OLD.comision, 'usuario_id', OLD.usuario_id),
    NULL, CURRENT_USER());
END$$

DELIMITER ;

-- ──────────────────────────────────────────
--  TRIGGERS: usuarios (no se audita la columna password; solo se
--  registra que cambió, para no exponer ni siquiera el hash bcrypt)
-- ──────────────────────────────────────────
DROP TRIGGER IF EXISTS `trg_usuarios_after_insert`;
DROP TRIGGER IF EXISTS `trg_usuarios_after_update`;
DROP TRIGGER IF EXISTS `trg_usuarios_after_delete`;

DELIMITER $$

CREATE TRIGGER `trg_usuarios_after_insert`
AFTER INSERT ON `usuarios`
FOR EACH ROW
BEGIN
  INSERT INTO auditoria (tabla_afectada, operacion, registro_id, datos_anteriores, datos_nuevos, usuario_bd)
  VALUES ('usuarios', 'INSERT', NEW.id, NULL,
    JSON_OBJECT('nombre', NEW.nombre, 'email', NEW.email, 'rol', NEW.rol, 'estado', NEW.estado),
    CURRENT_USER());
END$$

CREATE TRIGGER `trg_usuarios_after_update`
AFTER UPDATE ON `usuarios`
FOR EACH ROW
BEGIN
  INSERT INTO auditoria (tabla_afectada, operacion, registro_id, datos_anteriores, datos_nuevos, usuario_bd)
  VALUES ('usuarios', 'UPDATE', NEW.id,
    JSON_OBJECT('nombre', OLD.nombre, 'email', OLD.email, 'rol', OLD.rol, 'estado', OLD.estado,
                 'password_cambiado', IF(OLD.password <> NEW.password, TRUE, FALSE)),
    JSON_OBJECT('nombre', NEW.nombre, 'email', NEW.email, 'rol', NEW.rol, 'estado', NEW.estado,
                 'password_cambiado', IF(OLD.password <> NEW.password, TRUE, FALSE)),
    CURRENT_USER());
END$$

CREATE TRIGGER `trg_usuarios_after_delete`
AFTER DELETE ON `usuarios`
FOR EACH ROW
BEGIN
  INSERT INTO auditoria (tabla_afectada, operacion, registro_id, datos_anteriores, datos_nuevos, usuario_bd)
  VALUES ('usuarios', 'DELETE', OLD.id,
    JSON_OBJECT('nombre', OLD.nombre, 'email', OLD.email, 'rol', OLD.rol, 'estado', OLD.estado),
    NULL, CURRENT_USER());
END$$

DELIMITER ;
