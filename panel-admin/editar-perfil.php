<?php
session_start();

// Definir ruta base del proyecto
$root = $_SERVER['DOCUMENT_ROOT'] . '/LoauTech-main/';

// Incluir conexión con manejo de errores
try {
    require_once $root . 'conexion.php';
} catch (Throwable $e) {
    die("Error al conectar con la base de datos: Archivo de conexión no encontrado");
}

// Verificar disponibilidad de la base de datos
if (!isset($GLOBALS['db_not_available'])) {
    $GLOBALS['db_not_available'] = !DB_EXISTS;
}

if ($GLOBALS['db_not_available']) {
    echo '<div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-4">
            <i class="fas fa-exclamation-triangle mr-2"></i>
            La base de datos no está disponible actualmente. Por favor, importe un backup.
          </div>';
    echo '<a href="database/importar_db.php" class="bg-blue-600 text-white px-4 py-2 rounded-md inline-block mt-4">
            <i class="fas fa-file-import mr-2"></i> Importar Backup
          </a>';
    exit;
}

// Verificar autenticación del usuario
if (!isset($_SESSION['usuario'])) {
    header('Location: ../login.php');
    exit();
}

// Verificar rol de administrador
if ($_SESSION['usuario']['rol'] !== 'admin') {
    header('Location: ../login.php?error=acceso_no_autorizado');
    exit();
}

// Variables para mensajes
$mensaje = '';
$tipo_mensaje = '';

// Procesar formulario si se envió
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $telefono = trim($_POST['telefono'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $direccion = trim($_POST['direccion'] ?? '');
    $usuario_id = $_SESSION['usuario']['IDper'];
    
    // Validaciones básicas
    if (empty($telefono) || empty($email) || empty($direccion)) {
        $mensaje = 'Todos los campos son obligatorios.';
        $tipo_mensaje = 'error';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $mensaje = 'El formato del email no es válido.';
        $tipo_mensaje = 'error';
    } elseif (!preg_match('/^[0-9]{10,15}$/', $telefono)) {
        $mensaje = 'El teléfono debe contener solo números y tener entre 10 y 15 dígitos.';
        $tipo_mensaje = 'error';
    } else {
        // Verificar si ya existe un registro de contacto para este usuario
        $sql_check = "SELECT IDcont FROM contactos WHERE IDperso = ?";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->bind_param("i", $usuario_id);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();
        $contacto_existe = $result_check->fetch_assoc();
        $stmt_check->close();
        
        if ($contacto_existe) {
            // Actualizar registro existente
            $sql = "UPDATE contactos SET numerocont = ?, correocont = ?, direccioncont = ? WHERE IDperso = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssi", $telefono, $email, $direccion, $usuario_id);
        } else {
            // Crear nuevo registro
            $sql = "INSERT INTO contactos (numerocont, correocont, direccioncont, estadocont, IDperso) VALUES (?, ?, ?, 'activo', ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssi", $telefono, $email, $direccion, $usuario_id);
        }
        
        if ($stmt->execute()) {
            $mensaje = 'Información de contacto actualizada correctamente.';
            $tipo_mensaje = 'success';
        } else {
            $mensaje = 'Error al actualizar la información: ' . $conn->error;
            $tipo_mensaje = 'error';
        }
        $stmt->close();
    }
}

// Obtener información actual de contacto
$usuario_id = $_SESSION['usuario']['IDper'];
$sql = "SELECT numerocont, correocont, direccioncont 
        FROM contactos 
        WHERE IDperso = ? AND estadocont = 'activo'";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$resultado = $stmt->get_result();
$contacto = $resultado->fetch_assoc();
$stmt->close();

