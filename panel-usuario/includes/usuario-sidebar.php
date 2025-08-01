<!-- usuario-sidebar.php -->
<div class="w-64 bg-gray-800 text-white fixed top-0 left-0 bottom-0 z-10">
    <div class="p-4 border-b border-gray-700">
        <h1 class="text-xl font-bold">LOAUTECH</h1>
        <p class="text-sm text-gray-400">Panel de Usuario</p>
    </div>

    <nav class="p-4">
        <ul class="space-y-2">
            <li>
                <a href="panel-principal.php" class="flex items-center p-2 rounded hover:bg-gray-700 <?php if(basename($_SERVER['PHP_SELF']) == 'panel-principal.php') echo 'bg-gray-700'; ?>">
                    <i class="fas fa-tachometer-alt mr-3"></i>
                    Inicio
                </a>
            </li>
            <li>
                <a href="inventario.php" class="flex items-center p-2 rounded hover:bg-gray-700 <?php if(basename($_SERVER['PHP_SELF']) == 'inventario.php') echo 'bg-gray-700'; ?>">
                    <i class="fas fa-boxes mr-3"></i>
                    Inventario
                </a>
            </li>
            <li>
                <a href="registroelemento.php" class="flex items-center p-2 rounded hover:bg-gray-700 <?php if(basename($_SERVER['PHP_SELF']) == 'registroelemento.php') echo 'bg-gray-700'; ?>">
                    <i class="fas fa-plus-circle mr-3"></i>
                    Registrar Elemento
                </a>
            </li>
            <li>
                <a href="mis-ingresos.php" class="flex items-center p-2 rounded hover:bg-gray-700 <?php if(basename($_SERVER['PHP_SELF']) == 'mis-ingresos.php') echo 'bg-gray-700'; ?>">
                    <i class="fas fa-clipboard-list mr-3"></i>
                    Mis Ingresos
                </a>
            </li>
            <li>
                <a href="peticion.php" class="flex items-center p-2 rounded hover:bg-gray-700 <?php if(basename($_SERVER['PHP_SELF']) == 'peticion.php') echo 'bg-gray-700'; ?>">
                    <i class="fas fa-paper-plane mr-3"></i>
                    Mis Peticiones
                </a>
            </li>
            <li>
                <a href="perfil.php" class="flex items-center p-2 rounded hover:bg-gray-700 <?php if(basename($_SERVER['PHP_SELF']) == 'perfil.php') echo 'bg-gray-700'; ?>">
                    <i class="fas fa-user mr-3"></i>
                    Mi Perfil
                </a>
            </li>
            <li>
                <a href="../logout.php" class="flex items-center p-2 rounded hover:bg-gray-700 text-red-400 hover:text-red-300">
                    <i class="fas fa-sign-out-alt mr-3"></i>
                    Cerrar Sesión
                </a>
            </li>
        </ul>
    </nav>
</div>
