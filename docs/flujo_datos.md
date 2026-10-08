# Flujo de Datos Extremo a Extremo (End-to-End)

Este documento detalla el ciclo completo de vida de los datos en SIMAP, explicando la trazabilidad pedagógica requerida por la skill **`code-docs`**.

---

## El Hilo Conductor de la Arquitectura

```mermaid
flowchart TD
    subgraph 1_Vista ["1. Vista (Formulario HTML)"]
        F["Formulario HTML (#form-crear)"]
        F1["Campo #nombre (Input)"]
        F2["Campo #codigo (Input)"]
        F3["Campo #estado (Select)"]
    end

    subgraph 2_Cliente ["2. Cliente JS (Controlador Frontend)"]
        JS["Script AJAX (#form-crear submit)"]
        FD["FormData Payload: { funcion: 'crear', nombre, codigo, estado }"]
    end

    subgraph 3_Backend ["3. Controlador PHP (Backend)"]
        CTRL["ProductoController.php (case 'crear')"]
        VLD["Saneamiento trim() y Validación de vacíos"]
    end

    subgraph 4_Modelo ["4. Modelo PDO (Persistencia)"]
        MDL["Producto.php -> crear()"]
        TX["Transacción Atómica (beginTransaction)"]
        STMT["PDO Prepared Statement: INSERT INTO ... (:nombre, ...)"]
    end

    subgraph 5_DB ["5. Almacén SQLite"]
        DB[("simap.db (Modo WAL)")]
    end

    F -->|Submit| JS
    F1 -.-> FD
    F2 -.-> FD
    F3 -.-> FD
    JS -->|Empaqueta| FD
    FD -->|HTTP POST| CTRL
    CTRL -->|Valida| VLD
    VLD -->|Invoca| MDL
    MDL -->|Abre TX| TX
    TX -->|Ejecuta| STMT
    STMT -->|Persiste| DB
    DB -.->|Confirmación| MDL
    MDL -.->|commit()| CTRL
    CTRL -.->|Respuesta 'add' / JSON| JS
    JS -.->|SweetAlert2 Éxito| F
```

---

## 1. Capa de Vista: El Formulario HTML

En los archivos dentro de `assets/pages/*.php`, cada formulario define la estructura visible que el usuario interactúa.

**Regla de Documentación:**
- Comentario de bloque arriba del `<form>` indicando su ID, propósito funcional y hacia qué script JS se dirige.
- Cada campo de entrada (`<input>`, `<select>`, `<textarea>`) debe documentar:
  - Su identificador del DOM (`id` o `name`).
  - El dato que recopila.
  - El nombre de la variable en la que se convertirá en el cliente JS.

---

## 2. Capa de Cliente: El Despachador JavaScript

Ubicado en `assets/libs/js/*.js`. Captura el evento de envío del formulario (`submit`), previene la recarga de página por defecto con `e.preventDefault()`, extrae los valores de los inputs, los valida en frontend y los empaqueta.

**Regla de Documentación (JSDoc):**
- `@function`: Nombre de la función o selector que maneja el evento.
- `@flow`: Secuencia paso a paso de extracción, empaquetado en `FormData` o JSON, método HTTP (`POST`/`GET`), ruta del endpoint y manejo de respuesta.

---

## 3. Capa de Controlador: El Orquestador PHP

Ubicado en `assets/controller/*.php`. Recibe la petición HTTP, identifica la acción a ejecutar (generalmente mediante `$_POST['funcion']`), limpia los datos de entrada contra inyecciones y valida presencia de campos mínimos obligatorios.

**Regla de Documentación (PHPDoc):**
- `@route`: Método HTTP y archivo de destino.
- `@param`: Detalle de cada campo esperado en `$_POST` o `$_GET`.
- `FLUJO`: Pasos de saneamiento, llamada a la instancia del modelo y formato de respuesta emitida (`echo`).

---

## 4. Capa de Modelo: La Persistencia PDO en SQLite

Ubicada en `assets/db/*.php`. Contiene las clases de acceso a datos (`Usuario`, `Producto`, `Proveedor`, `Despacho`, etc.).

**Regla de Documentación:**
- Documentación de clase y métodos con `@param`, `@return`, `@throws`.
- Detalle explícito de la **transacción atómica** (`beginTransaction`, `commit`, `rollBack`).
- Sentencia preparada con placeholders de PDO (`:nombre`, `:codigo`).
