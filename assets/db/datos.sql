BEGIN TRANSACTION;
CREATE TABLE IF NOT EXISTS "detalle_venta" (
	"id_detalle"	INTEGER,
	"det_cantidad"	INTEGER NOT NULL,
	"det_vencimiento"	DATE NOT NULL,
	"id_det_lote"	INTEGER NOT NULL,
	"id_det_prod"	INTEGER NOT NULL,
	"lote_id_prov"	INTEGER NOT NULL,
	"id_det_venta"	INTEGER NOT NULL,
	PRIMARY KEY("id_detalle" AUTOINCREMENT),
	FOREIGN KEY("id_det_venta") REFERENCES "venta"("id_venta")
);
CREATE TABLE IF NOT EXISTS "laboratorio" (
	"id_laboratorio"	INTEGER,
	"nombre"	TEXT NOT NULL,
	PRIMARY KEY("id_laboratorio" AUTOINCREMENT)
);
CREATE TABLE IF NOT EXISTS "lote" (
	"id_lote"	INTEGER,
	"stock"	INTEGER NOT NULL,
	"vencimiento"	DATE NOT NULL,
	"id_lote_prod"	INTEGER NOT NULL,
	"lote_id_prov"	INTEGER NOT NULL,
	"cod_lote"	INTEGER,
	PRIMARY KEY("id_lote" AUTOINCREMENT),
	FOREIGN KEY("id_lote_prod") REFERENCES "producto"("id_producto"),
	FOREIGN KEY("lote_id_prov") REFERENCES "proveedor"("id_proveedor")
);
CREATE TABLE IF NOT EXISTS "presentacion" (
	"id_presentacion"	INTEGER,
	"nombre"	TEXT NOT NULL,
	PRIMARY KEY("id_presentacion" AUTOINCREMENT)
);
CREATE TABLE IF NOT EXISTS "producto" (
	"id_producto"	INTEGER,
	"nombre"	TEXT NOT NULL,
	"concentracion"	TEXT,
	"adicional"	TEXT,
	"precio"	REAL,
	"avatar"	TEXT,
	"prod_lab"	INTEGER NOT NULL,
	"prod_tip_prod"	INTEGER NOT NULL,
	"prod_present"	INTEGER NOT NULL,
	PRIMARY KEY("id_producto" AUTOINCREMENT),
	FOREIGN KEY("prod_lab") REFERENCES "laboratorio"("id_laboratorio"),
	FOREIGN KEY("prod_present") REFERENCES "presentacion"("id_presentacion"),
	FOREIGN KEY("prod_tip_prod") REFERENCES "tipo_producto"("id_tip_prod")
);
CREATE TABLE IF NOT EXISTS "proveedor" (
	"id_proveedor"	INTEGER,
	"nombre"	TEXT NOT NULL,
	"telefono"	INTEGER NOT NULL,
	"correo"	TEXT,
	"direccion"	TEXT NOT NULL,
	"avatar"	VARCHAR(255),
	PRIMARY KEY("id_proveedor" AUTOINCREMENT)
);
CREATE TABLE IF NOT EXISTS "tipo_producto" (
	"id_tip_prod"	INTEGER,
	"nombre"	TEXT NOT NULL,
	PRIMARY KEY("id_tip_prod" AUTOINCREMENT)
);
CREATE TABLE IF NOT EXISTS "tipo_us" (
	"id_tipo_us"	INTEGER,
	"nombre_tipo"	TEXT NOT NULL,
	PRIMARY KEY("id_tipo_us" AUTOINCREMENT)
);
CREATE TABLE IF NOT EXISTS "venta" (
	"id_venta"	INTEGER,
	"fecha"	DATETIME,
	"cliente"	TEXT,
	"ci"	TEXT,
	"total"	REAL,
	"vendedor"	INTEGER NOT NULL,
	PRIMARY KEY("id_venta" AUTOINCREMENT),
	FOREIGN KEY("vendedor") REFERENCES "usuario"("id_usuario")
);
CREATE TABLE IF NOT EXISTS "venta_producto" (
	"id_ventaproducto"	INTEGER,
	"cantidad"	INTEGER NOT NULL,
	"subtotal"	REAL NOT NULL,
	"producto_id_producto"	INTEGER NOT NULL,
	"venta_id_venta"	INTEGER NOT NULL,
	PRIMARY KEY("id_ventaproducto" AUTOINCREMENT),
	FOREIGN KEY("producto_id_producto") REFERENCES "producto"("id_producto"),
	FOREIGN KEY("venta_id_venta") REFERENCES "venta"("id_venta")
);
COMMIT;