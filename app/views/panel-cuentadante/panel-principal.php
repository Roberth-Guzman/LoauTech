<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Panel Cuentadante - Loautech</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-100">
<?php require_once 'includes/navbar.php'; ?>

<div class="p-4 sm:ml-64 max-w-7xl mx-auto">
    <div class="p-4 border-2 border-gray-200 border-dashed rounded-lg dark:border-gray-700">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            <div class="bg-green-500 text-white p-4 rounded-lg shadow-lg">
                <h3 class="text-xl font-bold">Aprobadas</h3>
                <p class="text-2xl"><?= $data['stats']['aprobadas'] ?></p>
            </div>
            <div class="bg-red-500 text-white p-4 rounded-lg shadow-lg">
                <h3 class="text-xl font-bold">Rechazadas</h3>
                <p class="text-2xl"><?= $data['stats']['rechazadas'] ?></p>
            </div>
            <div class="bg-blue-500 text-white p-4 rounded-lg shadow-lg">
                <h3 class="text-xl font-bold">Total Procesadas</h3>
                <p class="text-2xl"><?= $data['stats']['total'] ?></p>
            </div>
        </div>

        <!-- Solicitudes Pendientes de Aprobación -->
        <div class="bg-white p-6 rounded-lg shadow-md mb-6">
            <h2 class="text-2xl font-semibold mb-4 text-gray-800">Solicitudes Pendientes de Aprobación</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID Petición</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Solicitante</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Elemento</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cantidad</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if (!empty($data['solicitudes_pendientes'])) : ?>
                            <?php foreach ($data['solicitudes_pendientes'] as $peticion) : ?>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= htmlspecialchars($peticion->IDpre) ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= htmlspecialchars($peticion->solicitante_nombre) ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= htmlspecialchars($peticion->elemento_nombre) ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= htmlspecialchars($peticion->cantidad) ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= htmlspecialchars(date('d/m/Y H:i', strtotime($peticion->fecha_prestamo))) ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <form action="<?= BASE_URL ?>/cuentadante/procesarSolicitud" method="POST" class="inline">
                                                <input type="hidden" name="id_aprobacion" value="<?= $peticion->id_aprobacion ?>">
                                                <input type="hidden" name="accion" value="aprobar">
                                                <button type="submit" class="text-green-600 hover:text-green-900">Aprobar</button>
                                            </form>
                                            <button onclick="openModal('<?= $peticion->id_aprobacion ?>')" class="text-red-600 hover:text-red-900 ml-4">Rechazar</button>
                                        </td>
                                    </tr>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-gray-500">No hay solicitudes pendientes.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Historial de Aprobaciones -->
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-2xl font-semibold mb-4 text-gray-800">Historial de Aprobaciones</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Solicitante</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Elemento</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cantidad</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if (!empty($data['peticiones'])) : ?>
                            <?php foreach ($data['peticiones'] as $peticion) : ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= htmlspecialchars($peticion->IDpre) ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= htmlspecialchars($peticion->solicitante) ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= htmlspecialchars($peticion->elemento_nombre) ?> (<?= htmlspecialchars($peticion->codigoinventario) ?>)</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= htmlspecialchars($peticion->cantidad) ?> unidad(es)</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php
                                            $estadoClass = '';
                                            $estadoTexto = '';
                                            $estadoIcon = '';
                                            if ($peticion->estadoaut === 'Aprobado') {
                                                $estadoClass = 'bg-green-100 text-green-800';
                                                $estadoTexto = 'Aprobada';
                                                $estadoIcon = 'fas fa-check';
                                            } elseif ($peticion->estadoaut === 'Rechazado') {
                                                $estadoClass = 'bg-red-100 text-red-800';
                                                $estadoTexto = 'Rechazada';
                                                $estadoIcon = 'fas fa-times';
                                            }
                                        ?>
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $estadoClass ?>">
                                            <i class="<?= $estadoIcon ?> mr-1"></i><?= $estadoTexto ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500">No hay peticiones para mostrar.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Paginación -->
        <?php if ($data['paginacion']['total_paginas'] > 1): ?>
        <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6 mt-4 rounded-lg shadow">
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                <div>
                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                        <?php for ($i = 1; $i <= $data['paginacion']['total_paginas']; $i++): ?>
                            <a href="?pagina=<?= $i ?>" class="relative inline-flex items-center px-4 py-2 border text-sm font-medium <?= $i === $data['paginacion']['pagina_actual'] ? 'z-10 bg-blue-50 border-blue-500 text-blue-600' : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50' ?>">
                                <?= $i ?>
                            </a>
                        <?php endfor; ?>
                    </nav>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal para motivo de rechazo -->
<div id="rechazoModal" class="fixed z-10 inset-0 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form action="<?= BASE_URL ?>/cuentadante/procesarSolicitud" method="POST">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                Motivo del Rechazo
                            </h3>
                            <div class="mt-2">
                                <input type="hidden" name="id_aprobacion" id="modal_id_aprobacion">
                                <input type="hidden" name="accion" value="rechazar">
                                <textarea name="motivo" rows="4" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border border-gray-300 rounded-md" placeholder="Explica por qué se rechaza la solicitud..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Confirmar Rechazo
                    </button>
                    <button type="button" onclick="closeModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancelar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openModal(id_aprobacion) {
        document.getElementById('modal_id_aprobacion').value = id_aprobacion;
        document.getElementById('rechazoModal').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('rechazoModal').classList.add('hidden');
    }
</script>
</body>
</html>
