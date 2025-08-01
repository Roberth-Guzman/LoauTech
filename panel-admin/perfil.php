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

// Usar directamente los datos de la sesión para evitar problemas
$datos_usuario = [
    'nombrecompletoper' => $_SESSION['usuario']['nombre'],
    'numerodoc' => $_SESSION['usuario']['documento'],
    'IDper' => $_SESSION['usuario']['IDper'],
    'rol' => $_SESSION['usuario']['rol'],
    'estadocue' => 'activo', // Por defecto, ya que está logueado
    'telefonoper' => 'No registrado',
    'emailper' => 'No registrado',
    'direccionper' => 'No registrada',
    'tipodocumento' => 'CC' // Por defecto
];

// Obtener información de contacto desde la tabla contactos
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

// Si se encontró información de contacto, actualizar los datos
if ($contacto) {
    $datos_usuario['telefonoper'] = $contacto['numerocont'] ?: 'No registrado';
    $datos_usuario['emailper'] = $contacto['correocont'] ?: 'No registrado';
    $datos_usuario['direccionper'] = $contacto['direccioncont'] ?: 'No registrada';
}

// Obtener ruta de la foto actual del usuario
$sql_foto = "SELECT ruta FROM fotos_perfil WHERE id_persona = ? AND es_actual = 1 LIMIT 1";
$stmt_foto = $conn->prepare($sql_foto);
$stmt_foto->bind_param("i", $usuario_id);
$stmt_foto->execute();
$result_foto = $stmt_foto->get_result();
$foto_usuario = $result_foto->fetch_assoc();
$stmt_foto->close();

