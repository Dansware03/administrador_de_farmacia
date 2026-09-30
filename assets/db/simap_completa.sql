-- ========================================================
-- ESTRUCTURA COMPLETA Y DATOS SIMAP (SQLite 3)
-- Clinica Popular Especializada La Fria
-- Generado: 2026-09-29 01:39:18
-- ========================================================

PRAGMA foreign_keys = OFF;
BEGIN TRANSACTION;

-- --------------------------------------------------------
-- Tabla: `tipo_us`
-- --------------------------------------------------------
DROP TABLE IF EXISTS "tipo_us";
CREATE TABLE "tipo_us" (
	"id_tipo_us"	INTEGER,
	"nombre_tipo"	TEXT NOT NULL,
	PRIMARY KEY("id_tipo_us" AUTOINCREMENT)
);

INSERT INTO "tipo_us" ("id_tipo_us", "nombre_tipo") VALUES (1, 'Administrador');
INSERT INTO "tipo_us" ("id_tipo_us", "nombre_tipo") VALUES (2, 'Secretario');

-- --------------------------------------------------------
-- Tabla: `unidad_medida`
-- --------------------------------------------------------
DROP TABLE IF EXISTS "unidad_medida";
CREATE TABLE "unidad_medida" (
        "id_unidad" INTEGER PRIMARY KEY AUTOINCREMENT,
        "nombre" TEXT NOT NULL,
        "codigo" TEXT NOT NULL
    );

INSERT INTO "unidad_medida" ("id_unidad", "nombre", "codigo") VALUES (1, 'Unidad', 'und');
INSERT INTO "unidad_medida" ("id_unidad", "nombre", "codigo") VALUES (2, 'Litro', 'L');
INSERT INTO "unidad_medida" ("id_unidad", "nombre", "codigo") VALUES (3, 'Galón', 'gal');
INSERT INTO "unidad_medida" ("id_unidad", "nombre", "codigo") VALUES (4, 'Pimpina (20 Litros)', 'pmp');
INSERT INTO "unidad_medida" ("id_unidad", "nombre", "codigo") VALUES (5, 'Paquete', 'paq');
INSERT INTO "unidad_medida" ("id_unidad", "nombre", "codigo") VALUES (6, 'Caja', 'caja');
INSERT INTO "unidad_medida" ("id_unidad", "nombre", "codigo") VALUES (7, 'Kilogramo', 'kg');
INSERT INTO "unidad_medida" ("id_unidad", "nombre", "codigo") VALUES (8, 'Kit', 'kit');
INSERT INTO "unidad_medida" ("id_unidad", "nombre", "codigo") VALUES (9, 'Frasco', 'frc');

-- --------------------------------------------------------
-- Tabla: `area_servicio`
-- --------------------------------------------------------
DROP TABLE IF EXISTS "area_servicio";
CREATE TABLE "area_servicio" (
        "id_area" INTEGER PRIMARY KEY AUTOINCREMENT,
        "nombre_area" TEXT NOT NULL,
        "nivel_riesgo" TEXT DEFAULT 'Medio'
    );

INSERT INTO "area_servicio" ("id_area", "nombre_area", "nivel_riesgo") VALUES (1, 'Nutrición y Dietética', 'Medio');
INSERT INTO "area_servicio" ("id_area", "nombre_area", "nivel_riesgo") VALUES (2, 'Quirófano / Pabellón', 'Alto');
INSERT INTO "area_servicio" ("id_area", "nombre_area", "nivel_riesgo") VALUES (3, 'Emergencia y Triaje', 'Alto');
INSERT INTO "area_servicio" ("id_area", "nombre_area", "nivel_riesgo") VALUES (4, 'Hospitalización', 'Medio');
INSERT INTO "area_servicio" ("id_area", "nombre_area", "nivel_riesgo") VALUES (5, 'Mantenimiento y Servicios Generales', 'Bajo');
INSERT INTO "area_servicio" ("id_area", "nombre_area", "nivel_riesgo") VALUES (6, 'Baños y Áreas Sanitarias', 'Alto');
INSERT INTO "area_servicio" ("id_area", "nombre_area", "nivel_riesgo") VALUES (7, 'Dirección y Administración', 'Bajo');
INSERT INTO "area_servicio" ("id_area", "nombre_area", "nivel_riesgo") VALUES (8, 'Sala de Partos', 'Alto');
INSERT INTO "area_servicio" ("id_area", "nombre_area", "nivel_riesgo") VALUES (9, 'Laboratorio y Bioanálisis', 'Alto');

-- --------------------------------------------------------
-- Tabla: `tipo_producto`
-- --------------------------------------------------------
DROP TABLE IF EXISTS "tipo_producto";
CREATE TABLE "tipo_producto" (
	"id_tip_prod"	INTEGER,
	"nombre"	TEXT NOT NULL,
	PRIMARY KEY("id_tip_prod" AUTOINCREMENT)
);

