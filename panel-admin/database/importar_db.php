<?php
// Aumentar los límites de memoria y tiempo de ejecución
ini_set('memory_limit', '512M');
set_time_limit(0);

session_start();
include '../../conexion.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['rol'] != 'admin') {
    header("Location: ../../login.php");
    exit;
}

$error_message = '';
$success_message = '';
$db_name = 'loatech'; // Nombre de la base de datos principal

// Función para ejecutar consultas SQL desde un archivo
function execute_sql_file($conn, $file_path) {
    // Leer el archivo en fragmentos para evitar problemas de memoria
    $file = fopen($file_path, 'r');
    if (!$file) {
        return "Error al abrir el archivo SQL";
    }
    
    // Desactivar restricciones de claves foráneas temporalmente
    $conn->query("SET FOREIGN_KEY_CHECKS = 0");
    $conn->query("SET NAMES 'utf8mb4'");
    $conn->query("SET CHARACTER SET utf8mb4");
    
    $query = "";
    $errors = [];
    $in_multi_line_comment = false;
    
    while (!feof($file)) {
        $line = fgets($file);
        
        // Manejar comentarios de múltiples líneas
        if (preg_match('/\/\*/', $line)) {
            $in_multi_line_comment = true;
        }
        
        if ($in_multi_line_comment) {
            if (preg_match('/\*\//', $line)) {
                $in_multi_line_comment = false;
            }
            continue;
        }
        
        // Saltar comentarios de una línea
        if (preg_match('/^--|^#|^\/\//', trim($line))) {
            continue;
        }
        
        // Manejar delimitadores
        if (stripos(trim($line), 'DELIMITER') === 0) {
            continue;
        }
        
        // Agregar línea a la consulta actual
        $query .= $line;
        
        // Si la línea termina en punto y coma, ejecutamos la consulta
        if (preg_match('/;\s*$/', $line)) {
            $query = trim($query);
            
            // Solo ejecutar consultas no vacías
            if (!empty($query)) {
                if (!$conn->query($query)) {
                    $errors[] = "Error en consulta: " . $conn->error . "\nConsulta: " . substr($query, 0, 200) . "...";
                }
            }
            
            // Reiniciar la consulta
            $query = "";
        }
    }
    
    fclose($file);
    
    // Reactivar restricciones de claves foráneas
    $conn->query("SET FOREIGN_KEY_CHECKS = 1");
    
    return empty($errors) ? true : $errors;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['sql_file'])) {
    if ($_FILES['sql_file']['error'] === UPLOAD_ERR_OK) {
        $tmp_name = $_FILES['sql_file']['tmp_name'];
        $file_extension = strtolower(pathinfo($_FILES['sql_file']['name'], PATHINFO_EXTENSION));
        
        if ($file_extension !== 'sql') {
            $error_message = "El archivo debe tener extensión .sql";
        } else {
            // Crear conexión temporal sin seleccionar base de datos
            $temp_conn = new mysqli("localhost", "root", "");
            
            if ($temp_conn->connect_error) {
                $error_message = "Error de conexión al servidor: " . $temp_conn->connect_error;
            } else {
                // Crear la base de datos si no existe
                if (!$temp_conn->query("CREATE DATABASE IF NOT EXISTS `$db_name` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci")) {
                    $error_message = "Error al crear la base de datos: " . $temp_conn->error;
                } else {
                    // Seleccionar la base de datos
                    $temp_conn->select_db($db_name);
                    
                    // Ejecutar el archivo SQL
                    $result = execute_sql_file($temp_conn, $tmp_name);
                    
                    if ($result === true) {
                        $success_message = "Base de datos importada correctamente";
                        
                        // Verificar si las tablas principales existen
                        $required_tables = ['usuarios', 'personas', 'elementos'];
                        $missing_tables = [];
                        
                        foreach ($required_tables as $table) {
                            $check = $temp_conn->query("SHOW TABLES LIKE '$table'");
                            if ($check->num_rows === 0) {
                                $missing_tables[] = $table;
                            }
                        }
                        
                        if (!empty($missing_tables)) {
                            $error_message = "Advertencia: Faltan tablas importantes: " . implode(', ', $missing_tables);
                        }
                    } else {
                        $error_message = "Error al importar la base de datos:<br>" . 
                                      implode("<br>", (array)$result);
                    }
                }
                $temp_conn->close();
            }
        }
    } else {
        $error_message = "Error al subir el archivo: " . get_upload_error_message($_FILES['sql_file']['error']);
    }
}

