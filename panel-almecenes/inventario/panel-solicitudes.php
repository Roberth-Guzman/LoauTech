<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header('Location: ../../../login.php'); 
    exit();
}

if ($_SESSION['usuario']['rol'] !== 'almacenes') {
    header('Location: ../../../login.php?error=acceso_no_autorizado');
    exit();
}

require_once $_SERVER['DOCUMENT_ROOT'] . '/loautech-main/conexion.php';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Inventario - Solicitudes</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">
    <!-- Navbar (igual que en panel-inventario.php) -->
    <!-- ... -->

    <div class="container mx-auto px-4 py-6">
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="bg-gradient-to-r from-blue-600 to-purple-700 text-white px-6 py-4">
                <div class="flex items-center space-x-2">
                    <i class="fas fa-warehouse"></i>
                    <h2 class="text-xl font-bold">Solicitudes Pendientes</h2>
                </div>
            </div>
            <div class="p-6">
                <!-- Tabs -->
                <div class="mb-6">
                    <div class="border-b border-gray-200">
                        <nav class="flex space-x-2">
                            <button onclick="window.location.href='panel-solicitudes.php'" class="py-2 px-4 border-b-2 font-medium text-sm border-blue-500 text-blue-600">
                                Solicitudes Pendientes
                                <span class="bg-yellow-500 text-white rounded-full px-2 py-0.5 text-xs ml-1">2</span>
                            </button>
                            <button onclick="window.location.href='panel-inventario.php'" class="py-2 px-4 border-b-2 font-medium text-sm border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                                Gestión de Inventario
                            </button>
                            <button onclick="window.location.href='visualizacion-inventario.php'" class="py-2 px-4 border-b-2 font-medium text-sm border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                                Visualización de Inventario
                            </button>
                        </nav>
                    </div>
                </div>

                <!-- Contenido de Solicitudes Pendientes -->
                <div id="pending-requests-content" class="space-y-4">
                    <div class="border border-green-500 rounded-lg overflow-hidden">
                        <div class="bg-green-600 text-white px-4 py-3">
                            <h3 class="font-bold">Solicitud #001 - APROBADA</h3>
                        </div>
                        <div class="p-4">
                            <div class="space-y-1 text-sm">
                                <p><strong>Solicitante:</strong> Juan Pérez</p>
                                <p><strong>Elemento:</strong> Laptop Dell XPS</p>
                                <p><strong>Cantidad:</strong> 1</p>
                                <p><strong>Aprobada por:</strong> Admin</p>
                                <p><strong>Motivo:</strong> Reunión importante con cliente</p>
                            </div>
                            <div class="flex space-x-2 mt-4">
                                <button onclick="openModal('confirmInventoryModal', '001')" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm flex items-center space-x-1">
                                    <i class="fas fa-check"></i>
                                    <span>Confirmar Disponibilidad</span>
                                </button>
                                <button onclick="openModal('denyInventoryModal', '001')" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-sm flex items-center space-x-1">
                                    <i class="fas fa-times"></i>
                                    <span>No Disponible</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODALES -->
    <!-- Modal Confirmar Inventario -->
    <div id="confirmInventoryModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                            <i class="fas fa-check text-green-600"></i>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="confirmInventoryModalTitle">
                                Confirmar Disponibilidad
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    ¿Estás seguro que el elemento está disponible en inventario?
                                </p>
                                <div class="mt-4">
                                    <label for="itemLocation" class="block text-sm font-medium text-gray-700">Ubicación del Elemento</label>
                                    <input type="text" id="itemLocation" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="Ej: Estante A-3">
                                </div>
                                <div class="mt-3">
                                    <label for="itemCondition" class="block text-sm font-medium text-gray-700">Estado del Elemento</label>
                                    <select id="itemCondition" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        <option value="excellent">Excelente</option>
                                        <option value="good">Bueno</option>
                                        <option value="regular">Regular</option>
                                        <option value="needs_maintenance">Necesita mantenimiento</option>
                                    </select>
                                </div>
                                <div class="mt-3">
                                    <label for="inventoryNotes" class="block text-sm font-medium text-gray-700">Observaciones</label>
                                    <textarea id="inventoryNotes" rows="3" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="Observaciones adicionales..."></textarea>
                                </div>
                                <input type="hidden" id="confirmRequestId">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" onclick="confirmInventory(document.getElementById('confirmRequestId').value)" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Confirmar Disponibilidad
                    </button>
                    <button type="button" onclick="closeModal('confirmInventoryModal')" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Denegar Inventario -->
    <div id="denyInventoryModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <i class="fas fa-times text-red-600"></i>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="denyInventoryModalTitle">
                                Elemento No Disponible
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    ¿Por qué motivo el elemento no está disponible?
                                </p>
                                <div class="mt-4">
                                    <label for="unavailableReason" class="block text-sm font-medium text-gray-700">Motivo <span class="text-red-500">*</span></label>
                                    <select id="unavailableReason" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        <option value="">Seleccione un motivo...</option>
                                        <option value="out_of_stock">Agotado</option>
                                        <option value="in_use">En uso por otro usuario</option>
                                        <option value="maintenance">En mantenimiento</option>
                                        <option value="damaged">Dañado</option>
                                        <option value="not_found">No encontrado</option>
                                        <option value="other">Otro motivo</option>
                                    </select>
                                </div>
                                <div class="mt-3">
                                    <label for="unavailableDetails" class="block text-sm font-medium text-gray-700">Detalles</label>
                                    <textarea id="unavailableDetails" rows="3" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="Proporcione detalles..."></textarea>
                                </div>
                                <div class="mt-3">
                                    <label for="alternativeSuggestion" class="block text-sm font-medium text-gray-700">Sugerencia de alternativa</label>
                                    <input type="text" id="alternativeSuggestion" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="Elemento alternativo disponible (opcional)">
                                </div>
                                <input type="hidden" id="denyRequestId">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" onclick="denyInventory(document.getElementById('denyRequestId').value)" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Confirmar No Disponible
                    </button>
                    <button type="button" onclick="closeModal('denyInventoryModal')" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="script-solicitudes.js"></script>
</body>

</html>