INSERT INTO "tipo_producto" ("id_tip_prod", "nombre") VALUES (1, 'Productos Químicos y Detergentes');
INSERT INTO "tipo_producto" ("id_tip_prod", "nombre") VALUES (2, 'Equipos y Utensilios de Limpieza');
INSERT INTO "tipo_producto" ("id_tip_prod", "nombre") VALUES (3, 'Equipos de Protección Individual (EPI / EPP)');
INSERT INTO "tipo_producto" ("id_tip_prod", "nombre") VALUES (4, 'Productos de Papel e Higiene');
INSERT INTO "tipo_producto" ("id_tip_prod", "nombre") VALUES (5, 'Repuestos y Herramientas de Mantenimiento');

-- --------------------------------------------------------
-- Tabla: `presentacion`
-- --------------------------------------------------------
DROP TABLE IF EXISTS "presentacion";
CREATE TABLE "presentacion" (
	"id_presentacion"	INTEGER,
	"nombre"	TEXT NOT NULL,
	PRIMARY KEY("id_presentacion" AUTOINCREMENT)
);

INSERT INTO "presentacion" ("id_presentacion", "nombre") VALUES (1, 'Unidad / Pieza');
INSERT INTO "presentacion" ("id_presentacion", "nombre") VALUES (2, 'Litro (Envase de 1 L)');
INSERT INTO "presentacion" ("id_presentacion", "nombre") VALUES (3, 'Galón (3.785 L)');
INSERT INTO "presentacion" ("id_presentacion", "nombre") VALUES (4, 'Pimpina de 20 Litros');
INSERT INTO "presentacion" ("id_presentacion", "nombre") VALUES (5, 'Caja con 50 unidades');
INSERT INTO "presentacion" ("id_presentacion", "nombre") VALUES (6, 'Caja con 100 unidades');
INSERT INTO "presentacion" ("id_presentacion", "nombre") VALUES (7, 'Paquete de 4 rollos');
INSERT INTO "presentacion" ("id_presentacion", "nombre") VALUES (8, 'Paquete de 50 unidades');
INSERT INTO "presentacion" ("id_presentacion", "nombre") VALUES (9, 'Paquete de 100 unidades');
INSERT INTO "presentacion" ("id_presentacion", "nombre") VALUES (10, 'Bolsa de 1 Kg');
INSERT INTO "presentacion" ("id_presentacion", "nombre") VALUES (11, 'Frasco de 100 ml');
INSERT INTO "presentacion" ("id_presentacion", "nombre") VALUES (12, 'Kit Integral');

-- --------------------------------------------------------
-- Tabla: `laboratorio`
-- --------------------------------------------------------
DROP TABLE IF EXISTS "laboratorio";
CREATE TABLE "laboratorio" (
	"id_laboratorio"	INTEGER,
	"nombre"	TEXT NOT NULL,
	PRIMARY KEY("id_laboratorio" AUTOINCREMENT)
);

INSERT INTO "laboratorio" ("id_laboratorio", "nombre") VALUES (1, 'INTERSOS (Organización Humanitaria)');
INSERT INTO "laboratorio" ("id_laboratorio", "nombre") VALUES (2, 'UNICEF (Fondo de las Naciones Unidas)');
INSERT INTO "laboratorio" ("id_laboratorio", "nombre") VALUES (3, 'FUNREAHV (Ayuda Humanitaria)');
INSERT INTO "laboratorio" ("id_laboratorio", "nombre") VALUES (4, 'MPPS (Ministerio de Salud)');
INSERT INTO "laboratorio" ("id_laboratorio", "nombre") VALUES (5, 'Almacén Central');
INSERT INTO "laboratorio" ("id_laboratorio", "nombre") VALUES (6, '3M Bioseguridad');
INSERT INTO "laboratorio" ("id_laboratorio", "nombre") VALUES (7, 'Kimberly-Clark Professional');
INSERT INTO "laboratorio" ("id_laboratorio", "nombre") VALUES (8, 'Genérico / Nacional');

-- --------------------------------------------------------
-- Tabla: `proveedor`
-- --------------------------------------------------------
DROP TABLE IF EXISTS "proveedor";
CREATE TABLE "proveedor" (
	"id_proveedor"	INTEGER,
	"nombre"	TEXT NOT NULL,
	"telefono"	INTEGER NOT NULL,
	"correo"	TEXT,
	"direccion"	TEXT NOT NULL, avatar VARCHAR(255),
	PRIMARY KEY("id_proveedor" AUTOINCREMENT)
);

INSERT INTO "proveedor" ("id_proveedor", "nombre", "telefono", "correo", "direccion", "avatar") VALUES (1, 'INTERSOS Venezuela', '0414-9192806', 'lineareporte.ven@unicef.org', 'San Cristóbal / La Fría, Estado Táchira', 'ProveedorDefault.png');
INSERT INTO "proveedor" ("id_proveedor", "nombre", "telefono", "correo", "direccion", "avatar") VALUES (2, 'FUNREAHV Táchira', '0414-7014767', 'hola@funreahv.com', 'Av. Las Pilas, San Cristóbal, Táchira', 'ProveedorDefault.png');
INSERT INTO "proveedor" ("id_proveedor", "nombre", "telefono", "correo", "direccion", "avatar") VALUES (3, 'MPPS Almacén Central', '0800-72583', 'almacen@mpps.gob.ve', 'Depósito Central Regional', 'ProveedorDefault.png');

