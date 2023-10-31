--
-- Base de datos: `admin_farmacia`
--
-- --------------------------------------------------------
--
-- Estructura de tabla para la tabla `laboratorio`
--
ALTER TABLE `laboratorio` ADD `avatar` VARCHAR(255) NULL AFTER `nombre`;
--
-- Volcado de datos para la tabla `laboratorio`
--
INSERT INTO `laboratorio` (`id_laboratorio`, `nombre`, `avatar`) VALUES
(1, 'hola', 'LabDefault.png');