<?php
// Configuración de errores
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error.log');

// Incluir archivo de conexión
require_once __DIR__ . '/../../conexion.php';

// Iniciar sesión
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar autenticación y permisos
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'cuentadante') {
    header('Location: ../../login.php');
    exit;
}

// Función para logging
function logError($message) {
    error_log("[" . date('Y-m-d H:i:s') . "] " . $message . "\n", 3, __DIR__ . '/error.log');
}

// Configuración de paginación
$porPagina = 10;
$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$inicio = ($pagina - 1) * $porPagina;

// Obtener total de registros
$sqlTotal = "SELECT COUNT(*) as total
             FROM prestamos p
             JOIN personas per ON p.IDpersonas = per.IDper
             JOIN elementos e ON p.IDelementos = e.IDele
             JOIN autorizacion a ON p.IDautorizacion = a.IDaut
             LEFT JOIN aprobaciones ap ON p.IDpre = ap.IDprestamo
             WHERE (a.estadoaut = 'pendiente_almacen' OR a.estadoaut = 'rechazado') 
             AND ap.tipo_aprobacion = 'cuentadante'";

$totalResult = $conn->query($sqlTotal);
$totalRegistros = $totalResult->fetch_assoc()['total'];
$totalPaginas = ceil($totalRegistros / $porPagina);

