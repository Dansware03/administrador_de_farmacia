---
name: code-docs
description: >-
  Estándar y guía integral de documentación en código (in-code documentation) y generación
  automática de manuales técnicos (Web interactiva y PDF corporativo). Enseña al agente
  a documentar de forma pedagógica y trazable el flujo completo de datos extremo a extremo
  (Formulario HTML -> AJAX/Fetch JS -> Controlador PHP -> Modelo PDO -> Base de Datos SQLite),
  así como archivos, clases, métodos, funciones y estructuras relacionales en español.
---

# Skill: code-docs (Estándar de Documentación en Código SIMAP)

Esta skill establece las directrices obligatorias para documentar código dentro del proyecto de forma pedagógica, estructurada y fácil de entender para cualquier programador, evaluador o auditor.

---

## 1. El Hilo Conductor Obligatorio (Flujo Extremo a Extremo)

En aplicaciones web MVC nativas, documentar únicamente tipos de datos (`@param string $x`) es insuficiente. Cada funcionalidad debe documentar **la trazabilidad del flujo de datos**:
1. **Vista / Formulario HTML:** Qué campos solicita (`<form>`), con qué IDs y a qué variables corresponden.
2. **Cliente JavaScript (AJAX):** Cómo se capturan esas variables en el DOM, qué método HTTP (`POST`/`GET`) se usa y a qué endpoint se despachan.
3. **Controlador PHP:** Cómo recibe la petición, qué validaciones y saneamientos aplica (`trim`, tipos) y a qué modelo delega.
4. **Modelo PHP:** Cómo se orquesta la transacción atómica, qué reglas de negocio se verifican y qué sentencia preparada PDO (`:param`) persiste en SQLite.

---

## 2. Plantillas de Documentación por Capa

### A. Encabezado de Archivo (PHP y JavaScript)
Todo archivo debe comenzar con un bloque descriptivo claro:

```php
/**
 * ============================================================================
 * ARCHIVO: ProductoController.php
 * CAPA: Controlador (Backend / Orquestación HTTP)
 * DESCRIPCIÓN: Gestiona las peticiones asíncronas para el catálogo de insumos
 *              médicos (creación, edición, consulta y verificación de stock).
 * ENTRADA: Peticiones POST desde assets/libs/js/producto.js y carrito.js.
 * SALIDA: Respuestas en formato JSON o estados en texto plano ('add', 'edit').
 * DEPENDENCIAS: assets/db/producto.php (Modelo Producto).
 * ============================================================================
 */
```

---

### B. Formulario HTML (`assets/pages/*.php`)
Documentar la cabecera del formulario y el propósito de cada campo:

```html
<!-- ==========================================================================
     FORMULARIO: Registro de Nuevo Insumo / Producto (#form-crear)
     FLUJO: Capturado por assets/libs/js/producto.js
            -> Enviado vía AJAX POST a assets/controller/ProductoController.php [funcion=crear]
     CAMPOS:
       - #nombre: Denominación comercial o genérica del insumo (Variable: nombre)
       - #concentracion: Concentración química o física (Variable: concentracion)
       - #prod_lab: ID del laboratorio o fabricante (Variable: prod_lab)
       - #prod_tip_prod: ID de categoría o tipo de insumo (Variable: prod_tip_prod)
       - #prod_present: ID de presentación de empaque (Variable: prod_present)
     ========================================================================== -->
<form id="form-crear">
  <!-- Campo Nombre -->
  <div class="mb-3">
    <label for="nombre" class="form-label">Nombre del Insumo <span class="text-danger">*</span></label>
    <input type="text" id="nombre" class="form-control" required placeholder="Ej: Alcohol Isopropílico">
  </div>
  ...
</form>
```

---

### C. Cliente JavaScript / AJAX (`assets/libs/js/*.js`)
Documentar la función con etiquetas JSDoc y desglose del flujo:

