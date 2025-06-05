<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
	class Conexion
	{
		private $host='gondola.proxy.rlwy.net';
        private $port='49128';
		private $usuario='root';
		private $password = 'XSvZgbZFpXXcKskSSjFyvBmASVZeCXcM';
		private $base='railway';
		public $sentencia;
		private $rows =array();
		public $conexion;	

		public function abrir_conexion(){
			try {
                echo "DEBUG: Intentando conectar a la base de datos...<br>";
                echo "DEBUG: Host: " . $this->host . "<br>";
                echo "DEBUG: Puerto: " . $this->port . "<br>";
                echo "DEBUG: Usuario: " . $this->usuario . "<br>";
                echo "DEBUG: Base de datos: " . $this->base . "<br>";
                
				$this->conexion = new mysqli($this->host, $this->usuario, $this->password, $this->base, $this->port);
				if ($this->conexion->connect_error) {
                    echo "DEBUG: Error de conexión: " . $this->conexion->connect_error . "<br>";
					throw new Exception("Error de conexión: " . $this->conexion->connect_error);
				}
                echo "DEBUG: Conexión exitosa a la base de datos<br>";
				$this->conexion->set_charset("utf8");
			} catch (Exception $e) {
                echo "DEBUG: Excepción en la conexión: " . $e->getMessage() . "<br>";
				error_log("Error de conexión: " . $e->getMessage());
				throw $e;
			}
		}

		public function cerrar_conexion(){
			if ($this->conexion) {
				$this->conexion->close(); 
			}
		}

		public function ejecutar_sentencia(){
			try {
				$this->abrir_conexion();
				$bandera = $this->conexion->query($this->sentencia);
				if (!$bandera) {
					throw new Exception("Error en la consulta: " . $this->conexion->error);
				}
				$this->cerrar_conexion(); 
				return $bandera;
			} catch (Exception $e) {
				error_log("Error en ejecutar_sentencia: " . $e->getMessage());
				throw $e;
			}
		}

		public function obtener_sentencia(){
			try {
				$this->abrir_conexion();
				$result = $this->conexion->query($this->sentencia);
				if (!$result) {
					throw new Exception("Error en la consulta: " . $this->conexion->error);
				}
				return $result;
			} catch (Exception $e) {
				error_log("Error en obtener_sentencia: " . $e->getMessage());
				throw $e;
			}
		}

		public function obtener_ultimo_id()
		{
			return $this->conexion->insert_id;
		}
	}

?>
 