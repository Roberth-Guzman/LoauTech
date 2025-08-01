<?php
session_start();
include '../conexion.php';

// Verificar si el usuario es de portería
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'porteria') {
    header("Location: ../login.php");
    exit;
}

// --- PAGINACIÓN ---
$porPagina = 1; // Solo una solicitud por página
$pagina = isset($_GET['pagina']) ? max(1, intval($_GET['pagina'])) : 1;
$inicio = ($pagina - 1) * $porPagina;

// Contar total de solicitudes
$sqlTotal = "SELECT COUNT(*) as total
    FROM prestamos p
    JOIN personas per ON p.IDpersonas = per.IDper
    JOIN elementos e ON p.IDelementos = e.IDele
    JOIN autorizacion a ON p.IDautorizacion = a.IDaut
    WHERE a.estadoaut = 'aprobado'";
$totalResult = $conn->query($sqlTotal);
$totalSolicitudes = $totalResult->fetch_assoc()['total'];
$totalPaginas = ceil($totalSolicitudes / $porPagina);

// Consulta paginada de solicitudes aprobadas por almacenes
$sql = "SELECT 
            p.IDpre as id_prestamo,
            p.cantidad,
            p.formacionodependencia,
            p.cargopre,
            p.lugardetraslado,
            p.fecha_solicitud,
            per.nombrecompletoper as solicitante,
            per.numerodoc as documento,
            e.nombreele as elemento,
            e.codigoele as codigo_elemento,
            a.estadoaut as estado_autorizacion
        FROM prestamos p
        JOIN personas per ON p.IDpersonas = per.IDper
        JOIN elementos e ON p.IDelementos = e.IDele
        JOIN autorizacion a ON p.IDautorizacion = a.IDaut
        WHERE a.estadoaut = 'aprobado'
        ORDER BY p.IDpre DESC
        LIMIT $inicio, $porPagina";
$result = $conn->query($sql);

// Consulta de salidas del día (marcaciones)
$hoy = date('Y-m-d');
$sqlSalidas = "SELECT 
    m.hfecsalidamarc,
    per.nombrecompletoper as solicitante,
    e.nombreele as elemento,
    e.codigoele as codigo_elemento,
    v.nomvigilantesalida as vigilante,
    m.estadomarc