-- --------------------------------------------------------
-- Tabla: `usuario`
-- --------------------------------------------------------
DROP TABLE IF EXISTS "usuario";
CREATE TABLE "usuario" (
	"id_usuario"	INTEGER,
	"nombre_us"	TEXT NOT NULL,
	"apellidos_us"	TEXT NOT NULL,
	"edad"	DATE NOT NULL,
	"ci_us"	TEXT,
	"contrasena_us"	TEXT NOT NULL,
	"telefono_us"	TEXT,
	"correo_us"	TEXT,
	"genero_us"	TEXT,
	"info_us"	TEXT,
	"avatar"	TEXT,
	"us_tipo"	INTEGER NOT NULL,
	FOREIGN KEY("us_tipo") REFERENCES "tipo_us"("id_tipo_us"),
	PRIMARY KEY("id_usuario" AUTOINCREMENT)
);

INSERT INTO "usuario" ("id_usuario", "nombre_us", "apellidos_us", "edad", "ci_us", "contrasena_us", "telefono_us", "correo_us", "genero_us", "info_us", "avatar", "us_tipo") VALUES (1, 'Administrador', 'SIMAP', '2003-01-06', '123456789', '$2y$10$4cQ98SMc6D5Jta3kQOY/BOHQyePT7JRtvwtcen4FaEVcUpDlYOlWG', '123456789', 'admin@simap.gob.ve', 'hombre', 'Administrador Principal SIMAP', 'user-default.png', 1);

-- --------------------------------------------------------
-- Tabla: `producto`
-- --------------------------------------------------------
DROP TABLE IF EXISTS "producto";
CREATE TABLE "producto" (
	"id_producto"	INTEGER,
	"nombre"	TEXT NOT NULL,
	"concentracion"	TEXT,
	"adicional"	TEXT,
	"precio"	REAL,
	"avatar"	TEXT,
	"prod_lab"	INTEGER NOT NULL,
	"prod_tip_prod"	INTEGER NOT NULL,
	"prod_present"	INTEGER NOT NULL, "id_unidad" INTEGER DEFAULT 1, "especificacion_talla" TEXT DEFAULT NULL,
	FOREIGN KEY("prod_tip_prod") REFERENCES "tipo_producto"("id_tip_prod"),
	FOREIGN KEY("prod_present") REFERENCES "presentacion"("id_presentacion"),
	FOREIGN KEY("prod_lab") REFERENCES "laboratorio"("id_laboratorio"),
	PRIMARY KEY("id_producto" AUTOINCREMENT)
);

