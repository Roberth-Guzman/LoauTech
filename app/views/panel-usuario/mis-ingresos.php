<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($data['page_title'] ?? 'Mis Ingresos') ?> - LOAUTECH</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="flex min-h-screen">
        <?php include 'includes/sidebar-usuario.php'; ?>

        <div class="flex-1 ml-64 overflow-auto">
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 flex justify-between items-center">
                    <h1 class="text-2xl font-bold text-gray-900">Mis Ingresos</h1>
                    <a href="/mvc_dev/usuario/historialIngresos" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fa-solid fa-clock-rotate-left mr-2"></i>
                        Ver Historial
                    </a>
                </div>
            </header>

            <div class="main-content p-6">
                <div class="space-y-6">
                    <!-- Formulario de Registro de Ingreso -->
                    <div class="bg-white shadow overflow-hidden rounded-lg">
                        <div class="px-4 py-5 sm:px-6">
                            <h2 class="text-lg leading-6 font-medium text-gray-900">Registrar Ingreso Diario</h2>
                            <p class="mt-1 max-w-2xl text-sm text-gray-500">Selecciona los elementos de tu inventario
                                que ingresas hoy.</p>
                        </div>
                        <div class="border-t border-gray-200">
                            <?php if (!empty($data['inventario'])): ?>
                                <form action="/mvc_dev/ingreso/registrarIngresoDiario" method="POST">
                                    <div class="p-6">
                                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                            <?php foreach ($data['inventario'] as $item): ?>
                                                <div class="flex items-start">
                                                    <div class="flex items-center h-5">
                                                        <input id="item_<?= htmlspecialchars($item->identificador_unico) ?>"
                                                            name="elementos[<?= htmlspecialchars($item->identificador_unico) ?>]"
                                                            type="checkbox"
                                                            class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                                                            value="<?= htmlspecialchars(json_encode(['nombre' => $item->nombreingele, 'tipo' => $item->tipoelemento, 'descripcion' => $item->descripcioningele, 'serial' => $item->serial])) ?>">
                                                    </div>
                                                    <div class="ml-3 text-sm">
                                                        <label for="item_<?= htmlspecialchars($item->identificador_unico) ?>"
                                                            class="font-medium text-gray-700"><?= htmlspecialchars($item->nombreingele) ?></label>
                                                        <p class="text-gray-500"><?= htmlspecialchars($item->tipoelemento) ?> -
                                                            <?= htmlspecialchars($item->serial ?? 'N/A') ?>
                                                        </p>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        <div class="mt-4">
                                            <label for="observaciones"
                                                class="block text-sm font-medium text-gray-700">Observaciones
                                                generales</label>
                                            <textarea name="observaciones" id="observaciones" rows="3"
                                                class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border border-gray-300 rounded-md"></textarea>
                                        </div>
                                    </div>
                                    <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
                                        <button type="submit"
                                            class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                            Registrar Ingreso
                                        </button>
                                    </div>
                                </form>
                            <?php else: ?>
                                <p class="p-6 text-sm text-gray-500">Todos los elementos de tu inventario ya han sido
                                    ingresados
                                    hoy o no tienes elementos en tu inventario.</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Tabla de Elementos Ingresados Hoy -->
                    <div class="bg-white shadow overflow-hidden rounded-lg">
                        <div class="px-4 py-5 sm:px-6">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Elementos Ingresados Hoy</h3>
                        </div>
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nombre
                                    </th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Tipo
                                    </th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">
                                        Descripción
                                    </th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">
                                        Observación
                                    </th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Hora
                                        Entrada
                                    </th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Hora
                                        Salida
                                    </th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Acción
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php if (!empty($data['elementos'])): ?>
                                    
                                    <?php foreach ($data['elementos'] as $e): ?>
                                        <tr>
                                            <td class="px-4 py-2 text-sm"><?= htmlspecialchars($e->nombreingele) ?></td>
                                            <td class="px-4 py-2 text-sm"><?= htmlspecialchars($e->tipoelemento) ?></td>
                                            <td class="px-4 py-2 text-sm"><?= htmlspecialchars($e->descripcioningele) ?></td>
                                            <td class="px-4 py-2 text-sm"><?= htmlspecialchars(!empty($e->observacioningele) ? $e->observacioningele : 'Sin observación') ?></td>
                                            <td class="px-4 py-2 text-sm"><?= htmlspecialchars(date('h:i A', strtotime($e->hora_entrada))) ?></td>
                                            <td class="px-4 py-2 text-sm">
                                                <?= $e->hora_salida ? htmlspecialchars(date('h:i A', strtotime($e->hora_salida))) : '<span class="text-red-500">Pendiente</span>' ?>
                                            </td>
                                            <td class="px-4 py-2 text-sm">
                                                <?php if (!$e->hora_salida): ?>
                                                    <form method="POST" action="/mvc_dev/ingreso/registrarSalida"
                                                        class="inline">
                                                        <input type="hidden" name="registro_salida_id" value="<?= $e->IDingele ?>">
                                                        <button type="submit"
                                                            class="bg-blue-500 hover:bg-blue-600 text-white text-xs px-3 py-1 rounded">
                                                            Registrar salida
                                                        </button>
                                                    </form>
                                                <?php else: ?>
                                                    <span class="text-green-600 font-semibold text-xs">Completado</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-gray-500 text-sm">No se han registrado
                                            elementos aún.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    </main>


                </div>
                
            </div>
            <?php include 'includes/footer-usuario.php'; ?>
</body>

</html>