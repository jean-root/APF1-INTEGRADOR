-- ============================================================
--  BASE DE DATOS: bienes_raices
--  Proyecto: Plataforma "Hogar Ideal Perú"
--  Importar en phpMyAdmin o ejecutar en MySQL CLI
-- ============================================================

CREATE DATABASE IF NOT EXISTS `bienes_raices`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `bienes_raices`;

-- ──────────────────────────────────────────
--  TABLA: usuarios (administradores del panel)
-- ──────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `nombre`     VARCHAR(100)  NOT NULL,
  `email`      VARCHAR(150)  NOT NULL UNIQUE,
  `password`   VARCHAR(255)  NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ──────────────────────────────────────────
--  TABLA: vendedores (personal de ventas)
-- ──────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `vendedores` (
  `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `nombre`     VARCHAR(100) NOT NULL,
  `apellido`   VARCHAR(100) NOT NULL,
  `email`      VARCHAR(150) NOT NULL UNIQUE,
  `telefono`   VARCHAR(20),
  `foto`       VARCHAR(255) DEFAULT 'default.jpg',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ──────────────────────────────────────────
--  TABLA: propiedades
-- ──────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `propiedades` (
  `id`               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `titulo`           VARCHAR(200)  NOT NULL,
  `descripcion`      TEXT,
  `precio`           DECIMAL(12,2) NOT NULL,
  `tipo`             ENUM('casa','departamento','terreno','local') NOT NULL DEFAULT 'casa',
  `habitaciones`     TINYINT UNSIGNED DEFAULT 0,
  `banos`            TINYINT UNSIGNED DEFAULT 0,
  `estacionamientos` TINYINT UNSIGNED DEFAULT 0,
  `metros2`          DECIMAL(8,2),
  `direccion`        VARCHAR(255),
  `imagen`           VARCHAR(255) DEFAULT 'no-imagen.jpg',
  `vendedor_id`      INT UNSIGNED,
  `activo`           TINYINT(1) DEFAULT 1,
  `created_at`       TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`vendedor_id`) REFERENCES `vendedores`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ──────────────────────────────────────────
--  TABLA: mensajes (formulario de contacto)
-- ──────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `mensajes` (
  `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `nombre`     VARCHAR(100) NOT NULL,
  `email`      VARCHAR(150) NOT NULL,
  `telefono`   VARCHAR(20),
  `asunto`     VARCHAR(200),
  `mensaje`    TEXT NOT NULL,
  `leido`      TINYINT(1) DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ──────────────────────────────────────────
--  INDICES DE OPTIMIZACION (consultas frecuentes)
-- ──────────────────────────────────────────
CREATE INDEX `idx_propiedades_vendedor`   ON `propiedades` (`vendedor_id`);
CREATE INDEX `idx_propiedades_tipo`       ON `propiedades` (`tipo`);
CREATE INDEX `idx_propiedades_activo`     ON `propiedades` (`activo`);
CREATE INDEX `idx_propiedades_created_at` ON `propiedades` (`created_at`);

CREATE INDEX `idx_mensajes_leido`         ON `mensajes` (`leido`);
CREATE INDEX `idx_mensajes_created_at`    ON `mensajes` (`created_at`);
CREATE INDEX `idx_mensajes_email`         ON `mensajes` (`email`);

-- ──────────────────────────────────────────
--  SEGURIDAD (opcional): usuario de aplicacion
--  Ejecutar con un usuario administrador de MySQL
-- ──────────────────────────────────────────
-- CREATE USER 'hogar_app'@'localhost' IDENTIFIED BY 'Cambia_Esta_Clave';
-- GRANT SELECT, INSERT, UPDATE, DELETE ON `bienes_raices`.* TO 'hogar_app'@'localhost';
-- FLUSH PRIVILEGES;

-- ──────────────────────────────────────────
--  DATOS DE PRUEBA – Vendedores
-- ──────────────────────────────────────────
INSERT INTO `vendedores` (`nombre`, `apellido`, `email`, `telefono`) VALUES
('Gabriel',    'Gamero',  'gabriel@hogarideal.pe',    '+51 936 338 196'),
('Jean Pierre','Garcia',  'jeanpierre@hogarideal.pe', '+51 999 888 777'),
('Jorge',      'Campos',  'jorge@hogarideal.pe',      '+51 988 777 666'),
('Victor',     'Quispe',  'victor@hogarideal.pe',     '+51 977 666 555');

-- ──────────────────────────────────────────
--  DATOS DE PRUEBA – Propiedades de ejemplo
-- ──────────────────────────────────────────
INSERT INTO `propiedades` (`titulo`, `descripcion`, `precio`, `tipo`, `habitaciones`, `banos`, `estacionamientos`, `metros2`, `direccion`, `vendedor_id`) VALUES
('Casa Moderna en Surco',           'Hermosa casa de diseño contemporáneo con amplios espacios, cocina americana, jardín privado y piscina. Ideal para familias.',  450000, 'casa',         4, 3, 2, 280.00, 'Av. El Derby 278, Santiago de Surco, Lima',      1),
('Departamento en Miraflores',      'Moderno departamento frente al mar, con vista panorámica al Pacífico. Acabados de primera, gimnasio y áreas comunes.',          280000, 'departamento', 3, 2, 1, 120.00, 'Malecón de la Reserva 1035, Miraflores, Lima',   2),
('Casa en La Molina',               'Amplia casa en condominio cerrado con seguridad 24h. Sala doble altura, cochera para 3 autos y zona de parrilla.',              520000, 'casa',         5, 4, 3, 350.00, 'Calle Las Camelias 456, La Molina, Lima',         1),
('Terreno en Lurín',                'Terreno industrial en zona de expansión, con todos los servicios y acceso a la Panamericana Sur. Ideal para almacén o fábrica.', 90000, 'terreno',      0, 0, 0, 500.00, 'Km 38 Panamericana Sur, Lurín, Lima',             3),
('Local Comercial en San Isidro',   'Local en primer piso con vitrina al exterior, baños propios, depósito y estacionamiento incluido. Zona financiera.',            180000, 'local',        0, 1, 1,  85.00, 'Av. Javier Prado Oeste 1470, San Isidro, Lima',  4),
('Departamento en San Borja',       'Departamento seminuevo en edificio con ascensor, área de juegos para niños y vigilancia. Cerca al Jockey Plaza.',               195000, 'departamento', 3, 2, 1, 110.00, 'Av. San Luis 2345, San Borja, Lima',              2),
('Casa de Playa en Punta Hermosa',  'Casa de playa a 50 metros del mar. Terraza con vista al océano, piscina privada y parrilla. Perfecta para vacacionar.',         320000, 'casa',         4, 3, 2, 200.00, 'Calle Los Delfines 12, Punta Hermosa, Lima',      3),
('Terreno en Cieneguilla',          'Terreno en zona ecológica con vista al río Lurín. Apto para proyecto de bungalows o casa de campo. Acertado en documentos.',     45000, 'terreno',      0, 0, 0, 800.00, 'Sector El Sauce, Cieneguilla, Lima',              4);

-- ──────────────────────────────────────────
--  NOTA IMPORTANTE:
--  El usuario administrador se crea ejecutando:
--  http://localhost/APF1-INTEGRADOR/install
--
--  Credenciales por defecto:
--  Email:    admin@hogarideal.pe
--  Password: admin123
-- ──────────────────────────────────────────
