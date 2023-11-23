PRAGMA foreign_keys=off;

-- DROP TABLE IF EXISTS `detalle_venta`;
CREATE TABLE IF NOT EXISTS `detalle_venta` (
  `id_detalle` INTEGER PRIMARY KEY AUTOINCREMENT,
  `det_cantidad` INTEGER NOT NULL,
  `det_vencimiento` DATE NOT NULL,
  `id_det_lote` INTEGER NOT NULL,
  `id_det_prod` INTEGER NOT NULL,
  `lote_id_prov` INTEGER NOT NULL,
  `id_det_venta` INTEGER NOT NULL,
  FOREIGN KEY (`id_det_venta`) REFERENCES `venta` (`id_venta`)
);

-- DROP TABLE IF EXISTS `laboratorio`;
CREATE TABLE IF NOT EXISTS `laboratorio` (
  `id_laboratorio` INTEGER PRIMARY KEY AUTOINCREMENT,
  `nombre` TEXT NOT NULL
);

INSERT INTO `laboratorio` (`nombre`) VALUES
('Roche'),
('Bayer'),
('Pfizer'),
('Abbot'),
('Merck & Co'),
('Sanofi'),
('Novartis'),
('Celgene'),
('Johnson & Johnson');

-- DROP TABLE IF EXISTS `lote`;
CREATE TABLE IF NOT EXISTS `lote` (
  `id_lote` INTEGER PRIMARY KEY AUTOINCREMENT,
  `stock` INTEGER NOT NULL,
  `vencimiento` DATE NOT NULL,
  `id_lote_prod` INTEGER NOT NULL,
  `lote_id_prov` INTEGER NOT NULL,
  FOREIGN KEY (`id_lote_prod`) REFERENCES `producto` (`id_producto`),
  FOREIGN KEY (`lote_id_prov`) REFERENCES `proveedor` (`id_proveedor`)
);

-- DROP TABLE IF EXISTS `presentacion`;
CREATE TABLE IF NOT EXISTS `presentacion` (
  `id_presentacion` INTEGER PRIMARY KEY AUTOINCREMENT,
  `nombre` TEXT NOT NULL
);

INSERT INTO `presentacion` (`nombre`) VALUES
('Comprimidos o tabletas'),
('Cápsulas'),
('Jarabe'),
('Pomadas o ungüentos'),
('Inyecciones');

-- DROP TABLE IF EXISTS `producto`;
CREATE TABLE IF NOT EXISTS `producto` (
  `id_producto` INTEGER PRIMARY KEY AUTOINCREMENT,
  `nombre` TEXT NOT NULL,
  `concentracion` TEXT,
  `adicional` TEXT,
  `precio` REAL,
  `avatar` TEXT,
  `prod_lab` INTEGER NOT NULL,
  `prod_tip_prod` INTEGER NOT NULL,
  `prod_present` INTEGER NOT NULL,
  FOREIGN KEY (`prod_lab`) REFERENCES `laboratorio` (`id_laboratorio`),
  FOREIGN KEY (`prod_tip_prod`) REFERENCES `tipo_producto` (`id_tip_prod`),
  FOREIGN KEY (`prod_present`) REFERENCES `presentacion` (`id_presentacion`)
);

INSERT INTO `producto` (`nombre`, `concentracion`, `adicional`, `precio`, `avatar`, `prod_lab`, `prod_tip_prod`, `prod_present`) VALUES
('qwdasd', 'dd', 'eee', 4, 'wwewe', 4, 1, 1),
('qwdasd', 'dd', 'eee', 4, 'wwewe', 4, 1, 1);

-- DROP TABLE IF EXISTS `proveedor`;
CREATE TABLE IF NOT EXISTS `proveedor` (
  `id_proveedor` INTEGER PRIMARY KEY AUTOINCREMENT,
  `nombre` TEXT NOT NULL,
  `telefono` INTEGER NOT NULL,
  `correo` TEXT,
  `direccion` TEXT NOT NULL
);

-- DROP TABLE IF EXISTS `tipo_producto`;
CREATE TABLE IF NOT EXISTS `tipo_producto` (
  `id_tip_prod` INTEGER PRIMARY KEY AUTOINCREMENT,
  `nombre` TEXT NOT NULL
);

INSERT INTO `tipo_producto` (`nombre`) VALUES
('Medicamentos de venta con receta'),
('Medicamentos de venta libre'),
('Productos de cuidado de la piel'),
('Vitaminas y suplementos'),
('Productos para el cuidado de la salud y el bi');

-- DROP TABLE IF EXISTS `tipo_us`;
CREATE TABLE IF NOT EXISTS `tipo_us` (
  `id_tipo_us` INTEGER PRIMARY KEY AUTOINCREMENT,
  `nombre_tipo` TEXT NOT NULL
);

INSERT INTO `tipo_us` (`nombre_tipo`) VALUES
('Administrador'),
('Farmacéutico'),
('Root');

-- DROP TABLE IF EXISTS `usuario`;
CREATE TABLE IF NOT EXISTS `usuario` (
  `id_usuario` INTEGER PRIMARY KEY AUTOINCREMENT,
  `nombre_us` TEXT NOT NULL,
  `apellidos_us` TEXT NOT NULL,
  `edad` DATE NOT NULL,
  `ci_us` TEXT,
  `contrasena_us` TEXT NOT NULL,
  `telefono_us` TEXT,
  `correo_us` TEXT,
  `genero_us` TEXT,
  `info_us` TEXT,
  `avatar` TEXT,
  `us_tipo` INTEGER NOT NULL,
  FOREIGN KEY (`us_tipo`) REFERENCES `tipo_us` (`id_tipo_us`)
);

INSERT INTO `usuario` (`nombre_us`, `apellidos_us`, `edad`, `ci_us`, `contrasena_us`, `telefono_us`, `correo_us`, `genero_us`, `info_us`, `avatar`, `us_tipo`) VALUES
('Maiker', 'Bravo', '2003-01-06', '29674336', '123456', '04247217960', 'hola@correo.com', 'hombre', 'Hola', '654d678915831-user-default.png', 3),
('Michell Veronica', 'Caceres', '2003-10-26', '123456', '123456', '0412345678', 'hola@correo.com', 'Alien', 'Wakanda', '6536b492156e6-user-default.png', 1),
('Pancho', 'Villa232', '2016-02-03', '12345675', '123456', '+5842472179', 'dansware2003@gmail.com', 'mujer', 'bhjbhj', 'user-default.png', 2);

-- DROP TABLE IF EXISTS `venta`;
CREATE TABLE IF NOT EXISTS `venta` (
  `id_venta` INTEGER PRIMARY KEY AUTOINCREMENT,
  `fecha` DATETIME,
  `cliente` TEXT,
  `ci` TEXT,
  `total` REAL,
  `vendedor` INTEGER NOT NULL,
  FOREIGN KEY (`vendedor`) REFERENCES `usuario` (`id_usuario`)
);

-- DROP TABLE IF EXISTS `venta_producto`;
CREATE TABLE IF NOT EXISTS `venta_producto` (
  `id_ventaproducto` INTEGER PRIMARY KEY AUTOINCREMENT,
  `cantidad` INTEGER NOT NULL,
  `subtotal` REAL NOT NULL,
  `producto_id_producto` INTEGER NOT NULL,
  `venta_id_venta` INTEGER NOT NULL,
  FOREIGN KEY (`producto_id_producto`) REFERENCES `producto` (`id_producto`),
  FOREIGN KEY (`venta_id_venta`) REFERENCES `venta` (`id_venta`)
);

PRAGMA foreign_keys=on;
