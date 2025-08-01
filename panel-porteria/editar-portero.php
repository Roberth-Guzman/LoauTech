<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: ../login.php");
    exit;
}

include '../conexion.php';

$idUsuario = $_SESSION['usuario']['IDper'];
$mensaje = "";

// Procesar formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correo = $_POST['correo'] ?? '';
    $telefono = $_POST['telefono'] ?? '';

    // Validar datos
    if (empty($correo) || empty($telefono)) {
        $mensaje = "Todos los campos son obligatorios.";
    } else {
        // Actualizar tabla contactos
        $sql = "UPDATE contactos SET correocont = ?, numerocont = ? WHERE IDperso = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $correo, $telefono, $idUsuario);
        
        if ($stmt->execute()) {
            $mensaje = "Datos actualizados correctamente.";
            // Actualizar datos en la sesión
            $_SESSION['usuario']['correo'] = $correo;
        } else {
            $mensaje = "Error al actualizar los datos. Por favor, intente nuevamente.";
        }
    }
}

// Obtener datos actuales
$sql = "SELECT 
            c.correocont, 
            c.numerocont
        FROM contactos c
        WHERE c.IDperso = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idUsuario);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();

if (!$usuario) {
    echo "<div style='padding: 2rem; background: #fdd; color: red; font-weight: bold;'>
            No se encontró la información de contacto del usuario con ID $idUsuario.
          </div>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Contacto - Loautech</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>

<body class="bg-light">
    <div class="container-fluid vh-100 d-flex flex-column p-0">
        <!-- Header -->
        <div class="bg-primary text-white p-3 d-flex justify-content-between align-items-center">
            <a href="perfil-porteria.php" class="text-white fs-4 text-decoration-none">
                <i class="bi bi-arrow-left"></i>
            </a>
            <h1 class="mb-0 fs-4 fw-bold text-center flex-grow-1">EDITAR CONTACTO</h1>
            <span class="fs-4 text-white" style="width: 32px;"></span>
        </div>

        <!-- Formulario -->
        <div class="container py-4 flex-grow-1 overflow-auto">
            <div class="row justify-content-center">
                <div class="col-lg-6 col-md-8">
                    <?php if (!empty($mensaje)): ?>
                        <div class="alert alert-<?= strpos($mensaje, 'Error') !== false ? 'danger' : 'success' ?> alert-dismissible fade show" role="alert">
                            <i class="bi <?= strpos($mensaje, 'Error') !== false ? 'bi-exclamation-triangle-fill' : 'bi-check-circle-fill' ?> me-2"></i>
                            <?= $mensaje ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <form method="POST" action="">
                                <div class="mb-3">
                                    <label for="correo" class="form-label">Correo Electrónico</label>
                                    <input type="email" class="form-control" id="correo" name="correo" 
                                           value="<?= htmlspecialchars($usuario['correocont'] ?? '') ?>" required>
                                    <div class="form-text">Ingrese su dirección de correo electrónico.</div>
                                </div>

                                <div class="mb-4">
                                    <label for="telefono" class="form-label">Número de Teléfono</label>
                                    <input type="tel" class="form-control" id="telefono" name="telefono" 
                                           value="<?= htmlspecialchars($usuario['numerocont'] ?? '') ?>" required>
                                    <div class="form-text">Ingrese su número de teléfono de contacto.</div>
                                </div>

                                <div class="d-flex justify-content-between">
                                    <a href="perfil-porteria.php" class="btn btn-secondary">
                                        <i class="bi bi-arrow-left-circle me-2"></i>Volver al Perfil
                                    </a>
                                    <div>
                                        <button type="reset" class="btn btn-warning me-2">
                                            <i class="bi bi-eraser-fill me-1"></i>Limpiar
                                        </button>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-save2-fill me-2"></i>Guardar Cambios
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
