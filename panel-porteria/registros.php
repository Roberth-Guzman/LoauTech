<?php
session_start();
include '../conexion.php';

// Verificar si el usuario es de portería
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'porteria') {
    header("Location: ../login.php");
    exit;
}

// Obtener todos los registros de elementos ingresados
$sql = "SELECT p.nombrecompletoper, p.numerodoc, ie.* 
        FROM ingresoelementos ie
        JOIN personas p ON ie.IDPER = p.IDper
        ORDER BY ie.IDingele DESC";
$registros = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registros de Elementos - LOAUTECH</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen">
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
                <div class="flex items-center">
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
            <div class="bg-blue-600 px-6 py-4">
                <h1 class="text-2xl font-bold text-white text-center">
                    <i class="fas fa-clipboard-list mr-2"></i> REGISTROS DE ELEMENTOS
                </h1>
            </div>

            <!-- Cuerpo -->
            <div class="p-6">
                <div class="mb-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">
                        <i class="fas fa-list-ul mr-2 text-blue-500"></i> Listado de Elementos Registrados
                    </h2>
                    
                    <!-- Tabla de registros -->
                    <div class="overflow-x-auto rounded-lg border border-gray-200">
                        <div class="overflow-y-auto max-h-[70vh]">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sticky top-0 bg-gray-100 z-10">#</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sticky top-0 bg-gray-100 z-10">Nombre Persona</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sticky top-0 bg-gray-100 z-10">Documento</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sticky top-0 bg-gray-100 z-10">Elemento</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sticky top-0 bg-gray-100 z-10">Tipo</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sticky top-0 bg-gray-100 z-10">Descripción</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sticky top-0 bg-gray-100 z-10">Observaciones</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sticky top-0 bg-gray-100 z-10">Fecha Registro</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                <?php 
                                if ($registros && $registros->num_rows > 0):
                                    $contador = 1;
                                    while($registro = $registros->fetch_assoc()): 
                                ?>
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?= $contador++ ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?= htmlspecialchars($registro['nombrecompletoper']) ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= htmlspecialchars($registro['numerodoc']) ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= htmlspecialchars($registro['nombreingele']) ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            <?= htmlspecialchars($registro['tipoelemento']) ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500"><?= htmlspecialchars($registro['descripcioningele']) ?></td>
                                    <td class="px-6 py-4 text-sm text-gray-500"><?= htmlspecialchars($registro['observacioningele']) ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <div class="text-gray-900"><?= date('d/m/Y', strtotime($registro['fecharegistro'] ?? 'now')) ?></div>
                                        <div class="text-gray-500"><?= date('H:i', strtotime($registro['fecharegistro'] ?? 'now')) ?></div>
                                    </td>
                                </tr>
                                <?php 
                                    endwhile;
                                else:
                                ?>
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <div class="text-gray-500">No hay registros de elementos ingresados</div>
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="mt-4 flex flex-col sm:flex-row justify-between items-center px-6 py-4 bg-gray-50 border-t border-gray-200 rounded-b-lg">
                    <div class="text-sm text-gray-500 mb-2 sm:mb-0">
                        Mostrando <span class="font-medium text-gray-700"><?= $registros ? $registros->num_rows : 0 ?></span> registros
                    </div>
                    <a href="panel-principal.php" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i> Volver al Inicio
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>