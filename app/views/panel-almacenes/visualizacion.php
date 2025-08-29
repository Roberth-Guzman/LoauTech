<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visualización de Inventario - Gestión de Inventario</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .card {
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .status-available { background-color: #d1fae5; color: #065f46; }
        .status-loaned { background-color: #fef3c7; color: #92400e; }
        .status-inactive { background-color: #fee2e2; color: #991b1b; }
    </style>
</head>
<body class="bg-gray-100">

    <?php require_once __DIR__ . '/includes/navbar.php'; ?>

    <div class="container mx-auto px-4 py-6">
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="bg-gradient-to-r from-blue-600 to-purple-700 text-white px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-boxes"></i>
                        <h2 class="text-xl font-bold">Visualización de Inventario</h2>
                    </div>
                    <div class="flex items-center space-x-2">
                        <button id="gridViewBtn" class="p-2 rounded-full bg-blue-700 hover:bg-blue-800 focus:outline-none">
                            <i class="fas fa-th-large"></i>
                        </button>
                        <button id="listViewBtn" class="p-2 rounded-full hover:bg-blue-800 focus:outline-none">
                            <i class="fas fa-list"></i>
                        </button>
                    </div>
                </div>
                <p class="mt-1 text-sm text-blue-100">Explora y busca elementos del inventario</p>
            </div>
            
            <div class="p-6">
                <!-- Filtros -->
                <div class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="md:col-span-1">
                        <div class="relative">
                            <input type="text" id="searchInput" 
                                   class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                                   placeholder="Buscar por nombre o código...">
                            <div class="absolute left-3 top-2.5 text-gray-400">
                                <i class="fas fa-search"></i>
                            </div>
                        </div>
                    </div>
                    <div>
                        <select id="categoryFilter" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Todas las categorías</option>
                            <?php foreach($data['categorias'] as $categoria): ?>
                                <option value="<?= htmlspecialchars($categoria->caracteristicasele) ?>"><?= htmlspecialchars($categoria->caracteristicasele) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <select id="statusFilter" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Todos los estados</option>
                            <option value="activo">Disponible</option>
                            <option value="en prestamo">En préstamo</option>
                            <option value="inactivo">Inactivo</option>
                        </select>
                    </div>
                </div>

                <!-- Vista de tarjetas (grid) -->
                <div id="gridViewContent" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    <?php if (!empty($data['elementos'])): ?>
                        <?php foreach($data['elementos'] as $elemento): ?>
                            <div class="card bg-white rounded-lg border border-gray-200 overflow-hidden hover:shadow-md transition-shadow" 
                                 data-name="<?= strtolower(htmlspecialchars($elemento->nombreele)) ?>"
                                 data-code="<?= strtolower(htmlspecialchars($elemento->codigoele)) ?>"
                                 data-category="<?= htmlspecialchars($elemento->caracteristicasele ?? '') ?>" 
                                 data-status="<?= $elemento->estado ?>">
                                <div class="relative">
                                    <img src="<?= BASE_URL ?>/<?= htmlspecialchars($elemento->imagen ?: 'img/placeholder.png') ?>" 
                                         alt="<?= htmlspecialchars($elemento->nombreele) ?>" 
                                         class="w-full h-48 object-cover">
                                </div>
                                <div class="p-4">
                                    <h3 class="font-semibold text-lg text-gray-800 mb-1 truncate" title="<?= htmlspecialchars($elemento->nombreele) ?>">
                                        <?= htmlspecialchars($elemento->nombreele) ?>
                                    </h3>
                                    <p class="text-sm text-gray-600 mb-2">
                                        <span class="font-medium">Código:</span> <?= htmlspecialchars($elemento->codigoele) ?>
                                    </p>
                                    <div class="flex justify-between items-center mt-4">
                                        <span class="text-xs px-2 py-1 bg-gray-100 text-gray-700 rounded">
                                            <?= htmlspecialchars($elemento->caracteristicasele ?? 'Sin categoría') ?>
                                        </span>
                                        <div class="p-4 border-t flex justify-center">
                                            <a href="<?php echo BASE_URL; ?>/almacen/almacen/detalleElemento/<?php echo htmlspecialchars($elemento->IDele); ?>" class="text-blue-600 hover:text-blue-800 font-semibold text-sm">
                                                Ver detalles <i class="fas fa-arrow-right ml-1"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-span-full text-center py-8">
                            <i class="fas fa-inbox text-4xl text-gray-400 mb-2"></i>
                            <p class="text-gray-600">No se encontraron elementos para mostrar.</p>
                        </div>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const gridViewBtn = document.getElementById('gridViewBtn');
            const listViewBtn = document.getElementById('listViewBtn');

            // Por ahora, solo manejamos el estado visual de los botones
            gridViewBtn.addEventListener('click', function() {
                // Lógica para mostrar vista de cuadrícula
                this.classList.add('bg-blue-700');
                this.classList.remove('hover:bg-blue-800');
                listViewBtn.classList.remove('bg-blue-700');
                listViewBtn.classList.add('hover:bg-blue-800');
            });

            listViewBtn.addEventListener('click', function() {
                // Lógica para mostrar vista de lista
                this.classList.add('bg-blue-700');
                this.classList.remove('hover:bg-blue-800');
                gridViewBtn.classList.remove('bg-blue-700');
                gridViewBtn.classList.add('hover:bg-blue-800');
                // alert('La vista de lista aún no está implementada en este diseño.');
            });

            // Lógica de Filtros
            const searchInput = document.getElementById('searchInput');
            const categoryFilter = document.getElementById('categoryFilter');
            const statusFilter = document.getElementById('statusFilter');
            const cards = document.querySelectorAll('.card');

            function filterItems() {
                const searchTerm = searchInput.value.toLowerCase();
                const selectedCategory = categoryFilter.value;
                const selectedStatus = statusFilter.value;

                cards.forEach(card => {
                    const name = card.dataset.name;
                    const code = card.dataset.code;
                    const category = card.dataset.category;
                    const status = card.dataset.status;

                    const matchesSearch = name.includes(searchTerm) || code.includes(searchTerm);
                    const matchesCategory = !selectedCategory || category === selectedCategory;
                    const matchesStatus = !selectedStatus || status === selectedStatus;

                    if (matchesSearch && matchesCategory && matchesStatus) {
                        card.style.display = '';
                    } else {
                        card.style.display = 'none';
                    }
                });
            }

            searchInput.addEventListener('input', filterItems);
            categoryFilter.addEventListener('change', filterItems);
            statusFilter.addEventListener('change', filterItems);
        });
    </script>

</body>
</html>