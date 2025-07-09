<?php
session_start();
include '../../conexion.php';

$idUsuario = $_SESSION['usuario']['IDper'] ?? null;


if (!$idUsuario || !isset($_FILES['foto_perfil'])) {
    $_SESSION['error'] = "No se ha recibido la imagen.";
    header("Location: ../perfil-porteria.php");
    exit;
}

$foto = $_FILES['foto_perfil'];

// Carpeta común para fotos
$carpetaDestino = "uploads/fotos_perfil/";

// Crear carpeta si no existe
if (!is_dir("../../" . $carpetaDestino)) {
    mkdir("../../" . $carpetaDestino, 0755, true);
}

// Extensión segura y nombre único
$ext = strtolower(pathinfo($foto["name"], PATHINFO_EXTENSION));
$nombreArchivo = "porteria_" . $idUsuario . "_" . time() . "." . $ext;
$rutaFoto = $carpetaDestino . $nombreArchivo;
$destino = "../../" . $rutaFoto;

$extensionesPermitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
if (!in_array($ext, $extensionesPermitidas)) {
    $_SESSION['error'] = "Formato de imagen no permitido.";
    header("Location: ../perfil-porteria.php");
    exit;
}

if (move_uploaded_file($foto["tmp_name"], $destino)) {
    // Marcar todas las fotos anteriores como no actuales
    $stmtUpdate = $conn->prepare("UPDATE fotos_perfil SET es_actual = 0 WHERE id_persona = ?");
    $stmtUpdate->bind_param("i", $idUsuario);
    $stmtUpdate->execute();

    // Insertar nueva foto
    $stmtInsert = $conn->prepare("INSERT INTO fotos_perfil (id_persona, ruta, es_actual) VALUES (?, ?, 1)");
    $stmtInsert->bind_param("is", $idUsuario, $rutaFoto);
    $stmtInsert->execute();

    $_SESSION['mensaje'] = "Foto actualizada correctamente.";
} else {
    $_SESSION['error'] = "Error al subir la foto.";
}

header("Location: ../perfil-porteria.php");
exit;
?>
