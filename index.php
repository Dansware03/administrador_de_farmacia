<?php session_start(); if (!empty($_SESSION['us_tipo'])) { header('Location: assets/controller/LoginController.php'); }else { session_destroy(); } include_once 'headers.php'; ?>
<!DOCTYPE html> <html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Farmacia</title>
    <link rel="stylesheet" type="text/css" href="assets/libs/css/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="assets/libs/css/poppins-font/stylesheet.css">
    <link rel="stylesheet" type="text/css" href="assets/libs/css/styles.css">
</head>
<body>
    <img src="#" alt="" class="wave">
    <div class="contenedor">
        <div class="img">
            <img src="assets/libs/img/doctores.svg" alt="">
        </div>
        <div class="contenido-login">
            <form action="assets/controller/LoginController.php" method="post">
                <img src="assets/libs/img/logo.png" alt="">
                <h2>Farmacia</h2>
                <div class="input-div ci">
                    <div class="i"><i class="fas fa-user"></i></div>
                    <div class="div">
                        <h5>Cedula de Identidad</h5>
                        <input type="text" name="user" class="input">
                    </div>
                </div>
                <div class="input-div pass">
                    <div class="i"><i class="fas fa-lock"></i></div>
                    <div class="div">
                        <h5>Contraseña</h5>
                        <input type="password" name="pass" class="input">
                    </div>
                </div>
                <input type="submit" class="btn" value="Iniciar Sesion">
            </form>
        </div>
    </div>
<script src="assets/libs/js/login.js"></script>
</body>
</html>