INSERT INTO "producto" ("id_producto", "nombre", "concentracion", "adicional", "precio", "avatar", "prod_lab", "prod_tip_prod", "prod_present", "id_unidad", "especificacion_talla") VALUES (1, 'Hipoclorito de Sodio 3.5%', '3.5%', 'Para desinfección general de pisos y superficies', 0, 'ProductDefault.png', 8, 1, 3, 3, '3.5%');
INSERT INTO "producto" ("id_producto", "nombre", "concentracion", "adicional", "precio", "avatar", "prod_lab", "prod_tip_prod", "prod_present", "id_unidad", "especificacion_talla") VALUES (2, 'Hipoclorito de Sodio 5% - 6%', '5%', 'Desinfección de áreas críticas y quirófano', 0, 'ProductDefault.png', 8, 1, 3, 3, '5%');
INSERT INTO "producto" ("id_producto", "nombre", "concentracion", "adicional", "precio", "avatar", "prod_lab", "prod_tip_prod", "prod_present", "id_unidad", "especificacion_talla") VALUES (3, 'Cloro Líquido Desinfectante', 'Concentrado', 'Cloro desinfectante en galón', 0, 'ProductDefault.png', 1, 1, 3, 3, 'Galón');
INSERT INTO "producto" ("id_producto", "nombre", "concentracion", "adicional", "precio", "avatar", "prod_lab", "prod_tip_prod", "prod_present", "id_unidad", "especificacion_talla") VALUES (4, 'Amonio Cuaternario / Surfanio', 'Quirúrgico', 'Desinfección de alto nivel en áreas hospitalarias', 0, 'ProductDefault.png', 1, 1, 2, 2, 'Solución');
INSERT INTO "producto" ("id_producto", "nombre", "concentracion", "adicional", "precio", "avatar", "prod_lab", "prod_tip_prod", "prod_present", "id_unidad", "especificacion_talla") VALUES (5, 'Alcohol en Gel Antiséptico', '70%', 'Higiene de manos para personal asistencial', 0, 'ProductDefault.png', 3, 1, 4, 4, 'Pimpina 20L');
INSERT INTO "producto" ("id_producto", "nombre", "concentracion", "adicional", "precio", "avatar", "prod_lab", "prod_tip_prod", "prod_present", "id_unidad", "especificacion_talla") VALUES (6, 'Alcohol Isopropílico', '70%', 'Desinfección rápida de equipos y termómetros', 0, 'ProductDefault.png', 8, 1, 2, 2, '70%');
INSERT INTO "producto" ("id_producto", "nombre", "concentracion", "adicional", "precio", "avatar", "prod_lab", "prod_tip_prod", "prod_present", "id_unidad", "especificacion_talla") VALUES (7, 'Jabón Detergente Líquido', 'Multiuso', 'Lavado de áreas y utensilios', 0, 'ProductDefault.png', 1, 1, 3, 3, 'Galón');
INSERT INTO "producto" ("id_producto", "nombre", "concentracion", "adicional", "precio", "avatar", "prod_lab", "prod_tip_prod", "prod_present", "id_unidad", "especificacion_talla") VALUES (8, 'Jabón en Polvo Multiuso', '1 Kg', 'Lavado de paños y exteriores', 0, 'ProductDefault.png', 1, 1, 10, 7, '1 Kg');
INSERT INTO "producto" ("id_producto", "nombre", "concentracion", "adicional", "precio", "avatar", "prod_lab", "prod_tip_prod", "prod_present", "id_unidad", "especificacion_talla") VALUES (9, 'Desengrasante de Cocina y Mantenimiento', 'Concentrado', 'Limpieza profunda de grasas y taller', 0, 'ProductDefault.png', 1, 1, 3, 3, 'Galón');
INSERT INTO "producto" ("id_producto", "nombre", "concentracion", "adicional", "precio", "avatar", "prod_lab", "prod_tip_prod", "prod_present", "id_unidad", "especificacion_talla") VALUES (10, 'Agua Destilada para Disolución', 'Pura', 'Preparación de soluciones químicas y autoclave', 0, 'ProductDefault.png', 8, 1, 3, 3, 'Galón');
INSERT INTO "producto" ("id_producto", "nombre", "concentracion", "adicional", "precio", "avatar", "prod_lab", "prod_tip_prod", "prod_present", "id_unidad", "especificacion_talla") VALUES (11, 'Carro de Limpieza Amarillo con Prensa', 'Uso Hospitalario', 'Con prensa escurridora y compartimentos rotulados', 0, 'ProductDefault.png', 1, 2, 1, 1, 'Equipo');
INSERT INTO "producto" ("id_producto", "nombre", "concentracion", "adicional", "precio", "avatar", "prod_lab", "prod_tip_prod", "prod_present", "id_unidad", "especificacion_talla") VALUES (12, 'Mopa para Limpieza de Pisos con Cabo', 'Microfibra', 'Mopa completa con cabo y portacabo', 0, 'ProductDefault.png', 1, 2, 1, 1, 'Pieza');
INSERT INTO "producto" ("id_producto", "nombre", "concentracion", "adicional", "precio", "avatar", "prod_lab", "prod_tip_prod", "prod_present", "id_unidad", "especificacion_talla") VALUES (13, 'Paños de Limpieza Azul (Superficies)', 'Microfibra', 'Zona de riesgo medio / bajo (Superficies generales)', 0, 'ProductDefault.png', 1, 2, 1, 1, 'Azul');
INSERT INTO "producto" ("id_producto", "nombre", "concentracion", "adicional", "precio", "avatar", "prod_lab", "prod_tip_prod", "prod_present", "id_unidad", "especificacion_talla") VALUES (14, 'Paños de Limpieza Rosado (Baños)', 'Microfibra', 'Zona de riesgo sanitario (Exclusivo baños y sanitarios)', 0, 'ProductDefault.png', 1, 2, 1, 1, 'Rosado');
INSERT INTO "producto" ("id_producto", "nombre", "concentracion", "adicional", "precio", "avatar", "prod_lab", "prod_tip_prod", "prod_present", "id_unidad", "especificacion_talla") VALUES (15, 'Paños de Limpieza Rojo (Contenedores)', 'Microfibra', 'Zona de alto riesgo (Contenedores de basura biológica)', 0, 'ProductDefault.png', 1, 2, 1, 1, 'Rojo');
INSERT INTO "producto" ("id_producto", "nombre", "concentracion", "adicional", "precio", "avatar", "prod_lab", "prod_tip_prod", "prod_present", "id_unidad", "especificacion_talla") VALUES (16, 'Cepillo de Barrer con Palo Largo', 'Cerdas duras', 'Barrer pasillos y áreas exteriores', 0, 'ProductDefault.png', 1, 2, 1, 1, 'Pieza');
INSERT INTO "producto" ("id_producto", "nombre", "concentracion", "adicional", "precio", "avatar", "prod_lab", "prod_tip_prod", "prod_present", "id_unidad", "especificacion_talla") VALUES (17, 'Pala Plástica Recogedora de Basura', 'Mango largo', 'Pala con borde de goma', 0, 'ProductDefault.png', 1, 2, 1, 1, 'Pieza');
INSERT INTO "producto" ("id_producto", "nombre", "concentracion", "adicional", "precio", "avatar", "prod_lab", "prod_tip_prod", "prod_present", "id_unidad", "especificacion_talla") VALUES (18, 'Cepillo Limpiador de Poceta con Base', 'Sanitario', 'Limpieza de inodoros', 0, 'ProductDefault.png', 3, 2, 1, 1, 'Pieza');
INSERT INTO "producto" ("id_producto", "nombre", "concentracion", "adicional", "precio", "avatar", "prod_lab", "prod_tip_prod", "prod_present", "id_unidad", "especificacion_talla") VALUES (19, 'Tobo con Escurridor de Coleto', 'Resistente', 'Balde plástico con exprimidor', 0, 'ProductDefault.png', 1, 2, 1, 1, 'Pieza');
INSERT INTO "producto" ("id_producto", "nombre", "concentracion", "adicional", "precio", "avatar", "prod_lab", "prod_tip_prod", "prod_present", "id_unidad", "especificacion_talla") VALUES (20, 'Rociador o Atomizador Plástico', '1 Litro', 'Dispersión de soluciones desinfectantes preparadas', 0, 'ProductDefault.png', 3, 2, 1, 1, '1 L');
INSERT INTO "producto" ("id_producto", "nombre", "concentracion", "adicional", "precio", "avatar", "prod_lab", "prod_tip_prod", "prod_present", "id_unidad", "especificacion_talla") VALUES (21, 'Guantes de Nitrilo Talla L', 'Talla L', 'Caja x 100 unidades - Sin polvo, examen clínico', 0, 'ProductDefault.png', 3, 3, 6, 6, 'Talla L');
INSERT INTO "producto" ("id_producto", "nombre", "concentracion", "adicional", "precio", "avatar", "prod_lab", "prod_tip_prod", "prod_present", "id_unidad", "especificacion_talla") VALUES (22, 'Guantes de Nitrilo Talla M', 'Talla M', 'Caja x 100 unidades - Sin polvo', 0, 'ProductDefault.png', 3, 3, 6, 6, 'Talla M');
INSERT INTO "producto" ("id_producto", "nombre", "concentracion", "adicional", "precio", "avatar", "prod_lab", "prod_tip_prod", "prod_present", "id_unidad", "especificacion_talla") VALUES (23, 'Guantes Quirúrgicos Estériles', 'Par', 'Uso quirúrgico estéril', 0, 'ProductDefault.png', 1, 3, 1, 1, 'Estéril');
INSERT INTO "producto" ("id_producto", "nombre", "concentracion", "adicional", "precio", "avatar", "prod_lab", "prod_tip_prod", "prod_present", "id_unidad", "especificacion_talla") VALUES (24, 'Guantes Amarillos de Limpieza (Caucho)', 'Uso Rudo', 'Protección contra químicos y detergentes', 0, 'ProductDefault.png', 3, 3, 1, 1, 'Grueso');
INSERT INTO "producto" ("id_producto", "nombre", "concentracion", "adicional", "precio", "avatar", "prod_lab", "prod_tip_prod", "prod_present", "id_unidad", "especificacion_talla") VALUES (25, 'Mascarilla Médica KN-95', 'KN-95', 'Protección respiratoria de alta eficiencia', 0, 'ProductDefault.png', 1, 3, 1, 1, 'KN-95');
INSERT INTO "producto" ("id_producto", "nombre", "concentracion", "adicional", "precio", "avatar", "prod_lab", "prod_tip_prod", "prod_present", "id_unidad", "especificacion_talla") VALUES (26, 'Tapabocas Desechables Quirúrgicos', '3 Pliegues', 'Caja x 50 unidades', 0, 'ProductDefault.png', 3, 3, 5, 6, 'Caja 50');
INSERT INTO "producto" ("id_producto", "nombre", "concentracion", "adicional", "precio", "avatar", "prod_lab", "prod_tip_prod", "prod_present", "id_unidad", "especificacion_talla") VALUES (27, 'Batas Quirúrgicas Desechables', 'Antifluido', 'Manga larga con puño elástico', 0, 'ProductDefault.png', 1, 3, 1, 1, 'Estándar');
INSERT INTO "producto" ("id_producto", "nombre", "concentracion", "adicional", "precio", "avatar", "prod_lab", "prod_tip_prod", "prod_present", "id_unidad", "especificacion_talla") VALUES (28, 'Delantal Plástico Impermeable', 'PVC', 'Protección para lavado y manejo de químicos', 0, 'ProductDefault.png', 8, 3, 1, 1, 'Impermeable');
INSERT INTO "producto" ("id_producto", "nombre", "concentracion", "adicional", "precio", "avatar", "prod_lab", "prod_tip_prod", "prod_present", "id_unidad", "especificacion_talla") VALUES (29, 'Gafas de Protección Ocular', 'Policarbonato', 'Protección contra salpicaduras químicas', 0, 'ProductDefault.png', 1, 3, 1, 1, 'Transparente');
INSERT INTO "producto" ("id_producto", "nombre", "concentracion", "adicional", "precio", "avatar", "prod_lab", "prod_tip_prod", "prod_present", "id_unidad", "especificacion_talla") VALUES (30, 'Pantalla Facial / Careta Protectora', 'Ajustable', 'Protección facial completa', 0, 'ProductDefault.png', 1, 3, 1, 1, 'Acrílica');
INSERT INTO "producto" ("id_producto", "nombre", "concentracion", "adicional", "precio", "avatar", "prod_lab", "prod_tip_prod", "prod_present", "id_unidad", "especificacion_talla") VALUES (31, 'Mono Tyvek de Bioseguridad', 'Cuerpo Completo', 'Con capucha y elásticos en muñecas/tobillos', 0, 'ProductDefault.png', 1, 3, 1, 1, 'Talla Única');
INSERT INTO "producto" ("id_producto", "nombre", "concentracion", "adicional", "precio", "avatar", "prod_lab", "prod_tip_prod", "prod_present", "id_unidad", "especificacion_talla") VALUES (32, 'Gorro Quirúrgico Desechable', 'Elástico', 'Paquete con elásticos ajustables', 0, 'ProductDefault.png', 1, 3, 1, 1, 'Desechable');
INSERT INTO "producto" ("id_producto", "nombre", "concentracion", "adicional", "precio", "avatar", "prod_lab", "prod_tip_prod", "prod_present", "id_unidad", "especificacion_talla") VALUES (33, 'Cubrebotas Desechables (Par)', 'Antideslizante', 'Uso en quirófano y áreas estériles', 0, 'ProductDefault.png', 1, 3, 1, 1, 'Par');
INSERT INTO "producto" ("id_producto", "nombre", "concentracion", "adicional", "precio", "avatar", "prod_lab", "prod_tip_prod", "prod_present", "id_unidad", "especificacion_talla") VALUES (34, 'Zuecos Plásticos Blancos Talla Grande', 'Talla L / 40-42', 'Calzado sanitario lavable y autoclavable', 0, 'ProductDefault.png', 1, 3, 1, 1, 'Talla L');
INSERT INTO "producto" ("id_producto", "nombre", "concentracion", "adicional", "precio", "avatar", "prod_lab", "prod_tip_prod", "prod_present", "id_unidad", "especificacion_talla") VALUES (35, 'Papel Higiénico Institucional (Paquete 4 Rollos)', '4 Rollos', 'Papel higiénico suave de alta absorción', 0, 'ProductDefault.png', 1, 4, 7, 5, 'Paq 4');
INSERT INTO "producto" ("id_producto", "nombre", "concentracion", "adicional", "precio", "avatar", "prod_lab", "prod_tip_prod", "prod_present", "id_unidad", "especificacion_talla") VALUES (36, 'Toallas de Papel Interfoliadas / Toallín', 'Interfoliada', 'Secado de manos en dispensador', 0, 'ProductDefault.png', 1, 4, 1, 1, 'Rollo / Paq');
INSERT INTO "producto" ("id_producto", "nombre", "concentracion", "adicional", "precio", "avatar", "prod_lab", "prod_tip_prod", "prod_present", "id_unidad", "especificacion_talla") VALUES (37, 'Jabón Líquido Antibacterial para Manos', 'Triclosán/Aloe', 'Galón para recarga de dispensadores', 0, 'ProductDefault.png', 3, 4, 3, 3, 'Galón');
INSERT INTO "producto" ("id_producto", "nombre", "concentracion", "adicional", "precio", "avatar", "prod_lab", "prod_tip_prod", "prod_present", "id_unidad", "especificacion_talla") VALUES (38, 'Bolsa de Basura Negra 200 Litros', '200 L', 'Paquete de 50 bolsas gruesas para desechos', 0, 'ProductDefault.png', 1, 4, 8, 5, '200 Litros');
INSERT INTO "producto" ("id_producto", "nombre", "concentracion", "adicional", "precio", "avatar", "prod_lab", "prod_tip_prod", "prod_present", "id_unidad", "especificacion_talla") VALUES (39, 'Bolsa Azul con Asa 25 Kg', '25 Kg', 'Paquete de 100 bolsas con asa', 0, 'ProductDefault.png', 1, 4, 9, 5, '25 Kg');
INSERT INTO "producto" ("id_producto", "nombre", "concentracion", "adicional", "precio", "avatar", "prod_lab", "prod_tip_prod", "prod_present", "id_unidad", "especificacion_talla") VALUES (40, 'Bolsas para Papelera de Baño LDPE', 'Pequeña', 'Paquete x 100 bolsas higiénicas', 0, 'ProductDefault.png', 3, 4, 9, 5, 'LDPE');
INSERT INTO "producto" ("id_producto", "nombre", "concentracion", "adicional", "precio", "avatar", "prod_lab", "prod_tip_prod", "prod_present", "id_unidad", "especificacion_talla") VALUES (41, 'Recipiente para Desechos Cortopunzantes', 'Biopeligro', 'Guardián rojo para agujas y bisturís', 0, 'ProductDefault.png', 1, 4, 1, 1, 'Guardián');
INSERT INTO "producto" ("id_producto", "nombre", "concentracion", "adicional", "precio", "avatar", "prod_lab", "prod_tip_prod", "prod_present", "id_unidad", "especificacion_talla") VALUES (42, 'Herrajes para Poceta / Sanitarios', 'Plomería', 'Kit de repuesto para tanque y válvula de descarga', 0, 'ProductDefault.png', 8, 5, 12, 8, 'Kit');
INSERT INTO "producto" ("id_producto", "nombre", "concentracion", "adicional", "precio", "avatar", "prod_lab", "prod_tip_prod", "prod_present", "id_unidad", "especificacion_talla") VALUES (43, 'Llave de Paso y Canillas de 1/2 pulgada', 'Bronce/PVC', 'Repuesto para lavamanos y duchas', 0, 'ProductDefault.png', 8, 5, 1, 1, '1/2 pulg');
INSERT INTO "producto" ("id_producto", "nombre", "concentracion", "adicional", "precio", "avatar", "prod_lab", "prod_tip_prod", "prod_present", "id_unidad", "especificacion_talla") VALUES (44, 'Bombillos LED 12W / 18W Blanca', 'Iluminación', 'Repuesto para salas de espera y consultorios', 0, 'ProductDefault.png', 8, 5, 1, 1, 'LED');
INSERT INTO "producto" ("id_producto", "nombre", "concentracion", "adicional", "precio", "avatar", "prod_lab", "prod_tip_prod", "prod_present", "id_unidad", "especificacion_talla") VALUES (45, 'Destapacaños Manual con Mango', 'Sanitario', 'Herramienta de desobstrucción rápida', 0, 'ProductDefault.png', 3, 5, 1, 1, 'Pieza');

