<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] != 'admin') {
    header('Location: ../login.php');
    exit();
}

// Incluir el archivo de conexión desde la raíz del proyecto
require_once __DIR__ . '/../../conexion.php';

// Verificar si la conexión se estableció correctamente
if (!isset($conn) || $conn->connect_error) {
    die("Error de conexión a la base de datos");
}

// Obtener datos del usuario para la sesión
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

// Obtener el mensaje de éxito si existe
$mensaje = $_GET['mensaje'] ?? '';
$tipoMensaje = $_GET['tipo'] ?? 'info';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Autorizaciones - LOAUTECH</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <?php include('../includes/sidebar.php'); ?>
        
        <div class="flex-1 ml-64 overflow-auto">
            <!-- Header -->
            <?php include('../includes/header.php'); ?>

            <!-- Contenido principal -->
            <main class="p-6">
                <?php if ($mensaje): ?>
                    <div class="mb-4 p-4 rounded-md <?= $tipoMensaje === 'exito' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' ?>">
                        <?= htmlspecialchars($mensaje) ?>
                    </div>
                <?php endif; ?>

                <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                    <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0">
                            <div>
                                <h3 class="text-lg leading-6 font-medium text-gray-900">
                                    Gestión de Autorizaciones
                                </h3>
                                <p class="mt-1 max-w-2xl text-sm text-gray-500">
                                    Administra las autorizaciones del sistema
                                </p>
                            </div>
                            <div class="flex-shrink-0">
                                <a href="crear_autorizacion.php" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    <i class="fas fa-plus mr-2"></i> Nueva Autorización
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">V°B° Cuentadante</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Autorizador</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cargo</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php
                                try {
                                    $query = "SELECT * FROM autorizacion ORDER BY IDaut DESC";
                                    $result = $conn->query($query);

                                    if ($result && $result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                            // Clases para los estados
                                            $estadoClases = [
                                                'pendiente' => 'bg-yellow-100 text-yellow-800',
                                                'aprobado' => 'bg-green-100 text-green-800',
                                                'rechazado' => 'bg-red-100 text-red-800'
                                            ];
                                            $estadoTexto = ucfirst($row['estadoaut']);
                                            $clase = $estadoClases[$row['estadoaut']] ?? 'bg-gray-100 text-gray-800';
                                            
                                            echo "<tr class='hover:bg-gray-50'>";
                                            echo "<td class='px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900'>" . $row['IDaut'] . "</td>";
                                            echo "<td class='px-6 py-4 whitespace-nowrap text-sm text-gray-500'>" . htmlspecialchars($row['VoBoCuentadanteaut']) . "</td>";
                                            echo "<td class='px-6 py-4 whitespace-nowrap text-sm text-gray-900'>" . htmlspecialchars($row['nomquienaturiza']) . "</td>";
                                            echo "<td class='px-6 py-4 whitespace-nowrap text-sm text-gray-500'>" . htmlspecialchars($row['cargoquienautoriza']) . "</td>";
                                            echo "<td class='px-6 py-4 whitespace-nowrap'>";
                                            echo "<span class='px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full $clase'>$estadoTexto</span>";
                                            echo "</td>";
                                            echo "<td class='px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2'>";
                                            echo "<a href='ver_autorizacion.php?id=" . $row['IDaut'] . "' class='text-blue-600 hover:text-blue-900' title='Ver detalles'><i class='fas fa-eye'></i></a>";
                                            echo "<a href='editar_autorizacion.php?id=" . $row['IDaut'] . "' class='text-indigo-600 hover:text-indigo-900' title='Editar'><i class='fas fa-edit'></i></a>";
                                            echo "<a href='eliminar_autorizacion.php?id=" . $row['IDaut'] . "' class='text-red-600 hover:text-red-900' title='Eliminar' onclick='return confirm(\"¿Está seguro de eliminar esta autorización?\")'><i class='fas fa-trash'></i></a>";
                                            echo "</td>";
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='6' class='px-6 py-8 text-center text-sm text-gray-500'>";
                                        echo "<i class='fas fa-inbox text-4xl text-gray-300 mb-2 block'></i>";
                                        echo "<p>No se encontraron autorizaciones registradas</p>";
                                        echo "<p class='mt-2'><a href='crear_autorizacion.php' class='text-indigo-600 hover:text-indigo-900 font-medium'>Crea una nueva autorización</a> para comenzar.</p>";
                                        echo "</td></tr>";
                                    }
                                } catch (Exception $e) {
                                    echo "<tr><td colspan='6' class='px-6 py-8 text-center text-sm text-red-500'>";
                                    echo "<i class='fas fa-exclamation-triangle text-2xl text-red-300 mb-2 block'></i>";
                                    echo "<p>Error al cargar las autorizaciones. Por favor, intente nuevamente.</p>";
                                    if ($_SESSION['user']['rol'] === 'admin') {
                                        echo "<p class='text-xs mt-1 text-gray-500'>" . htmlspecialchars($e->getMessage()) . "</p>";
                                    }
                                    echo "</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Paginación (puedes implementarla según tu necesidad) -->
                    <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                        <div class="flex-1 flex justify-between sm:hidden">
                            <a href="#" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                Anterior
                            </a>
                            <a href="#" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                Siguiente
                            </a>
                        </div>
                        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                            <div>
                                <p class="text-sm text-gray-700">
                                    Mostrando <span class="font-medium">1</span> a <span class="font-medium">10</span> de <span class="font-medium">20</span> resultados
                                </p>
                            </div>
                            <div>
                                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                    <a href="#" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                        <span class="sr-only">Anterior</span>
                                        <i class="fas fa-chevron-left h-5 w-5"></i>
                                    </a>
                                    <!-- Current: "z-10 bg-indigo-50 border-indigo-500 text-indigo-600", Default: "bg-white border-gray-300 text-gray-500 hover:bg-gray-50" -->
                                    <a href="#" aria-current="page" class="z-10 bg-indigo-50 border-indigo-500 text-indigo-600 relative inline-flex items-center px-4 py-2 border text-sm font-medium"> 1 </a>
                                    <a href="#" class="bg-white border-gray-300 text-gray-500 hover:bg-gray-50 relative inline-flex items-center px-4 py-2 border text-sm font-medium"> 2 </a>
                                    <a href="#" class="bg-white border-gray-300 text-gray-500 hover:bg-gray-50 relative inline-flex items-center px-4 py-2 border text-sm font-medium"> 3 </a>
                                    <a href="#" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                        <span class="sr-only">Siguiente</span>
                                        <i class="fas fa-chevron-right h-5 w-5"></i>
                                    </a>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
</body>
</html>
