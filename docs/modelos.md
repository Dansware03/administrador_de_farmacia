# Modelos de Persistencia (Capa de Datos PDO)

> **Generado automáticamente a partir del código fuente PHP (`assets/db/`).**
> La capa de datos gestiona transacciones atómicas (ACID), consultas preparadas e integridad referencial en SQLite 3 (Modo WAL).

---

## Proveedor.php

**Descripción:** Modelo de Datos y Lógica de Negocio para Proveedores y Donantes Humanitarios - Administra las empresas proveedoras, donantes y organismos humanitarios
(INTERSOS, UNICEF, FUNREAHV, etc.) en la base de datos local SQLite.
Gestiona el catálogo de entidades, contactos, logos institucionales y validaciones de unicidad.
* Flujo de Integración Extremo a Extremo:
1. UI/Cliente: Formulario en gestión de proveedores (`assets/libs/js/proveedor.js`).
2. Petición HTTP: AJAX POST hacia `assets/controller/ProveedorController.php`.
3. Procesamiento Modelo: `Proveedor::crear`, `buscar`, `editar`, `borrar_prove`, `cambiar_avatar`.
4. Persistencia DB: Consultas preparadas PDO sobre tabla `proveedor` y eliminación física de logos.
5. Respuesta: Respuestas de estado ('add', 'borrado', etc.) o colección JSON al cliente.
*

### Métodos y Operaciones Disponibles

| Método | Parámetros | Propósito |
| :--- | :--- | :--- |
| `__construct()` | *Sin parámetros* | Modelo de Datos y Lógica de Negocio para Proveedores y Donantes Humanitarios |
| `crear()` | `$nombre, $telefono, $correo, $direccion, $avatar` | Registrar un nuevo proveedor o entidad donante. |
| `buscar()` | `$consulta = &#039;&#039;` | Buscar proveedores por filtro de coincidencia o listar los primeros registros. |
| `editar()` | `$id_proveedor, $nombre, $telefono, $correo, $direccion` | Editar los datos informativos de un proveedor. |
| `borrar_prove()` | `$id` | Eliminar un proveedor y remover su archivo de logotipo del almacenamiento local. |
| `cambiar_avatar()` | `$id, $nombre` | Actualizar la ruta del logotipo o avatar del proveedor. |
| `rellenar_proveedor()` | *Sin parámetros* | Obtener el catálogo completo de proveedores ordenados alfabéticamente. |

---

## despacho.php

**Descripción:** Modelo de Datos y Lógica de Negocio para Despachos y Actas de Entrega - Administra el ciclo de vida completo de las actas de despacho institucional de insumos médicos.
Implementa control transaccional estricto (ACID), deducción de inventario bajo algoritmo FEFO
(First Expired, First Out - Primero en Vencer, Primero en Salir) a nivel de lotes,
reversión íntegra de existencias al anular actas y recálculo automático en modificaciones.
* Flujo de Integración Extremo a Extremo:
1. UI/Cliente: Módulo de despachos / carrito (`assets/libs/js/despacho.js`, `carrito.js`).
2. Petición HTTP: AJAX POST hacia `assets/controller/DespachoController.php`.
3. Procesamiento Modelo: `Despacho::registrar_despacho`, `revertir_despacho`, `actualizar_despacho`.
4. Persistencia DB: Tablas `despacho`, `despacho_insumo`, `detalle_despacho` y actualización de `lote`.
5. Respuesta: Retorno de identificador o confirmación JSON al controlador.
*

### Métodos y Operaciones Disponibles

