<!-- Navbar Almacen -->
<nav class="bg-gray-800 text-white shadow-lg">
    <div class="container mx-auto px-4 py-3 flex justify-between items-center">
        <div class="flex items-center space-x-2">
            <a href="<?= BASE_URL ?>/almacen/almacen/panelPrincipal" class="flex items-center space-x-2">
                <i class="fas fa-warehouse"></i>
                <span class="font-bold">Panel Almacenes</span>
            </a>
        </div>
       <div class="hidden md:flex space-x-6">
            <?php $active_menu = $data['active_menu'] ?? ''; ?>

            <a href="<?= BASE_URL ?>/almacen/almacen/panelPrincipal" 
               class="px-3 py-2 rounded-md text-white transition-colors 
                      <?= $active_menu == 'solicitudes' ? 'bg-blue-700' : 'hover:bg-gray-700' ?>">
                <i class="fas fa-clipboard-list mr-1"></i> Solicitudes
            </a>
            <a href="<?= BASE_URL ?>/almacen/almacen/inventario" 
               class="px-3 py-2 rounded-md text-white transition-colors 
                      <?= $active_menu == 'inventario' ? 'bg-blue-700' : 'hover:bg-gray-700' ?>">
                <i class="fas fa-boxes mr-1"></i> Inventario
            </a>
            <a href="<?= BASE_URL ?>/almacen/almacen/visualizacion" 
               class="px-3 py-2 rounded-md text-white transition-colors
                      <?= $active_menu == 'visualizacion' ? 'bg-blue-700' : 'hover:bg-gray-700' ?>">
                <i class="fas fa-eye mr-1"></i> Visualización
            </a>
            <a href="<?= BASE_URL ?>/almacen/historial" 
               class="px-3 py-2 rounded-md text-white transition-colors
                      <?= $active_menu == 'historial' ? 'bg-blue-700' : 'hover:bg-gray-700' ?>">
                <i class="fas fa-history mr-1"></i> Historial
            </a>
        </div>
        <!-- User Profile Dropdown -->
        <div class="relative ml-4">
            <button id="user-menu-button" class="flex items-center space-x-2 text-white hover:bg-gray-700 px-3 py-2 rounded-md">
                <i class="fas fa-user-circle text-xl"></i>
                <span class="hidden md:inline"><?= htmlspecialchars($_SESSION['user_name'] ?? 'Usuario') ?></span>
                <i class="fas fa-chevron-down text-xs ml-1"></i>
            </button>
            <div id="user-dropdown" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
                <a href="<?= BASE_URL ?>/almacen/almacen/perfil" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                    <i class="fas fa-user-circle mr-2"></i> Mi Perfil
                </a>
                <div class="border-t border-gray-100 my-1"></div>
                <a href="<?= BASE_URL ?>/logout" class="block px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                    <i class="fas fa-sign-out-alt mr-2"></i> Cerrar Sesión
                </a>
            </div>
        </div>
    </div>
</nav>

<!-- Script para el dropdown del perfil de usuario -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const userMenuButton = document.getElementById('user-menu-button');
        const userDropdown = document.getElementById('user-dropdown');

        if (userMenuButton) {
            userMenuButton.addEventListener('click', function(event) {
                userDropdown.classList.toggle('hidden');
                event.stopPropagation();
            });
        }

        document.addEventListener('click', function(event) {
            if (userDropdown && !userDropdown.classList.contains('hidden') && !userMenuButton.contains(event.target)) {
                userDropdown.classList.add('hidden');
            }
        });
    });
</script>