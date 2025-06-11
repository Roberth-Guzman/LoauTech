<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: ../login.php");
    exit;
}


include '../../conexion.php';

$idUsuario = $_SESSION['usuario']['IDper'];


if (!isset($_FILES['foto_perfil']) || $_FILES['foto_perfil']['error'] !== UPLOAD_ERR_OK) {
    $_SESSION['error'] = "Error al subir la foto. Intenta de nuevo.";
    header("Location: ../perfil.php");
    exit;
}

$foto = $_FILES['foto_perfil'];

// Validar tipo de archivo
$permitidos = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
if (!in_array($foto['type'], $permitidos)) {
    $_SESSION['error'] = "Formato de archivo no permitido. Usa JPG, PNG, GIF o WEBP.";
    header("Location: ../perfil.php");
    exit;
}

// Validar tamaño máximo (2MB)
$maxSize = 2 * 1024 * 1024;
if ($foto['size'] > $maxSize) {
    $_SESSION['error'] = "El archivo es demasiado grande. Máximo 2MB.";
    header("Location: ../perfil.php");
    exit;
}

// Crear carpeta si no existe
$carpeta = "../../uploads/fotos_perfil/";
if (!is_dir($carpeta)) {
    mkdir($carpeta, 0755, true);
}

$extension = pathinfo($foto['name'], PATHINFO_EXTENSION);
$nombreArchivo = "perfil_" . $idUsuario . "_" . time() . "." . $extension;
$rutaDestino = $carpeta . $nombreArchivo;

// Mover archivo
if (!move_uploaded_file($foto['tmp_name'], $rutaDestino)) {
    $_SESSION['error'] = "No se pudo guardar la foto en el servidor.";
    header("Location: ../perfil.php");
    exit;
}

// Ruta relativa para la base de datos
$rutaBD = "uploads/fotos_perfil/" . $nombreArchivo;

$conn->begin_transaction();

try {
    $sqlNoActual = "UPDATE fotos_perfil SET es_actual = 0 WHERE id_persona = ?";
    $stmt = $conn->prepare($sqlNoActual);
    $stmt->bind_param("i", $idUsuario);
    $stmt->execute();

    $sqlInsert = "INSERT INTO fotos_perfil (id_persona, ruta, es_actual) VALUES (?, ?, 1)";
    $stmt = $conn->prepare($sqlInsert);
    $stmt->bind_param("is", $idUsuario, $rutaBD);
    $stmt->execute();

    $conn->commit();

    $_SESSION['mensaje'] = "Foto de perfil actualizada correctamente.";
} catch (Exception $e) {
    $conn->rollback();
    if (file_exists($rutaDestino)) {
        unlink($rutaDestino);
    }
    $_SESSION['error'] = "Error al guardar la foto en la base de datos.";
}

header("Location: ../perfil.php");
exit;
