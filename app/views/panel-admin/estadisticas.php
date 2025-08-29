<?php
// Asegurarse de que el array $data y sus claves existen para evitar errores
$total_usuarios = $data['total_usuarios'] ?? 0;
$total_elementos = $data['total_elementos'] ?? 0;
$peticiones_hoy = $data['peticiones_hoy'] ?? 0;
$alertas_seguridad = $data['alertas_seguridad'] ?? 0;
$distribucion_roles = $data['distribucion_roles'] ?? [];
$distribucion_inventario = $data['distribucion_inventario'] ?? [];
$top_elementos = $data['top_elementos'] ?? [];
$tiempo_aprobacion_promedio = $data['tiempo_aprobacion_promedio'] ?? 'N/A';

// Preparar datos para los gráficos
$roles_labels = json_encode(array_map(fn($rol) => $rol->rol, $distribucion_roles));
$roles_counts = json_encode(array_map(fn($rol) => $rol->count, $distribucion_roles));

$inventario_labels = json_encode(array_map(fn($item) => $item->estado, $distribucion_inventario));
$inventario_counts = json_encode(array_map(fn($item) => $item->count, $distribucion_inventario));
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Superadministrador - Estadísticas Avanzadas</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-100">

    <div class="flex">
        <?php require_once 'includes/sidebar-admin.php'; ?>

        <div class="flex-1 p-10">
            <h1 class="text-4xl font-bold text-gray-800 mb-8">Dashboard de Superadministrador</h1>

            <!-- KPIs Principales -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-white p-6 rounded-lg shadow-md flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Total de Usuarios</p>
                        <p class="text-3xl font-bold text-gray-800"><?php echo $total_usuarios; ?></p>
                    </div>
                    <i class="fas fa-users text-4xl text-blue-500"></i>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Total de Ítems en Inventario</p>
                        <p class="text-3xl font-bold text-gray-800"><?php echo $total_elementos; ?></p>
                    </div>
                    <i class="fas fa-boxes text-4xl text-green-500"></i>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Total de Peticiones</p>
                        <p class="text-3xl font-bold text-gray-800"><?php echo $peticiones_hoy; ?></p>
                    </div>
                    <i class="fas fa-clipboard-list text-4xl text-yellow-500"></i>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Alertas de Seguridad</p>
                        <p class="text-3xl font-bold text-gray-800"><?php echo $alertas_seguridad; ?></p>
                    </div>
                    <i class="fas fa-shield-alt text-4xl text-red-500"></i>
                </div>
            </div>

            <!-- Gráficos -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                <div class="bg-white p-6 rounded-lg shadow-md flex flex-col">
                    <h2 class="text-xl font-bold text-gray-700 mb-4">Distribución de Usuarios por Rol</h2>
                    <div class="relative h-80">
                        <canvas id="userRoleChart"></canvas>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md flex flex-col">
                    <h2 class="text-xl font-bold text-gray-700 mb-4">Estado General del Inventario</h2>
                    <div class="relative h-80">
                        <canvas id="inventoryStatusChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Nuevas Estadísticas de Eficiencia y Uso -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
                <div class="lg:col-span-2 bg-white p-6 rounded-lg shadow-md">
                    <h2 class="text-xl font-bold text-gray-700 mb-4">Top 5 Elementos Más Solicitados</h2>
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white">
                            <thead class="bg-gray-200">
                                <tr>
                                    <th class="py-2 px-4 text-left text-sm font-semibold text-gray-600">#</th>
                                    <th class="py-2 px-4 text-left text-sm font-semibold text-gray-600">Nombre del Elemento</th>
                                    <th class="py-2 px-4 text-center text-sm font-semibold text-gray-600">Nº de Solicitudes</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($top_elementos)): ?>
                                    <?php foreach ($top_elementos as $index => $elemento): ?>
                                        <tr class="border-b">
                                            <td class="py-2 px-4 text-gray-700"><?php echo $index + 1; ?></td>
                                            <td class="py-2 px-4 text-gray-700"><?php echo htmlspecialchars($elemento->nombreele); ?></td>
                                            <td class="py-2 px-4 text-center text-gray-700 font-bold"><?php echo $elemento->total_solicitudes; ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="3" class="py-4 px-4 text-center text-gray-500">No hay datos de solicitudes disponibles.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md flex flex-col justify-center items-center">
                    <h2 class="text-xl font-bold text-gray-700 mb-2 text-center">Tiempo Promedio de Aprobación</h2>
                    <p class="text-4xl font-bold text-purple-600 mb-2"><?php echo $tiempo_aprobacion_promedio; ?></p>
                    <p class="text-sm text-gray-500 text-center">(Desde la creación hasta la aprobación)</p>
                </div>
            </div>

            <!-- Tabla de Actividad Crítica -->
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-xl font-bold text-gray-700 mb-4">Log de Actividad Crítica Reciente</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white">
                        <thead class="bg-gray-200">
                            <tr>
                                <th class="py-2 px-4 text-left text-sm font-semibold text-gray-600">Timestamp</th>
                                <th class="py-2 px-4 text-left text-sm font-semibold text-gray-600">Usuario</th>
                                <th class="py-2 px-4 text-left text-sm font-semibold text-gray-600">Acción Crítica</th>
                                <th class="py-2 px-4 text-left text-sm font-semibold text-gray-600">Detalles</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Fila de ejemplo 1 -->
                            <tr class="border-b">
                                <td class="py-2 px-4 text-gray-700">2023-10-27 10:15:00</td>
                                <td class="py-2 px-4 text-gray-700">admin@loatech.com</td>
                                <td class="py-2 px-4 text-red-600 font-semibold">Eliminación de Usuario</td>
                                <td class="py-2 px-4 text-gray-700">Usuario 'test_user' eliminado</td>
                            </tr>
                            <!-- Fila de ejemplo 2 -->
                            <tr class="border-b">
                                <td class="py-2 px-4 text-gray-700">2023-10-27 09:30:12</td>
                                <td class="py-2 px-4 text-gray-700">superadmin@loatech.com</td>
                                <td class="py-2 px-4 text-yellow-600 font-semibold">Cambio de Rol</td>
                                <td class="py-2 px-4 text-gray-700">Rol de 'user@loatech.com' cambiado a 'Almacén'</td>
                            </tr>
                            <!-- Fila de ejemplo 3 -->
                            <tr>
                                <td class="py-2 px-4 text-gray-700">2023-10-26 18:05:45</td>
                                <td class="py-2 px-4 text-gray-700">admin@loatech.com</td>
                                <td class="py-2 px-4 text-blue-600 font-semibold">Reseteo de Contraseña</td>
                                <td class="py-2 px-4 text-gray-700">Contraseña reseteada para 'support@loatech.com'</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Gráfico de Distribución de Usuarios por Rol
            const userRoleCtx = document.getElementById('userRoleChart').getContext('2d');
            new Chart(userRoleCtx, {
                type: 'doughnut',
                data: {
                    labels: <?php echo $roles_labels; ?>,
                    datasets: [{
                        label: 'Nº de Usuarios',
                        data: <?php echo $roles_counts; ?>,
                        backgroundColor: ['#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6'],
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                        }
                    }
                }
            });

            // Gráfico de Estado General del Inventario
            const inventoryStatusCtx = document.getElementById('inventoryStatusChart').getContext('2d');
            new Chart(inventoryStatusCtx, {
                type: 'bar',
                data: {
                    labels: <?php echo $inventario_labels; ?>,
                    datasets: [{
                        label: 'Cantidad de Ítems',
                        data: <?php echo $inventario_counts; ?>,
                        backgroundColor: ['#10B981', '#F59E0B', '#6B7280', '#3B82F6'],
                        borderColor: ['#059669', '#D97706', '#4B5563', '#2563EB'],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
        });
    </script>

</body>
</html>