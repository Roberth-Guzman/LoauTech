<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Historial de Ingresos - LOAUTECH</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">
    <div class="flex min-h-screen">
        <?php include 'includes/sidebar-usuario.php'; ?>

        <div class="flex-1 flex flex-col ml-64">
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    <h1 class="text-2xl font-bold text-gray-900">Historial de Ingresos</h1>
                </div>
            </header>

            <main class="flex-grow p-6">
                <div class="bg-white shadow overflow-hidden rounded-lg">
                    <div class="px-4 py-5 sm:px-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Registros Históricos</h3>
                        <p class="mt-1 max-w-2xl text-sm text-gray-500">Aquí puedes ver todos tus registros de entrada y salida de elementos.</p>
                    </div>
                    <div class="border-t border-gray-200">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Elemento</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Serial</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hora de Entrada</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hora de Salida</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php if (!empty($data['historial'])) : ?>
                                    <?php foreach ($data['historial'] as $registro) : ?>
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?= htmlspecialchars($registro->nombreingele); ?></td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= htmlspecialchars($registro->serial ? $registro->serial : 'N/A'); ?></td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= htmlspecialchars($registro->tipoelemento); ?></td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= htmlspecialchars(date('d/m/Y h:i A', strtotime($registro->hora_entrada))); ?></td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                <?php if ($registro->hora_salida && $registro->hora_salida != '0000-00-00 00:00:00') : ?>
                                                    <?= htmlspecialchars(date('d/m/Y h:i A', strtotime($registro->hora_salida))); ?>
                                                <?php else : ?>
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                        Salida no registrada
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">No tienes registros históricos.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                 <!-- Paginación -->
            <div class="mt-6 flex justify-between items-center">
                <div>
                    <span class="text-sm text-gray-500">
                        Página <?php echo $data['paginaActual']; ?> de <?php echo $data['totalPaginas']; ?>
                    </span>
                </div>
                <div class="flex space-x-2">
                    <?php if ($data['paginaActual'] > 1) : ?>
                        <a href="/mvc_dev/usuario/historialIngresos/<?php echo $data['paginaActual'] - 1; ?>" class="px-4 py-2 text-sm font-medium text-white bg-gray-800 rounded-md hover:bg-gray-700">
                            Anterior
                        </a>
                    <?php endif; ?>

                    <?php if ($data['paginaActual'] < $data['totalPaginas']) : ?>
                        <a href="/mvc_dev/usuario/historialIngresos/<?php echo $data['paginaActual'] + 1; ?>" class="px-4 py-2 text-sm font-medium text-white bg-gray-800 rounded-md hover:bg-gray-700">
                            Siguiente
                        </a>
                    <?php endif; ?> 
                </div>
            </div>

        </div>
    </main>
</div>