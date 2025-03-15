<?php
session_start();

// Verificar si el usuario no está logueado
if (!isset($_SESSION["username"]) || empty($_SESSION["username"])) {
    // El usuario no está logueado, destruir cualquier sesión existente y redirigir al login
    session_destroy();
    header("Location: login.php");
    exit();
}

// Si llega hasta aquí, la sesión es válida y el usuario puede acceder a la página
?>