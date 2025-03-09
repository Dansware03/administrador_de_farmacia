<?php
class EmpresaController {
    
    // Función para obtener datos de la empresa
    public function obtener_datos() {
        // En un sistema real, estos datos se obtendrían de la base de datos
        // Aquí los definimos estáticamente como ejemplo
        $datos_empresa = array(
            'nombre' => 'Farmacia Sistema',
            'direccion' => 'Calle Principal #123, Ciudad',
            'telefono' => '123-456-7890',
            'email' => 'contacto@farmaciasistema.com',
            'ruc' => '1234567890-1',
            'slogan' => 'Cuidando su salud desde siempre'
        );
        
        return $datos_empresa;
    }
}
?>