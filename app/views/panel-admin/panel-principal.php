<?php
// Datos del usuario y estadísticas (pasados desde el controlador)
$usuario = $data['usuario'] ?? new stdClass();
$estadisticas = $data['estadisticas'] ?? new stdClass();
$ultimos_usuarios = $data['ultimos_usuarios'] ?? [];
$ultimos_elementos = $data['ultimos_elementos'] ?? [];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración - LoauTech</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-100 font-sans">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <?php require_once 'includes/sidebar-admin.php'; ?>
         <div class="flex-1 p-10"> 
            
            <!-- Cabecera -->
            <header class="mb-8">
                <h1 class="text-3xl font-bold text-gray-800">Bienvenido, <?= htmlspecialchars($usuario->nombre ?? 'Admin') ?></h1>
                <p class="text-gray-500">Este es el resumen de la actividad en LoauTech.</p>
            </header>

            <!-- Estadísticas -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                <div class="bg-white p-6 rounded-lg shadow-md flex items-center">
                    <i class="fas fa-users text-4xl text-blue-500 mr-4"></i>
                    <div>
                        <p class="text-gray-500">Total de Usuarios</p>
                        <p class="text-2xl font-bold"><?= $estadisticas->total_usuarios ?? '0' ?></p>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md flex items-center">
                    <i class="fas fa-boxes text-4xl text-green-500 mr-4"></i>
                    <div>
                        <p class="text-gray-500">Total de Elementos</p>
                        <p class="text-2xl font-bold"><?= $estadisticas->total_elementos ?? '0' ?></p>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md flex items-center">
                    <i class="fas fa-hand-holding-usd text-4xl text-yellow-500 mr-4"></i>
                    <div>
                        <p class="text-gray-500">Préstamos Activos</p>
                        <p class="text-2xl font-bold"><?= $estadisticas->prestamos_activos ?? '0' ?></p>
                    </div>
                </div>
            </div>

            <!-- Listas Recientes -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                
                <!-- Últimos Usuarios Registrados -->
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h2 class="text-xl font-bold mb-4">Últimos Usuarios Registrados</h2>
                    <ul class="space-y-3">
                        <?php if (!empty($ultimos_usuarios)): ?>
                            <?php foreach ($ultimos_usuarios as $u): ?>
                                <li class="flex justify-between items-center p-2 rounded hover:bg-gray-50">
                                    <span><?= htmlspecialchars($u->nombrecompletoper) ?></span>
                                    <span class="text-sm text-gray-500"><?= htmlspecialchars($u->numerodoc) ?></span>
                                </li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-gray-500">No hay usuarios recientes.</p>
                        <?php endif; ?>
                    </ul>
                </div>

                <!-- Últimos Elementos Añadidos -->
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h2 class="text-xl font-bold mb-4">Últimos Elementos Añadidos</h2>
                    <ul class="space-y-3">
                        <?php if (!empty($ultimos_elementos)): ?>
                            <?php foreach ($ultimos_elementos as $e): ?>
                                <li class="flex justify-between items-center p-2 rounded hover:bg-gray-50">
                                    <div>
                                        <p><?= htmlspecialchars($e->nombreele) ?></p>
                                        <p class="text-xs text-gray-400"><?= htmlspecialchars($e->codigoele) ?></p>
                                    </div>
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full
                                        <?= $e->estado === 'Disponible' ? 'bg-green-200 text-green-800' : 'bg-yellow-200 text-yellow-800' ?>">
                                        <?= htmlspecialchars($e->estado) ?>
                                    </span>
                                </li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-gray-500">No hay elementos recientes.</p>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>

        </div>
    </div>

</body>
</html>