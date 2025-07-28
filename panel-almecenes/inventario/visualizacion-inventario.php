<?php
// Iniciar sesión al principio del script
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario'])) {
    header('Location: /login.php');
    exit();
}

// Verificar permisos
if ($_SESSION['usuario']['rol'] !== 'admin' && $_SESSION['usuario']['rol'] !== 'almacenes') {
    header('Location: /index.php');
    exit();
}

// Incluir el archivo de conexión a la base de datos
require_once __DIR__ . '/../../conexion.php';

// Resto del código HTML y PHP...
?>
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
    <!-- Navbar -->
    <nav class="bg-gray-800 text-white shadow-lg">
        <div class="container mx-auto px-4 py-3 flex justify-between items-center">
            <div class="flex items-center space-x-2">
                <i class="fas fa-eye"></i>
                <span class="font-bold">Sistema de Inventario</span>
            </div>
            <div class="flex items-center space-x-4">
                <a href="panel-solicitudes.php" class="text-gray-300 hover:text-white px-3 py-2 rounded-md text-sm font-medium">
                    <i class="fas fa-clipboard-list mr-1"></i> Solicitudes
                </a>
                <a href="elementos/panel-inventario.php" class="text-gray-300 hover:text-white px-3 py-2 rounded-md text-sm font-medium">
                    <i class="fas fa-boxes mr-1"></i> Inventario
                </a>
                <a href="visualizacion-inventario.php" class="bg-gray-900 text-white px-3 py-2 rounded-md text-sm font-medium">
                    <i class="fas fa-eye mr-1"></i> Visualización
                </a>
                <div class="relative">
                    <button class="flex items-center space-x-2 hover:bg-gray-700 px-3 py-2 rounded">
                        <i class="fas fa-user-circle"></i>
                        <span><?= htmlspecialchars($_SESSION['usuario']['nombre'] ?? 'Usuario') ?></span>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <div class="container mx-auto px-4 py-6">
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="bg-gradient-to-r from-blue-600 to-purple-700 text-white px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-boxes"></i>
                        <h2 class="text-xl font-bold">Visualización de Inventario</h2>
                    </div>
                    <div class="flex items-center space-x-2">
                        <button id="gridView" class="p-2 rounded-full hover:bg-blue-700">
                            <i class="fas fa-th-large"></i>
                        </button>
                        <button id="listView" class="p-2 rounded-full hover:bg-blue-700">
                            <i class="fas fa-list"></i>
                        </button>
                    </div>
                </div>
                <p class="mt-1 text-sm text-blue-100">Explora y busca elementos del inventario</p>
            </div>
            
            <div class="p-6">
                <!-- Filtros -->
                <div class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="md:col-span-2">
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
                            <option value="Computadores">Computadores</option>
                            <option value="Monitores">Monitores</option>
                            <option value="Teclados">Teclados</option>
                            <option value="Mouse">Mouse</option>
                            <option value="Impresoras">Impresoras</option>
                            <option value="Muebles">Muebles</option>
                            <option value="Otros">Otros</option>
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
                    <?php
                    $sql = "SELECT * FROM elementos WHERE estado = 'activo' ORDER BY nombreele ASC";
                    $result = $conn->query($sql);
                    
                    if ($result->num_rows > 0):
                        while($elemento = $result->fetch_assoc()):
                            $statusClass = '';
                            $statusText = '';
                            
                            switch($elemento['estado']) {
                                case 'activo':
                                    $statusClass = 'status-available';
                                    $statusText = 'Disponible';
                                    break;
                                case 'en prestamo':
                                    $statusClass = 'status-loaned';
                                    $statusText = 'En préstamo';
                                    break;
                                case 'inactivo':
                                    $statusClass = 'status-inactive';
                                    $statusText = 'Inactivo';
                                    break;
                            }
                    ?>
                    <div class="card bg-white rounded-lg border border-gray-200 overflow-hidden hover:shadow-md transition-shadow" 
                         data-category="<?= htmlspecialchars($elemento['caracteristicasele'] ?? '') ?>" 
                         data-status="<?= $elemento['estado'] ?>">
                        <div class="relative">
                            <?php if (!empty($elemento['imagen'])): ?>
                                <img src="/<?= htmlspecialchars($elemento['imagen']) ?>" 
                                     alt="<?= htmlspecialchars($elemento['nombreele']) ?>" 
                                     class="w-full h-48 object-cover">
                            <?php else: ?>
                                <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                                    <i class="fas fa-image text-4xl text-gray-400"></i>
                                </div>
                            <?php endif; ?>
                            <span class="absolute top-2 right-2 px-2 py-1 text-xs font-semibold rounded-full <?= $statusClass ?>">
                                <?= $statusText ?>
                            </span>
                        </div>
                        <div class="p-4">
                            <h3 class="font-semibold text-lg text-gray-800 mb-1">
                                <?= htmlspecialchars($elemento['nombreele']) ?>
                            </h3>
                            <p class="text-sm text-gray-600 mb-2">
                                <span class="font-medium">Código:</span> <?= htmlspecialchars($elemento['codigoele']) ?>
                            </p>
                            <p class="text-sm text-gray-600 mb-3">
                                <span class="font-medium">Cantidad:</span> <?= $elemento['cantidadele'] ?>
                            </p>
                            <div class="flex justify-between items-center">
                                <span class="text-xs px-2 py-1 bg-gray-100 text-gray-700 rounded">
                                    <?= htmlspecialchars($elemento['caracteristicasele'] ?? 'Sin categoría') ?>
                                </span>
                                <a href="elementos/detalles.php?id=<?= $elemento['IDele'] ?>" 
                                   class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                    Ver detalles <i class="fas fa-arrow-right ml-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php 
                        endwhile;
                    else:
                    ?>
                    <div class="col-span-full text-center py-8">
                        <i class="fas fa-inbox text-4xl text-gray-400 mb-2"></i>
                        <p class="text-gray-600">No se encontraron elementos en el inventario.</p>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Vista de lista (oculta por defecto) -->
                <div id="listViewContent" class="hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Elemento
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Código
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Categoría
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Cantidad
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Estado
                                    </th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Acciones
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php
                                if (isset($result) && $result->num_rows > 0) {
                                    $result->data_seek(0); // Reiniciar el puntero del resultado
                                    while($elemento = $result->fetch_assoc()):
                                        $statusClass = '';
                                        $statusText = '';
                                        
                                        switch($elemento['estado']) {
                                            case 'activo':
                                                $statusClass = 'status-available';
                                                $statusText = 'Disponible';
                                                break;
                                            case 'en prestamo':
                                                $statusClass = 'status-loaned';
                                                $statusText = 'En préstamo';
                                                break;
                                            case 'inactivo':
                                                $statusClass = 'status-inactive';
                                                $statusText = 'Inactivo';
                                                break;
                                        }
                                ?>
                                <tr class="hover:bg-gray-50" 
                                    data-category="<?= htmlspecialchars($elemento['caracteristicasele'] ?? '') ?>" 
                                    data-status="<?= $elemento['estado'] ?>">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <?php if (!empty($elemento['imagen'])): ?>
                                                    <img class="h-10 w-10 rounded-full object-cover" 
                                                         src="/<?= htmlspecialchars($elemento['imagen']) ?>" 
                                                         alt="<?= htmlspecialchars($elemento['nombreele']) ?>">
                                                <?php else: ?>
                                                    <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                                                        <i class="fas fa-box text-gray-400"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">
                                                    <?= htmlspecialchars($elemento['nombreele']) ?>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900"><?= htmlspecialchars($elemento['codigoele']) ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                            <?= htmlspecialchars($elemento['caracteristicasele'] ?? 'Sin categoría') ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?= $elemento['cantidadele'] ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $statusClass ?>">
                                            <?= $statusText ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="elementos/detalles.php?id=<?= $elemento['IDele'] ?>" 
                                           class="text-blue-600 hover:text-blue-900">Ver detalles</a>
                                    </td>
                                </tr>
                                <?php 
                                    endwhile;
                                } else {
                                ?>
                                <tr>
                                    <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                        No se encontraron elementos en el inventario.
                                    </td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Cambiar entre vista de cuadrícula y lista
        document.getElementById('gridView').addEventListener('click', function() {
            document.getElementById('gridViewContent').classList.remove('hidden');
            document.getElementById('listViewContent').classList.add('hidden');
            this.classList.add('bg-blue-700');
            document.getElementById('listView').classList.remove('bg-blue-700');
        });

        document.getElementById('listView').addEventListener('click', function() {
            document.getElementById('listViewContent').classList.remove('hidden');
            document.getElementById('gridViewContent').classList.add('hidden');
            this.classList.add('bg-blue-700');
            document.getElementById('gridView').classList.remove('bg-blue-700');
        });

        // Filtros
        const searchInput = document.getElementById('searchInput');
        const categoryFilter = document.getElementById('categoryFilter');
        const statusFilter = document.getElementById('statusFilter');
        const cards = document.querySelectorAll('.card');
        const rows = document.querySelectorAll('tbody tr');

        function filterItems() {
            const searchTerm = searchInput.value.toLowerCase();
            const selectedCategory = categoryFilter.value;
            const selectedStatus = statusFilter.value;

            // Función para verificar si un elemento coincide con los filtros
            function matchesFilters(element) {
                const name = element.querySelector('h3') ? element.querySelector('h3').textContent.toLowerCase() : '';
                const code = element.querySelector('p:nth-of-type(1)') ? 
                             element.querySelector('p:nth-of-type(1)').textContent.toLowerCase() : '';
                
                const category = element.getAttribute('data-category') || '';
                const status = element.getAttribute('data-status') || '';

                const matchesSearch = name.includes(searchTerm) || code.includes(searchTerm);
                const matchesCategory = !selectedCategory || category === selectedCategory;
                const matchesStatus = !selectedStatus || status === selectedStatus;

                return matchesSearch && matchesCategory && matchesStatus;
            }

            // Aplicar filtros a las tarjetas (vista de cuadrícula)
            cards.forEach(card => {
                if (matchesFilters(card)) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });

            // Aplicar filtros a las filas (vista de lista)
            rows.forEach(row => {
                if (row.getAttribute('data-category') !== null) { // Solo filas de datos, no el encabezado
                    if (matchesFilters(row)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                }
            });
        }

        // Agregar event listeners para los filtros
        searchInput.addEventListener('input', filterItems);
        categoryFilter.addEventListener('change', filterItems);
        statusFilter.addEventListener('change', filterItems);
    </script>
</body>
</html>