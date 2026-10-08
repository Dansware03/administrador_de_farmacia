# Controladores del Sistema (Capa de Orquestación)

> **Ubicación:** `assets/controller/`  
> **Rol:** Orquestadores de peticiones HTTP/AJAX. Reciben entradas desde el cliente (JavaScript), sanean datos, validan reglas de negocio, delegan operaciones a los Modelos PDO y emiten respuestas JSON o estados en texto plano.

---

## 1. AreaController.php
- **Archivo:** `assets/controller/AreaController.php`
- **Descripción:** Administra las áreas de servicio hospitalario receptoras de insumos (Quirófano, Emergencia, Nutrición, etc.) y el catálogo de unidades métricas.
- **Dependencias:** `assets/db/conexion.php`

### Casos de Uso Registrados
| Acción (`funcion`) | Método HTTP | Descripción | Parámetros |
| :--- | :--- | :--- | :--- |
| `cargar_areas` | POST | Obtiene el listado alfabético de todas las áreas hospitalarias. | *Ninguno* |
| `cargar_unidades` | POST | Retorna el catálogo métrico de unidades de medida (`und`, `L`, `gal`, etc.). | *Ninguno* |
| `buscar_areas` | POST | Búsqueda textual y filtrado seguro de áreas con PDO. | `consulta` (opcional) |
| `crear_area` | POST | Registra una nueva área hospitalaria (Exige rol Administrador). | `nombre_area` |
| `editar_area` | POST | Actualiza la denominación de un área hospitalaria existente. | `id_area`, `nombre_area` |
| `borrar_area` | POST | Elimina un área verificando que no tenga actas de despacho históricas asociadas. | `id_area` |

---

## 2. DespachoController.php
- **Archivo:** `assets/controller/DespachoController.php`
- **Descripción:** Orquesta las salidas institucionales de insumos hacia las áreas de la clínica, actas de entrega, reintegros de stock y soporte para actas oficiales en PDF.
- **Dependencias:** `assets/db/despacho.php`

### Casos de Uso Registrados
| Acción (`funcion`) | Método HTTP | Descripción | Parámetros |
| :--- | :--- | :--- | :--- |
| `registrar_despacho` | POST | Registra entrega institucional descontando stock por lotes (criterio FEFO). | `nombre`, `ci`, `id_area`, `cargo_receptor`, `observacion`, `productos` (JSON) |
| `listar_despachos` | GET/POST | Consulta el historial de despachos emitidos con filtro opcional de fechas. | `fecha_inicio`, `fecha_fin` (opcionales) |
| `ver_detalle_despacho` | GET/POST | Retorna los renglones de insumos y lotes entregados en un acta. | `id_despacho` |
| `revertir_despacho` | POST | Anula una entrega y restituye atómicamente el stock a los lotes originales. | `id_despacho` |
| `obtener_despacho` | GET/POST | Retorna los metadatos de cabecera de un despacho. | `id_despacho` |
| `obtener_stock_lote` | POST | Consulta puntual de existencia disponible en un lote específico. | `id_producto`, `id_lote` |
| `actualizar_despacho` | POST | Modifica los datos de receptor o renglones de un acta. | `id_despacho`, `receptor`, `ci_receptor`, `productos`, `id_area`, `cargo_receptor`, `observacion` |
| `obtener_despacho_pdf` | GET (`accion`) | Endpoint consolidado para compilar cabecera y detalle para la vista de acta PDF. | `id` |

---

## 3. LaboratoryController.php
- **Archivo:** `assets/controller/LaboratoryController.php`
- **Descripción:** Administra las entidades de fabricantes farmacéuticos y organizaciones donantes de insumos (INTERSOS, UNICEF, 3M, MPPS, etc.).
- **Dependencias:** `assets/db/laboratory.php`

### Casos de Uso Registrados
| Acción (`funcion`) | Método HTTP | Descripción | Parámetros |
| :--- | :--- | :--- | :--- |
| `crear` | POST | Registra un nuevo laboratorio o fabricante con validación de no duplicidad. | `nombre_laboratory` |
| `editar` | POST | Modifica la denominación comercial de un fabricante. | `nombre_laboratory`, `id_editado` |
| `buscar` | POST | Filtrado asíncrono y mapeo a JSON de laboratorios. | `consulta` (opcional) |
| `borrar_lab` | POST | Da de baja una entidad de laboratorio. | `id` |
| `rellenar_laboratorio` | POST | Provee la colección de fabricantes para alimentar dropdowns en formularios. | *Ninguno* |

---

