<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($data['titulo']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-gray-100">

    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <?php require_once __DIR__ . '/includes/sidebar-admin.php'; ?>

        <!-- Contenido Principal -->
        <div class="flex-1 p-10">
            <h1 class="text-3xl font-bold mb-6"><?= htmlspecialchars($data['titulo']) ?></h1>

            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl">Inventario de Elementos</h2>
                <a href="<?= BASE_URL ?>/admin/agregarElemento"
                    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    <i class="fas fa-plus mr-2"></i> Nuevo Elemento
                </a>
            </div>

            <?php if (isset($_SESSION['mensaje'])): ?>
                <div class="bg-<?= $_SESSION['tipo_mensaje'] == 'success' ? 'green' : 'red' ?>-100 border-l-4 border-<?= $_SESSION['tipo_mensaje'] == 'success' ? 'green' : 'red' ?>-500 text-<?= $_SESSION['tipo_mensaje'] == 'success' ? 'green' : 'red' ?>-700 p-4 mb-4"
                    role="alert">
                    <p><?= $_SESSION['mensaje'] ?></p>
                </div>
                <?php
                unset($_SESSION['mensaje']);
                unset($_SESSION['tipo_mensaje']);
            endif;
            ?>

            <!-- Tabla de Elementos -->
            <div class="bg-white shadow-md rounded-lg overflow-hidden">
                <table class="min-w-full leading-normal">
                    <thead class="bg-gray-800 text-white">
                        <tr>
                            <th class="py-3 px-4">ID</th>
                            <th class="py-3 px-4">Nombre</th>
                            <th class="py-3 px-4">Código</th>
                            <th class="py-3 px-4">Código Inventario</th>
                            <th class="py-3 px-4">Cantidad</th>
                            <th class="py-3 px-4">Estado</th>
                            <th class="py-3 px-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700">
                        <?php if (!empty($data['elementos'])): ?>
                            <?php foreach ($data['elementos'] as $elemento):
                                $estadoClase = '';
                                switch ($elemento->estado) {
                                    case 'activo':
                                        $estadoClase = 'bg-green-100 text-green-800';
                                        break;
                                    case 'en prestamo':
                                        $estadoClase = 'bg-yellow-100 text-yellow-800';
                                        break;
                                    case 'inactivo':
                                        $estadoClase = 'bg-red-100 text-red-800';
                                        break;
                                }
                                ?>
                                <tr class="border-b border-gray-200 hover:bg-gray-50">
                                    <td class="py-3 px-4"><?= htmlspecialchars($elemento->IDele) ?></td>
                                    <td class="py-3 px-4 font-medium"><?= htmlspecialchars($elemento->nombreele) ?></td>
                                    <td class="py-3 px-4"><?= htmlspecialchars($elemento->codigoele) ?></td>
                                    <td class="py-3 px-4"><?= htmlspecialchars($elemento->codigoinventario) ?></td>
                                    <td class="py-3 px-4">
                                        <span class="px-2 py-1 rounded-full bg-gray-100">
                                            <?= htmlspecialchars($elemento->cantidadele) ?>
                                        </span>
                                    </td>
                                    <td class="py-3 px-4">
                                        <span class="px-2 py-1 rounded-full <?= $estadoClase ?>">
                                            <?= ucfirst(htmlspecialchars($elemento->estado)) ?>
                                        </span>
                                    </td>
                                    <td class="py-3 px-4">
                                        <div class="flex space-x-2">
                                            <a href="<?= BASE_URL ?>/admin/editarElemento/<?= $elemento->IDele ?>"
                                                class="bg-yellow-500 text-white p-2 rounded hover:bg-yellow-600"
                                                title="Editar elemento">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="<?= BASE_URL ?>/admin/eliminarElemento/<?= $elemento->IDele ?>"
                                                class="bg-gray-500 text-white p-2 rounded hover:bg-gray-600"
                                                onclick="return confirm('¿Estás seguro de desactivar este elemento?')"
                                                title="Desactivar elemento">
                                                <i class="fas fa-toggle-off"></i>
                                            </a>
                                            <a href="<?= BASE_URL ?>/admin/verElemento/<?= $elemento->IDele ?>"
                                                class="bg-blue-500 text-white p-2 rounded hover:bg-blue-600"
                                                title="Ver detalles">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="py-4 text-center text-gray-500">
                                    No hay elementos registrados. <a href="<?= BASE_URL ?>/admin/crearElemento"
                                        class="text-blue-600 hover:underline">Agregar nuevo elemento</a>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <div class="mt-6 flex justify-center">
                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                    <a href="<?= BASE_URL ?>/admin/elementos/<?= ($data['pagina_actual'] > 1) ? $data['pagina_actual'] - 1 : 1 ?>"
                        class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 <?= ($data['pagina_actual'] <= 1) ? 'opacity-50 cursor-not-allowed' : '' ?>">
                        <i class="fas fa-chevron-left h-5 w-5"></i>
                    </a>
                    <?php for ($i = 1; $i <= $data['total_paginas']; $i++): ?>
                        <a href="<?= BASE_URL ?>/admin/elementos/<?= $i ?>"
                            class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium <?= ($i == $data['pagina_actual']) ? 'z-10 bg-blue-50 border-blue-500 text-blue-600' : 'bg-white text-gray-700 hover:bg-gray-50' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>
                    <a href="<?= BASE_URL ?>/admin/elementos/<?= ($data['pagina_actual'] < $data['total_paginas']) ? $data['pagina_actual'] + 1 : $data['total_paginas'] ?>"
                        class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 <?= ($data['pagina_actual'] >= $data['total_paginas']) ? 'opacity-50 cursor-not-allowed' : '' ?>">
                        <i class="fas fa-chevron-right h-5 w-5"></i>
                    </a>
                </nav>
            </div>
        </div>
    </div>
</body>

</html>