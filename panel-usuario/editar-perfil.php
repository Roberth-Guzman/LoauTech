<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario'])) {
    header("Location: ../login.php");
    exit;
}

include '../conexion.php';

$idUsuario = $_SESSION['usuario']['IDper'];
$mensaje = '';
$error = '';

// Obtener datos actuales del usuario
$sql = "SELECT 
            p.nombrecompletoper, 
            p.tipodocumento, 
            p.numerodoc,
            c.correocont, 
            c.numerocont,
            r.rol,
            fp.ruta AS foto_ruta
        FROM personas p
        LEFT JOIN contactos c ON p.IDper = c.IDperso
        LEFT JOIN roles r ON p.IDper = r.idper
        LEFT JOIN fotos_perfil fp ON p.IDper = fp.id_persona AND fp.es_actual = 1
        WHERE p.IDper = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idUsuario);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();

if (!$usuario) {
    $_SESSION['error'] = "No se encontró la información del usuario.";
    header("Location: perfil.php");
    exit;
}

// Procesar el formulario cuando se envía
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validar y limpiar los datos del formulario
    $nombre = trim($_POST['nombre'] ?? '');
    $correo = filter_var(trim($_POST['correo'] ?? ''), FILTER_SANITIZE_EMAIL);
    $telefono = trim($_POST['telefono'] ?? '');
    $contrasenaActual = $_POST['contrasena_actual'] ?? '';
    $nuevaContrasena = $_POST['nueva_contrasena'] ?? '';
    $confirmarContrasena = $_POST['confirmar_contrasena'] ?? '';
    
    // Validaciones básicas
    if (empty($nombre)) {
        $error = "El nombre completo es obligatorio.";
    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $error = "Por favor, ingresa un correo electrónico válido.";
    } elseif (!empty($nuevaContrasena) && strlen($nuevaContrasena) < 8) {
        $error = "La nueva contraseña debe tener al menos 8 caracteres.";
    } elseif (!empty($nuevaContrasena) && $nuevaContrasena !== $confirmarContrasena) {
        $error = "Las contraseñas no coinciden.";
    } else {
        // Iniciar transacción
        $conn->begin_transaction();
        
        try {
            // Actualizar datos de la tabla personas
            $sqlPersona = "UPDATE personas SET nombrecompletoper = ? WHERE IDper = ?";
            $stmt = $conn->prepare($sqlPersona);
            $stmt->bind_param("si", $nombre, $idUsuario);
            $stmt->execute();
            
            // Verificar si el contacto ya existe
            $sqlCheckContacto = "SELECT COUNT(*) as total FROM contactos WHERE IDperso = ?";
            $stmt = $conn->prepare($sqlCheckContacto);
            $stmt->bind_param("i", $idUsuario);
            $stmt->execute();
            $result = $stmt->get_result();
            $existeContacto = $result->fetch_assoc()['total'] > 0;
            
            // Actualizar o insertar en la tabla contactos
            if ($existeContacto) {
                $sqlContacto = "UPDATE contactos SET correocont = ?, numerocont = ? WHERE IDperso = ?";
            } else {
                $sqlContacto = "INSERT INTO contactos (correocont, numerocont, IDperso) VALUES (?, ?, ?)";
            }
            
            $stmt = $conn->prepare($sqlContacto);
            $stmt->bind_param("ssi", $correo, $telefono, $idUsuario);
            $stmt->execute();
            
            // Actualizar contraseña si se proporcionó una nueva
            if (!empty($nuevaContrasena) && !empty($contrasenaActual)) {
                // Verificar la contraseña actual
                $sqlPass = "SELECT contrasena FROM usuarios WHERE IDper = ?";
                $stmt = $conn->prepare($sqlPass);
                $stmt->bind_param("i", $idUsuario);
                $stmt->execute();
                $result = $stmt->get_result();
                $usuarioPass = $result->fetch_assoc();
                
                if ($usuarioPass && password_verify($contrasenaActual, $usuarioPass['contrasena'])) {
                    // Actualizar la contraseña
                    $hashedPassword = password_hash($nuevaContrasena, PASSWORD_DEFAULT);
                    $sqlUpdatePass = "UPDATE usuarios SET contrasena = ? WHERE IDper = ?";
                    $stmt = $conn->prepare($sqlUpdatePass);
                    $stmt->bind_param("si", $hashedPassword, $idUsuario);
                    $stmt->execute();
                } else {
                    throw new Exception("La contraseña actual no es correcta.");
                }
            }
            
            // Confirmar la transacción
            $conn->commit();
            
            // Actualizar los datos en la sesión
            $_SESSION['usuario']['nombre'] = $nombre;
            
            // Redirigir con mensaje de éxito
            $_SESSION['mensaje'] = "Perfil actualizado correctamente.";
            header("Location: perfil.php");
            exit;
            
        } catch (Exception $e) {
            // Revertir la transacción en caso de error
            $conn->rollback();
            $error = "Error al actualizar el perfil: " . $e->getMessage();
        }
    }
}
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
                        <a href="registroelemento.php" class="flex items-center p-2 rounded hover:bg-gray-700">
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
                        <a href="perfil.php" class="flex items-center p-2 rounded bg-gray-700">
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
                        <a href="perfil.php" class="mr-4 text-gray-500 hover:text-gray-700">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                        <h1 class="text-xl font-bold text-gray-900">
                            Editar Perfil
                        </h1>
                    </div>
                    <div class="flex items-center space-x-4">
                        <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                            <?= ucfirst(htmlspecialchars($usuario['rol'] ?? 'usuario')) ?>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Contenido -->
            <main class="p-6">
                <div class="max-w-4xl mx-auto">
                    <?php if (!empty($error)): ?>
                        <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                            <?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Tarjeta de perfil -->
                    <div class="bg-white rounded-lg shadow-sm overflow-hidden mb-6">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Información del Perfil</h3>
                            <p class="mt-1 text-sm text-gray-500">Actualiza tu información personal y datos de contacto.</p>
                        </div>
                        
                        <form method="POST" class="px-6 py-4">
                            <div class="space-y-6">
                                <!-- Información básica -->
                                <div>
                                    <h4 class="text-md font-medium text-gray-900 mb-4">Información Personal</h4>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div>
                                            <label for="nombre" class="block text-sm font-medium text-gray-700 mb-1">Nombre Completo</label>
                                            <input type="text" id="nombre" name="nombre" required
                                                   value="<?= htmlspecialchars($usuario['nombrecompletoper']) ?>"
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Documento</label>
                                            <div class="mt-1 text-sm text-gray-900 bg-gray-100 p-2 rounded">
                                                <?= htmlspecialchars($usuario['tipodocumento']) ?>
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Número de Documento</label>
                                            <div class="mt-1 text-sm text-gray-900 bg-gray-100 p-2 rounded">
                                                <?= htmlspecialchars($usuario['numerodoc']) ?>
                                            </div>
                                        </div>
                                        <div>
                                            <label for="correo" class="block text-sm font-medium text-gray-700 mb-1">Correo Electrónico</label>
                                            <input type="email" id="correo" name="correo" required
                                                   value="<?= htmlspecialchars($usuario['correocont'] ?? '') ?>"
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                        </div>
                                        <div>
                                            <label for="telefono" class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                                            <input type="tel" id="telefono" name="telefono"
                                                   value="<?= htmlspecialchars($usuario['numerocont'] ?? '') ?>"
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Cambio de contraseña -->
                                <div class="pt-6 border-t border-gray-200">
                                    <h4 class="text-md font-medium text-gray-900 mb-4">Cambiar Contraseña</h4>
                                    <p class="text-sm text-gray-500 mb-4">Deja estos campos en blanco si no deseas cambiar la contraseña.</p>
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div>
                                            <label for="contrasena_actual" class="block text-sm font-medium text-gray-700 mb-1">Contraseña Actual</label>
                                            <input type="password" id="contrasena_actual" name="contrasena_actual"
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                        </div>
                                        <div class="opacity-50">
                                            <!-- Espacio vacío para mantener el diseño -->
                                        </div>
                                        <div>
                                            <label for="nueva_contrasena" class="block text-sm font-medium text-gray-700 mb-1">Nueva Contraseña</label>
                                            <input type="password" id="nueva_contrasena" name="nueva_contrasena"
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                            <p class="mt-1 text-xs text-gray-500">Mínimo 8 caracteres</p>
                                        </div>
                                        <div>
                                            <label for="confirmar_contrasena" class="block text-sm font-medium text-gray-700 mb-1">Confirmar Nueva Contraseña</label>
                                            <input type="password" id="confirmar_contrasena" name="confirmar_contrasena"
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-8 flex justify-end space-x-3">
                                <a href="perfil.php" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    Cancelar
                                </a>
                                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <i class="fas fa-save mr-2"></i>Guardar Cambios
                                </button>
                            </div>
                        </form>
                    </div>
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

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
</body>
</html>
