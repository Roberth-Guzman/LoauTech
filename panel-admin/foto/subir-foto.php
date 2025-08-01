<?php
session_start();

if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['fotoPerfil'])) {
    $usuario_id = $_SESSION['usuario']['IDper'];

    $file = $_FILES['fotoPerfil'];
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    $max_size = 2 * 1024 * 1024; // 2MB

    if ($file['error'] !== UPLOAD_ERR_OK) {
        $_SESSION['error_foto'] = "Error al subir la imagen.";
        header('Location: ../perfil.php');
        exit();
    }

    if (!in_array($file['type'], $allowed_types)) {
        $_SESSION['error_foto'] = "Tipo de archivo no permitido. Solo JPG, PNG y GIF.";
        header('Location: ../perfil.php');
        exit();
    }

    if ($file['size'] > $max_size) {
        $_SESSION['error_foto'] = "El archivo es demasiado grande. Máximo 2MB.";
        header('Location: ../perfil.php');
        exit();
    }

    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $nuevo_nombre = 'perfil_' . $usuario_id . '.' . $ext;
    $ruta_destino = __DIR__ . '/uploads/' . $nuevo_nombre;

    if (!is_dir(__DIR__ . '/uploads')) {
        mkdir(__DIR__ . '/uploads', 0755, true);
    }

    if (move_uploaded_file($file['tmp_name'], $ruta_destino)) {
        require_once __DIR__ . '/../../conexion.php';

        $ruta_relativa = 'panel-admin/foto/uploads/' . $nuevo_nombre;

        $sql_update = "UPDATE fotos_perfil SET es_actual = 0 WHERE id_persona = ?";
        $stmt = $conn->prepare($sql_update);
        $stmt->bind_param("i", $usuario_id);
        $stmt->execute();
        $stmt->close();

        $sql_insert = "INSERT INTO fotos_perfil (id_persona, ruta, es_actual) VALUES (?, ?, 1)";
        $stmt = $conn->prepare($sql_insert);
        $stmt->bind_param("is", $usuario_id, $ruta_relativa);
        $stmt->execute();
        $stmt->close();

        $_SESSION['mensaje_foto'] = "Foto de perfil actualizada correctamente.";
    } else {
        $_SESSION['error_foto'] = "Error al mover el archivo subido.";
    }
} else {
    $_SESSION['error_foto'] = "No se recibió ningún archivo.";
}

header('Location: ../perfil.php');
exit();