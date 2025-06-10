<?php
session_start();
include '../../conexion.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['rol'] != 'admin') {
    header("Location: ../../login.php");
    exit;
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Obtener datos del usuario
$usuario = [];
$contacto = [];
$rol = '';

if ($id > 0) {
    $query = "SELECT p.*, c.*, r.rol 
              FROM personas p
              LEFT JOIN contactos c ON p.IDper = c.IDperso
              LEFT JOIN roles r ON p.IDper = r.idper
              WHERE p.IDper = $id";
    $result = $conn->query($query);
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $usuario = [
            'IDper' => $row['IDper'],
            'nombrecompletoper' => $row['nombrecompletoper'],
            'tipodocumento' => $row['tipodocumento'],
            'numerodoc' => $row['numerodoc']
        ];
        $contacto = [
            'numerocont' => $row['numerocont'],
            'direccioncont' => $row['direccioncont'],
            'correocont' => $row['correocont']
        ];
        $rol = $row['rol'];
    } else {
        header("Location: consultar.php?error=Usuario no encontrado");
        exit;
    }
}

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $conn->real_escape_string($_POST['nombre']);
    $tipoDoc = $conn->real_escape_string($_POST['tipo_documento']);
    $numeroDoc = $conn->real_escape_string($_POST['numero_documento']);
    $telefono = $conn->real_escape_string($_POST['telefono']);
    $direccion = $conn->real_escape_string($_POST['direccion']);
    $correo = $conn->real_escape_string($_POST['correo']);
    $rol = $conn->real_escape_string($_POST['rol']);
    
    $conn->begin_transaction();
    
    try {
        if ($id > 0) {
            // Actualizar usuario existente
            $conn->query("UPDATE personas SET 
                          nombrecompletoper = '$nombre',
                          tipodocumento = '$tipoDoc',
                          numerodoc = '$numeroDoc'
                          WHERE IDper = $id");
            
            $conn->query("UPDATE contactos SET
                          numerocont = '$telefono',
                          direccioncont = '$direccion',
                          correocont = '$correo'
                          WHERE IDperso = $id");
            
            $conn->query("UPDATE roles SET
                          rol = '$rol'
                          WHERE idper = $id");
        } else {
            // Crear nuevo usuario
            $conn->query("INSERT INTO personas (nombrecompletoper, tipodocumento, numerodoc) 
                          VALUES ('$nombre', '$tipoDoc', '$numeroDoc')");
            $id = $conn->insert_id;
            
            $conn->query("INSERT INTO contactos (numerocont, direccioncont, correocont, estadocont, IDperso) 
                          VALUES ('$telefono', '$direccion', '$correo', 'activo', $id)");
            
            $conn->query("INSERT INTO roles (rol, estadorol, idper) 
                          VALUES ('$rol', 'activo', $id)");
            
            // Crear cuenta (contraseña por defecto)
            $password = password_hash($numeroDoc, PASSWORD_DEFAULT);
            $conn->query("INSERT INTO cuentas (numerodoc, contracue, estadocue) 
                          VALUES ('$numeroDoc', '$password', 'activo')");
        }
        
        $conn->commit();
        header("Location: consultar.php?success=Usuario guardado correctamente");
        exit;
    } catch (Exception $e) {
        $conn->rollback();
        $error = "Error al guardar: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $id > 0 ? 'Editar' : 'Nuevo' ?> Usuario</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <?php include '../includes/header.php'; ?>
    
    <div class="ml-64 p-6">
        <div class="max-w-4xl mx-auto bg-white rounded-lg shadow p-6">
            <h2 class="text-2xl font-bold mb-6 text-gray-800">
                <i class="fas fa-user-edit mr-2"></i>
                <?= $id > 0 ? 'Editar Usuario' : 'Nuevo Usuario' ?>
            </h2>
            
            <?php if (isset($error)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="nombre" class="block text-sm font-medium text-gray-700 mb-1">Nombre Completo</label>
                        <input type="text" id="nombre" name="nombre" required
                               value="<?= htmlspecialchars($usuario['nombrecompletoper'] ?? '') ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    
                    <div>
                        <label for="tipo_documento" class="block text-sm font-medium text-gray-700 mb-1">Tipo Documento</label>
                        <select id="tipo_documento" name="tipo_documento" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <option value="TI" <?= ($usuario['tipodocumento'] ?? '') == 'TI' ? 'selected' : '' ?>>Tarjeta de Identidad</option>
                            <option value="CC" <?= ($usuario['tipodocumento'] ?? '') == 'CC' ? 'selected' : '' ?>>Cédula de Ciudadanía</option>
                        </select>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="numero_documento" class="block text-sm font-medium text-gray-700 mb-1">Número Documento</label>
                        <input type="text" id="numero_documento" name="numero_documento" required
                               value="<?= htmlspecialchars($usuario['numerodoc'] ?? '') ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    
                    <div>
                        <label for="telefono" class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                        <input type="text" id="telefono" name="telefono" required
                               value="<?= htmlspecialchars($contacto['numerocont'] ?? '') ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
                
                <div>
                    <label for="direccion" class="block text-sm font-medium text-gray-700 mb-1">Dirección</label>
                    <input type="text" id="direccion" name="direccion" required
                           value="<?= htmlspecialchars($contacto['direccioncont'] ?? '') ?>"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="correo" class="block text-sm font-medium text-gray-700 mb-1">Correo Electrónico</label>
                        <input type="email" id="correo" name="correo" required
                               value="<?= htmlspecialchars($contacto['correocont'] ?? '') ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    
                    <div>
                        <label for="rol" class="block text-sm font-medium text-gray-700 mb-1">Rol</label>
                        <select id="rol" name="rol" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <option value="usuario" <?= ($rol ?? '') == 'usuario' ? 'selected' : '' ?>>Usuario</option>
                            <option value="porteria" <?= ($rol ?? '') == 'porteria' ? 'selected' : '' ?>>Portería</option>
                            <option value="admin" <?= ($rol ?? '') == 'admin' ? 'selected' : '' ?>>Administrador</option>
                        </select>
                    </div>
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