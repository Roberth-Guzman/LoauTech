<?php
session_start();
include '../../conexion.php';

$idUsuario = $_SESSION['usuario']['IDper'] ?? null;


if (!$idUsuario) {
    echo "Usuario no identificado.";
    exit;
}

// Ruta común para fotos de perfil
$sql = "SELECT ruta FROM fotos_perfil WHERE id_persona = ? AND es_actual = 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idUsuario);
$stmt->execute();
$result = $stmt->get_result();
$foto = $result->fetch_assoc();

if (!$foto) {
    echo "No se encontró ninguna foto.";
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Foto de Perfil</title>
</head>
<body style="margin:0; display:flex; justify-content:center; align-items:center; height:100vh; background:#000;">
    <img src="../../<?= htmlspecialchars($foto['ruta']) ?>" alt="Foto de perfil" style="max-width:90%; max-height:90%;">
</body>
</html>
