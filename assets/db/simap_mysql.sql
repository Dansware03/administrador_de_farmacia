-- ========================================================
-- MIGRACION COMPLETA MYSQL / MARIADB: SIMAP
-- Adaptada para Laragon / MySQL 8.0+ / MariaDB 10.4+
-- Charset: utf8mb4 / Collation: utf8mb4_unicode_ci
-- ========================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- --------------------------------------------------------
-- 1. Tabla: `tipo_us` (Roles de Usuario)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `tipo_us`;
CREATE TABLE `tipo_us` (
  `id_tipo_us` INT NOT NULL AUTO_INCREMENT,
  `nombre_tipo` VARCHAR(100) NOT NULL,
  PRIMARY KEY (`id_tipo_us`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `tipo_us` (`id_tipo_us`, `nombre_tipo`) VALUES
(1, 'Administrador'),
(2, 'Farmacéutico'),
(3, 'Root');

-- --------------------------------------------------------
-- 2. Tabla: `usuario` (Cuentas y Personal)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `usuario`;
CREATE TABLE `usuario` (
  `id_usuario` INT NOT NULL AUTO_INCREMENT,
  `nombre_us` VARCHAR(100) NOT NULL,
  `apellidos_us` VARCHAR(100) NOT NULL,
  `edad` DATE NOT NULL,
  `ci_us` VARCHAR(25) DEFAULT NULL,
  `contrasena_us` VARCHAR(255) NOT NULL,
  `telefono_us` VARCHAR(50) DEFAULT NULL,
  `correo_us` VARCHAR(150) DEFAULT NULL,
  `genero_us` VARCHAR(20) DEFAULT NULL,
  `info_us` TEXT DEFAULT NULL,
  `avatar` VARCHAR(255) DEFAULT 'user-default.png',
  `us_tipo` INT NOT NULL,
  PRIMARY KEY (`id_usuario`),
  KEY `fk_usuario_tipo_us` (`us_tipo`),
  CONSTRAINT `fk_usuario_tipo_us` FOREIGN KEY (`us_tipo`) REFERENCES `tipo_us` (`id_tipo_us`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `usuario` (`id_usuario`, `nombre_us`, `apellidos_us`, `edad`, `ci_us`, `contrasena_us`, `telefono_us`, `correo_us`, `genero_us`, `info_us`, `avatar`, `us_tipo`) VALUES
(1, 'Root', '- Administrador', '2003-01-06', '123456789', '123456789', '123456789', 'hola@correo.com', 'hombre', 'Administrador Principal', '67cb629bd7e1e-user-default.png', 3);

-- --------------------------------------------------------
-- 3. Tabla: `laboratorio` (Fabricantes / Marcas de Insumos)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `laboratorio`;
CREATE TABLE `laboratorio` (
  `id_laboratorio` INT NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(150) NOT NULL,
  PRIMARY KEY (`id_laboratorio`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `laboratorio` (`id_laboratorio`, `nombre`) VALUES
(1, 'Roche'),
(2, 'Bayer'),
(3, 'Pfizer'),
(4, 'Abbott'),
(5, 'Merck & Co'),
(6, 'Sanofi'),
(7, 'Novartis'),
(8, 'Celgene'),
(9, 'Johnson & Johnson');

-- --------------------------------------------------------
-- 4. Tabla: `presentacion` (Formatos / Envases)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `presentacion`;
CREATE TABLE `presentacion` (
  `id_presentacion` INT NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(150) NOT NULL,
  PRIMARY KEY (`id_presentacion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `presentacion` (`id_presentacion`, `nombre`) VALUES
(7, 'Cápsulas, frasco con 30 unidades'),
(8, 'Tabletas efervescentes, tubo con 20 tabletas'),
(9, 'Cápsulas de liberación retardada, caja con 14 unidades'),
(10, 'Jarabe, botella de 120 ml'),
(11, 'Comprimidos, blister con 10 tabletas');

-- --------------------------------------------------------
-- 5. Tabla: `tipo_producto` (Categorías de Insumos / Materiales)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `tipo_producto`;
CREATE TABLE `tipo_producto` (
  `id_tip_prod` INT NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(150) NOT NULL,
  PRIMARY KEY (`id_tip_prod`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `tipo_producto` (`id_tip_prod`, `nombre`) VALUES
(6, 'Antibiótico de amplio espectro'),
(7, 'Analgésico y antiinflamatorio no esteroideo (AINE)'),
(8, 'Antihistamínico'),
(9, 'Analgésico y antipirético'),
(10, 'Suplemento vitamínico'),
(11, 'Inhibidor de la bomba de protones (IBP)');

-- --------------------------------------------------------
-- 6. Tabla: `proveedor` (Distribuidores / Proveedores)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `proveedor`;
CREATE TABLE `proveedor` (
  `id_proveedor` INT NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(150) NOT NULL,
  `telefono` VARCHAR(50) NOT NULL,
  `correo` VARCHAR(150) DEFAULT NULL,
  `direccion` TEXT NOT NULL,
  `avatar` VARCHAR(255) DEFAULT 'ProveedorDefault.png',
  PRIMARY KEY (`id_proveedor`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `proveedor` (`id_proveedor`, `nombre`, `telefono`, `correo`, `direccion`, `avatar`) VALUES
(2, 'Almacen', '0424-7217960', 'hola@correo.com', 'carrera 12 entre calle 6 y 7', 'ProveedorDefault.png');

-- --------------------------------------------------------
-- 7. Tabla: `producto` (Catálogo Maestro de Insumos)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `producto`;
CREATE TABLE `producto` (
  `id_producto` INT NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(150) NOT NULL,
  `concentracion` VARCHAR(100) DEFAULT NULL,
  `adicional` TEXT DEFAULT NULL,
  `precio` DECIMAL(12,2) DEFAULT 0.00,
  `avatar` VARCHAR(255) DEFAULT 'ProductDefault.png',
  `prod_lab` INT NOT NULL,
  `prod_tip_prod` INT NOT NULL,
  `prod_present` INT NOT NULL,
  PRIMARY KEY (`id_producto`),
  KEY `fk_producto_laboratorio` (`prod_lab`),
  KEY `fk_producto_tipo` (`prod_tip_prod`),
  KEY `fk_producto_presentacion` (`prod_present`),
  CONSTRAINT `fk_producto_laboratorio` FOREIGN KEY (`prod_lab`) REFERENCES `laboratorio` (`id_laboratorio`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_producto_tipo` FOREIGN KEY (`prod_tip_prod`) REFERENCES `tipo_producto` (`id_tip_prod`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_producto_presentacion` FOREIGN KEY (`prod_present`) REFERENCES `presentacion` (`id_presentacion`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `producto` (`id_producto`, `nombre`, `concentracion`, `adicional`, `precio`, `avatar`, `prod_lab`, `prod_tip_prod`, `prod_present`) VALUES
(50, 'Omeprazol', '500 mg/ml', 'Ejemplo', 150.00, 'ProductDefault.png', 3, 10, 7);

-- --------------------------------------------------------
-- 8. Tabla: `lote` (Lotes, Vencimientos y Stock Físico)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `lote`;
CREATE TABLE `lote` (
  `id_lote` INT NOT NULL AUTO_INCREMENT,
  `stock` INT NOT NULL DEFAULT 0,
  `vencimiento` DATE NOT NULL,
  `id_lote_prod` INT NOT NULL,
  `lote_id_prov` INT NOT NULL,
  `cod_lote` INT DEFAULT NULL,
  PRIMARY KEY (`id_lote`),
  KEY `fk_lote_producto` (`id_lote_prod`),
  KEY `fk_lote_proveedor` (`lote_id_prov`),
  CONSTRAINT `fk_lote_producto` FOREIGN KEY (`id_lote_prod`) REFERENCES `producto` (`id_producto`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_lote_proveedor` FOREIGN KEY (`lote_id_prov`) REFERENCES `proveedor` (`id_proveedor`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `lote` (`id_lote`, `stock`, `vencimiento`, `id_lote_prod`, `lote_id_prov`, `cod_lote`) VALUES
(1, 6, '2025-03-29', 50, 2, 5020),
(2, 200, '2025-10-29', 50, 2, 540001);

-- --------------------------------------------------------
-- 9. Tabla: `venta` (Cabecera de Salidas / Entregas)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `venta`;
CREATE TABLE `venta` (
  `id_venta` INT NOT NULL AUTO_INCREMENT,
  `fecha` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `cliente` VARCHAR(150) DEFAULT NULL,
  `ci` VARCHAR(50) DEFAULT NULL,
  `total` DECIMAL(12,2) DEFAULT 0.00,
  `vendedor` INT NOT NULL,
  PRIMARY KEY (`id_venta`),
  KEY `fk_venta_usuario` (`vendedor`),
  CONSTRAINT `fk_venta_usuario` FOREIGN KEY (`vendedor`) REFERENCES `usuario` (`id_usuario`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `venta` (`id_venta`, `fecha`, `cliente`, `ci`, `total`, `vendedor`) VALUES
(5, '2025-03-09 16:47:49', 'MAIKER DANIEL BRAVO CHACO', '29674336', 150.00, 1);

-- --------------------------------------------------------
-- 10. Tabla: `detalle_venta` (Trazabilidad de Lotes en Salidas)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `detalle_venta`;
CREATE TABLE `detalle_venta` (
  `id_detalle` INT NOT NULL AUTO_INCREMENT,
  `det_cantidad` INT NOT NULL,
  `det_vencimiento` DATE NOT NULL,
  `id_det_lote` INT NOT NULL,
  `id_det_prod` INT NOT NULL,
  `lote_id_prov` INT NOT NULL,
  `id_det_venta` INT NOT NULL,
  PRIMARY KEY (`id_detalle`),
  KEY `fk_detalle_venta` (`id_det_venta`),
  CONSTRAINT `fk_detalle_venta` FOREIGN KEY (`id_det_venta`) REFERENCES `venta` (`id_venta`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 11. Tabla: `venta_producto` (Líneas de Salida de Producto)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `venta_producto`;
CREATE TABLE `venta_producto` (
  `id_ventaproducto` INT NOT NULL AUTO_INCREMENT,
  `cantidad` INT NOT NULL,
  `subtotal` DECIMAL(12,2) NOT NULL,
  `producto_id_producto` INT NOT NULL,
  `venta_id_venta` INT NOT NULL,
  PRIMARY KEY (`id_ventaproducto`),
  KEY `fk_ventaprod_producto` (`producto_id_producto`),
  KEY `fk_ventaprod_venta` (`venta_id_venta`),
  CONSTRAINT `fk_ventaprod_producto` FOREIGN KEY (`producto_id_producto`) REFERENCES `producto` (`id_producto`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_ventaprod_venta` FOREIGN KEY (`venta_id_venta`) REFERENCES `venta` (`id_venta`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `venta_producto` (`id_ventaproducto`, `cantidad`, `subtotal`, `producto_id_producto`, `venta_id_venta`) VALUES
(6, 1, 150.00, 50, 5);

SET FOREIGN_KEY_CHECKS = 1;
