<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Solicitudes - Loatech</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body class="bg-gray-100">
    <div class="min-h-screen">
        <?php require_once __DIR__ . '/includes/navbar.php'; ?>


        <!-- Main Content -->
        <main class="container mx-auto px-4 py-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-6">Solicitudes Pendientes</h1>

            <div id="pending-requests-container" class="bg-white p-6 rounded-lg shadow-md">
                <div class="space-y-4" id="pending-requests">
                    <?php if (!empty($data['solicitudes'])) : ?>
                        <div class="space-y-4">
                            <?php foreach ($data['solicitudes'] as $solicitud) : ?>
                                <div id="solicitud-<?= $solicitud->IDpre ?>" class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
                                    <div class="p-5">
                                        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                                            <div class="flex-1">
                                                <div class="flex items-center gap-3">
                                                    <div class="bg-indigo-50 p-2 rounded-lg">
                                                        <i class="fas fa-file-alt text-indigo-500"></i>
                                                    </div>
                                                    <div>
                                                        <h3 class="text-base font-medium text-gray-800">Solicitud #<?= $solicitud->IDpre ?></h3>
                                                        <p class="text-xs text-gray-500"><?= date('d/m/Y H:i', strtotime($solicitud->fecha_solicitud)) ?></p>
                                                    </div>
                                                </div>

                                                <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                                                    <div class="space-y-1">
                                                        <p class="text-xs font-medium text-gray-500">Solicitante</p>
                                                        <p class="text-sm text-gray-800"><?= $solicitud->solicitante ?></p>
                                                        <p class="text-xs text-gray-500"><?= ucfirst($solicitud->tipodocumento) ?>: <?= $solicitud->documento ?></p>
                                                    </div>
                                                    <div class="space-y-1">
                                                        <p class="text-xs font-medium text-gray-500">Elemento</p>
                                                        <p class="text-sm text-gray-800"><?= $solicitud->elemento_nombre ?></p>
                                                        <p class="text-xs text-gray-500">Código: <?= $solicitud->codigoinventario ?></p>
                                                    </div>
                                                    <div class="space-y-1">
                                                        <p class="text-xs font-medium text-gray-500">Cantidad</p>
                                                        <p class="text-sm text-gray-800"><?= $solicitud->cantidad ?> unidades</p>
                                                    </div>
                                                    <div class="space-y-1">
                                                        <p class="text-xs font-medium text-gray-500">Lugar de traslado</p>
                                                        <p class="text-sm text-gray-800"><?= $solicitud->lugardetraslado ?></p>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="flex flex-col sm:flex-row sm:items-center gap-2">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                    <?= ucfirst($solicitud->estadoaut) ?>
                                                </span>
                                                <div class="flex gap-2 mt-2 sm:mt-0">
                                                    <button onclick="openApproveModal(<?= $solicitud->IDpre ?>)" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-1 focus:ring-green-500">
                                                        <i class="fas fa-check mr-1"></i> Aprobar
                                                    </button>
                                                    <button onclick="rejectRequest(<?= $solicitud->IDpre ?>)" class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                                        <i class="fas fa-times mr-1"></i> Rechazar
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else : ?>
                        <div id="no-requests-message" class="text-center py-8">
                            <i class="fas fa-inbox text-5xl text-gray-400 mb-4"></i>
                            <p class="text-xl text-gray-600">No hay solicitudes pendientes.</p>
                            <p class="text-gray-500 mt-2">Todas las solicitudes han sido procesadas.</p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Paginación -->
                <?php if ($data['total_paginas'] > 1): ?>
                    <div class="mt-8 flex items-center justify-between border-t border-gray-200 px-4 py-3 sm:px-6">
                        <div class="flex flex-1 justify-between sm:hidden">
                            <?php if ($data['pagina_actual'] > 1): ?>
                                <a href="<?= BASE_URL ?>/almacen/almacen/panelPrincipal/<?= $data['pagina_actual'] - 1 ?>" class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Anterior</a>
                            <?php endif; ?>
                            <?php if ($data['pagina_actual'] < $data['total_paginas']): ?>
                                <a href="<?= BASE_URL ?>/almacen/almacen/panelPrincipal/<?= $data['pagina_actual'] + 1 ?>" class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Siguiente</a>
                            <?php endif; ?>
                        </div>
                        <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                            <div>
                                <p class="text-sm text-gray-700">
                                    Mostrando <span class="font-medium"><?= (($data['pagina_actual'] - 1) * 10) + 1 ?></span> a
                                    <span class="font-medium"><?= min($data['pagina_actual'] * 10, $data['total_registros']) ?></span> de
                                    <span class="font-medium"><?= $data['total_registros'] ?></span> resultados
                                </p>
                            </div>
                            <div>
                                <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
                                    <?php if ($data['pagina_actual'] > 1): ?>
                                        <a href="<?= BASE_URL ?>/almacen/almacen/panelPrincipal/<?= $data['pagina_actual'] - 1 ?>" class="relative inline-flex items-center rounded-l-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0">
                                            <span class="sr-only">Anterior</span>
                                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                <path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 01-.02 1.06L8.832 10l3.938 3.71a.75.75 0 11-1.04 1.08l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 011.06.02z" clip-rule="evenodd" />
                                            </svg>
                                        </a>
                                    <?php endif; ?>

                                    <?php
                                    $inicio = max(1, $data['pagina_actual'] - 2);
                                    $fin = min($data['total_paginas'], $data['pagina_actual'] + 2);

                                    if ($inicio > 1) {
                                        echo '<a href="' . BASE_URL . '/almacen/almacen/panelPrincipal/1" class="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0">1</a>';
                                        if ($inicio > 2) {
                                            echo '<span class="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-700 ring-1 ring-inset ring-gray-300 focus:outline-offset-0">...</span>';
                                        }
                                    }

                                    for ($i = $inicio; $i <= $fin; $i++):
                                        $active = $i == $data['pagina_actual'] ? 'bg-indigo-600 text-white hover:bg-indigo-600 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600' : 'text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50';
                                    ?>
                                        <a href="<?= BASE_URL ?>/almacen/almacen/panelPrincipal/<?= $i ?>" class="relative inline-flex items-center px-4 py-2 text-sm font-semibold <?= $active ?> focus:z-20 focus:outline-offset-0">
                                            <?= $i ?>
                                        </a>
                                    <?php
                                    endfor;

                                    if ($fin < $data['total_paginas']) {
                                        if ($fin < $data['total_paginas'] - 1) {
                                            echo '<span class="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-700 ring-1 ring-inset ring-gray-300 focus:outline-offset-0">...</span>';
                                        }
                                        echo '<a href="' . BASE_URL . '/almacen/almacen/panelPrincipal/' . $data['total_paginas'] . '" class="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0">' . $data['total_paginas'] . '</a>';
                                    }
                                    ?>

                                    <?php if ($data['pagina_actual'] < $data['total_paginas']): ?>
                                        <a href="<?= BASE_URL ?>/almacen/almacen/panelPrincipal/<?= $data['pagina_actual'] + 1 ?>" class="relative inline-flex items-center rounded-r-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0">
                                            <span class="sr-only">Siguiente</span>
                                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                                            </svg>
                                        </a>
                                    <?php endif; ?>
                                </nav>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

            </div>
        </main>
    </div>

    <!-- Approve Modal -->
    <div id="approveModal" class="fixed inset-0 bg-gray-900 bg-opacity-60 flex items-center justify-center hidden z-50">
        <div class="bg-white p-7 rounded-lg shadow-xl w-full max-w-md transform transition-all">
            <h2 class="text-2xl font-bold mb-4 text-gray-800">Confirmar Aprobación</h2>
            <p class="text-gray-600">¿Estás seguro de que quieres aprobar esta solicitud?</p>
            <div class="mt-6 flex justify-end space-x-4">
                <button onclick="closeModal('approveModal')" class="bg-gray-300 text-gray-800 px-4 py-2 rounded-md hover:bg-gray-400">Cancelar</button>
                <button id="confirmApproveBtn" class="bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600">Aprobar</button>
            </div>
        </div>
    </div>

    <!-- Reject Modal -->
    <div id="rejectModal" class="fixed inset-0 bg-gray-900 bg-opacity-60 flex items-center justify-center hidden z-50">
        <div class="bg-white p-7 rounded-lg shadow-xl w-full max-w-md transform transition-all">
            <h2 class="text-2xl font-bold mb-4 text-gray-800">Rechazar Solicitud</h2>
            <p class="text-gray-600 mb-4">Por favor, especifica el motivo del rechazo.</p>
            <textarea id="rejectionReason" class="w-full px-3 py-2 text-gray-700 border rounded-lg focus:outline-none" rows="4" placeholder="Motivo del rechazo..."></textarea>
            <div class="mt-6 flex justify-end space-x-4">
                <button onclick="closeModal('rejectModal')" class="bg-gray-300 text-gray-800 px-4 py-2 rounded-md hover:bg-gray-400">Cancelar</button>
                <button id="confirmRejectBtn" class="bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-600">Rechazar</button>
            </div>
        </div>
    </div>

    <!-- Toast Notification -->
    <div id="toast" class="fixed top-5 right-5 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg hidden z-50 flex items-center">
        <i id="toast-icon" class="fas fa-check-circle mr-3"></i>
        <p id="toast-message"></p>
    </div>

    <script>
        const BASE_URL = '<?= BASE_URL ?>';
        let currentRequestId = null;
        let isProcessing = false;



        function openModal(modalId) {
            document.getElementById(modalId).classList.remove('hidden');
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
        }

        function openApproveModal(id) {
            currentRequestId = id;
            openModal('approveModal');
        }

        // Eliminado openRejectModal: rechazo será directo

        function showToast(message, type = 'success') {
            const toast = document.getElementById('toast');
            const toastMessage = document.getElementById('toast-message');
            const toastIcon = document.getElementById('toast-icon');

            toastMessage.textContent = message;
            toast.className = 'fixed top-5 right-5 text-white px-6 py-3 rounded-lg shadow-lg z-50 flex items-center';
            toastIcon.className = 'mr-3';

            if (type === 'success') {
                toast.classList.add('bg-green-500');
                toastIcon.classList.add('fa-check-circle');
            } else {
                toast.classList.add('bg-red-500');
                toastIcon.classList.add('fa-times-circle');
            }

            toast.classList.remove('hidden');
            setTimeout(() => {
                toast.classList.add('hidden');
            }, 3000);
        }

        function handleSuccessfulResponse(id, actionText) {
            const solicitudElement = document.getElementById(`solicitud-${id}`);
            if (solicitudElement) {
                solicitudElement.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                solicitudElement.style.opacity = '0';
                solicitudElement.style.transform = 'translateX(100%)';
                setTimeout(() => {
                    solicitudElement.remove();
                    checkIfNoMoreRequests();
                }, 500);
            }
            showToast(`Solicitud ${actionText} exitosamente.`, 'success');
        }

        function checkIfNoMoreRequests() {
            const pendingContainer = document.getElementById('pending-requests');
            if (!pendingContainer.querySelector('[id^="solicitud-"]')) {
                pendingContainer.innerHTML = `
                    <div id="no-requests-message" class="text-center py-8">
                        <i class="fas fa-inbox text-5xl text-gray-400 mb-4"></i>
                        <p class="text-xl text-gray-600">No hay solicitudes pendientes.</p>
                        <p class="text-gray-500 mt-2">Todas las solicitudes han sido procesadas.</p>
                    </div>
                `;
            }
        }

        async function processRequest(id, action, body) {
            if (isProcessing) return;
            isProcessing = true;

            const btnId = action === 'aprobar' ? 'confirmApproveBtn' : 'confirmRejectBtn';
            const btn = document.getElementById(btnId);
            const originalText = btn ? btn.innerHTML : null;
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Procesando...';
            }

            try {
                const response = await fetch(`${BASE_URL}/almacen/aprobarRechazar`, {
                    method: 'POST',
                    body: body
                });

                const data = await response.json();

                if (data.status === 'success') {
                    handleSuccessfulResponse(id, action === 'aprobar' ? 'aprobada' : 'rechazada');
                } else {
                    showToast(data.message || 'Ocurrió un error inesperado.', 'error');
                }
            } catch (error) {
                console.error('Error en la solicitud:', error);
                showToast('Error de conexión. Inténtelo de nuevo.', 'error');
            } finally {
                isProcessing = false;
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                }
                if (action === 'aprobar') closeModal('approveModal');
            }
        }

        document.getElementById('confirmApproveBtn').addEventListener('click', () => {
            const formData = new FormData();
            formData.append('peticion_id', currentRequestId);
            formData.append('accion', 'aprobar');
            processRequest(currentRequestId, 'aprobar', formData);
        });

        function rejectRequest(id) {
            currentRequestId = id;
            const formData = new FormData();
            formData.append('peticion_id', id);
            formData.append('accion', 'rechazar');
            // motivo opcional eliminado
            processRequest(id, 'rechazar', formData);
        }
    </script>
</body>

</html>