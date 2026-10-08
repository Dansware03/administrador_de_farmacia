# SIMAP - Sistema de Inventario de Materiales e Insumos de Protección

> **Institución:** Clínica Popular Especializada La Fría  
> **Arquitectura:** PHP 8 Nativo + SQLite 3 (WAL) + Vanilla JS + Bootstrap 5  
> **Filosofía de Desarrollo:** Ponytail (Máxima eficiencia, trazabilidad pedagógica, cero dependencias innecesarias)  
> **Autoría:** Grupo de Proyecto &bull; Versión 1.0.0

---

## 1. Visión General del Proyecto

**SIMAP** es un sistema web local diseñado para controlar de manera estricta y transparente la dotación, almacenamiento y despacho de insumos médicos, equipos de protección individual (EPI) y materiales sanitarios de la **Clínica Popular Especializada La Fría**.

El sistema fue concebido para garantizar trazabilidad médica de punta a punta, erradicando el desvío de insumos y optimizando el ciclo de entrega mediante el algoritmo de despacho **FEFO** (*First Expired, First Out*).

### Pilares de Ingeniería y Arquitectura

1. **Backend Nativo en PHP 8:**
   - Cero frameworks pesados o sobrecargas innecesarias.
   - Orquestación desacoplada basada en controladores de acción (`assets/controller/`) y modelos PDO (`assets/db/`).
   - Saneamiento y validación estricta de parámetros en todas las fronteras de confianza.

2. **Base de Datos Embebida de Alto Rendimiento (SQLite 3 WAL):**
   - Modo `WAL` (*Write-Ahead Logging*) para concurrencia óptima de lectura/escritura sin contenciones de bloqueo.
   - Forzado activo de integridad referencial (`PRAGMA foreign_keys = ON;`).
   - Esquema relacional canónico de 13 tablas con claves foráneas e índices estructurados.

3. **Seguridad Criptográfica y Defensiva:**
   - 100% de consultas parametrizadas mediante **PDO Prepared Statements** contra inyecciones SQL.
   - Almacenamiento de contraseñas con derivación criptográfica segura mediante `password_hash($pass, PASSWORD_BCRYPT)`.
   - Cabeceras de seguridad HTTP (`X-Frame-Options: DENY`, `X-Content-Type-Options: nosniff`, `Content-Security-Policy`).

4. **Instalación y Diagnóstico Autónomos:**
   - Asistente guiado de instalación (`install.php`) para inicializar el entorno y crear el usuario administrador inicial.
   - Suite de calidad y auditoría de seguridad automatizada (`check.php`) que verifica sintaxis PHP/JS, prepared statements, autenticación e integridad SQLite con cero dependencias externas.

---

## 2. Mapa de Directorios y Estructura del Proyecto

```text
administrador_de_farmacia/
├── assets/
│   ├── controller/          # Orquestación Backend (11 controladores PHP HTTP/AJAX)
│   ├── db/                  # Capa de Persistencia y Modelos PDO
│   │   ├── conexion.php     # Conector singleton PDO para SQLite WAL
│   │   ├── schema.sql       # Esquema canónico DDL de 13 tablas relacionales
│   │   └── simap.db         # Archivo binario de base de datos local
│   ├── libs/                # Recursos estáticos offline (CSS, Vanilla JS, iconos)
│   │   ├── css/             # Hojas de estilo personalizadas (app.css, main.css, login.css)
│   │   └── js/              # Controladores de cliente en Vanilla JS (14 módulos)
│   └── pages/               # Vistas, formularios e interfaces del sistema (HTML/PHP)
│       └── layouts/         # Componentes transversales reutilizables (header, nav, footer)
├── docs/                    # Portal de Documentación Técnica y Manuales
│   ├── index.html           # Portal interactivo Docsify (Zero-Build)
│   ├── generate.php         # Script extractor y generador dinámico de manuales
│   ├── manual_tecnico.html  # Manual técnico unificado optimizado para exportación a PDF
│   ├── controladores.md     # Documentación técnica de controladores backend
│   ├── modelos.md           # Documentación técnica de modelos y base de datos
│   └── flujo_datos.md       # Diagrama y trazabilidad del flujo de datos de extremo a extremo
├── index.php                # Punto de entrada principal y pantalla de autenticación
├── install.php              # Asistente interactivo de instalación del sistema
├── check.php                # Suite de calidad de código y auditoría de seguridad
└── biome.json               # Configuración de linter y formateo JavaScript
```

---

## 3. Navegación por la Documentación

Utilice la barra lateral para explorar en detalle cada una de las secciones del sistema:

- **[Flujo de Datos Extremo a Extremo](flujo_datos.md):** Trazabilidad pedagógica desde el DOM hasta SQLite pasando por AJAX y PDO.
- **[Controladores del Sistema (Backend)](controladores.md):** Catálogo de endpoints, acciones `POST`/`GET` y parámetros.
- **[Modelos y Base de Datos (Persistencia)](modelos.md):** Métodos de negocio, transacciones atómicas y esquema relacional.
- **[Manual Técnico Completo (PDF)](manual_tecnico.html):** Versión unificada y estilizada para impresión corporativa.