| Método | Parámetros | Propósito |
| :--- | :--- | :--- |
| `__construct()` | *Sin parámetros* | Modelo de Datos y Lógica de Negocio para Despachos y Actas de Entrega |
| `registrar_despacho()` | `$receptor, $ci_receptor, $responsable, $productos, $id_area = null, $cargo_receptor = &#039;&#039;, $observacion = &#039;&#039;` | Registrar un nuevo despacho y deducir existencias según vencimiento (FEFO). |
| `actualizar_stock_por_lotes()` | `$id_producto, $cantidad_requerida, $id_despacho` | Deducir existencias de lotes priorizando fechas de caducidad próximas (Algoritmo FEFO). |
| `listar_despachos()` | *Sin parámetros* | Listar todos los despachos con datos del responsable y área asignada. |
| `listar_despachos_por_fechas()` | `$fecha_inicio, $fecha_fin` | Filtrar despachos institucionales por rango de fechas. |
| `ver_detalle_despacho()` | `$id_despacho` | Obtener el detalle de insumos despachados por acta de entrega. |
| `obtener_despacho()` | `$id_despacho` | Obtener los datos de cabecera de un despacho específico. |
| `revertir_despacho()` | `$id_despacho` | Anular un despacho y restituir íntegramente las existencias en sus respectivos lotes. |
| `obtener_stock_lote()` | `$id_producto, $id_lote` | Consultar el stock disponible actual de un lote determinado. |
| `actualizar_despacho()` | `$id_despacho, $receptor, $ci_receptor, $productos, $id_area = null, $cargo_receptor = &#039;&#039;, $observacion = &#039;&#039;` | Actualizar datos del acta y recalcular inventario atómicamente. |

---

## laboratory.php

**Descripción:** Modelo de Datos y Lógica de Negocio para Laboratorios Fabricantes - Administra el catálogo de marcas y laboratorios farmacéuticos productores
de insumos y medicamentos. Provee operaciones CRUD con validaciones
de unicidad de nombre y verificación de integridad referencial previo al borrado.
* Flujo de Integración Extremo a Extremo:
1. UI/Cliente: Formulario en vista de laboratorios (`assets/libs/js/laboratorio.js`).
2. Petición HTTP: AJAX POST hacia `assets/controller/LaboratoryController.php`.
3. Procesamiento Modelo: `Laboratorio::crear`, `editar`, `borrar_lab` o `buscar`.
4. Persistencia DB: Consultas preparadas en tabla `laboratorio` y chequeo de FK en `producto`.
5. Respuesta: Retorno de datos o emisión directa de estado JSON al cliente.
*

### Métodos y Operaciones Disponibles

| Método | Parámetros | Propósito |
| :--- | :--- | :--- |
| `__construct()` | *Sin parámetros* | Modelo de Datos y Lógica de Negocio para Laboratorios Fabricantes |
| `crear()` | `$nombre` | Registrar un nuevo laboratorio fabricante. |
| `buscar()` | *Sin parámetros* | Buscar laboratorios por coincidencia de texto o listar los primeros registros. |
| `borrar_lab()` | `$id` | Eliminar un laboratorio garantizando integridad referencial. |
| `editar()` | `$nombre, $id_editado` | Actualizar el nombre descriptivo de un laboratorio existente. |
| `rellenar_laboratorio()` | *Sin parámetros* | Obtener el catálogo completo de laboratorios ordenados alfabéticamente. |

---

## lote.php

**Descripción:** Modelo de Datos y Lógica de Negocio para Lotes e Inventario - Administra los lotes de insumos médicos y farmacéuticos en la base de datos SQLite.
Gestiona existencias por lote, control de caducidad (insumos perecederos y no perecederos),
trazabilidad de proveedores/donantes y persistencia transaccional.
* Flujo de Integración Extremo a Extremo:
1. UI/Cliente: Formularios de registro y edición de lotes (`assets/libs/js/lote.js`, `gestion_lote.js`).
2. Petición HTTP: AJAX POST hacia `assets/controller/LoteController.php`.
3. Procesamiento Modelo: `Lote::crear`, `buscar`, `editar`, `borrar_lote`.
4. Persistencia DB: Operaciones sobre tabla `lote` con relaciones hacia `producto`, `proveedor`.
5. Respuesta: Notificaciones de texto plano ('add', 'edit', 'borrado') o colección JSON.
*

### Métodos y Operaciones Disponibles

| Método | Parámetros | Propósito |
| :--- | :--- | :--- |
| `__construct()` | *Sin parámetros* | Modelo de Datos y Lógica de Negocio para Lotes e Inventario |
| `crear()` | `$id_producto, $proveedor, $cod_lote, $stock, $vencimiento` | Registrar un nuevo lote de insumos médicos. |
| `buscar()` | *Sin parámetros* | Buscar lotes con filtro por nombre de insumo médico. |
| `editar()` | `$id_lote, $stock` | Actualizar la cantidad de existencias de un lote específico. |
| `borrar_lote()` | `$id` | Eliminar un lote de inventario dentro de una transacción atómica. |
| `verificar_existencia_lote()` | `$id` | Verificar la existencia de un lote específico por ID. |

