<?php
session_start();
include '../../conexion.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['rol'] != 'admin') {
    header("Location: ../../login.php");
    exit;
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Obtener datos del elemento
$elemento = [
    'nombreele' => '',
    'cantidadele' => 1,
    'cantidadest' => 'activo',
    'codigoele' => '',
    'codigoinventario' => '',
    'descripcionele' => '',
    'caracteristicasele' => '',
    'estado' => 'activo',
    'estadoelemento' => 'activo'
];

if ($id > 0) {
    $query = "SELECT * FROM elementos WHERE IDele = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $elemento = $result->fetch_assoc();
    } else {
        header("Location: consultar.php?error=Elemento no encontrado");
        exit;
    }
}

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombreele = $conn->real_escape_string($_POST['nombreele']);
    $cantidadele = intval($_POST['cantidadele']);
    $cantidadest = $_POST['cantidadest'] === 'activo' ? 'activo' : 'inactivo';
    $codigoele = $conn->real_escape_string($_POST['codigoele']);
    $codigoinventario = $conn->real_escape_string($_POST['codigoinventario']);
    $descripcionele = $conn->real_escape_string($_POST['descripcionele']);
    $caracteristicasele = $conn->real_escape_string($_POST['caracteristicasele']);
    $estado = in_array($_POST['estado'], ['activo', 'inactivo', 'en prestamo']) ? $_POST['estado'] : 'activo';
    $estadoelemento = $_POST['estadoelemento'] === 'activo' ? 'activo' : 'inactivo';
    
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
                  estadoelemento = ?
                  WHERE IDele = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sississssi", 
            $nombreele, $cantidadele, $cantidadest, $codigoele, $codigoinventario,
            $descripcionele, $caracteristicasele, $estado, $estadoelemento, $id
        );
    } else {
        // Crear nuevo elemento
        $query = "INSERT INTO elementos 
                 (nombreele, cantidadele, cantidadest, codigoele, codigoinventario, descripcionele, 
                  caracteristicasele, estado, estadoelemento) 
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sississss", 
            $nombreele, $cantidadele, $cantidadest, $codigoele, $codigoinventario,
            $descripcionele, $caracteristicasele, $estado, $estadoelemento
        );
    }
    
    if ($stmt->execute()) {
        header("Location: consultar.php?success=Elemento " . ($id > 0 ? 'actualizado' : 'creado') . " correctamente");
    } else {
        $error = "Error al " . ($id > 0 ? 'actualizar' : 'crear') . " el elemento: " . $conn->error;
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $id > 0 ? 'Editar' : 'Nuevo' ?> Elemento</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <?php include '../includes/header.php'; ?>
    
    <div class="ml-64 p-6">
        <div class="max-w-4xl mx-auto bg-white rounded-lg shadow p-6">
            <h2 class="text-2xl font-bold mb-6 text-gray-800">
                <i class="fas fa-box-open mr-2"></i>
                <?= $id > 0 ? 'Editar Elemento' : 'Nuevo Elemento' ?>
            </h2>
            
            <?php if (isset($error)): ?>
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                    <p><?= htmlspecialchars($error) ?></p>
                </div>
            <?php endif; ?>
            
            <form method="POST" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="nombreele" class="block text-sm font-medium text-gray-700 mb-1">Nombre del Elemento *</label>
                        <input type="text" id="nombreele" name="nombreele" required
                               value="<?= htmlspecialchars($elemento['nombreele']) ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    
                    <div>
                        <label for="codigoele" class="block text-sm font-medium text-gray-700 mb-1">Código *</label>
                        <input type="text" id="codigoele" name="codigoele" required
                               value="<?= htmlspecialchars($elemento['codigoele']) ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div>
                        <label for="codigoinventario" class="block text-sm font-medium text-gray-700 mb-1">Código de Inventario *</label>
                        <input type="text" id="codigoinventario" name="codigoinventario" required
                               value="<?= htmlspecialchars($elemento['codigoinventario']) ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Ingrese el código de inventario">
                    </div>
                    
                    <div>
                        <label for="cantidadele" class="block text-sm font-medium text-gray-700 mb-1">Cantidad *</label>
                        <input type="number" id="cantidadele" name="cantidadele" min="0" required
                               value="<?= htmlspecialchars($elemento['cantidadele']) ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    
                    <div>
                        <label for="cantidadest" class="block text-sm font-medium text-gray-700 mb-1">Estado de Cantidad *</label>
                        <select id="cantidadest" name="cantidadest" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <option value="activo" <?= $elemento['cantidadest'] === 'activo' ? 'selected' : '' ?>>Activo</option>
                            <option value="inactivo" <?= $elemento['cantidadest'] === 'inactivo' ? 'selected' : '' ?>>Inactivo</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="estado" class="block text-sm font-medium text-gray-700 mb-1">Estado *</label>
                        <select id="estado" name="estado" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <option value="activo" <?= $elemento['estado'] === 'activo' ? 'selected' : '' ?>>Activo</option>
                            <option value="en prestamo" <?= $elemento['estado'] === 'en prestamo' ? 'selected' : '' ?>>En Préstamo</option>
                            <option value="inactivo" <?= $elemento['estado'] === 'inactivo' ? 'selected' : '' ?>>Inactivo</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="estadoelemento" class="block text-sm font-medium text-gray-700 mb-1">Estado del Elemento *</label>
                        <select id="estadoelemento" name="estadoelemento" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <option value="activo" <?= $elemento['estadoelemento'] === 'activo' ? 'selected' : '' ?>>Activo</option>
                            <option value="inactivo" <?= $elemento['estadoelemento'] === 'inactivo' ? 'selected' : '' ?>>Inactivo</option>
                        </select>
                    </div>
                    
                    <div class="md:col-span-2">
                        <label for="descripcionele" class="block text-sm font-medium text-gray-700 mb-1">Descripción *</label>
                        <textarea id="descripcionele" name="descripcionele" rows="2" required
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"><?= htmlspecialchars($elemento['descripcionele']) ?></textarea>
                    </div>
                    
                    <div class="md:col-span-2">
                        <label for="caracteristicasele" class="block text-sm font-medium text-gray-700 mb-1">Características</label>
                        <textarea id="caracteristicasele" name="caracteristicasele" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"><?= htmlspecialchars($elemento['caracteristicasele']) ?></textarea>
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200 mt-6">
                    <a href="consultar.php" class="bg-gray-300 text-gray-800 px-4 py-2 rounded-md hover:bg-gray-400">
                        <i class="fas fa-times mr-2"></i> Cancelar
                    </a>
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                        <i class="fas fa-save mr-2"></i> Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <?php include '../includes/footer.php'; ?>
</body>
</html>