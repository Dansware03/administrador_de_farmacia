# SIMAP - Sistema de Inventario de Materiales e Insumos de Protección

> **Institución:** Clínica Popular Especializada La Fría  
> **Arquitectura:** PHP 8 Nativo + SQLite 3 + Vanilla JS + Bootstrap 5  
> **Filosofía de Desarrollo:** Ponytail (Eficiencia, minimalismo, cero dependencias innecesarias)

---

## 1. Visión General del Proyecto

**SIMAP** es un sistema web local diseñado para controlar de manera estricta y transparente la dotación, almacenamiento y despacho de insumos médicos, equipos de protección individual (EPI) y materiales sanitarios de la Clínica Popular Especializada La Fría.

### Pilares Técnicos
- **Sin Frameworks Pesados:** Todo el backend está desarrollado en PHP 8 estándar aprovechando el rendimiento nativo del lenguaje.
- **Base de Datos Embebida de Alto Rendimiento:** Utiliza SQLite 3 en modo `WAL` (Write-Ahead Logging) y claves foráneas activas (`PRAGMA foreign_keys = ON;`), eliminando la sobrecarga de servidores de bases de datos pesados como MySQL en entornos locales.
- **Seguridad Criptográfica:** Uso estricto de contraseñas hasheadas mediante `password_hash($pass, PASSWORD_BCRYPT)` y sentencias preparadas PDO parametrizadas contra inyecciones SQL.
- **Asistente de Instalación Autónomo:** Incluye un instalador asistido (`install.php`) que verifica extensiones de PHP, inicializa el esquema SQLite e inscribe al superadministrador.

---

## 2. Mapa de Directorios

```text
administrador_de_farmacia/
├── assets/
│   ├── controller/      # Controladores PHP (Recepción de peticiones HTTP/AJAX)
│   ├── db/              # Capa de Datos PDO (Modelos y conexión a SQLite)
│   │   ├── conexion.php # Conexión singleton PDO configurada para SQLite WAL
│   │   ├── schema.sql   # Esquema canónico DDL de las 13 tablas relacionales
│   │   └── simap.db     # Archivo binario de base de datos local
│   ├── libs/            # Librerías estáticas offline (CSS, JS, iconos, imágenes)
│   └── pages/           # Vistas y formularios del sistema (HTML / PHP)
├── docs/                # Portal de Documentación Técnica y Manuales
│   ├── index.html       # Portal interactivo Docsify (Zero-Build)
│   ├── generate.php     # Script extractor y generador de manual imprimible
│   └── manual_tecnico.html # Manual unificado optimizado para exportación a PDF
├── index.php            # Entrada principal y pantalla de autenticación
├── install.php          # Asistente interactivo de instalación del sistema
└── check.php            # Suite de calidad de código y auditoría de seguridad
```
