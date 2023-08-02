<?php
require 'conexion.php';
$conexionBD = new ConexionBD();
$conexion = $conexionBD->getConexion();

if (isset($_GET['regionId'])) {
    $regionId = $_GET['regionId'];

    // Código PHP para cargar las comunas desde la base de datos según la región seleccionada
    $consulta = "SELECT * FROM comunas WHERE id_region = ?";
    $stmt = $conexion->prepare($consulta);
    $stmt->execute([$regionId]);
    $comunas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Devuelve las comunas en formato JSON
    header('Content-Type: application/json');
    echo json_encode($comunas);
}
?>
