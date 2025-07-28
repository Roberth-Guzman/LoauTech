<?php
session_start();
include '../../conexion.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['rol'] != 'admin') {
    header("Location: ../../login.php");
    exit;
}

// Consulta para obtener elementos desde la tabla 'elementos'
$query = "SELECT * FROM elementos ORDER BY IDele DESC";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Elementos</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <?php include '../includes/header.php'; ?>
    
    <div class="ml-64 p-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold">Gestión de Elementos</h2>
                <a href="editar.php" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    <i class="fas fa-plus mr-2"></i> Nuevo Elemento
                </a>
            </div>
            
            <?php if (isset($_GET['success'])): ?>
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                    <p><?= htmlspecialchars($_GET['success']) ?></p>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_GET['error'])): ?>
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                    <p><?= htmlspecialchars($_GET['error']) ?></p>
                </div>
            <?php endif; ?>
            
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white">
                    <thead class="bg-gray-800 text-white">
                        <tr>
                            <th class="py-3 px-4">ID</th>
                            <th class="py-3 px-4">Nombre</th>
                            <th class="py-3 px-4">Código</th>
                            <th class="py-3 px-4">Código Inventario</th>
                            <th class="py-3 px-4">Cantidad</th>
                            <th class="py-3 px-4">Estado</th>
                            <th class="py-3 px-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700">
                        <?php if ($result->num_rows > 0): ?>
                            <?php while($row = $result->fetch_assoc()): 
                                $estadoClase = $row['estado'] === 'activo' ? 'bg-green-100 text-green-800' : 
                                             ($row['estado'] === 'en prestamo' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800');
                            ?>
                            <tr class="border-b border-gray-200 hover:bg-gray-50">
                                <td class="py-3 px-4"><?= $row['IDele'] ?></td>
                                <td class="py-3 px-4 font-medium"><?= htmlspecialchars($row['nombreele']) ?></td>
                                <td class="py-3 px-4"><?= htmlspecialchars($row['codigoele']) ?></td>
                                <td class="py-3 px-4"><?= htmlspecialchars($row['codigoinventario']) ?></td>
                                <td class="py-3 px-4">
                                    <span class="px-2 py-1 rounded-full bg-gray-100">
                                        <?= $row['cantidadele'] ?>
                                    </span>
                                </td>
                                <td class="py-3 px-4">
                                    <span class="px-2 py-1 rounded-full <?= $estadoClase ?>">
                                        <?= ucfirst($row['estado']) ?>
                                    </span>
                                </td>
                                <td class="py-3 px-4">
                                    <div class="flex space-x-2">
                                        <a href="editar.php?id=<?= $row['IDele'] ?>" 
                                           class="bg-yellow-500 text-white p-2 rounded hover:bg-yellow-600"
                                           title="Editar elemento">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="eliminar.php?id=<?= $row['IDele'] ?>" 
                                           class="bg-red-500 text-white p-2 rounded hover:bg-red-600"
                                           onclick="return confirm('¿Estás seguro de eliminar este elemento?')"
                                           title="Eliminar elemento">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                        <a href="detalles.php?id=<?= $row['IDele'] ?>" 
                                           class="bg-blue-500 text-white p-2 rounded hover:bg-blue-600"
                                           title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="py-4 text-center text-gray-500">
                                    No hay elementos registrados. <a href="editar.php" class="text-blue-600 hover:underline">Agregar nuevo elemento</a>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Paginación -->
            <div class="flex justify-between items-center mt-4 px-4">
                <div class="text-sm text-gray-700">
                    Mostrando <span class="font-medium">1</span> a <span class="font-medium">10</span> de <span class="font-medium">20</span> resultados
                </div>
                <div class="flex space-x-2">
                    <button class="px-3 py-1 border rounded bg-white text-gray-700 hover:bg-gray-50">Anterior</button>
                    <button class="px-3 py-1 border rounded bg-blue-600 text-white hover:bg-blue-700">1</button>
                    <button class="px-3 py-1 border rounded bg-white text-gray-700 hover:bg-gray-50">2</button>
                    <button class="px-3 py-1 border rounded bg-white text-gray-700 hover:bg-gray-50">Siguiente</button>
                </div>
            </div>
        </div>
    </div>
    
    <?php include '../includes/footer.php'; ?>
    
    <script>
        // Cerrar automáticamente los mensajes después de 5 segundos
        setTimeout(function() {
            const alerts = document.querySelectorAll('[role="alert"]');
            alerts.forEach(alert => {
                alert.style.transition = 'opacity 1s';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 1000);
            });
        }, 5000);
    </script>
</body>
</html>