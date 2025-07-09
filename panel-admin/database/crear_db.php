<?php
session_start();
include '../../conexion.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['rol'] != 'admin') {
    header("Location: ../../login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['db_name'])) {
    // Conexión sin seleccionar BD
    $conn = new mysqli("localhost", "root", "");
    
    $db_name = mysqli_real_escape_string($conn, $_POST['db_name']);
    
    if (mysqli_query($conn, "CREATE DATABASE IF NOT EXISTS $db_name")) {
        echo "<script>alert('Base de datos creada correctamente'); window.location.href='eliminardb.php';</script>";
    } else {
        echo "<script>alert('Error al crear la base de datos: " . mysqli_error($conn) . "');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Base de Datos</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <?php include '../includes/header.php'; ?>
    
    <div class="ml-64 p-6">
        <div class="max-w-md mx-auto bg-white rounded-lg shadow p-6">
            <div class="text-center mb-6">
                <i class="fas fa-database text-blue-500 text-4xl mb-3"></i>
                <h2 class="text-xl font-bold">Crear Nueva Base de Datos</h2>
            </div>
            
            <form method="POST" class="space-y-4">
                <div>
                    <label for="db_name" class="block text-sm font-medium text-gray-700 mb-1">Nombre de la Base de Datos</label>
                    <input type="text" id="db_name" name="db_name" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Ej: loatech_backup">
                </div>
                
                <div class="flex justify-end space-x-3">
                    <a href="eliminardb.php" class="bg-gray-300 text-gray-800 px-4 py-2 rounded-md hover:bg-gray-400">
                        Cancelar
                    </a>
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                        Crear
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <?php include '../includes/footer.php'; ?>
</body>
</html>