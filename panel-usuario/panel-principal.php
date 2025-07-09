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

// Conexión a BD si es necesaria
require_once '../conexion.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Usuario - Loautech</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body class="bg-light">
    <div class="container-fluid p-0">
        <!-- Header -->
        <div class="bg-primary text-white text-center py-4 mb-4">
            <div class="container">
                <h2 class="mb-1">BIENVENIDO AL PANEL DE USUARIO</h2>
                <div class="d-flex justify-content-center gap-4 align-items-center">
                    <a href="perfil.php" class="text-white text-decoration-none d-flex align-items-center">
                        <i class="bi bi-person me-2"></i>Perfil
                    </a>
                    <a href="../logout.php" class="text-white text-decoration-none d-flex align-items-center">
                        <i class="bi bi-box-arrow-right me-2"></i>Cerrar sesión
                    </a>
                </div>
            </div>
        </div>

        <!-- Contenido principal -->
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 col-md-10 col-sm-12">
                    <!-- Sección de Inventario -->
                    <a href="inventario.php" class="text-decoration-none">
                        <div class="card shadow-sm mb-4">
                            <div class="card-body p-4">
                                <h5 class="text-primary">INVENTARIO</h5>
                            </div>
                        </div>
                    </a>
                    
                    <!-- Sección de Petición -->
                    <a href="peticion.php" class="text-decoration-none">
                        <div class="card shadow-sm mb-4">
                            <div class="card-body p-4">
                                <h5 class="text-primary">PETICIÓN</h5>
                            </div>
                        </div>
                    </a>
                    
                    <!-- Nueva sección para registro de elementos -->
                    <a href="registroelemento.php" class="text-decoration-none">
                        <div class="card shadow-sm mb-4">
                            <div class="card-body p-4">
                                <h5 class="text-primary">REGISTRO DE ELEMENTOS</h5>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>