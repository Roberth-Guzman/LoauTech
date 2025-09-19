<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Usuario - LOAUTECH</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="flex h-screen">
        <?php include __DIR__ . '/includes/sidebar-usuario.php'; ?>
        <!-- Contenido principal -->
        <div class="flex-1 ml-64 overflow-auto">
            <!-- Header -->
            <div class="bg-white shadow-sm">
                <div class="max-w-7xl mx-auto px-4 py-4 sm:px-6 lg:px-8 flex justify-between items-center">
                    <h1 class="text-xl font-bold text-gray-900">
                        Bienvenido, <?php echo htmlspecialchars($_SESSION['usuario']['nombre'] ?? 'Usuario'); ?>
                    </h1>
                    <div class="flex items-center space-x-6">
                        <!-- Notifications Dropdown -->
                        <?php include __DIR__ . '/../layouts/_notification_dropdown.php'; ?>
                        
                        <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                            <?php echo ucfirst(htmlspecialchars($_SESSION['usuario']['rol'] ?? 'usuario')); ?>
                        </span>
                        
                        <!-- Cerrar Sesión -->
                        <a href="<?php echo BASE_URL; ?>/logout" class="text-gray-500 hover:text-red-600" title="Cerrar Sesión">
                            <i class="fas fa-sign-out-alt text-lg"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Contenido -->
            <main class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <!-- Tarjeta Inventario -->
                    <a href="<?php echo BASE_URL; ?>usuario/inventario" class="block">
                        <div class="bg-white rounded-lg shadow p-6 hover:shadow-md transition-shadow">
                            <div class="flex items-center">
                                <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
                                    <i class="fas fa-boxes text-2xl"></i>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-800">Inventario</h3>
                                    <p class="text-sm text-gray-600">Consulta los elementos disponibles</p>
                                </div>
                            </div>
                        </div>
                    </a>

                    <!-- Tarjeta Registrar Elemento -->
                    <a href="<?php echo BASE_URL; ?>usuario/registrarElemento" class="block">
                        <div class="bg-white rounded-lg shadow p-6 hover:shadow-md transition-shadow">
                            <div class="flex items-center">
                                <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
                                    <i class="fas fa-plus-circle text-2xl"></i>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-800">Registrar Elemento</h3>
                                    <p class="text-sm text-gray-600">Registra un nuevo elemento en el sistema</p>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Sección de Acciones Rápidas -->
                <div class="bg-white rounded-lg shadow p-6 mb-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-4">Acciones Rápidas</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <a href="<?php echo BASE_URL; ?>usuario/inventario" class="bg-blue-50 hover:bg-blue-100 rounded-lg p-4 text-center transition-colors">
                            <i class="fas fa-search text-blue-600 text-2xl mb-2"></i>
                            <p class="font-medium text-gray-700">Consultar Inventario</p>
                        </a>
                        <a href="<?php echo BASE_URL; ?>usuario/registrarElemento" class="bg-green-50 hover:bg-green-100 rounded-lg p-4 text-center transition-colors">
                            <i class="fas fa-plus-circle text-green-600 text-2xl mb-2"></i>
                            <p class="font-medium text-gray-700">Nuevo Elemento</p>
                        </a>
                        <a href="<?php echo BASE_URL; ?>usuario/misPeticiones" class="bg-yellow-50 hover:bg-yellow-100 rounded-lg p-4 text-center transition-colors">
                            <i class="fas fa-paper-plane text-yellow-600 text-2xl mb-2"></i>
                            <p class="font-medium text-gray-700">Mis Peticiones</p>
                        </a>
                        <a href="<?php echo BASE_URL; ?>usuario/perfil" class="bg-purple-50 hover:bg-purple-100 rounded-lg p-4 text-center transition-colors">
                            <i class="fas fa-user-edit text-purple-600 text-2xl mb-2"></i>
                            <p class="font-medium text-gray-700">Editar Perfil</p>
                        </a>
                    </div>
                </div>
            </main>

    <?php include __DIR__ . '/includes/footer-usuario.php'; ?>
    </div>
    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
</body>
</html>