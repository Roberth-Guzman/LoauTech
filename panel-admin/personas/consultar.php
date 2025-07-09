<?php
session_start();
include '../../conexion.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['rol'] != 'admin') {
    header("Location: ../../login.php");
    exit;
}

// Consulta para obtener personas
$query = "SELECT p.*, r.rol, c.correocont, c.numerocont 
          FROM personas p
          LEFT JOIN roles r ON p.IDper = r.idper
          LEFT JOIN contactos c ON p.IDper = c.IDperso";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <?php include '../includes/header.php'; ?>
    
    <div class="ml-64 p-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold">Gestión de Usuarios</h2>
                <a href="editar.php" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    <i class="fas fa-plus mr-2"></i> Nuevo Usuario
                </a>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white">
                    <thead class="bg-gray-800 text-white">
                        <tr>
                            <th class="py-3 px-4">ID</th>
                            <th class="py-3 px-4">Nombre</th>
                            <th class="py-3 px-4">Documento</th>
                            <th class="py-3 px-4">Rol</th>
                            <th class="py-3 px-4">Contacto</th>
                            <th class="py-3 px-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700">
                        <?php while($row = $result->fetch_assoc()): ?>
                        <tr class="border-b border-gray-200 hover:bg-gray-50">
                            <td class="py-3 px-4"><?= $row['IDper'] ?></td>
                            <td class="py-3 px-4"><?= $row['nombrecompletoper'] ?></td>
                            <td class="py-3 px-4"><?= $row['tipodocumento'] ?> <?= $row['numerodoc'] ?></td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-1 rounded-full text-xs 
                                    <?= $row['rol'] == 'admin' ? 'bg-purple-200 text-purple-800' : 
                                       ($row['rol'] == 'porteria' ? 'bg-blue-200 text-blue-800' : 'bg-green-200 text-green-800') ?>">
                                    <?= ucfirst($row['rol']) ?>
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                <div><?= $row['correocont'] ?></div>
                                <div class="text-sm text-gray-500"><?= $row['numerocont'] ?></div>
                            </td>
                            <td class="py-3 px-4">
                                <div class="flex space-x-2">
                                    <a href="editar.php?id=<?= $row['IDper'] ?>" 
                                       class="bg-yellow-500 text-white p-2 rounded hover:bg-yellow-600">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="eliminar.php?id=<?= $row['IDper'] ?>" 
                                       class="bg-red-500 text-white p-2 rounded hover:bg-red-600"
                                       onclick="return confirm('¿Eliminar este usuario y todos sus datos relacionados?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="mt-4 flex justify-between items-center">
                <div>
                    <a href="../database/eliminar2.php" 
                       class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700"
                       onclick="return confirm('¿Está seguro de limpiar TODOS los usuarios? Esta acción no se puede deshacer.')">
                        <i class="fas fa-trash-alt mr-2"></i> Limpiar Tabla
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <?php include '../includes/footer.php'; ?>
</body>
</html>