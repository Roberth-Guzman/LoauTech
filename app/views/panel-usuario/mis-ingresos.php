<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($data['page_title'] ?? 'Mis Ingresos') ?> - LOAUTECH</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="flex h-screen">
        <?php include 'includes/sidebar-usuario.php'; ?>

        <div class="flex-1 ml-64 overflow-auto">
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    <h1 class="text-2xl font-bold text-gray-900">Mis Ingresos</h1>
                </div>
            </header>

            <main class="p-6">
                <?php if (!empty($data['mensaje'])): ?>
                    <div class="mb-4 text-sm text-green-700 bg-green-100 p-3 rounded">
                        <?= htmlspecialchars($data['mensaje']) ?>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($data['error'])): ?>
                  <div class="mb-4 text-sm text-red-700 bg-red-100 p-3 rounded">
                    <?= htmlspecialchars($data['error']) ?>
                  </div>
                <?php endif; ?>

                <div class="bg-white shadow overflow-hidden rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Descripción</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Observación</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Hora Entrada</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Hora Salida</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Acción</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php if (!empty($data['elementos'])): ?>
                                <?php foreach ($data['elementos'] as $e): ?>
                                    <tr>
                                        <td class="px-4 py-2 text-sm"><?= htmlspecialchars($e->nombreingele) ?></td>
                                        <td class="px-4 py-2 text-sm"><?= htmlspecialchars($e->tipoelemento) ?></td>
                                        <td class="px-4 py-2 text-sm"><?= htmlspecialchars($e->descripcioningele) ?></td>
                                        <td class="px-4 py-2 text-sm"><?= htmlspecialchars($e->observacioningele) ?></td>
                                        <td class="px-4 py-2 text-sm"><?= htmlspecialchars($e->hora_entrada) ?></td>
                                        <td class="px-4 py-2 text-sm">
                                            <?= $e->hora_salida ? htmlspecialchars($e->hora_salida) : '<span class="text-red-500">Pendiente</span>' ?>
                                        </td>
                                        <td class="px-4 py-2 text-sm">
                                            <?php if (!$e->hora_salida): ?>
                                                <form method="POST" action="<?= BASE_URL ?>/ingreso/registrarSalida" class="inline">
                                                    <input type="hidden" name="registro_salida_id" value="<?= $e->IDingele ?>">
                                                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white text-xs px-3 py-1 rounded">
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
                                    <td colspan="7" class="text-center py-4 text-gray-500 text-sm">No se han registrado elementos aún.</td>
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