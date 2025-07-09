<?php
session_start();
include '../../conexion.php';

$idUsuario = $_SESSION['usuario']['IDper'] ?? null;


if (!$idUsuario) {
    $_SESSION['error'] = "Usuario no identificado.";
    header("Location: ../perfil-porteria.php");
    exit;
}

// Obtener ruta de foto actual
$sql = "SELECT ruta FROM fotos_perfil WHERE id_persona = ? AND es_actual = 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idUsuario);
$stmt->execute();
$result = $stmt->get_result();
$foto = $result->fetch_assoc();

if ($foto) {
    $ruta = "../../" . $foto['ruta'];
    if (file_exists($ruta)) {
        unlink($ruta); // eliminar archivo del servidor
    }

    // Eliminar registro de la foto
    $stmtDel = $conn->prepare("DELETE FROM fotos_perfil WHERE id_persona = ? AND es_actual = 1");
    $stmtDel->bind_param("i", $idUsuario);
    $stmtDel->execute();

    $_SESSION['mensaje'] = "Foto eliminada correctamente.";
} else {
    $_SESSION['error'] = "No se encontró foto para eliminar.";
}

header("Location: ../perfil-porteria.php");
exit;
?>
