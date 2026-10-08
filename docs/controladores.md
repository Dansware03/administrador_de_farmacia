# Controladores del Sistema (Capa de Orquestación)

> **Generado automáticamente a partir del código fuente PHP (`assets/controller/`).**
> La única fuente de verdad son los comentarios DocBlocks en los archivos del sistema.

---

## AreaController.php

### Casos de Uso Documentados en Código

| Acción (`funcion`) | Ruta / Endpoint | Parámetros |
| :--- | :--- | :--- |
| `cargar_areas` | `POST assets/controller/AreaController.php [funcion=cargar_areas]` | *Ninguno* |
| `cargar_unidades` | `POST assets/controller/AreaController.php [funcion=cargar_unidades]` | *Ninguno* |
| `buscar_areas` | `POST assets/controller/AreaController.php [funcion=buscar_areas]` | `$_POST['consulta']` |
| `crear_area` | `POST assets/controller/AreaController.php [funcion=crear_area]` | `$_POST['nombre_area']` |
| `editar_area` | `POST assets/controller/AreaController.php [funcion=editar_area]` | `$_POST['id_area']`, `$_POST['nombre_area']` |
| `borrar_area` | `POST assets/controller/AreaController.php [funcion=borrar_area]` | `$_POST['id_area']` |

---

## DespachoController.php

### Casos de Uso Documentados en Código

| Acción (`funcion`) | Ruta / Endpoint | Parámetros |
| :--- | :--- | :--- |
| `registrar_despacho` | `POST assets/controller/DespachoController.php [funcion=registrar_despacho]` | `$nombre`, `$ci`, `$id_area`, `$cargo_receptor`, `$observacion`, `$productos` |
| `listar_despachos` | `GET/POST assets/controller/DespachoController.php [funcion=listar_despachos]` | `$_GET['fecha_inicio']`, `$_GET['fecha_fin']` |
| `ver_detalle_despacho` | `POST/GET assets/controller/DespachoController.php [funcion=ver_detalle_despacho]` | `$id_despacho` |
| `revertir_despacho` | `POST assets/controller/DespachoController.php [funcion=revertir_despacho]` | `$_POST['id_despacho']` |
| `obtener_despacho` | `POST/GET assets/controller/DespachoController.php [funcion=obtener_despacho]` | `$id_despacho` |
| `obtener_stock_lote` | `POST assets/controller/DespachoController.php [funcion=obtener_stock_lote]` | `$_POST['id_producto']`, `$_POST['id_lote']` |
| `actualizar_despacho` | `POST assets/controller/DespachoController.php [funcion=actualizar_despacho]` | `$_POST['id_despacho']`, `$_POST['receptor']`, `$_POST['ci_receptor']`, `$_POST['productos']` |
| `accion` | `GET assets/controller/DespachoController.php?accion=obtener_despacho_pdf&id=X` | *Ninguno* |

---

## LaboratoryController.php

### Casos de Uso Documentados en Código

| Acción (`funcion`) | Ruta / Endpoint | Parámetros |
| :--- | :--- | :--- |
| `crear` | `POST assets/controller/LaboratoryController.php [funcion=crear]` | `$_POST['nombre_laboratory']` |
| `editar` | `POST assets/controller/LaboratoryController.php [funcion=editar]` | `$_POST['nombre_laboratory']`, `$_POST['id_editado']` |
| `buscar` | `POST assets/controller/LaboratoryController.php [funcion=buscar]` | `$_POST['consulta']` |
| `borrar_lab` | `POST assets/controller/LaboratoryController.php [funcion=borrar_lab]` | `$_POST['id']` |
| `rellenar_laboratorio` | `POST assets/controller/LaboratoryController.php [funcion=rellenar_laboratorio]` | *Ninguno* |

---

## LoginController.php

### Casos de Uso Documentados en Código

| Acción (`funcion`) | Ruta / Endpoint | Parámetros |
| :--- | :--- | :--- |
| `user` | `POST assets/controller/LoginController.php` | `$_POST['user']`, `$_POST['pass']` |

---

## Logout.php

_Controlador de acción directa o redirección de sesión._

---

## LoteController.php

### Casos de Uso Documentados en Código

| Acción (`funcion`) | Ruta / Endpoint | Parámetros |
| :--- | :--- | :--- |
| `crear` | `POST assets/controller/LoteController.php [funcion=crear]` | `$_POST['id_producto']`, `$_POST['proveedor']`, `$_POST['cod_lote']`, `$_POST['stock']`, `$_POST['vencimiento']` |
| `buscar_lote` | `POST assets/controller/LoteController.php [funcion=buscar_lote]` | *Ninguno* |
| `editar` | `POST assets/controller/LoteController.php [funcion=editar]` | `$_POST['id']`, `$_POST['stock']` |
| `borrar_lote` | `POST assets/controller/LoteController.php [funcion=borrar_lote]` | `$_POST['id']` |

---

## PresentacionesController.php

### Casos de Uso Documentados en Código

| Acción (`funcion`) | Ruta / Endpoint | Parámetros |
| :--- | :--- | :--- |
| `crear` | `POST assets/controller/PresentacionesController.php [funcion=crear]` | `$_POST['nombre_pre']` |
| `editar` | `POST assets/controller/PresentacionesController.php [funcion=editar]` | `$_POST['nombre_pre']`, `$_POST['id_editado']` |
| `buscar` | `POST assets/controller/PresentacionesController.php [funcion=buscar]` | `$_POST['consulta']` |
| `borrar_pre` | `POST assets/controller/PresentacionesController.php [funcion=borrar_pre]` | `$_POST['id']` |
| `rellenar_presentacion` | `POST assets/controller/PresentacionesController.php [funcion=rellenar_presentacion]` | *Ninguno* |

