<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($data['titulo'] ?? 'Restaurar Base de Datos'); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="flex min-h-screen">
        <?php require_once __DIR__ . '/includes/sidebar-admin.php'; ?>
        <div class="flex-1 p-8">
            <div class="max-w-4xl mx-auto">
                <!-- Encabezado -->
                <div class="text-center mb-8">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-yellow-100 rounded-full mb-4">
                        <i class="fas fa-database text-2xl text-yellow-600"></i>
                    </div>
                    <h1 class="text-3xl font-bold text-gray-800 mb-2">Base de Datos Vacía</h1>
                    <p class="text-gray-600">La base de datos no contiene tablas. Necesitas importar un archivo SQL para restaurar los datos.</p>
                </div>

                <!-- Mensajes de estado -->
                <?php if (!empty($data['success'])): ?>
                    <div class="mb-6 p-4 rounded-lg bg-green-100 border border-green-200">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-600 mr-3"></i>
                            <div>
                                <h3 class="font-semibold text-green-800">¡Operación Exitosa!</h3>
                                <p class="text-green-700">
                                    <?php if ($data['success'] === 'bd_eliminada'): ?>
                                        Base de datos eliminada correctamente. Ahora puedes importar una nueva copia.
                                    <?php elseif (strpos($data['success'], 'importada') !== false): ?>
                                        <?php echo htmlspecialchars($data['success']); ?>
                                    <?php else: ?>
                                        <?php echo htmlspecialchars($data['success']); ?>
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($data['error'])): ?>
                    <div class="mb-6 p-4 rounded-lg bg-red-100 border border-red-200">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-triangle text-red-600 mr-3"></i>
                            <div>
                                <h3 class="font-semibold text-red-800">Error</h3>
                                <p class="text-red-700">
                                    <?php 
                                    $errorMessages = [
                                        'no_archivo' => 'No se seleccionó ningún archivo.',
                                        'extension_invalida' => 'El archivo debe tener extensión .sql',
                                        'archivo_muy_grande' => 'El archivo es demasiado grande (máximo 50MB).',
                                        'archivo_vacio' => 'El archivo seleccionado está vacío.',
                                        'import_fail' => 'Error al importar la base de datos.',
                                        'upload_error' => 'Error al subir el archivo.',
                                        'excepcion' => 'Ocurrió un error inesperado.'
                                    ];
                                    
                                    $errorCode = $data['error'];
                                    $errorMsg = $errorMessages[$errorCode] ?? 'Error desconocido: ' . $errorCode;
                                    
                                    if (isset($_GET['msg'])) {
                                        $errorMsg .= ' Detalles: ' . htmlspecialchars($_GET['msg']);
                                    }
                                    
                                    echo $errorMsg;
                                    ?>
                                </p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Formulario de importación principal -->
                <div class="bg-white rounded-lg shadow-lg p-8 mb-6">
                    <div class="text-center mb-6">
                        <h2 class="text-2xl font-bold text-gray-800 mb-2">
                            <i class="fas fa-upload text-blue-600 mr-2"></i>
                            Importar Base de Datos
                        </h2>
                        <p class="text-gray-600">Selecciona un archivo SQL para restaurar la base de datos completa</p>
                    </div>

                    <form action="<?php echo BASE_URL; ?>/admin/importarBD" method="POST" enctype="multipart/form-data" class="space-y-6">
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-blue-400 transition-colors">
                            <div class="mb-4">
                                <i class="fas fa-file-upload text-4xl text-gray-400 mb-2"></i>
                                <p class="text-lg font-medium text-gray-700">Arrastra tu archivo SQL aquí</p>
                                <p class="text-sm text-gray-500">o haz clic para seleccionar</p>
                            </div>
                            <input type="file" name="archivo_sql" accept=".sql" required 
                                   class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
                        </div>
                        
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <h3 class="font-semibold text-blue-800 mb-2">
                                <i class="fas fa-info-circle mr-2"></i>
                                Información Importante
                            </h3>
                            <ul class="text-sm text-blue-700 space-y-1">
                                <li>• El archivo debe tener extensión .sql</li>
                                <li>• Tamaño máximo: 50MB</li>
                                <li>• Se creará automáticamente la base de datos y todas las tablas</li>
                                <li>• Compatible con archivos exportados desde phpMyAdmin</li>
                                <li>• El proceso puede tomar varios minutos para archivos grandes</li>
                            </ul>
                        </div>

                        <div class="text-center">
                            <button type="submit" class="inline-flex items-center px-8 py-3 text-lg font-medium rounded-lg bg-blue-600 hover:bg-blue-700 text-white transition-colors shadow-lg">
                                <i class="fas fa-upload mr-2"></i>
                                Importar Base de Datos
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Información adicional -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="font-semibold text-gray-800 mb-3">
                            <i class="fas fa-question-circle text-blue-600 mr-2"></i>
                            ¿Necesitas un archivo SQL?
                        </h3>
                        <p class="text-sm text-gray-600 mb-3">
                            Si no tienes un archivo de respaldo, puedes usar el archivo loatech.sql que contiene la estructura base de la aplicación.
                        </p>
                        <div class="text-sm text-gray-500">
                            <strong>Archivo de referencia:</strong> loatech.sql<br>
                            <strong>Ubicación:</strong> Raíz del proyecto
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="font-semibold text-gray-800 mb-3">
                            <i class="fas fa-cogs text-green-600 mr-2"></i>
                            Estado del Sistema
                        </h3>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Base de datos:</span>
                                <span class="font-medium"><?php echo DB_NAME; ?></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Servidor:</span>
                                <span class="font-medium"><?php echo DB_HOST; ?></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Tablas:</span>
                                <span class="font-medium text-red-600">
                                    <?php echo $data['estado_bd']['total_tablas'] ?? 0; ?> (Vacía)
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Botón para volver al panel normal -->
                <div class="text-center mt-8">
                    <a href="<?php echo BASE_URL; ?>/admin/backup" class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg bg-gray-600 hover:bg-gray-700 text-white transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Volver al Panel de Respaldo
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Mejorar la experiencia del usuario con drag & drop
        const fileInput = document.querySelector('input[type="file"]');
        const dropZone = fileInput.closest('.border-dashed');

        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            dropZone.addEventListener(eventName, highlight, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, unhighlight, false);
        });

        function highlight(e) {
            dropZone.classList.add('border-blue-400', 'bg-blue-50');
        }

        function unhighlight(e) {
            dropZone.classList.remove('border-blue-400', 'bg-blue-50');
        }

        dropZone.addEventListener('drop', handleDrop, false);

        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            fileInput.files = files;
        }
    </script>
</body>
</html>