<?php
session_start();
// Verificar si el usuario tiene permisos de administrador o almacén
if (!isset($_SESSION['usuario']) || ($_SESSION['usuario']['rol'] !== 'admin' && $_SESSION['usuario']['rol'] !== 'almacenes')) {
    header('HTTP/1.1 403 Forbidden');
    echo json_encode(['success' => false, 'message' => 'Acceso denegado']);
    exit;
}

// Incluir archivo de conexión
require_once('../../conexion.php');

// Configuración de respuesta
header('Content-Type: application/json');

// Validar que se reciba el ID del elemento
if (!isset($_POST['id']) || empty($_POST['id'])) {
    echo json_encode(['success' => false, 'message' => 'ID de elemento no proporcionado']);
    exit;
}

$elemento_id = intval($_POST['id']);

// Inicializar la respuesta
$response = ['success' => false, 'message' => ''];

try {
    // Obtener datos del formulario
    $nombre = trim($_POST['nombre'] ?? '');
    $codigo = trim($_POST['codigo'] ?? '');
    $categoria = trim($_POST['categoria'] ?? '');
    $stock = intval($_POST['stock'] ?? 0);
    $estado = ($_POST['estado'] === 'available') ? 'activo' : 'inactivo';
    $descripcion = trim($_POST['descripcion'] ?? '');
    $imagen_actual = $_POST['current_image'] ?? '';
    $eliminar_imagen = isset($_POST['remove_image']) && $_POST['remove_image'] === '1';
    
    // Validar campos obligatorios
    if (empty($nombre) || empty($codigo)) {
        throw new Exception('El nombre y el código son campos obligatorios');
    }
    
    // Iniciar transacción
    $conn->begin_transaction();
    
    // Manejo de la imagen
    $ruta_imagen = $imagen_actual;
    $imagen_subida = false;
    
    // Verificar si se subió un archivo
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $archivo = $_FILES['imagen'];
        $nombre_archivo = basename($archivo['name']);
        $tipo_archivo = strtolower(pathinfo($nombre_archivo, PATHINFO_EXTENSION));
        $tamano_archivo = $archivo['size'];
        $archivo_temporal = $archivo['tmp_name'];
        
        // Validar tipo de archivo
        $extensiones_permitidas = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($tipo_archivo, $extensiones_permitidas)) {
            throw new Exception('Solo se permiten archivos JPG, JPEG, PNG y GIF');
        }
        
        // Validar tamaño de archivo (máximo 2MB)
        $tamano_maximo = 2 * 1024 * 1024; // 2MB
        if ($tamano_archivo > $tamano_maximo) {
            throw new Exception('El tamaño del archivo no debe exceder los 2MB');
        }
        
        // Crear directorio de uploads si no existe
        $directorio_uploads = '../../uploads/elementos/';
        if (!is_dir($directorio_uploads)) {
            if (!mkdir($directorio_uploads, 0755, true)) {
                throw new Exception('No se pudo crear el directorio de uploads');
            }
        }
        
        // Generar nombre único para el archivo
        $nombre_unico = uniqid('elemento_') . '.' . $tipo_archivo;
        $ruta_destino = $directorio_uploads . $nombre_unico;
        
        // Mover el archivo subido al directorio de destino
        if (move_uploaded_file($archivo_temporal, $ruta_destino)) {
            // Si se subió una nueva imagen, eliminar la anterior si existe
            if (!empty($imagen_actual) && file_exists('../../' . $imagen_actual)) {
                unlink('../../' . $imagen_actual);
            }
            $ruta_imagen = 'uploads/elementos/' . $nombre_unico;
            $imagen_subida = true;
        } else {
            throw new Exception('Error al subir la imagen');
        }
    } 
    // Si se solicitó eliminar la imagen actual
    elseif ($eliminar_imagen && !empty($imagen_actual)) {
        if (file_exists('../../' . $imagen_actual)) {
            unlink('../../' . $imagen_actual);
        }
        $ruta_imagen = '';
    }
    
    // Preparar la consulta SQL para actualizar el elemento
    $sql = "UPDATE elementos SET 
            nombreele = ?, 
            codigoele = ?, 
            caracteristicasele = ?, 
            cantidadele = ?, 
            estadoelemento = ?, 
            descripcionele = ?,
            imagen = ?
            WHERE IDele = ?";
    
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception('Error al preparar la consulta: ' . $conn->error);
    }
    
    $stmt->bind_param('sssissii', 
        $nombre,
        $codigo,
        $categoria,
        $stock,
        $estado,
        $descripcion,
        $ruta_imagen,
        $elemento_id
    );
    
    if (!$stmt->execute()) {
        // Si hay un error en la consulta, revertir la subida de la imagen si se subió una nueva
        if ($imagen_subida && !empty($ruta_imagen) && file_exists('../../' . $ruta_imagen)) {
            unlink('../../' . $ruta_imagen);
        }
        throw new Exception('Error al actualizar el elemento: ' . $stmt->error);
    }
    
    // Confirmar la transacción
    $conn->commit();
    
    $response = [
        'success' => true,
        'message' => 'Elemento actualizado correctamente',
        'imagen' => $ruta_imagen
    ];
    
} catch (Exception $e) {
    // Revertir la transacción en caso de error
    $conn->rollback();
    $response = [
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ];
}

// Cerrar la conexión
if (isset($stmt)) $stmt->close();
$conn->close();

// Devolver la respuesta en formato JSON
echo json_encode($response);
?>