// Ajustar la ruta para que apunte a la carpeta uploads/fotos_perfil en la raíz del proyecto
$base_url = '/uploads/fotos_perfil/'; // carpeta uploads/fotos_perfil al nivel raíz del servidor web
$foto_perfil_url = $foto_usuario ? $base_url . basename($foto_usuario['ruta']) : null;

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Mi Perfil - LOAUTECH</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
</head>
<body class="bg-gray-100">
    <div class="flex h-screen">
        <?php include 'includes/sidebar-admin.php'; ?>
        <div class="flex-1 ml-64 overflow-auto">
            <!-- Header -->
            <div class="bg-white shadow-sm">
                <div class="max-w-7xl mx-auto px-4 py-4 sm:px-6 lg:px-8 flex justify-between items-center">
                    <h1 class="text-xl font-bold text-gray-900">
                        <i class="fas fa-user mr-2 text-blue-600"></i>
                        Mi Perfil
                    </h1>
                    <div class="flex items-center space-x-4">
                        <div class="flex items-center">
                            <a href="perfil.php" class="flex items-center hover:bg-gray-100 rounded-lg p-2 transition-colors duration-200">
                                <?php if ($foto_perfil_url): ?>
                                    <img src="<?= htmlspecialchars($foto_perfil_url) ?>" alt="Usuario" class="h-8 w-8 rounded-full cursor-pointer" />
                                <?php else: ?>
                                    <img src="https://ui-avatars.com/api/?name=<?= urlencode($_SESSION['usuario']['nombre'] ?? 'Admin') ?>&background=random" 
                                         alt="Usuario" class="h-8 w-8 rounded-full cursor-pointer" />
                                <?php endif; ?>
                                <span class="ml-2 text-sm font-medium text-gray-700"><?= htmlspecialchars($_SESSION['usuario']['nombre'] ?? 'Admin') ?></span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contenido del perfil -->
            <main class="p-6">
                <!-- Mensajes de foto -->
                <?php if (isset($_SESSION['mensaje_foto'])): ?>
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded" role="alert">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle mr-2"></i>
                            <p><?= htmlspecialchars($_SESSION['mensaje_foto']) ?></p>
                        </div>
                    </div>
                    <?php unset($_SESSION['mensaje_foto']); ?>
                <?php endif; ?>

                <?php if (isset($_SESSION['error_foto'])): ?>
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded" role="alert">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            <p><?= htmlspecialchars($_SESSION['error_foto']) ?></p>
                        </div>
                    </div>
                    <?php unset($_SESSION['error_foto']); ?>
                <?php endif; ?>

                <div class="max-w-4xl mx-auto">
                    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                        <div class="bg-gradient-to-r from-blue-600 to-blue-800 px-6 py-8">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 relative">
                                    <?php if ($foto_perfil_url): ?>
                                        <img src="<?= htmlspecialchars($foto_perfil_url) ?>" alt="Avatar" class="h-24 w-24 rounded-full border-4 border-white shadow-lg" />
                                    <?php else: ?>
                                        <img src="https://ui-avatars.com/api/?name=<?= urlencode($datos_usuario['nombrecompletoper']) ?>&background=random&size=120" 
                                             alt="Avatar" class="h-24 w-24 rounded-full border-4 border-white shadow-lg" />
                                    <?php endif; ?>
                                    <form action="foto/subir-foto.php" method="POST" enctype="multipart/form-data" class="absolute bottom-0 right-0">
                                        <label for="fotoPerfil" class="cursor-pointer bg-white text-blue-600 rounded-full p-2 shadow-md hover:bg-blue-50 transition-colors">
                                            <i class="fas fa-camera"></i>
                                        </label>
                                        <input type="file" name="fotoPerfil" id="fotoPerfil" accept="image/*" class="hidden" onchange="this.form.submit()">
                                    </form>
                                </div>
                                <div class="ml-6">
                                    <h2 class="text-2xl font-bold text-white"><?= htmlspecialchars($datos_usuario['nombrecompletoper']) ?></h2>
                                    <p class="text-blue-100 text-lg"><?= htmlspecialchars(ucfirst($datos_usuario['rol'])) ?></p>
                                    <div class="mt-2">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                            <i class="fas fa-check-circle mr-1"></i>
                                            <?= htmlspecialchars(ucfirst($datos_usuario['estadocue'])) ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="px-6 py-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                                        <i class="fas fa-user mr-2 text-blue-600"></i>
                                        Información Personal
                                    </h3>
                                    <div class="space-y-3">
                                        <div class="flex justify-between">
                                            <span class="text-sm font-medium text-gray-500">Documento:</span>
                                            <span class="text-sm text-gray-900"><?= htmlspecialchars($datos_usuario['numerodoc']) ?></span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-sm font-medium text-gray-500">Teléfono:</span>
                                            <span class="text-sm text-gray-900"><?= htmlspecialchars($datos_usuario['telefonoper']) ?></span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-sm font-medium text-gray-500">Email:</span>
                                            <span class="text-sm text-gray-900"><?= htmlspecialchars($datos_usuario['emailper']) ?></span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-sm font-medium text-gray-500">Dirección:</span>
                                            <span class="text-sm text-gray-900"><?= htmlspecialchars($datos_usuario['direccionper']) ?></span>
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                                        <i class="fas fa-cog mr-2 text-blue-600"></i>
                                        Información de la Cuenta
                                    </h3>
                                    <div class="space-y-3">
                                        <div class="flex justify-between">
                                            <span class="text-sm font-medium text-gray-500">Rol:</span>
                                            <span class="text-sm text-gray-900 capitalize"><?= htmlspecialchars($datos_usuario['rol']) ?></span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-sm font-medium text-gray-500">Estado:</span>
                                            <span class="text-sm text-gray-900 capitalize"><?= htmlspecialchars($datos_usuario['estadocue']) ?></span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-sm font-medium text-gray-500">Fecha de Registro:</span>
                                            <span class="text-sm text-gray-900">No disponible</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-sm font-medium text-gray-500">ID de Usuario:</span>
                                            <span class="text-sm text-gray-900"><?= htmlspecialchars($datos_usuario['IDper']) ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-8 flex flex-wrap gap-4">
                                <a href="editar-perfil.php" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 inline-block">
                                    <i class="fas fa-edit mr-2"></i>
                                    Editar Perfil
                                </a>
                               
                                <a href="panel-principal.php" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 inline-block">
                                    <i class="fas fa-arrow-left mr-2"></i>
                                    Volver al Dashboard
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </main>

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

    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
</body>
</html>