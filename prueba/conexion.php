<?php
// conexion.php

class ConexionBD {
    private $conexion;

    public function __construct() {
        $dsn = 'mysql:host=localhost;dbname=prueba';
        $usuario = 'root';
        $contrasena = '';

        try {
            $this->conexion = new PDO($dsn, $usuario, $contrasena);
            $this->conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo "Error de conexión: " . $e->getMessage();
            exit;
        }
    }

    public function getConexion() {
        return $this->conexion;
    }
}
