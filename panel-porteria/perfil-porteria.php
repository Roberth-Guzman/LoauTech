<?php
session_start();

// Verificar si la sesión está iniciada y tiene la estructura esperada
if (!isset($_SESSION['usuario']) || !is_array($_SESSION['usuario'])) {
    header("Location: ../login.php");
    exit;
}

// Verificar que el usuario tenga el rol de portería
if ($_SESSION['usuario']['rol'] !== 'porteria') {
    header("Location: ../login.php?error=acceso_denegado");
    exit;
}

// Verificar que el ID del usuario esté presente en la sesión
if (!isset($_SESSION['usuario']['IDper']) || empty($_SESSION['usuario']['IDper'])) {
    session_destroy();
    header("Location: ../login.php?error=sesion_invalida");
    exit;
}

include '../conexion.php';

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
    echo "<div style='padding: 2rem; background: #fdd; color: red; font-weight: bold;'>
            No se encontró información del usuario con ID $idUsuario.
          </div>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de Portería - LOAUTECH</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div class="w-64 bg-gray-800 text-white fixed top-0 left-0 bottom-0 z-10">
            <div class="p-4 border-b border-gray-700">
                <h1 class="text-xl font-bold">LOAUTECH</h1>
                <p class="text-sm text-gray-400">Panel de Portería</p>
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
                        <a href="escanner.php" class="flex items-center p-2 rounded hover:bg-gray-700">
                            <i class="fas fa-qrcode mr-3"></i>
                            Escanear QR
                        </a>
                    </li>
                    <li>
                        <a href="registros.php" class="flex items-center p-2 rounded hover:bg-gray-700">
                            <i class="fas fa-clipboard-list mr-3"></i>
                            Registros
                        </a>
                    </li>
                    <li>
                        <a href="aceptar-peticiones.php" class="flex items-center p-2 rounded hover:bg-gray-700">
                            <i class="fas fa-clipboard-check mr-3"></i>
                            Peticiones
                        </a>
                    </li>
                    <li>
                        <a href="perfil-porteria.php" class="flex items-center p-2 rounded bg-gray-700">
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
            <!-- Barra superior -->
            <header class="bg-white shadow-sm">
                <div class="max-w-7xl mx-auto px-4 py-4 sm:px-6 lg:px-8 flex justify-between items-center">
                    <h1 class="text-xl font-semibold text-gray-900">Perfil de Usuario</h1>
                    <div class="flex items-center space-x-4">
                        <span class="text-sm text-gray-500">
                            <?= date('d/m/Y') ?>
                        </span>
                    </div>
                </div>
            </header>

            <!-- Mensajes de éxito/error -->
            <?php if (!empty($mensaje)): ?>
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-md flex items-center justify-between" role="alert">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle mr-2"></i>
                            <p class="text-sm"><?= $mensaje ?></p>
                        </div>
                        <button type="button" class="text-green-700 hover:text-green-900" onclick="this.parentElement.remove()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            <?php endif; ?>

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
                                        if ($foto && file_exists("../" . $foto)) {
                                            echo "<img src='../" . htmlspecialchars($foto) . "' alt='Foto de perfil' class='h-40 w-40 rounded-full object-cover border-4 border-white shadow-md'>";
                                        } else {
                                            echo "<div class='h-40 w-40 rounded-full bg-gray-200 flex items-center justify-center'>";
                                            echo "<i class='fas fa-user text-gray-400 text-6xl'></i>";
                                            echo "</div>";
                                        }
                                        ?>
                                        <div class="absolute inset-0 rounded-full flex items-center justify-center bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition-opacity">
                                            <div class="flex space-x-2">
                                                <a href="foto/ver-foto.php" target="_blank" class="h-10 w-10 bg-white bg-opacity-90 rounded-full flex items-center justify-center text-blue-600 hover:bg-white transition-colors" title="Ver foto">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <button onclick="document.getElementById('modalSubirFoto').classList.remove('hidden')" class="h-10 w-10 bg-white bg-opacity-90 rounded-full flex items-center justify-center text-blue-600 hover:bg-white transition-colors" title="Cambiar foto">
                                                    <i class="fas fa-camera"></i>
                                                </button>
                                                <?php if ($foto): ?>
                                                <a href="foto/borrar-foto.php" onclick="return confirm('¿Seguro que quieres borrar tu foto de perfil?');" class="h-10 w-10 bg-white bg-opacity-90 rounded-full flex items-center justify-center text-red-600 hover:bg-white transition-colors" title="Eliminar foto">
                                                    <i class="fas fa-trash-alt"></i>
                                                </a>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Información del usuario -->
                                <div class="flex-1">
                                    <div class="space-y-4">
                                        <div>
                                            <h4 class="text-lg font-medium text-gray-900"><?= htmlspecialchars($usuario['nombrecompletoper'] ?? 'No disponible') ?></h4>
                                            <p class="text-sm text-gray-500"><?= ucfirst(htmlspecialchars($usuario['rol'] ?? 'portería')) ?></p>
                                        </div>
                                        
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <!-- Correo electrónico -->
                                            <div>
                                                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Correo Electrónico</p>
                                                <p class="mt-1 text-sm text-gray-900 break-all">
                                                    <?= !empty($usuario['correocont']) ? htmlspecialchars($usuario['correocont']) : '<span class="text-gray-400">No especificado</span>' ?>
                                                </p>
                                            </div>
                                            
                                            <!-- Teléfono -->
                                            <div>
                                                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Teléfono</p>
                                                <p class="mt-1 text-sm text-gray-900">
                                                    <?= !empty($usuario['numerocont']) ? htmlspecialchars($usuario['numerocont']) : '<span class="text-gray-400">No especificado</span>' ?>
                                                </p>
                                            </div>
                                            
                                            <!-- Documento -->
                                            <div>
                                                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    <?= htmlspecialchars($usuario['tipodocumento'] ?? 'Documento') ?>
                                                </p>
                                                <p class="mt-1 text-sm text-gray-900">
                                                    <?= !empty($usuario['numerodoc']) ? htmlspecialchars($usuario['numerodoc']) : '<span class="text-gray-400">No especificado</span>' ?>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end">
                            <a href="editar-portero.php" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <i class="fas fa-edit mr-2"></i>
                                Editar Perfil
                            </a>
                        </div>
                    </div>

    <!-- Modal para subir foto -->
    <div id="modalSubirFoto" class="fixed z-10 inset-0 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="this.parentElement.parentElement.classList.add('hidden')"></div>
            
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form action="foto/subir-foto.php" method="post" enctype="multipart/form-data">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                                <i class="fas fa-camera text-blue-600"></i>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                    Cambiar Foto de Perfil
                                </h3>
                                <div class="mt-4">
                                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                                        <div class="space-y-1 text-center">
                                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                            <div class="flex text-sm text-gray-600">
                                                <label for="foto_perfil" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                                    <span>Subir un archivo</span>
                                                    <input id="foto_perfil" name="foto_perfil" type="file" class="sr-only" accept="image/*" required>
                                                </label>
                                                <p class="pl-1">o arrastrar y soltar</p>
                                            </div>
                                            <p class="text-xs text-gray-500">
                                                Formatos: JPG, PNG, GIF (Máx. 2MB)
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                            <i class="fas fa-upload mr-2"></i> Subir Foto
                        </button>
                        <button type="button" onclick="document.getElementById('modalSubirFoto').classList.add('hidden')" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                            Cancelar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Función para mostrar la vista previa de la imagen seleccionada
        document.getElementById('foto_perfil')?.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.createElement('img');
                    preview.src = e.target.result;
                    preview.className = 'mt-2 rounded-full w-24 h-24 object-cover mx-auto';
                    const previewContainer = document.querySelector('.space-y-1.text-center');
                    const existingPreview = previewContainer.querySelector('img');
                    if (existingPreview) {
                        previewContainer.replaceChild(preview, existingPreview);
                    } else {
                        previewContainer.insertBefore(preview, previewContainer.firstChild);
                    }
                }
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>
</html>
