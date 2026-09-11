-- ========================================================
-- ESTRUCTURA COMPLETA Y MIGRACION: SIMAP
-- Base de datos origen: SQLite 3 (bdfarmacia.db)
-- Generado automaticamente
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

-- Datos para `tipo_us`
INSERT INTO "tipo_us" ("id_tipo_us", "nombre_tipo") VALUES (1, 'Administrador');
INSERT INTO "tipo_us" ("id_tipo_us", "nombre_tipo") VALUES (2, 'Farmacéutico');
INSERT INTO "tipo_us" ("id_tipo_us", "nombre_tipo") VALUES (3, 'Root');

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

-- Datos para `usuario`
INSERT INTO "usuario" ("id_usuario", "nombre_us", "apellidos_us", "edad", "ci_us", "contrasena_us", "telefono_us", "correo_us", "genero_us", "info_us", "avatar", "us_tipo") VALUES (1, 'Root', '- Prueba', '2003-01-06', 123456789, 123456789, 123456789, 'hola@correo.com', 'hombre', 'Hola', '67cb629bd7e1e-user-default.png', 3);

-- --------------------------------------------------------
-- Tabla: `laboratorio`
-- --------------------------------------------------------
DROP TABLE IF EXISTS "laboratorio";
CREATE TABLE "laboratorio" (
	"id_laboratorio"	INTEGER,
	"nombre"	TEXT NOT NULL,
	PRIMARY KEY("id_laboratorio" AUTOINCREMENT)
);

-- Datos para `laboratorio`
INSERT INTO "laboratorio" ("id_laboratorio", "nombre") VALUES (1, 'Roche');
INSERT INTO "laboratorio" ("id_laboratorio", "nombre") VALUES (2, 'Bayer');
INSERT INTO "laboratorio" ("id_laboratorio", "nombre") VALUES (3, 'Pfizer');
INSERT INTO "laboratorio" ("id_laboratorio", "nombre") VALUES (4, 'Abbott');
INSERT INTO "laboratorio" ("id_laboratorio", "nombre") VALUES (5, 'Merck & Co');
INSERT INTO "laboratorio" ("id_laboratorio", "nombre") VALUES (6, 'Sanofi');
INSERT INTO "laboratorio" ("id_laboratorio", "nombre") VALUES (7, 'Novartis');
INSERT INTO "laboratorio" ("id_laboratorio", "nombre") VALUES (8, 'Celgene');
INSERT INTO "laboratorio" ("id_laboratorio", "nombre") VALUES (9, 'Johnson & Johnson');

-- --------------------------------------------------------
-- Tabla: `presentacion`
-- --------------------------------------------------------
DROP TABLE IF EXISTS "presentacion";
CREATE TABLE "presentacion" (
	"id_presentacion"	INTEGER,
	"nombre"	TEXT NOT NULL,
	PRIMARY KEY("id_presentacion" AUTOINCREMENT)
);

-- Datos para `presentacion`
INSERT INTO "presentacion" ("id_presentacion", "nombre") VALUES (7, 'Cápsulas, frasco con 30 unidades');
INSERT INTO "presentacion" ("id_presentacion", "nombre") VALUES (8, 'Tabletas efervescentes, tubo con 20 tabletas');
INSERT INTO "presentacion" ("id_presentacion", "nombre") VALUES (9, ' Cápsulas de liberación retardada, caja con 14 unidades');
INSERT INTO "presentacion" ("id_presentacion", "nombre") VALUES (10, ' Jarabe, botella de 120 ml');
INSERT INTO "presentacion" ("id_presentacion", "nombre") VALUES (11, 'Comprimidos, blister con 10 tabletas');

-- --------------------------------------------------------
-- Tabla: `tipo_producto`
-- --------------------------------------------------------
DROP TABLE IF EXISTS "tipo_producto";
CREATE TABLE "tipo_producto" (
	"id_tip_prod"	INTEGER,
	"nombre"	TEXT NOT NULL,
	PRIMARY KEY("id_tip_prod" AUTOINCREMENT)
);

-- Datos para `tipo_producto`
INSERT INTO "tipo_producto" ("id_tip_prod", "nombre") VALUES (6, 'Antibiótico de amplio espectro');
INSERT INTO "tipo_producto" ("id_tip_prod", "nombre") VALUES (7, 'Analgésico y antiinflamatorio no esteroideo (AINE)');
INSERT INTO "tipo_producto" ("id_tip_prod", "nombre") VALUES (8, 'Antihistamínico');
INSERT INTO "tipo_producto" ("id_tip_prod", "nombre") VALUES (9, 'Analgésico y antipirético');
INSERT INTO "tipo_producto" ("id_tip_prod", "nombre") VALUES (10, 'Suplemento vitamínico');
INSERT INTO "tipo_producto" ("id_tip_prod", "nombre") VALUES (11, ' Inhibidor de la bomba de protones (IBP)');

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

-- Datos para `proveedor`
INSERT INTO "proveedor" ("id_proveedor", "nombre", "telefono", "correo", "direccion", "avatar") VALUES (2, 'Almacen', 4247217960, 'hola@correo.com', 'carrera 12 entre calle 6 y 7', 'ProveedorDefault.png');

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
	"prod_present"	INTEGER NOT NULL,
	FOREIGN KEY("prod_tip_prod") REFERENCES "tipo_producto"("id_tip_prod"),
	FOREIGN KEY("prod_present") REFERENCES "presentacion"("id_presentacion"),
	FOREIGN KEY("prod_lab") REFERENCES "laboratorio"("id_laboratorio"),
	PRIMARY KEY("id_producto" AUTOINCREMENT)
);

-- Datos para `producto`
INSERT INTO "producto" ("id_producto", "nombre", "concentracion", "adicional", "precio", "avatar", "prod_lab", "prod_tip_prod", "prod_present") VALUES (50, 'Omeprazol', '500 mg/ml', 'Ejemplo', 150, 'ProductDefault.png', 3, 10, 7);

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

-- Datos para `lote`
INSERT INTO "lote" ("id_lote", "stock", "vencimiento", "id_lote_prod", "lote_id_prov", "cod_lote") VALUES (1, 6, '2025-03-29', 50, 2, 5020);
INSERT INTO "lote" ("id_lote", "stock", "vencimiento", "id_lote_prod", "lote_id_prov", "cod_lote") VALUES (2, 200, '2025-10-29', 50, 2, 540001);

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
	"vendedor"	INTEGER NOT NULL,
	FOREIGN KEY("vendedor") REFERENCES "usuario"("id_usuario"),
	PRIMARY KEY("id_venta" AUTOINCREMENT)
);

-- Datos para `venta`
INSERT INTO "venta" ("id_venta", "fecha", "cliente", "ci", "total", "vendedor") VALUES (5, '2025-03-09 16:47:49', 'MAIKER DANIEL BRAVO CHACO', 29674336, 150, 1);

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

-- Datos para `venta_producto`
INSERT INTO "venta_producto" ("id_ventaproducto", "cantidad", "subtotal", "producto_id_producto", "venta_id_venta") VALUES (6, 1, 150, 50, 5);

COMMIT;
PRAGMA foreign_keys = ON;
