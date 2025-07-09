<?php
session_start();
include '../../conexion.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['rol'] != 'admin') {
    header("Location: ../../login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $observacion = isset($_POST['observacion']) ? trim($_POST['observacion']) : '';
    
    if (empty($observacion)) {
        $error = "Debe ingresar una observación para esta acción";
    } else {
        // Iniciar transacción para eliminar todo
        $conn->begin_transaction();
        
        try {
            // 1. Eliminar contactos
            $conn->query("TRUNCATE TABLE contactos");
            
            // 2. Eliminar roles
            $conn->query("TRUNCATE TABLE roles");
            
            // 3. Eliminar cuentas
            $conn->query("TRUNCATE TABLE cuentas");
            
            // 4. Eliminar personas
            $conn->query("TRUNCATE TABLE personas");
            
            $conn->commit();
            header("Location: ../personas/consultar.php?success=Todos los usuarios han sido eliminados");
            exit;
        } catch (Exception $e) {
            $conn->rollback();
            $error = "Error al limpiar la base de datos: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Limpiar Base de Datos</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <?php include '../includes/header.php'; ?>
    
    <div class="ml-64 p-6">
        <div class="max-w-2xl mx-auto bg-white rounded-lg shadow p-6">
            <div class="text-center mb-6">
                <i class="fas fa-exclamation-triangle text-red-500 text-5xl mb-4"></i>
                <h2 class="text-2xl font-bold text-red-600">Limpiar Base de Datos de Usuarios</h2>
                <p class="text-gray-600 mt-2">Esta acción eliminará TODOS los usuarios y sus datos relacionados. Esta acción no se puede deshacer.</p>
            </div>
            
            <?php if (isset($error)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" class="space-y-4">
                <div>
                    <label for="observacion" class="block text-sm font-medium text-gray-700 mb-1">Motivo de la limpieza</label>
                    <textarea id="observacion" name="observacion" rows="4" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Explique el motivo de esta acción"></textarea>
                </div>
                
                <div class="flex justify-end space-x-3 pt-4">
                    <a href="../personas/consultar.php" class="bg-gray-300 text-gray-800 px-4 py-2 rounded-md hover:bg-gray-400">
                        Cancelar
                    </a>
                    <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700">
                        <i class="fas fa-trash-alt mr-2"></i> Confirmar Limpieza
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <?php include '../includes/footer.php'; ?>
</body>
</html>