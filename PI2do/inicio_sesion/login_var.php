<?php
    if(isset($_POST['boton_ingresar']))
    {
        $correo = $_POST['Correo'];
        $password = $_POST['Contraseña'];
        
        // Validar que los campos no estén vacíos
        if(empty($correo) || empty($password)) {
            header("location: login.php?error=1");
            exit();
        }
        
        require_once '../Base de datos/contacto.php';
        $obj = new Contacto();
        $obj->login($correo, $password);
    }
?>