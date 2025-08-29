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
                        <?php foreach ($data['solicitudes'] as $peticion) : ?>
                            <div id="solicitud-<?= $peticion->peticion_id ?>" class="p-4 rounded-lg border-l-4 border-yellow-500 bg-gray-50 flex justify-between items-center">
                                <div>
                                    <p class="font-bold text-gray-800">Solicitante: <span class="font-normal"><?= htmlspecialchars($peticion->solicitante_nombre) ?></span></p>
                                    <p class="text-sm text-gray-600">Elemento: <span class="font-semibold"><?= htmlspecialchars($peticion->nombreele) ?></span> (Cantidad: <?= $peticion->cantidad ?>)</p>
                                    <p class="text-sm text-gray-600">Destino: <span class="font-normal"><?= htmlspecialchars($peticion->lugardetraslado) ?></span></p>
                                </div>
                                <div class="text-right">
                                    <button onclick="openApproveModal(<?= $peticion->peticion_id ?>)" class="bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600 transition-transform transform hover:scale-105">Aprobar</button>
                                    <button onclick="openRejectModal(<?= $peticion->peticion_id ?>)" class="bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-600 transition-transform transform hover:scale-105 ml-2">Rechazar</button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <div id="no-requests-message" class="text-center py-8">
                            <i class="fas fa-inbox text-5xl text-gray-400 mb-4"></i>
                            <p class="text-xl text-gray-600">No hay solicitudes pendientes.</p>
                            <p class="text-gray-500 mt-2">Todas las solicitudes han sido procesadas.</p>
                        </div>
                    <?php endif; ?>
                </div>
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
            <form id="rejectForm">
                <div class="mb-4">
                    <label for="motivo" class="block text-sm font-medium text-gray-700">Motivo del Rechazo</label>
                    <textarea id="motivo" name="motivo" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required></textarea>
                </div>
                <div class="mt-6 flex justify-end space-x-4">
                    <button type="button" onclick="closeModal('rejectModal')" class="bg-gray-300 text-gray-800 px-4 py-2 rounded-md hover:bg-gray-400">Cancelar</button>
                    <button id="confirmRejectBtn" type="submit" class="bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-600">Rechazar</button>
                </div>
            </form>
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

        function openRejectModal(id) {
            currentRequestId = id;
            document.getElementById('motivo').value = '';
            openModal('rejectModal');
        }

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
            const originalText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Procesando...';

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
                btn.disabled = false;
                btn.innerHTML = originalText;
                closeModal(action === 'aprobar' ? 'approveModal' : 'rejectModal');
            }
        }

        document.getElementById('confirmApproveBtn').addEventListener('click', () => {
            const formData = new FormData();
            formData.append('peticion_id', currentRequestId);
            formData.append('accion', 'aprobar');
            processRequest(currentRequestId, 'aprobar', formData);
        });

        document.getElementById('rejectForm').addEventListener('submit', (e) => {
            e.preventDefault();
            const motivo = document.getElementById('motivo').value;
            if (motivo.trim()) {
                const formData = new FormData();
                formData.append('peticion_id', currentRequestId);
                formData.append('motivo', motivo);
                formData.append('accion', 'rechazar');
                processRequest(currentRequestId, 'rechazar', formData);
            } else {
                showToast('Por favor, ingrese un motivo para el rechazo.', 'error');
            }
        });
    </script>
</body>
</html>