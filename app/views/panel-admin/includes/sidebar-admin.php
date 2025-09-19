<!-- Sidebar Unificado con Nuevo Esquema de Color y Enlaces de Superadmin -->
<div class="hidden md:flex md:flex-shrink-0">
    <div class="flex flex-col w-64 bg-gray-800">
        <div class="flex items-center h-16 px-4 bg-gray-900">
            <img src=" <?= BASE_URL ?> /public/img/logo_loautech_white.png" alt="LoauTech" class="h-8 w-auto mr-2">

            <h1 class="text-white text-xl font-bold">LOAUTECH</h1>
        </div>
        <div class="flex flex-col flex-grow px-4 py-4 overflow-y-auto">
            <nav class="flex-1 space-y-2">
                <p class="text-xs text-gray-400 uppercase tracking-wider px-4">Principal</p>
                <a href="<?= BASE_URL ?>/admin/index"
                    class="flex items-center px-4 py-2 text-white bg-blue-900 rounded-lg">
                    <i class="fas fa-tachometer-alt w-5 mr-3"></i>
                    Dashboard
                </a>
                <a href="<?= BASE_URL ?>/admin/estadisticas"
                    class="flex items-center px-4 py-2 text-gray-300 hover:bg-gray-700 rounded-lg">
                    <i class="fas fa-chart-bar w-5 mr-3"></i>
                    Estadísticas
                </a>
                <a href="<?= BASE_URL ?>/admin/usuarios"
                    class="flex items-center px-4 py-2 text-gray-300 hover:bg-gray-700 rounded-lg">
                    <i class="fas fa-users w-5 mr-3"></i>
                    Usuarios
                </a>
                <a href="<?= BASE_URL ?>/admin/elementos"
                    class="flex items-center px-4 py-2 text-gray-300 hover:bg-gray-700 rounded-lg">
                    <i class="fas fa-boxes w-5 mr-3"></i>
                    Elementos
                </a>
                <a href="<?= BASE_URL ?>/admin/almacenes"
                    class="flex items-center px-4 py-2 text-gray-300 hover:bg-gray-700 rounded-lg">
                    <i class="fas fa-warehouse w-5 mr-3"></i>
                    Almacenes
                </a>

                <a href="<?= BASE_URL ?>/admin/roles_permisos"
                    class="flex items-center px-4 py-2 text-gray-300 hover:bg-gray-700 rounded-lg">
                    <i class="fas fa-user-shield w-5 mr-3"></i>
                    Roles y Permisos
                </a>
                <a href="<?= BASE_URL ?>/admin/gestionarAdmin"
                    class="flex items-center px-4 py-2 text-gray-300 hover:bg-gray-700 rounded-lg">
                    <i class="fas fa-user-cog w-5 mr-3"></i>
                    Gestionar Administradores
                </a>
                <a href="<?= BASE_URL ?>/admin/resetPassword"
                    class="flex items-center px-4 py-2 text-gray-300 hover:bg-gray-700 rounded-lg">
                    <i class="fas fa-key w-5 mr-3"></i>
                    Restablecer Contraseñas
                </a>
                <a href="<?= BASE_URL ?>/admin/backup"
                    class="flex items-center px-4 py-2 text-gray-300 hover:bg-gray-700 rounded-lg">
                    <i class="fas fa-database w-5 mr-3"></i>
                    Base de Datos
                </a>
            </nav>

        </div>

        <div class="p-4 border-t border-gray-700">
            <a href="<?= BASE_URL ?>/admin/perfil"
                class="flex items-center px-4 py-2 text-gray-300 hover:bg-gray-700 rounded-lg">
                <i class="fas fa-user-circle w-5 mr-3"></i>
                Ver Perfil
            </a>
            <a href="<?= BASE_URL ?>/logout"
                class="flex items-center justify-center mt-4 px-4 py-2 text-white bg-red-600 hover:bg-red-700 rounded-lg">
                <i class="fas fa-sign-out-alt w-5 mr-2"></i>
                Cerrar Sesión
            </a>
        </div>
    </div>
</div>