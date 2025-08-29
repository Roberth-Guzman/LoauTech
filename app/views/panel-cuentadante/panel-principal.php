<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Panel Cuentadante - Loautech</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-100">
<?php require_once 'includes/navbar.php'; ?>

<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">
            <i class="fas fa-history text-blue-600 mr-2"></i>
            Historial de Aprobaciones
        </h1>
        <p class="text-gray-600">Registro de todas las solicitudes aprobadas y rechazadas por el cuentadante</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <i class="fas fa-check text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Aprobadas</p>
                    <p class="text-2xl font-semibold text-gray-900"><?= $data['estadisticas']->aprobadas ?? 0 ?></p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-red-100 text-red-600">
                    <i class="fas fa-times text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Rechazadas</p>
                    <p class="text-2xl font-semibold text-gray-900"><?= $data['estadisticas']->rechazadas ?? 0 ?></p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <i class="fas fa-list text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Procesadas</p>
                    <p class="text-2xl font-semibold text-gray-900"><?= $data['estadisticas']->total ?? 0 ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white shadow overflow-hidden sm:rounded-md">
        <div class="px-4 py-5 sm:px-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
                Registro de Aprobaciones y Rechazos
            </h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">
                Historial completo de decisiones tomadas por el cuentadante
            </p>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Solicitante</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Elemento</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cantidad</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (!empty($data['peticiones'])) : ?>
                        <?php foreach ($data['peticiones'] as $peticion) : ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= htmlspecialchars($peticion->IDpre) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= htmlspecialchars($peticion->nombrecompletoper) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= htmlspecialchars($peticion->nombreele) ?> (<?= htmlspecialchars($peticion->codigoinventario) ?>)</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= htmlspecialchars($peticion->cantidad) ?> unidad(es)</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php
                                        $estadoClass = '';
                                        $estadoTexto = '';
                                        $estadoIcon = '';
                                        if ($peticion->estadoaut === 'Pendiente') {
                                            $estadoClass = 'bg-yellow-100 text-yellow-800';
                                            $estadoTexto = 'Pendiente';
                                            $estadoIcon = 'fas fa-clock';
                                        } elseif ($peticion->estadoaut === 'Rechazado') {
                                            $estadoClass = 'bg-red-100 text-red-800';
                                            $estadoTexto = 'Rechazada';
                                            $estadoIcon = 'fas fa-times';
                                        }
                                    ?>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $estadoClass ?>">
                                        <i class="<?= $estadoIcon ?> mr-1"></i><?= $estadoTexto ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">No hay peticiones para mostrar.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Paginación -->
    <?php if ($data['paginacion']['total_paginas'] > 1): ?>
    <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6 mt-4 rounded-lg shadow">
        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
            <div>
                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                    <?php for ($i = 1; $i <= $data['paginacion']['total_paginas']; $i++): ?>
                        <a href="?pagina=<?= $i ?>" class="relative inline-flex items-center px-4 py-2 border text-sm font-medium <?= $i === $data['paginacion']['pagina_actual'] ? 'z-10 bg-blue-50 border-blue-500 text-blue-600' : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>
                </nav>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>