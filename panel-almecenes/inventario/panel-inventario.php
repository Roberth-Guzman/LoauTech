<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Inventario</title>
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
                    <i class="fas fa-warehouse"></i>
                    <h2 class="text-xl font-bold">Gestión de Inventario</h2>
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
                            <button onclick="window.location.href='panel-inventario.php'" class="py-2 px-4 border-b-2 font-medium text-sm border-blue-500 text-blue-600">
                                Gestión de Inventario
                            </button>
                            <button onclick="window.location.href='visualizacion-inventario.php'" class="py-2 px-4 border-b-2 font-medium text-sm border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                                Visualización de Inventario
                            </button>
                        </nav>
                    </div>
                </div>

                <!-- Contenido de Gestión de Inventario -->
                <div id="inventory-management-content" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <!-- Botón de Agregar con ancho optimizado -->
                        <button onclick="openModal('addItemModal')"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-lg flex items-center justify-center space-x-2 shadow-md hover:shadow-lg transition-all duration-200 font-medium w-[120px]">
                            <i class="fas fa-plus text-sm"></i>
                            <span class="text-sm">Agregar</span>
                        </button>

                        <!-- Campo de búsqueda -->
                        <input type="text" class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 w-full" placeholder="Buscar...">
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-800 text-white">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Código</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Nombre</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Categoría</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Stock</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Estado</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">LAP001</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">Laptop Dell XPS</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">Computadores</td>
                                    <td class="px-6 py-4 whitespace-nowrap"><span class="bg-green-500 text-white px-2 py-1 rounded-full text-xs">5</span></td>
                                    <td class="px-6 py-4 whitespace-nowrap"><span class="bg-green-500 text-white px-2 py-1 rounded-full text-xs">Disponible</span></td>
                                    <td class="px-6 py-4 whitespace-nowrap space-x-1">
                                        <button onclick="openModal('editItemModal', 'LAP001')" class="text-blue-600 hover:text-blue-900">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button onclick="openModal('deleteItemModal', 'LAP001')" class="text-red-600 hover:text-red-900">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODALES INVENTARIO -->
    <!-- Modal Agregar Elemento -->
    <div id="addItemModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                                <i class="fas fa-plus-circle text-blue-500 mr-2"></i>
                                Agregar Nuevo Elemento
                            </h3>
                            <form id="addItemForm" class="space-y-4">
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label for="itemCode" class="block text-sm font-medium text-gray-700">Código</label>
                                        <input type="text" id="itemCode" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                    </div>
                                    <div>
                                        <label for="itemName" class="block text-sm font-medium text-gray-700">Nombre</label>
                                        <input type="text" id="itemName" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                    </div>
                                </div>

                                <div>
                                    <label for="itemCategory" class="block text-sm font-medium text-gray-700">Categoría</label>
                                    <select id="itemCategory" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                        <option value="computers">Computadores</option>
                                        <option value="audio_video">Audio/Video</option>
                                        <option value="printers">Impresoras</option>
                                        <option value="furniture">Mobiliario</option>
                                        <option value="other">Otros</option>
                                    </select>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label for="itemStock" class="block text-sm font-medium text-gray-700">Stock</label>
                                        <input type="number" id="itemStock" min="0" value="1" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                    </div>
                                    <div>
                                        <label for="itemStatus" class="block text-sm font-medium text-gray-700">Estado</label>
                                        <select id="itemStatus" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                            <option value="available">Disponible</option>
                                            <option value="maintenance">Mantenimiento</option>
                                        </select>
                                    </div>
                                </div>

                                <div>
                                    <label for="itemDescription" class="block text-sm font-medium text-gray-700">Descripción</label>
                                    <textarea id="itemDescription" rows="2" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"></textarea>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" onclick="addInventoryItem()" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                        <i class="fas fa-save mr-2"></i> Guardar
                    </button>
                    <button type="button" onclick="closeModal('addItemModal')" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Editar Elemento -->
    <div id="editItemModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100 sm:mx-0 sm:h-10 sm:w-10">
                            <i class="fas fa-edit text-yellow-600"></i>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="editItemModalTitle">
                                Editar Elemento
                            </h3>
                            <div class="mt-2">
                                <form id="editItemForm">
                                    <input type="hidden" id="editItemId">
                                    <div class="grid grid-cols-1 gap-4">
                                        <div>
                                            <label for="editItemCode" class="block text-sm font-medium text-gray-700">Código</label>
                                            <input type="text" id="editItemCode" readonly class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm bg-gray-100">
                                        </div>
                                        <div>
                                            <label for="editItemName" class="block text-sm font-medium text-gray-700">Nombre <span class="text-red-500">*</span></label>
                                            <input type="text" id="editItemName" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        </div>
                                        <div>
                                            <label for="editItemCategory" class="block text-sm font-medium text-gray-700">Categoría</label>
                                            <select id="editItemCategory" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                                <option value="computers">Computadores</option>
                                                <option value="audio_video">Audio/Video</option>
                                                <option value="printers">Impresoras</option>
                                                <option value="furniture">Mobiliario</option>
                                                <option value="tools">Herramientas</option>
                                                <option value="other">Otros</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label for="editItemStock" class="block text-sm font-medium text-gray-700">Stock</label>
                                            <input type="number" id="editItemStock" min="0" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        </div>
                                        <div>
                                            <label for="editItemStatus" class="block text-sm font-medium text-gray-700">Estado</label>
                                            <select id="editItemStatus" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                                <option value="available">Disponible</option>
                                                <option value="maintenance">En mantenimiento</option>
                                                <option value="out_of_order">Fuera de servicio</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label for="editItemDescription" class="block text-sm font-medium text-gray-700">Descripción</label>
                                            <textarea id="editItemDescription" rows="3" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"></textarea>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" onclick="updateInventoryItem()" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-yellow-600 text-base font-medium text-white hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Actualizar Elemento
                    </button>
                    <button type="button" onclick="closeModal('editItemModal')" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Eliminar Elemento -->
    <div id="deleteItemModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <i class="fas fa-exclamation text-red-600"></i>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="deleteItemModalTitle">
                                Eliminar Elemento
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    ¿Estás seguro que deseas eliminar este elemento del inventario? Esta acción no se puede deshacer.
                                </p>
                                <input type="hidden" id="deleteItemId">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" onclick="deleteInventoryItem(document.getElementById('deleteItemId').value)" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Eliminar
                    </button>
                    <button type="button" onclick="closeModal('deleteItemModal')" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="script-inventario.js"></script>
</body>

</html>