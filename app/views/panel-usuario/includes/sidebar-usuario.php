<!-- usuario-sidebar.php -->
<div class="w-64 bg-gray-800 text-white fixed top-0 left-0 bottom-0 z-10">
    <div class="p-4 border-b border-gray-700">
        <h1 class="text-xl font-bold">LOAUTECH</h1>
        <p class="text-sm text-gray-400">Panel de Usuario</p>
    </div>

    <nav class="p-4">
        <ul class="space-y-2">
            <li>
                <a href="<?php echo BASE_URL; ?>usuario/panelPrincipal" class="flex items-center p-2 rounded hover:bg-gray-700 <?php echo ($this->active_menu ?? '') === 'inicio' ? 'bg-gray-700' : ''; ?>">
                    <i class="fas fa-tachometer-alt mr-3"></i>
                    Inicio
                </a>
            </li>
            <li>
                <a href="<?php echo BASE_URL; ?>usuario/inventario" class="flex items-center p-2 rounded hover:bg-gray-700 <?php echo ($this->active_menu ?? '') === 'inventario' ? 'bg-gray-700' : ''; ?>">
                    <i class="fas fa-boxes mr-3"></i>
                    Inventario
                </a>
            </li>
            <li>
                <a href="<?php echo BASE_URL; ?>usuario/registrarElemento" class="flex items-center p-2 rounded hover:bg-gray-700 <?php echo ($this->active_menu ?? '') === 'registrar_elemento' ? 'bg-gray-700' : ''; ?>">
                    <i class="fas fa-plus-circle mr-3"></i>
                    Registrar Elemento
                </a>
            </li>
            <li>
                <a href="<?php echo BASE_URL; ?>usuario/misIngresos" class="flex items-center p-2 rounded hover:bg-gray-700 <?php echo ($this->active_menu ?? '') === 'mis_ingresos' ? 'bg-gray-700' : ''; ?>">
                    <i class="fas fa-clipboard-list mr-3"></i>
                    Mis Ingresos
                </a>
            </li>
            <li>
                <a href="<?php echo BASE_URL; ?>usuario/misPeticiones" class="flex items-center p-2 rounded hover:bg-gray-700 <?php echo ($this->active_menu ?? '') === 'mis_peticiones' ? 'bg-gray-700' : ''; ?>">
                    <i class="fas fa-paper-plane mr-3"></i>
                    Mis Peticiones
                </a>
            </li>
            <li>
                <a href="<?php echo BASE_URL; ?>usuario/perfil" class="flex items-center p-2 rounded hover:bg-gray-700 <?php echo ($this->active_menu ?? '') === 'perfil' ? 'bg-gray-700' : ''; ?>">
                    <i class="fas fa-user mr-3"></i>
                    Mi Perfil
                </a>
            </li>
            <li>
                <a href="<?php echo BASE_URL; ?>logout" class="flex items-center p-2 rounded hover:bg-gray-700 text-red-400 hover:text-red-300">
                    <i class="fas fa-sign-out-alt mr-3"></i>
                    Cerrar Sesión
                </a>
            </li>
        </ul>
    </nav>
</div>