<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitud de Equipo - Loautech</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="flex h-screen">
        <?php include_once __DIR__ . '/../panel-usuario/includes/sidebar-usuario.php'; ?>

        <div class="flex-1 flex flex-col ml-64">
            <main class="flex-1 p-6 overflow-y-auto">
                <div class="max-w-4xl mx-auto bg-white p-8 rounded-lg shadow-md">
                    <h1 class="text-3xl font-bold text-gray-800 mb-6">Solicitud de Equipo</h1>

                    <?php if (isset($_GET['success'])): ?>
                        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                            <p class="font-bold">¡Éxito!</p>
                            <p><?php echo htmlspecialchars($_GET['success']); ?></p>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($data['error'])): ?>
                        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                            <p class="font-bold">Error</p>
                            <p><?php echo htmlspecialchars($data['error']); ?></p>
                        </div>
                    <?php endif; ?>

                    <form action="../peticion/procesar" method="POST" class="space-y-8">
                        <input type="hidden" name="idele" value="<?php echo $data['elemento']->IDele; ?>">

                        <!-- Datos Pre-cargados -->
                        <div class="bg-gray-50 p-6 rounded-lg border border-gray-200">
                            <h2 class="text-xl font-semibold text-gray-700 mb-4">Información de la Solicitud</h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Info Solicitante -->
                                <div>
                                    <h3 class="text-lg font-medium text-gray-800">Solicitante</h3>
                                    <p class="mt-1 text-gray-600"><span class="font-semibold">Nombre:</span> <?php echo htmlspecialchars(($_SESSION['nombre'] ?? '')); ?></p>
                                    <p class="mt-1 text-gray-600"><span class="font-semibold">Documento:</span> <?php echo htmlspecialchars(($_SESSION['user_documento'] ?? '')); ?></p>
                                    <p class="mt-1 text-gray-600"><span class="font-semibold">Teléfono:</span> <?php echo htmlspecialchars(($_SESSION['user_telefono'] ?? '')); ?></p>
                                </div>
                                <!-- Info Elemento -->
                                <div>
                                    <h3 class="text-lg font-medium text-gray-800">Elemento</h3>
                                    <p class="mt-1 text-gray-600"><span class="font-semibold">Nombre:</span> <?php echo htmlspecialchars($data['elemento']->nombreele); ?></p>
                                    <p class="mt-1 text-gray-600"><span class="font-semibold">Código:</span> <?php echo htmlspecialchars($data['elemento']->codigoele); ?></p>
                                    <p class="mt-1 text-gray-600"><span class="font-semibold">Disponibles:</span> <?php echo htmlspecialchars($data['elemento']->cantidadele); ?></p>
                                </div>
                            </div>
                        </div>

                        <!-- Campos a Llenar por el Usuario -->
                        <div class="pt-8">
                            <h2 class="text-xl font-semibold text-gray-700">Completa los Detalles del Préstamo</h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                                <div>
                                    <label for="cargo" class="block text-sm font-medium text-gray-700">Tu Cargo</label>
                                    <select name="cargo" id="cargo" class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                                        <option value="" disabled selected>Selecciona tu cargo</option>
                                        <option value="funcionario">Funcionario</option>
                                        <option value="instructor">Instructor</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="cantidad_solicitada" class="block text-sm font-medium text-gray-700">Cantidad a Solicitar</label>
                                    <input type="number" name="cantidad_solicitada" id="cantidad_solicitada" min="1" max="<?php echo $data['elemento']->cantidadele; ?>" value="1" class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                                </div>
                                <div>
                                    <label for="fecha_prestamo" class="block text-sm font-medium text-gray-700">Fecha de Préstamo</label>
                                    <input type="date" name="fecha_prestamo" id="fecha_prestamo" value="<?php echo date('Y-m-d'); ?>" class="mt-1 block w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-md shadow-sm focus:outline-none sm:text-sm" readonly>
                                </div>
                                <div>
                                    <label for="fecha_devolucion" class="block text-sm font-medium text-gray-700">Fecha de Devolución</label>
                                    <input type="date" name="fecha_devolucion" id="fecha_devolucion" class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                                </div>
                                <div class="md:col-span-2">
                                    <label for="lugar_uso" class="block text-sm font-medium text-gray-700">Lugar de Uso</label>
                                    <input type="text" name="lugar_uso" id="lugar_uso" placeholder="Ej: Sala de conferencias, Oficina 301" class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                                </div>
                                <div class="md:col-span-2">
                                    <label for="razon_descripcion" class="block text-sm font-medium text-gray-700">Razón/Descripción</label>
                                    <textarea name="razon_descripcion" id="razon_descripcion" rows="4" placeholder="Explique brevemente el motivo de la solicitud." class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Términos y Condiciones -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-800">Términos y Condiciones</h3>
                            <div class="mt-2 p-4 border rounded-md max-h-32 overflow-y-auto bg-gray-50 text-sm text-gray-600">
                                <p>Al solicitar este equipo, usted se compromete a cuidarlo y devolverlo en las mismas condiciones en que lo recibió. Cualquier daño o pérdida será su responsabilidad y podría incurrir en costos de reparación o reemplazo. El equipo debe ser devuelto en la fecha acordada.</p>
                            </div>
                            <div class="mt-4 flex items-center">
                                <input type="checkbox" name="acepta_terminos" id="acepta_terminos" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded" required>
                                <label for="acepta_terminos" class="ml-2 block text-sm text-gray-900">Acepto los términos y condiciones</label>
                            </div>
                        </div>

                        <!-- Botones de Acción -->
                        <div class="flex items-center justify-end space-x-4 pt-6 border-t">
                            <a href="../usuario/inventario" class="px-6 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 transition-colors">Cancelar</a>
                            <button type="submit" class="px-6 py-2 bg-blue-600 text-white font-semibold rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">Enviar Solicitud</button>
                        </div>
                    </form>
                </div>
            </main>

            <footer class="bg-white p-4 text-center text-sm text-gray-600 border-t">
                © <?php echo date('Y'); ?> Loautech. Todos los derechos reservados.
            </footer>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const fechaPrestamo = document.getElementById('fecha_prestamo');
            const fechaDevolucion = document.getElementById('fecha_devolucion');
            const hoy = new Date().toISOString().split('T')[0];
            
            if (fechaPrestamo) {
                fechaPrestamo.min = hoy;
                
                fechaPrestamo.addEventListener('change', function() {
                    if (fechaDevolucion) {
                        fechaDevolucion.min = this.value;
                        if (fechaDevolucion.value && fechaDevolucion.value < this.value) {
                            fechaDevolucion.value = this.value;
                        }
                    }
                });
            }
            
            if (fechaDevolucion) {
                fechaDevolucion.addEventListener('change', function() {
                    if (fechaPrestamo && this.value < fechaPrestamo.value) {
                        this.value = fechaPrestamo.value;
                    }
                });
            }
        });
    </script>
</body>
</html>