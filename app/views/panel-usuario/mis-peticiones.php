<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Peticiones - LOAUTECH</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="flex h-screen">
        <?php include __DIR__ . '/includes/sidebar-usuario.php'; ?>
        <div class="flex-1 ml-64 overflow-auto">
            <main class="p-6">
                <h1 class="text-3xl font-bold mb-4">Mis Peticiones</h1>

                <!-- Formulario de Búsqueda y Filtros -->
                <div class="bg-white p-4 rounded-lg shadow-md mb-6">
                    <form action="<?php echo BASE_URL; ?>/usuario/misPeticiones" method="GET" class="flex flex-col sm:flex-row gap-4">
                        <div class="flex-grow">
                            <label for="search" class="sr-only">Buscar</label>
                            <input type="text" name="search" id="search" placeholder="Buscar por nombre de elemento..." 
                                   value="<?php echo htmlspecialchars($data['filtros']['search']); ?>" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div class="flex-shrink-0">
                            <label for="status" class="sr-only">Estado</label>
                            <select name="status" id="status" class="w-full sm:w-auto px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">Todos los estados</option>
                                <option value="Pendiente" <?php if ($data['filtros']['status'] == 'Pendiente') echo 'selected'; ?>>Pendiente</option>
                                <option value="Aprobado" <?php if ($data['filtros']['status'] == 'Aprobado') echo 'selected'; ?>>Aprobado</option>
                                <option value="Rechazado" <?php if ($data['filtros']['status'] == 'Rechazado') echo 'selected'; ?>>Rechazado</option>
                            </select>
                        </div>
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition">
                            <i class="fa fa-search mr-2"></i>Buscar
                        </button>
                    </form>
                </div>

                <!-- Tabla de Peticiones -->
                <div class="bg-white shadow-md rounded-lg overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Elemento</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cantidad</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php if (empty($data['peticiones'])) : ?>
                                    <tr>
                                        <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">No se encontraron peticiones con los filtros actuales.</td>
                                    </tr>
                                <?php else : ?>
                                    <?php foreach ($data['peticiones'] as $peticion) : ?>
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($peticion->elemento_nombre); ?></div>
                                                <div class="text-sm text-gray-500"><?php echo htmlspecialchars($peticion->elemento_codigo ?? 'N/A'); ?></div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($peticion->cantidad); ?></td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo date('d/m/Y H:i', strtotime($peticion->fecha_solicitud)); ?></td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <?php
                                                    $estado = strtolower($peticion->estpet);
                                                    $color = 'bg-yellow-100 text-yellow-800'; 
                                                    if ($estado === 'aprobado') $color = 'bg-green-100 text-green-800';
                                                    elseif ($estado === 'rechazado') $color = 'bg-red-100 text-red-800';
                                                ?>
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $color; ?>">
                                                    <?php echo ucfirst(htmlspecialchars($peticion->estpet)); ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Controles de Paginación -->
                <?php if ($data['totalPaginas'] > 1) : ?>
                <div class="mt-6 flex justify-between items-center">
                    <div class="text-sm text-gray-700">
                        Página <?php echo $data['paginaActual']; ?> de <?php echo $data['totalPaginas']; ?>
                    </div>
                    <div class="flex items-center">
                        <?php
                            // Construir la URL base para los enlaces de paginación, manteniendo los filtros
                            $queryString = http_build_query([
                                'search' => $data['filtros']['search'],
                                'status' => $data['filtros']['status']
                            ]);
                        ?>
                        <!-- Botón Anterior -->
                        <?php if ($data['paginaActual'] > 1) : ?>
                            <a href="<?php echo BASE_URL; ?>/usuario/misPeticiones?page=<?php echo $data['paginaActual'] - 1; ?>&<?php echo $queryString; ?>"
                               class="px-3 py-1 mx-1 bg-white border border-gray-300 text-gray-700 rounded hover:bg-gray-100">
                                Anterior
                            </a>
                        <?php else: ?>
                            <span class="px-3 py-1 mx-1 bg-gray-200 border border-gray-300 text-gray-400 rounded cursor-not-allowed">Anterior</span>
                        <?php endif; ?>

                        <!-- Números de Página -->
                        <?php for ($i = 1; $i <= $data['totalPaginas']; $i++) : ?>
                            <a href="<?php echo BASE_URL; ?>/usuario/misPeticiones?page=<?php echo $i; ?>&<?php echo $queryString; ?>"
                               class="px-3 py-1 mx-1 border rounded <?php echo ($i == $data['paginaActual']) ? 'bg-blue-600 text-white border-blue-600' : 'bg-white border-gray-300 text-gray-700 hover:bg-gray-100'; ?>">
                                <?php echo $i; ?>
                            </a>
                        <?php endfor; ?>

                        <!-- Botón Siguiente -->
                        <?php if ($data['paginaActual'] < $data['totalPaginas']) : ?>
                            <a href="<?php echo BASE_URL; ?>/usuario/misPeticiones?page=<?php echo $data['paginaActual'] + 1; ?>&<?php echo $queryString; ?>"
                               class="px-3 py-1 mx-1 bg-white border border-gray-300 text-gray-700 rounded hover:bg-gray-100">
                                Siguiente
                            </a>
                        <?php else: ?>
                            <span class="px-3 py-1 mx-1 bg-gray-200 border border-gray-300 text-gray-400 rounded cursor-not-allowed">Siguiente</span>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>

            </main>
            <?php include __DIR__ . '/includes/footer-usuario.php'; ?>
        </div>
    </div>
</body>
</html>