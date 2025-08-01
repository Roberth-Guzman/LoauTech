<?php
session_start();
require_once('../../../conexion.php');

// Verificar permisos
if (!isset($_SESSION['usuario']) || ($_SESSION['usuario']['rol'] !== 'admin' && $_SESSION['usuario']['rol'] !== 'almacenes')) {
    header('Location: /index.php');
    exit;
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    header("Location: panel-inventario.php?error=ID de elemento no válido");
    exit;
}

// Obtener los datos del elemento
$query = "SELECT * FROM elementos WHERE IDele = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: panel-inventario.php?error=Elemento no encontrado");
    exit;
}

$elemento = $result->fetch_assoc();

// Función para formatear el estado
function formatearEstado($estado) {
    switch ($estado) {
        case 'activo':
            return '<span class="px-2 py-1 text-xs font-semibold leading-tight text-green-700 bg-green-100 rounded-full">Disponible</span>';
        case 'en prestamo':
            return '<span class="px-2 py-1 text-xs font-semibold leading-tight text-yellow-700 bg-yellow-100 rounded-full">En préstamo</span>';
        case 'inactivo':
            return '<span class="px-2 py-1 text-xs font-semibold leading-tight text-red-700 bg-red-100 rounded-full">Inactivo</span>';
        default:
            return ucfirst($estado);
    }
}

// Función para formatear el estado del elemento
function formatearEstadoElemento($estado) {
    if ($estado === 'activo') {
        return '<span class="px-2 py-1 text-xs font-semibold leading-tight text-green-700 bg-green-100 rounded-full">Activo</span>';
    } else {
        return '<span class="px-2 py-1 text-xs font-semibold leading-tight text-red-700 bg-red-100 rounded-full">Inactivo</span>';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles del Elemento - Gestión de Inventario</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="min-h-screen">
        <!-- Navbar -->
        <nav class="bg-gray-800 text-white shadow-lg">
            <div class="container mx-auto px-4 py-3 flex justify-between items-center">
                <div class="flex items-center space-x-2">
                    <i class="fas fa-boxes"></i>
                    <span class="font-bold">Detalles del Elemento</span>
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
                        <i class="fas fa-info-circle mr-2"></i>
                        Detalles del Elemento
                    </h2>
                </div>
                
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Columna Izquierda - Imagen -->
                        <div class="md:col-span-1">
                            <div class="bg-gray-100 rounded-lg p-4 border border-gray-200">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Imagen del Elemento</h3>
                                <?php if (!empty($elemento['imagen'])): ?>
                                    <img src="/<?= htmlspecialchars($elemento['imagen']) ?>" 
                                         alt="<?= htmlspecialchars($elemento['nombreele']) ?>" 
                                         class="w-full h-64 object-contain mx-auto">
                                <?php else: ?>
                                    <div class="bg-gray-200 rounded-lg h-64 flex items-center justify-center">
                                        <i class="fas fa-image text-gray-400 text-5xl"></i>
                                    </div>
                                    <p class="text-sm text-gray-500 text-center mt-2">Sin imagen</p>
                                <?php endif; ?>
                            </div>
                            
                            <div class="mt-4">
                                <a href="editar.php?id=<?= $elemento['IDele'] ?>" 
                                   class="w-full flex items-center justify-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                    <i class="fas fa-edit mr-2"></i> Editar Elemento
                                </a>
                            </div>
                        </div>
                        
                        <!-- Columna Derecha - Detalles -->
                        <div class="md:col-span-2">
                            <div class="bg-white overflow-hidden">
                                <div class="px-4 py-5 sm:px-6">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                                        Información del Elemento
                                    </h3>
                                    <p class="mt-1 max-w-2xl text-sm text-gray-500">
                                        Detalles completos del elemento en el inventario.
                                    </p>
                                </div>
                                <div class="border-t border-gray-200">
                                    <dl>
                                        <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                            <dt class="text-sm font-medium text-gray-500">
                                                Nombre
                                            </dt>
                                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                                <?= htmlspecialchars($elemento['nombreele']) ?>
                                            </dd>
                                        </div>
                                        <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                            <dt class="text-sm font-medium text-gray-500">
                                                Código
                                            </dt>
                                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                                <?= htmlspecialchars($elemento['codigoele']) ?>
                                            </dd>
                                        </div>
                                        <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                            <dt class="text-sm font-medium text-gray-500">
                                                Código de Inventario
                                            </dt>
                                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                                <?= !empty($elemento['codigoinventario']) ? htmlspecialchars($elemento['codigoinventario']) : 'No especificado' ?>
                                            </dd>
                                        </div>
                                        <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                            <dt class="text-sm font-medium text-gray-500">
                                                Cantidad
                                            </dt>
                                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                                <?= intval($elemento['cantidadele']) ?>
                                                <?php if ($elemento['cantidadest'] === 'inactivo'): ?>
                                                    <span class="ml-2 text-xs text-red-600">(Inactivo)</span>
                                                <?php endif; ?>
                                            </dd>
                                        </div>
                                        <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                            <dt class="text-sm font-medium text-gray-500">
                                                Estado
                                            </dt>
                                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                                <?= formatearEstado($elemento['estado']) ?>
                                            </dd>
                                        </div>
                                        <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                            <dt class="text-sm font-medium text-gray-500">
                                                Estado del Elemento
                                            </dt>
                                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                                <?= formatearEstadoElemento($elemento['estadoelemento']) ?>
                                            </dd>
                                        </div>
                                        <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                            <dt class="text-sm font-medium text-gray-500">
                                                Categoría
                                            </dt>
                                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                                <?= !empty($elemento['caracteristicasele']) ? htmlspecialchars($elemento['caracteristicasele']) : 'No especificada' ?>
                                            </dd>
                                        </div>
                                        <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                            <dt class="text-sm font-medium text-gray-500">
                                                Descripción
                                            </dt>
                                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2 whitespace-pre-line">
                                                <?= !empty($elemento['descripcionele']) ? nl2br(htmlspecialchars($elemento['descripcionele'])) : 'No hay descripción disponible.' ?>
                                            </dd>
                                        </div>
                                    </dl>
                                </div>
                            </div>
                            
                            <div class="mt-6 flex justify-end space-x-3">
                                <a href="panel-inventario.php" 
                                   class="bg-gray-300 text-gray-800 px-4 py-2 rounded-md hover:bg-gray-400">
                                    <i class="fas fa-arrow-left mr-2"></i> Volver al listado
                                </a>
                                <a href="editar.php?id=<?= $elemento['IDele'] ?>" 
                                   class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                                    <i class="fas fa-edit mr-2"></i> Editar Elemento
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
