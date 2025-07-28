<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: ../login.php");
    exit;
}

include '../../conexion.php';

$idUsuario = $_SESSION['usuario']['IDper'];

// Verificar si se envió un archivo
if (!isset($_FILES['foto_perfil']) || $_FILES['foto_perfil']['error'] !== UPLOAD_ERR_OK) {
    $error = "Error al subir la foto. ";
    switch ($_FILES['foto_perfil']['error'] ?? 0) {
        case UPLOAD_ERR_INI_SIZE:
            $error .= "El archivo excede el tamaño máximo permitido por el servidor.";
            break;
        case UPLOAD_ERR_FORM_SIZE:
            $error .= "El archivo excede el tamaño máximo permitido.";
            break;
        case UPLOAD_ERR_PARTIAL:
            $error .= "El archivo solo se subió parcialmente.";
            break;
        case UPLOAD_ERR_NO_FILE:
            $error = "No se seleccionó ningún archivo.";
            break;
        case UPLOAD_ERR_NO_TMP_DIR:
            $error .= "Error del servidor: Falta la carpeta temporal.";
            break;
        case UPLOAD_ERR_CANT_WRITE:
            $error .= "Error del servidor: Error al escribir en el disco.";
            break;
        case UPLOAD_ERR_EXTENSION:
            $error .= "Error del servidor: Subida detenida por una extensión.";
            break;
        default:
            $error .= "Error desconocido al subir el archivo.";
    }
    $_SESSION['error'] = $error;
    header("Location: ../perfil.php");
    exit;
}

$foto = $_FILES['foto_perfil'];

// Validar tipo de archivo
$permitidos = [
    'image/jpeg' => 'jpg',
    'image/png' => 'png',
    'image/gif' => 'gif',
    'image/webp' => 'webp'
];

// Obtener el tipo MIME real del archivo
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime = finfo_file($finfo, $foto['tmp_name']);
finfo_close($finfo);

if (!in_array($mime, array_keys($permitidos))) {
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
$carpetaBase = "../../uploads/fotos_perfil/";
if (!is_dir($carpetaBase)) {
    if (!mkdir($carpetaBase, 0755, true)) {
        $_SESSION['error'] = "No se pudo crear el directorio para guardar la foto.";
        header("Location: ../perfil.php");
        exit;
    }
}

// Generar un nombre único para el archivo
$extension = $permitidos[$mime];
$nombreArchivo = "perfil_" . $idUsuario . "_" . uniqid() . "." . $extension;
$rutaDestino = $carpetaBase . $nombreArchivo;
$rutaRelativa = "uploads/fotos_perfil/" . $nombreArchivo;

// Mover archivo
if (!move_uploaded_file($foto['tmp_name'], $rutaDestino)) {
    $_SESSION['error'] = "No se pudo guardar la foto en el servidor.";
    header("Location: ../perfil.php");
    exit;
}

// Iniciar transacción para asegurar la integridad de los datos
$conn->begin_transaction();

try {
    // Desmarcar cualquier foto actual como no actual
    $stmt = $conn->prepare("UPDATE fotos_perfil SET es_actual = 0 WHERE id_persona = ?");
    $stmt->bind_param("i", $idUsuario);
    $stmt->execute();
    
    // Insertar la nueva foto
    $stmt = $conn->prepare("INSERT INTO fotos_perfil (id_persona, ruta, es_actual) VALUES (?, ?, 1)");
    $stmt->bind_param("is", $idUsuario, $rutaRelativa);
    $stmt->execute();
    
    // Confirmar la transacción
    $conn->commit();
    
    $_SESSION['mensaje'] = "Foto de perfil actualizada correctamente.";
} catch (Exception $e) {
    // Si hay algún error, deshacer la transacción
    $conn->rollback();
    
    // Eliminar la foto subida en caso de error
    if (file_exists($rutaDestino)) {
        unlink($rutaDestino);
    }
    
    $_SESSION['error'] = "Error al guardar la información de la foto en la base de datos: " . $e->getMessage();
}

header("Location: ../perfil.php");
exit;
