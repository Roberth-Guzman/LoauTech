<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $data['page_title'] ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-gray-100">
    <?php
    require_once dirname(__DIR__) . '/panel-almacenes/includes/navbar.php';
    ?>

    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-xl overflow-hidden">
            <div class="bg-gradient-to-r from-blue-600 to-purple-700 text-white px-6 py-4">
                <h2 class="text-2xl font-bold flex items-center">
                    <i class="fas fa-info-circle mr-3"></i>
                    <span><?php echo htmlspecialchars($data['titulo']); ?></span>
                </h2>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Columna Izquierda - Imagen y Acciones -->
                    <div class="md:col-span-1">
                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200 text-center">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Imagen del Elemento</h3>
                            <?php if (!empty($data['elemento']->imagen)): ?>
                                <img src="<?php echo BASE_URL . '/' . htmlspecialchars($data['elemento']->imagen); ?>"
                                    alt="<?php echo htmlspecialchars($data['elemento']->nombreele); ?>"
                                    class="w-full h-64 object-contain mx-auto rounded-md shadow-sm">
                            <?php else: ?>
                                <div class="bg-gray-200 rounded-lg h-64 flex items-center justify-center">
                                    <i class="fas fa-image text-gray-400 text-5xl"></i>
                                </div>
                                <p class="text-sm text-gray-500 text-center mt-2">Sin imagen disponible</p>
                            <?php endif; ?>
                        </div>

                        <div class="mt-6 text-center">
                            <a href="<?php echo BASE_URL; ?>/almacen/almacen/editarElemento/<?php echo htmlspecialchars($data['elemento']->IDele); ?>"
                                class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition duration-300 ease-in-out transform hover:-translate-y-1">
                                <i class="fas fa-pencil-alt mr-2"></i>Editar Elemento
                            </a>
                        </div>
                    </div>

                    <!-- Columna Derecha - Detalles -->
                    <div class="md:col-span-2">
                        <div class="bg-white overflow-hidden">
                            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                                <h3 class="text-lg leading-6 font-medium text-gray-900">
                                    Información del Elemento
                                </h3>
                                <p class="mt-1 max-w-2xl text-sm text-gray-500">
                                    Detalles completos del elemento en el inventario.
                                </p>
                            </div>
                            <div class="border-t border-gray-200">
                                <dl>
                                    <div class="bg-gray-50 px-4 py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                        <dt class="text-sm font-medium text-gray-600">Nombre</dt>
                                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2 font-semibold">
                                            <?php echo htmlspecialchars($data['elemento']->nombreele); ?></dd>
                                    </div>
                                    <div class="bg-white px-4 py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                        <dt class="text-sm font-medium text-gray-600">Código</dt>
                                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                            <?php echo htmlspecialchars($data['elemento']->codigoele); ?></dd>
                                    </div>
                                    <div class="bg-gray-50 px-4 py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                        <dt class="text-sm font-medium text-gray-600">Código de Inventario</dt>
                                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                            <?php echo !empty($data['elemento']->codigoinventario) ? htmlspecialchars($data['elemento']->codigoinventario) : 'No especificado'; ?>
                                        </dd>
                                    </div>
                                    <div class="bg-white px-4 py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                        <dt class="text-sm font-medium text-gray-600">Cantidad</dt>
                                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                            <?php echo intval($data['elemento']->cantidadele); ?>
                                            <?php if ($data['elemento']->cantidadest === 'inactivo'): ?>
                                                <span class="ml-2 text-xs text-red-600">(Inactivo)</span>
                                            <?php endif; ?>
                                        </dd>
                                    </div>
                                    <div class="bg-gray-50 px-4 py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                        <dt class="text-sm font-medium text-gray-600">Estado</dt>
                                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                            <?php
                                            $estado = $data['elemento']->estado;
                                            if ($estado === 'activo') {
                                                echo '<span class="px-2 py-1 text-xs font-semibold leading-tight text-green-700 bg-green-100 rounded-full">Disponible</span>';
                                            } elseif ($estado === 'en prestamo') {
                                                echo '<span class="px-2 py-1 text-xs font-semibold leading-tight text-yellow-700 bg-yellow-100 rounded-full">En préstamo</span>';
                                            } else {
                                                echo '<span class="px-2 py-1 text-xs font-semibold leading-tight text-red-700 bg-red-100 rounded-full">Inactivo</span>';
                                            }
                                            ?>
                                        </dd>
                                    </div>
                                    <div class="bg-white px-4 py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                        <dt class="text-sm font-medium text-gray-600">Categoría</dt>
                                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                            <?php echo !empty($data['elemento']->caracteristicasele) ? htmlspecialchars($data['elemento']->caracteristicasele) : 'No especificada'; ?>
                                        </dd>
                                    </div>
                                    <div class="bg-gray-50 px-4 py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                        <dt class="text-sm font-medium text-gray-600">Descripción</dt>
                                        <dd
                                            class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2 whitespace-pre-line">
                                            <?php echo !empty($data['elemento']->descripcionele) ? nl2br(htmlspecialchars($data['elemento']->descripcionele)) : 'No hay descripción disponible.'; ?>
                                        </dd>
                                    </div>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex justify-end space-x-3 border-t pt-4">
                    <a href="<?php echo BASE_URL; ?>/almacen/almacen/visualizacion"
                        class="bg-gray-300 text-gray-800 px-4 py-2 rounded-md hover:bg-gray-400 transition duration-300">
                        <i class="fas fa-arrow-left mr-2"></i> Volver a la visualización
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>

</html>