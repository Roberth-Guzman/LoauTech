   <!-- Sidebar - Usando fixed para posición fija -->
        <div class="w-64 bg-gray-800 text-white fixed top-0 left-0 bottom-0 z-10">
            <div class="p-4 border-b border-gray-700">
                <h1 class="text-xl font-bold">LOAUTECH</h1>
                <p class="text-sm text-gray-400">Panel de Administración</p>
            </div>
            
            <nav class="p-4">
                <ul class="space-y-2">
                    <li>
                        <a href="panel-principal.php" class="flex items-center p-2 rounded hover:bg-gray-700">
                            <i class="fas fa-tachometer-alt mr-3"></i>
                            Dashboard
                        </a>
                    </li>
                    <li>
                        <a href="personas/consultar.php" class="flex items-center p-2 rounded hover:bg-gray-700">
                            <i class="fas fa-users mr-3"></i>
                            Usuarios
                        </a>
                    </li>
                    <li>
                        <a href="elementos/consultar.php" class="flex items-center p-2 rounded hover:bg-gray-700">
                            <i class="fas fa-boxes mr-3"></i>
                            Elementos
                        </a>
                    </li>
                    <li>
                        <a href="almacenes/listar_autorizaciones.php" class="flex items-center p-2 rounded hover:bg-gray-700">
                            <i class="fas fa-warehouse mr-3"></i>
                            Almacenes
                        </a>
                    </li>
                    <li>
                        <a href="database/exportar_db.php" class="flex items-center p-2 rounded hover:bg-gray-700">
                            <i class="fas fa-database mr-3"></i>
                            Base de Datos
                        </a>
                    </li>
                    <li class="pt-4 mt-4 border-t border-gray-700">
                        <a href="../logout.php" class="flex items-center p-2 rounded hover:bg-red-600">
                            <i class="fas fa-sign-out-alt mr-3"></i>
                            Cerrar Sesión
                        </a>
                    </li>
                </ul>
            </nav>
        </div>