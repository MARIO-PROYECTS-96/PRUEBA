<?php
require 'conexion.php';
$conexionBD = new ConexionBD();
$conexion = $conexionBD->getConexion();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recibe los datos del formulario
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $alias = $_POST['alias'];
    $rut = $_POST['rut'];
    $email = $_POST['email'];
    $region_id = $_POST['region']; // Se usa region_id para obtener el valor del ID de la región seleccionada
    $comuna_id = $_POST['comuna']; // Se usa comuna_id para obtener el valor del ID de la comuna seleccionada
    $candidato_id = $_POST['candidato']; // Se usa candidato_id para obtener el valor del ID del candidato seleccionado
    $como_se_entero = isset($_POST['entero']) ? $_POST['entero'] : [];

    // Valida que al menos se seleccionen 2 opciones en el campo "Como se enteró de nosotros"
    if (count($como_se_entero) < 2) {
        echo "Debes seleccionar al menos dos opciones en 'Como se enteró de nosotros'.";
        exit;
    }

    // Realiza la inserción en la base de datos
    try {
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Obtener el texto de la región, comuna y candidato usando sus respectivos IDs
        $consulta_region = "SELECT nombre_region FROM regiones WHERE id = ?";
        $stmt_region = $conexion->prepare($consulta_region);
        $stmt_region->execute([$region_id]);
        $region = $stmt_region->fetchColumn();

        $consulta_comuna = "SELECT nombre_comuna FROM comunas WHERE id = ?";
        $stmt_comuna = $conexion->prepare($consulta_comuna);
        $stmt_comuna->execute([$comuna_id]);
        $comuna = $stmt_comuna->fetchColumn();

        $consulta_candidato = "SELECT nombre_candidato FROM candidatos WHERE id = ?";
        $stmt_candidato = $conexion->prepare($consulta_candidato);
        $stmt_candidato->execute([$candidato_id]);
        $candidato = $stmt_candidato->fetchColumn();

        // Inserta los datos en la tabla "registro"
        $consulta = "INSERT INTO registro (nombre, apellido, alias, rut, email, region, comuna, candidato, fecha_voto)
                     VALUES (:nombre, :apellido, :alias, :rut, :email, :region, :comuna, :candidato, NOW())";
        $stmt = $conexion->prepare($consulta);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':apellido', $apellido);
        $stmt->bindParam(':alias', $alias);
        $stmt->bindParam(':rut', $rut);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':region', $region, PDO::PARAM_STR);
        $stmt->bindParam(':comuna', $comuna, PDO::PARAM_STR);
        $stmt->bindParam(':candidato', $candidato, PDO::PARAM_STR);
        $stmt->execute();

        // Obtiene el ID del registro insertado para la relación muchos a muchos
        $registro_id = $conexion->lastInsertId();

        // Inserta las opciones seleccionadas en la tabla de relación muchos a muchos "registro_entero"
        $consulta_entero = "INSERT INTO registro_entero (registro_id, opcion) VALUES (:registro_id, :opcion)";
        $stmt_entero = $conexion->prepare($consulta_entero);
        $stmt_entero->bindParam(':registro_id', $registro_id);

        // Verifica si la combinación ya existe antes de insertar
        foreach ($como_se_entero as $opcion) {
            $stmt_entero->bindParam(':opcion', $opcion);

            // Verificar si la combinación ya existe
            $consulta_verificar = "SELECT COUNT(*) AS count FROM registro_entero WHERE registro_id = :registro_id AND opcion = :opcion";
            $stmt_verificar = $conexion->prepare($consulta_verificar);
            $stmt_verificar->bindParam(':registro_id', $registro_id);
            $stmt_verificar->bindParam(':opcion', $opcion);
            $stmt_verificar->execute();

            $resultado_verificar = $stmt_verificar->fetch(PDO::FETCH_ASSOC);

            if ($resultado_verificar['count'] == 0) {
                // Insertar solo si la combinación no existe
                $stmt_entero->execute();
            }
        }

        echo "Voto registrado con éxito.";
    } catch (PDOException $e) {
        echo "Error al registrar el voto: " . $e->getMessage();
    }
}
?>