// Función para obtener mensajes de error de subida de archivos
function get_upload_error_message($error_code) {
    switch ($error_code) {
        case UPLOAD_ERR_INI_SIZE:
            return 'El archivo excede el tamaño máximo permitido por el servidor.';
        case UPLOAD_ERR_FORM_SIZE:
            return 'El archivo excede el tamaño máximo permitido por el formulario.';
        case UPLOAD_ERR_PARTIAL:
            return 'El archivo solo se subió parcialmente.';
        case UPLOAD_ERR_NO_FILE:
            return 'No se seleccionó ningún archivo.';
        case UPLOAD_ERR_NO_TMP_DIR:
            return 'Falta la carpeta temporal.';
        case UPLOAD_ERR_CANT_WRITE:
            return 'Error al escribir el archivo en el disco.';
        case UPLOAD_ERR_EXTENSION:
            return 'Una extensión de PHP detuvo la carga del archivo.';
        default:
            return 'Error desconocido al subir el archivo.';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Importar Base de Datos</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <?php include '../includes/header.php'; ?>
    
    <div class="ml-64 p-6">
        <div class="max-w-3xl mx-auto bg-white rounded-lg shadow p-6">
            <div class="text-center mb-6">
                <i class="fas fa-database text-blue-500 text-4xl mb-3"></i>
                <h1 class="text-2xl font-bold">Importar Base de Datos</h1>
                <p class="text-sm text-gray-500 mt-1">Se importará a la base de datos: <span class="font-semibold"><?= htmlspecialchars($db_name) ?></span></p>
            </div>
            
            <?php if ($error_message): ?>
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-circle text-red-500"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm"><?= $error_message ?></p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            
            <?php if ($success_message): ?>
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-check-circle text-green-500"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium"><?= $success_message ?></p>
                            <?php if (isset($missing_tables) && !empty($missing_tables)): ?>
                                <p class="text-sm mt-1 text-yellow-700">
                                    <i class="fas fa-exclamation-triangle mr-1"></i>
                                    <?= $error_message ?>
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            
            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6 rounded">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-triangle text-yellow-500"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-yellow-700">
                            <strong>Advertencia:</strong> Esta operación reemplazará la base de datos actual. 
                            Asegúrate de tener una copia de seguridad antes de continuar.
                        </p>
                    </div>
                </div>
            </div>
            
            <form method="post" enctype="multipart/form-data" class="space-y-6">
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center">
                    <div id="drop-zone" class="cursor-pointer">
                        <i class="fas fa-file-upload text-4xl text-gray-400 mb-3"></i>
                        <p class="text-sm text-gray-600 mb-2">Arrastra tu archivo SQL aquí o haz clic para seleccionar</p>
                        <p class="text-xs text-gray-500 mb-4">Archivos .sql</p>
                        <div id="file-info" class="hidden mt-4 p-3 bg-gray-50 rounded-md">
                            <i class="fas fa-file-alt text-blue-500 mr-2"></i>
                            <span id="file-name" class="text-sm font-medium"></span>
                        </div>
                        <input type="file" id="sql_file" name="sql_file" accept=".sql" class="hidden" required>
                    </div>
                </div>
                
                <div class="flex items-center justify-between">
                    <a href="eliminardb.php" class="text-sm text-gray-600 hover:text-gray-800 flex items-center">
                        <i class="fas fa-arrow-left mr-1"></i> Volver al panel
                    </a>
                    <div class="space-x-3">
                        <button type="reset" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            Cancelar
                        </button>
                        <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <i class="fas fa-upload mr-2"></i> Importar Base de Datos
                        </button>
                    </div>
                </div>
            </form>
            
            <div class="mt-8 border-t pt-6">
                <h3 class="text-lg font-medium text-gray-900 mb-3">Instrucciones:</h3>
                <ol class="list-decimal list-inside space-y-2 text-sm text-gray-600">
                    <li>Haz clic en el área de arriba o arrastra un archivo SQL.</li>
                    <li>El archivo debe ser una exportación válida de la base de datos.</li>
                    <li>El proceso puede tardar varios minutos dependiendo del tamaño del archivo.</li>
                    <li>No cierres ni actualices la página durante la importación.</li>
                </ol>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const dropZone = document.getElementById('drop-zone');
        const fileInput = document.getElementById('sql_file');
        const fileName = document.getElementById('file-name');
        const fileInfo = document.getElementById('file-info');
        
        // Manejar clic en el área de soltar
        dropZone.addEventListener('click', () => fileInput.click());
        
        // Mostrar información del archivo seleccionado
        fileInput.addEventListener('change', (e) => {
            if (fileInput.files.length > 0) {
                fileName.textContent = fileInput.files[0].name;
                fileInfo.classList.remove('hidden');
            } else {
                fileInfo.classList.add('hidden');
            }
        });
        
        // Manejar arrastrar y soltar
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
        
        function highlight() {
            dropZone.classList.add('border-blue-400', 'bg-blue-50');
        }
        
        function unhighlight() {
            dropZone.classList.remove('border-blue-400', 'bg-blue-50');
        }
        
        // Manejar archivos soltados
        dropZone.addEventListener('drop', handleDrop, false);
        
        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            
            if (files.length) {
                fileInput.files = files;
                fileName.textContent = files[0].name;
                fileInfo.classList.remove('hidden');
            }
        }
    });
    </script>
</body>
</html>