<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error - PRODCONS</title>
    <link rel="stylesheet" href="../Dashboard/sidebar.css">
    <style>
        .error-container {
            text-align: center;
            padding: 50px;
            margin: 50px auto;
            max-width: 600px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .error-message {
            color: #dc3545;
            margin-bottom: 20px;
        }
        .back-button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s;
        }
        .back-button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <h1 class="error-message">Ha ocurrido un error</h1>
        <?php if (isset($_SESSION['error'])): ?>
            <p><?php echo htmlspecialchars($_SESSION['error']); ?></p>
        <?php else: ?>
            <p>Ha ocurrido un error inesperado. Por favor, intente nuevamente.</p>
        <?php endif; ?>
        <a href="estadisticas-adm.php" class="back-button">Volver a Estadísticas</a>
    </div>
</body>
</html> 