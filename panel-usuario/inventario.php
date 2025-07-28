<?php
session_start();

// Verificación robusta de sesión y rol
if (!isset($_SESSION['usuario'])) {
    header('Location: ../login.php');
    exit();
}

// Lista blanca de roles permitidos
$rolesPermitidos = ['usuario'];
if (!in_array($_SESSION['usuario']['rol'], $rolesPermitidos)) {
    header('Location: ../login.php?error=acceso_no_autorizado');
    exit();
}

// Conexión a BD
require_once '../conexion.php';

// Consultar elementos de la base de datos (elementos disponibles: no en préstamo y activos)
$sql = "SELECT IDele, nombreele, cantidadele, cantidadest, codigoele, descripcionele, caracteristicasele, estado, estadoelemento FROM elementos WHERE (estado != 'en prestamo' OR estado IS NULL OR estado = '') AND estadoelemento = 'activo' ORDER BY IDele";
$result = $conn->query($sql);
$elementos = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $elementos[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventario - LOAUTECH</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div class="w-64 bg-gray-800 text-white fixed top-0 left-0 bottom-0 z-10">
            <div class="p-4 border-b border-gray-700">
                <h1 class="text-xl font-bold">LOAUTECH</h1>
                <p class="text-sm text-gray-400">Panel de Usuario</p>
            </div>
            
            <nav class="p-4">
                <ul class="space-y-2">
                    <li>
                        <a href="panel-principal.php" class="flex items-center p-2 rounded hover:bg-gray-700">
                            <i class="fas fa-tachometer-alt mr-3"></i>
                            Inicio
                        </a>
                    </li>
                    <li>
                        <a href="inventario.php" class="flex items-center p-2 rounded hover:bg-gray-700 bg-gray-700">
                            <i class="fas fa-boxes mr-3"></i>
                            Inventario
                        </a>
                    </li>
                    <li>
                        <a href="registroelemento.php" class="flex items-center p-2 rounded hover:bg-gray-700">
                            <i class="fas fa-plus-circle mr-3"></i>
                            Registrar Elemento
                        </a>
                    </li>
                    <li>
                        <a href="peticion.php" class="flex items-center p-2 rounded hover:bg-gray-700">
                            <i class="fas fa-paper-plane mr-3"></i>
                            Mis Peticiones
                        </a>
                    </li>
                    <li>
                        <a href="perfil.php" class="flex items-center p-2 rounded hover:bg-gray-700">
                            <i class="fas fa-user mr-3"></i>
                            Mi Perfil
                        </a>
                    </li>
                    <li>
                        <a href="../logout.php" class="flex items-center p-2 rounded hover:bg-gray-700 text-red-400 hover:text-red-300">
                            <i class="fas fa-sign-out-alt mr-3"></i>
                            Cerrar Sesión
                        </a>
                    </li>
                </ul>
            </nav>
        </div>

        <!-- Contenido principal -->
        <div class="flex-1 ml-64 overflow-auto">
            <!-- Header -->
            <div class="bg-white shadow-sm">
                <div class="max-w-7xl mx-auto px-4 py-4 sm:px-6 lg:px-8 flex justify-between items-center">
                    <div class="flex items-center">
                        <h1 class="text-xl font-bold text-gray-900">
                            Inventario de Elementos
                        </h1>
                    </div>
                    <div class="flex items-center space-x-4">
                        <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                            <?php echo ucfirst(htmlspecialchars($_SESSION['usuario']['rol'] ?? 'usuario')); ?>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Contenido -->
            <main class="p-6">
                <?php if (isset($_GET['success']) && $_GET['success'] === 'peticion_enviada'): ?>
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded" role="alert">
                        <div class="flex">
                            <div class="py-1">
                                <i class="fas fa-check-circle text-green-500 mr-2"></i>
                            </div>
                            <div>
                                <p class="font-bold">¡Petición enviada exitosamente!</p>
                                <p class="text-sm">Tu solicitud ha sido registrada y está pendiente de autorización.</p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($_GET['error'])): ?>
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded" role="alert">
                        <div class="flex">
                            <div class="py-1">
                                <i class="fas fa-exclamation-triangle text-red-500 mr-2"></i>
                            </div>
                            <div>
                                <p class="font-bold">Error</p>
                                <p class="text-sm">
                                    <?php 
                                    switch($_GET['error']) {
                                        case 'elemento_no_disponible':
                                            echo 'El elemento seleccionado no está disponible o no existe.';
                                            break;
                                        case 'acceso_no_autorizado':
                                            echo 'Acceso no autorizado. Debes seleccionar un elemento del inventario.';
                                            break;
                                        default:
                                            echo htmlspecialchars($_GET['error']);
                                    }
                                    ?>
                                </p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (empty($elementos)): ?>
                    <div class="text-center py-12 bg-white rounded-lg shadow p-6">
                        <i class="fas fa-box-open text-5xl text-gray-400 mb-4"></i>
                        <h3 class="text-lg font-medium text-gray-900">No hay elementos en el inventario</h3>
                        <p class="mt-1 text-sm text-gray-500">Contacta al administrador para agregar elementos.</p>
                    </div>
                <?php else: ?>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                        <?php foreach ($elementos as $elemento): ?>
                            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300 cursor-pointer"
                                 onclick="mostrarDetalleElemento(
                                     '<?php echo $elemento['IDele']; ?>',
                                     '<?php echo addslashes($elemento['nombreele']); ?>',
                                     '<?php echo addslashes($elemento['codigoele']); ?>',
                                     '<?php echo addslashes($elemento['descripcionele']); ?>',
                                     '<?php echo addslashes($elemento['caracteristicasele']); ?>',
                                     '<?php echo $elemento['cantidadele']; ?>',
                                     '<?php echo $elemento['estadoelemento']; ?>',
                                     '<?php echo $elemento['estado']; ?>'
                                 )">
                                <div class="p-4">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h3 class="text-lg font-semibold text-gray-900"><?php echo htmlspecialchars($elemento['nombreele']); ?></h3>
                                            <p class="text-sm text-gray-600">Código: <?php echo htmlspecialchars($elemento['codigoele']); ?></p>
                                        </div>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            <?php echo $elemento['cantidadele']; ?> disponibles
                                        </span>
                                    </div>
                                    <div class="mt-2">
                                        <p class="text-sm text-gray-600 line-clamp-2">
                                            <?php echo htmlspecialchars(substr($elemento['descripcionele'], 0, 100)); ?><?php echo strlen($elemento['descripcionele']) > 100 ? '...' : ''; ?>
                                        </p>
                                    </div>
                                </div>
                                <div class="bg-gray-50 px-4 py-3 border-t border-gray-100">
                                    <div class="flex justify-between items-center">
                                        <span class="text-xs font-medium <?php echo $elemento['estado'] === 'disponible' ? 'text-green-800 bg-green-100' : 'text-yellow-800 bg-yellow-100'; ?> px-2.5 py-0.5 rounded-full">
                                            <?php echo ucfirst(htmlspecialchars($elemento['estado'] ?? 'Disponible')); ?>
                                        </span>
                                        <button class="text-blue-600 hover:text-blue-800 text-sm font-medium" 
                                                onclick="event.stopPropagation();">
                                            Ver detalles
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </main>
        </div>
    </div>

    <!-- Modal para mostrar información completa del elemento -->
    <div id="elementoModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 id="modalTitulo" class="text-xl font-bold text-gray-900"></h3>
                        <p id="modalCodigo" class="text-sm text-gray-500"></p>
                    </div>
                    <button onclick="cerrarModal()" class="text-gray-400 hover:text-gray-500">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <div class="mt-6 space-y-4">
                    <div>
                        <h4 class="text-sm font-medium text-gray-500">Descripción</h4>
                        <p id="modalDescripcion" class="mt-1 text-gray-900"></p>
                    </div>
                    
                    <div>
                        <h4 class="text-sm font-medium text-gray-500">Características</h4>
                        <p id="modalCaracteristicas" class="mt-1 text-gray-900"></p>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Cantidad Disponible</h4>
                            <p id="modalCantidad" class="mt-1 text-gray-900"></p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Estado</h4>
                            <p id="modalEstado" class="mt-1">
                                <span id="estadoBadge" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"></span>
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="mt-6 pt-6 border-t border-gray-200 flex justify-end space-x-3">
                    <button type="button" onclick="cerrarModal()" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Cerrar
                    </button>
                    <a id="btnSolicitar" href="#" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Solicitar Elemento
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
    <script>
        // Variables globales para almacenar los datos del elemento
        let elementoActual = null;

        // Función para mostrar el modal con los detalles del elemento
        function mostrarDetalleElemento(id, nombre, codigo, descripcion, caracteristicas, cantidad, estado, estadoPrestamo) {
            // Guardar los datos del elemento
            elementoActual = {
                id: id,
                nombre: nombre,
                codigo: codigo,
                descripcion: descripcion || ''
            };

            // Actualizar la UI
            document.getElementById('modalTitulo').textContent = nombre;
            document.getElementById('modalCodigo').textContent = 'Código: ' + codigo;
            document.getElementById('modalDescripcion').textContent = descripcion || 'No hay descripción disponible';
            document.getElementById('modalCaracteristicas').textContent = caracteristicas || 'No hay características disponibles';
            document.getElementById('modalCantidad').textContent = cantidad;
            
            // Configurar el botón de solicitud
            const btnSolicitar = document.getElementById('btnSolicitar');
            btnSolicitar.href = `peticion.php?elemento_id=${id}&elemento_nombre=${encodeURIComponent(nombre)}&elemento_codigo=${encodeURIComponent(codigo)}`;
            
            const estadoBadge = document.getElementById('estadoBadge');
            estadoBadge.textContent = estadoPrestamo ? estadoPrestamo.charAt(0).toUpperCase() + estadoPrestamo.slice(1) : 'Disponible';
            
            // Mostrar/ocultar el botón según disponibilidad
            if (estadoPrestamo === 'disponible' || !estadoPrestamo) {
                estadoBadge.className = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800';
            } else {
                estadoBadge.className = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800';
            }
            
            // Mostrar el modal
            document.getElementById('elementoModal').classList.remove('hidden');
        }
        
        // Función para cerrar el modal
        function cerrarModal() {
            document.getElementById('elementoModal').classList.add('hidden');
        }
        
        // Cerrar el modal al hacer clic fuera del contenido
        document.getElementById('elementoModal').addEventListener('click', function(e) {
            if (e.target === this) {
                cerrarModal();
            }
        });
        
        // Cerrar el modal con la tecla Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                cerrarModal();
            }
        });
    </script>
</body>
</html>