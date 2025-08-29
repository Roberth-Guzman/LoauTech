<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($data['titulo'] ?? 'Control de Portería'); ?> - LOAUTECH</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        /* Animación para las tarjetas al cargar */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .tarjeta-animada {
            animation: fadeInUp 0.5s ease-out forwards;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Barra de navegación -->
    <nav class="bg-gray-800 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="<?php echo BASE_URL; ?>/porteria/index" class="flex-shrink-0 flex items-center">
                        <i class="fas fa-arrow-left text-xl mr-2"></i>
                        <span class="text-xl font-bold">LOAUTECH</span>
                    </a>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="relative">
                        <i class="fas fa-bell text-xl"></i>
                        <span class="absolute -top-2 -right-2 bg-green-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs">
                            <?php echo htmlspecialchars($data['totalPeticionesAprobadas'] ?? 0); ?>
                        </span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-user-circle"></i>
                        <span><?php echo htmlspecialchars($data['nombre_usuario'] ?? 'Usuario'); ?></span>
                    </div>
                    <a href="<?php echo BASE_URL; ?>/logout" class="text-gray-300 hover:bg-gray-700 hover:text-white px-3 py-2 rounded-md text-sm font-medium">
                        <i class="fas fa-sign-out-alt mr-1"></i> Cerrar Sesión
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Contenido principal -->
    <div class="max-w-7xl mx-auto px-4 py-6 sm:px-6 lg:px-8">
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <!-- Encabezado -->
            <div class="bg-gradient-to-r from-blue-600 to-blue-800 px-6 py-4">
                <h1 class="text-2xl font-bold text-white">
                    <i class="fas fa-clipboard-check mr-2"></i> AUTORIZACIÓN DE SALIDAS
                </h1>
            </div>

            <!-- Alerta -->
            <div class="m-6 bg-green-50 border-l-4 border-green-500 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle text-green-500 text-xl"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-green-800">Elementos Listos para Salida</h3>
                        <div class="mt-1 text-sm text-green-700">
                            <p>
                                <?php
                                $totalPeticiones = $data['totalPeticionesAprobadas'] ?? 0;
                                if ($totalPeticiones > 0) {
                                    echo "Hay <strong>{$totalPeticiones}</strong> elemento(s) confirmado(s) por inventario listo(s) para autorizar su salida.";
                                } else {
                                    echo "No hay elementos listos para salida en este momento.";
                                }
                                ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contenido Principal -->
            <div class="p-6">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Columna Izquierda: Tarjetas de Solicitud -->
                    <div class="lg:col-span-2 space-y-6">
                        <?php if (!empty($data['peticionesAprobadas'])): ?>
                            <?php foreach($data['peticionesAprobadas'] as $peticion): ?>
                                <div class="bg-white border border-green-200 rounded-lg shadow-sm overflow-hidden tarjeta-animada">
                                    <div class="bg-green-600 px-4 py-3">
                                        <h3 class="text-lg font-semibold text-white flex items-center">
                                            <i class="fas fa-box-open mr-2"></i>
                                            SOLICITUD #<?php echo htmlspecialchars($peticion->IDpre); ?> - LISTA PARA SALIDA
                                        </h3>
                                    </div>
                                    <div class="p-4">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                            <div class="space-y-2">
                                                <p class="text-sm"><span class="font-medium text-gray-700">Solicitante:</span> <span class="text-gray-900"><?php echo htmlspecialchars($peticion->nombrecompletoper); ?></span></p>
                                                <p class="text-sm"><span class="font-medium text-gray-700">Elemento:</span> <span class="text-gray-900"><?php echo htmlspecialchars($peticion->nombreele); ?></span></p>
                                                <p class="text-sm"><span class="font-medium text-gray-700">Código:</span> <span class="font-mono bg-gray-100 px-2 py-0.5 rounded"><?php echo htmlspecialchars($peticion->codigoinventario); ?></span></p>
                                            </div>
                                            <div class="space-y-2">
                                                <p class="text-sm"><span class="font-medium text-gray-700">Aprobada por:</span> <span class="text-gray-900">Inventario</span></p>
                                                <p class="text-sm"><span class="font-medium text-gray-700">Estado:</span> <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800"><i class="fas fa-check-circle mr-1"></i> Lista para salida</span></p>
                                                <p class="text-sm"><span class="font-medium text-gray-700">Lugar de traslado:</span> <span class="text-gray-900"><?php echo htmlspecialchars($peticion->lugardetraslado); ?></span></p>
                                            </div>
                                        </div>
                                        <hr class="my-4 border-gray-200">
                                        <form method="POST" action="<?php echo BASE_URL; ?>/porteria/registrarSalida" class="space-y-3">
                                            <input type="hidden" name="id_prestamo" value="<?php echo htmlspecialchars($peticion->IDpre); ?>">
                                            <div>
                                                <label for="vigilante_<?php echo $peticion->IDpre; ?>" class="block text-sm font-medium text-gray-700 mb-1">Vigilante Responsable:</label>
                                                <input type="text" name="nombre_vigilante" id="vigilante_<?php echo $peticion->IDpre; ?>" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" value="<?php echo htmlspecialchars($data['nombre_usuario']); ?>" required>
                                            </div>
                                            <div>
                                                <label for="horaSalida_<?php echo $peticion->IDpre; ?>" class="block text-sm font-medium text-gray-700 mb-1">Hora de Salida:</label>
                                                <input type="datetime-local" name="hora_salida" id="horaSalida_<?php echo $peticion->IDpre; ?>" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" required>
                                            </div>
                                            <div>
                                                <label for="observaciones_<?php echo $peticion->IDpre; ?>" class="block text-sm font-medium text-gray-700 mb-1">Observaciones de Salida:</label>
                                                <textarea name="observaciones" id="observaciones_<?php echo $peticion->IDpre; ?>" rows="2" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"></textarea>
                                            </div>
                                            <div class="mt-6 flex justify-end">
                                                <button type="submit" class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                                    <i class="fas fa-check-circle mr-2"></i> Autorizar Salida
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            <?php endforeach; ?>

                            <!-- PAGINACIÓN -->
                            <?php if (($data['totalPaginas'] ?? 1) > 1): ?>
                                <div class="flex justify-between items-center mt-6">
                                    <!-- Botón Anterior -->
                                    <?php if ($data['paginaActual'] > 1): ?>
                                        <a href="<?php echo BASE_URL; ?>/porteria/peticiones/<?php echo $data['paginaActual'] - 1; ?>" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                            <i class="fas fa-arrow-left mr-2"></i>
                                            Anterior
                                        </a>
                                    <?php else: ?>
                                        <div class="w-28"></div> <!-- Placeholder para mantener el espacio -->
                                    <?php endif; ?>

                                    <!-- Indicador de Página -->
                                    <span class="text-sm text-gray-700">
                                        Página <?php echo $data['paginaActual']; ?> de <?php echo $data['totalPaginas']; ?>
                                    </span>

                                    <!-- Botón Siguiente -->
                                    <?php if ($data['paginaActual'] < $data['totalPaginas']): ?>
                                        <a href="<?php echo BASE_URL; ?>/porteria/peticiones/<?php echo $data['paginaActual'] + 1; ?>" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                            Siguiente
                                            <i class="fas fa-arrow-right ml-2"></i>
                                        </a>
                                    <?php else: ?>
                                        <div class="w-28"></div> <!-- Placeholder para mantener el espacio -->
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>

                        <?php else: ?>
                            <div class="text-center py-10 px-4 bg-gray-50 rounded-lg">
                                <i class="fas fa-info-circle text-4xl text-blue-400"></i>
                                <p class="mt-4 text-lg font-medium text-gray-700">No hay peticiones aprobadas pendientes de salida.</p>
                                <p class="text-gray-500">Cuando un administrador apruebe una solicitud, aparecerá aquí.</p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Columna Derecha: Registro de Salidas del Día -->
                    <div class="lg:col-span-1">
                        <div class="bg-white rounded-xl shadow-md p-4 border border-gray-200">
                            <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                                <i class="fas fa-history mr-2 text-blue-500"></i> Registro de Salidas del Día
                            </h3>
                            <div class="overflow-auto" style="max-height: 500px;">
                                <table class="min-w-full bg-white">
                                    <thead class="bg-gray-100 sticky top-0">
                                        <tr>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Elemento</th>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hora</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        <?php if (!empty($data['salidasHoy'])): ?>
                                            <?php foreach ($data['salidasHoy'] as $salida): ?>
                                                <tr>
                                                    <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-700">
                                                        <p class="font-semibold"><?php echo htmlspecialchars($salida->nombreele); ?></p>
                                                        <p class="text-xs text-gray-500">ID: <?php echo htmlspecialchars($salida->IDpre ?? 'N/A'); ?> | Sol: <?php echo htmlspecialchars($salida->nombrecompletoper); ?></p>
                                                    </td>
                                                    <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-700"><?php echo htmlspecialchars(date("h:i A", strtotime($salida->horasalida))); ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="2" class="text-center py-4 text-sm text-gray-500">No hay salidas registradas hoy.</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>