## 4. LoginController.php
- **Archivo:** `assets/controller/LoginController.php`
- **Descripción:** Control de autenticación y sesiones. Valida contraseñas con `password_verify` (Bcrypt) y redirige según rol institucional (`1: Administrador` $\rightarrow$ `adm_catalogo.php`, `2: Secretario` $\rightarrow$ `tec_catalogo.php`).
- **Dependencias:** `assets/db/usuario.php`

### Flujo de Autenticación
- **Entrada:** `$_POST['user']` (Cédula de Identidad), `$_POST['pass']` (Contraseña).
- **Control Preventivo:** Si el usuario ya cuenta con sesión activa en memoria, redirige inmediatamente sin reprocesar.
- **Validación:** Invoca `Usuario::Loguearse()`. Si las credenciales coinciden, inicializa `$_SESSION['usuario']`, `us_tipo` y `nombre_us`.
- **Fallo:** Redirige a `index.php?login_error=1` disparando alerta SweetAlert2 en el cliente.

---

## 5. Logout.php
- **Archivo:** `assets/controller/Logout.php`
- **Descripción:** Controlador de terminación segura de sesión. Destruye todas las variables registradas en el servidor con `session_destroy()` y redirige al portal de acceso principal `index.php`.

---

## 6. LoteController.php
- **Archivo:** `assets/controller/LoteController.php`
- **Descripción:** Administra los lotes de inventario físico, control de fechas de vencimiento, clasificación de perecederos vs no perecederos y semaforización de caducidad.
- **Dependencias:** `assets/db/lote.php`

### Casos de Uso Registrados
| Acción (`funcion`) | Método HTTP | Descripción | Parámetros |
| :--- | :--- | :--- | :--- |
| `crear` | POST | Registra un nuevo lote de existencias físicas con su fecha de caducidad. | `id_producto`, `proveedor`, `cod_lote`, `stock`, `vencimiento` |
| `buscar_lote` | POST | Consulta inventario y calcula semaforización en tiempo real (`no_perecedero`, `light`, `warning`, `danger`). | *Ninguno* |
| `editar` | POST | Actualiza la cantidad de stock de un lote por inventario físico. | `id`, `stock` |
| `borrar_lote` | POST | Da de baja un lote del almacén. | `id` |

---

## 7. PresentacionesController.php
- **Archivo:** `assets/controller/PresentacionesController.php`
- **Descripción:** Administra el catálogo de formas farmacéuticas y tipos de empaque de insumos médicos (Unidad, Litro, Galón, Caja x 50, etc.).
- **Dependencias:** `assets/db/presentaciones.php`

### Casos de Uso Registrados
| Acción (`funcion`) | Método HTTP | Descripción | Parámetros |
| :--- | :--- | :--- | :--- |
| `crear` | POST | Registra una nueva presentación con validación de no duplicidad. | `nombre_pre` |
| `editar` | POST | Modifica una presentación existente. | `nombre_pre`, `id_editado` |
| `buscar` | POST | Búsqueda asíncrona de presentaciones en formato JSON. | `consulta` (opcional) |
| `borrar_pre` | POST | Elimina una presentación de empaque del catálogo. | `id` |
| `rellenar_presentacion` | POST | Provee datos para dropdowns en el alta y edición de insumos. | *Ninguno* |

---

## 8. ProductoController.php
- **Archivo:** `assets/controller/ProductoController.php`
- **Descripción:** Orquestador central del catálogo maestro de insumos, materiales y equipos de protección individual (EPI).
- **Dependencias:** `assets/db/producto.php`

### Casos de Uso Registrados
| Acción (`funcion`) | Método HTTP | Descripción | Parámetros |
| :--- | :--- | :--- | :--- |
| `crear` | POST | Crea nuevo insumo con soporte atómico opcional para lote y stock inicial. | `nombre`, `concentracion`, `adicional`, `prod_lab`, `prod_tip_prod`, `prod_present`, `id_unidad`, `especificacion_talla`, `cod_lote`, `stock_inicial`, `proveedor_lote`, `vencimiento_lote` |
| `cambiar_avatar` | POST | Valida y actualiza la fotografía o avatar del insumo. | `id_logo_prod`, `avatar`, `foto` (FILE) |
| `buscar_product` | POST | Consulta agregada que totaliza el stock disponible de lotes por insumo. | `consulta` (opcional) |
| `editar` | POST | Actualiza especificaciones técnicas y unidades métricas de un producto. | `id_edit_prod`, `nombre`, `concentracion`, `adicional`, `prod_lab`, `prod_tip_prod`, `prod_present`, `id_unidad`, `especificacion_talla` |
| `borrar_produts` | POST | Da de baja un insumo y elimina su archivo de imagen físico. | `id` |
| `validar_existencia` | POST | Valida IDs contra la base de datos para depurar insumos huérfanos del carrito en `localStorage`. | `ids` (JSON array) |
| `verificarStock` | POST | Valida existencias disponibles en almacén antes de consolidar una solicitud. | `productos` (JSON array) |

