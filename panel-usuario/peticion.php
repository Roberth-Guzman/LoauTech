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

// Verificar que se accede con parámetros válidos del elemento
if (!isset($_GET['elemento_id']) || !isset($_GET['elemento_nombre']) || !isset($_GET['elemento_codigo'])) {
    header('Location: inventario.php?error=acceso_no_autorizado');
    exit();
}

// Obtener y validar parámetros del elemento
$elemento_id = intval($_GET['elemento_id']);
$elemento_nombre = htmlspecialchars($_GET['elemento_nombre']);
$elemento_codigo = htmlspecialchars($_GET['elemento_codigo']);
$elemento_descripcion = isset($_GET['elemento_descripcion']) ? htmlspecialchars($_GET['elemento_descripcion']) : '';

// Conexión a BD para verificar que el elemento existe y está disponible
require_once '../conexion.php';
$sql = "SELECT * FROM elementos WHERE IDele = ? AND (estado != 'en prestamo' OR estado IS NULL OR estado = '') AND estadoelemento = 'activo' AND cantidadele > 0";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $elemento_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header('Location: inventario.php?error=elemento_no_disponible');
    exit();
}

$elemento = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitud de Equipo - LOAUTECH</title>
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
                        <a href="inventario.php" class="flex items-center p-2 rounded hover:bg-gray-700">
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
                        <a href="peticion.php" class="flex items-center p-2 rounded bg-gray-700">
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
                        <a href="inventario.php" class="text-blue-600 hover:text-blue-800 mr-4">
                            <i class="fas fa-arrow-left text-xl"></i>
                        </a>
                        <h1 class="text-xl font-bold text-gray-900">
                            SOLICITUD DE EQUIPO
                        </h1>
                        <span class="ml-4 text-sm text-gray-600"><?= $elemento_nombre ?></span>
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
                <!-- Mensajes de éxito y error -->
                <?php if (isset($_GET['success'])): ?>
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded" role="alert">
                        <div class="flex">
                            <div class="py-1">
                                <i class="fas fa-check-circle text-green-500 mr-2"></i>
                            </div>
                            <div>
                                <p class="font-bold">¡Éxito!</p>
                                <p class="text-sm">
                                    <?php if ($_GET['success'] === 'peticion_enviada'): ?>
                                        Tu solicitud ha sido registrada y está pendiente de aprobación.
                                    <?php endif; ?>
                                </p>
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
                                        $error = $_GET['error'];
                                        if ($error === 'elemento_no_disponible') {
                                            echo 'El elemento seleccionado no está disponible.';
                                        } elseif ($error === 'stock_insuficiente') {
                                            echo 'No hay suficiente stock disponible.';
                                        } elseif ($error === 'datos_incompletos') {
                                            echo 'Por favor, completa todos los campos obligatorios.';
                                        } else {
                                            echo htmlspecialchars(urldecode($error));
                                        }
                                    ?>
                                </p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="bg-white rounded-lg shadow-sm overflow-hidden mb-6">
                    <form action="procesar_peticion.php" method="post" class="p-6">
                        <!-- Información del Solicitante -->
                        <div class="mb-6">
                            <h3 class="text-lg font-medium text-gray-900 border-b pb-2 mb-4">Información del Solicitante</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre Completo</label>
                                    <p class="text-gray-900"><?= htmlspecialchars($_SESSION['usuario']['nombre'] ?? '') ?></p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Documento</label>
                                    <p class="text-gray-900"><?= htmlspecialchars($_SESSION['usuario']['documento'] ?? '') ?></p>
                                </div>
                                <div>
                                    <label for="formacionodependencia" class="block text-sm font-medium text-gray-700 mb-1">
                                        Formación o Dependencia <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" id="formacionodependencia" name="formacionodependencia" required
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                           placeholder="Ej: Ingeniería de Sistemas">
                                </div>
                                <div>
                                    <label for="cargopre" class="block text-sm font-medium text-gray-700 mb-1">
                                        Cargo <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" id="cargopre" name="cargopre" required
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                           placeholder="Ej: Estudiante">
                                </div>
                            </div>
                        </div>

                        <!-- Información del Elemento -->
                        <div class="mb-6">
                            <h3 class="text-lg font-medium text-gray-900 border-b pb-2 mb-4">Información del Elemento</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Elemento Solicitado</label>
                                    <p class="text-gray-900"><?= htmlspecialchars($elemento_nombre) ?></p>
                                    <input type="hidden" name="idelementos" value="<?= $elemento_id ?>">
                                    <input type="hidden" name="idpersonas" value="<?= $_SESSION['usuario']['IDper'] ?? '' ?>">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Código</label>
                                    <p class="text-gray-900"><?= htmlspecialchars($elemento_codigo) ?></p>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                                    <p class="text-gray-900"><?= $elemento_descripcion ? htmlspecialchars($elemento_descripcion) : 'No hay descripción disponible' ?></p>
                                </div>
                                <div>
                                    <label for="cantidad" class="block text-sm font-medium text-gray-700 mb-1">
                                        Cantidad <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number" id="cantidad" name="cantidad" min="1" max="<?= $elemento['cantidadele'] ?? 1 ?>" required
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                           value="1">
                                    <p class="mt-1 text-xs text-gray-500">Disponibles: <?= $elemento['cantidadele'] ?? 0 ?></p>
                                </div>
                            </div>
                        </div>

                        <!-- Detalles de la Solicitud -->
                        <div class="mb-6">
                            <h3 class="text-lg font-medium text-gray-900 border-b pb-2 mb-4">Detalles de la Solicitud</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="md:col-span-2">
                                    <label for="lugardetraslado" class="block text-sm font-medium text-gray-700 mb-1">
                                        Lugar de Uso/Traslado <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" id="lugardetraslado" name="lugardetraslado" required
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                           placeholder="Ej: Salón 302, Edificio de Ingeniería">
                                </div>
                                <div class="md:col-span-2">
                                    <label for="observaciones" class="block text-sm font-medium text-gray-700 mb-1">
                                        Observaciones
                                    </label>
                                    <input type="hidden" name="iddetalle" value="0">
                                    <textarea id="observaciones" name="observaciones" rows="3"
                                              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                              placeholder="Detalles adicionales sobre el uso del equipo..."></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Términos y Condiciones -->
                        <div class="mb-6">
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input id="terminos" name="terminos" type="checkbox" required
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="terminos" class="font-medium text-gray-700">Acepto los términos y condiciones</label>
                                    <p class="text-gray-500">Confirmo que la información proporcionada es correcta y me comprometo a devolver el equipo en las condiciones en que fue entregado.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Botones de acción -->
                        <div class="flex justify-end space-x-4">
                            <a href="inventario.php" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <i class="fas fa-times mr-2"></i> Cancelar
                            </a>
                            <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <i class="fas fa-paper-plane mr-2"></i> Enviar Solicitud
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Datos Técnicos -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-medium text-gray-900 border-b pb-2 mb-4">Datos Técnicos</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">Sistema</p>
                            <p class="text-sm font-medium text-gray-900"><?= php_uname('s') . ' ' . php_uname('r') ?></p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">PHP</p>
                            <p class="text-sm font-medium text-gray-900"><?= phpversion() ?></p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Servidor Web</p>
                            <p class="text-sm font-medium text-gray-900"><?= $_SERVER['SERVER_SOFTWARE'] ?? 'N/A' ?></p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Base de Datos</p>
                            <p class="text-sm font-medium text-gray-900">MySQL</p>
                        </div>
                    </div>
                </div>
            </main>

            <!-- Footer -->
            <footer class="bg-white border-t mt-8">
                <div class="max-w-7xl mx-auto px-4 py-4 sm:px-6 lg:px-8">
                    <p class="text-center text-sm text-gray-500">
                        &copy; <?php echo date('Y'); ?> LOAUTECH - Todos los derechos reservados
                    </p>
                </div>
            </footer>
        </div>
    </div>

    <script>
        // Validación de fechas
        document.addEventListener('DOMContentLoaded', function() {
            const fechaPrestamo = document.getElementById('fecha_prestamo');
            const fechaDevolucion = document.getElementById('fecha_devolucion');
            const hoy = new Date().toISOString().split('T')[0];
            
            // Establecer fecha mínima como hoy
            fechaPrestamo.min = hoy;
            
            // Actualizar fecha mínima de devolución cuando cambia la fecha de préstamo
            fechaPrestamo.addEventListener('change', function() {
                fechaDevolucion.min = this.value;
                if (fechaDevolucion.value && fechaDevolucion.value < this.value) {
                    fechaDevolucion.value = this.value;
                }
            });
            
            // Validar que la fecha de devolución sea posterior a la de préstamo
            fechaDevolucion.addEventListener('change', function() {
                if (this.value < fechaPrestamo.value) {
                    this.value = fechaPrestamo.value;
                }
            });
        });
    </script>
</body>
</html>