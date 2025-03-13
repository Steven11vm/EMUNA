<?php
session_start();
if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="dashboard-container">
        <h2>Bienvenido, <?= $_SESSION["username"] ?></h2>
        <p>Tu cargo es: <strong><?= $_SESSION["role"] ?></strong></p>
        <a href="logout.php">Cerrar sesión</a>
    </div>
</body>
</html>
