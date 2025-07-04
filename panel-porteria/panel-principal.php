<?php
session_start();

// Validar que el usuario haya iniciado sesión
if (!isset($_SESSION['usuario'])) {
    header('Location: ../login.php');
    exit();
}

// Validar que el rol sea 'porteria'
if ($_SESSION['usuario']['rol'] !== 'porteria') {
    header('Location: ../login.php?error=acceso_no_autorizado');
    exit();
}

// Incluir conexión a la base de datos
require_once '../conexion.php';

// Obtener y cargar datos adicionales si lo necesitas
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

// Guardar datos del usuario en sesión si es necesario
$_SESSION['user'] = [
    'nombre' => $datos_usuario['nombrecompletoper'] ?? 'Portería',
    'rol' => $datos_usuario['rol'] ?? 'porteria'
];
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de porteria - Loautech</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>

<body class="bg-light">
    <div class="container-fluid p-0 min-vh-100 d-flex flex-column">
        <!-- Header -->
        <div class="bg-primary py-3 d-flex flex-column align-items-center">
            <h4 class="text-white text-center mb-1">BIENVENIDO AL PANEL DE PORTERIA</h4>
            <div class="d-flex gap-3">
                <a href="perfil-porteria.php" class="text-white small text-decoration-none">
                    <i class="bi bi-person-circle me-1"></i>Perfil
                </a>
                <a href="../logout.php" class="text-white text-decoration-none">
                    <i class="bi bi-box-arrow-right me-2"></i>Cerrar sesión
                </a>
            </div>
        </div>

        <!-- Contenido principal -->
        <div class="flex-grow-1 bg-white">
            <div class="container py-5">
                <div class="row justify-content-center">
                    <div class="col-12 col-md-10 col-lg-8">
                        <!-- Tarjeta del escáner -->
                        <a href="escanner.php" class="text-decoration-none">
                            <div class="card shadow-sm border-0 mb-3">
                                <div class="card-body py-3 px-4">
                                    <h6 class="text-primary mb-0">ESCANER DE CARNET</h6>
                                </div>
                            </div>
                        </a>
                        <!-- Dentro del contenedor, después de la tarjeta del escáner -->
                        <a href="registros.php" class="text-decoration-none">
                            <div class="card shadow-sm border-0 mb-3">
                                <div class="card-body py-3 px-4">
                                    <h6 class="text-primary mb-0">REGISTROS DE MOVIMIENTOS</h6>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>