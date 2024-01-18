INSTRUCCIONES DE INSTALACION:

1-colocar los archivos en la raiz en su hosting o hosting local. La carpeta se llama "prueba"

2-El nombre de la base de datos es "prueba" la puede encontrar en la carpeta llamada SQL
  debe inportarla en su motor de base de datos.


3-debe acceder a los archivos y editar el archivo llamado "conexion.php" en el cual debe reemplazar con 
  credenciales de esta manera. 

   $dsn = 'mysql:host=su_servidor_web;dbname=prueba'; 
        $usuario = 'su_usuario';
        $contrasena = 'su_clave';

4-acceder a la carpeta desde su navegador de esta manera

http://localhost:8080/prueba/ = en servidor local
http://suhosting/prueba/


5-version de php

PHP Version 8.0.28

6-version base de datos

10.4.28-MariaDB
