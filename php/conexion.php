    <?php
    $serverName = "localhost"; // En XAMPP, generalmente es "localhost"
    $username = "root"; // Usuario por defecto en XAMPP para MySQL
    $password = ""; // Sin contraseña en la instalación estándar de XAMPP
    $database = "pagos"; // Nombre de la base de datos

    // Realizamos la conexión
    $conn = new mysqli($serverName, $username, $password, $database);

    // Verificamos si la conexión fue exitosa
    if ($conn->connect_error) {
        die("Error al conectar a la base de datos: " . $conn->connect_error);
    } else {
        echo "";
    }

    // Cerrar la conexión cuando ya no la necesites
    // $conn->close();
    ?>
