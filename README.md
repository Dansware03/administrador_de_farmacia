# SIMAP — Sistema de Inventario de Materiales, Artículos de Mantenimiento y Protección Hospitalaria
**Clínica Popular Especializada La Fría (Estado Táchira, Venezuela)**

---

## Descripción del Sistema
SIMAP es una plataforma web desarrollada a la medida para la administración, trazabilidad y control de dotaciones institucionales de insumos de bioseguridad, materiales de limpieza hospitalaria y repuestos de mantenimiento.

El sistema opera bajo una arquitectura **100% offline**, sin depender de conexión a Internet ni gestores de paquetes externos, garantizando operatividad continua en entornos asistenciales.

---

## Roles y Control de Acceso
El sistema implementa estrictamente **2 roles de usuario**:

1. **Administrador (`us_tipo = 1`):**
   - Control total del inventario general y catálogo de insumos.
   - Administración de lotes, fechas de vencimiento y proveedores.
   - Configuración de áreas hospitalarias y niveles de riesgo biológico.
   - Gestión y auditoría de personal (creación y ascenso/descenso de cuentas).
   - Consulta, edición y emisión de actas oficiales de entrega/despacho.

2. **Secretario (`us_tipo = 2`):**
   - Consulta de catálogo institucional de insumos disponibles.
   - Elaboración y solicitud de pedidos de dotación para áreas del centro de salud.
   - Gestión de datos de su cuenta y actualización de credenciales.

---

## Tecnologías Utilizadas

- **Backend:** PHP 8.3 (Nativo, arquitectura modular Modelo-Controlador).
- **Base de Datos:** SQLite 3 (`simap.db`, almacén local embebido de alto rendimiento).
- **Frontend:** HTML5 semántico, JavaScript Vanilla / jQuery y CSS nativo.
- **Framework de Diseño:** Bootstrap 5.3.3 (distribución local offline) con Bootstrap Icons.
- **Plugins Locales:** DataTables, SweetAlert2, Select2, Toastr, Animate.css.
- **Emisión Documental:** Generación e impresión de Actas Oficiales de Entrega mediante estándar nativo HTML5 Print (sin dependencias pesadas de librerías PDF).

---

## Estructura Principal del Proyecto

```text
├── assets/
│   ├── controller/      # Controladores de lógica de negocio (Login, Usuario, Lote, Venta, etc.)
│   ├── db/              # Modelos PDO y base de datos local SQLite (simap.db, simap_completa.sql)
│   ├── libs/            # Librerías estáticas offline (Bootstrap 5, CSS, JS, imágenes e iconos)
│   └── pages/           # Vistas y formularios del sistema (Catálogo, Lotes, Retiros, Usuarios)
├── LICENSE              # Licencia del proyecto
├── README.md            # Ficha técnica oficial
├── index.php            # Portal de inicio de sesión y validación de seguridad
└── install.php          # Asistente de instalación autónomo
```

---

## Instalación y Ejecución Local

1. Clonar o copiar el directorio del proyecto dentro del servidor local (ej: `C:\laragon\www\administrador_de_farmacia`).
2. Iniciar el servicio Apache en Laragon o XAMPP con PHP 8.1+.
3. Abrir el navegador e ingresar a: `http://localhost/administrador_de_farmacia/`.
4. El sistema utiliza automáticamente la base de datos local SQLite ubicada en `assets/db/simap.db`.
