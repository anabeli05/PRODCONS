<?php
include_once 'log_utils.php';

    if(isset($_POST['boton_ingresar']))
    {
        writeLog("DEBUG: Botón de ingreso presionado");
        $correo = $_POST['Correo'];
        $password = $_POST['Contraseña'];
        
        writeLog("DEBUG: Correo recibido: " . $correo);
        writeLog("DEBUG: Contraseña recibida: " . $password);
        
        // Validar que los campos no estén vacíos
        if(empty($correo) || empty($password)) {
            writeLog("DEBUG: Campos vacíos detectados");
            header("location: login.php?error=1");
            exit();
        }
        
        require_once '../Base de datos/contacto.php';
        writeLog("DEBUG: Clase Contacto cargada");
        $obj = new Contacto();
        writeLog("DEBUG: Objeto Contacto creado");
        $obj->login($correo, $password);
    }
?>