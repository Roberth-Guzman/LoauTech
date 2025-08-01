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

$mensaje = '';
$error = '';
$vobo = $nombre_autorizador = $cargo_autorizador = $firma_autorizador = '';
$estado = 'pendiente';

// Procesar el formulario cuando se envía
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $vobo = trim($_POST['vobo']);
    $nombre_autorizador = trim($_POST['nombre_autorizador']);
    $cargo_autorizador = trim($_POST['cargo_autorizador']);
    $firma_autorizador = trim($_POST['firma_autorizador']);
    $estado = $_POST['estado'];

    // Validar campos obligatorios
    if (empty($vobo) || empty($nombre_autorizador) || empty($cargo_autorizador) || empty($firma_autorizador)) {
        $error = "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4'>Todos los campos son obligatorios.</div>";
    } else {
        // Insertar la nueva autorización en la base de datos
        $query = "INSERT INTO autorizacion (
                    VoBoCuentadanteaut, 
                    nomquienaturiza, 
                    cargoquienautoriza, 
                    firmaquienautoriza, 
                    estadoaut
                 ) VALUES (?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssss", $vobo, $nombre_autorizador, $cargo_autorizador, $firma_autorizador, $estado);
        
        if ($stmt->execute()) {
            $id_nuevo = $conn->insert_id;
            $mensaje = "<div class='bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4'>";
            $mensaje .= "Autorización creada correctamente. ";
            $mensaje .= "<a href='editar_autorizacion.php?id=" . $id_nuevo . "' class='font-semibold hover:underline'>Ver detalles</a>";
            $mensaje .= "</div>";
            
            // Limpiar los campos del formulario
            $vobo = $nombre_autorizador = $cargo_autorizador = $firma_autorizador = '';
            $estado = 'pendiente';
        } else {
            $error = "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4'>Error al crear la autorización: " . $conn->error . "</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Autorización - LOAUTECH</title>
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
                                Nueva Autorización
                            </h3>
                            <a href="listar_autorizaciones.php" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-gray-600 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                                <i class="fas fa-arrow-left mr-2"></i> Volver al listado
                            </a>
                        </div>
                    </div>

                    <div class="px-4 py-5 sm:p-6">
                        <?php 
                        // Mostrar mensajes de éxito o error
                        echo $mensaje;
                        echo $error;
                        ?>

                        <form method="POST" class="space-y-6">
                            <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                                <div class="sm:col-span-3">
                                    <label for="vobo" class="block text-sm font-medium text-gray-700">V°B° Cuentadante</label>
                                    <input type="text" name="vobo" id="vobo" value="<?= htmlspecialchars($vobo) ?>" 
                                           class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                </div>

                                <div class="sm:col-span-3">
                                    <label for="nombre_autorizador" class="block text-sm font-medium text-gray-700">Nombre del Autorizador</label>
                                    <input type="text" name="nombre_autorizador" id="nombre_autorizador" value="<?= htmlspecialchars($nombre_autorizador) ?>" 
                                           class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                </div>

                                <div class="sm:col-span-3">
                                    <label for="cargo_autorizador" class="block text-sm font-medium text-gray-700">Cargo del Autorizador</label>
                                    <input type="text" name="cargo_autorizador" id="cargo_autorizador" value="<?= htmlspecialchars($cargo_autorizador) ?>" 
                                           class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                </div>

                                <div class="sm:col-span-3">
                                    <label for="firma_autorizador" class="block text-sm font-medium text-gray-700">Firma del Autorizador</label>
                                    <input type="text" name="firma_autorizador" id="firma_autorizador" value="<?= htmlspecialchars($firma_autorizador) ?>" 
                                           class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                </div>

                                <div class="sm:col-span-3">
                                    <label for="estado" class="block text-sm font-medium text-gray-700">Estado</label>
                                    <select id="estado" name="estado" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        <option value="pendiente" <?= $estado == 'pendiente' ? 'selected' : '' ?>>Pendiente</option>
                                        <option value="aprobado" <?= $estado == 'aprobado' ? 'selected' : '' ?>>Aprobado</option>
                                        <option value="rechazado" <?= $estado == 'rechazado' ? 'selected' : '' ?>>Rechazado</option>
                                    </select>
                                </div>
                            </div>

                            <div class="flex justify-end pt-5">
                                <button type="submit" class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    <i class="fas fa-save mr-2"></i> Guardar Autorización
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
</body>
</html>
