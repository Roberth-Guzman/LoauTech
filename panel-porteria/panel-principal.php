<?php
session_start();

// Validar que el usuario haya iniciado sesión
if (!isset($_SESSION['usuario'])) {
    header('Location: ../login.php');
    exit();
}

// Validar que el rol sea 'porteria'
if ($_SESSION['usuario']['rol'] !== 'porteria') {
    header('Location: ../login.php?error=acceso_no_autorizado');
    exit();
}

// Incluir conexión a la base de datos
require_once '../conexion.php';

// Obtener y cargar datos adicionales si lo necesitas
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

// Guardar datos del usuario en sesión si es necesario
$_SESSION['user'] = [
    'nombre' => $datos_usuario['nombrecompletoper'] ?? 'Portería',
    'rol' => $datos_usuario['rol'] ?? 'porteria'
];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Portería - LOAUTECH</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        .sidebar {
            transition: all 0.3s;
        }
        .sidebar.collapsed {
            margin-left: -16rem;
        }
        .main-content {
            transition: all 0.3s;
        }
        .main-content.expanded {
            margin-left: 0;
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <div class="w-64 bg-gray-800 text-white fixed top-0 left-0 bottom-0 z-10 shadow-lg">
            <div class="p-4 border-b border-gray-700">
                <h1 class="text-xl font-bold text-white">LOAUTECH</h1>
                <p class="text-sm text-gray-300">Panel de Portería</p>
            </div>
            
            <!-- Menú de Navegación -->
            <nav class="p-4">
                <ul class="space-y-2">
                    <li>
                        <a href="panel-principal.php" class="flex items-center p-3 rounded-lg text-gray-300 hover:bg-gray-700 hover:text-white transition-colors">
                            <i class="fas fa-tachometer-alt w-6 text-center mr-3"></i>
                            <span>Inicio</span>
                        </a>
                    </li>
                    <li>
                        <a href="escanner.php" class="flex items-center p-3 rounded-lg text-gray-300 hover:bg-gray-700 hover:text-white transition-colors">
                            <i class="fas fa-qrcode w-6 text-center mr-3"></i>
                            <span>Escanear Carnet</span>
                        </a>
                    </li>
                    <li>
                        <a href="registros.php" class="flex items-center p-3 rounded-lg text-gray-300 hover:bg-gray-700 hover:text-white transition-colors">
                            <i class="fas fa-clipboard-list w-6 text-center mr-3"></i>
                            <span>Registros de Movimientos</span>
                        </a>
                    </li>
                    <li>
                        <a href="aceptar-peticiones.php" class="flex items-center p-3 rounded-lg text-gray-300 hover:bg-gray-700 hover:text-white transition-colors">
                            <i class="fas fa-clipboard-check w-6 text-center mr-3"></i>
                            <span>Peticiones</span>
                        </a>
                    </li>
                </ul>
            </nav>
            
            <!-- Perfil en la parte inferior -->
            <div class="absolute bottom-0 w-full p-4 border-t border-gray-700">
                <a href="perfil-porteria.php" class="flex items-center p-2 rounded-lg hover:bg-gray-700 transition-colors">
                    <div class="w-10 h-10 rounded-full bg-gray-600 flex items-center justify-center mr-3">
                        <i class="fas fa-user text-white"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-white truncate"><?php echo htmlspecialchars($_SESSION['user']['nombre']); ?></p>
                        <p class="text-xs text-gray-400 capitalize">Portería</p>
                    </div>
                    <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
                </a>
            </div>
        </div>

        <!-- Contenido Principal -->
        <div class="flex-1 ml-64 overflow-auto">
            <!-- Header -->
            <header class="bg-white shadow-sm">
                <div class="max-w-7xl mx-auto px-4 py-4 sm:px-6 lg:px-8 flex justify-between items-center">
                    <h1 class="text-2xl font-bold text-gray-900">
                        Panel de Portería
                    </h1>
                    <div class="flex items-center space-x-4">
                        <a href="../logout.php" class="flex items-center text-gray-700 hover:text-gray-900">
                            <i class="fas fa-sign-out-alt mr-2"></i>
                            <span>Cerrar Sesión</span>
                        </a>
                    </div>
                </div>
            </header>

            <!-- Contenido -->
            <main class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                    <!-- Tarjeta Escanear Carnet -->
                    <a href="escanner.php" class="block group">
                        <div class="bg-white rounded-xl shadow-md overflow-hidden transition-all duration-300 transform hover:-translate-y-1 hover:shadow-lg">
                            <div class="p-6">
                                <div class="flex items-center">
                                    <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
                                        <i class="fas fa-qrcode text-2xl"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900">Escanear Carnet</h3>
                                        <p class="text-sm text-gray-500">Registrar entrada/salida de personal</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>

                    <!-- Tarjeta Registros -->
                    <a href="registros.php" class="block group">
                        <div class="bg-white rounded-xl shadow-md overflow-hidden transition-all duration-300 transform hover:-translate-y-1 hover:shadow-lg">
                            <div class="p-6">
                                <div class="flex items-center">
                                    <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
                                        <i class="fas fa-clipboard-list text-2xl"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900">Registros</h3>
                                        <p class="text-sm text-gray-500">Ver historial de movimientos</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>

                    <!-- Tarjeta Peticiones -->
                    <a href="aceptar-peticiones.php" class="block group">
                        <div class="bg-white rounded-xl shadow-md overflow-hidden transition-all duration-300 transform hover:-translate-y-1 hover:shadow-lg">
                            <div class="p-6">
                                <div class="flex items-center">
                                    <div class="p-3 rounded-full bg-purple-100 text-purple-600 mr-4">
                                        <i class="fas fa-clipboard-check text-2xl"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900">Peticiones</h3>
                                        <p class="text-sm text-gray-500">Gestionar solicitudes de préstamo</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Sección de Bienvenida -->
                <div class="bg-white rounded-xl shadow-md overflow-hidden mb-8">
                    <div class="p-6">
                        <h2 class="text-xl font-bold text-gray-800 mb-4">Bienvenido, <?php echo htmlspecialchars(explode(' ', $_SESSION['user']['nombre'])[0]); ?></h2>
                        <p class="text-gray-600 mb-4">
                            Desde este panel podrás gestionar los accesos al edificio, registrar movimientos y gestionar las peticiones de préstamo de elementos.
                        </p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
                            <div class="bg-blue-50 p-4 rounded-lg">
                                <h3 class="font-medium text-blue-800 mb-2">Acceso Rápido</h3>
                                <ul class="space-y-2">
                                    <li>
                                        <a href="escanner.php" class="text-blue-600 hover:text-blue-800 text-sm flex items-center">
                                            <i class="fas fa-arrow-right mr-2"></i> Registrar entrada/salida
                                        </a>
                                    </li>
                                    <li>
                                        <a href="registros.php" class="text-blue-600 hover:text-blue-800 text-sm flex items-center">
                                            <i class="fas fa-arrow-right mr-2"></i> Ver registros del día
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            <div class="bg-green-50 p-4 rounded-lg">
                                <h3 class="font-medium text-green-800 mb-2">Peticiones Pendientes</h3>
                                <p class="text-green-600 text-sm">
                                    <a href="aceptar-peticiones.php" class="hover:underline">Ver peticiones pendientes de revisión</a>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </main>

            <!-- Footer -->
            <footer class="bg-white border-t mt-8">
                <div class="max-w-7xl mx-auto px-4 py-4 sm:px-6 lg:px-8">
                    <p class="text-center text-sm text-gray-500">
                        &copy; <?php echo date('Y'); ?> LOAUTECH - Sistema de Control de Acceso y Préstamos
                    </p>
                </div>
            </footer>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
    <script>
        // Toggle sidebar en móviles
        function toggleSidebar() {
            const sidebar = document.querySelector('.sidebar');
            const mainContent = document.querySelector('.main-content');
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('expanded');
        }
    </script>
</body>
</html>