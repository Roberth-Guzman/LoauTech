<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informe de Horarios - Portería</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/css/porteria/informe-horario.css">
    
    <!-- Librerías para exportación (usadas por los módulos JS) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.23/jspdf-autotable.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
</head>
<body class="bg-gray-100 font-sans">
    <?php 
        include_once __DIR__ . '/includes/sidebar-porteria.php'; 
        ?>
        <div class="main-content" style="margin-left: 16rem;">
        
        <div class="flex-1">
            <main class="p-4 sm:p-6">
                <div class="container mx-auto">
                    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                        <div class="px-6 py-4 bg-gray-800 text-white">
                            <h1 class="text-2xl font-bold">Informe de Horarios</h1>
                        <p class="text-sm opacity-80">Generado el <?php echo date('d/m/Y \a \l\a\s H:i'); ?></p>
                    </div>

                    <p class="text-sm opacity-80">Generado el <?php echo date('d/m/Y \a \l\a\s H:i'); ?></p>
                    </div>

                    <!-- Controles de Paginación por Día y Filtro -->
                    <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                        <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                            <!-- Controles de Navegación de Fecha -->
                            <div class="flex items-center gap-2">
                                <a href="<?php echo $data['fecha_anterior_url']; ?>" 
                                   class="px-4 py-2 bg-gray-300 text-gray-800 rounded-md hover:bg-gray-400"
                                   title="Día Anterior">
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                                <div class="text-center px-4">
                                    <span class="text-lg font-semibold text-gray-700">
                                        <?php echo (new DateTime($data['fecha_mostrada']))->format('d/m/Y'); ?>
                                    </span>
                                </div>
                                <a href="<?php echo $data['fecha_siguiente_url'] ?? 'javascript:void(0);'; ?>" 
                                   class="px-4 py-2 bg-gray-300 text-gray-800 rounded-md hover:bg-gray-400 <?php echo empty($data['fecha_siguiente_url']) ? 'opacity-50 cursor-not-allowed' : ''; ?>"
                                   title="Día Siguiente">
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            </div>

                            <!-- Filtros y Selector de Fecha -->
                            <form method="GET" action="<?php echo BASE_URL . 'porteria/informeHorario'; ?>" class="flex flex-col sm:flex-row items-end gap-3">
                                <!-- Selector de Fecha Específica -->
                                <div>
                                    <label for="fecha_especifica" class="block text-sm font-medium text-gray-700 mb-1">Ir a fecha</label>
                                    <input type="date" id="fecha_especifica" name="fecha_especifica" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" value="<?php echo htmlspecialchars($data['fecha_mostrada']); ?>" max="<?php echo date('Y-m-d'); ?>">
                                </div>

                                <!-- Filtro por Tipo de Elemento -->
                                <div class="w-full sm:w-48">
                                    <label for="tipo-elemento" class="block text-sm font-medium text-gray-700 mb-1">Tipo de Elemento</label>
                                    <select id="tipo-elemento" name="tipo_elemento" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="todos">Todos los tipos</option>
                                        <?php if (!empty($data['tipos_elementos'])): ?>
                                            <?php foreach ($data['tipos_elementos'] as $tipo): ?>
                                                <option value="<?php echo htmlspecialchars($tipo->tipoelemento); ?>" <?php echo (isset($data['tipo_elemento_seleccionado']) && $data['tipo_elemento_seleccionado'] === $tipo->tipoelemento) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars(ucfirst($tipo->tipoelemento)); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>

                                <!-- Botones de Formulario -->
                                <div class="flex gap-2">
                                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md" title="Aplicar filtros">
                                        <i class="fas fa-search"></i>
                                    </button>
                                    <a href="<?php echo BASE_URL . 'porteria/informeHorario?fecha_especifica=' . $data['fecha_mostrada']; ?>" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md" title="Limpiar filtro de tipo">
                                        <i class="fas fa-sync-alt"></i>
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Resumen -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 p-6 bg-gray-50 border-b border-gray-200">
                        <div class="bg-white p-4 rounded-lg shadow">
                            <div class="flex items-center">
                                <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
                                    <i class="fas fa-laptop fa-lg"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Elementos Totales</p>
                                    <p class="text-2xl font-bold text-gray-800"><?php echo $data['total_registros']; ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="bg-white p-4 rounded-lg shadow">
                            <div class="flex items-center">
                                <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
                                    <i class="fas fa-check-circle fa-lg"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Registros con Salida</p>
                                    <p class="text-2xl font-bold text-gray-800"><?php echo $data['stats']['con_salida']; ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="bg-white p-4 rounded-lg shadow">
                            <div class="flex items-center">
                                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600 mr-4">
                                    <i class="fas fa-exclamation-triangle fa-lg"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Pendientes de Salida</p>
                                    <p class="text-2xl font-bold text-gray-800"><?php echo $data['stats']['pendientes']; ?></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Contenido principal con tarjetas -->
                    <div class="p-6">
                        <!-- Encabezado con acciones -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <h2 class="text-lg font-semibold text-gray-800">
            <i class="fas fa-th-large mr-2 text-indigo-600"></i> Elementos Registrados
            <span class="text-sm font-normal text-gray-500" id="contador-registros">(<?php echo $data['total_registros']; ?> elementos)</span>
        </h2>

        <div class="flex gap-2">
            <?php
            // Prepara los parámetros para las URLs de exportación
            $export_params = http_build_query([
                'fecha' => $data['fecha_mostrada'],
                'tipo_elemento' => $data['tipo_elemento_seleccionado']
            ]);
            ?>

            <!-- Botón para PDF -->
            <a href="<?php echo BASE_URL; ?>/porteria/exportarInforme/pdf?<?php echo $export_params; ?>" target="_blank" id="btn-exportar-pdf" class="px-3 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 flex items-center no-print">
                <i class="fas fa-file-pdf mr-1"></i> PDF
            </a>

            <!-- Botón para Excel (CSV) -->
            <a href="<?php echo BASE_URL; ?>/porteria/exportarInforme/csv?<?php echo $export_params; ?>" id="btn-exportar-excel" class="px-3 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 flex items-center no-print">
                <i class="fas fa-file-excel mr-1"></i> Excel
            </a>
        </div>
    </div>
                        
                        <!-- Grid de tarjetas -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="contenedor-elementos">
                            <?php if (!empty($data['registros'])): ?>
                                <?php foreach ($data['registros'] as $registro): 
                                    $estadoClases = [
                                        'Registrado' => 'bg-green-100 text-green-800',
                                        'Pendiente' => 'bg-yellow-100 text-yellow-800',
                                    ][$registro->estado] ?? 'bg-gray-100 text-gray-800';
                                ?>
                                <div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-200 hover:shadow-lg transition-shadow duration-300 elemento-card" 
                                     data-codigo="<?php echo htmlspecialchars(strtolower($registro->codigo ?? '')); ?>"
                                     data-tipo="<?php echo htmlspecialchars(strtolower($registro->tipo)); ?>"
                                     data-marca="<?php echo htmlspecialchars(strtolower($registro->marca)); ?>"
                                     data-modelo="<?php echo htmlspecialchars(strtolower($registro->modelo)); ?>">
                                    <div class="p-5">
                                        <div class="flex justify-between items-start mb-3">
                                            <div>
                                                <h3 class="text-lg font-semibold text-gray-900 mb-1">
                                                    <?php echo htmlspecialchars($registro->nombre); ?>
                                                </h3>
                                                <p class="text-sm text-gray-500">
                                                    <i class="far fa-clock mr-1"></i> <?php echo (new DateTime($registro->hora_registro))->format('h:i A'); ?>
                                                </p>
                                            </div>
                                            <span class="px-2 py-1 text-xs font-medium rounded-full <?php echo $estadoClases; ?>">
                                                <?php echo $registro->estado; ?>
                                            </span>
                                        </div>
                                        
                                        <div class="mt-4 space-y-2 text-sm">
                                            <p class="flex items-center">
                                                <span class="w-24 text-gray-500">Código:</span>
                                                <span class="font-medium"><?php echo htmlspecialchars($registro->codigo ?? 'Sin código'); ?></span>
                                            </p>
                                            <p class="flex items-center">
                                                <span class="w-24 text-gray-500">Descripción:</span>
                                                <span class="font-medium"><?php echo htmlspecialchars($registro->marca ?? 'N/A'); ?></span>
                                            </p>
                                            <p class="flex items-center">
                                                <span class="w-24 text-gray-500">Observación:</span>
                                                <span class="font-medium"><?php echo htmlspecialchars($registro->modelo ?? 'N/A'); ?></span>
                                            </p>
                                        </div>
                                        
                                        <div class="mt-4 pt-3 border-t border-gray-100 flex justify-between items-center">
                                            <button class="ver-detalle text-sm text-indigo-600 hover:text-indigo-800 font-medium"
                                                    data-codigo="<?php echo htmlspecialchars($registro->codigo ?? ''); ?>"
                                                    data-tipo="<?php echo htmlspecialchars($registro->tipo); ?>"
                                                    data-marca="<?php echo htmlspecialchars($registro->marca); ?>"
                                                    data-modelo="<?php echo htmlspecialchars($registro->modelo); ?>"
                                                    data-hora="<?php echo (new DateTime($registro->hora_registro))->format('h:i A'); ?>"
                                                    data-estado="<?php echo $registro->estado; ?>"
                                                    data-observaciones="<?php echo htmlspecialchars($registro->observaciones ?? ''); ?>"
                                                    data-nombre-persona="<?php echo htmlspecialchars($registro->nombrecompletoper ?? 'No disponible'); ?>"
                                                    data-documento-persona="<?php echo htmlspecialchars($registro->numerodoc ?? 'No disponible'); ?>">
                                                <i class="fas fa-eye mr-1"></i> Ver detalles
                                            </button>
                                            <span class="text-xs text-gray-400">
                                                ID: <?php echo $registro->id; ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="col-span-1 md:col-span-2 lg:col-span-3 text-center py-12">
                                    <i class="fas fa-folder-open fa-4x text-gray-300"></i>
                                    <p class="mt-4 text-lg text-gray-500">No se encontraron registros para la fecha y filtros seleccionados.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Detalles -->
    <div id="modal-detalles" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl overflow-hidden">
            <div class="flex justify-between items-center px-6 py-4 border-b bg-gray-50">
                <h3 class="text-lg font-semibold text-gray-800">Detalles del Elemento</h3>
                <button id="cerrar-modal" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times fa-lg"></i>
                </button>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                    <div>
                        <p class="text-sm text-gray-500">Código:</p>
                        <p class="font-medium text-gray-800" id="detalle-codigo"></p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Tipo:</p>
                        <p class="font-medium text-gray-800" id="detalle-tipo"></p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Marca:</p>
                        <p class="font-medium text-gray-800" id="detalle-marca"></p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Modelo:</p>
                        <p class="font-medium text-gray-800" id="detalle-modelo"></p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Hora de Registro:</p>
                        <p class="font-medium text-gray-800" id="detalle-hora"></p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Estado:</p>
                        <div id="detalle-estado-contenedor">
                            <span id="detalle-estado"></span>
                        </div>
                    </div>
                </div>
                <div class="mt-4">
                    <p class="text-sm text-gray-500">Observaciones:</p>
                    <p class="font-medium text-gray-800 bg-gray-50 p-3 rounded-md border" id="detalle-observaciones"></p>
                </div>
                
                <div class="bg-gray-50 p-4 rounded-lg mt-6 border-t">
                    <h4 class="font-semibold text-gray-700 mb-3">Información de la Persona que Registra</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">Nombre:</p>
                            <p class="font-medium text-gray-800" id="detalle-nombre-persona"></p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Documento:</p>
                            <p class="font-medium text-gray-800" id="detalle-documento-persona"></p>
                        </div>
                    </div>
                </div>  

            </div>
            <div class="px-6 py-3 bg-gray-100 text-right border-t">
                <button id="btn-cerrar-modal" class="px-5 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">Cerrar</button>
            </div>
        </div>
    </div>

    <!-- Scripts de la aplicación -->
    <script type="module" src="<?php echo BASE_URL; ?>/public/js/porteria/informe-horario.js"></script>

</body>
</html>