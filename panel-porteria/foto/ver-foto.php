<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: ../login.php");
    exit;
}

include '../../conexion.php';

$idUsuario = $_SESSION['usuario']['IDper'];

// Obtener la ruta de la foto actual del usuario
$sql = "SELECT ruta FROM fotos_perfil WHERE id_persona = ? AND es_actual = 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idUsuario);
$stmt->execute();
$result = $stmt->get_result();
$foto = $result->fetch_assoc();

// Verificar si la foto existe y el archivo físico está presente
if (!$foto || !file_exists(__DIR__ . '/../../' . $foto['ruta'])) {
    // Mostrar una imagen por defecto o un mensaje si no hay foto
    header('Content-Type: text/plain');
    echo "No tienes una foto de perfil cargada.";
    exit;
}

// Mostrar la imagen
header('Content-Type: ' . mime_content_type(__DIR__ . '/../../' . $foto['ruta']));
readfile(__DIR__ . '/../../' . $foto['ruta']);
?>
