<?php
session_start();

// 1. Validar sesión
if (!isset($_SESSION['usuario'])) {
    header('Location: ../../../login.php'); 
    exit();
}

// 2. Validar rol 'cuentadante'
if ($_SESSION['usuario']['rol'] !== 'cuentadante') {
    header('Location: ../../../login.php?error=acceso_no_autorizado');
    exit();
}

// 3. Conexión a BD (si es necesaria)
require_once $_SERVER['DOCUMENT_ROOT'] . '/loautech-main/conexion.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Peticiones</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <!-- Navbar -->
    <nav class="bg-gray-800 text-white shadow-lg">
        <div class="container mx-auto px-4 py-3 flex justify-between items-center">
            <div class="flex items-center space-x-2">
                <i class="fas fa-boxes"></i>
                <span class="font-bold">Gestión de Peticiones</span>
            </div>
            <div class="flex items-center space-x-4">
                <div class="relative">
                    <button class="relative p-2 rounded-full hover:bg-gray-700">
                        <i class="fas fa-bell"></i>
                        <span class="absolute -top-1 -right-1 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs">3</span>
                    </button>
                </div>
                <div class="relative">
                   <button class="flex items-center space-x-2 hover:bg-gray-700 px-3 py-2 rounded">
                    <a href="perfil-cuentadante.php"> 
                        <i class="fas fa-user-circle"></i>
                    </a>
                        <span>Usuarios</span>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <div class="container mx-auto px-4 py-6">
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="bg-gradient-to-r from-blue-600 to-purple-700 text-white px-6 py-4">
                <div class="flex items-center space-x-2">
                    <i class="fas fa-clipboard-list"></i>
                    <h2 class="text-xl font-bold">Gestión de Peticiones</h2>
                </div>
            </div>
            <div class="p-6">
                <!-- Filtros -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <select class="border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Todos los estados</option>
                        <option value="pending">Pendiente</option>
                        <option value="approved">Aprobado</option>
                        <option value="rejected">Rechazado</option>
                    </select>
                    <input type="text" class="border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Buscar por solicitante...">
                    <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center justify-center space-x-2">
                        <i class="fas fa-sync-alt"></i>
                        <span>Actualizar</span>
                    </button>
                </div>

                <!-- Lista de Peticiones -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
                    <!-- Petición 1 -->
                    <div class="border-l-4 border-yellow-500 border rounded-lg overflow-hidden">
                        <div class="p-4">
                            <div class="flex justify-between items-start mb-3">
                                <h3 class="font-bold">Solicitud #001</h3>
                                <span class="bg-yellow-500 text-white text-xs px-2 py-1 rounded">Pendiente</span>
                            </div>
                            <div class="space-y-1 text-sm">
                                <p><strong>Solicitante:</strong> Juan Pérez</p>
                                <p><strong>Elemento:</strong> Laptop Dell XPS</p>
                                <p><strong>Cantidad:</strong> 1</p>
                                <p><strong>Fecha:</strong> 02/07/2025 10:30 AM</p>
                                <p><strong>Prioridad:</strong> <span class="text-red-500">Alta</span></p>
                            </div>
                            <div class="flex justify-end space-x-2 mt-4">
                                <button onclick="openModal('approveModal', '001')" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm flex items-center space-x-1">
                                    <i class="fas fa-check"></i>
                                    <span>Aprobar</span>
                                </button>
                                <button onclick="openModal('rejectModal', '001')" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-sm flex items-center space-x-1">
                                    <i class="fas fa-times"></i>
                                    <span>Rechazar</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Petición 2 -->
                    <div class="border-l-4 border-yellow-500 border rounded-lg overflow-hidden">
                        <div class="p-4">
                            <div class="flex justify-between items-start mb-3">
                                <h3 class="font-bold">Solicitud #002</h3>
                                <span class="bg-yellow-500 text-white text-xs px-2 py-1 rounded">Pendiente</span>
                            </div>
                            <div class="space-y-1 text-sm">
                                <p><strong>Solicitante:</strong> María González</p>
                                <p><strong>Elemento:</strong> Proyector Epson</p>
                                <p><strong>Cantidad:</strong> 1</p>
                                <p><strong>Fecha:</strong> 02/07/2025 11:15 AM</p>
                                <p><strong>Prioridad:</strong> <span class="text-yellow-500">Media</span></p>
                            </div>
                            <div class="flex justify-end space-x-2 mt-4">
                                <button onclick="openModal('approveModal', '002')" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm flex items-center space-x-1">
                                    <i class="fas fa-check"></i>
                                    <span>Aprobar</span>
                                </button>
                                <button onclick="openModal('rejectModal', '002')" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-sm flex items-center space-x-1">
                                    <i class="fas fa-times"></i>
                                    <span>Rechazar</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Mensajes de Celular -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold mb-3 flex items-center space-x-2">
                        <i class="fas fa-mobile-alt"></i>
                        <span>Mensajes de Celular</span>
                    </h3>
                    <div class="bg-white border rounded-lg overflow-hidden">
                        <div class="max-h-72 overflow-y-auto p-2">
                            <div class="border-b border-gray-200 p-2 hover:bg-gray-50">
                                <div class="flex justify-between items-center">
                                    <strong>+57 300 123 4567</strong>
                                    <small class="text-gray-500">10:45 AM</small>
                                </div>
                                <p class="text-sm">Necesito urgente el proyector para la presentación de las 2 PM</p>
                            </div>
                            <div class="border-b border-gray-200 p-2 hover:bg-gray-50">
                                <div class="flex justify-between items-center">
                                    <strong>+57 301 987 6543</strong>
                                    <small class="text-gray-500">11:20 AM</small>
                                </div>
                                <p class="text-sm">¿Está disponible la laptop que solicité?</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODALES -->
    <!-- Modal Aprobar Petición -->
    <div id="approveModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
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
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="approveModalTitle">
                                Aprobar Petición
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    ¿Estás seguro que deseas aprobar esta petición?
                                </p>
                                <input type="hidden" id="approveRequestId">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" onclick="approveRequest(document.getElementById('approveRequestId').value)" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Aprobar
                    </button>
                    <button type="button" onclick="closeModal('approveModal')" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Rechazar Petición - ACTUALIZADO -->
    <div id="rejectModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
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
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="rejectModalTitle">
                                Rechazar Petición
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    ¿Estás seguro que deseas rechazar esta petición?
                                </p>
                                <div class="mt-4">
                                    <label for="rejectReason" class="block text-sm font-medium text-gray-700">Motivo del rechazo <span class="text-red-500">*</span></label>
                                    <select id="rejectReason" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        <option value="">Seleccione un motivo...</option>
                                        <option value="justificacion_insuficiente">Justificación insuficiente</option>
                                        <option value="elemento_no_disponible">Elemento no disponible</option>
                                        <option value="prioridad_baja">Prioridad baja</option>
                                        <option value="politicas_empresa">No cumple con políticas de la empresa</option>
                                        <option value="otro">Otro motivo</option>
                                    </select>
                                </div>
                                <div class="mt-3">
                                    <label for="rejectDetails" class="block text-sm font-medium text-gray-700">Detalles adicionales</label>
                                    <textarea id="rejectDetails" rows="3" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="Proporcione detalles sobre el motivo del rechazo..."></textarea>
                                </div>
                                <div class="mt-3 flex items-center">
                                    <input id="notifyApplicant" name="notifyApplicant" type="checkbox" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                    <label for="notifyApplicant" class="ml-2 block text-sm text-gray-700">
                                        Notificar al solicitante por correo electrónico
                                    </label>
                                </div>
                                <input type="hidden" id="rejectRequestId">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" onclick="rejectRequest(document.getElementById('rejectRequestId').value)" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Rechazar Petición
                    </button>
                    <button type="button" onclick="closeModal('rejectModal')" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="script.js"></script>
</body>
</html>