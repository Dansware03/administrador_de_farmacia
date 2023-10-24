<?php
header('Content-Type: application/json');
// Content Security Policy (CSP)
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval'; style-src 'self' 'unsafe-inline';");
// Este encabezado establece una directiva de política de seguridad que restringe el origen de los recursos (por ejemplo, scripts y estilos).
// - default-src: El sitio permite recursos solo desde su propio origen.
// - script-src: Permite la ejecución de scripts solo desde el mismo origen y también permite scripts inline y evaluación insegura.
// - style-src: Permite estilos solo desde el mismo origen y también permite estilos inline y evaluación insegura.

// HTTP Strict Transport Security (HSTS)
header("Strict-Transport-Security: max-age=31536000; includeSubDomains; preload");
// Este encabezado asegura que las solicitudes solo se realicen a través de HTTPS y que se cachee la política de seguridad durante 1 año.
// - max-age: Tiempo en segundos durante el cual el navegador forzará HTTPS.
// - includeSubDomains: Incluye todos los subdominios.
// - preload: Indica a los navegadores que el sitio debe incluirse en la lista de pre-carga HSTS.

// X-Frame-Options
header("X-Frame-Options: DENY");
// Este encabezado evita que el contenido se incruste en un marco/iframe, lo que ayuda a prevenir ataques de clicjacking.

// X-XSS-Protection
header("X-XSS-Protection: 1; mode=block");
// Activa el filtro anti-XSS (cross-site scripting) en los navegadores que lo admiten.

// Referrer Policy
header("Referrer-Policy: no-referrer");
// Este encabezado controla qué información del referente (URL anterior) se incluye en las solicitudes HTTP. "no-referrer" no envía información de referente.

// Content-Type Options
header("X-Content-Type-Options: nosniff");
// Evita que los navegadores interpreten incorrectamente los tipos de contenido, evitando así ataques MIME-sniffing.

// Cross-Origin Resource Sharing (CORS)
header("Access-Control-Allow-Origin: https://tusitio.com");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
// Estos encabezados permiten solicitudes desde un origen específico (https://tusitio.com), con métodos y encabezados HTTP específicos.

// Cache-Control Headers (Evitar almacenamiento en caché)
header("Cache-Control: no-store, no-cache, must-revalidate, proxy-revalidate");
// Estos encabezados evitan el almacenamiento en caché del contenido, asegurando que las páginas siempre se vuelvan a cargar desde el servidor.
include_once '../db/usuario.php';
$usuario = new Usuario();
session_start();
$id_usuario= $_SESSION['usuario'];
if ($_POST['funcion']=='buscar_usuario'){
    $json=array();
    $fecha_actual = new DateTime();
    $usuario->obtener_datos($_POST['dato']);
    foreach ($usuario->objetos as $objeto) {
        $nacimiento = new DateTime($objeto->edad);
        $edad = $nacimiento->diff($fecha_actual);
        $edad_year = $edad->y;
        $json[]=array(
            'nombre'=>$objeto->nombre_us,
            'apellidos'=>$objeto->apellidos_us,
            'edad' => $edad_year,
            'ci'=>$objeto->ci_us,
            'tipo'=>$objeto->nombre_tipo,
            'telefono'=>$objeto->telefono_us,
            'correo'=>$objeto->correo_us,
            'genero'=>$objeto->genero_us,
            'info'=>$objeto->info_us,
            'avatar'=>'../libs/img/avatars/'.$objeto->avatar

        );
    }
    $jsonstring = json_encode($json[0]);
    echo $jsonstring;
}
if ($_POST['funcion']=='capturar_datos'){
    $json=array();
    $id_usuario=$_POST['id_usuario'];
    $usuario->obtener_datos($id_usuario);
    foreach ($usuario->objetos as $objeto) {
        $json[]=array(
            'telefono'=>$objeto->telefono_us,
            'correo'=>$objeto->correo_us,
            'genero'=>$objeto->genero_us,
            'info'=>$objeto->info_us
            
        );
    }
    $jsonstring = json_encode($json[0]);
    echo $jsonstring;
}
if ($_POST['funcion']=='editar_usuario'){
    $id_usuario=$_POST['id_usuario'];
    $telefono=$_POST['telefono'];
    $correo=$_POST['correo'];
    $genero=$_POST['genero'];
    $info=$_POST['info'];
    $usuario->editar($id_usuario,$telefono,$correo,$genero,$info);
    echo 'editado';
}
if ($_POST['funcion']=='cambiar_contra'){
    $id_usuario=$_POST['id_usuario'];
    $oldpass=$_POST['oldpass'];
    $newpass=$_POST['newpass'];
    $usuario->cambiar_contra($id_usuario,$oldpass,$newpass);
}
if ($_POST['funcion']=='cambiar_foto'){
    if (($_FILES['foto']['type'] == 'image/jpeg') || ($_FILES['foto']['type'] == 'image/png') || ($_FILES['foto']['type'] == 'image/jpg') || ($_FILES['foto']['type'] == 'image/bmp')){
    $nombre=uniqid().'-'.$_FILES['foto']['name'];
    $ruta='../libs/img/avatars/'.$nombre;
    move_uploaded_file($_FILES['foto']['tmp_name'],$ruta);
    $usuario->cambiar_foto($id_usuario,$nombre);
    foreach ($usuario->objetos as $objeto) {
        unlink('../libs/img/avatars/' . $objeto->avatar);
    }
        $json= array();
        $json[]=array(
            'ruta'=>$ruta,
            'alert'=>'edit'
        );
        $jsonstring = json_encode($json[0]);
        echo $jsonstring;
    }else {
        $json= array();
        $json[]=array(
            'alert'=>'noedit'
        );
        $jsonstring = json_encode($json[0]);
        echo $jsonstring;
    }
}
?>
