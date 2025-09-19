<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $data['titulo']; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="flex h-screen">
        <?php include __DIR__ . '/includes/sidebar-usuario.php'; ?>
        <!-- Contenido principal -->
        <div class="flex-1 ml-64 overflow-auto">
            <!-- Header -->
            <div class="bg-white shadow-sm">
                <div class="max-w-7xl mx-auto px-4 py-4 sm:px-6 lg:px-8 flex justify-between items-center">
                    <h1 class="text-xl font-bold text-gray-900">
                        <?php echo $data['titulo']; ?>
                    </h1>
                    <div class="flex items-center space-x-4">
                        <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                            <?php echo ucfirst(htmlspecialchars($_SESSION['usuario']['rol'] ?? 'usuario')); ?>
                        </span>
                        <a href="../logout" class="text-gray-500 hover:text-red-600" title="Cerrar Sesión">
                            <i class="fas fa-sign-out-alt text-lg"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Contenido -->
            <main class="p-6">
                <?php if (isset($data['success']) && $data['success'] === 'peticion_enviada'): ?>
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded" role="alert">
                        <div class="flex">
                            <div class="py-1">
                                <i class="fas fa-check-circle text-green-500 mr-2"></i>
                            </div>
                            <div>
                                <p class="font-bold">¡Petición enviada exitosamente!</p>    
                                <p class="text-sm">Tu solicitud ha sido registrada y está pendiente de autorización.</p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($data['error'])): ?>
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded" role="alert">
                        <div class="flex">
                            <div class="py-1">
                                <i class="fas fa-exclamation-triangle text-red-500 mr-2"></i>
                            </div>
                            <div>
                                <p class="font-bold">Error</p>
                                <p class="text-sm">
                                    <?php 
                                    switch($data['error']) {
                                        case 'elemento_no_disponible':
                                            echo 'El elemento seleccionado no está disponible o no existe.';
                                            break;
                                        case 'acceso_no_autorizado':
                                            echo 'Acceso no autorizado. Debes seleccionar un elemento del inventario.';
                                            break;
                                        default:
                                            echo htmlspecialchars($data['error']);
                                    }
                                    ?>
                                </p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-4">Inventario de Elementos</h2>

                    <?php if (empty($data['elementos'])) : ?>
                        <div class="text-center py-12">
                            <i class="fas fa-box-open text-5xl text-gray-400 mb-4"></i>
                            <h3 class="text-lg font-medium text-gray-900">No hay elementos en el inventario</h3>
                            <p class="mt-1 text-sm text-gray-500">Contacta al administrador para agregar elementos.</p>
                        </div>
                    <?php else : ?>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mt-6">
                            <?php foreach ($data['elementos'] as $elemento) : ?>
                                <div onclick='mostrarDetalleElemento(<?php echo json_encode($elemento, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>)' class="cursor-pointer block bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden hover:shadow-lg transition-shadow duration-300">
                                    <div class="p-4">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <h3 class="text-lg font-semibold text-gray-900"><?php echo htmlspecialchars($elemento->nombreele); ?></h3>
                                                <p class="text-sm text-gray-600">Código: <?php echo htmlspecialchars($elemento->codigoele); ?></p>
                                            </div>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                <?php echo $elemento->cantidadele; ?> disponibles
                                            </span>
                                        </div>
                                        <div class="mt-2">
                                            <p class="text-sm text-gray-600 line-clamp-2">
                                                <?php echo htmlspecialchars(substr($elemento->descripcionele, 0, 100)); ?><?php echo strlen($elemento->descripcionele) > 100 ? '...' : ''; ?>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="bg-gray-50 px-4 py-3 border-t border-gray-200">
                                        <div class="flex justify-between items-center">
                                            <span class="text-xs font-medium <?php echo $elemento->estado === 'disponible' ? 'text-green-800 bg-green-100' : 'text-yellow-800 bg-yellow-100'; ?> px-2.5 py-0.5 rounded-full">
                                                <?php echo ucfirst(htmlspecialchars($elemento->estado ?? 'Disponible')); ?>
                                            </span>
                                            <span class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                                Ver más
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </main>

        </div>
    </div>

    <!-- Modal para Detalles del Elemento -->
    <div id="detalleElementoModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center pb-3 border-b">
                <h3 class="text-2xl font-bold text-gray-900" id="modalTitulo"></h3>
                <button id="cerrarModalBtn" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="mt-4" id="modalContenido">
                <!-- El contenido se inyectará aquí -->
            </div>
        </div>
    </div>

     <script>
        function mostrarDetalleElemento(elemento) {
            document.getElementById('modalTitulo').textContent = elemento.nombreele;
            
            let contenidoHtml = `
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Código</p>
                        <p class="text-lg text-gray-900">${elemento.codigoele}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Cantidad Disponible</p>
                        <p class="text-lg text-gray-900">${elemento.cantidadele}</p>
                    </div>
                </div>
                <div class="mt-4">
                    <p class="text-sm font-medium text-gray-500">Descripción</p>
                    <p class="text-gray-800">${elemento.descripcionele}</p>
                </div>
                <div class="mt-4">
                    <p class="text-sm font-medium text-gray-500">Características</p>
                    <p class="text-gray-800">${elemento.caracteristicasele || 'No especificadas'}</p>
                </div>
                <div class="mt-6 pt-4 border-t border-gray-200 flex justify-end">`;

            // Lógica condicional para el botón de solicitar
            if (elemento.cuentadante_id) {
                contenidoHtml += `
                    <div class="text-right">
                        <p class="text-lg font-semibold text-gray-800">Aprobación Requerida</p>
                        <p class="text-sm text-gray-600">Este elemento será solicitado a un cuentadante para su aprobación.</p>
                        <a href="../peticion/registrar?elemento_id=${elemento.IDele}" class="mt-2 inline-flex items-center px-6 py-2 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <i class="fas fa-paper-plane mr-2"></i>
                            Continuar y Solicitar
                        </a>
                    </div>`;
            } else {
                contenidoHtml += `
                    <a href="../peticion/registrar?elemento_id=${elemento.IDele}" class="inline-flex items-center px-6 py-2 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-paper-plane mr-2"></i>
                        Solicitar Elemento
                    </a>`;
            }

            contenidoHtml += `
                </div>
            `;

            document.getElementById('modalContenido').innerHTML = contenidoHtml;
            document.getElementById('detalleElementoModal').classList.remove('hidden');
        }

        function cerrarModal() {
            document.getElementById('detalleElementoModal').classList.add('hidden');
        }

        document.getElementById('cerrarModalBtn').addEventListener('click', cerrarModal);

        document.getElementById('detalleElementoModal').addEventListener('click', function(event) {
            if (event.target.id === 'detalleElementoModal') {
                cerrarModal();
            }
        });
    </script>
</body>
</html>