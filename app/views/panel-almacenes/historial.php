<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($data['titulo']); ?></title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome para iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-100">

    <?php 
    // Incluimos la barra de navegación específica del panel de almacenes
    require_once 'includes/navbar.php'; 
    ?>

    <div class="container mx-auto px-4 sm:px-8 py-8">
        <h1 class="text-2xl font-bold text-gray-800 mb-4">Historial de Solicitudes Gestionadas</h1>

        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b">
                <h2 class="text-lg font-semibold text-gray-700">Registros de Aprobaciones y Rechazos</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full leading-normal">
                    <thead>
                        <tr>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                ID Préstamo
                            </th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Solicitante
                            </th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Elemento
                            </th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Estado
                            </th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Fecha
                            </th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Motivo de Rechazo
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($data['historial'])) : ?>
                            <?php foreach ($data['historial'] as $registro) : ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-5 py-4 border-b border-gray-200 text-sm">
                                        <p class="text-gray-900 whitespace-no-wrap"><?php echo htmlspecialchars($registro->IDpre); ?></p>
                                    </td>
                                    <td class="px-5 py-4 border-b border-gray-200 text-sm">
                                        <p class="text-gray-900 whitespace-no-wrap"><?php echo htmlspecialchars($registro->solicitante); ?></p>
                                    </td>
                                    <td class="px-5 py-4 border-b border-gray-200 text-sm">
                                        <p class="text-gray-900 whitespace-no-wrap"><?php echo htmlspecialchars($registro->elemento); ?></p>
                                    </td>
                                    <td class="px-5 py-4 border-b border-gray-200 text-sm">
                                        <?php
                                        $estado = htmlspecialchars($registro->estado);
                                        $badge_class = 'bg-gray-200 text-gray-800';
                                        if ($estado === 'aprobado') {
                                            $badge_class = 'bg-green-200 text-green-800';
                                        } elseif ($estado === 'rechazado') {
                                            $badge_class = 'bg-red-200 text-red-800';
                                        }
                                        echo "<span class='px-2 py-1 font-semibold leading-tight " . $badge_class . " rounded-full text-xs'>" . ucfirst($estado) . "</span>";
                                        ?>
                                    </td>
                                    <td class="px-5 py-4 border-b border-gray-200 text-sm">
                                        <?php
                                        $fecha_evento = $registro->estado === 'aprobado' ? $registro->fecha_aprobacion : $registro->fecha_rechazo;
                                        if ($fecha_evento) {
                                            echo htmlspecialchars(date('d/m/Y H:i', strtotime($fecha_evento)));
                                        } else {
                                            echo 'N/A';
                                        }
                                        ?>
                                    </td>
                                    <td class="px-5 py-4 border-b border-gray-200 text-sm">
                                        <p class="text-gray-900 whitespace-no-wrap"><?php echo htmlspecialchars($registro->motivo ?? 'N/A'); ?></p>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <tr>
                                <td colspan="6" class="text-center py-10">
                                    <p class="text-gray-500">No hay registros en el historial.</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <?php if ($data['total_paginas'] > 1) : ?>
                <div class="px-5 py-5 bg-white border-t flex flex-col xs:flex-row items-center xs:justify-between">
                    <span class="text-xs xs:text-sm text-gray-900">
                        Página <?php echo $data['pagina_actual']; ?> de <?php echo $data['total_paginas']; ?> (Total: <?php echo $data['total_registros']; ?> registros)
                    </span>
                    <div class="inline-flex mt-2 xs:mt-0">
                        <a href="<?php echo BASE_URL; ?>/almacen/historial/<?php echo $data['pagina_actual'] - 1; ?>"
                           class="text-sm bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-4 rounded-l <?php echo ($data['pagina_actual'] <= 1) ? 'opacity-50 cursor-not-allowed' : ''; ?>">
                            Anterior
                        </a>
                        <a href="<?php echo BASE_URL; ?>/almacen/historial/<?php echo $data['pagina_actual'] + 1; ?>"
                           class="text-sm bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-4 rounded-r <?php echo ($data['pagina_actual'] >= $data['total_paginas']) ? 'opacity-50 cursor-not-allowed' : ''; ?>">
                            Siguiente
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>