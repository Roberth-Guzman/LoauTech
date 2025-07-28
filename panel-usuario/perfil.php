<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario'])) {
    header("Location: ../login.php");
    exit;
}

include '../conexion.php';

$idUsuario = $_SESSION['usuario']['IDper'];

// Consulta para obtener los datos del usuario
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
    die("No se encontró información del usuario.");
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de Usuario - LOAUTECH</title>
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
                        <h1 class="text-xl font-bold text-gray-900">
                            Perfil de Usuario
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
                    <!-- Tarjeta de perfil -->
                    <div class="bg-white rounded-lg shadow-sm overflow-hidden mb-6">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Información del Perfil</h3>
                            <p class="mt-1 text-sm text-gray-500">Detalles personales y datos de contacto.</p>
                        </div>
                        
                        <div class="px-6 py-4">
                            <div class="flex flex-col md:flex-row items-center md:items-start space-y-6 md:space-y-0 md:space-x-6">
                                <!-- Foto de perfil -->
                                <div class="flex-shrink-0">
                                    <div class="relative group">
                                        <?php 
                                        $foto = $usuario['foto_ruta'] ?? null;
                                        if ($foto && file_exists("../" . $foto)): 
                                        ?>
                                            <img src="../<?= htmlspecialchars($foto) ?>" alt="Foto de perfil" 
                                                 class="h-40 w-40 rounded-full object-cover border-4 border-white shadow-md">
                                        <?php else: ?>
                                            <div class="h-40 w-40 rounded-full bg-gray-200 flex items-center justify-center">
                                                <i class="fas fa-user text-gray-400 text-6xl"></i>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <div class="absolute inset-0 bg-black bg-opacity-50 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                            <div class="flex space-x-2">
                                                <a href="#" class="bg-white p-2 rounded-full text-gray-800 hover:bg-gray-100" 
                                                   data-bs-toggle="modal" data-bs-target="#modalSubirFoto" title="Cambiar foto">
                                                    <i class="fas fa-camera"></i>
                                                </a>
                                                <?php if ($foto): ?>
                                                <a href="foto/borrar-foto.php" 
                                                   class="bg-white p-2 rounded-full text-red-600 hover:bg-red-50"
                                                   onclick="return confirm('¿Estás seguro de que quieres eliminar tu foto de perfil?');"
                                                   title="Eliminar foto">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="mt-4 text-center">
                                        <button type="button" 
                                                class="text-sm text-blue-600 hover:text-blue-800 font-medium"
                                                data-bs-toggle="modal" data-bs-target="#modalSubirFoto">
                                            Cambiar foto
                                        </button>
                                    </div>
                                </div>
                                
                                <!-- Información del usuario -->
                                <div class="flex-1">
                                    <div class="space-y-4">
                                        <div>
                                            <h4 class="text-lg font-medium text-gray-900"><?= htmlspecialchars($usuario['nombrecompletoper'] ?? '') ?></h4>
                                            <p class="text-sm text-gray-500"><?= ucfirst(htmlspecialchars($usuario['rol'] ?? 'usuario')) ?></p>
                                        </div>
                                        
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <p class="text-sm font-medium text-gray-500">Tipo de Documento</p>
                                                <p class="text-sm text-gray-900"><?= htmlspecialchars($usuario['tipodocumento'] ?? 'No especificado') ?></p>
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium text-gray-500">Número de Documento</p>
                                                <p class="text-sm text-gray-900"><?= htmlspecialchars($usuario['numerodoc'] ?? 'No especificado') ?></p>
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium text-gray-500">Correo Electrónico</p>
                                                <p class="text-sm text-gray-900"><?= htmlspecialchars($usuario['correocont'] ?? 'No especificado') ?></p>
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium text-gray-500">Teléfono</p>
                                                <p class="text-sm text-gray-900"><?= htmlspecialchars($usuario['numerocont'] ?? 'No especificado') ?></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end">
                            <a href="editar-perfil.php" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <i class="fas fa-edit mr-2"></i>
                                Editar Perfil
                            </a>
                        </div>
                    </div>
                    
                    <!-- Sección de estadísticas -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
                        <!-- Tarjeta de peticiones activas -->
                        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                            <div class="px-6 py-4 bg-blue-50 border-b border-blue-100">
                                <h3 class="text-sm font-medium text-blue-800">Peticiones Activas</h3>
                            </div>
                            <div class="px-6 py-4">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-3xl font-bold text-gray-900">0</p>
                                        <p class="text-sm text-gray-500">En proceso</p>
                                    </div>
                                    <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                                        <i class="fas fa-tasks text-xl"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Tarjeta de elementos registrados -->
                        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                            <div class="px-6 py-4 bg-green-50 border-b border-green-100">
                                <h3 class="text-sm font-medium text-green-800">Elementos Registrados</h3>
                            </div>
                            <div class="px-6 py-4">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-3xl font-bold text-gray-900">0</p>
                                        <p class="text-sm text-gray-500">En total</p>
                                    </div>
                                    <div class="p-3 rounded-full bg-green-100 text-green-600">
                                        <i class="fas fa-boxes text-xl"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Tarjeta de historial -->
                        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                            <div class="px-6 py-4 bg-purple-50 border-b border-purple-100">
                                <h3 class="text-sm font-medium text-purple-800">Actividad Reciente</h3>
                            </div>
                            <div class="px-6 py-4">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-3xl font-bold text-gray-900">0</p>
                                        <p class="text-sm text-gray-500">Acciones hoy</p>
                                    </div>
                                    <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                                        <i class="fas fa-history text-xl"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Sección de actividad reciente -->
                    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Actividad Reciente</h3>
                            <p class="mt-1 text-sm text-gray-500">Últimas acciones en el sistema</p>
                        </div>
                        <div class="divide-y divide-gray-200">
                            <div class="px-6 py-4">
                                <p class="text-sm text-gray-500 text-center">No hay actividad reciente para mostrar</p>
                            </div>
                        </div>
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

    <!-- Modal para subir foto -->
    <div class="modal fade" id="modalSubirFoto" tabindex="-1" aria-labelledby="modalSubirFotoLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-gray-100 border-b border-gray-200">
                    <h5 class="text-lg font-medium text-gray-900" id="modalSubirFotoLabel">Cambiar Foto de Perfil</h5>
                    <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center" data-bs-dismiss="modal" aria-label="Cerrar">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <form action="foto/subir-foto.php" method="post" enctype="multipart/form-data">
                    <div class="modal-body p-6">
                        <div class="mb-4">
                            <label for="foto" class="block text-sm font-medium text-gray-700 mb-2">Seleccionar imagen</label>
                            <input type="file" id="foto" name="foto_perfil" accept="image/jpeg, image/png, image/gif, image/webp" required
                                   class="block w-full text-sm text-gray-500
                                          file:mr-4 file:py-2 file:px-4
                                          file:rounded-md file:border-0
                                          file:text-sm file:font-semibold
                                          file:bg-blue-50 file:text-blue-700
                                          hover:file:bg-blue-100">
                            <p class="mt-1 text-xs text-gray-500">Formatos permitidos: JPG, PNG, GIF, WEBP. Tamaño máximo: 2MB</p>
                        </div>
                    </div>
                    <div class="modal-footer bg-gray-50 px-6 py-4 border-t border-gray-200 flex justify-end space-x-3">
                        <button type="button" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500" data-bs-dismiss="modal">
                            Cancelar
                        </button>
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <i class="fas fa-upload mr-2"></i>Subir Foto
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Inicializar tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    </script>
</body>
</html>