---

## ProductoController.php

### Casos de Uso Documentados en Código

| Acción (`funcion`) | Ruta / Endpoint | Parámetros |
| :--- | :--- | :--- |
| `crear` | `POST assets/controller/ProductoController.php [funcion=crear]` | `$_POST['nombre']`, `$_POST['concentracion']`, `$_POST['adicional']`, `$_POST['prod_lab']`, `$_POST['prod_tip_prod']`, `$_POST['prod_present']`, `$_POST['id_unidad']`, `$_POST['especificacion_talla']`, `$_POST['cod_lote']`, `$_POST['stock_inicial']`, `$_POST['proveedor_lote']`, `$_POST['vencimiento_lote']` |
| `cambiar_avatar` | `POST assets/controller/ProductoController.php [funcion=cambiar_avatar]` | `$_POST['id_logo_prod']`, `$_FILES['foto']` |
| `buscar_product` | `POST assets/controller/ProductoController.php [funcion=buscar_product]` | `$_POST['consulta']` |
| `editar` | `POST assets/controller/ProductoController.php [funcion=editar]` | `$_POST['id_edit_prod']` |
| `borrar_produts` | `POST assets/controller/ProductoController.php [funcion=borrar_produts]` | `$_POST['id']` |
| `validar_existencia` | `POST assets/controller/ProductoController.php [funcion=validar_existencia]` | `$_POST['ids']` |
| `verificarStock` | `POST assets/controller/ProductoController.php [funcion=verificarStock]` | `$_POST['productos']` |

---

## ProveedorController.php

### Casos de Uso Documentados en Código

| Acción (`funcion`) | Ruta / Endpoint | Parámetros |
| :--- | :--- | :--- |
| `crear` | `POST assets/controller/ProveedorController.php [funcion=crear]` | `$_POST['nombre']`, `$_POST['telefono']`, `$_POST['correo']`, `$_POST['direccion']` |
| `cambiar_avatar` | `POST assets/controller/ProveedorController.php [funcion=cambiar_avatar]` | `$_POST['id_logo_prod']`, `$_FILES['foto']` |
| `buscar_prov` | `POST assets/controller/ProveedorController.php [funcion=buscar_prov]` | `$_POST['consulta']` |
| `editar` | `POST assets/controller/ProveedorController.php [funcion=editar]` | `$_POST['id_editado']`, `$_POST['nombre']`, `$_POST['telefono']`, `$_POST['correo']`, `$_POST['direccion']` |
| `borrar_prove` | `POST assets/controller/ProveedorController.php [funcion=borrar_prove]` | `$_POST['id']` |
| `rellenar_proveedor` | `POST assets/controller/ProveedorController.php [funcion=rellenar_proveedor]` | *Ninguno* |

---

## TypeController.php

### Casos de Uso Documentados en Código

| Acción (`funcion`) | Ruta / Endpoint | Parámetros |
| :--- | :--- | :--- |
| `crear` | `POST assets/controller/TypeController.php [funcion=crear]` | `$_POST['nombre_type']` |
| `editar_type` | `POST assets/controller/TypeController.php [funcion=editar_type]` | `$_POST['nombre_type']`, `$_POST['id_editado']` |
| `buscar` | `POST assets/controller/TypeController.php [funcion=buscar]` | `$_POST['consulta']` |
| `borrar_type` | `POST assets/controller/TypeController.php [funcion=borrar_type]` | `$_POST['id']` |
| `rellenar_type` | `POST assets/controller/TypeController.php [funcion=rellenar_type]` | *Ninguno* |

---

## UserController.php

### Casos de Uso Documentados en Código

| Acción (`funcion`) | Ruta / Endpoint | Parámetros |
| :--- | :--- | :--- |
| `buscar_usuario` | `POST assets/controller/UserController.php [funcion=buscar_usuario]` | `$_POST['dato']` |
| `capturar_datos` | `POST assets/controller/UserController.php [funcion=capturar_datos]` | *Ninguno* |
| `editar_usuario` | `POST assets/controller/UserController.php [funcion=editar_usuario]` | `$_POST['telefono']`, `$_POST['correo']`, `$_POST['genero']`, `$_POST['info']` |
| `cambiar_contra` | `POST assets/controller/UserController.php [funcion=cambiar_contra]` | `$_POST['oldpass']`, `$_POST['newpass']` |
| `cambiar_foto` | `POST assets/controller/UserController.php [funcion=cambiar_foto]` | `$_FILES['foto']` |
| `buscar_usuario_adm` | `POST assets/controller/UserController.php [funcion=buscar_usuario_adm]` | *Ninguno* |
| `crear_usuario` | `POST assets/controller/UserController.php [funcion=crear_usuario]` | `$_POST['nombre']`, `$_POST['apellido']`, `$_POST['edad']`, `$_POST['ci']`, `$_POST['genero']`, `$_POST['pass']` |
| `ascender` | `POST assets/controller/UserController.php [funcion=ascender]` | `$_POST['pass']`, `$_POST['id_usuario']` |
| `descender` | `POST assets/controller/UserController.php [funcion=descender]` | `$_POST['pass']`, `$_POST['id_usuario']` |
| `delete_user` | `POST assets/controller/UserController.php [funcion=delete_user]` | `$_POST['pass']`, `$_POST['id_usuario']` |

---

