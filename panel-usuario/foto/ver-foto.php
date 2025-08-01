<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: ../login.php");
    exit;
}

include '../conexion.php';

$idUsuario = $_SESSION['usuario']['IDper'];

// Obtener la ruta de la foto
$result = $conn->query("SELECT ruta FROM fotos_perfil WHERE id_persona = $idUsuario AND es_actual = 1");
if ($result->num_rows > 0) {
    $foto = $result->fetch_assoc();
    $ruta_archivo = "../" . $foto['ruta'];
    
    if (file_exists($ruta_archivo)) {
        // Obtener el tipo MIME de la imagen
        $tipo_mime = mime_content_type($ruta_archivo);
        
        // Enviar los encabezados adecuados
        header("Content-Type: " . $tipo_mime);
        header("Content-Length: " . filesize($ruta_archivo));
        
        // Leer y enviar el archivo
        readfile($ruta_archivo);
        exit;
    }
}

// Si no hay foto, mostrar una imagen por defecto
header("Content-Type: image/png");
readfile("../assets/img/default-profile.png");
exit;
?>