// Consulta principal
$sql = "SELECT 
            p.IDpre,
            p.cantidad,
            p.formacionodependencia,
            p.cargopre,
            p.lugardetraslado,
            per.nombrecompletoper as solicitante,
            per.numerodoc as documento,
            e.nombreele as elemento,
            e.codigoele as codigo_elemento,
            a.estadoaut as estado_autorizacion,
            ap.estado as estado_aprobacion,
            ap.fecha_creacion,
            ap.fecha_actualizacion,
            ap.aprobado_por,
            ap.tipo_aprobacion,
            ap.motivo
        FROM prestamos p
        JOIN personas per ON p.IDpersonas = per.IDper
        JOIN elementos e ON p.IDelementos = e.IDele
        JOIN autorizacion a ON p.IDautorizacion = a.IDaut
        LEFT JOIN aprobaciones ap ON p.IDpre = ap.IDprestamo
        WHERE (a.estadoaut = 'pendiente_almacen' OR a.estadoaut = 'rechazado') 
        AND ap.tipo_aprobacion = 'cuentadante'
        ORDER BY ap.fecha_actualizacion DESC, ap.fecha_creacion DESC
        LIMIT $inicio, $porPagina";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial de Aprobaciones - Cuentadante</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="min-h-screen">
        <!-- Navbar -->
        <nav class="bg-gray-800 text-white shadow-lg">
            <div class="container mx-auto px-4 py-3 flex justify-between items-center">
                <div class="flex items-center space-x-2">
                    <i class="fas fa-clipboard-check"></i>
                    <span class="font-bold">Gestión de Peticiones - Cuentadante</span>
                </div>
                <div class="hidden md:flex space-x-6">
                    <a href="panel-peticiones.php" class="px-3 py-2 rounded-md text-white hover:bg-gray-700 transition-colors">
                        <i class="fas fa-clipboard-list mr-1"></i> Peticiones Pendientes
                    </a>
                    <a href="historial-aprobaciones.php" class="px-3 py-2 rounded-md text-white bg-blue-700 hover:bg-blue-800 transition-colors">
                        <i class="fas fa-history mr-1"></i> Historial
                    </a>
                </div>
                <!-- User Profile Dropdown -->
                <div class="relative ml-4">
                    <button id="user-menu-button" class="flex items-center space-x-2 text-white hover:bg-gray-700 px-3 py-2 rounded-md">
                        <i class="fas fa-user-circle text-xl"></i>
                        <span class="hidden md:inline"><?= htmlspecialchars($_SESSION['usuario']['nombre'] ?? 'Usuario') ?></span>
                        <i class="fas fa-chevron-down text-xs ml-1"></i>
                    </button>
                    <div id="user-menu" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
                        <a href="perfil-cuentadante.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-user mr-2"></i>Perfil
                        </a>
                        <a href="../../logout.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-sign-out-alt mr-2"></i>Cerrar Sesión
                        </a>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <div class="container mx-auto px-4 py-8">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">
                    <i class="fas fa-history text-blue-600 mr-2"></i>
                    Historial de Aprobaciones
                </h1>
                <p class="text-gray-600">Registro de todas las solicitudes aprobadas y rechazadas por el cuentadante</p>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <?php
                // Estadísticas
                $sqlAprobadas = "SELECT COUNT(*) as total FROM prestamos p
                                JOIN autorizacion a ON p.IDautorizacion = a.IDaut
                                LEFT JOIN aprobaciones ap ON p.IDpre = ap.IDprestamo
                                WHERE a.estadoaut = 'pendiente_almacen' 
                                AND ap.tipo_aprobacion = 'cuentadante'";
                $resultAprobadas = $conn->query($sqlAprobadas);
                $totalAprobadas = $resultAprobadas->fetch_assoc()['total'];

                $sqlRechazadas = "SELECT COUNT(*) as total FROM prestamos p
                                 JOIN autorizacion a ON p.IDautorizacion = a.IDaut
                                 LEFT JOIN aprobaciones ap ON p.IDpre = ap.IDprestamo
                                 WHERE a.estadoaut = 'rechazado' 
                                 AND ap.tipo_aprobacion = 'cuentadante'";
                $resultRechazadas = $conn->query($sqlRechazadas);
                $totalRechazadas = $resultRechazadas->fetch_assoc()['total'];

                $sqlTotal = "SELECT COUNT(*) as total FROM prestamos p
                            JOIN autorizacion a ON p.IDautorizacion = a.IDaut
                            LEFT JOIN aprobaciones ap ON p.IDpre = ap.IDprestamo
                            WHERE (a.estadoaut = 'pendiente_almacen' OR a.estadoaut = 'rechazado') 
                            AND ap.tipo_aprobacion = 'cuentadante'";
                $resultTotal = $conn->query($sqlTotal);
                $totalGeneral = $resultTotal->fetch_assoc()['total'];
                ?>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-100 text-green-600">
                            <i class="fas fa-check text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Aprobadas</p>
                            <p class="text-2xl font-semibold text-gray-900"><?= $totalAprobadas ?></p>
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
                            <p class="text-2xl font-semibold text-gray-900"><?= $totalRechazadas ?></p>
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
                            <p class="text-2xl font-semibold text-gray-900"><?= $totalGeneral ?></p>
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
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Motivo</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php
                            if ($result && $result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo '<tr class="hover:bg-gray-50">';
                                    echo '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">' . $row['IDpre'] . '</td>';
                                    echo '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">' . htmlspecialchars($row['solicitante']) . '</td>';
                                    echo '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">' . 
                                        htmlspecialchars($row['elemento']) . ' (' . htmlspecialchars($row['codigo_elemento']) . ')' . 
                                        '</td>';
                                    echo '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">' . 
                                        htmlspecialchars($row['cantidad']) . ' unidad(es)' . 
                                        '</td>';
                                    
                                    // Estado
                                    $estadoClass = '';
                                    $estadoTexto = '';
                                    $estadoIcon = '';
                                    
                                    if ($row['estado_autorizacion'] === 'pendiente_almacen') {
                                        $estadoClass = 'bg-green-100 text-green-800';
                                        $estadoTexto = 'Aprobada';
                                        $estadoIcon = 'fas fa-check';
                                    } elseif ($row['estado_autorizacion'] === 'rechazado') {
                                        $estadoClass = 'bg-red-100 text-red-800';
                                        $estadoTexto = 'Rechazada';
                                        $estadoIcon = 'fas fa-times';
                                    }
                                    
                                    echo '<td class="px-6 py-4 whitespace-nowrap">';
                                    echo '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ' . $estadoClass . '">';
                                    echo '<i class="' . $estadoIcon . ' mr-1"></i>' . $estadoTexto;
                                    echo '</span>';
                                    echo '</td>';
                                    
                                    // Fecha
                                    $fecha = $row['fecha_actualizacion'] ?: $row['fecha_creacion'];
                                    echo '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">' . 
                                        date('d/m/Y H:i', strtotime($fecha)) . 
                                        '</td>';
                                    
                                    // Motivo
                                    $motivo = $row['motivo'] ?: 'Sin motivo especificado';
                                    echo '<td class="px-6 py-4 text-sm text-gray-900">' . 
                                        htmlspecialchars($motivo) . 
                                        '</td>';
                                    
                                    echo '</tr>';
                                }
                            } else {
                                echo '<tr><td colspan="7" class="px-6 py-4 text-center text-gray-500">No hay registros en el historial</td></tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Paginación -->
            <?php if ($totalPaginas > 1): ?>
            <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6 mt-4 rounded-lg shadow">
                <div class="flex-1 flex justify-between sm:hidden">
                    <?php if ($pagina > 1): ?>
                        <a href="?pagina=<?= $pagina - 1 ?>" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            Anterior
                        </a>
                    <?php endif; ?>
                    <?php if ($pagina < $totalPaginas): ?>
                        <a href="?pagina=<?= $pagina + 1 ?>" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            Siguiente
                        </a>
                    <?php endif; ?>
                </div>
                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700">
                            Mostrando <span class="font-medium"><?= $inicio + 1 ?></span> a <span class="font-medium"><?= min($inicio + $porPagina, $totalRegistros) ?></span> de <span class="font-medium"><?= $totalRegistros ?></span> resultados
                        </p>
                    </div>
                    <div>
                        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                            <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                                <a href="?pagina=<?= $i ?>" class="relative inline-flex items-center px-4 py-2 border text-sm font-medium <?= $i === $pagina ? 'z-10 bg-blue-50 border-blue-500 text-blue-600' : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50' ?>">
                                    <?= $i ?>
                                </a>
                            <?php endfor; ?>
                        </nav>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Toggle user menu
        document.getElementById('user-menu-button').addEventListener('click', function() {
            document.getElementById('user-menu').classList.toggle('hidden');
        });

        // Close user menu when clicking outside
        document.addEventListener('click', function(event) {
            const userMenu = document.getElementById('user-menu');
            const userMenuButton = document.getElementById('user-menu-button');
            
            if (!userMenu.contains(event.target) && !userMenuButton.contains(event.target)) {
                userMenu.classList.add('hidden');
            }
        });
    </script>
</body>
</html> 