```javascript
/**
 * @function registrarInsumo
 * @description Captura el evento submit del formulario #form-crear, valida campos
 *              obligatorios en frontend y despacha el payload vía POST al controlador.
 * @flow
 *   1. DOM: Lee valores de #nombre, #concentracion, #prod_lab, etc.
 *   2. Validación Frontend: Verifica que no existan campos vacíos críticos.
 *   3. Payload: Empaqueta en FormData { funcion: 'crear', nombre, concentracion, ... }.
 *   4. Endpoint: HTTP POST ../controller/ProductoController.php.
 *   5. Respuesta: Si recibe 'add', dispara SweetAlert2 de éxito y recarga el listado.
 * @param {Event} e - Evento del formulario submit.
 * @returns {void}
 */
$('#form-crear').submit(function(e) {
    e.preventDefault();
    ...
});
```

---

### D. Controlador PHP (`assets/controller/*.php`)
Documentar cada caso de uso o endpoint:

```php
/**
 * CASO DE USO: Crear Insumo / Producto
 * ----------------------------------------------------------------------------
 * @route POST assets/controller/ProductoController.php
 * @param string $_POST['funcion'] Debe ser 'crear'.
 * @param string $_POST['nombre'] Nombre comercial del insumo.
 * @param string $_POST['concentracion'] Concentración (opcional).
 * @param int    $_POST['prod_lab'] ID del laboratorio fabricante.
 * @param int    $_POST['prod_tip_prod'] ID del tipo de producto.
 * @param int    $_POST['prod_present'] ID de la presentación.
 * 
 * FLUJO DE EJECUCIÓN:
 *   1. Extrae y sanea variables con trim() y casting numérico.
 *   2. Valida presencia de campos obligatorios.
 *   3. Invoca $producto->crear(...).
 *   4. Imprime respuesta 'add' o mensaje de error capturado.
 * ----------------------------------------------------------------------------
 */
case 'crear':
    ...
    break;
```

---

### E. Modelo PHP (`assets/db/*.php`)
Documentar clases y métodos siguiendo el estándar PHPDoc PSR-5 / PSR-19 con transacciones:

```php
/**
 * Clase Producto
 *
 * Modelo de persistencia para el catálogo de insumos médicos en SQLite.
 */
class Producto {
    /**
     * Registra un nuevo insumo médico en la base de datos de forma atómica.
     *
     * FLUJO DE PERSISTENCIA:
     *   1. Verifica si ya existe un insumo con idéntico nombre y laboratorio.
     *   2. Inicia transacción atómica en PDO ($this->acceso->beginTransaction()).
     *   3. Inserta registro en la tabla `producto` con sentencia preparada.
     *   4. Si se incluye información de lote inicial, inserta en la tabla `lote`.
     *   5. Confirma la transacción con commit() o revierte en catch con rollBack().
     *
     * @param string     $nombre               Nombre comercial del insumo.
     * @param string     $concentracion        Concentración o especificación técnica.
     * @param string     $adicional            Información adicional de uso.
     * @param string     $avatar               Nombre del archivo de imagen.
     * @param int        $prod_lab             ID del laboratorio foráneo.
     * @param int        $prod_tip_prod        ID del tipo de producto foráneo.
     * @param int        $prod_present         ID de la presentación foránea.
     * @param int        $id_unidad            ID de la unidad de medida (defecto 1).
     * @param string     $especificacion_talla Talla o detalle métrico.
     * @param array|null $lote_data            Datos opcionales de lote inicial.
     * @return void
     * @throws Exception Si el insumo ya existe o falla la inserción en BD.
     */
    public function crear($nombre, $concentracion, $adicional, $avatar, $prod_lab, $prod_tip_prod, $prod_present, $id_unidad, $especificacion_talla, $lote_data = null) {
        ...
    }
}
```

---

## 3. Generación Automática de Documentación (Web y PDF)

El proyecto incluye dos mecanismos autónomos sin dependencias pesadas:

1. **Portal Web Interactivo (Docsify):**
   - Acceder en el navegador a: `http://localhost/administrador_de_farmacia/docs/`
   - Renderiza en tiempo real los documentos en `docs/` con buscador, índice dinámico y diagramas Mermaid.
2. **Generador y Exportador a PDF (`docs/generate.php`):**
   - Ejecutar en terminal: `php docs/generate.php`
   - Escanea el código del proyecto, extrae los DocBlocks y compila `docs/manual_tecnico.html`.
   - Abre `http://localhost/administrador_de_farmacia/docs/manual_tecnico.html` y pulsa **Ctrl + P** para exportar el **PDF Corporativo de Alta Calidad** con saltos de página y diseño institucional.
