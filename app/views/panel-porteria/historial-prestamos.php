<?php
// Definir rutas a los includes
$sidebar_path = __DIR__ . '/includes/sidebar-porteria.php';
$footer_path = __DIR__ . '/includes/footer-porteria.php';

// Variables para los filtros, con valores por defecto
$titulo = $data['titulo'] ?? 'Historial de Préstamos';
$registros = $data['registros'] ?? [];
$filtro_tiempo = $data['filtro_tiempo'] ?? 'diario';
$busqueda_texto = $data['busqueda_texto'] ?? '';
$filtro_estado = $data['filtro_estado'] ?? '';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($titulo); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-100">

<div class="flex">
    <?php include $sidebar_path; ?>

    <!-- Contenido principal -->
    <div class="flex-1 p-4 sm:ml-64">
        <div class="bg-white p-6 rounded-lg shadow-lg">
            <h1 class="text-2xl font-bold mb-4"><?php echo htmlspecialchars($titulo); ?></h1>

            <!-- Filtros -->
            <div class="mb-4 p-4 bg-gray-50 rounded-lg border">
                <!-- Botones de filtro de tiempo -->
                <div class="flex space-x-2 mb-4">
                    <a href="?url=porteria/historialPrestamos&filtro_tiempo=diario" class="px-4 py-2 rounded text-white <?php echo ($filtro_tiempo === 'diario') ? 'bg-blue-700' : 'bg-blue-500 hover:bg-blue-600'; ?>">
                       Préstamos de Hoy (6 AM - 11 PM)
                    </a>
                    <a href="?url=porteria/historialPrestamos&filtro_tiempo=todos" class="px-4 py-2 rounded text-white <?php echo ($filtro_tiempo === 'todos') ? 'bg-blue-700' : 'bg-blue-500 hover:bg-blue-600'; ?>">
                       Historial Completo
                    </a>
                </div>

                <!-- Formulario de búsqueda y filtro de estado -->
                <form action="" method="get">
                    <input type="hidden" name="url" value="porteria/historialPrestamos">
                    <input type="hidden" name="filtro_tiempo" value="<?php echo htmlspecialchars($filtro_tiempo); ?>">
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="busqueda_texto" class="block text-sm font-medium text-gray-700">Buscar por Nombre o Elemento</label>
                            <input type="text" id="busqueda_texto" name="busqueda_texto" placeholder="Ej: Juan Perez, Portátil..." class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" value="<?php echo htmlspecialchars($busqueda_texto); ?>">
                        </div>
                        <div>
                            <label for="filtro_estado" class="block text-sm font-medium text-gray-700">Filtrar por Estado</label>
                            <select id="filtro_estado" name="filtro_estado" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm">
                                <option value="">Todos los estados</option>
                                <option value="en_prestamo" <?php echo ($filtro_estado === 'en_prestamo') ? 'selected' : ''; ?>>En Préstamo</option>
                                <option value="devuelto" <?php echo ($filtro_estado === 'devuelto') ? 'selected' : ''; ?>>Devuelto</option>
                            </select>
                        </div>
                        <div class="flex items-end">
                            <button type="submit" class="w-full bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700">
                                Aplicar Filtros
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Tabla de registros -->
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white">
                    <thead class="bg-gray-800 text-white">
                        <tr>
                            <th class="py-2 px-4 border-b">ID</th>
                            <th class="py-2 px-4 border-b">Elemento</th>
                            <th class="py-2 px-4 border-b">Persona</th>
                            <th class="py-2 px-4 border-b">Cantidad</th>
                            <th class="py-2 px-4 border-b">Lugar Traslado</th>
                            <th class="py-2 px-4 border-b">Fecha Solicitud</th>
                            <th class="py-2 px-4 border-b">Fecha Salida</th>
                            <th class="py-2 px-4 border-b">Fecha Devolución</th>
                            <th class="py-2 px-4 border-b">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700">
                        <?php if (!empty($registros)): ?>
                            <?php foreach ($registros as $registro): ?>
                                <tr>
                                    <td class='py-2 px-4 border-b'><?php echo htmlspecialchars($registro->IDpre); ?></td>
                                    <td class='py-2 px-4 border-b'><?php echo htmlspecialchars($registro->nombre_elemento); ?></td>
                                    <td class='py-2 px-4 border-b'><?php echo htmlspecialchars($registro->nombre_persona); ?></td>
                                    <td class='py-2 px-4 border-b'><?php echo htmlspecialchars($registro->cantidad); ?></td>
                                    <td class='py-2 px-4 border-b'><?php echo htmlspecialchars($registro->lugardetraslado); ?></td>
                                    <td class='py-2 px-4 border-b'><?php echo htmlspecialchars($registro->fecha_solicitud); ?></td>
                                    <td class='py-2 px-4 border-b'><?php echo $registro->fecha_salida ? htmlspecialchars($registro->fecha_salida) : 'N/A'; ?></td>
                                    <td class='py-2 px-4 border-b'><?php echo $registro->fecha_devolucion ? htmlspecialchars($registro->fecha_devolucion) : 'En préstamo'; ?></td>
                                    <td class='py-2 px-4 border-b'>
                                        <?php
                                        $estado = htmlspecialchars($registro->estado);
                                        $clase_css = ($estado == 'en_prestamo') ? 'bg-yellow-500 text-white' : 'bg-green-500 text-white';
                                        ?>
                                        <span class='px-2 py-1 rounded-full text-xs font-semibold <?php echo $clase_css; ?>'>
                                            <?php echo ucfirst(str_replace('_', ' ', $estado)); ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="9" class="py-4 px-4 text-center">No hay registros para mostrar con los filtros actuales.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include $footer_path; ?>

</body>
</html>