// Valores por defecto
$telefono_actual = $contacto['numerocont'] ?? '';
$email_actual = $contacto['correocont'] ?? '';
$direccion_actual = $contacto['direccioncont'] ?? '';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Perfil - LOAUTECH</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="flex h-screen">
        <!-- Sidebar - Usando fixed para posición fija -->
        <div class="w-64 bg-gray-800 text-white fixed top-0 left-0 bottom-0 z-10">
            <div class="p-4 border-b border-gray-700">
                <h1 class="text-xl font-bold">LOAUTECH</h1>
                <p class="text-sm text-gray-400">Panel de Administración</p>
            </div>
            
            <nav class="p-4">
                <ul class="space-y-2">
                    <li>
                        <a href="panel-principal.php" class="flex items-center p-2 rounded hover:bg-gray-700">
                            <i class="fas fa-tachometer-alt mr-3"></i>
                            Dashboard
                        </a>
                    </li>
                    <li>
                        <a href="personas/consultar.php" class="flex items-center p-2 rounded hover:bg-gray-700">
                            <i class="fas fa-users mr-3"></i>
                            Usuarios
                        </a>
                    </li>
                    <li>
                        <a href="elementos/consultar.php" class="flex items-center p-2 rounded hover:bg-gray-700">
                            <i class="fas fa-boxes mr-3"></i>
                            Elementos
                        </a>
                    </li>
                    <li>
                        <a href="almacenes/listar_autorizaciones.php" class="flex items-center p-2 rounded hover:bg-gray-700">
                            <i class="fas fa-warehouse mr-3"></i>
                            Almacenes
                        </a>
                    </li>
                    <li>
                        <a href="database/exportar_db.php" class="flex items-center p-2 rounded hover:bg-gray-700">
                            <i class="fas fa-database mr-3"></i>
                            Base de Datos
                        </a>
                    </li>
                    <li>
                        <a href="perfil.php" class="flex items-center p-2 rounded bg-gray-700">
                            <i class="fas fa-user mr-3"></i>
                            Mi Perfil
                        </a>
                    </li>
                    <li>
                        <a href="../logout.php" class="flex items-center p-2 rounded hover:bg-gray-700">
                            <i class="fas fa-sign-out-alt mr-3"></i>
                            Cerrar Sesión
                        </a>
                    </li>
                </ul>
            </nav>
        </div>

        <!-- Contenido principal con margen para el sidebar -->
        <div class="flex-1 ml-64 overflow-auto">
            <!-- Header -->
            <div class="bg-white shadow-sm">
                <div class="max-w-7xl mx-auto px-4 py-4 sm:px-6 lg:px-8 flex justify-between items-center">
                    <h1 class="text-xl font-bold text-gray-900">
                        <i class="fas fa-edit mr-2 text-blue-600"></i>
                        Editar Perfil
                    </h1>
                    <div class="flex items-center space-x-4">
                        <div class="flex items-center">
                            <a href="perfil.php" class="flex items-center hover:bg-gray-100 rounded-lg p-2 transition-colors duration-200">
                                <img src="https://ui-avatars.com/api/?name=<?= urlencode($_SESSION['usuario']['nombre'] ?? 'Admin') ?>&background=random" 
                                     alt="Usuario" class="h-8 w-8 rounded-full cursor-pointer">
                                <span class="ml-2 text-sm font-medium text-gray-700"><?= htmlspecialchars($_SESSION['usuario']['nombre'] ?? 'Admin') ?></span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contenido del formulario -->
            <main class="p-6">
                <div class="max-w-2xl mx-auto">
                    <!-- Mensajes de éxito o error -->
                    <?php if ($mensaje): ?>
                        <div class="mb-6 p-4 rounded-lg <?= $tipo_mensaje === 'success' ? 'bg-green-100 border border-green-400 text-green-700' : 'bg-red-100 border border-red-400 text-red-700' ?>">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas <?= $tipo_mensaje === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle' ?> mr-2"></i>
                                </div>
                                <div><?= htmlspecialchars($mensaje) ?></div>
                                <?php if ($tipo_mensaje === 'success'): ?>
                                    <div class="ml-auto">
                                        <a href="perfil.php" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 font-medium">
                                            <i class="fas fa-arrow-left mr-2"></i>
                                            Volver al perfil
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Formulario de edición -->
                    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                        <!-- Header del formulario -->
                        <div class="bg-gradient-to-r from-blue-600 to-blue-800 px-6 py-4">
                            <h2 class="text-xl font-bold text-white">
                                <i class="fas fa-edit mr-2"></i>
                                Editar Información de Contacto
                            </h2>
                            <p class="text-blue-100 text-sm mt-1">Actualiza tu teléfono, email y dirección</p>
                        </div>

                        <!-- Formulario -->
                        <form method="POST" class="px-6 py-6">
                            <div class="space-y-6">
                                <!-- Campo Teléfono -->
                                <div>
                                    <label for="telefono" class="block text-sm font-medium text-gray-700 mb-2">
                                        <i class="fas fa-phone mr-2 text-blue-600"></i>
                                        Teléfono
                                    </label>
                                    <input type="tel" 
                                           id="telefono" 
                                           name="telefono" 
                                           value="<?= htmlspecialchars($telefono_actual) ?>"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                                           placeholder="Ej: 3201234567"
                                           pattern="[0-9]{10,15}"
                                           required>
                                    <p class="text-xs text-gray-500 mt-1">Solo números, entre 10 y 15 dígitos</p>
                                </div>

                                <!-- Campo Email -->
                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                        <i class="fas fa-envelope mr-2 text-blue-600"></i>
                                        Correo Electrónico
                                    </label>
                                    <input type="email" 
                                           id="email" 
                                           name="email" 
                                           value="<?= htmlspecialchars($email_actual) ?>"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                                           placeholder="Ej: usuario@ejemplo.com"
                                           required>
                                </div>

                                <!-- Campo Dirección -->
                                <div>
                                    <label for="direccion" class="block text-sm font-medium text-gray-700 mb-2">
                                        <i class="fas fa-map-marker-alt mr-2 text-blue-600"></i>
                                        Dirección
                                    </label>
                                    <textarea id="direccion" 
                                              name="direccion" 
                                              rows="3"
                                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                                              placeholder="Ej: Calle 123 # 45-67, Barrio, Ciudad"
                                              required><?= htmlspecialchars($direccion_actual) ?></textarea>
                                </div>
                            </div>

                            <!-- Botones de acción -->
                            <div class="mt-8 flex flex-wrap gap-4">
                                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition-colors duration-200 font-medium">
                                    <i class="fas fa-save mr-2"></i>
                                    Guardar Cambios
                                </button>
                                <a href="perfil.php" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-lg transition-colors duration-200 inline-block font-medium">
                                    <i class="fas fa-times mr-2"></i>
                                    Cancelar
                                </a>
                            </div>
                        </form>
                    </div>

                    <!-- Información adicional -->
                    <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-info-circle text-blue-600"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-blue-800">Información importante</h3>
                                <div class="mt-2 text-sm text-blue-700">
                                    <ul class="list-disc pl-5 space-y-1">
                                        <li>Esta información será visible en tu perfil</li>
                                        <li>Asegúrate de que los datos sean correctos</li>
                                        <li>El teléfono debe ser un número válido</li>
                                        <li>El email debe tener un formato válido</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>

            <!-- Footer -->
            <footer class="bg-white border-t mt-8">
                <div class="max-w-7xl mx-auto px-4 py-4 sm:px-6 lg:px-8">
                    <div class="flex flex-col md:flex-row justify-between items-center">
                        <p class="text-sm text-gray-500">
                            &copy; <?= date('Y') ?> LOAUTECH. Todos los derechos reservados.
                        </p>
                        <div class="flex space-x-6 mt-4 md:mt-0">
                            <a href="#" class="text-gray-400 hover:text-gray-500">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <a href="#" class="text-gray-400 hover:text-gray-500">
                                <i class="fab fa-twitter"></i>
                            </a>
                            <a href="#" class="text-gray-400 hover:text-gray-500">
                                <i class="fab fa-instagram"></i>
                            </a>
                            <a href="#" class="text-gray-400 hover:text-gray-500">
                                <i class="fab fa-linkedin-in"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
    <script>
        // Validación en tiempo real del teléfono
        document.getElementById('telefono').addEventListener('input', function(e) {
            const value = e.target.value;
            e.target.value = value.replace(/[^0-9]/g, '');
        });

        // Confirmación antes de enviar el formulario
        document.querySelector('form').addEventListener('submit', function(e) {
            if (!confirm('¿Estás seguro de que quieres actualizar tu información de contacto?')) {
                e.preventDefault();
            }
        });
    </script>
</body>
</html>
