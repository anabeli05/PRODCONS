<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: ../login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Consumo y Producción Sostenible</title>
  <style>
    body {
      font-family: sans-serif;
      background-image: url('https://images.unsplash.com/photo-1560807707-8cc77767d783'); /* Fondo de campo */
      background-size: cover;
      background-position: center;
      margin: 0;
      padding: 0;
    }

    .cuestionario {
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      padding: 20px;
    }

    .contenedor-cuestionario {
      background: white;
      border-radius: 20px;
      padding: 30px;
      max-width: 900px;
      width: 100%;
      box-shadow: 0 0 20px rgba(0,0,0,0.15);
      display: flex;
      flex-wrap: wrap;
      justify-content: space-between;
      gap: 20px;
    }

    h2 {
      font-size: 24px;
      margin-bottom: 20px;
      width: 100%;
    }

    .preguntas {
      list-style: none;
      padding: 0;
      flex: 2;
      width: 60%;
      margin-right: 32px;
    }

    .preguntas li {
      margin-bottom: 20px;
    }

    .preguntas label {
      font-weight: bold;
      display: block;
      margin-bottom: 5px;
    }

    .preguntas input {
      width: 100%;
      padding: 10px;
      border: 1px solid #ddd;
      background-color: #f3e3d9;
      border-radius: 8px;
    }

    .respuestas-box {
      background-color: #e4f6aa;
      padding: 20px;
      border-radius: 15px;
      text-align: center;
      flex: 1;
      width: 30%;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      gap: 20px;
    }

    .respuestas-box p {
      font-weight: bold;
      margin-bottom: 10px;
      color: #2c3e50;
      font-size: 1.1em;
    }

    .respuestas-box img {
      width: 120px;
      margin-bottom: 15px;
      transition: transform 0.3s ease;
    }

    .respuestas-box img:hover {
      transform: scale(1.1);
    }

    .enviar {
      background-color: #4CAF50;
      color: white;
      border: none;
      padding: 15px 30px;
      border-radius: 25px;
      font-size: 1.1em;
      font-weight: bold;
      cursor: pointer;
      transition: all 0.3s ease;
      box-shadow: 0 4px 6px rgba(0,0,0,0.1);
      width: auto;
      min-width: 200px;
      position: static;
      margin-top: 10px;
    }

    .enviar:hover {
      background-color: #45a049;
      transform: translateY(-2px);
      box-shadow: 0 6px 8px rgba(0,0,0,0.15);
    }

    .enviar:active {
      transform: translateY(0);
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    @media (max-width: 768px) {
      .contenedor-cuestionario {
        flex-direction: column;
      }

      .preguntas, .respuestas-box {
        width: 100%;
      }

      .preguntas {
        margin-right: 0;
      }

      .enviar {
        width: 100%;
        margin-top: 20px;
      }
    }
  </style>
</head>
<body>

  <section class="cuestionario">
    <div class="contenedor-cuestionario">
      <h2>¿Qué tanto sabes del consumo y la producción sostenible?</h2>

      <ul class="preguntas">
        <li>
          <label>¿Cómo se afecta el medio ambiente?</label>
          <input type="text" placeholder="Escribe tu respuesta aquí" />
        </li>
        <li>
          <label>¿Cómo se pueden reducir los desechos?</label>
          <input type="text" placeholder="Escribe tu respuesta aquí" />
        </li>
        <li>
          <label>¿Cómo se pueden aprovechar los recursos?</label>
          <input type="text" placeholder="Escribe tu respuesta aquí" />
        </li>
        <li>
          <label>¿Cómo se pueden mejorar las condiciones de vida?</label>
          <input type="text" placeholder="Escribe tu respuesta aquí" />
        </li>
      </ul>

      <div class="respuestas-box">
        <p>Ingresa tus respuestas</p>
        <img src="https://cdn-icons-png.flaticon.com/512/4202/4202844.png" alt="Niña sonriente animada" />
        <button class="enviar">Enviar Respuestas</button>
      </div>
    </div>
  </section>

</body>
</html>