<?php
session_start();
include '../../conexion.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['rol'] != 'admin') {
    header("Location: ../../login.php");
    exit;
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Obtener datos del elemento
$elemento = [];

if ($id > 0) {
    $query = "SELECT * FROM ingresoelementos WHERE IDingele = $id";
    $result = $conn->query($query);
    
    if ($result->num_rows > 0) {
        $elemento = $result->fetch_assoc();
    } else {
        header("Location: consultar.php?error=Elemento no encontrado");
        exit;
    }
}

// Obtener personas para el select
$personas = [];
$result_personas = $conn->query("SELECT IDper, nombrecompletoper FROM personas");
while ($row = $result_personas->fetch_assoc()) {
    $personas[] = $row;
}

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $conn->real_escape_string($_POST['nombre']);
    $tipo = $conn->real_escape_string($_POST['tipo']);
    $descripcion = $conn->real_escape_string($_POST['descripcion']);
    $observacion = $conn->real_escape_string($_POST['observacion']);
    $persona_id = intval($_POST['persona_id']);
    
    if ($id > 0) {
        // Actualizar elemento existente
        $conn->query("UPDATE ingresoelementos SET 
                      nombreingele = '$nombre',
                      tipoelemento = '$tipo',
                      descripcioningele = '$descripcion',
                      observacioningele = '$observacion',
                      IDPER = $persona_id
                      WHERE IDingele = $id");
    } else {
        // Crear nuevo elemento
        $conn->query("INSERT INTO ingresoelementos 
                      (nombreingele, tipoelemento, descripcioningele, observacioningele, IDPER) 
                      VALUES ('$nombre', '$tipo', '$descripcion', '$observacion', $persona_id)");
    }
    
    header("Location: consultar.php?success=Elemento guardado correctamente");
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
            
            <form method="POST" class="space-y-4">
                <div>
                    <label for="nombre" class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
                    <input type="text" id="nombre" name="nombre" required
                           value="<?= htmlspecialchars($elemento['nombreingele'] ?? '') ?>"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                </div>
                
                <div>
                    <label for="tipo" class="block text-sm font-medium text-gray-700 mb-1">Tipo</label>
                    <input type="text" id="tipo" name="tipo" required
                           value="<?= htmlspecialchars($elemento['tipoelemento'] ?? '') ?>"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                </div>
                
                <div>
                    <label for="descripcion" class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                    <textarea id="descripcion" name="descripcion" rows="3" required
                              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"><?= htmlspecialchars($elemento['descripcioningele'] ?? '') ?></textarea>
                </div>
                
                <div>
                    <label for="observacion" class="block text-sm font-medium text-gray-700 mb-1">Observación</label>
                    <textarea id="observacion" name="observacion" rows="2"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"><?= htmlspecialchars($elemento['observacioningele'] ?? '') ?></textarea>
                </div>
                
                <div>
                    <label for="persona_id" class="block text-sm font-medium text-gray-700 mb-1">Responsable</label>
                    <select id="persona_id" name="persona_id" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <?php foreach ($personas as $persona): ?>
                        <option value="<?= $persona['IDper'] ?>" <?= ($elemento['IDPER'] ?? 0) == $persona['IDper'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($persona['nombrecompletoper']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="flex justify-end space-x-3 pt-4">
                    <a href="consultar.php" class="bg-gray-300 text-gray-800 px-4 py-2 rounded-md hover:bg-gray-400">
                        Cancelar
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