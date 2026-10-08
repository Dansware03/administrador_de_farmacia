PRAGMA foreign_keys = ON;

-- 1. Catálogo de Tipos de Usuario (Roles)
CREATE TABLE IF NOT EXISTS "tipo_us" (
    "id_tipo_us" INTEGER PRIMARY KEY AUTOINCREMENT,
    "nombre_tipo" TEXT NOT NULL
);

INSERT INTO "tipo_us" ("id_tipo_us", "nombre_tipo") VALUES 
(1, 'Administrador'),
(2, 'Secretario');

-- 2. Tabla de Usuarios
CREATE TABLE IF NOT EXISTS "usuario" (
    "id_usuario" INTEGER PRIMARY KEY AUTOINCREMENT,
    "nombre_us" TEXT NOT NULL,
    "apellidos_us" TEXT NOT NULL,
    "fecha_nacimiento" DATE NOT NULL,
    "ci_us" TEXT UNIQUE NOT NULL,
    "contrasena_us" TEXT NOT NULL,
    "telefono_us" TEXT,
    "correo_us" TEXT,
    "genero_us" TEXT,
    "info_us" TEXT,
    "avatar" TEXT DEFAULT 'user-default.png',
    "us_tipo" INTEGER NOT NULL,
    FOREIGN KEY("us_tipo") REFERENCES "tipo_us"("id_tipo_us")
);

-- 3. Catálogo de Laboratorios / Fabricantes
CREATE TABLE IF NOT EXISTS "laboratorio" (
    "id_laboratorio" INTEGER PRIMARY KEY AUTOINCREMENT,
    "nombre" TEXT NOT NULL
);

INSERT INTO "laboratorio" ("id_laboratorio", "nombre") VALUES
(1, 'INTERSOS (Organización Humanitaria)'),
(2, 'UNICEF (Fondo de las Naciones Unidas)'),
(3, 'FUNREAHV (Ayuda Humanitaria)'),
(4, 'MPPS (Ministerio de Salud)'),
(5, 'Almacén Central Regional'),
(6, '3M Bioseguridad'),
(7, 'Kimberly-Clark Professional'),
(8, 'Genérico / Nacional');

-- 4. Catálogo de Tipos / Categorías de Insumos
CREATE TABLE IF NOT EXISTS "tipo_producto" (
    "id_tip_prod" INTEGER PRIMARY KEY AUTOINCREMENT,
    "nombre" TEXT NOT NULL
);

INSERT INTO "tipo_producto" ("id_tip_prod", "nombre") VALUES
(1, 'Productos Químicos y Detergentes'),
(2, 'Equipos y Utensilios de Limpieza'),
(3, 'Equipos de Protección Individual (EPI / EPP)'),
(4, 'Productos de Papel e Higiene'),
(5, 'Repuestos y Herramientas de Mantenimiento');

-- 5. Catálogo de Presentaciones
CREATE TABLE IF NOT EXISTS "presentacion" (
    "id_presentacion" INTEGER PRIMARY KEY AUTOINCREMENT,
    "nombre" TEXT NOT NULL
);

INSERT INTO "presentacion" ("id_presentacion", "nombre") VALUES
(1, 'Unidad / Pieza'),
(2, 'Litro (Envase de 1 L)'),
(3, 'Galón (3.785 L)'),
(4, 'Pimpina de 20 Litros'),
(5, 'Caja con 50 unidades'),
(6, 'Caja con 100 unidades'),
(7, 'Paquete de 4 rollos'),
(8, 'Paquete de 50 unidades'),
(9, 'Paquete de 100 unidades'),
(10, 'Bolsa de 1 Kg'),
(11, 'Frasco de 100 ml'),
(12, 'Kit Integral');

-- 6. Catálogo de Unidades de Medida
CREATE TABLE IF NOT EXISTS "unidad_medida" (
    "id_unidad" INTEGER PRIMARY KEY AUTOINCREMENT,
    "nombre" TEXT NOT NULL,
    "codigo" TEXT NOT NULL
);

INSERT INTO "unidad_medida" ("id_unidad", "nombre", "codigo") VALUES
(1, 'Unidad', 'und'),
(2, 'Litro', 'L'),
(3, 'Galón', 'gal'),
(4, 'Pimpina (20 Litros)', 'pmp'),
(5, 'Paquete', 'paq'),
(6, 'Caja', 'caja'),
(7, 'Kilogramo', 'kg'),
(8, 'Kit', 'kit'),
(9, 'Frasco', 'frc');

-- 7. Catálogo de Áreas Hospitalarias Receptoras
CREATE TABLE IF NOT EXISTS "area_servicio" (
    "id_area" INTEGER PRIMARY KEY AUTOINCREMENT,
    "nombre_area" TEXT NOT NULL
);

INSERT INTO "area_servicio" ("id_area", "nombre_area") VALUES
(1, 'Nutrición y Dietética'),
(2, 'Quirófano / Pabellón'),
(3, 'Emergencia y Triaje'),
(4, 'Hospitalización'),
(5, 'Mantenimiento y Servicios Generales'),
(6, 'Baños y Áreas Sanitarias'),
(7, 'Dirección y Administración'),
(8, 'Sala de Partos'),
(9, 'Laboratorio y Bioanálisis');

