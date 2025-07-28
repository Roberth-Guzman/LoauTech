<?php
session_start();
header('Content-Type: application/json');

// Verificar si el usuario tiene permisos de administrador o almacén
if (!isset($_SESSION['usuario']) || ($_SESSION['usuario']['rol'] !== 'admin' && $_SESSION['usuario']['rol'] !== 'almacenes')) {
    echo json_encode(['success' => false, 'message' => 'No tienes permisos para realizar esta acción']);
    exit;
}

// Configuración de la base de datos
require_once('../../conexion.php');

// Directorio donde se guardarán las imágenes
$uploadDir = __DIR__ . '/../../uploads/elementos/';
$imagenPath = null;

// Manejar la subida de la imagen si se proporcionó
if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
    $fileTmpPath = $_FILES['imagen']['tmp_name'];
    $fileName = $_FILES['imagen']['name'];
    $fileSize = $_FILES['imagen']['size'];
    $fileType = $_FILES['imagen']['type'];
    $fileNameCmps = explode(".", $fileName);
    $fileExtension = strtolower(end($fileNameCmps));
    
    // Validar tipo de archivo
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
    if (!in_array($fileExtension, $allowedExtensions)) {
        echo json_encode(['success' => false, 'message' => 'Tipo de archivo no permitido. Solo se permiten imágenes JPG, JPEG, PNG o GIF.']);
        exit;
    }
    
    // Validar tamaño de archivo (máximo 2MB)
    $maxFileSize = 2 * 1024 * 1024; // 2MB
    if ($fileSize > $maxFileSize) {
        echo json_encode(['success' => false, 'message' => 'El archivo es demasiado grande. El tamaño máximo permitido es de 2MB.']);
        exit;
    }
    
    // Generar un nombre único para la imagen
    $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
    $destPath = $uploadDir . $newFileName;
    
    // Mover el archivo subido al directorio de destino
    if (move_uploaded_file($fileTmpPath, $destPath)) {
        $imagenPath = 'uploads/elementos/' . $newFileName;
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al subir la imagen.']);
        exit;
    }
}

try {
    // Obtener los datos del formulario
    $nombre = $_POST['nombre'] ?? '';
    $codigo = $_POST['codigo'] ?? '';
    $categoria = $_POST['categoria'] ?? '';
    $stock = intval($_POST['stock'] ?? 0);
    $estado = $_POST['estado'] ?? 'disponible';
    $descripcion = $_POST['descripcion'] ?? '';
    
    // Validar datos obligatorios
    if (empty($nombre) || empty($codigo)) {
        throw new Exception('El nombre y el código son campos obligatorios');
    }
    
    // Preparar la consulta SQL
    $sql = "INSERT INTO elementos (nombreele, cantidadele, cantidadest, codigoele, descripcionele, caracteristicasele, estado, estadoelemento, codigoinventario, imagen) 
            VALUES (?, ?, 'activo', ?, ?, ?, 'activo', 'activo', ?, ?)";
    
    $stmt = $conn->prepare($sql);
    
    // Los parámetros son: nombre, cantidad, código, descripción, características, código de inventario, imagen
    $stmt->bind_param("sisssss", 
        $nombre, 
        $stock, 
        $codigo, 
        $descripcion, 
        $categoria, 
        $codigo, // Usamos el mismo código para codigoinventario por ahora
        $imagenPath
    );
    
    // Ejecutar la consulta
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Elemento guardado correctamente']);
    } else {
        throw new Exception('Error al guardar el elemento en la base de datos');
    }
    
} catch (Exception $e) {
    // Si hay un error, eliminar la imagen subida (si existe)
    if ($imagenPath && file_exists('../../' . $imagenPath)) {
        unlink('../../' . $imagenPath);
    }
    
    echo json_encode([
        'success' => false, 
        'message' => 'Error: ' . $e->getMessage(),
        'debug' => $e->getTraceAsString()
    ]);
}

$conn->close();
?>
