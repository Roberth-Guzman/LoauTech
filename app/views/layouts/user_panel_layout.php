<!DOCTYPE html>
<html lang="es" data-user-id="<?= $_SESSION['user_id'] ?? '' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $data['titulo'] ?? 'Panel de Usuario - LOAUTECH' ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="<?= BASE_URL ?>/public/js/notificaciones.js" defer></script>
</head>
<body class="bg-gray-100">
    <div class="flex h-screen">
        <?php include __DIR__ . '/../panel-usuario/includes/sidebar-usuario.php'; ?>
        
        <!-- Contenido principal -->
        <div class="flex-1 ml-64 overflow-auto">
            <!-- Header -->
            <div class="bg-white shadow-sm">
                <div class="max-w-7xl mx-auto px-4 py-4 sm:px-6 lg:px-8 flex justify-between items-center">
                    <h1 class="text-xl font-bold text-gray-900">
                        <?= $data['titulo'] ?? 'Panel de Usuario' ?>
                    </h1>
                    <div class="flex items-center space-x-6">
                        <!-- Notifications Dropdown -->
                        <?php include __DIR__ . '/_notification_dropdown.php'; ?>
                        
                        <!-- User Menu -->
                        <div class="relative">
                            <button type="button" class="flex rounded-full bg-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-gray-800" id="user-menu-button" aria-expanded="false" aria-haspopup="true">
                                <span class="sr-only">Abrir menú de usuario</span>
                                <div class="h-8 w-8 rounded-full bg-gray-500 flex items-center justify-center text-white">
                                    <?= strtoupper(substr($_SESSION['nombre'] ?? 'U', 0, 1)) ?>
                                </div>
                            </button>
                            
                            <!-- Dropdown menu -->
                            <div class="absolute right-0 z-10 mt-2 w-48 origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none hidden" role="menu" aria-orientation="vertical" aria-labelledby="user-menu-button" tabindex="-1">
                                <a href="<?= BASE_URL ?>/usuario/perfil" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem" tabindex="-1" id="user-menu-item-0">
                                    <i class="fas fa-user mr-2"></i> Mi perfil
                                </a>
                                <a href="<?= BASE_URL ?>/usuario/notificaciones" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem" tabindex="-1" id="user-menu-item-1">
                                    <i class="fas fa-bell mr-2"></i> Notificaciones
                                </a>
                                <a href="<?= BASE_URL ?>/logout" class="block px-4 py-2 text-sm text-red-600 hover:bg-gray-100" role="menuitem" tabindex="-1" id="user-menu-item-2">
                                    <i class="fas fa-sign-out-alt mr-2"></i> Cerrar sesión
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Page Content -->
            <main>
                <?php if (isset($content)) { echo $content; } ?>
            </main>
        </div>
    </div>

    <script>
        // Toggle user dropdown
        const userMenuButton = document.getElementById('user-menu-button');
        const userMenu = document.querySelector('#user-menu-button + div');
        
        userMenuButton.addEventListener('click', () => {
            userMenu.classList.toggle('hidden');
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (!userMenuButton.contains(e.target) && !userMenu.contains(e.target)) {
                userMenu.classList.add('hidden');
            }
        });
        
        // Toggle notification dropdown
        const notificationButton = document.getElementById('notification-button');
        const notificationDropdown = document.getElementById('notification-dropdown');
        
        if (notificationButton && notificationDropdown) {
            notificationButton.addEventListener('click', (e) => {
                e.stopPropagation();
                notificationDropdown.classList.toggle('hidden');
            });
            
            // Close dropdown when clicking outside
            document.addEventListener('click', (e) => {
                if (!notificationButton.contains(e.target) && !notificationDropdown.contains(e.target)) {
                    notificationDropdown.classList.add('hidden');
                }
            });
        }
    </script>
</body>
</html>