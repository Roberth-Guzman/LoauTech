<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Control de Portería</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <!-- Navbar -->
    <nav class="bg-gray-800 text-white p-4">
        <div class="container mx-auto flex justify-between items-center">
            <div class="flex items-center space-x-2">
                <i class="fas fa-shield-alt"></i>
                <span class="font-bold">Control de Portería</span>
            </div>
            <div class="flex items-center space-x-4">
                <div class="relative">
                    <i class="fas fa-bell text-xl"></i>
                    <span class="absolute -top-2 -right-2 bg-green-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs">1</span>
                </div>
                <div class="flex items-center space-x-2">
                    <i class="fas fa-user-circle"></i>
                    <span>Usuario</span>
                </div>
            </div>
        </div>
    </nav>

    <div class="container mx-auto p-4">
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <!-- Card Header -->
            <div class="bg-gradient-to-r from-blue-600 to-purple-700 text-white p-4">
                <div class="flex items-center">
                    <i class="fas fa-shield-alt mr-2"></i>
                    <h2 class="text-xl font-bold">Control de Portería</h2>
                </div>
            </div>

            <!-- Alerta -->
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4">
                <div class="flex items-center">
                    <i class="fas fa-check-circle mr-2"></i>
                    <div>
                        <h4 class="font-bold">Elementos Listos para Salida</h4>
                        <p>Hay 1 elemento confirmado por inventario listo para autorizar su salida.</p>
                    </div>
                </div>
            </div>

            <!-- Contenido Principal -->
            <div class="p-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Solicitud -->
                    <div class="md:col-span-2">
                        <div class="border border-green-500 rounded">
                            <div class="bg-green-600 text-white p-3">
                                <h3 class="font-bold flex items-center">
                                    <i class="fas fa-box mr-2"></i> Solicitud #001 - LISTA PARA SALIDA
                                </h3>
                            </div>
                            <div class="p-3">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-sm"><strong>Solicitante:</strong> Juan Pérez</p>
                                        <p class="text-sm"><strong>Elemento:</strong> Laptop Dell XPS</p>
                                        <p class="text-sm"><strong>Código:</strong> LAP001</p>
                                        <p class="text-sm"><strong>Cantidad:</strong> 1</p>
                                        <p class="text-sm"><strong>Fecha Solicitud:</strong> 02/07/2025 10:30 AM</p>
                                    </div>
                                    <div>
                                        <p class="text-sm"><strong>Aprobada por:</strong> Admin</p>
                                        <p class="text-sm"><strong>Confirmada por:</strong> Inventario</p>
                                        <p class="text-sm"><strong>Estado:</strong> <span class="bg-green-500 text-white px-2 py-1 rounded text-xs">Lista para salida</span></p>
                                        <p class="text-sm"><strong>Hora límite retorno:</strong> 02/07/2025 6:00 PM</p>
                                    </div>
                                </div>

                                <hr class="my-3">

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block mb-2 text-sm">Observaciones de Salida:</label>
                                        <textarea class="w-full border rounded p-2 text-sm" rows="3" placeholder="Observaciones..."></textarea>
                                    </div>
                                    <div>
                                        <label class="block mb-2 text-sm">Vigilante Responsable:</label>
                                        <input type="text" class="w-full border rounded p-2 text-sm" placeholder="Nombre del vigilante">
                                        
                                        <label class="block mb-2 text-sm mt-2">Hora de Salida:</label>
                                        <input type="datetime-local" class="w-full border rounded p-2 text-sm">
                                    </div>
                                </div>

                                <div class="flex justify-end mt-3 space-x-2">
                                    <button class="bg-green-600 text-white px-4 py-2 rounded flex items-center">
                                        <i class="fas fa-check-circle mr-2"></i> Autorizar Salida
                                    </button>
                                    <button class="bg-yellow-500 text-white px-4 py-2 rounded flex items-center">
                                        <i class="fas fa-pause mr-2"></i> Retener
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Panel de Información -->
                    <div>
                        <div class="bg-gray-100 rounded border p-4">
                            <div class="border-b pb-2 mb-3">
                                <h3 class="font-bold flex items-center text-sm">
                                    <i class="fas fa-info-circle mr-2"></i> Información
                                </h3>
                            </div>
                            <div class="mb-4">
                                <small class="text-gray-500">Elementos en préstamo hoy:</small>
                                <h4 class="text-blue-600 text-2xl">3</h4>
                            </div>
                            <div class="mb-4">
                                <small class="text-gray-500">Pendientes de retorno:</small>
                                <h4 class="text-yellow-600 text-2xl">1</h4>
                            </div>
                            <div class="mb-4">
                                <small class="text-gray-500">Retornos en tiempo:</small>
                                <h4 class="text-green-600 text-2xl">95%</h4>
                            </div>
                            <button class="w-full border border-blue-500 text-blue-500 rounded py-1 text-sm flex items-center justify-center">
                                <i class="fas fa-history mr-2"></i> Ver Historial
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Registro de Salidas -->
                <div class="mt-6">
                    <h3 class="font-bold mb-3 flex items-center">
                        <i class="fas fa-clipboard-list mr-2"></i> Registro de Salidas Hoy
                    </h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border text-sm">
                            <thead class="bg-gray-800 text-white">
                                <tr>
                                    <th class="py-2 px-4">Hora</th>
                                    <th class="py-2 px-4">Solicitante</th>
                                    <th class="py-2 px-4">Elemento</th>
                                    <th class="py-2 px-4">Vigilante</th>
                                    <th class="py-2 px-4">Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="border-b">
                                    <td class="py-2 px-4">09:15 AM</td>
                                    <td class="py-2 px-4">Carlos Ruiz</td>
                                    <td class="py-2 px-4">Proyector Epson</td>
                                    <td class="py-2 px-4">José García</td>
                                    <td class="py-2 px-4"><span class="bg-green-500 text-white px-2 py-1 rounded text-xs">Salida autorizada</span></td>
                                </tr>
                                <tr>
                                    <td class="py-2 px-4">10:30 AM</td>
                                    <td class="py-2 px-4">Ana López</td>
                                    <td class="py-2 px-4">Laptop HP</td>
                                    <td class="py-2 px-4">José García</td>
                                    <td class="py-2 px-4"><span class="bg-yellow-500 text-white px-2 py-1 rounded text-xs">Pendiente retorno</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Autorizar Salida -->
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden" id="exitModal">
        <div class="bg-white rounded-lg w-full max-w-md">
            <div class="bg-green-600 text-white p-4 rounded-t-lg flex justify-between items-center">
                <h3 class="font-bold flex items-center">
                    <i class="fas fa-check-circle mr-2"></i> Confirmar Salida
                </h3>
                <button onclick="closeModal('exitModal')">&times;</button>
            </div>
            <div class="p-4">
                <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-3 mb-4">
                    <p>¿Está seguro de autorizar la salida del elemento <strong>Laptop Dell XPS</strong> para <strong>Juan Pérez</strong>?</p>
                </div>
                <div class="mb-4">
                    <label class="block mb-2">Observaciones Finales:</label>
                    <textarea class="w-full border rounded p-2" rows="2" placeholder="Observaciones finales..."></textarea>
                </div>
                <div class="flex items-center mb-4">
                    <input type="checkbox" id="notifyExit" class="mr-2" checked>
                    <label for="notifyExit">Notificar a todos los usuarios involucrados</label>
                </div>
            </div>
            <div class="p-4 border-t flex justify-end space-x-2">
                <button class="bg-gray-500 text-white px-4 py-2 rounded" onclick="closeModal('exitModal')">Cancelar</button>
                <button class="bg-green-600 text-white px-4 py-2 rounded flex items-center">
                    <i class="fas fa-check-circle mr-2"></i> Autorizar
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Retener -->
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden" id="holdModal">
        <div class="bg-white rounded-lg w-full max-w-md">
            <div class="bg-yellow-500 text-white p-4 rounded-t-lg flex justify-between items-center">
                <h3 class="font-bold flex items-center">
                    <i class="fas fa-pause mr-2"></i> Retener Elemento
                </h3>
                <button onclick="closeModal('holdModal')">&times;</button>
            </div>
            <div class="p-4">
                <div class="mb-4">
                    <label class="block mb-2">Motivo de Retención:</label>
                    <select class="w-full border rounded p-2">
                        <option value="">Seleccione un motivo...</option>
                        <option value="documentation_missing">Documentación faltante</option>
                        <option value="identification_required">Identificación requerida</option>
                        <option value="element_issue">Problema con el elemento</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block mb-2">Detalles de la Retención:</label>
                    <textarea class="w-full border rounded p-2" rows="3" placeholder="Describa los detalles..."></textarea>
                </div>
                <div class="mb-4">
                    <label class="block mb-2">Fecha de Resolución Esperada:</label>
                    <input type="datetime-local" class="w-full border rounded p-2">
                </div>
            </div>
            <div class="p-4 border-t flex justify-end space-x-2">
                <button class="bg-gray-500 text-white px-4 py-2 rounded" onclick="closeModal('holdModal')">Cancelar</button>
                <button class="bg-yellow-500 text-white px-4 py-2 rounded flex items-center">
                    <i class="fas fa-pause mr-2"></i> Retener
                </button>
            </div>
        </div>
    </div>

    <script>
        // Funciones para manejar modales
        function openModal(id) {
            document.getElementById(id).classList.remove('hidden');
        }

        function closeModal(id) {
            document.getElementById(id).classList.add('hidden');
        }

        // Simular clic en botones
        document.querySelectorAll('[onclick*="openModal"]').forEach(btn => {
            btn.addEventListener('click', function() {
                const modalId = this.getAttribute('onclick').match(/'([^']+)'/)[1];
                openModal(modalId);
            });
        });
    </script>
</body>
</html>