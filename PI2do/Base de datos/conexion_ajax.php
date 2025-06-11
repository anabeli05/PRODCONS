<?php
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
            $this->conexion = new mysqli($this->host, $this->usuario, $this->password, $this->base, $this->port);
            if ($this->conexion->connect_error) {
                throw new Exception("Error de conexión: " . $this->conexion->connect_error);
            }
            $this->conexion->set_charset("utf8");
        } catch (Exception $e) {
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
        } catch (Exception $e) {
            error_log("Error en la consulta: " . $e->getMessage());
            throw $e;
        }
    }
}