---

## 9. ProveedorController.php
- **Archivo:** `assets/controller/ProveedorController.php`
- **Descripción:** Gestiona entidades de proveedores comerciales, organismos humanitarios y donantes institucionales (INTERSOS, UNICEF, FUNREAHV, etc.).
- **Dependencias:** `assets/db/Proveedor.php`

### Casos de Uso Registrados
| Acción (`funcion`) | Método HTTP | Descripción | Parámetros |
| :--- | :--- | :--- | :--- |
| `crear` | POST | Registra proveedor o donante con validación de nombre y teléfono. | `nombre`, `telefono`, `correo`, `direccion` |
| `cambiar_avatar` | POST | Actualiza el logotipo institucional del proveedor. | `id_logo_prod`, `avatar`, `foto` (FILE) |
| `buscar_prov` | POST | Lista proveedores para la tabla de gestión comercial. | `consulta` (opcional) |
| `editar` | POST | Modifica datos de contacto y dirección operativa. | `id_editado`, `nombre`, `telefono`, `correo`, `direccion` |
| `borrar_prove` | POST | Da de baja un proveedor del sistema. | `id` |
| `rellenar_proveedor` | POST | Alimenta dropdowns de proveedores en el ingreso de lotes de inventario. | *Ninguno* |

---

## 10. TypeController.php
- **Archivo:** `assets/controller/TypeController.php`
- **Descripción:** Administra el catálogo de categorías o tipos de producto (Químicos, Equipos, EPI, Papel, Repuestos).
- **Dependencias:** `assets/db/type.php`

### Casos de Uso Registrados
| Acción (`funcion`) | Método HTTP | Descripción | Parámetros |
| :--- | :--- | :--- | :--- |
| `crear` | POST | Registra una nueva categoría de insumo con control de duplicados. | `nombre_type` |
| `editar_type` | POST | Modifica una categoría existente. | `nombre_type`, `id_editado` |
| `buscar` | POST | Búsqueda asíncrona de categorías en formato JSON. | `consulta` (opcional) |
| `borrar_type` | POST | Elimina una categoría del catálogo. | `id` |
| `rellenar_type` | POST | Alimenta menús desplegables para categorizar insumos médicos. | *Ninguno* |

---

## 11. UserController.php
- **Archivo:** `assets/controller/UserController.php`
- **Descripción:** Administra el ciclo de vida de los operadores, perfil personal, contraseñas Bcrypt y control de acceso con protecciones Anti-IDOR.
- **Dependencias:** `assets/db/usuario.php`

### Casos de Uso Registrados
| Acción (`funcion`) | Método HTTP | Descripción | Parámetros |
| :--- | :--- | :--- | :--- |
| `buscar_usuario` | POST | Consulta datos de un usuario por ID con cálculo dinámico de edad. | `dato` (ID) |
| `capturar_datos` | POST | Obtiene datos del perfil del usuario autenticado (Anti-IDOR: usa `$_SESSION['usuario']`). | *Ninguno* |
| `editar_usuario` | POST | Modifica datos de contacto (teléfono, correo, género, info) en sesión. | `telefono`, `correo`, `genero`, `info` |
| `cambiar_contra` | POST | Cambio seguro de clave con verificación y hash Bcrypt. | `oldpass`, `newpass` |
| `cambiar_foto` | POST | Valida y actualiza foto de perfil preservando `user-default.png`. | `foto` (FILE) |
| `buscar_usuario_adm` | POST | Lista todos los usuarios para la tabla de gestión administrativa. | *Ninguno* |
| `crear_usuario` | POST | Registra nuevo operador asignando rol Secretario (`tipo = 2`). Exige rol Administrador. | `nombre`, `apellido`, `edad`, `ci`, `genero`, `pass` |
| `ascender` | POST | Promueve a un Secretario a Administrador previa clave de confirmación. | `pass`, `id_usuario` |
| `descender` | POST | Degrada a un Administrador a Secretario. | `pass`, `id_usuario` |
| `delete_user` | POST | Da de baja a un operador del sistema previa confirmación de clave. | `pass`, `id_usuario` |