---

## presentaciones.php

**Descripción:** Modelo de Datos y Lógica de Negocio para Presentaciones Farmacéuticas - Administra el catálogo maestro de presentaciones, formas farmacéuticas
y empaques de insumos y medicamentos (Ampolla, Frasco, Blíster, Caja x 50, etc.).
Implementa control de duplicados y validación de integridad referencial.
* Flujo de Integración Extremo a Extremo:
1. UI/Cliente: Formulario en vista de presentaciones (`assets/libs/js/presentacion.js`).
2. Petición HTTP: AJAX POST hacia `assets/controller/PresentacionesController.php`.
3. Procesamiento Modelo: `Presentacion::crear`, `buscar`, `editar`, `borrar_pre`.
4. Persistencia DB: Operaciones sobre tabla `presentacion` y validación de dependencias en `producto`.
5. Respuesta: Notificaciones de texto plano ('add', 'no add', 'edit', 'borrado') o colección JSON.
*

### Métodos y Operaciones Disponibles

| Método | Parámetros | Propósito |
| :--- | :--- | :--- |
| `__construct()` | *Sin parámetros* | Modelo de Datos y Lógica de Negocio para Presentaciones Farmacéuticas |
| `crear()` | `$nombre` | Registrar una nueva presentación farmacéutica. |
| `buscar()` | *Sin parámetros* | Buscar presentaciones por coincidencia de texto en el nombre. |
| `borrar_pre()` | `$id` | Eliminar una presentación garantizando integridad referencial. |
| `editar()` | `$nombre, $id_editado` | Actualizar la denominación de una presentación farmacéutica existente. |
| `rellenar_presentacion()` | *Sin parámetros* | Obtener el catálogo completo de presentaciones ordenadas alfabéticamente. |

---

## producto.php

**Descripción:** Modelo de Insumos y Materiales Médicos (Catálogo Central) - Administra el catálogo de insumos sanitarios, materiales de bioseguridad y EPIs.
Gestiona inserciones atómicas, consultas optimizadas con consolidación de inventario
para prevenir problemas N+1, y trazabilidad de almacenamiento.
*

### Métodos y Operaciones Disponibles

| Método | Parámetros | Propósito |
| :--- | :--- | :--- |
| `__construct()` | *Sin parámetros* | Modelo de Insumos y Materiales Médicos (Catálogo Central) |
| `crear()` | `$nombre, $concentracion, $adicional, $avatar = &#039;ProductDefault.png&#039;, $prod_lab = &#039;&#039;, $prod_tip_prod = &#039;&#039;, $prod_present = &#039;&#039;, $id_unidad = 1, $especificacion_talla = &#039;&#039;, $lote_data = null` | Registra un nuevo insumo y opcionalmente su primer lote de inventario en una transacción atómica. |
| `buscar()` | `$consulta = &#039;&#039;` | Consulta el inventario consolidado de insumos con búsqueda opcional por texto. |
| `cambiar_avatar()` | `$id, $nombre` | Actualiza la imagen representativa del insumo. |
| `editar()` | `$id_edit_prod, $nombre, $concentracion, $adicional, $prod_lab = &#039;&#039;, $prod_tip_prod = &#039;&#039;, $prod_present = &#039;&#039;, $id_unidad = 1, $especificacion_talla = &#039;&#039;` | Actualiza la ficha descriptiva y clasificaciones de un insumo. |
| `borrar_produts()` | `$id` | Elimina un producto y remueve su archivo de imagen asociado si no es el por defecto. |
| `obtener_stock()` | `$id` | Obtiene la sumatoria total del stock físico de todos los lotes de un producto. |
| `validar_existentes()` | `$ids = []` | Valida de manera masiva la existencia de un conjunto de IDs de productos en la base de datos. |

---

## type.php

