<?php
session_start();
include '../../conexion.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['rol'] != 'admin') {
    header("Location: ../../login.php");
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: consultar.php?error=ID inválido");
    exit;
}

$id = $_GET['id'];

// Eliminar elemento
$conn->query("DELETE FROM ingresoelementos WHERE IDingele = $id");

header("Location: consultar.php?success=Elemento eliminado correctamente");
exit;
?>