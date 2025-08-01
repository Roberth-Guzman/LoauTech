<?php
session_start();

// 2. Definir ruta base del proyecto
$root = $_SERVER['DOCUMENT_ROOT'] . '/LoauTech-main/';

// 3. Incluir conexión con manejo de errores
try {
    require_once $root . 'conexion.php';
} catch (Throwable $e) {
    die("Error al conectar con la base de datos: Archivo de conexión no encontrado");
}

// 4. Verificar disponibilidad de la base de datos
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
    include $root . 'panel-admin/includes/footer.php';
    exit;
}


// 2. Verificar autenticación del usuario
if (!isset($_SESSION['usuario'])) {
    header('Location: ../login.php');
    exit();
}

// 3. Verificar rol de administrador
if ($_SESSION['usuario']['rol'] !== 'admin') {
    header('Location: ../login.php?error=acceso_no_autorizado');
    exit();
}

// 4. Obtener datos del usuario actual
$usuario_actual = $_SESSION['usuario']['documento'];
$sql = "SELECT p.nombrecompletoper, r.rol 
        FROM personas p
        JOIN roles r ON p.IDper = r.idper
        WHERE p.numerodoc = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_actual);
$stmt->execute();
$resultado = $stmt->get_result();
$datos_usuario = $resultado->fetch_assoc();
$stmt->close();

