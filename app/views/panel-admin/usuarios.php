<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($data['titulo']) ?></title>
    <!-- Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-100">

    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <?php 

            require_once __DIR__ . '/includes/sidebar-admin.php'; 
        ?>

        <!-- Contenido Principal -->
        <div class="flex-1 p-10">
            <h1 class="text-3xl font-bold mb-6"><?= htmlspecialchars($data['titulo']) ?></h1>

            <!-- Tabla de Usuarios -->
            <div class="bg-white shadow-md rounded-lg overflow-hidden">
                <table class="min-w-full leading-normal">
                    <thead>
                        <tr>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">ID</th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nombre Completo</th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Documento</th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Rol</th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Contacto</th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data['usuarios'] as $usuario): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm"><?= htmlspecialchars($usuario->IDper) ?></td>
                                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm"><?= htmlspecialchars($usuario->nombrecompletoper) ?></td>
                                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm"><?= htmlspecialchars($usuario->tipodocumento) ?>: <?= htmlspecialchars($usuario->numerodoc) ?></td>
                                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm"><?= htmlspecialchars($usuario->rol ?? 'No asignado') ?></td>
                                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                    <p><?= htmlspecialchars($usuario->correocont) ?></p>
                                    <p><?= htmlspecialchars($usuario->numerocont) ?></p>
                                </td>
                                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                    <a href="<?= BASE_URL ?>/admin/editarUsuario/<?= $usuario->IDper ?>" class="text-blue-600 hover:text-blue-900 mr-3"><i class="fas fa-pencil-alt"></i></a>
                                    <a href="<?= BASE_URL ?>/admin/eliminarUsuario/<?= $usuario->IDper ?>" class="text-red-600 hover:text-red-900" onclick="return confirm('¿Estás seguro de que quieres eliminar a este usuario?');"><i class="fas fa-trash-alt"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Controles de Paginación -->
            <div class="mt-6 flex justify-center">
                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                    <!-- Botón Anterior -->
                    <a href="<?= BASE_URL ?>/admin/usuarios/<?= ($data['pagina_actual'] > 1) ? $data['pagina_actual'] - 1 : 1 ?>"
                       class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 <?= ($data['pagina_actual'] <= 1) ? 'opacity-50 cursor-not-allowed' : '' ?>">
                        <span class="sr-only">Anterior</span>
                        <i class="fas fa-chevron-left h-5 w-5"></i>
                    </a>

                    <?php for ($i = 1; $i <= $data['total_paginas']; $i++): ?>
                        <a href="<?= BASE_URL ?>/admin/usuarios/<?= $i ?>"
                           class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium 
                           <?= ($i == $data['pagina_actual']) ? 'z-10 bg-blue-50 border-blue-500 text-blue-600' : 'bg-white text-gray-700 hover:bg-gray-50' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>

                    <!-- Botón Siguiente -->
                    <a href="<?= BASE_URL ?>/admin/usuarios/<?= ($data['pagina_actual'] < $data['total_paginas']) ? $data['pagina_actual'] + 1 : $data['total_paginas'] ?>"
                       class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 <?= ($data['pagina_actual'] >= $data['total_paginas']) ? 'opacity-50 cursor-not-allowed' : '' ?>">
                        <span class="sr-only">Siguiente</span>
                        <i class="fas fa-chevron-right h-5 w-5"></i>
                    </a>
                </nav>
            </div>
            
        </div>
    </div>

</body>
</html>