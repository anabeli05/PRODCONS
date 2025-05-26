<?php

    include 'conexion.php';

    class Contacto extends Conexion{
        public function login($correo, $password)
        {
            echo "<br>DEBUG: Entrando a login()<br>";
            try {
                // Abrir conexión
                $this->abrir_conexion();
                echo "<br>DEBUG: Conexión abierta<br>";
                
                // Usar prepared statement para prevenir SQL injection
                $stmt = $this->conexion->prepare("SELECT * FROM usuarios WHERE Correo = ?");
                if (!$stmt) {
                    echo "<br>DEBUG: Error en la preparación de la consulta<br>";
                    throw new Exception("Error en la preparación de la consulta: " . $this->conexion->error);
                }
                
                $stmt->bind_param("s", $correo);
                $stmt->execute();
                $resultado = $stmt->get_result();
                echo "<br>DEBUG: Consulta ejecutada<br>";

                if ($row = $resultado->fetch_assoc()) {
                    echo "<br>DEBUG: Usuario encontrado<br>";
                    // Verificar contraseña
                    if ($row['Contraseña'] == $password) {
                        echo "<br>DEBUG: Contraseña correcta<br>";
                        // Si el usuario está inactivo, reactivarlo
                        if (isset($row['activo']) && $row['activo'] == 0) {
                            $id = $row['Usuario_ID'];
                            $updateStmt = $this->conexion->prepare("UPDATE usuarios SET activo = 1 WHERE Usuario_ID = ?");
                            $updateStmt->bind_param("i", $id);
                            $updateStmt->execute();
                            $row['activo'] = 1;
                        }
                        
                        // Iniciar sesión y guardar variables de sesión
                        session_start();
                        $_SESSION['Usuario_ID'] = $row['Usuario_ID'];
                        $_SESSION['Nombre'] = $row['Nombre'];
                        $_SESSION['Correo'] = $row['Correo'];
                        
                        // Normalizar el rol
                        $rol = $row['Rol'];
                        if ($rol === 'SuperAdmin') {
                            $rol = 'Super Admin';
                        } elseif ($rol === 'Editores') {
                            $rol = 'Editor';
                        }
                        $_SESSION['Rol'] = $rol;
                        
                        $_SESSION['Fecha'] = $row['Fecha'];
                        $_SESSION['Codigo de Recuperacion'] = $row['Codigo de Recuperacion'];
                        $_SESSION['Estado'] = $row['Estado'];

                        echo "<br>DEBUG: Redirigiendo según el rol: $rol<br>";
                        // Redirigir al dashboard de acuerdo al tipo_usuario
                        switch ($_SESSION['Rol']) {
                            case 'Super Admin':
                                echo "<br>DEBUG: Redirigiendo a Super Admin<br>";
                                header("location: ../Dashboard_SuperAdmin/inicio/inicioSA.php");
                                break;
                            case 'Editor':
                                echo "<br>DEBUG: Redirigiendo a Editor<br>";
                                header("location: ../Dashboard_Editores/inicio/inicio.php");
                                break;
                            case 'Usuario':
                                echo "<br>DEBUG: Redirigiendo a Usuario<br>";
                                header("location: ../Dashboard_Usuario/dashboard.php");
                                break;
                            default:
                                echo "<br>DEBUG: Rol desconocido<br>";
                                header("location: login.php?error=1");
                                break;
                        }
                        exit();
                    } else {
                        echo "<br>DEBUG: Contraseña incorrecta<br>";
                    }
                } else {
                    echo "<br>DEBUG: Usuario no encontrado<br>";
                }
                
                // Si llegamos aquí, las credenciales son incorrectas
                header("location: login.php?error=1");
                exit();
                
            } catch (Exception $e) {
                error_log("Error en login: " . $e->getMessage());
                echo "<br>DEBUG: Excepción capturada: " . $e->getMessage() . "<br>";
                header("location: login.php?error=1");
                exit();
            } finally {
                if (isset($stmt)) {
                    $stmt->close();
                }
                $this->cerrar_conexion();
            }
        }
    }
?>
