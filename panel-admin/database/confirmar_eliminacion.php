<?php
session_start();
include '../../conexion.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['rol'] != 'admin') {
    header("Location: ../../login.php");
    exit;
}

if (!isset($_GET['db']) || empty($_GET['db'])) {
    header('Location: eliminardb.php?error=No se especificó la base de datos');
    exit;
}

$nombre_db = $_GET['db'];
$bases_sistema = array('information_schema', 'mysql', 'performance_schema', 'phpmyadmin', 'sys');

if (in_array($nombre_db, $bases_sistema)) {
    header('Location: eliminardb.php?error=No se pueden eliminar bases de datos del sistema');
    exit;
}

// Conexión sin seleccionar BD
$conn = new mysqli("localhost", "root", "");

if (mysqli_query($conn, "DROP DATABASE IF EXISTS `$nombre_db`")) {
    header('Location: eliminardb.php?success=Base+de+datos+eliminada+correctamente');
} else {
    header('Location: eliminardb.php?error=Error+al+eliminar:+'.urlencode(mysqli_error($conn)));
}

exit;
?>