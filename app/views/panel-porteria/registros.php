<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($data['titulo']); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="flex h-screen overflow-hidden">
        
        <?php require_once __DIR__ . '/includes/sidebar-porteria.php'; ?>

        <div class="flex-1 ml-64 flex flex-col overflow-y-auto main-content">
            <div class="max-w-7xl mx-auto px-4 py-6 sm:px-6 lg:px-8 w-full">
                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <div class="bg-gray-800 px-6 py-4 flex justify-between items-center">
                        <h1 class="text-2xl font-bold text-white">
                            <i class="fas fa-clipboard-list mr-2"></i> REGISTROS DE ENTRADA Y SALIDA
                        </h1>
                        <a href="<?php echo BASE_URL; ?>/porteria/informeHorario" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            <i class="fas fa-book mr-2"></i>Ver Informe
                        </a>
                    </div>

                    <div class="p-6">
                        <!-- Mensajes de Sesión -->
                        <?php if (!empty($data['mensaje'])): ?>
                            <div class="mb-4 p-4 text-sm rounded-lg bg-green-100 text-green-800">
                                <?php echo htmlspecialchars($data['mensaje']); ?>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($data['error'])): ?>
                            <div class="mb-4 p-4 text-sm rounded-lg bg-red-100 text-red-800">
                                <?php echo htmlspecialchars($data['error']); ?>
                            </div>
                        <?php endif; ?>

                        <?php if (isset($data['dia_cerrado']) && $data['dia_cerrado']): ?>
                            <div class="p-4 mb-6 text-center text-lg bg-blue-100 text-blue-800 rounded-lg">
                                <i class="fas fa-info-circle mr-2"></i>
                                Los registros de este día han sido archivados. No se pueden realizar nuevos registros.
                            </div>
                        <?php else: ?>
                            <?php if ($data['fuera_de_horario']): ?>
                                <div class="p-4 mb-6 text-center text-lg bg-yellow-100 text-yellow-800 rounded-lg">
                                    <i class="fas fa-clock mr-2"></i>
                                    El sistema de registros está disponible únicamente de 6:00 AM a 11:00 PM. Los registros están desactivados.
                                </div>
                            <?php endif; ?>

                            <!-- Formularios de Registro -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                <!-- Formulario de Ingreso -->
                                <form action="<?php echo BASE_URL; ?>/porteria/registros" method="post" class="border p-4 rounded-lg">
                                    <h2 class="text-lg font-semibold mb-3 text-gray-800">Registrar Ingreso</h2>
                                    <label for="documento_ingreso" class="block text-sm font-medium text-gray-700">Número de Documento:</label>
                                    <input type="text" name="documento_ingreso" id="documento_ingreso" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" placeholder="Documento para ingreso..." autofocus <?php if ($data['fuera_de_horario']) echo 'disabled'; ?>>
                                    <button type="submit" name="action" value="registrar_ingreso" class="mt-4 w-full inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 <?php if ($data['fuera_de_horario']) echo 'cursor-not-allowed bg-gray-400'; ?>" <?php if ($data['fuera_de_horario']) echo 'disabled'; ?>>
                                        <i class="fas fa-arrow-down mr-2"></i>Registrar Ingreso
                                    </button>
                                </form>

                                <!-- Formulario de Salida -->
                                <form action="<?php echo BASE_URL; ?>/porteria/registros" method="post" class="border p-4 rounded-lg">
                                    <h2 class="text-lg font-semibold mb-3 text-gray-800">Registrar Salida</h2>
                                    <label for="documento_salida" class="block text-sm font-medium text-gray-700">Número de Documento:</label>
                                    <input type="text" name="documento_salida" id="documento_salida" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" placeholder="Documento para salida..." <?php if ($data['fuera_de_horario']) echo 'disabled'; ?>>
                                    <button type="submit" name="action" value="registrar_salida" class="mt-4 w-full inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 <?php if ($data['fuera_de_horario']) echo 'cursor-not-allowed bg-gray-400'; ?>" <?php if ($data['fuera_de_horario']) echo 'disabled'; ?>>
                                        <i class="fas fa-arrow-up mr-2"></i>Registrar Salida
                                    </button>
                                </form>
                            </div>

                            <?php if (!$data['fuera_de_horario']): ?>
                                <!-- Tabla Única de Registros -->
                                <div class="w-full">
                                    <h2 class="text-xl font-semibold text-gray-800 mb-4"><i class="fas fa-clipboard-list mr-2 text-gray-600"></i>Registros del Día</h2>
                                    <div class="overflow-x-auto rounded-lg border border-gray-200">
                                        <div class="overflow-y-auto max-h-[50vh]">
                                            <table class="min-w-full divide-y divide-gray-200">
                                                <thead class="bg-gray-50">
                                                    <tr>
                                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sticky top-0 bg-gray-100 z-10">Usuario</th>
                                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sticky top-0 bg-gray-100 z-10">Hora Ingreso</th>
                                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sticky top-0 bg-gray-100 z-10">Hora Salida</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="bg-white divide-y divide-gray-200">
                                                    <?php if (empty($data['registros'])): ?>
                                                        <tr><td colspan="3" class="px-6 py-4 text-center text-gray-500">No hay registros para hoy.</td></tr>
                                                    <?php else: ?>
                                                        <?php foreach($data['registros'] as $registro): ?>
                                                        <tr class="hover:bg-gray-50">
                                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($registro->nombrecompletoper); ?> (<?php echo htmlspecialchars($registro->numerodoc); ?>)</td>
                                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo date('d/m/Y H:i', strtotime($registro->hora_ingreso)); ?></td>
                                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                                <?php if ($registro->hora_salida): ?>
                                                                    <?php echo date('d/m/Y H:i', strtotime($registro->hora_salida)); ?>
                                                                <?php else: ?>
                                                                    <span class="font-bold text-green-600">ACTIVO</span>
                                                                <?php endif; ?>
                                                            </td>
                                                        </tr>
                                                        <?php endforeach; ?>
                                                    <?php endif; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php require_once __DIR__ . '/includes/footer-porteria.php'; ?>
        </div>
    </div>
</body>
</html>