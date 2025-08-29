<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $data['page_title'] ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 1rem;
        }
        .pagination a, .pagination span {
            padding: 0.5rem 1rem;
            margin: 0 0.25rem;
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            color: #4b5563;
            text-decoration: none;
        }
        .pagination a:hover {
            background-color: #e5e7eb;
        }
        .pagination .active {
            background-color: #3b82f6;
            color: white;
            border-color: #3b82f6;
        }
        .pagination .disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
    </style>
</head>
<body class="bg-gray-100">
    <?php require_once __DIR__ . '/includes/navbar.php'; ?>
    <div class="container mx-auto px-4 py-6">
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="bg-gradient-to-r from-blue-600 to-purple-700 text-white px-6 py-4">
                <div class="flex items-center space-x-2">
                    <i class="fas fa-warehouse"></i>
                    <h2 class="text-xl font-bold">Gestión de Inventario</h2>
                </div>
                <p class="mt-1 text-sm text-blue-100">Administra los elementos del inventario</p>
            </div>
            
            <div class="p-6">
                <!-- Tabs -->
                <div class="mb-6">
                    <div class="border-b border-gray-200">
                        <nav class="flex space-x-2" aria-label="Tabs">
                            <a href="<?= BASE_URL ?>/almacen/inventario" 
                               class="px-4 py-2 text-sm font-medium text-blue-600 border-b-2 border-blue-600 rounded-t-lg">
                                <i class="fas fa-boxes mr-2"></i>Elementos
                            </a>
                            <a href="#" 
                               class="px-4 py-2 text-sm font-medium text-gray-500 border-b-2 border-transparent hover:text-gray-700 hover:border-gray-300 rounded-t-lg">
                                <i class="fas fa-tags mr-2"></i>Categorías
                            </a>
                            <a href="#" 
                               class="px-4 py-2 text-sm font-medium text-gray-500 border-b-2 border-transparent hover:text-gray-700 hover:border-gray-300 rounded-t-lg">
                                <i class="fas fa-archive mr-2"></i>Almacenes
                            </a>
                        </nav>
                    </div>
                </div>

                <!-- Contenido principal -->
                <div id="inventory-management-content" class="space-y-4">
                    <div class="flex flex-col md:flex-row justify-between gap-4 mb-6">
                        <!-- Botón de Agregar -->
                        <a href="#" 
                           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center justify-center space-x-2 shadow-md hover:shadow-lg transition-all duration-200 font-medium w-full md:w-auto">
                            <i class="fas fa-plus"></i>
                            <span>Agregar Elemento</span>
                        </a>
                        
                        <!-- Buscador -->
                        <div class="relative w-full md:w-64">
                            <input type="text" id="searchInput" 
                                   class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                                   placeholder="Buscar elementos...">
                            <div class="absolute left-3 top-2.5 text-gray-400">
                                <i class="fas fa-search"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Tabla de elementos -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white">
                            <thead class="bg-gray-800 text-white">
                                <tr>
                                    <th class="py-3 px-4 text-left">ID</th>
                                    <th class="py-3 px-4 text-left">Imagen</th>
                                    <th class="py-3 px-4 text-left">Nombre</th>
                                    <th class="py-3 px-4 text-left">Código</th>
                                    <th class="py-3 px-4 text-left">Cantidad</th>
                                    <th class="py-3 px-4 text-left">Estado</th>
                                    <th class="py-3 px-4 text-left">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-700">
                                <?php if (!empty($data['elementos'])): ?>
                                    <?php foreach($data['elementos'] as $elemento): 
                                        $estadoClase = 'bg-green-100 text-green-800';
                                        $estadoTexto = 'Disponible';
                                    ?>
                                    <tr class="border-b border-gray-200 hover:bg-gray-50">
                                        <td class="py-3 px-4"><?= $elemento->IDele ?></td>
                                        <td class="py-3 px-4">
                                            <?php if (!empty($elemento->imagen)): ?>
                                                <img src="<?= BASE_URL ?>/<?= htmlspecialchars($elemento->imagen) ?>" 
                                                     alt="<?= htmlspecialchars($elemento->nombreele) ?>" 
                                                     class="w-10 h-10 object-cover rounded-full">
                                            <?php else: ?>
                                                <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center">
                                                    <i class="fas fa-box text-gray-400"></i>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="py-3 px-4 font-medium"><?= htmlspecialchars($elemento->nombreele) ?></td>
                                        <td class="py-3 px-4"><?= htmlspecialchars($elemento->codigoele) ?></td>
                                        <td class="py-3 px-4"><?= $elemento->cantidadele ?></td>
                                        <td class="py-3 px-4">
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full <?= $estadoClase ?>">
                                                <?= $estadoTexto ?>
                                            </span>
                                        </td>
                                        <td class="py-3 px-4">
                                            <div class="flex space-x-2">
                                                <a href="#" 
                                                   class="text-blue-600 hover:text-blue-800" 
                                                   title="Ver detalles">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="#" 
                                                   class="text-yellow-600 hover:text-yellow-800" 
                                                   title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="py-4 text-center text-gray-500">
                                            No se encontraron elementos en el inventario.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginación -->
                    <?php if ($data['total_paginas'] > 1): ?>
                    <div class="pagination mt-6">
                        <?php if ($data['pagina_actual'] > 1): ?>
                            <a href="<?= BASE_URL ?>/almacen/inventario?pagina=1" class="page-link"><i class="fas fa-angle-double-left"></i></a>
                            <a href="<?= BASE_URL ?>/almacen/inventario?pagina=<?= $data['pagina_actual'] - 1 ?>" class="page-link"><i class="fas fa-angle-left"></i></a>
                        <?php else: ?>
                            <span class="page-link disabled"><i class="fas fa-angle-double-left"></i></span>
                            <span class="page-link disabled"><i class="fas fa-angle-left"></i></span>
                        <?php endif; ?>

                        <?php
                        $inicio_pagina = max(1, $data['pagina_actual'] - 2);
                        $fin_pagina = min($data['total_paginas'], $inicio_pagina + 4);
                        $inicio_pagina = max(1, $fin_pagina - 4);
                        
                        for ($i = $inicio_pagina; $i <= $fin_pagina; $i++): ?>
                            <a href="<?= BASE_URL ?>/almacen/inventario?pagina=<?= $i ?>" class="page-link <?= $i == $data['pagina_actual'] ? 'active' : '' ?>">
                                <?= $i ?>
                            </a>
                        <?php endfor; ?>

                        <?php if ($data['pagina_actual'] < $data['total_paginas']): ?>
                            <a href="<?= BASE_URL ?>/almacen/inventario?pagina=<?= $data['pagina_actual'] + 1 ?>" class="page-link"><i class="fas fa-angle-right"></i></a>
                            <a href="<?= BASE_URL ?>/almacen/inventario?pagina=<?= $data['total_paginas'] ?>" class="page-link"><i class="fas fa-angle-double-right"></i></a>
                        <?php else: ?>
                            <span class="page-link disabled"><i class="fas fa-angle-right"></i></span>
                            <span class="page-link disabled"><i class="fas fa-angle-double-right"></i></span>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Función para filtrar la tabla
        document.getElementById('searchInput').addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('tbody tr');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                if (text.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });


    </script>
</body>
</html>