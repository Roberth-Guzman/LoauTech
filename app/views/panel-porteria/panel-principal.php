<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($data['titulo']); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="flex h-screen overflow-hidden">
        
        <?php include __DIR__ . '/includes/sidebar-porteria.php'; ?>

        <!-- Contenido Principal -->
        <div class="flex-1 ml-64 flex flex-col overflow-y-auto main-content">
            <!-- Header -->
            <header class="bg-white shadow-sm">
                <div class="max-w-7xl mx-auto px-4 py-4 sm:px-6 lg:px-8 flex justify-between items-center">
                    <h1 class="text-2xl font-bold text-gray-900">
                        Panel de Portería
                    </h1>
                    <div class="flex items-center space-x-4">
                        <a href="<?php echo BASE_URL; ?>/logout" class="flex items-center text-gray-700 hover:text-gray-900">
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
                    <a href="<?php echo BASE_URL; ?>/porteria/escaner" class="block group">
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
                    <a href="<?php echo BASE_URL; ?>/porteria/registros" class="block group">
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
                    <a href="<?php echo BASE_URL; ?>/porteria/peticiones" class="block group">
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
                        <h2 class="text-xl font-bold text-gray-800 mb-4">Bienvenido, <?php echo htmlspecialchars(explode(' ', $data['nombre_usuario'])[0]); ?></h2>
                        <p class="text-gray-600 mb-4">
                            Desde este panel podrás gestionar los accesos al edificio, registrar movimientos y gestionar las peticiones de préstamo de elementos.
                        </p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
                            <div class="bg-blue-50 p-4 rounded-lg">
                                <h3 class="font-medium text-blue-800 mb-2">Acceso Rápido</h3>
                                <ul class="space-y-2">
                                    <li>
                                        <a href="<?php echo BASE_URL; ?>/porteria/escaner" class="text-blue-600 hover:text-blue-800 text-sm flex items-center">
                                            <i class="fas fa-arrow-right mr-2"></i> Registrar entrada/salida
                                        </a>
                                    </li>
                                    <li>
                                        <a href="<?php echo BASE_URL; ?>/porteria/registros" class="text-blue-600 hover:text-blue-800 text-sm flex items-center">
                                            <i class="fas fa-arrow-right mr-2"></i> Ver registros del día
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            <div class="bg-green-50 p-4 rounded-lg">
                                <h3 class="font-medium text-green-800 mb-2">Peticiones Pendientes</h3>
                                <p class="text-green-600 text-sm">
                                    <a href="<?php echo BASE_URL; ?>/porteria/peticiones" class="hover:underline">Ver peticiones pendientes de revisión</a>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </main>

            <?php include __DIR__ . '/includes/footer-porteria.php'; ?>
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