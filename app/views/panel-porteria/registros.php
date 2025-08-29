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

        <!-- Contenido Principal -->
        <div class="flex-1 ml-64 flex flex-col overflow-y-auto main-content">
            <div class="max-w-7xl mx-auto px-4 py-6 sm:px-6 lg:px-8 w-full">
                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <!-- Encabezado -->
                    <div class="bg-gray-800 px-6 py-4">
                        <h1 class="text-2xl font-bold text-white text-center">
                            <i class="fas fa-clipboard-list mr-2"></i> REGISTROS DE ELEMENTOS
                        </h1>
                    </div>

                    <!-- Cuerpo -->
                    <div class="p-6">
                        <div class="mb-6">
                            <h2 class="text-xl font-semibold text-gray-800 mb-4">
                                <i class="fas fa-list-ul mr-2 text-gray-600"></i> Listado de Elementos Registrados
                            </h2>
                            
                            <div class="overflow-x-auto rounded-lg border border-gray-200">
                                <div class="overflow-y-auto max-h-[70vh]">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sticky top-0 bg-gray-100 z-10">#</th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sticky top-0 bg-gray-100 z-10">Nombre Persona</th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sticky top-0 bg-gray-100 z-10">Documento</th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sticky top-0 bg-gray-100 z-10">Elemento</th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sticky top-0 bg-gray-100 z-10">Tipo</th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sticky top-0 bg-gray-100 z-10">Descripción</th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sticky top-0 bg-gray-100 z-10">Observaciones</th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sticky top-0 bg-gray-100 z-10">Fecha Ingreso</th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sticky top-0 bg-gray-100 z-10">Fecha Salida</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                        <?php if (!empty($data['registros'])): ?>
                                            <?php $contador = 1; ?>
                                            <?php foreach($data['registros'] as $registro): ?>
                                            <tr class="hover:bg-gray-50 transition-colors">
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?php echo $contador++; ?></td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo htmlspecialchars($registro->nombrecompletoper); ?></td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($registro->numerodoc); ?></td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($registro->nombreingele); ?></td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-200 text-gray-800">
                                                        <?php echo htmlspecialchars($registro->tipoelemento); ?>
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 text-sm text-gray-500"><?php echo htmlspecialchars($registro->descripcioningele); ?></td>
                                                <td class="px-6 py-4 text-sm text-gray-500"><?php echo htmlspecialchars($registro->observacioningele); ?></td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    <div class="text-gray-900"><?php echo date('d/m/Y', strtotime($registro->hora_entrada)); ?></div>
                                                    <div class="text-gray-500"><?php echo date('H:i', strtotime($registro->hora_entrada)); ?></div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    <?php if ($registro->hora_salida): ?>
                                                        <div class="text-gray-900"><?php echo date('d/m/Y', strtotime($registro->hora_salida)); ?></div>
                                                        <div class="text-gray-500"><?php echo date('H:i', strtotime($registro->hora_salida)); ?></div>
                                                    <?php else: ?>
                                                        <span class="text-red-500 font-semibold">Sin retirar</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                        <tr>
                                            <td colspan="9" class="text-center py-4">
                                                <div class="text-gray-500">No hay registros de elementos ingresados</div>
                                            </td>
                                        </tr>
                                        <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="mt-4 flex flex-col sm:flex-row justify-between items-center px-6 py-4 bg-gray-50 border-t border-gray-200 rounded-b-lg">
                            <div class="text-sm text-gray-500 mb-2 sm:mb-0">
                                Mostrando <span class="font-medium text-gray-700"><?php echo count($data['registros']); ?></span> registros
                            </div>
                            <a href="<?php echo BASE_URL; ?>/porteria" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-gray-700 hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors">
                                <i class="fas fa-arrow-left mr-2"></i> Volver al Panel Principal
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php require_once __DIR__ . '/includes/footer-porteria.php'; ?>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
</body>
</html>