// Configurar datos para las vistas
$_SESSION['user'] = [
    'nombre' => $datos_usuario['nombrecompletoper'] ?? 'Administrador',
    'rol' => $datos_usuario['rol'] ?? 'admin'
];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración - LOAUTECH</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="flex h-screen">
     
    <?php include 'includes/sidebar-admin.php'; ?>
        <!-- Contenido principal con margen para el sidebar -->
        <div class="flex-1 ml-64 overflow-auto">
            <!-- Header -->
            <div class="bg-white shadow-sm">
                <div class="max-w-7xl mx-auto px-4 py-4 sm:px-6 lg:px-8 flex justify-between items-center">
                    <h1 class="text-xl font-bold text-gray-900">
                        <i class="fas fa-user-shield mr-2 text-blue-600"></i>
                        Panel de Administración
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

            <!-- Contenido del dashboard -->
            <main class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                    <!-- Tarjeta Usuarios -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
                                <i class="fas fa-users text-xl"></i>
                            </div>
                            <div>
                                <p class="text-gray-500">Total Usuarios</p>
                                <h3 class="text-2xl font-bold">
                                    <?php
                                    $sql = "SELECT COUNT(*) as total FROM personas";
                                    $result = $conn->query($sql);
                                    echo $result ? $result->fetch_assoc()['total'] : '0';
                                    ?>
                                </h3>
                            </div>
                        </div>
                    </div>

                    <!-- Tarjeta Elementos -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
                                <i class="fas fa-boxes text-xl"></i>
                            </div>
                            <div>
                                <p class="text-gray-500">Total Elementos</p>
                                <h3 class="text-2xl font-bold">
                                    <?php
                                    $sql = "SELECT COUNT(*) as total FROM elementos";
                                    $result = $conn->query($sql);
                                    echo $result ? $result->fetch_assoc()['total'] : '0';
                                    ?>
                                </h3>
                            </div>
                        </div>
                    </div>

                    <!-- Tarjeta Préstamos -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-yellow-100 text-yellow-600 mr-4">
                                <i class="fas fa-exchange-alt text-xl"></i>
                            </div>
                            <div>
                                <p class="text-gray-500">Préstamos Activos</p>
                                <h3 class="text-2xl font-bold">
                                    <?php
                                    $sql = "SELECT COUNT(*) as total FROM prestamos";
                                    $result = $conn->query($sql);
                                    echo $result ? $result->fetch_assoc()['total'] : '0';
                                    ?>
                                </h3>
                            </div>
                        </div>
                    </div>

                    <!-- Tarjeta Alertas -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-red-100 text-red-600 mr-4">
                                <i class="fas fa-exclamation-triangle text-xl"></i>
                            </div>
                            <div>
                                <p class="text-gray-500">Alertas</p>
                                <h3 class="text-2xl font-bold">0</h3>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sección de Acciones Rápidas -->
                <div class="bg-white rounded-lg shadow p-6 mb-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-4">Acciones Rápidas</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <a href="personas/consultar.php" class="bg-blue-50 hover:bg-blue-100 rounded-lg p-4 text-center transition-colors">
                            <i class="fas fa-user-plus text-blue-600 text-2xl mb-2"></i>
                            <p class="font-medium">Nuevo Usuario</p>
                        </a>
                        <a href="elementos/consultar.php" class="bg-green-50 hover:bg-green-100 rounded-lg p-4 text-center transition-colors">
                            <i class="fas fa-box-open text-green-600 text-2xl mb-2"></i>
                            <p class="font-medium">Registrar Elemento</p>
                        </a>
                        <a href="database/exportar_db.php" class="bg-purple-50 hover:bg-purple-100 rounded-lg p-4 text-center transition-colors">
                            <i class="fas fa-file-export text-purple-600 text-2xl mb-2"></i>
                            <p class="font-medium">Exportar BD</p>
                        </a>
                        <a href="database/importar_db.php" class="bg-yellow-50 hover:bg-yellow-100 rounded-lg p-4 text-center transition-colors">
                            <i class="fas fa-file-import text-yellow-600 text-2xl mb-2"></i>
                            <p class="font-medium">Importar BD</p>
                        </a>
                        <a href="database/eliminardb.php" class="bg-red-50 hover:bg-red-100 rounded-lg p-4 text-center transition-colors" onclick="return confirm('¿Estás seguro de que deseas eliminar la base de datos? Esta acción no se puede deshacer.');">
                            <i class="fas fa-trash-alt text-red-600 text-2xl mb-2"></i>
                            <p class="font-medium">Eliminar BD</p>
                        </a>
                    </div>
                </div>

                <!-- Últimos Registros -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    <!-- Últimos Usuarios -->
                    <div class="bg-white rounded-lg shadow overflow-hidden">
                        <div class="p-4 border-b">
                            <h2 class="text-lg font-semibold text-gray-800">Últimos Usuarios Registrados</h2>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Documento</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php
                                    $sql = "SELECT * FROM personas ORDER BY IDper DESC LIMIT 5";
                                    $result = $conn->query($sql);
                                    
                                    if ($result && $result->num_rows > 0) {
                                        while($row = $result->fetch_assoc()) {
                                            echo "<tr>
                                                    <td class='px-6 py-4 whitespace-nowrap'>".htmlspecialchars($row['nombrecompletoper'])."</td>
                                                    <td class='px-6 py-4 whitespace-nowrap'>".htmlspecialchars($row['numerodoc'])."</td>
                                                </tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='2' class='px-6 py-4 text-center'>No hay usuarios registrados</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Últimos Elementos -->
                    <div class="bg-white rounded-lg shadow overflow-hidden">
                        <div class="p-4 border-b">
                            <h2 class="text-lg font-semibold text-gray-800">Últimos Elementos Registrados</h2>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Elemento</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Código</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php
                                    $sql = "SELECT * FROM elementos ORDER BY IDele DESC LIMIT 5";
                                    $result = $conn->query($sql);
                                    
                                    if ($result && $result->num_rows > 0) {
                                        while($row = $result->fetch_assoc()) {
                                            $badgeColor = $row['estado'] == 'activo' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
                                            echo "<tr>
                                                    <td class='px-6 py-4 whitespace-nowrap'>".htmlspecialchars($row['nombreele'])."</td>
                                                    <td class='px-6 py-4 whitespace-nowrap'>".htmlspecialchars($row['codigoele'])."</td>
                                                    <td class='px-6 py-4 whitespace-nowrap'>
                                                        <span class='px-2 py-1 text-xs rounded-full $badgeColor'>".htmlspecialchars($row['estado'])."</span>
                                                    </td>
                                                </tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='3' class='px-6 py-4 text-center'>No hay elementos registrados</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
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
</body>
</html>