<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administrador - Almacenes</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-100">

    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <?php require_once __DIR__ . '/includes/sidebar-admin.php'; ?>

        <!-- Contenido Principal -->
        <div class="flex-1 p-10">
            <h1 class="text-3xl font-bold mb-6">Gestión de Autorizaciones</h1>

            <!-- Tabla de Autorizaciones -->
            <div class="bg-white shadow-md rounded-lg overflow-hidden">
                <table class="min-w-full leading-normal">
                    <thead>
                        <tr>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">ID</th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Visto Bueno Cuentadante</th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nombre Autoriza</th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Cargo Autoriza</th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Estado</th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($data['autorizaciones'])): ?>
                            <?php foreach ($data['autorizaciones'] as $autorizacion): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm"><?php echo htmlspecialchars($autorizacion->IDaut); ?></td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm"><?php echo htmlspecialchars($autorizacion->VoBoCuentadanteaut); ?></td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm"><?php echo htmlspecialchars($autorizacion->nomquienaturiza); ?></td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm"><?php echo htmlspecialchars($autorizacion->cargoquienautoriza); ?></td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            <?php echo htmlspecialchars($autorizacion->estadoaut); ?>
                                        </span>
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        <a href="#" class="text-blue-600 hover:text-blue-900 mr-3"><i class="fas fa-eye"></i></a>
                                        <a href="#" class="text-yellow-600 hover:text-yellow-900 mr-3"><i class="fas fa-edit"></i></a>
                                        <a href="#" class="text-red-600 hover:text-red-900"><i class="fas fa-trash"></i></a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-10">No hay autorizaciones para mostrar.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Controles de Paginación -->
            <div class="mt-6 flex justify-center">
                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                    <?php if ($data['total_paginas'] > 1): ?>
                        <!-- Botón Anterior -->
                        <a href="<?php echo BASE_URL; ?>admin/almacenes?pagina=<?php echo $data['pagina_actual'] - 1; ?>"
                           class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 <?php echo ($data['pagina_actual'] <= 1) ? 'opacity-50 cursor-not-allowed' : ''; ?>">
                            <span class="sr-only">Anterior</span>
                            <i class="fas fa-chevron-left h-5 w-5"></i>
                        </a>

                        <?php for ($i = 1; $i <= $data['total_paginas']; $i++): ?>
                            <a href="<?php echo BASE_URL; ?>admin/almacenes?pagina=<?php echo $i; ?>"
                               class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium 
                               <?php echo ($i == $data['pagina_actual']) ? 'z-10 bg-blue-50 border-blue-500 text-blue-600' : 'bg-white text-gray-700 hover:bg-gray-50'; ?>">
                                <?php echo $i; ?>
                            </a>
                        <?php endfor; ?>

                        <!-- Botón Siguiente -->
                        <a href="<?php echo BASE_URL; ?>admin/almacenes?pagina=<?php echo $data['pagina_actual'] + 1; ?>"
                           class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 <?php echo ($data['pagina_actual'] >= $data['total_paginas']) ? 'opacity-50 cursor-not-allowed' : ''; ?>">
                            <span class="sr-only">Siguiente</span>
                            <i class="fas fa-chevron-right h-5 w-5"></i>
                        </a>
                    <?php endif; ?>
                </nav>
            </div>
        </div>
    </div>

</body>
</html>