<!DOCTYPE html>
<html>
<?php require 'conexion.php';
$conexionBD = new ConexionBD();
$conexion = $conexionBD->getConexion();
 ?>
<head>
    <title>Formulario de Votación</title>
    <style>
        /* Estilos para el cuerpo del documento */
        body {
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f2f2f2;
        }

        /* Contenedor principal del formulario */
        #contenedor-formulario {
            width: 100%;
            max-width: 400px;
            padding: 20px;
            border-radius: 10px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            box-sizing: border-box;
            margin: 20px;
            /* Agregar margen para separar del borde de la ventana */
        }

        /* Estilos para los elementos del formulario ... */

        /* El resto de los estilos se mantiene igual */
    </style>
</head>
<div id="formulario">

    <body>
        <h1>Formulario de Votación</h1>
        <form action="procesar_votacion.php" method="post">
            <!-- ... Otros campos del formulario ... -->
            <label for="nombre">Nombre:</label>
            <input type="text" id="nombre" name="nombre" required><br><br>

            <label for="apellido">Apellido:</label>
            <input type="text" id="apellido" name="apellido" required><br><br>

            <label for="alias">Alias (mínimo 6 caracteres ):</label>
            <input type="text" id="alias" name="alias" pattern="^[a-zA-Z0-9]{6,}$" required><br><br>

            <label for="rut">RUT (Formato Chile):</label>
            <input type="text" id="rut" name="rut" pattern="^\d{1,2}\.\d{3}\.\d{3}[-][0-9kK]{1}$" required><br><br>

            <label for="email">Correo Electrónico:</label>
            <input type="email" id="email" name="email" required><br><br>

            <label for="region">Región:</label>
            <select id="region" name="region" required>

                <option value="">Selecciona una Región</option>
                <?php
                // Código PHP para cargar las regiones desde la base de datos
                $consulta = "SELECT * FROM regiones";
                $stmt = $conexion->query($consulta);
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo '<option value="' . $row['id'] . '">' . $row['nombre_region'] . '</option>';
                }
                ?>
            </select><br><br>

            <label for="comuna">Comuna:</label>
            <select id="comuna" name="comuna" required>
                <option value="">Selecciona una Comuna</option>
                <!-- Las opciones de comuna se cargarán mediante JavaScript / Ajax -->
            </select><br><br>
            <label for="candidato">Candidato:</label>
            <select id="candidato" name="candidato" required>
                <option value="">Selecciona un Candidato</option>
                <?php
                // Código PHP para cargar los candidatos desde la base de datos
                $consulta = "SELECT * FROM candidatos";
                $stmt = $conexion->query($consulta);
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo '<option value="' . $row['id'] . '">' . $row['nombre_candidato'] . '</option>';
                }
                ?>
            </select><br><br>
            <!-- ... Otros campos del formulario ... -->
            <p>Como se enteró de nosotros (selecciona al menos 2 opciones):</p>
            <input type="checkbox" name="entero[]" value="WEB"> WEB
            <input type="checkbox" name="entero[]" value="TV"> TV
            <input type="checkbox" name="entero[]" value="Redes sociales"> Redes sociales
            <input type="checkbox" name="entero[]" value="Amigo"> Amigo
            <p id="mensaje_validacion" style="color: red; display: none;">Controla esta casilla si deseas continuar.</p>
            <br /><br />
            <input type="submit" value="Votar" onclick="return validarCheckbox()">
        </form>

        <script>
            function validarCheckbox() {
                var checkboxes = document.getElementsByName('entero[]');
                var seleccionados = 0;
                for (var i = 0; i < checkboxes.length; i++) {
                    if (checkboxes[i].checked) {
                        seleccionados++;
                    }
                }

                if (seleccionados < 2) {
                    document.getElementById('mensaje_validacion').style.display = 'block';
                    return false;
                } else {
                    document.getElementById('mensaje_validacion').style.display = 'none';
                    return true;
                }
            }
        </script>
        </form>

        <script>
            // Código JavaScript / Ajax para cargar las comunas según la región seleccionada
            document.getElementById('region').addEventListener('change', function() {
                var regionId = this.value;
                var comunaSelect = document.getElementById('comuna');

                // Elimina las opciones de comuna actuales
                while (comunaSelect.firstChild) {
                    comunaSelect.removeChild(comunaSelect.firstChild);
                }

                // Si no se ha seleccionado una región, no se carga nada en el campo de comuna
                if (regionId === '') {
                    return;
                }

                // Realiza una petición Ajax para obtener las comunas según la región seleccionada
                var xhr = new XMLHttpRequest();
                xhr.open('GET', 'obtener_comunas.php?regionId=' + regionId, true);
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
                        var comunas = JSON.parse(xhr.responseText);

                        // Agrega las opciones de comuna al campo select
                        comunas.forEach(function(comuna) {
                            var option = document.createElement('option');
                            option.value = comuna.id;
                            option.textContent = comuna.nombre_comuna;
                            comunaSelect.appendChild(option);
                        });
                    }
                };
                xhr.send();
            });
        </script>
    </body>
</div>

</html>