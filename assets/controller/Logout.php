<?php
/**
 * ============================================================================
 * ARCHIVO: Logout.php
 * CAPA: Controlador (Gestión de Sesión / Salida Segura)
 * DESCRIPCIÓN: Destruye de forma segura la sesión activa del usuario actual
 *              y redirige a la pantalla principal de inicio de sesión.
 * ENTRADA: Solicitud GET o navegación hacia assets/controller/Logout.php
 *          (habitualmente invocada desde el botón #btn-logout en la barra de navegación).
 * SALIDA: Destrucción de variables de sesión y redirección HTTP a index.php.
 * @author Grupo de Proyecto
 * @version 1.0.0
 * ============================================================================
 */

session_start();

// Destruir todas las variables registradas en la sesión del servidor
session_destroy();

// Redirigir al usuario al portal de acceso principal
header('Location: ../../index.php');
exit();
?>