-- --------------------------------------------------------
-- Tabla: `lote`
-- --------------------------------------------------------
DROP TABLE IF EXISTS "lote";
CREATE TABLE "lote" (
	"id_lote"	INTEGER,
	"stock"	INTEGER NOT NULL,
	"vencimiento"	DATE NOT NULL,
	"id_lote_prod"	INTEGER NOT NULL,
	"lote_id_prov"	INTEGER NOT NULL, cod_lote INTEGER,
	FOREIGN KEY("lote_id_prov") REFERENCES "proveedor"("id_proveedor"),
	FOREIGN KEY("id_lote_prod") REFERENCES "producto"("id_producto"),
	PRIMARY KEY("id_lote" AUTOINCREMENT)
);

INSERT INTO "lote" ("id_lote", "stock", "vencimiento", "id_lote_prod", "lote_id_prov", "cod_lote") VALUES (1, 50, '2026-12-31', 1, 3, 202601);
INSERT INTO "lote" ("id_lote", "stock", "vencimiento", "id_lote_prod", "lote_id_prov", "cod_lote") VALUES (2, 35, '2026-11-30', 2, 3, 202602);
INSERT INTO "lote" ("id_lote", "stock", "vencimiento", "id_lote_prod", "lote_id_prov", "cod_lote") VALUES (3, 43, '2027-06-30', 3, 1, 102401);
INSERT INTO "lote" ("id_lote", "stock", "vencimiento", "id_lote_prod", "lote_id_prov", "cod_lote") VALUES (4, 20, '2027-01-31', 4, 1, 102402);
INSERT INTO "lote" ("id_lote", "stock", "vencimiento", "id_lote_prod", "lote_id_prov", "cod_lote") VALUES (5, 8, '2027-08-31', 5, 2, 202501);
INSERT INTO "lote" ("id_lote", "stock", "vencimiento", "id_lote_prod", "lote_id_prov", "cod_lote") VALUES (6, 30, '2027-03-31', 6, 3, 202603);
INSERT INTO "lote" ("id_lote", "stock", "vencimiento", "id_lote_prod", "lote_id_prov", "cod_lote") VALUES (7, 86, '2028-12-31', 7, 1, 102403);
INSERT INTO "lote" ("id_lote", "stock", "vencimiento", "id_lote_prod", "lote_id_prov", "cod_lote") VALUES (8, 43, '2028-12-31', 8, 1, 102404);
INSERT INTO "lote" ("id_lote", "stock", "vencimiento", "id_lote_prod", "lote_id_prov", "cod_lote") VALUES (9, 43, '2027-10-31', 9, 1, 102405);
INSERT INTO "lote" ("id_lote", "stock", "vencimiento", "id_lote_prod", "lote_id_prov", "cod_lote") VALUES (10, 4, '2030-01-01', 11, 1, 5001);
INSERT INTO "lote" ("id_lote", "stock", "vencimiento", "id_lote_prod", "lote_id_prov", "cod_lote") VALUES (11, 15, '2030-01-01', 12, 1, 5002);
INSERT INTO "lote" ("id_lote", "stock", "vencimiento", "id_lote_prod", "lote_id_prov", "cod_lote") VALUES (12, 30, '2030-01-01', 13, 1, 5003);
INSERT INTO "lote" ("id_lote", "stock", "vencimiento", "id_lote_prod", "lote_id_prov", "cod_lote") VALUES (13, 30, '2030-01-01', 14, 1, 5004);
INSERT INTO "lote" ("id_lote", "stock", "vencimiento", "id_lote_prod", "lote_id_prov", "cod_lote") VALUES (14, 30, '2030-01-01', 15, 1, 5005);
INSERT INTO "lote" ("id_lote", "stock", "vencimiento", "id_lote_prod", "lote_id_prov", "cod_lote") VALUES (15, 25, '2028-06-30', 21, 2, 4001);
INSERT INTO "lote" ("id_lote", "stock", "vencimiento", "id_lote_prod", "lote_id_prov", "cod_lote") VALUES (16, 25, '2028-06-30', 22, 2, 4002);
INSERT INTO "lote" ("id_lote", "stock", "vencimiento", "id_lote_prod", "lote_id_prov", "cod_lote") VALUES (17, 580, '2028-12-31', 25, 1, 7001);
INSERT INTO "lote" ("id_lote", "stock", "vencimiento", "id_lote_prod", "lote_id_prov", "cod_lote") VALUES (18, 108, '2028-12-31', 27, 1, 7002);
INSERT INTO "lote" ("id_lote", "stock", "vencimiento", "id_lote_prod", "lote_id_prov", "cod_lote") VALUES (19, 86, '2030-12-31', 35, 1, 8001);
INSERT INTO "lote" ("id_lote", "stock", "vencimiento", "id_lote_prod", "lote_id_prov", "cod_lote") VALUES (20, 10, '2028-05-31', 37, 2, 8002);

