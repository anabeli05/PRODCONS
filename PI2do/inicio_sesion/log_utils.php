<?php
// Configuración de errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Crear archivo de log
$logFile = __DIR__ . '/debug.log';
file_put_contents($logFile, "--- Nuevo Log de Sesión: " . date('Y-m-d H:i:s') . " ---\n", FILE_APPEND);

// Función para escribir en el log
function writeLog($message) {
    global $logFile;
    // Asegurarse de que $logFile esté definido
    if (!isset($logFile)) {
        $logFile = __DIR__ . '/debug.log';
    }
    file_put_contents($logFile, $message . "\n", FILE_APPEND);
}
?> 