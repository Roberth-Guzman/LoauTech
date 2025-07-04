<?php
session_start();
include '../../conexion.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['rol'] != 'admin') {
    header("Location: ../../login.php");
    exit;
}

// Conexión sin seleccionar BD
$conn = new mysqli("localhost", "root", "");

// Función para obtener bases de datos (excluyendo las del sistema)
function obtenerBasesDeDatos($link) {
    $bases = array();
    $result = mysqli_query($link, "SHOW DATABASES");
    
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $db = $row['Database'];
            if (!in_array($db, array('information_schema', 'mysql', 'performance_schema', 'phpmyadmin', 'sys'))) {
                $bases[] = $db;
            }
        }
    }
    return $bases;
}

$bases_de_datos = obtenerBasesDeDatos($conn);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bases de Datos</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <?php include '../includes/header.php'; ?>
    
    <div class="ml-64 p-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold">Gestión de Bases de Datos</h2>
                <a href="crear_db.php" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    <i class="fas fa-plus mr-2"></i> Nueva Base de Datos
                </a>
            </div>
            
            <?php if (!DB_EXISTS): ?>
                <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-4">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    La base de datos principal no está disponible. Puedes crear una nueva o importar un backup.
                </div>
            <?php endif; ?>
            
            <?php if (isset($_GET['error'])): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <?= htmlspecialchars($_GET['error']) ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_GET['success'])): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    <?= htmlspecialchars($_GET['success']) ?>
                </div>
            <?php endif; ?>
            
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white">
                    <thead class="bg-gray-800 text-white">
                        <tr>
                            <th class="py-3 px-4">Nombre</th>
                            <th class="py-3 px-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700">
                        <?php if (count($bases_de_datos) > 0): ?>
                            <?php foreach ($bases_de_datos as $db): ?>
                            <tr class="border-b border-gray-200 hover:bg-gray-50">
                                <td class="py-3 px-4"><?= htmlspecialchars($db) ?></td>
                                <td class="py-3 px-4">
                                    <div class="flex space-x-2">
                                        <a href="exportar_db.php?db=<?= urlencode($db) ?>" 
                                           class="bg-green-500 text-white p-2 rounded hover:bg-green-600"
                                           title="Exportar">
                                            <i class="fas fa-file-export"></i>
                                        </a>
                                        <a href="confirmar_eliminacion.php?db=<?= urlencode($db) ?>" 
                                           class="bg-red-500 text-white p-2 rounded hover:bg-red-600"
                                           title="Eliminar"
                                           onclick="return confirm('¿Está seguro de eliminar la base de datos <?= addslashes($db) ?>?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="2" class="py-4 text-center text-gray-500">
                                    No se encontraron bases de datos (excepto las del sistema)
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <?php include '../includes/footer.php'; ?>
</body>
</html>