**Descripción:** Modelo de Datos y Lógica de Negocio para Categorías y Tipos de Insumo - Administra el catálogo maestro de clasificaciones y familias de insumos médicos
(Productos Químicos, Equipos de Limpieza, Equipos de Protección Individual EPI,
Productos de Papel, Repuestos y Mantenimiento).
Implementa control de duplicados y validación de integridad referencial.
* Flujo de Integración Extremo a Extremo:
1. UI/Cliente: Formulario en vista de tipos (`assets/libs/js/tipo.js`).
2. Petición HTTP: AJAX POST hacia `assets/controller/TypeController.php`.
3. Procesamiento Modelo: `tipo_producto::crear`, `buscar`, `editar`, `borrar_type`.
4. Persistencia DB: Operaciones sobre tabla `tipo_producto` y verificación en `producto`.
5. Respuesta: Notificaciones de texto plano ('add', 'no add', 'edit', 'borrado') o colección JSON.
*

### Métodos y Operaciones Disponibles

| Método | Parámetros | Propósito |
| :--- | :--- | :--- |
| `__construct()` | *Sin parámetros* | Modelo de Datos y Lógica de Negocio para Categorías y Tipos de Insumo |
| `crear()` | `$nombre` | Registrar una nueva categoría o tipo de insumo médico. |
| `buscar()` | *Sin parámetros* | Buscar tipos de producto por coincidencia de texto. |
| `borrar_type()` | `$id` | Eliminar un tipo o categoría garantizando integridad referencial. |
| `editar()` | `$nombre, $id_editado` | Actualizar la denominación de una categoría existente. |
| `rellenar_type()` | *Sin parámetros* | Obtener el catálogo completo de tipos de producto ordenados alfabéticamente. |

---

## usuario.php

**Descripción:** Modelo de Datos y Lógica de Negocio para Usuarios y Seguridad - Administra el ciclo de vida de los operadores y administradores del sistema SIMAP.
Implementa autenticación robusta mediante Bcrypt (`password_verify`, `password_hash`),
gestión del perfil personal, control de roles (Administrador = 1, Secretario = 2),
transacciones atómicas para operaciones sensibles y reglas de negocio críticas:
prevención de auto-descenso, prevención de auto-eliminación y protección del último administrador.
* Flujo de Integración Extremo a Extremo:
1. UI/Cliente: Formulario de perfil (`assets/libs/js/usuario.js`) y gestión de usuarios (`gestion_user.js`).
2. Petición HTTP: AJAX POST hacia `assets/controller/UserController.php` y `LoginController.php`.
3. Procesamiento Modelo: `Usuario::Loguearse`, `obtener_datos`, `editar`, `cambiar_contra`, `ascender`, `descender`, `delete`.
4. Persistencia DB: Consultas preparadas en tabla `usuario` y relaciones con `tipo_us`.
5. Respuesta: Notificaciones de estado ('update', 'up', 'donw', 'delete', etc.) o colección JSON.
*

### Métodos y Operaciones Disponibles

| Método | Parámetros | Propósito |
| :--- | :--- | :--- |
| `__construct()` | *Sin parámetros* | Modelo de Datos y Lógica de Negocio para Usuarios y Seguridad |
| `Loguearse()` | `$ci, $pass` | Autenticar credenciales de usuario mediante algoritmo seguro Bcrypt. |
| `obtener_datos()` | `$id` | Obtener los datos completos de perfil de un usuario por identificador. |
| `editar()` | `$id_usuario, $telefono, $correo, $genero, $info` | Editar información de contacto y biográfica del perfil propio. |
| `cambiar_contra()` | `$id_usuario, $oldpass, $newpass` | Modificar la contraseña personal del usuario previa validación de la clave actual. |
| `cambiar_foto()` | `$id_usuario, $nombre` | Actualizar el nombre de archivo del avatar de perfil del usuario. |
| `buscar()` | *Sin parámetros* | Buscar usuarios por nombre, apellido o cédula de identidad. |
| `crear()` | `$nombre, $apellido, $edad, $ci, $genero, $pass, $tipo, $avatar` | Registrar un nuevo operador en el sistema con contraseña Bcrypt. |
| `verificarPasswordAdmin()` | `$pass, $id_usuario` | Validar la contraseña de confirmación del administrador ejecutor. |
| `ascender()` | `$pass, $id_up, $id_usuario` | Ascender un usuario operador a rol de Administrador. |
| `descender()` | `$pass, $id_donw, $id_usuario` | Degradar un usuario administrador a rol de Secretario/Operador. |
| `delete()` | `$pass, $id_delete, $id_usuario` | Eliminar un usuario del sistema y borrar su avatar personalizado del disco. |

---

