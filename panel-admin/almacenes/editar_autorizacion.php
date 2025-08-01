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

// Verificar si se recibió un ID válido
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: listar_autorizaciones.php');
    exit();
}

$id = intval($_GET['id']);

// Obtener los datos actuales de la autorización
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
        // Actualizar la autorización en la base de datos
        $query = "UPDATE autorizacion SET 
                 VoBoCuentadanteaut = ?, 
                 nomquienaturiza = ?, 
                 cargoquienautoriza = ?, 
                 firmaquienautoriza = ?, 
                 estadoaut = ?
                 WHERE IDaut = ?";
        
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssssi", $vobo, $nombre_autorizador, $cargo_autorizador, $firma_autorizador, $estado, $id);
        
        if ($stmt->execute()) {
            $mensaje = "<div class='bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4'>";
            $mensaje .= "Autorización actualizada correctamente.";
            $mensaje .= "</div>";
            
            // Actualizar los datos mostrados
            $autorizacion = array_merge($autorizacion, [
                'VoBoCuentadanteaut' => $vobo,
                'nomquienaturiza' => $nombre_autorizador,
                'cargoquienautoriza' => $cargo_autorizador,
                'firmaquienautoriza' => $firma_autorizador,
                'estadoaut' => $estado
            ]);
        } else {
            $error = "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4'>Error al actualizar la autorización: " . $conn->error . "</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Autorización - LOAUTECH</title>
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
                                Editar Autorización #<?= $id ?>
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
                                    <input type="text" name="vobo" id="vobo" 
                                           value="<?= htmlspecialchars($autorizacion['VoBoCuentadanteaut']) ?>" 
                                           class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                </div>

                                <div class="sm:col-span-3">
                                    <label for="nombre_autorizador" class="block text-sm font-medium text-gray-700">Nombre del Autorizador</label>
                                    <input type="text" name="nombre_autorizador" id="nombre_autorizador" 
                                           value="<?= htmlspecialchars($autorizacion['nomquienaturiza']) ?>" 
                                           class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                </div>

                                <div class="sm:col-span-3">
                                    <label for="cargo_autorizador" class="block text-sm font-medium text-gray-700">Cargo del Autorizador</label>
                                    <input type="text" name="cargo_autorizador" id="cargo_autorizador" 
                                           value="<?= htmlspecialchars($autorizacion['cargoquienautoriza']) ?>" 
                                           class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                </div>

                                <div class="sm:col-span-3">
                                    <label for="firma_autorizador" class="block text-sm font-medium text-gray-700">Firma del Autorizador</label>
                                    <input type="text" name="firma_autorizador" id="firma_autorizador" 
                                           value="<?= htmlspecialchars($autorizacion['firmaquienautoriza']) ?>" 
                                           class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                </div>

                                <div class="sm:col-span-3">
                                    <label for="estado" class="block text-sm font-medium text-gray-700">Estado</label>
                                    <select id="estado" name="estado" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        <option value="pendiente" <?= $autorizacion['estadoaut'] == 'pendiente' ? 'selected' : '' ?>>Pendiente</option>
                                        <option value="aprobado" <?= $autorizacion['estadoaut'] == 'aprobado' ? 'selected' : '' ?>>Aprobado</option>
                                        <option value="rechazado" <?= $autorizacion['estadoaut'] == 'rechazado' ? 'selected' : '' ?>>Rechazado</option>
                                    </select>
                                </div>
                            </div>

                            <div class="flex justify-between pt-5">
                                <a href="eliminar_autorizacion.php?id=<?= $id ?>" 
                                   class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                                   onclick="return confirm('¿Está seguro de eliminar esta autorización?')">
                                    <i class="fas fa-trash mr-2"></i> Eliminar
                                </a>
                                <div>
                                    <a href="listar_autorizaciones.php" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        Cancelar
                                    </a>
                                    <button type="submit" class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        <i class="fas fa-save mr-2"></i> Guardar Cambios
                                    </button>
                                </div>
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
