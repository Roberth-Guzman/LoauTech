<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Control de Portería</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Agregar Bootstrap CSS y JS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body class="bg-gray-100">
    <!-- Navbar -->
    <nav class="bg-gray-800 text-white shadow-lg">
        <div class="container mx-auto px-4 py-3 flex justify-between items-center">
            <div class="flex items-center space-x-2">
                <i class="fas fa-boxes"></i>
                <span class="font-bold">Control de Portería</span>
            </div>
            <div class="flex items-center space-x-4">
                <div class="relative">
                    <button class="relative p-2 rounded-full hover:bg-gray-700">
                        <i class="fas fa-bell"></i>
                        <span class="absolute -top-1 -right-1 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs">1</span>
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
                    <i class="fas fa-shield-alt"></i>
                    <h2 class="text-xl font-bold">Control de Portería</h2>
                </div>
            </div>
            <div class="p-6">
                <!-- Notificación -->
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6">
                    <div class="flex items-start">
                        <i class="fas fa-check-circle mt-1 mr-2"></i>
                        <div>
                            <h4 class="font-bold">Elementos Listos para Salida</h4>
                            <p>Hay 1 elemento confirmado por inventario listo para autorizar su salida.</p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Elemento pendiente de autorización -->
                    <div class="md:col-span-2">
                        <div class="border border-green-500 rounded-lg overflow-hidden">
                            <div class="bg-green-600 text-white px-4 py-3">
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-box"></i>
                                    <h3 class="font-bold">Solicitud #001 - LISTA PARA SALIDA</h3>
                                </div>
                            </div>
                            <div class="p-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <div class="space-y-1 text-sm">
                                            <p><strong>Solicitante:</strong> Juan Pérez</p>
                                            <p><strong>Elemento:</strong> Laptop Dell XPS</p>
                                            <p><strong>Código:</strong> LAP001</p>
                                            <p><strong>Cantidad:</strong> 1</p>
                                            <p><strong>Fecha Solicitud:</strong> 02/07/2025 10:30 AM</p>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="space-y-1 text-sm">
                                            <p><strong>Aprobada por:</strong> Admin</p>
                                            <p><strong>Confirmada por:</strong> Inventario</p>
                                            <p><strong>Estado:</strong> <span class="bg-green-500 text-white px-2 py-0.5 rounded-full text-xs">Lista para salida</span></p>
                                            <p><strong>Hora límite retorno:</strong> 02/07/2025 6:00 PM</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex justify-end space-x-3 mt-6">
                                    <button type="button" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2" data-bs-toggle="modal" data-bs-target="#confirmExitModal">
                                        <i class="fas fa-check-circle"></i>
                                        <span>Autorizar Salida</span>
                                    </button>
                                    <button type="button" class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg flex items-center space-x-2" data-bs-toggle="modal" data-bs-target="#holdExitModal">
                                        <i class="fas fa-pause"></i>
                                        <span>Retener</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Panel de información -->
                    <div>
                        <div class="bg-gray-50 border rounded-lg overflow-hidden">
                            <div class="bg-gray-200 px-4 py-3">
                                <h3 class="font-bold flex items-center space-x-2">
                                    <i class="fas fa-info-circle"></i>
                                    <span>Información</span>
                                </h3>
                            </div>
                            <div class="p-4">
                                <div class="mb-4">
                                    <small class="text-gray-500">Elementos en préstamo hoy:</small>
                                    <h4 class="text-blue-600 text-2xl font-bold">3</h4>
                                </div>
                                <div class="mb-4">
                                    <small class="text-gray-500">Pendientes de retorno:</small>
                                    <h4 class="text-yellow-500 text-2xl font-bold">1</h4>
                                </div>
                                <div class="mb-4">
                                    <small class="text-gray-500">Retornos en tiempo:</small>
                                    <h4 class="text-green-600 text-2xl font-bold">95%</h4>
                                </div>
                                <button class="w-full border border-blue-500 text-blue-500 hover:bg-blue-50 px-4 py-2 rounded-lg flex items-center justify-center space-x-2">
                                    <i class="fas fa-history"></i>
                                    <span>Ver Historial</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Registro de salidas -->
                <div class="mt-8">
                    <h3 class="text-lg font-semibold mb-3 flex items-center space-x-2">
                        <i class="fas fa-clipboard-list"></i>
                        <span>Registro de Salidas Hoy</span>
                    </h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-800 text-white">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium uppercase tracking-wider">Hora</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium uppercase tracking-wider">Solicitante</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium uppercase tracking-wider">Elemento</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium uppercase tracking-wider">Vigilante</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium uppercase tracking-wider">Estado</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr>
                                    <td class="px-4 py-2 whitespace-nowrap text-sm">09:15 AM</td>
                                    <td class="px-4 py-2 whitespace-nowrap text-sm">Carlos Ruiz</td>
                                    <td class="px-4 py-2 whitespace-nowrap text-sm">Proyector Epson</td>
                                    <td class="px-4 py-2 whitespace-nowrap text-sm">José García</td>
                                    <td class="px-4 py-2 whitespace-nowrap"><span class="bg-green-500 text-white px-2 py-0.5 rounded-full text-xs">Salida autorizada</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODALES PORTERÍA -->
    <!-- Modal Confirmar Salida -->
    <div class="modal fade" id="confirmExitModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-green-600 text-white">
                    <h5 class="modal-title text-xl font-bold flex items-center space-x-2">
                        <i class="fas fa-check-circle"></i>
                        <span>Confirmar Salida</span>
                    </h5>
                    <button type="button" class="text-white" data-bs-dismiss="modal" aria-label="Close">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="modal-body p-6">
                    <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 mb-4">
                        <h6 class="font-bold">Confirmar Autorización de Salida</h6>
                        <p class="mb-0">¿Está seguro de autorizar la salida del elemento <strong>Laptop Dell XPS</strong> para <strong>Juan Pérez</strong>?</p>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <div class="space-y-1 text-sm">
                                <p><strong>Solicitante:</strong> Juan Pérez</p>
                                <p><strong>Elemento:</strong> Laptop Dell XPS</p>
                                <p><strong>Código:</strong> LAP001</p>
                                <p><strong>Cantidad:</strong> 1</p>
                            </div>
                        </div>
                        <div>
                            <div class="space-y-1 text-sm">
                                <p><strong>Aprobada por:</strong> Admin</p>
                                <p><strong>Confirmada por:</strong> Inventario</p>
                                <p><strong>Hora límite retorno:</strong> 02/07/2025 6:00 PM</p>
                            </div>
                        </div>
                    </div>
                    
                    <form>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Observaciones de Salida:</label>
                                <textarea class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" rows="3" placeholder="Ingrese observaciones sobre el estado del elemento..."></textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Vigilante Responsable:</label>
                                <input type="text" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Nombre del vigilante">

                                <label class="block text-sm font-medium text-gray-700 mt-3 mb-1">Hora de Salida:</label>
                                <input type="datetime-local" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <div class="flex items-center">
                                <input type="checkbox" class="rounded text-blue-500 focus:ring-blue-500" id="notifyExit" checked>
                                <label for="notifyExit" class="ml-2 text-sm text-gray-700">
                                    Notificar a todos los usuarios involucrados
                                </label>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer bg-gray-100">
                    <button type="button" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg" data-bs-dismiss="modal">
                        Cancelar
                    </button>
                    <button type="button" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2" onclick="authorizeExit('001')">
                        <i class="fas fa-check-circle"></i>
                        <span>Autorizar Salida</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Retener Elemento -->
    <div class="modal fade" id="holdExitModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-yellow-500 text-white">
                    <h5 class="modal-title text-xl font-bold flex items-center space-x-2">
                        <i class="fas fa-pause"></i>
                        <span>Retener Elemento</span>
                    </h5>
                    <button type="button" class="text-white" data-bs-dismiss="modal" aria-label="Close">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="modal-body p-6">
                    <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-4">
                        <h6 class="font-bold">Confirmar Retención de Elemento</h6>
                        <p class="mb-0">¿Está seguro de retener el elemento <strong>Laptop Dell XPS</strong> solicitado por <strong>Juan Pérez</strong>?</p>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <div class="space-y-1 text-sm">
                                <p><strong>Solicitante:</strong> Juan Pérez</p>
                                <p><strong>Elemento:</strong> Laptop Dell XPS</p>
                                <p><strong>Código:</strong> LAP001</p>
                                <p><strong>Cantidad:</strong> 1</p>
                            </div>
                        </div>
                        <div>
                            <div class="space-y-1 text-sm">
                                <p><strong>Aprobada por:</strong> Admin</p>
                                <p><strong>Confirmada por:</strong> Inventario</p>
                                <p><strong>Hora límite retorno:</strong> 02/07/2025 6:00 PM</p>
                            </div>
                        </div>
                    </div>
                    
                    <form>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Motivo de Retención: <span class="text-red-500">*</span></label>
                            <select class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                <option value="">Seleccione un motivo...</option>
                                <option value="documentation_missing">Documentación faltante</option>
                                <option value="identification_required">Identificación requerida</option>
                                <option value="element_issue">Problema con el elemento</option>
                                <option value="authorization_pending">Autorización pendiente</option>
                                <option value="other">Otro motivo</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Detalles de la Retención:</label>
                            <textarea class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" rows="3" placeholder="Describa los detalles de por qué se retiene el elemento..."></textarea>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de Resolución Esperada:</label>
                            <input type="datetime-local" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </form>
                </div>
                <div class="modal-footer bg-gray-100">
                    <button type="button" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg" data-bs-dismiss="modal">
                        Cancelar
                    </button>
                    <button type="button" class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg flex items-center space-x-2" onclick="holdItem('001')">
                        <i class="fas fa-pause"></i>
                        <span>Retener Elemento</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="script.js"></script>
</body>
</html>