-- 8. Proveedores y Donantes
CREATE TABLE IF NOT EXISTS "proveedor" (
    "id_proveedor" INTEGER PRIMARY KEY AUTOINCREMENT,
    "nombre" TEXT NOT NULL,
    "telefono" TEXT NOT NULL,
    "correo" TEXT,
    "direccion" TEXT NOT NULL,
    "avatar" VARCHAR(255) DEFAULT 'ProveedorDefault.png'
);

-- 9. Catálogo de Productos / Insumos
CREATE TABLE IF NOT EXISTS "producto" (
    "id_producto" INTEGER PRIMARY KEY AUTOINCREMENT,
    "nombre" TEXT NOT NULL,
    "concentracion" TEXT,
    "adicional" TEXT,
    "avatar" TEXT DEFAULT 'ProductDefault.png',
    "prod_lab" INTEGER NOT NULL,
    "prod_tip_prod" INTEGER NOT NULL,
    "prod_present" INTEGER NOT NULL,
    "id_unidad" INTEGER DEFAULT 1,
    "especificacion_talla" TEXT DEFAULT NULL,
    FOREIGN KEY("prod_lab") REFERENCES "laboratorio"("id_laboratorio"),
    FOREIGN KEY("prod_tip_prod") REFERENCES "tipo_producto"("id_tip_prod"),
    FOREIGN KEY("prod_present") REFERENCES "presentacion"("id_presentacion"),
    FOREIGN KEY("id_unidad") REFERENCES "unidad_medida"("id_unidad")
);

-- 10. Lotes de Existencias e Inventario
CREATE TABLE IF NOT EXISTS "lote" (
    "id_lote" INTEGER PRIMARY KEY AUTOINCREMENT,
    "stock" INTEGER NOT NULL,
    "vencimiento" DATE NOT NULL,
    "id_lote_prod" INTEGER NOT NULL,
    "lote_id_prov" INTEGER NOT NULL,
    "cod_lote" INTEGER,
    FOREIGN KEY("id_lote_prod") REFERENCES "producto"("id_producto"),
    FOREIGN KEY("lote_id_prov") REFERENCES "proveedor"("id_proveedor")
);

-- 11. Cabecera de Despachos y Entregas
CREATE TABLE IF NOT EXISTS "despacho" (
    "id_despacho" INTEGER PRIMARY KEY AUTOINCREMENT,
    "fecha" DATETIME NOT NULL,
    "receptor" TEXT NOT NULL,
    "ci_receptor" TEXT NOT NULL,
    "responsable" INTEGER NOT NULL,
    "id_area" INTEGER DEFAULT NULL,
    "cargo_receptor" TEXT DEFAULT NULL,
    "observacion" TEXT DEFAULT NULL,
    FOREIGN KEY("responsable") REFERENCES "usuario"("id_usuario"),
    FOREIGN KEY("id_area") REFERENCES "area_servicio"("id_area")
);

-- 12. Relación de Despacho e Insumos Totales
CREATE TABLE IF NOT EXISTS "despacho_insumo" (
    "id_despacho_insumo" INTEGER PRIMARY KEY AUTOINCREMENT,
    "cantidad" INTEGER NOT NULL,
    "producto_id_producto" INTEGER NOT NULL,
    "despacho_id_despacho" INTEGER NOT NULL,
    FOREIGN KEY("producto_id_producto") REFERENCES "producto"("id_producto"),
    FOREIGN KEY("despacho_id_despacho") REFERENCES "despacho"("id_despacho") ON DELETE CASCADE
);

-- 13. Trazabilidad de Lotes Despachados
CREATE TABLE IF NOT EXISTS "detalle_despacho" (
    "id_detalle" INTEGER PRIMARY KEY AUTOINCREMENT,
    "det_cantidad" INTEGER NOT NULL,
    "det_vencimiento" DATE NOT NULL,
    "id_det_lote" INTEGER NOT NULL,
    "id_det_prod" INTEGER NOT NULL,
    "lote_id_prov" INTEGER NOT NULL,
    "id_det_despacho" INTEGER NOT NULL,
    FOREIGN KEY("id_det_despacho") REFERENCES "despacho"("id_despacho") ON DELETE CASCADE
);

-- Índices de Rendimiento
CREATE INDEX IF NOT EXISTS "idx_usuario_ci" ON "usuario"("ci_us");
CREATE INDEX IF NOT EXISTS "idx_producto_tipo" ON "producto"("prod_tip_prod");
CREATE INDEX IF NOT EXISTS "idx_lote_producto" ON "lote"("id_lote_prod");
CREATE INDEX IF NOT EXISTS "idx_lote_proveedor" ON "lote"("lote_id_prov");
CREATE INDEX IF NOT EXISTS "idx_despacho_fecha" ON "despacho"("fecha");