-- --------------------------------------------------------
-- Tabla: `venta`
-- --------------------------------------------------------
DROP TABLE IF EXISTS "venta";
CREATE TABLE "venta" (
	"id_venta"	INTEGER,
	"fecha"	DATETIME,
	"cliente"	TEXT,
	"ci"	TEXT,
	"total"	REAL,
	"vendedor"	INTEGER NOT NULL, "id_area" INTEGER DEFAULT NULL, "cargo_receptor" TEXT DEFAULT NULL, "observacion" TEXT DEFAULT NULL,
	FOREIGN KEY("vendedor") REFERENCES "usuario"("id_usuario"),
	PRIMARY KEY("id_venta" AUTOINCREMENT)
);

INSERT INTO "venta" ("id_venta", "fecha", "cliente", "ci", "total", "vendedor", "id_area", "cargo_receptor", "observacion") VALUES (1, '2026-09-28 10:30:00', 'Adriana López', 15272470, 0, 1, 1, 'Coordinadora de Nutrición', 'Dotación mensual según Acta FUNREAHV');

-- --------------------------------------------------------
-- Tabla: `detalle_venta`
-- --------------------------------------------------------
DROP TABLE IF EXISTS "detalle_venta";
CREATE TABLE "detalle_venta" (
	"id_detalle"	INTEGER,
	"det_cantidad"	INTEGER NOT NULL,
	"det_vencimiento"	DATE NOT NULL,
	"id_det_lote"	INTEGER NOT NULL,
	"id_det_prod"	INTEGER NOT NULL,
	"lote_id_prov"	INTEGER NOT NULL,
	"id_det_venta"	INTEGER NOT NULL,
	FOREIGN KEY("id_det_venta") REFERENCES "venta"("id_venta"),
	PRIMARY KEY("id_detalle" AUTOINCREMENT)
);