FROM marcaciones m
JOIN prestamos p ON m.IDpres = p.IDpre
JOIN personas per ON p.IDpersonas = per.IDper
JOIN elementos e ON p.IDelementos = e.IDele
LEFT JOIN vigilantes v ON m.IDautori = v.idautorizacion
WHERE DATE(m.hfecsalidamarc) = '$hoy'
ORDER BY m.hfecsalidamarc DESC";
$resSalidas = $conn->query($sqlSalidas);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Control de Portería - LOAUTECH</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Barra de navegación -->
    <nav class="bg-gray-800 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="panel-principal.php" class="flex-shrink-0 flex items-center">
                        <i class="fas fa-arrow-left text-xl mr-2"></i>
                        <span class="text-xl font-bold">LOAUTECH</span>
                    </a>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="relative">
                        <i class="fas fa-bell text-xl"></i>
                        <span class="absolute -top-2 -right-2 bg-green-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs">
                            <?php echo ($result && $result->num_rows > 0) ? $result->num_rows : 0; ?>
                        </span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-user-circle"></i>
                        <span><?= htmlspecialchars($_SESSION['usuario']['nombre'] ?? 'Usuario') ?></span>
                    </div>
                    <a href="../logout.php" class="text-gray-300 hover:bg-gray-700 hover:text-white px-3 py-2 rounded-md text-sm font-medium">
                        <i class="fas fa-sign-out-alt mr-1"></i> Cerrar Sesión
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Contenido principal -->
    <div class="max-w-7xl mx-auto px-4 py-6 sm:px-6 lg:px-8">
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <!-- Encabezado -->
            <div class="bg-gradient-to-r from-blue-600 to-blue-800 px-6 py-4">
                <h1 class="text-2xl font-bold text-white">
                    <i class="fas fa-clipboard-check mr-2"></i> AUTORIZACIÓN DE SALIDAS
                </h1>
            </div>

            <!-- Alerta -->
            <div class="bg-green-50 border-l-4 border-green-500 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle text-green-500 text-xl"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-green-800">Elementos Listos para Salida</h3>
                        <div class="mt-1 text-sm text-green-700">
                            <p>
                                <?php
                                if ($result && $result->num_rows > 0) {
                                    echo "Hay {$totalSolicitudes} elemento(s) confirmado(s) por inventario listo(s) para autorizar su salida.";
                                } else {
                                    echo "No hay elementos listos para salida en este momento.";
                                }
                                ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contenido Principal -->
            <div class="p-6">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Tarjeta de Solicitud -->
                    <div class="lg:col-span-2 space-y-6">
                        <?php if ($result && $result->num_rows > 0): ?>
                            <?php while($solicitud = $result->fetch_assoc()): ?>
                                <div class="bg-white border border-green-200 rounded-lg shadow-sm overflow-hidden tarjeta-animada opacity-0 translate-y-4">
                                    <!-- Encabezado de la tarjeta -->
                                    <div class="bg-green-600 px-4 py-3">
                                        <h3 class="text-lg font-semibold text-white flex items-center">
                                            <i class="fas fa-box-open mr-2"></i>
                                            SOLICITUD #<?php echo $solicitud['id_prestamo']; ?> - LISTA PARA SALIDA
                                        </h3>
                                    </div>
                                    <!-- Cuerpo de la tarjeta -->
                                    <div class="p-4">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                            <div class="space-y-2">
                                                <p class="text-sm">
                                                    <span class="font-medium text-gray-700">Solicitante:</span>
                                                    <span class="text-gray-900"><?php echo htmlspecialchars($solicitud['solicitante']); ?></span>
                                                </p>
                                                <p class="text-sm">
                                                    <span class="font-medium text-gray-700">Elemento:</span>
                                                    <span class="text-gray-900"><?php echo htmlspecialchars($solicitud['elemento']); ?></span>
                                                </p>
                                                <p class="text-sm">
                                                    <span class="font-medium text-gray-700">Código:</span>
                                                    <span class="font-mono bg-gray-100 px-2 py-0.5 rounded"><?php echo htmlspecialchars($solicitud['codigo_elemento']); ?></span>
                                                </p>
                                                <p class="text-sm">
                                                    <span class="font-medium text-gray-700">Cantidad:</span>
                                                    <span class="text-gray-900"><?php echo $solicitud['cantidad']; ?></span>
                                                </p>
                                                <p class="text-sm">
                                                    <span class="font-medium text-gray-700">Solicitado el:</span>
                                                    <span class="text-gray-900">
                                                        <?php echo isset($solicitud['fecha_solicitud']) ? date('d/m/Y h:i A', strtotime($solicitud['fecha_solicitud'])) : 'Sin registrar'; ?>
                                                    </span>
                                                </p>
                                            </div>
                                            <div class="space-y-2">
                                                <p class="text-sm">
                                                    <span class="font-medium text-gray-700">Aprobada por:</span>
                                                    <span class="text-gray-900">Inventario</span>
                                                </p>
                                                <p class="text-sm">
                                                    <span class="font-medium text-gray-700">Estado:</span>
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        <i class="fas fa-check-circle mr-1"></i> Lista para salida
                                                    </span>
                                                </p>
                                                <p class="text-sm">
                                                    <span class="font-medium text-gray-700">Lugar de traslado:</span>
                                                    <span class="text-gray-900"><?php echo htmlspecialchars($solicitud['lugardetraslado']); ?></span>
                                                </p>
                                            </div>
                                        </div>
                                        <hr class="my-4 border-gray-200">
                                        <!-- Formulario de autorización -->
                                        <form method="POST" action="registrar_salida.php" class="space-y-3">
                                            <input type="hidden" name="id_prestamo" value="<?php echo $solicitud['id_prestamo']; ?>">
                                            <div>
                                                <label for="vigilante_<?php echo $solicitud['id_prestamo']; ?>" class="block text-sm font-medium text-gray-700 mb-1">
                                                    Vigilante Responsable:
                                                </label>
                                                <input type="text" name="nombre_vigilante" id="vigilante_<?php echo $solicitud['id_prestamo']; ?>" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="Nombre del vigilante" required>
                                            </div>
                                            <div>
                                                <label for="horaSalida_<?php echo $solicitud['id_prestamo']; ?>" class="block text-sm font-medium text-gray-700 mb-1">
                                                    Hora de Salida:
                                                </label>
                                                <input type="datetime-local" name="hora_salida" id="horaSalida_<?php echo $solicitud['id_prestamo']; ?>" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" required>
                                            </div>
                                            <div>
                                                <label for="observaciones_<?php echo $solicitud['id_prestamo']; ?>" class="block text-sm font-medium text-gray-700 mb-1">
                                                    Observaciones de Salida:
                                                </label>
                                                <textarea name="observaciones" id="observaciones_<?php echo $solicitud['id_prestamo']; ?>" rows="2" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"></textarea>
                                            </div>
                                            <div class="mt-6 flex flex-col sm:flex-row justify-end space-y-2 sm:space-y-0 sm:space-x-3">
                                                <button type="submit"
                                                    class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                                    <i class="fas fa-check-circle mr-2"></i> Autorizar Salida
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            <?php endwhile; ?>

                            <!-- PAGINACIÓN -->
                            <?php if ($totalPaginas > 1): ?>
                                <div class="flex justify-center mt-6">
                                    <nav class="inline-flex rounded-md shadow-sm" aria-label="Pagination">
                                        <?php if ($pagina > 1): ?>
                                            <a href="?pagina=<?php echo $pagina - 1; ?>" class="px-3 py-1 border border-gray-300 bg-white text-gray-500 hover:bg-gray-50 rounded-l-md">
                                                <i class="fas fa-chevron-left"></i> Anterior
                                            </a>
                                        <?php endif; ?>
                                        <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                                            <a href="?pagina=<?php echo $i; ?>" class="px-3 py-1 border border-gray-300 <?php echo $i == $pagina ? 'bg-blue-500 text-white' : 'bg-white text-gray-700'; ?>">
                                                <?php echo $i; ?>
                                            </a>
                                        <?php endfor; ?>
                                        <?php if ($pagina < $totalPaginas): ?>
                                            <a href="?pagina=<?php echo $pagina + 1; ?>" class="px-3 py-1 border border-gray-300 bg-white text-gray-500 hover:bg-gray-50 rounded-r-md">
                                                Siguiente <i class="fas fa-chevron-right"></i>
                                            </a>
                                        <?php endif; ?>
                                    </nav>
                                </div>
                            <?php endif; ?>

                        <?php else: ?>
                            <div class="bg-yellow-100 border-l-4 border-yellow-500 p-4 rounded">
                                <p class="text-yellow-800">No hay solicitudes listas para salida en este momento.</p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Panel de Estadísticas -->
                    <div>
                        <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden">
                            <!-- Encabezado del panel -->
                            <div class="px-4 py-3 bg-gray-50 border-b border-gray-200">
                                <h3 class="text-base font-medium text-gray-900 flex items-center">
                                    <i class="fas fa-chart-bar text-blue-500 mr-2"></i> Estadísticas del Día
                                </h3>
                            </div>
                            
                            <!-- Contenido del panel -->
                            <div class="p-4 space-y-6">
                                <!-- Tarjeta de estadística -->
                                <div class="p-3 bg-blue-50 rounded-lg">
                                    <p class="text-xs font-medium text-blue-700 uppercase tracking-wider">
                                        Elementos en préstamo hoy
                                    </p>
                                    <div class="mt-1 flex items-baseline justify-between md:block lg:flex">
                                        <p class="text-2xl font-semibold text-blue-600">3</p>
                                        <div class="inline-flex items-baseline text-xs font-semibold text-green-600">
                                            <i class="fas fa-arrow-up text-xs mr-1"></i>
                                            <span>12% más que ayer</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tarjeta de estadística -->
                                <div class="p-3 bg-yellow-50 rounded-lg">
                                    <p class="text-xs font-medium text-yellow-700 uppercase tracking-wider">
                                        Pendientes de retorno
                                    </p>
                                    <div class="mt-1 flex items-baseline justify-between md:block lg:flex">
                                        <p class="text-2xl font-semibold text-yellow-600">1</p>
                                        <div class="inline-flex items-baseline text-xs font-semibold text-yellow-600">
                                            <i class="fas fa-exclamation-circle text-xs mr-1"></i>
                                            <span>Por verificar</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tarjeta de estadística -->
                                <div class="p-3 bg-green-50 rounded-lg">
                                    <p class="text-xs font-medium text-green-700 uppercase tracking-wider">
                                        Retornos en tiempo
                                    </p>
                                    <div class="mt-1 flex items-baseline justify-between md:block lg:flex">
                                        <p class="text-2xl font-semibold text-green-600">95%</p>
                                        <div class="inline-flex items-baseline text-xs font-semibold text-green-600">
                                            <i class="fas fa-check-circle text-xs mr-1"></i>
                                            <span>Excelente</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Botón de acción -->
                                <button type="button" class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <i class="fas fa-history mr-2 text-gray-500"></i>
                                    Ver Historial Completo
                                </button>
                            </div>
                        </div>
                </div>

                <!-- Registro de Salidas Recientes -->
                <div class="mt-8">
                    <div class="flex flex-col">
                        <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                            <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                                <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                                    <div class="bg-white px-4 py-5 border-b border-gray-200 sm:px-6">
                                        <h3 class="text-lg leading-6 font-medium text-gray-900 flex items-center">
                                            <i class="fas fa-clipboard-list text-blue-500 mr-2"></i>
                                            Registro de Salidas del Día
                                            <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                Hoy
                                            </span>
                                        </h3>
                                    </div>
                                    <div class="overflow-x-auto">
                                        <table class="min-w-full divide-y divide-gray-200">
                                            <thead class="bg-gray-50">
                                                <tr>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hora</th>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Solicitante</th>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Elemento</th>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vigilante</th>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white divide-y divide-gray-200">
                                                <?php if ($resSalidas && $resSalidas->num_rows > 0): ?>
                                                    <?php while($salida = $resSalidas->fetch_assoc()): ?>
                                                        <tr class="hover:bg-gray-50">
                                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                                <?php echo date('h:i A', strtotime($salida['hfecsalidamarc'])); ?>
                                                            </td>
                                                            <td class="px-6 py-4 whitespace-nowrap">
                                                                <?php echo htmlspecialchars($salida['solicitante']); ?>
                                                            </td>
                                                            <td class="px-6 py-4 whitespace-nowrap">
                                                                <?php echo htmlspecialchars($salida['elemento']); ?>
                                                                <div class="text-xs text-gray-500">Código: <?php echo htmlspecialchars($salida['codigo_elemento']); ?></div>
                                                            </td>
                                                            <td class="px-6 py-4 whitespace-nowrap">
                                                                <?php echo htmlspecialchars($salida['vigilante']); ?>
                                                            </td>
                                                            <td class="px-6 py-4 whitespace-nowrap">
                                                                <?php if ($salida['estadomarc'] === 'activo'): ?>
                                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                                        <i class="fas fa-check-circle mr-1"></i> Salida autorizada
                                                                    </span>
                                                                <?php else: ?>
                                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                                        <i class="fas fa-clock mr-1"></i> Pendiente retorno
                                                                    </span>
                                                                <?php endif; ?>
                                                            </td>
                                                        </tr>
                                                    <?php endwhile; ?>
                                                <?php else: ?>
                                                    <tr>
                                                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">No hay salidas registradas hoy.</td>
                                                    </tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Confirmación -->
    <div class="fixed z-10 inset-0 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true" id="exitModal">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeModal('exitModal')"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full modal-animado scale-95 opacity-0">
                <!-- Encabezado del Modal -->
                <div class="bg-green-600 px-4 py-3 sm:px-6 sm:flex sm:items-center sm:justify-between rounded-t-lg">
                    <h3 class="text-lg leading-6 font-medium text-white flex items-center">
                        <i class="fas fa-check-circle mr-2"></i> Confirmar Autorización de Salida
                    </h3>
                    <button type="button" onclick="closeModal('exitModal')" class="text-white hover:text-gray-200 focus:outline-none">
                        <span class="sr-only">Cerrar</span>
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <!-- Cuerpo del Modal -->
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                            <i class="fas fa-exclamation text-green-600"></i>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                Confirmar autorización
                            </h3>
                            <div class="mt-2">
                                <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-4">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-info-circle text-blue-400"></i>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm text-blue-700">
                                                ¿Está seguro de autorizar la salida del elemento <strong>Laptop Dell XPS</strong> para <strong>Juan Pérez</strong>?
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="space-y-4">
                                    <div>
                                        <label for="finalObservations" class="block text-sm font-medium text-gray-700 mb-1">
                                            Observaciones Finales:
                                        </label>
                                        <textarea id="finalObservations" rows="3" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="Ingrese observaciones relevantes..."></textarea>
                                    </div>
                                    
                                    <div class="flex items-start">
                                        <div class="flex items-center h-5">
                                            <input id="notifyExit" name="notifyExit" type="checkbox" class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded" checked>
                                        </div>
                                        <div class="ml-3 text-sm">
                                            <label for="notifyExit" class="font-medium text-gray-700">Notificar a todos los involucrados</label>
                                            <p class="text-gray-500">Se enviará una notificación por correo electrónico al solicitante y al personal de inventario.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Pie del Modal -->
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse rounded-b-lg">
                    <button type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
                        <i class="fas fa-check-circle mr-2"></i> Confirmar Autorización
                    </button>
                    <button type="button" onclick="closeModal('exitModal')" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        <i class="fas fa-times mr-2"></i> Cancelar
                    </button>
                </div>
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Retener -->
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden" id="holdModal">
        <div class="bg-white rounded-lg w-full max-w-md">
            <div class="bg-yellow-500 text-white p-4 rounded-t-lg flex justify-between items-center">
                <h3 class="font-bold flex items-center">
                    <i class="fas fa-pause mr-2"></i> Retener Elemento
                </h3>
                <button onclick="closeModal('holdModal')">&times;</button>
            </div>
            <div class="p-4">
                <div class="mb-4">
                    <label class="block mb-2">Motivo de Retención:</label>
                    <select class="w-full border rounded p-2">
                        <option value="">Seleccione un motivo...</option>
                        <option value="documentation_missing">Documentación faltante</option>
                        <option value="identification_required">Identificación requerida</option>
                        <option value="element_issue">Problema con el elemento</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block mb-2">Detalles de la Retención:</label>
                    <textarea class="w-full border rounded p-2" rows="3" placeholder="Describa los detalles..."></textarea>
                </div>
                <div class="mb-4">
                    <label class="block mb-2">Fecha de Resolución Esperada:</label>
                    <input type="datetime-local" class="w-full border rounded p-2">
                </div>
            </div>
            <div class="p-4 border-t flex justify-end space-x-2">
                <button class="bg-gray-500 text-white px-4 py-2 rounded" onclick="closeModal('holdModal')">Cancelar</button>
                <button class="bg-yellow-500 text-white px-4 py-2 rounded flex items-center">
                    <i class="fas fa-pause mr-2"></i> Retener
                </button>
            </div>
        </div>
    </div>

    <script>
        // Animación de entrada para la tarjeta de solicitud
        document.addEventListener('DOMContentLoaded', function() {
            const tarjetas = document.querySelectorAll('.tarjeta-animada');
            tarjetas.forEach(function(tarjeta, i) {
                setTimeout(() => {
                    tarjeta.classList.remove('opacity-0', 'translate-y-4');
                    tarjeta.classList.add('opacity-100', 'translate-y-0');
                }, 100 * i);
            });
        });

        // Funciones para manejar modales con animación
        function openModal(id) {
            const modal = document.getElementById(id);
            if (!modal) return;
            modal.classList.remove('hidden');
            const content = modal.querySelector('.modal-animado');
            if (content) {
                setTimeout(() => {
                    content.classList.remove('scale-95', 'opacity-0');
                    content.classList.add('scale-100', 'opacity-100');
                }, 10);
            }
        }

        function closeModal(id) {
            const modal = document.getElementById(id);
            if (!modal) return;
            const content = modal.querySelector('.modal-animado');
            if (content) {
                content.classList.remove('scale-100', 'opacity-100');
                content.classList.add('scale-95', 'opacity-0');
                setTimeout(() => {
                    modal.classList.add('hidden');
                }, 250);
            } else {
                modal.classList.add('hidden');
            }
        }

        // Para los botones que abren modales
        document.querySelectorAll('[onclick*="openModal"]').forEach(btn => {
            btn.addEventListener('click', function() {
                const modalId = this.getAttribute('onclick').match(/'([^']+)'/)[1];
                openModal(modalId);
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.btn-autorizar-salida').forEach(btn => {
                btn.addEventListener('click', function() {
                    const idPrestamo = this.dataset.id;
                    const observaciones = document.getElementById('observaciones_' + idPrestamo).value;
                    const vigilante = document.getElementById('vigilante_' + idPrestamo).value;
                    const horaSalida = document.getElementById('horaSalida_' + idPrestamo).value;

                    if (!vigilante || !horaSalida) {
                        alert('Por favor, complete el nombre del vigilante y la hora de salida.');
                        return;
                    }

                    fetch('registrar_salida.php', {
                        method: 'POST',
                        headers: {'Content-Type': 'application/json'},
                        body: JSON.stringify({
                            idPrestamo,
                            observaciones,
                            vigilante,
                            horaSalida
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            alert('Salida registrada correctamente');
                            location.reload();
                        } else {
                            alert('Error: ' + data.message);
                        }
                    });
                });
            });
        });
    </script>
    <style>
    /* Animación de entrada para tarjetas */
    .tarjeta-animada {
        transition: all 0.4s cubic-bezier(.4,0,.2,1);
        opacity: 0;
        transform: translateY(1rem);
    }
    .tarjeta-animada.opacity-100 {
        opacity: 1;
    }
    .tarjeta-animada.translate-y-0 {
        transform: translateY(0);
    }
    /* Animación para el contenido del modal */
    .modal-animado {
        transition: all 0.3s cubic-bezier(.4,0,.2,1);
        transform: scale(0.95);
        opacity: 0;
    }
    .modal-animado.scale-100 {
        transform: scale(1);
    }
    .modal-animado.opacity-100 {
        opacity: 1;
    }
    </style>
</body>
</html>