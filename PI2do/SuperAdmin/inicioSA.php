<?php
session_start();
if (!isset($_SESSION['Rol']) || $_SESSION['Rol'] != 'Super Admin') {
    header("Location: /PI2do/inicio sesion/login.html");
    exit();
}
?>
<!-- Aquí va el HTML del panel de Super Admin -->
