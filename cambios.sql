SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;


DROP TABLE IF EXISTS `detalle_venta`;
CREATE TABLE IF NOT EXISTS `detalle_venta` (
  `id_detalle` int NOT NULL AUTO_INCREMENT,
  `det_cantidad` int NOT NULL,
  `det_vencimiento` date NOT NULL,
  `id__det_lote` int NOT NULL,
  `id__det_prod` int NOT NULL,
  `lote_id_prov` int NOT NULL,
  `id_det_venta` int NOT NULL,
  PRIMARY KEY (`id_detalle`),
  KEY `id_det_venta_idx` (`id_det_venta`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

DROP TABLE IF EXISTS `laboratorio`;
CREATE TABLE IF NOT EXISTS `laboratorio` (
  `id_laboratorio` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(45) NOT NULL,
  PRIMARY KEY (`id_laboratorio`)
) ENGINE=InnoDB AUTO_INCREMENT=47 DEFAULT CHARSET=utf8mb3;

INSERT INTO `laboratorio` (`id_laboratorio`, `nombre`) VALUES
(1, 'Roche'),
(2, 'Bayer'),
(3, 'Pfizer'),
(4, 'Abbot'),
(5, 'Merck & Co'),
(6, 'Sanofi'),
(7, 'Novartis'),
(8, 'Celgene'),
(9, 'Johnson & Johnson');

DROP TABLE IF EXISTS `lote`;
CREATE TABLE IF NOT EXISTS `lote` (
  `id_lote` int NOT NULL AUTO_INCREMENT,
  `stock` int NOT NULL,
  `vencimiento` date NOT NULL,
  `lote_id_prod` int NOT NULL,
  `lote_id_prov` int NOT NULL,
  PRIMARY KEY (`id_lote`),
  KEY `lote_id_prod_idx` (`lote_id_prod`),
  KEY `lote_id_prov_idx` (`lote_id_prov`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

DROP TABLE IF EXISTS `presentacion`;
CREATE TABLE IF NOT EXISTS `presentacion` (
  `id_presentacion` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(45) NOT NULL,
  PRIMARY KEY (`id_presentacion`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb3;

INSERT INTO `presentacion` (`id_presentacion`, `nombre`) VALUES
(1, 'Comprimidos o tabletas'),
(2, 'Cápsulas'),
(3, 'Jarabe'),
(4, 'Pomadas o ungüentos'),
(5, 'Inyecciones');

DROP TABLE IF EXISTS `producto`;
CREATE TABLE IF NOT EXISTS `producto` (
  `id_producto` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(45) NOT NULL,
  `concentracion` varchar(255) DEFAULT NULL,
  `adicional` varchar(255) DEFAULT NULL,
  `precio` float DEFAULT NULL,
  `avatar` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `prod_lab` int NOT NULL,
  `prod_tip_prod` int NOT NULL,
  `prod_present` int NOT NULL,
  PRIMARY KEY (`id_producto`),
  KEY `prod_lab_idx` (`prod_lab`),
  KEY `prod_tip_prod_idx` (`prod_tip_prod`),
  KEY `prod_present_idx` (`prod_present`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb3;

INSERT INTO `producto` (`id_producto`, `nombre`, `concentracion`, `adicional`, `precio`, `avatar`, `prod_lab`, `prod_tip_prod`, `prod_present`) VALUES
(10, 'qwdasd', 'dd', 'eee', 4, 'wwewe', 4, 1, 1),
(11, 'qwdasd', 'dd', 'eee', 4, 'wwewe', 4, 1, 1);

DROP TABLE IF EXISTS `proveedor`;
CREATE TABLE IF NOT EXISTS `proveedor` (
  `id_proveedor` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(45) NOT NULL,
  `telefono` int NOT NULL,
  `correo` varchar(45) DEFAULT NULL,
  `direccion` varchar(45) NOT NULL,
  PRIMARY KEY (`id_proveedor`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

DROP TABLE IF EXISTS `tipo_producto`;
CREATE TABLE IF NOT EXISTS `tipo_producto` (
  `id_tip_prod` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(45) NOT NULL,
  PRIMARY KEY (`id_tip_prod`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb3;

INSERT INTO `tipo_producto` (`id_tip_prod`, `nombre`) VALUES
(1, 'Medicamentos de venta con receta'),
(2, 'Medicamentos de venta libre'),
(3, 'Productos de cuidado de la piel'),
(4, 'Vitaminas y suplementos'),
(5, 'Productos para el cuidado de la salud y el bi');

DROP TABLE IF EXISTS `tipo_us`;
CREATE TABLE IF NOT EXISTS `tipo_us` (
  `id_tipo_us` int NOT NULL AUTO_INCREMENT,
  `nombre_tipo` varchar(45) NOT NULL,
  PRIMARY KEY (`id_tipo_us`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb3;

INSERT INTO `tipo_us` (`id_tipo_us`, `nombre_tipo`) VALUES
(1, 'Administrador'),
(2, 'Farmacéutico'),
(3, 'Root');

DROP TABLE IF EXISTS `usuario`;
CREATE TABLE IF NOT EXISTS `usuario` (
  `id_usuario` int NOT NULL AUTO_INCREMENT,
  `nombre_us` varchar(45) NOT NULL,
  `apellidos_us` varchar(45) NOT NULL,
  `edad` date NOT NULL,
  `ci_us` varchar(255) DEFAULT NULL,
  `contrasena_us` varchar(45) NOT NULL,
  `telefono_us` varchar(11) DEFAULT NULL,
  `correo_us` varchar(25) DEFAULT NULL,
  `genero_us` varchar(25) DEFAULT NULL,
  `info_us` varchar(500) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `us_tipo` int NOT NULL,
  PRIMARY KEY (`id_usuario`),
  KEY `us_tipo_idx` (`us_tipo`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb3;

INSERT INTO `usuario` (`id_usuario`, `nombre_us`, `apellidos_us`, `edad`, `ci_us`, `contrasena_us`, `telefono_us`, `correo_us`, `genero_us`, `info_us`, `avatar`, `us_tipo`) VALUES
(1, 'Maiker', 'Bravo', '2003-01-06', '29674336', '123456', '04247217960', 'hola@correo.com', 'hombre', 'Hola', '654d678915831-user-default.png', 3),
(2, 'Michell Veronica', 'Caceres', '2003-10-26', '123456', '123456', '0412345678', 'hola@correo.com', 'Alien', 'Wakanda', '6536b492156e6-user-default.png', 1),
(14, 'Pancho', 'Villa232', '2016-02-03', '12345675', '123456', '+5842472179', 'dansware2003@gmail.com', 'mujer', 'bhjbhj', 'user-default.png', 2);

DROP TABLE IF EXISTS `venta`;
CREATE TABLE IF NOT EXISTS `venta` (
  `id_venta` int NOT NULL AUTO_INCREMENT,
  `fecha` datetime DEFAULT NULL,
  `cliente` varchar(45) DEFAULT NULL,
  `ci` varchar(255) DEFAULT NULL,
  `total` float DEFAULT NULL,
  `vendedor` int NOT NULL,
  PRIMARY KEY (`id_venta`),
  KEY `vendedor` (`vendedor`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

DROP TABLE IF EXISTS `venta_producto`;
CREATE TABLE IF NOT EXISTS `venta_producto` (
  `id_ventaproducto` int NOT NULL AUTO_INCREMENT,
  `cantidad` int NOT NULL,
  `subtotal` float NOT NULL,
  `producto_id_producto` int NOT NULL,
  `venta_id_venta` int NOT NULL,
  PRIMARY KEY (`id_ventaproducto`),
  KEY `fk_venta_has_producto_producto1_idx` (`producto_id_producto`),
  KEY `fk_venta_has_producto_venta1_idx` (`venta_id_venta`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;


ALTER TABLE `detalle_venta`
  ADD CONSTRAINT `id_det_venta` FOREIGN KEY (`id_det_venta`) REFERENCES `venta` (`id_venta`);

ALTER TABLE `lote`
  ADD CONSTRAINT `lote_id_prod` FOREIGN KEY (`lote_id_prod`) REFERENCES `producto` (`id_producto`),
  ADD CONSTRAINT `lote_id_prov` FOREIGN KEY (`lote_id_prov`) REFERENCES `proveedor` (`id_proveedor`);

ALTER TABLE `producto`
  ADD CONSTRAINT `fk_prod_lab` FOREIGN KEY (`prod_lab`) REFERENCES `laboratorio` (`id_laboratorio`),
  ADD CONSTRAINT `prod_lab` FOREIGN KEY (`prod_lab`) REFERENCES `laboratorio` (`id_laboratorio`),
  ADD CONSTRAINT `prod_present` FOREIGN KEY (`prod_present`) REFERENCES `presentacion` (`id_presentacion`),
  ADD CONSTRAINT `prod_tip_prod` FOREIGN KEY (`prod_tip_prod`) REFERENCES `tipo_producto` (`id_tip_prod`);

ALTER TABLE `usuario`
  ADD CONSTRAINT `us_tipo` FOREIGN KEY (`us_tipo`) REFERENCES `tipo_us` (`id_tipo_us`);

ALTER TABLE `venta`
  ADD CONSTRAINT `venta_ibfk_1` FOREIGN KEY (`vendedor`) REFERENCES `usuario` (`id_usuario`);

ALTER TABLE `venta_producto`
  ADD CONSTRAINT `fk_venta_has_producto_producto1` FOREIGN KEY (`producto_id_producto`) REFERENCES `producto` (`id_producto`),
  ADD CONSTRAINT `fk_venta_has_producto_venta1` FOREIGN KEY (`venta_id_venta`) REFERENCES `venta` (`id_venta`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
