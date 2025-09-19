<div class="w-64 bg-gray-800 text-white fixed top-0 left-0 bottom-0 z-10 shadow-lg sidebar">
    <div class="p-4 border-b border-gray-700">
        <h1 class="text-xl font-bold text-white">LOAUTECH</h1>
        <p class="text-sm text-gray-300">Panel de Portería</p>
    </div>

    <!-- Menú de Navegación -->
    <nav class="p-4">
        <ul class="space-y-2">
            <li>
                <a href="<?php echo BASE_URL; ?>/porteria"
                    class="flex items-center p-3 rounded-lg text-gray-300 hover:bg-gray-700 hover:text-white transition-colors">
                    <i class="fas fa-tachometer-alt w-6 text-center mr-3"></i>
                    <span>Inicio</span>
                </a>
            </li>
            <li>
                <a href="<?php echo BASE_URL; ?>/porteria/escaner"
                    class="flex items-center p-3 rounded-lg text-gray-300 hover:bg-gray-700 hover:text-white transition-colors">
                    <i class="fas fa-qrcode w-6 text-center mr-3"></i>
                    <span>Escanear Carnet</span>
                </a>
            </li>
            <li>
                <a href="<?php echo BASE_URL; ?>/porteria/registros"
                    class="flex items-center p-3 rounded-lg text-gray-300 hover:bg-gray-700 hover:text-white transition-colors">
                    <i class="fas fa-clipboard-list w-6 text-center mr-3"></i>
                    <span>Registros</span>
                </a>
            </li>
            <li>
                <a href="<?php echo BASE_URL; ?>/porteria/peticiones"
                    class="flex items-center p-3 rounded-lg text-gray-300 hover:bg-gray-700 hover:text-white transition-colors">
                    <i class="fas fa-clipboard-check w-6 text-center mr-3"></i>
                    <span>Peticiones</span>
                </a>
            </li>
            <li>
                <a href="<?php echo BASE_URL; ?>/porteria/devoluciones"
                    class="flex items-center p-3 rounded-lg text-gray-300 hover:bg-gray-700 hover:text-white transition-colors">
                    <i class="fas fa-undo w-6 text-center mr-3"></i>
                    <span>Registro de Devoluciones</span>
                </a>
            </li>

            <li>
                <a href="<?php echo BASE_URL; ?>/porteria/historialPrestamos"
                    class="flex items-center p-3 rounded-lg text-gray-300 hover:bg-gray-700 hover:text-white transition-colors">
                    <i class="fas fa-history w-6 text-center mr-3"></i>
                    <span>Historial de Préstamos</span>
                </a>
            </li>
            <li>
                <a href="<?php echo BASE_URL; ?>/porteria/informeHorario"
                    class="flex items-center p-3 rounded-lg text-gray-300 hover:bg-gray-700 hover:text-white transition-colors">
                    <i class="fas fa-clock w-6 text-center mr-3"></i>
                    <span>Informe Horario</span>
                </a>
            </li>

    </nav>

    <!-- Perfil en la parte inferior -->
    <div class="absolute bottom-0 w-full p-4 border-t border-gray-700">
        <a href="<?php echo BASE_URL; ?>/porteria/perfil"
            class="flex items-center p-2 rounded-lg hover:bg-gray-700 transition-colors">
            <div class="w-10 h-10 rounded-full bg-gray-600 flex items-center justify-center mr-3">
                <i class="fas fa-user text-white"></i>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-white truncate">
                    <?php echo htmlspecialchars($data['nombre_usuario'] ?? 'Usuario'); ?>
                </p>
                <p class="text-xs text-gray-400 capitalize">Portería</p>
            </div>
            <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
        </a>
    </div>
</div>