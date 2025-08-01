<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: ../login.php");
    exit;
}

include '../conexion.php';

$idUsuario = $_SESSION['usuario']['IDper'];

// Directorio donde se guardarán las fotos
$directorio = "../uploads/fotos_perfil/";

// Crear el directorio si no existe
if (!file_exists($directorio)) {
    mkdir($directorio, 0777, true);
}

// Validar que se haya subido un archivo
if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] === UPLOAD_ERR_OK) {
    $archivo_tmp = $_FILES['foto_perfil']['tmp_name'];
    $nombre_archivo = $_FILES['foto_perfil']['name'];
    $extension = strtolower(pathinfo($nombre_archivo, PATHINFO_EXTENSION));
    
    // Validar extensión del archivo
    $extensiones_permitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    if (!in_array($extension, $extensiones_permitidas)) {
        $_SESSION['error'] = "Solo se permiten archivos JPG, JPEG, PNG, GIF o WEBP.";
        header("Location: perfil.php");
        exit;
    }
    
    // Validar tamaño del archivo (máximo 2MB)
    if ($_FILES['foto_perfil']['size'] > 2097152) {
        $_SESSION['error'] = "El archivo es demasiado grande. El tamaño máximo permitido es 2MB.";
        header("Location: perfil.php");
        exit;
    }
    
    // Generar un nombre único para el archivo
    $nombre_unico = "perfil_" . $idUsuario . "_" . time() . "." . $extension;
    $ruta_destino = $directorio . $nombre_unico;
    
    // Mover el archivo subido al directorio destino
    if (move_uploaded_file($archivo_tmp, $ruta_destino)) {
        // Primero, marcar todas las fotos anteriores como no actuales
        $conn->query("UPDATE fotos_perfil SET es_actual = 0 WHERE id_persona = $idUsuario");
        
        // Insertar la nueva foto
        $ruta_relativa = "uploads/fotos_perfil/" . $nombre_unico;
        $stmt = $conn->prepare("INSERT INTO fotos_perfil (id_persona, ruta, es_actual) VALUES (?, ?, 1)");
        $stmt->bind_param("is", $idUsuario, $ruta_relativa);
        
        if ($stmt->execute()) {
            $_SESSION['exito'] = "Foto de perfil actualizada correctamente.";
        } else {
            $_SESSION['error'] = "Error al guardar la información en la base de datos.";
            // Eliminar la foto subida si hubo error en la BD
            unlink($ruta_destino);
        }
    } else {
        $_SESSION['error'] = "Error al subir el archivo.";
    }
} else {
    $_SESSION['error'] = "No se ha seleccionado ningún archivo o hubo un error en la subida.";
}

header("Location: perfil.php");
exit;
?>