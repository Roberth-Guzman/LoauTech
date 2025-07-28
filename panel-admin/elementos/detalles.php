<?php
session_start();
// Corregir la ruta de inclusión para que apunte correctamente al archivo de conexión
include '../../conexion.php';

// Verificar autenticación y permisos
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] != 'admin') {
    header("Location: ../../login.php");
    exit;
}

// Validar ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: consultar.php?error=ID inválido");
    exit;
}

$id = intval($_GET['id']);

// Obtener los datos del elemento
$query = "SELECT * FROM elementos WHERE IDele = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: consultar.php?error=Elemento no encontrado");
    exit;
}

$elemento = $result->fetch_assoc();

// Función para mostrar el estado con colores
function mostrarEstado($estado) {
    $clases = [
        'activo' => 'bg-green-100 text-green-800',
        'en prestamo' => 'bg-yellow-100 text-yellow-800',
        'inactivo' => 'bg-red-100 text-red-800'
    ];
    
    $clase = $clases[strtolower($estado)] ?? 'bg-gray-100 text-gray-800';
    return '<span class="px-2 py-1 rounded-full text-sm font-medium ' . $clase . '">' . ucfirst($estado) . '</span>';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles del Elemento - Panel de Administración</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <?php include '../includes/header.php'; ?>
    
    <div class="ml-64 p-6">
        <div class="max-w-4xl mx-auto bg-white rounded-lg shadow overflow-hidden">
            <!-- Encabezado -->
            <div class="bg-gray-800 text-white px-6 py-4 flex justify-between items-center">
                <h2 class="text-xl font-semibold">
                    <i class="fas fa-info-circle mr-2"></i>
                    Detalles del Elemento
                </h2>
                <div class="space-x-2">
                    <a href="editar.php?id=<?= $id ?>" 
                       class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded text-sm">
                        <i class="fas fa-edit mr-1"></i> Editar
                    </a>
                    <a href="consultar.php" 
                       class="bg-gray-500 hover:bg-gray-600 text-white px-3 py-1 rounded text-sm">
                        <i class="fas fa-arrow-left mr-1"></i> Volver
                    </a>
                </div>
            </div>
            
            <!-- Contenido -->
            <div class="p-6">
                <!-- Información básica -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4 border-b pb-2">Información Básica</h3>
                        <dl class="space-y-3">
                            <div class="flex">
                                <dt class="w-1/3 text-sm font-medium text-gray-500">ID:</dt>
                                <dd class="text-sm text-gray-900"><?= $elemento['IDele'] ?></dd>
                            </div>
                            <div class="flex">
                                <dt class="w-1/3 text-sm font-medium text-gray-500">Código:</dt>
                                <dd class="text-sm text-gray-900 font-medium"><?= htmlspecialchars($elemento['codigoele']) ?></dd>
                            </div>
                            <div class="flex">
                                <dt class="w-1/3 text-sm font-medium text-gray-500">Código de Inventario:</dt>
                                <dd class="text-sm text-gray-900 font-medium"><?= htmlspecialchars($elemento['codigoinventario']) ?></dd>
                            </div>
                            <div class="flex">
                                <dt class="w-1/3 text-sm font-medium text-gray-500">Nombre:</dt>
                                <dd class="text-sm text-gray-900"><?= htmlspecialchars($elemento['nombreele']) ?></dd>
                            </div>
                            <div class="flex">
                                <dt class="w-1/3 text-sm font-medium text-gray-500">Cantidad:</dt>
                                <dd class="text-sm text-gray-900"><?= $elemento['cantidadele'] ?></dd>
                            </div>
                            <div class="flex">
                                <dt class="w-1/3 text-sm font-medium text-gray-500">Estado de Cantidad:</dt>
                                <dd class="text-sm"><?= mostrarEstado($elemento['cantidadest']) ?></dd>
                            </div>
                        </dl>
                    </div>
                    
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4 border-b pb-2">Estado y Características</h3>
                        <dl class="space-y-3">
                            <div class="flex">
                                <dt class="w-1/3 text-sm font-medium text-gray-500">Estado:</dt>
                                <dd class="text-sm"><?= mostrarEstado($elemento['estado']) ?></dd>
                            </div>
                            <div class="flex">
                                <dt class="w-1/3 text-sm font-medium text-gray-500">Estado del Elemento:</dt>
                                <dd class="text-sm"><?= mostrarEstado($elemento['estadoelemento']) ?></dd>
                            </div>
                            <?php if (!empty($elemento['fecharegistroelemento'])): ?>
                            <div class="flex">
                                <dt class="w-1/3 text-sm font-medium text-gray-500">Fecha de Registro:</dt>
                                <dd class="text-sm text-gray-900"><?= date('d/m/Y H:i', strtotime($elemento['fecharegistroelemento'])) ?></dd>
                            </div>
                            <?php endif; ?>
                        </dl>
                    </div>
                </div>
                
                <!-- Descripción y Características -->
                <div class="space-y-6">
                    <?php if (!empty($elemento['descripcionele'])): ?>
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Descripción</h3>
                        <div class="bg-gray-50 p-4 rounded border border-gray-200 text-sm text-gray-700">
                            <?= nl2br(htmlspecialchars($elemento['descripcionele'])) ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($elemento['caracteristicasele'])): ?>
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Características Adicionales</h3>
                        <div class="bg-gray-50 p-4 rounded border border-gray-200 text-sm text-gray-700 whitespace-pre-line">
                            <?= nl2br(htmlspecialchars($elemento['caracteristicasele'])) ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                
                <!-- Historial de cambios (puedes implementarlo más adelante) -->
                <!--
                <div class="mt-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4 border-b pb-2">Historial de Cambios</h3>
                    <div class="bg-white shadow overflow-hidden sm:rounded-md">
                        <ul class="divide-y divide-gray-200">
                            <li class="px-4 py-4">
                                <div class="flex items-center justify-between">
                                    <p class="text-sm font-medium text-indigo-600 truncate">Actualización de estado</p>
                                    <div class="ml-2 flex-shrink-0 flex">
                                        <p class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Completado
                                        </p>
                                    </div>
                                </div>
                                <div class="mt-2 sm:flex sm:justify-between">
                                    <div class="sm:flex">
                                        <p class="flex items-center text-sm text-gray-500">
                                            <i class="fas fa-user-circle mr-1.5 h-5 w-5 text-gray-400"></i>
                                            Admin
                                        </p>
                                        <p class="mt-2 flex items-center text-sm text-gray-500 sm:mt-0 sm:ml-6">
                                            <i class="far fa-clock mr-1.5 h-4 w-4 text-gray-400"></i>
                                            2 horas atrás
                                        </p>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
                -->
            </div>
            
            <!-- Pie de página -->
            <div class="bg-gray-50 px-6 py-3 flex justify-end space-x-3">
                <a href="consultar.php" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded text-sm">
                    <i class="fas fa-arrow-left mr-1"></i> Volver a la lista
                </a>
                <a href="editar.php?id=<?= $id ?>" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm">
                    <i class="fas fa-edit mr-1"></i> Editar Elemento
                </a>
            </div>
        </div>
    </div>
    
    <?php include '../includes/footer.php'; ?>
</body>
</html>