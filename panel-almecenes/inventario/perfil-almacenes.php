<?php
session_start();

// Verificar si la sesión está iniciada y tiene la estructura esperada
if (!isset($_SESSION['usuario']) || !is_array($_SESSION['usuario'])) {
    header("Location: ../../login.php");
    exit;
}

// Verificar que el usuario tenga el rol de almacenes
if ($_SESSION['usuario']['rol'] !== 'almacenes') {
    header("Location: ../../login.php?error=acceso_denegado");
    exit;
}

// Verificar que el ID del usuario esté presente en la sesión
if (!isset($_SESSION['usuario']['IDper']) || empty($_SESSION['usuario']['IDper'])) {
    session_destroy();
    header("Location: ../../login.php?error=sesion_invalida");
    exit;
}

// Incluir el archivo de conexión a la base de datos
require_once __DIR__ . '/../../conexion.php';

$idUsuario = (int)$_SESSION['usuario']['IDper'];
$mensaje = isset($_GET['mensaje']) ? htmlspecialchars($_GET['mensaje']) : '';

// Obtener datos del usuario
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
    echo "<div class='p-8 bg-red-100 text-red-700 font-bold'>
            No se encontró información del usuario con ID $idUsuario.
          </div>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Perfil de Almacenes - Loautech</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex flex-col">
        <!-- Header -->
        <header class="bg-blue-600 text-white p-4 shadow-md">
            <div class="container mx-auto flex justify-between items-center">
                <a href="panel-inventario.php" class="text-white text-xl font-bold flex items-center space-x-2">
                    <i class="fas fa-arrow-left"></i>
                    <span>Volver al panel</span>
                </a>
                <h1 class="text-xl font-bold">Perfil de Usuario</h1>
                <div class="w-8"></div> <!-- Spacer para mantener el título centrado -->
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-grow container mx-auto p-4 md:p-6">
            <?php if (!empty($mensaje)): ?>
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded" role="alert">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle mr-2"></i>
                        <p><?= $mensaje ?></p>
                    </div>
                </div>
            <?php endif; ?>

            <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-md overflow-hidden">
                <!-- Profile Header -->
                <div class="bg-gradient-to-r from-blue-600 to-blue-800 p-6 text-white text-center">
                    <div class="relative w-32 h-32 mx-auto mb-4">
                        <div class="w-full h-full rounded-full bg-white p-1">
                            <?php if (!empty($usuario['foto_ruta'])): ?>
                                <img src="<?= htmlspecialchars($usuario['foto_ruta']) ?>" 
                                     alt="Foto de perfil" 
                                     class="w-full h-full rounded-full object-cover" />
                            <?php else: ?>
                                <div class="w-full h-full rounded-full bg-blue-200 flex items-center justify-center">
                                    <i class="fas fa-user text-5xl text-blue-600"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        <button class="absolute bottom-0 right-0 bg-white text-blue-600 rounded-full p-2 shadow-md hover:bg-blue-50 transition-colors">
                            <i class="fas fa-camera"></i>
                        </button>
                    </div>
                    <h2 class="text-2xl font-bold"><?= htmlspecialchars($usuario['nombrecompletoper']) ?></h2>
                    <p class="text-blue-100"><?= ucfirst(htmlspecialchars($usuario['rol'])) ?></p>
                </div>

                <!-- Profile Information -->
                <div class="p-6">
                    <!-- Personal Information -->
                    <div class="mb-8">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold text-gray-800">Información Personal</h3>
                            <a href="editar-perfil.php" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                <i class="fas fa-edit mr-1"></i> Editar
                            </a>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <p class="text-sm text-gray-500 mb-1">Tipo de Documento</p>
                                <p class="font-medium"><?= htmlspecialchars($usuario['tipodocumento'] ?? 'No especificado') ?></p>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <p class="text-sm text-gray-500 mb-1">Número de Documento</p>
                                <p class="font-medium"><?= htmlspecialchars($usuario['numerodoc'] ?? 'No especificado') ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Information -->
                    <div>
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold text-gray-800">Información de Contacto</h3>
                            <a href="editar-contacto.php" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                <i class="fas fa-edit mr-1"></i> Editar
                            </a>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <p class="text-sm text-gray-500 mb-1">Correo Electrónico</p>
                                <p class="font-medium"><?= htmlspecialchars($usuario['correocont'] ?? 'No especificado') ?></p>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <p class="text-sm text-gray-500 mb-1">Teléfono</p>
                                <p class="font-medium"><?= htmlspecialchars($usuario['numerocont'] ?? 'No especificado') ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="bg-gray-50 px-6 py-4 border-t flex justify-end space-x-3">
                    <a href="cambiar-contrasena.php" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        Cambiar Contraseña
                    </a>
                    <a href="../../logout.php" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700">
                        Cerrar Sesión
                    </a>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-gray-800 text-white p-4 text-center text-sm">
            <p> Loautech - Sistema de Gestión de Inventario</p>
        </footer>
    </div>
</body>
</html>