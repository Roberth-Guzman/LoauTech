<nav class="bg-white shadow-lg">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <a href="/" class="text-2xl font-bold text-primary">
                        <i class="fas fa-boxes mr-2"></i>Mi Aplicación
                    </a>
                </div>
            </div>
            <div class="flex items-center space-x-4">
                <a href="/" class="text-gray-700 hover:text-primary px-3 py-2 rounded-md text-sm font-medium">Inicio</a>
                <?php if (!isset($_SESSION['usuario_id'])): ?>
                    <a href="/login" class="text-gray-700 hover:text-primary px-3 py-2 rounded-md text-sm font-medium">Iniciar Sesión</a>
                    <a href="/registro" class="bg-primary text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-secondary">Registrarse</a>
                <?php else: ?>
                    <a href="/dashboard" class="text-gray-700 hover:text-primary px-3 py-2 rounded-md text-sm font-medium">Panel</a>
                    <a href="/logout" class="text-gray-700 hover:text-primary px-3 py-2 rounded-md text-sm font-medium">Cerrar Sesión</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>
