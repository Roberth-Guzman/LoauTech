<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: ../login.php");
    exit;
}

include '../../conexion.php';

$idUsuario = $_SESSION['usuario']['IDper'];

$sql = "SELECT ruta FROM fotos_perfil WHERE id_persona = ? AND es_actual = 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idUsuario);
$stmt->execute();
$result = $stmt->get_result();
$foto = $result->fetch_assoc();

if (!$foto || !file_exists(__DIR__ . '/../../' . $foto['ruta'])) {
    echo "No tienes una foto de perfil cargada.";
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
