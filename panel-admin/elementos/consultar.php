<?php
session_start();
include '../../conexion.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['rol'] != 'admin') {
    header("Location: ../../login.php");
    exit;
}

// Consulta para obtener elementos
$query = "SELECT e.*, p.nombrecompletoper 
          FROM ingresoelementos e
          LEFT JOIN personas p ON e.IDPER = p.IDper";
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
            
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white">
                    <thead class="bg-gray-800 text-white">
                        <tr>
                            <th class="py-3 px-4">ID</th>
                            <th class="py-3 px-4">Nombre</th>
                            <th class="py-3 px-4">Tipo</th>
                            <th class="py-3 px-4">Descripción</th>
                            <th class="py-3 px-4">Responsable</th>
                            <th class="py-3 px-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700">
                        <?php while($row = $result->fetch_assoc()): ?>
                        <tr class="border-b border-gray-200 hover:bg-gray-50">
                            <td class="py-3 px-4"><?= $row['IDingele'] ?></td>
                            <td class="py-3 px-4"><?= $row['nombreingele'] ?></td>
                            <td class="py-3 px-4"><?= $row['tipoelemento'] ?></td>
                            <td class="py-3 px-4"><?= $row['descripcioningele'] ?></td>
                            <td class="py-3 px-4"><?= $row['nombrecompletoper'] ?></td>
                            <td class="py-3 px-4">
                                <div class="flex space-x-2">
                                    <a href="editar.php?id=<?= $row['IDingele'] ?>" 
                                       class="bg-yellow-500 text-white p-2 rounded hover:bg-yellow-600">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="eliminar.php?id=<?= $row['IDingele'] ?>" 
                                       class="bg-red-500 text-white p-2 rounded hover:bg-red-600"
                                       onclick="return confirm('¿Eliminar este elemento?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <?php include '../includes/footer.php'; ?>
</body>
</html>