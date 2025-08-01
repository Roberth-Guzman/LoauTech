<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: ../login.php");
    exit;
}

include '../conexion.php';

$idUsuario = $_SESSION['usuario']['IDper'];

// Obtener la ruta de la foto actual
$result = $conn->query("SELECT id, ruta FROM fotos_perfil WHERE id_persona = $idUsuario AND es_actual = 1");
if ($result->num_rows > 0) {
    $foto = $result->fetch_assoc();
    
    // Eliminar el archivo físico
    $ruta_archivo = "../" . $foto['ruta'];
    if (file_exists($ruta_archivo)) {
        unlink($ruta_archivo);
    }
    
    // Eliminar el registro de la base de datos
    $conn->query("DELETE FROM fotos_perfil WHERE id = " . $foto['id']);
    
    $_SESSION['exito'] = "Foto de perfil eliminada correctamente.";
} else {
    $_SESSION['error'] = "No se encontró ninguna foto de perfil para eliminar.";
}

header("Location: perfil.php");
exit;
?>