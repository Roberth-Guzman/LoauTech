<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: ../login.php");
    exit;
}

include '../../conexion.php';

$idUsuario = $_SESSION['usuario']['IDper'];

$sql = "SELECT id, ruta FROM fotos_perfil WHERE id_persona = ? AND es_actual = 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idUsuario);
$stmt->execute();
$result = $stmt->get_result();
$foto = $result->fetch_assoc();

if ($foto) {
    $rutaFoto = __DIR__ . '/../../' . $foto['ruta'];

    if (file_exists($rutaFoto)) {
        unlink($rutaFoto);
    }

    $stmt = $conn->prepare("DELETE FROM fotos_perfil WHERE id = ?");
    $stmt->bind_param("i", $foto['id']);
    $stmt->execute();

    $_SESSION['mensaje'] = "Foto de perfil borrada correctamente.";
} else {
    $_SESSION['error'] = "No se encontró una foto de perfil para borrar.";
}

header("Location: ../perfil-usuario.php");
exit;