INSERT INTO "detalle_venta" ("id_detalle", "det_cantidad", "det_vencimiento", "id_det_lote", "id_det_prod", "lote_id_prov", "id_det_venta") VALUES (1, 1, '2027-08-31', 5, 5, 2, 1);
INSERT INTO "detalle_venta" ("id_detalle", "det_cantidad", "det_vencimiento", "id_det_lote", "id_det_prod", "lote_id_prov", "id_det_venta") VALUES (2, 2, '2028-06-30', 15, 21, 2, 1);
INSERT INTO "detalle_venta" ("id_detalle", "det_cantidad", "det_vencimiento", "id_det_lote", "id_det_prod", "lote_id_prov", "id_det_venta") VALUES (3, 50, '2028-12-31', 17, 25, 1, 1);

-- --------------------------------------------------------
-- Tabla: `venta_producto`
-- --------------------------------------------------------
DROP TABLE IF EXISTS "venta_producto";
CREATE TABLE "venta_producto" (
	"id_ventaproducto"	INTEGER,
	"cantidad"	INTEGER NOT NULL,
	"subtotal"	REAL NOT NULL,
	"producto_id_producto"	INTEGER NOT NULL,
	"venta_id_venta"	INTEGER NOT NULL,
	FOREIGN KEY("producto_id_producto") REFERENCES "producto"("id_producto"),
	FOREIGN KEY("venta_id_venta") REFERENCES "venta"("id_venta"),
	PRIMARY KEY("id_ventaproducto" AUTOINCREMENT)
);

INSERT INTO "venta_producto" ("id_ventaproducto", "cantidad", "subtotal", "producto_id_producto", "venta_id_venta") VALUES (1, 1, 0, 5, 1);
INSERT INTO "venta_producto" ("id_ventaproducto", "cantidad", "subtotal", "producto_id_producto", "venta_id_venta") VALUES (2, 2, 0, 21, 1);
INSERT INTO "venta_producto" ("id_ventaproducto", "cantidad", "subtotal", "producto_id_producto", "venta_id_venta") VALUES (3, 50, 0, 25, 1);

COMMIT;
PRAGMA foreign_keys = ON;
