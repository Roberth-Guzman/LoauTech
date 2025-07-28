<?php
session_start();

// Verificar sesión y rol de usuario
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'usuario') {
    header('Location: ../login.php');
    exit();
}

require_once '../conexion.php';

// Debug: Verificar contenido de la sesión
error_log("Contenido de la sesión: " . print_r($_SESSION, true));

$error = '';
$success = '';
$usuario_id = $_SESSION['usuario']['IDper'] ?? $_SESSION['usuario']['idper'] ?? null;
$usuario_documento = $_SESSION['usuario']['documento'] ?? 'N/A';

// Verificar que el ID del usuario está disponible
if (!$usuario_id) {
    $error = "Error: No se pudo obtener el ID del usuario. Por favor, cierre sesión y vuelva a iniciar.";
}

// Procesar el formulario de registro de elementos
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $usuario_id) {
    // Sanitizar y validar datos
    $nombre = $conn->real_escape_string(trim($_POST['nombreingele']));
    $tipo = $conn->real_escape_string(trim($_POST['tipoelemento']));
    $descripcion = $conn->real_escape_string(trim($_POST['descripcioningele']));
    $observacion = $conn->real_escape_string(trim($_POST['observacioningele']));
    $idper = $usuario_id;

    // Validaciones
    if (empty($nombre) || strlen($nombre) > 250) {
        $error = "El nombre es requerido y debe tener máximo 250 caracteres";
    } elseif (empty($tipo) || strlen($tipo) > 200) {
        $error = "El tipo de elemento es requerido y debe tener máximo 200 caracteres";
    } else {
        try {
            $conn->begin_transaction();

            // Insertar el elemento en la tabla ingresoelementos
            $stmt = $conn->prepare("INSERT INTO ingresoelementos 
                                  (nombreingele, tipoelemento, descripcioningele, observacioningele, IDPER) 
                                  VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssi", $nombre, $tipo, $descripcion, $observacion, $idper);
            $stmt->execute();
            $idElemento = $conn->insert_id;
            $stmt->close();

            $conn->commit();
            $success = "Elemento registrado exitosamente";
            
            // Limpiar campos después de registro exitoso
            $_POST = array();
            
        } catch (Exception $e) {
            $conn->rollback();
            $error = "Error al registrar el elemento: " . $e->getMessage();
            error_log("Error en registroelemento.php: " . $e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Elementos - LOAUTECH</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div class="w-64 bg-gray-800 text-white fixed top-0 left-0 bottom-0 z-10">
            <div class="p-4 border-b border-gray-700">
                <h1 class="text-xl font-bold">LOAUTECH</h1>
                <p class="text-sm text-gray-400">Panel de Usuario</p>
            </div>
            
            <nav class="p-4">
                <ul class="space-y-2">
                    <li>
                        <a href="panel-principal.php" class="flex items-center p-2 rounded hover:bg-gray-700">
                            <i class="fas fa-tachometer-alt mr-3"></i>
                            Inicio
                        </a>
                    </li>
                    <li>
                        <a href="inventario.php" class="flex items-center p-2 rounded hover:bg-gray-700">
                            <i class="fas fa-boxes mr-3"></i>
                            Inventario
                        </a>
                    </li>
                    <li>
                        <a href="registroelemento.php" class="flex items-center p-2 rounded hover:bg-gray-700 bg-gray-700">
                            <i class="fas fa-plus-circle mr-3"></i>
                            Registrar Elemento
                        </a>
                    </li>
                    <li>
                        <a href="peticion.php" class="flex items-center p-2 rounded hover:bg-gray-700">
                            <i class="fas fa-paper-plane mr-3"></i>
                            Mis Peticiones
                        </a>
                    </li>
                    <li>
                        <a href="perfil.php" class="flex items-center p-2 rounded hover:bg-gray-700">
                            <i class="fas fa-user mr-3"></i>
                            Mi Perfil
                        </a>
                    </li>
                    <li>
                        <a href="../logout.php" class="flex items-center p-2 rounded hover:bg-gray-700 text-red-400 hover:text-red-300">
                            <i class="fas fa-sign-out-alt mr-3"></i>
                            Cerrar Sesión
                        </a>
                    </li>
                </ul>
            </nav>
        </div>

        <!-- Contenido principal -->
        <div class="flex-1 ml-64 overflow-auto">
            <!-- Header -->
            <div class="bg-white shadow-sm">
                <div class="max-w-7xl mx-auto px-4 py-4 sm:px-6 lg:px-8 flex justify-between items-center">
                    <div class="flex items-center">
                        <h1 class="text-xl font-bold text-gray-900">
                            Registro de Elementos
                        </h1>
                    </div>
                    <div class="flex items-center space-x-4">
                        <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                            <?php echo ucfirst(htmlspecialchars($_SESSION['usuario']['rol'] ?? 'usuario')); ?>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Contenido -->
            <main class="p-6">
                <!-- Mensajes de éxito/error -->
                <?php if ($error): ?>
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded" role="alert">
                        <div class="flex">
                            <div class="py-1">
                                <i class="fas fa-exclamation-triangle text-red-500 mr-2"></i>
                            </div>
                            <div>
                                <p class="font-bold">Error</p>
                                <p class="text-sm"><?php echo htmlspecialchars($error); ?></p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded" role="alert">
                        <div class="flex">
                            <div class="py-1">
                                <i class="fas fa-check-circle text-green-500 mr-2"></i>
                            </div>
                            <div>
                                <p class="font-bold">¡Éxito!</p>
                                <p class="text-sm"><?php echo htmlspecialchars($success); ?></p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Información del usuario -->
                <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-3">Información del Usuario</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Nombre</p>
                            <p class="mt-1 text-sm text-gray-900"><?= htmlspecialchars($_SESSION['usuario']['nombre'] ?? '') ?></p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Documento</p>
                            <p class="mt-1 text-sm text-gray-900"><?= htmlspecialchars($usuario_documento) ?></p>
                        </div>
                    </div>
                </div>

                <!-- Formulario de registro -->
                <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Datos del Elemento</h3>
                        <p class="mt-1 text-sm text-gray-500">Complete todos los campos obligatorios (*) para registrar un nuevo elemento.</p>
                    </div>
                    
                    <form method="POST" class="p-6">
                        <div class="space-y-6">
                            <!-- Nombre del elemento -->
                            <div>
                                <label for="nombreingele" class="block text-sm font-medium text-gray-700">
                                    Nombre del Elemento <span class="text-red-500">*</span>
                                </label>
                                <div class="mt-1">
                                    <input type="text" id="nombreingele" name="nombreingele" required
                                           value="<?= htmlspecialchars($_POST['nombreingele'] ?? '') ?>"
                                           class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                </div>
                                <p class="mt-1 text-sm text-gray-500">Máximo 250 caracteres</p>
                            </div>

                            <!-- Tipo de elemento -->
                            <div>
                                <label for="tipoelemento" class="block text-sm font-medium text-gray-700">
                                    Tipo de Elemento <span class="text-red-500">*</span>
                                </label>
                                <div class="mt-1">
                                    <input type="text" id="tipoelemento" name="tipoelemento" required
                                           value="<?= htmlspecialchars($_POST['tipoelemento'] ?? '') ?>"
                                           class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                </div>
                                <p class="mt-1 text-sm text-gray-500">Ej: Computador, Teléfono, Herramienta, etc.</p>
                            </div>

                            <!-- Descripción -->
                            <div>
                                <label for="descripcioningele" class="block text-sm font-medium text-gray-700">
                                    Descripción
                                </label>
                                <div class="mt-1">
                                    <textarea id="descripcioningele" name="descripcioningele" rows="3"
                                              class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border border-gray-300 rounded-md"><?= htmlspecialchars($_POST['descripcioningele'] ?? '') ?></textarea>
                                </div>
                                <p class="mt-1 text-sm text-gray-500">Describa las características principales del elemento.</p>
                            </div>

                            <!-- Observaciones -->
                            <div>
                                <label for="observacioningele" class="block text-sm font-medium text-gray-700">
                                    Observaciones
                                </label>
                                <div class="mt-1">
                                    <textarea id="observacioningele" name="observacioningele" rows="2"
                                              class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border border-gray-300 rounded-md"><?= htmlspecialchars($_POST['observacioningele'] ?? '') ?></textarea>
                                </div>
                                <p class="mt-1 text-sm text-gray-500">Cualquier información adicional que desee registrar.</p>
                            </div>

                            <!-- Botones de acción -->
                            <div class="pt-5">
                                <div class="flex justify-end space-x-3">
                                    <a href="panel-principal.php" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        <i class="fas fa-arrow-left mr-2"></i> Cancelar
                                    </a>
                                    <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        <i class="fas fa-save mr-2"></i> Guardar Elemento
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </main>

            <!-- Footer -->
            <footer class="bg-white border-t mt-8">
                <div class="max-w-7xl mx-auto px-4 py-4 sm:px-6 lg:px-8">
                    <p class="text-center text-sm text-gray-500">
                        &copy; <?php echo date('Y'); ?> LOAUTECH - Todos los derechos reservados
                    </p>
                </div>
            </footer>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
</body>
</html>