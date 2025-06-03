<?php
session_start();

// Destruir todas las variables de sesión
$_SESSION = array();

// Destruir la sesión
session_destroy();

// Redirigir al index.php
header("Location: /PRODCONS/index.php");
exit();
?>
