<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Devoluciones - Panel de Portería</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="flex">
        <!-- Sidebar -->
        <?php include 'includes/sidebar-porteria.php'; ?>

        <!-- Contenido Principal -->
        <main class="flex-1 p-8 ml-64">
            <h1 class="text-3xl font-bold mb-6 text-gray-800">Registro de Devoluciones</h1>
            
            <!-- Tabla de Préstamos Activos -->
            <div class="bg-white shadow-md rounded-lg p-6">
                <h2 class="text-xl font-semibold mb-4 text-gray-700">Préstamos Activos</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white">
                        <thead class="bg-gray-800 text-white">
                            <tr>
                                <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Elemento</th>
                                <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Cantidad</th>
                                <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Solicitante</th>
                                <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Fecha de Salida</th>
                                <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-700">
                            <?php if (!empty($data['prestamos_activos'])): ?>
                                <?php foreach ($data['prestamos_activos'] as $prestamo): ?>
                                    <tr class="border-b border-gray-200 hover:bg-gray-100">
                                        <td class="py-3 px-4"><?php echo htmlspecialchars($prestamo->nombre_elemento); ?></td>
                                        <td class="py-3 px-4"><?php echo htmlspecialchars($prestamo->cantidad); ?></td>
                                        <td class="py-3 px-4"><?php echo htmlspecialchars($prestamo->nombre_persona); ?></td>
                                        <td class="py-3 px-4"><?php echo htmlspecialchars(date('d/m/Y H:i', strtotime($prestamo->fecha_salida))); ?></td>
                                        <td class="py-3 px-4">
                                            <button 
                                                onclick="openModal('<?php echo $prestamo->IDpre; ?>')"
                                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition-colors duration-200">
                                                Registrar
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr class="border-b border-gray-200">
                                    <td colspan="5" class="py-3 px-4 text-center text-gray-500">No hay préstamos activos en este momento.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal para Registrar Devolución -->
    <div id="devolucionModal" class="fixed inset-0 bg-gray-900 bg-opacity-75 hidden items-center justify-center z-50 transition-opacity duration-300">
        <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md mx-auto transform transition-all scale-95">
            <div class="flex justify-between items-center border-b pb-3">
                <h3 class="text-xl font-semibold text-gray-800">Registrar Devolución</h3>
                <button onclick="closeModal()" class="text-gray-500 hover:text-gray-800 text-3xl font-light">&times;</button>
            </div>
            <form id="devolucionForm" action="<?php echo BASE_URL; ?>/porteria/registrarDevolucion" method="post" class="mt-4">
                <input type="hidden" id="id_prestamo" name="id_prestamo">
                <div class="mb-4">
                    <label for="observaciones_devolucion" class="block text-gray-700 text-sm font-bold mb-2">Observaciones de la Devolución:</label>
                    <textarea 
                        id="observaciones_devolucion" 
                        name="observaciones_devolucion" 
                        rows="4" 
                        class="shadow-sm appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500" 
                        placeholder="Ej: El equipo se devuelve en buen estado."></textarea>
                </div>
                <div class="flex items-center justify-end pt-4 border-t">
                    <button 
                        type="button" 
                        onclick="closeModal()"
                        class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded mr-2 transition-colors duration-200">
                        Cancelar
                    </button>
                    <button 
                        type="submit" 
                        class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded transition-colors duration-200">
                        Confirmar Devolución
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const modal = document.getElementById('devolucionModal');
        const modalContent = modal.querySelector('.transform');
        const form = document.getElementById('devolucionForm');
        const prestamoIdInput = document.getElementById('id_prestamo');

        function openModal(prestamoId) {
            prestamoIdInput.value = prestamoId;
            modal.classList.remove('hidden');
            void modal.offsetWidth;
            modal.classList.remove('bg-opacity-0');
            modal.classList.add('bg-opacity-75');
            modalContent.classList.remove('scale-95');
            modalContent.classList.add('scale-100');
        }

        function closeModal() {
            modal.classList.add('bg-opacity-0');
            modalContent.classList.add('scale-95');
            setTimeout(() => {
                modal.classList.add('hidden');
                form.reset();
            }, 300);
        }

        window.addEventListener('click', function(event) {
            if (event.target == modal) {
                closeModal();
            }
        });
    </script>

    <?php include 'includes/footer-porteria.php'; ?>
</body>
</html>