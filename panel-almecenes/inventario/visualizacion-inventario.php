<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visualización de Inventario</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">
    <!-- Navbar -->
    <nav class="bg-gray-800 text-white shadow-lg">
        <div class="container mx-auto px-4 py-3 flex justify-between items-center">
            <div class="flex items-center space-x-2">
                <i class="fas fa-boxes"></i>
                <span class="font-bold">Gestión de Inventario</span>
            </div>
            <div class="flex items-center space-x-4">
                <div class="relative">
                    <button class="relative p-2 rounded-full hover:bg-gray-700">
                        <i class="fas fa-bell"></i>
                        <span class="absolute -top-1 -right-1 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs">2</span>
                    </button>
                </div>
                <div class="relative">
                    <button class="flex items-center space-x-2 hover:bg-gray-700 px-3 py-2 rounded">
                        <i class="fas fa-user-circle"></i>
                        <span>Usuario</span>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <div class="container mx-auto px-4 py-6">
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="bg-gradient-to-r from-blue-600 to-purple-700 text-white px-6 py-4">
                <div class="flex items-center space-x-2">
                    <i class="fas fa-images"></i>
                    <h2 class="text-xl font-bold">Visualización de Inventario</h2>
                </div>
            </div>
            <div class="p-6">
                <!-- Tabs -->
                <div class="mb-6">
                    <div class="border-b border-gray-200">
                        <nav class="flex space-x-2">
                            <button onclick="window.location.href='panel-solicitudes.php'" class="py-2 px-4 border-b-2 font-medium text-sm border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                                Solicitudes Pendientes
                                <span class="bg-yellow-500 text-white rounded-full px-2 py-0.5 text-xs ml-1">2</span>
                            </button>
                            <button onclick="window.location.href='panel-inventario.php'" class="py-2 px-4 border-b-2 font-medium text-sm border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                                Gestión de Inventario
                            </button>
                            <button onclick="window.location.href='visualizacion-inventario.php'" class="py-2 px-4 border-b-2 font-medium text-sm border-blue-500 text-blue-600">
                                Visualización de Inventario
                            </button>
                        </nav>
                    </div>
                </div>

                <!-- Filtros -->
                <div class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="categoryFilter" class="block text-sm font-medium text-gray-700 mb-1">Categoría</label>
                        <select id="categoryFilter" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Todas las categorías</option>
                            <option value="computers">Computadores</option>
                            <option value="audio_video">Audio/Video</option>
                            <option value="printers">Impresoras</option>
                            <option value="furniture">Mobiliario</option>
                            <option value="other">Otros</option>
                        </select>
                    </div>
                    <div>
                        <label for="statusFilter" class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                        <select id="statusFilter" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Todos los estados</option>
                            <option value="available">Disponible</option>
                            <option value="maintenance">Mantenimiento</option>
                            <option value="out_of_order">Fuera de servicio</option>
                        </select>
                    </div>
                    <div>
                        <label for="searchFilter" class="block text-sm font-medium text-gray-700 mb-1">Buscar</label>
                        <input type="text" id="searchFilter" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Buscar por nombre o código...">
                    </div>
                </div>

                <!-- Grid de elementos -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    <!-- Ejemplo de elemento -->
                    <div class="bg-white rounded-lg border border-gray-200 shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300">
                        <div class="bg-gray-100 h-48 flex items-center justify-center">
                            <img src="https://via.placeholder.com/300x200?text=Laptop+Dell+XPS" alt="Laptop Dell XPS" class="max-h-full max-w-full object-contain">
                        </div>
                        <div class="p-4">
                            <div class="flex justify-between items-start">
                                <h3 class="text-lg font-semibold text-gray-800">Laptop Dell XPS</h3>
                                <span class="bg-green-500 text-white px-2 py-1 rounded-full text-xs">Disponible</span>
                            </div>
                            <p class="text-sm text-gray-600 mt-1">Código: LAP001</p>
                            <p class="text-sm text-gray-600">Categoría: Computadores</p>
                            <p class="text-sm text-gray-600">Stock: 5</p>
                            
                            <div class="mt-3 pt-3 border-t border-gray-200">
                                <h4 class="text-sm font-medium text-gray-700">Descripción:</h4>
                                <p class="text-sm text-gray-600 mt-1">Laptop de alto rendimiento para tareas de oficina, 16GB RAM, 512GB SSD, pantalla 15.6"</p>
                            </div>
                            
                            <div class="mt-4 flex justify-between items-center">
                                <span class="text-xs text-gray-500">Última actualización: 15/06/2023</span>
                                <button onclick="openItemDetail('LAP001')" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                    Ver detalles <i class="fas fa-chevron-right ml-1"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Puedes añadir más elementos aquí -->
                    <div class="bg-white rounded-lg border border-gray-200 shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300">
                        <div class="bg-gray-100 h-48 flex items-center justify-center">
                            <img src="https://via.placeholder.com/300x200?text=Proyector" alt="Proyector Epson" class="max-h-full max-w-full object-contain">
                        </div>
                        <div class="p-4">
                            <div class="flex justify-between items-start">
                                <h3 class="text-lg font-semibold text-gray-800">Proyector Epson</h3>
                                <span class="bg-yellow-500 text-white px-2 py-1 rounded-full text-xs">Mantenimiento</span>
                            </div>
                            <p class="text-sm text-gray-600 mt-1">Código: PRO002</p>
                            <p class="text-sm text-gray-600">Categoría: Audio/Video</p>
                            <p class="text-sm text-gray-600">Stock: 2</p>
                            
                            <div class="mt-3 pt-3 border-t border-gray-200">
                                <h4 class="text-sm font-medium text-gray-700">Descripción:</h4>
                                <p class="text-sm text-gray-600 mt-1">Proyector Full HD 1080p, 3,500 lúmenes, entrada HDMI y VGA</p>
                            </div>
                            
                            <div class="mt-4 flex justify-between items-center">
                                <span class="text-xs text-gray-500">Última actualización: 10/06/2023</span>
                                <button onclick="openItemDetail('PRO002')" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                    Ver detalles <i class="fas fa-chevron-right ml-1"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Detalles del Elemento -->
    <div id="itemDetailModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4" id="itemDetailModalTitle">
                                Detalles del Elemento
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="bg-gray-100 rounded-lg flex items-center justify-center h-64">
                                    <img id="detailItemImage" src="" alt="Imagen del elemento" class="max-h-full max-w-full object-contain">
                                </div>
                                <div>
                                    <h4 id="detailItemName" class="text-xl font-bold text-gray-800"></h4>
                                    <p id="detailItemCode" class="text-sm text-gray-600 mt-1"></p>
                                    <p id="detailItemCategory" class="text-sm text-gray-600"></p>
                                    
                                    <div class="mt-4 grid grid-cols-2 gap-2">
                                        <div>
                                            <p class="text-sm font-medium text-gray-700">Stock</p>
                                            <p id="detailItemStock" class="text-sm text-gray-600"></p>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-700">Estado</p>
                                            <p id="detailItemStatus" class="text-sm text-gray-600"></p>
                                        </div>
                                    </div>
                                    
                                    <div class="mt-4">
                                        <p class="text-sm font-medium text-gray-700">Descripción</p>
                                        <p id="detailItemDescription" class="text-sm text-gray-600 mt-1"></p>
                                    </div>
                                    
                                    <div class="mt-4">
                                        <p class="text-sm font-medium text-gray-700">Ubicación</p>
                                        <p id="detailItemLocation" class="text-sm text-gray-600 mt-1">Estante A-3</p>
                                    </div>
                                    
                                    <div class="mt-4">
                                        <p class="text-sm font-medium text-gray-700">Última actualización</p>
                                        <p id="detailItemLastUpdate" class="text-sm text-gray-600 mt-1">15/06/2023</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" onclick="closeModal('itemDetailModal')" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Funciones para manejar modales
        function openModal(modalId) {
            document.getElementById(modalId).classList.remove('hidden');
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
        }

        // Cerrar modal al hacer clic fuera del contenido
        window.onclick = function(event) {
            if (event.target.classList.contains('fixed')) {
                const modals = document.querySelectorAll('.fixed.inset-0.z-50');
                modals.forEach(modal => {
                    if (!modal.classList.contains('hidden')) {
                        modal.classList.add('hidden');
                    }
                });
            }
        }

        // Cerrar modal con la tecla Escape
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                const modals = document.querySelectorAll('.fixed.inset-0.z-50');
                modals.forEach(modal => {
                    if (!modal.classList.contains('hidden')) {
                        modal.classList.add('hidden');
                    }
                });
            }
        });

        // Función para abrir detalles del elemento
        function openItemDetail(itemId) {
            // Aquí podrías cargar los datos del elemento con AJAX
            // Ejemplo simplificado:
            if (itemId === 'LAP001') {
                document.getElementById('detailItemName').textContent = 'Laptop Dell XPS';
                document.getElementById('detailItemCode').textContent = 'Código: LAP001';
                document.getElementById('detailItemCategory').textContent = 'Categoría: Computadores';
                document.getElementById('detailItemStock').textContent = '5 unidades';
                document.getElementById('detailItemStatus').innerHTML = '<span class="bg-green-500 text-white px-2 py-1 rounded-full text-xs">Disponible</span>';
                document.getElementById('detailItemDescription').textContent = 'Laptop de alto rendimiento para tareas de oficina, 16GB RAM, 512GB SSD, pantalla 15.6"';
                document.getElementById('detailItemImage').src = 'https://via.placeholder.com/300x200?text=Laptop+Dell+XPS';
            } else if (itemId === 'PRO002') {
                document.getElementById('detailItemName').textContent = 'Proyector Epson';
                document.getElementById('detailItemCode').textContent = 'Código: PRO002';
                document.getElementById('detailItemCategory').textContent = 'Categoría: Audio/Video';
                document.getElementById('detailItemStock').textContent = '2 unidades';
                document.getElementById('detailItemStatus').innerHTML = '<span class="bg-yellow-500 text-white px-2 py-1 rounded-full text-xs">Mantenimiento</span>';
                document.getElementById('detailItemDescription').textContent = 'Proyector Full HD 1080p, 3,500 lúmenes, entrada HDMI y VGA';
                document.getElementById('detailItemImage').src = 'https://via.placeholder.com/300x200?text=Proyector';
            }
            
            openModal('itemDetailModal');
        }
    </script>
</body>

</html>