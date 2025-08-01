<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] != 'admin') {
    header('Location: ../login.php');
    exit();
}

// Incluir el archivo de conexión desde la raíz del proyecto
require_once __DIR__ . '/../../conexion.php';

// Verificar si la conexión se estableció correctamente
if (!isset($conn) || $conn->connect_error) {
    die("Error de conexión a la base de datos");
}

// Obtener datos del usuario para la sesión
$usuario_actual = $_SESSION['usuario']['documento'];
$sql = "SELECT p.nombrecompletoper, r.rol 
        FROM personas p
        JOIN roles r ON p.IDper = r.idper
        WHERE p.numerodoc = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_actual);
$stmt->execute();
$resultado = $stmt->get_result();
$datos_usuario = $resultado->fetch_assoc();
$stmt->close();

// Configurar datos para las vistas
$_SESSION['user'] = [
    'nombre' => $datos_usuario['nombrecompletoper'] ?? 'Administrador',
    'rol' => $datos_usuario['rol'] ?? 'admin'
];

// Verificar ID de autorización
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: listar_autorizaciones.php');
    exit();
}

$id = intval($_GET['id']);
$query = "SELECT * FROM autorizacion WHERE IDaut = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header('Location: listar_autorizaciones.php');
    exit();
}

$autorizacion = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles de Autorización - LOAUTECH</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <?php include('../includes/sidebar.php'); ?>
        
        <div class="flex-1 ml-64 overflow-auto">
            <!-- Header -->
            <?php include('../includes/header.php'); ?>

            <!-- Contenido principal -->
            <main class="p-6">
                <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                    <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                        <div class="flex justify-between items-center">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">
                                Detalles de la Autorización #<?= $id ?>
                            </h3>
                            <div class="space-x-2">
                                <a href="editar_autorizacion.php?id=<?= $id ?>" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                                    <i class="fas fa-edit mr-2"></i> Editar
                                </a>
                                <a href="listar_autorizaciones.php" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-gray-600 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                                    <i class="fas fa-arrow-left mr-2"></i> Volver al listado
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="px-4 py-5 sm:p-6">
                        <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-2">
                            <!-- Información Básica -->
                            <div class="bg-white overflow-hidden shadow rounded-lg">
                                <div class="px-4 py-5 sm:px-6 bg-gray-50">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                                        Información Básica
                                    </h3>
                                </div>
                                <div class="border-t border-gray-200 px-4 py-5 sm:p-6">
                                    <dl class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-1">
                                        <div class="sm:col-span-1">
                                            <dt class="text-sm font-medium text-gray-500">ID de Autorización</dt>
                                            <dd class="mt-1 text-sm text-gray-900"><?= htmlspecialchars($autorizacion['IDaut']) ?></dd>
                                        </div>
                                        <div class="sm:col-span-1">
                                            <dt class="text-sm font-medium text-gray-500">V°B° Cuentadante</dt>
                                            <dd class="mt-1 text-sm text-gray-900"><?= htmlspecialchars($autorizacion['VoBoCuentadanteaut']) ?></dd>
                                        </div>
                                        <div class="sm:col-span-1">
                                            <dt class="text-sm font-medium text-gray-500">Estado</dt>
                                            <dd class="mt-1">
                                                <?php
                                                $estadoClases = [
                                                    'pendiente' => 'bg-yellow-100 text-yellow-800',
                                                    'aprobado' => 'bg-green-100 text-green-800',
                                                    'rechazado' => 'bg-red-100 text-red-800'
                                                ];
                                                $estadoTexto = ucfirst($autorizacion['estadoaut']);
                                                $clase = $estadoClases[$autorizacion['estadoaut']] ?? 'bg-gray-100 text-gray-800';
                                                ?>
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $clase ?>">
                                                    <?= $estadoTexto ?>
                                                </span>
                                            </dd>
                                        </div>
                                    </dl>
                                </div>
                            </div>

                            <!-- Información del Autorizador -->
                            <div class="bg-white overflow-hidden shadow rounded-lg">
                                <div class="px-4 py-5 sm:px-6 bg-gray-50">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                                        Información del Autorizador
                                    </h3>
                                </div>
                                <div class="border-t border-gray-200 px-4 py-5 sm:p-6">
                                    <dl class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-1">
                                        <div class="sm:col-span-1">
                                            <dt class="text-sm font-medium text-gray-500">Nombre</dt>
                                            <dd class="mt-1 text-sm text-gray-900"><?= htmlspecialchars($autorizacion['nomquienaturiza']) ?></dd>
                                        </div>
                                        <div class="sm:col-span-1">
                                            <dt class="text-sm font-medium text-gray-500">Cargo</dt>
                                            <dd class="mt-1 text-sm text-gray-900"><?= htmlspecialchars($autorizacion['cargoquienautoriza']) ?></dd>
                                        </div>
                                        <div class="sm:col-span-1">
                                            <dt class="text-sm font-medium text-gray-500">Firma</dt>
                                            <dd class="mt-1 text-sm text-gray-900"><?= htmlspecialchars($autorizacion['firmaquienautoriza']) ?></dd>
                                        </div>
                                    </dl>
                                </div>
                            </div>
                        </div>

                        <!-- Sección de acciones -->
                        <div class="mt-6 flex justify-between">
                            <div>
                                <a href="eliminar_autorizacion.php?id=<?= $id ?>" 
                                   class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                                   onclick="return confirm('¿Está seguro de eliminar esta autorización?')">
                                    <i class="fas fa-trash mr-2"></i> Eliminar Autorización
                                </a>
                            </div>
                            <div class="space-x-2">
                                <a href="editar_autorizacion.php?id=<?= $id ?>" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    <i class="fas fa-edit mr-2"></i> Editar
                                </a>
                                <a href="listar_autorizaciones.php" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    <i class="fas fa-list mr-2"></i> Ver todas las autorizaciones
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
</body>
</html>
