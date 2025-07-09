<?php
session_start();
include '../../conexion.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['rol'] != 'admin') {
    header("Location: ../../login.php");
    exit;
}

$error_message = '';
$bd = "loatech"; // Nombre de la base de datos principal

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['sql_file'])) {
    if ($_FILES['sql_file']['error'] === UPLOAD_ERR_OK) {
        $tmp_name = $_FILES['sql_file']['tmp_name'];
        $sql_content = file_get_contents($tmp_name);
        
        // Conexión al servidor sin seleccionar BD
        $conn = new mysqli("localhost", "root", "");
        
        if ($conn->connect_error) {
            $error_message = "Error de conexión: " . $conn->connect_error;
        } else {
            // Verificar si el SQL contiene la creación de la BD
            if (stripos($sql_content, "CREATE DATABASE") === false && 
                stripos($sql_content, "USE `$bd`") === false) {
                // Agregar sentencias para crear y usar la BD si no están presentes
                $sql_content = "DROP DATABASE IF EXISTS `$bd`;\nCREATE DATABASE `$bd` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;\nUSE `$bd`;\n" . $sql_content;
            }
            
            // Ejecutar el SQL completo
            if (mysqli_multi_query($conn, $sql_content)) {
                // Esperar a que todas las consultas se completen
                while ($conn->more_results()) {
                    $conn->next_result();
                }
                
                // Verificar si la importación fue exitosa
                if ($conn->select_db($bd)) {
                    header('Location: eliminardb.php?success=Base+de+datos+importada+correctamente');
                    exit;
                } else {
                    $error_message = "La importación no creó la base de datos correctamente";
                }
            } else {
                $error_message = "Error al importar: " . mysqli_error($conn);
            }
        }
    } else {
        $error_message = "Error al subir el archivo: Código " . $_FILES['sql_file']['error'];
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Importar Base de Datos</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <?php include '../includes/header.php'; ?>
    
    <div class="ml-64 p-6">
        <div class="max-w-md mx-auto bg-white rounded-lg shadow p-6">
            <div class="text-center mb-6">
                <i class="fas fa-file-import text-blue-500 text-4xl mb-3"></i>
                <h2 class="text-xl font-bold">Importar Base de Datos</h2>
                <p class="text-sm text-gray-500 mt-1">Se importará a la base de datos: <?= htmlspecialchars($bd) ?></p>
            </div>
            
            <?php if ($error_message): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <?= htmlspecialchars($error_message) ?>
                </div>
            <?php endif; ?>
            
            <form method="post" enctype="multipart/form-data" class="space-y-4">
                <div>
                    <label for="sql_file" class="block text-sm font-medium text-gray-700 mb-1">Archivo SQL</label>
                    <input type="file" id="sql_file" name="sql_file" accept=".sql" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    <p class="mt-1 text-sm text-gray-500">Seleccione un archivo .sql para importar</p>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <a href="eliminardb.php" class="bg-gray-300 text-gray-800 px-4 py-2 rounded-md hover:bg-gray-400">
                        Cancelar
                    </a>
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                        <i class="fas fa-upload mr-2"></i> Importar
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <?php include '../includes/footer.php'; ?>
</body>
</html>