<?php
$host = "localhost";
$usuario = "root";
$contrasena = "";
$bd = "loatech";

// Conexión básica al servidor
$conn = new mysqli($host, $usuario, $contrasena);

if ($conn->connect_error) {
    die("Error de conexión al servidor: " . $conn->connect_error);
}

// Verificar si la base de datos existe
$db_exists = false;
if ($result = $conn->query("SHOW DATABASES LIKE '$bd'")) {
    $db_exists = $result->num_rows > 0;
    $result->close();
}

// Definir constantes y variables globales
define('DB_EXISTS', $db_exists);
$GLOBALS['db_not_available'] = !$db_exists;

if ($db_exists) {
    // Si existe, reconectar seleccionando la BD
    $conn = new mysqli($host, $usuario, $contrasena, $bd);
    if ($conn->connect_error) {
        die("Error al conectar a la base de datos: " . $conn->connect_error);
    }
    $conn->set_charset("utf8mb4");
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>