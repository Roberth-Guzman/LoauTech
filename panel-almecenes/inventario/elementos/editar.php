<?php
session_start();
require_once('../../../conexion.php');

// Verificar permisos
if (!isset($_SESSION['usuario']) || ($_SESSION['usuario']['rol'] !== 'admin' && $_SESSION['usuario']['rol'] !== 'almacenes')) {
    header('Location: /index.php');
    exit;
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$elemento = [
    'nombreele' => '',
    'cantidadele' => 1,
    'cantidadest' => 'activo',
    'codigoele' => '',
    'codigoinventario' => '',
    'descripcionele' => '',
    'caracteristicasele' => 'Otros',
    'estado' => 'activo',
    'estadoelemento' => 'activo',
    'imagen' => ''
];

// Si es una edición, cargar los datos del elemento
if ($id > 0) {
    $query = "SELECT * FROM elementos WHERE IDele = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $elemento = $result->fetch_assoc();
    } else {
        header("Location: panel-inventario.php?error=Elemento no encontrado");
        exit;
    }
}

// Procesar el formulario cuando se envía
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validar y limpiar los datos
    $nombreele = $conn->real_escape_string(trim($_POST['nombreele']));
    $cantidadele = intval($_POST['cantidadele']);
    $cantidadest = $_POST['cantidadest'] === 'activo' ? 'activo' : 'inactivo';
    $codigoele = $conn->real_escape_string(trim($_POST['codigoele']));
    $codigoinventario = $conn->real_escape_string(trim($_POST['codigoinventario']));
    $descripcionele = $conn->real_escape_string(trim($_POST['descripcionele']));
    $caracteristicasele = $conn->real_escape_string(trim($_POST['caracteristicasele']));
    $estado = in_array($_POST['estado'], ['activo', 'inactivo', 'en prestamo']) ? $_POST['estado'] : 'activo';
    $estadoelemento = $_POST['estadoelemento'] === 'activo' ? 'activo' : 'inactivo';
    
    // Validaciones básicas
    if (empty($nombreele) || empty($codigoele)) {
        $error = "El nombre y el código son campos obligatorios.";
    } else {
        // Procesar la imagen si se subió una
        $imagen = $elemento['imagen']; // Mantener la imagen actual por defecto
        
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
            $directorio = '../../../uploads/inventario/';
            
            // Crear el directorio si no existe
            if (!file_exists($directorio)) {
                mkdir($directorio, 0777, true);
            }
            
            // Validar el tipo de archivo
            $permitidos = ['image/jpeg', 'image/png', 'image/gif'];
            $tipo = mime_content_type($_FILES['imagen']['tmp_name']);
            
            if (in_array($tipo, $permitidos)) {
                // Generar un nombre único para la imagen
                $nombre_archivo = uniqid('item_') . '_' . basename($_FILES['imagen']['name']);
                $ruta_archivo = $directorio . $nombre_archivo;
                
                if (move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta_archivo)) {
                    // Si se subió correctamente, actualizar la ruta de la imagen
                    $imagen = 'uploads/inventario/' . $nombre_archivo;
                    
                    // Eliminar la imagen anterior si existe
                    if (!empty($elemento['imagen']) && file_exists('../../../' . $elemento['imagen'])) {
                        unlink('../../../' . $elemento['imagen']);
                    }
                } else {
                    $error = "Error al subir la imagen. Inténtalo de nuevo.";
                }
            } else {
                $error = "Solo se permiten archivos de imagen (JPEG, PNG, GIF).";
            }
        }
        
        // Si no hay errores, guardar en la base de datos
        if (!isset($error)) {
            if ($id > 0) {
                // Actualizar elemento existente
                $query = "UPDATE elementos SET 
                          nombreele = ?, 
                          cantidadele = ?, 
                          cantidadest = ?,
                          codigoele = ?,
                          codigoinventario = ?,
                          descripcionele = ?,
                          caracteristicasele = ?,
                          estado = ?,
                          estadoelemento = ?,
                          imagen = ?
                          WHERE IDele = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("sississsssi", 
                    $nombreele, $cantidadele, $cantidadest, $codigoele, $codigoinventario,
                    $descripcionele, $caracteristicasele, $estado, $estadoelemento, $imagen, $id
                );
            } else {
                // Crear nuevo elemento
                $query = "INSERT INTO elementos 
                         (nombreele, cantidadele, cantidadest, codigoele, codigoinventario, descripcionele, 
                          caracteristicasele, estado, estadoelemento, imagen) 
                         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("sississsss", 
                    $nombreele, $cantidadele, $cantidadest, $codigoele, $codigoinventario,
                    $descripcionele, $caracteristicasele, $estado, $estadoelemento, $imagen
                );
            }
            
            if ($stmt->execute()) {
                $mensaje = $id > 0 ? 'Elemento actualizado correctamente' : 'Elemento creado correctamente';
                header("Location: panel-inventario.php?success=" . urlencode($mensaje));
                exit;
            } else {
                $error = "Error al guardar el elemento: " . $conn->error;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $id > 0 ? 'Editar' : 'Nuevo' ?> Elemento - Gestión de Inventario</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .preview-image {
            max-width: 200px;
            max-height: 200px;
            margin-top: 10px;
            display: none;
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen">
        <!-- Navbar -->
        <nav class="bg-gray-800 text-white shadow-lg">
            <div class="container mx-auto px-4 py-3 flex justify-between items-center">
                <div class="flex items-center space-x-2">
                    <i class="fas fa-boxes"></i>
                    <span class="font-bold"><?= $id > 0 ? 'Editar' : 'Nuevo' ?> Elemento</span>
                </div>
                <div>
                    <a href="panel-inventario.php" class="text-gray-300 hover:text-white px-3 py-2 rounded-md text-sm font-medium">
                        <i class="fas fa-arrow-left mr-1"></i> Volver al listado
                    </a>
                </div>
            </div>
        </nav>

        <div class="container mx-auto px-4 py-8">
            <div class="max-w-4xl mx-auto bg-white rounded-lg shadow overflow-hidden">
                <div class="bg-gradient-to-r from-blue-600 to-purple-700 text-white px-6 py-4">
                    <h2 class="text-xl font-bold">
                        <i class="fas <?= $id > 0 ? 'fa-edit' : 'fa-plus-circle' ?> mr-2"></i>
                        <?= $id > 0 ? 'Editar Elemento' : 'Agregar Nuevo Elemento' ?>
                    </h2>
                </div>
                
                <div class="p-6">
                    <?php if (isset($error)): ?>
                        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                            <p><?= $error ?></p>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" enctype="multipart/form-data" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Columna Izquierda -->
                            <div class="space-y-4">
                                <!-- Nombre -->
                                <div>
                                    <label for="nombreele" class="block text-sm font-medium text-gray-700 mb-1">
                                        Nombre del Elemento <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" id="nombreele" name="nombreele" required
                                           value="<?= htmlspecialchars($elemento['nombreele']) ?>"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                
                                <!-- Código -->
                                <div>
                                    <label for="codigoele" class="block text-sm font-medium text-gray-700 mb-1">
                                        Código <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" id="codigoele" name="codigoele" required
                                           value="<?= htmlspecialchars($elemento['codigoele']) ?>"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                
                                <!-- Código de Inventario -->
                                <div>
                                    <label for="codigoinventario" class="block text-sm font-medium text-gray-700 mb-1">
                                        Código de Inventario
                                    </label>
                                    <input type="text" id="codigoinventario" name="codigoinventario"
                                           value="<?= htmlspecialchars($elemento['codigoinventario']) ?>"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                
                                <!-- Cantidad -->
                                <div>
                                    <label for="cantidadele" class="block text-sm font-medium text-gray-700 mb-1">
                                        Cantidad
                                    </label>
                                    <input type="number" id="cantidadele" name="cantidadele" min="0"
                                           value="<?= intval($elemento['cantidadele']) ?>"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                
                                <!-- Estado de Cantidad -->
                                <div>
                                    <label for="cantidadest" class="block text-sm font-medium text-gray-700 mb-1">
                                        Estado de Cantidad
                                    </label>
                                    <select id="cantidadest" name="cantidadest"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                        <option value="activo" <?= $elemento['cantidadest'] === 'activo' ? 'selected' : '' ?>>Activo</option>
                                        <option value="inactivo" <?= $elemento['cantidadest'] === 'inactivo' ? 'selected' : '' ?>>Inactivo</option>
                                    </select>
                                </div>
                            </div>
                            
                            <!-- Columna Derecha -->
                            <div class="space-y-4">
                                <!-- Estado -->
                                <div>
                                    <label for="estado" class="block text-sm font-medium text-gray-700 mb-1">
                                        Estado
                                    </label>
                                    <select id="estado" name="estado"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                        <option value="activo" <?= $elemento['estado'] === 'activo' ? 'selected' : '' ?>>Disponible</option>
                                        <option value="en prestamo" <?= $elemento['estado'] === 'en prestamo' ? 'selected' : '' ?>>En préstamo</option>
                                        <option value="inactivo" <?= $elemento['estado'] === 'inactivo' ? 'selected' : '' ?>>Inactivo</option>
                                    </select>
                                </div>
                                
                                <!-- Estado del Elemento -->
                                <div>
                                    <label for="estadoelemento" class="block text-sm font-medium text-gray-700 mb-1">
                                        Estado del Elemento
                                    </label>
                                    <select id="estadoelemento" name="estadoelemento"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                        <option value="activo" <?= $elemento['estadoelemento'] === 'activo' ? 'selected' : '' ?>>Activo</option>
                                        <option value="inactivo" <?= $elemento['estadoelemento'] === 'inactivo' ? 'selected' : '' ?>>Inactivo</option>
                                    </select>
                                </div>
                                
                                <!-- Características -->
                                <div>
                                    <label for="caracteristicasele" class="block text-sm font-medium text-gray-700 mb-1">
                                        Categoría
                                    </label>
                                    <select id="caracteristicasele" name="caracteristicasele"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                        <option value="Computadores" <?= $elemento['caracteristicasele'] === 'Computadores' ? 'selected' : '' ?>>Computadores</option>
                                        <option value="Monitores" <?= $elemento['caracteristicasele'] === 'Monitores' ? 'selected' : '' ?>>Monitores</option>
                                        <option value="Teclados" <?= $elemento['caracteristicasele'] === 'Teclados' ? 'selected' : '' ?>>Teclados</option>
                                        <option value="Mouse" <?= $elemento['caracteristicasele'] === 'Mouse' ? 'selected' : '' ?>>Mouse</option>
                                        <option value="Impresoras" <?= $elemento['caracteristicasele'] === 'Impresoras' ? 'selected' : '' ?>>Impresoras</option>
                                        <option value="Muebles" <?= $elemento['caracteristicasele'] === 'Muebles' ? 'selected' : '' ?>>Muebles</option>
                                        <option value="Otros" <?= empty($elemento['caracteristicasele']) || $elemento['caracteristicasele'] === 'Otros' ? 'selected' : '' ?>>Otros</option>
                                    </select>
                                </div>
                                
                                <!-- Imagen -->
                                <div>
                                    <label for="imagen" class="block text-sm font-medium text-gray-700 mb-1">
                                        Imagen del Elemento
                                    </label>
                                    <div class="mt-1 flex items-center">
                                        <input type="file" id="imagen" name="imagen" accept="image/*" 
                                               class="block w-full text-sm text-gray-500
                                                      file:mr-4 file:py-2 file:px-4
                                                      file:rounded-md file:border-0
                                                      file:text-sm file:font-semibold
                                                      file:bg-blue-50 file:text-blue-700
                                                      hover:file:bg-blue-100"
                                               onchange="previewImage(this)">
                                    </div>
                                    
                                    <!-- Vista previa de la imagen -->
                                    <div class="mt-2">
                                        <?php if (!empty($elemento['imagen'])): ?>
                                            <p class="text-sm text-gray-500 mb-1">Imagen actual:</p>
                                            <img src="/<?= htmlspecialchars($elemento['imagen']) ?>" alt="Imagen actual" class="h-32 object-cover rounded">
                                            <p class="text-xs text-gray-500 mt-1">Subir una nueva imagen la reemplazará.</p>
                                        <?php endif; ?>
                                        
                                        <img id="imagePreview" src="#" alt="Vista previa de la imagen" class="preview-image rounded">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Descripción (ancho completo) -->
                        <div>
                            <label for="descripcionele" class="block text-sm font-medium text-gray-700 mb-1">
                                Descripción
                            </label>
                            <textarea id="descripcionele" name="descripcionele" rows="3"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"><?= htmlspecialchars($elemento['descripcionele']) ?></textarea>
                        </div>
                        
                        <!-- Botones de acción -->
                        <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                            <a href="panel-inventario.php" class="bg-gray-300 text-gray-800 px-4 py-2 rounded-md hover:bg-gray-400">
                                <i class="fas fa-times mr-2"></i> Cancelar
                            </a>
                            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                                <i class="fas fa-save mr-2"></i> <?= $id > 0 ? 'Actualizar' : 'Guardar' ?>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Función para previsualizar la imagen seleccionada
        function previewImage(input) {
            const preview = document.getElementById('imagePreview');
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                }
                
                reader.readAsDataURL(input.files[0]);
            }
        }
        
        // Mostrar/ocultar vista previa según si hay una imagen seleccionada
        document.addEventListener('DOMContentLoaded', function() {
            const imageInput = document.getElementById('imagen');
            if (imageInput) {
                imageInput.addEventListener('change', function() {
                    const preview = document.getElementById('imagePreview');
                    if (this.files && this.files[0]) {
                        preview.style.display = 'block';
                    } else {
                        preview.style.display = 'none';
                    }
                });
            }
        });
    </script>
</body>
</html>
