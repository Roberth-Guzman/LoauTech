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
    $nombre = $_POST['nombre'] ?? '';
    $correo = $_POST['correo'] ?? '';
    $telefono = $_POST['telefono'] ?? '';
    $tipoDoc = $_POST['tipodocumento'] ?? '';
    $numeroDoc = $_POST['numerodoc'] ?? '';
    $nuevaContrasena = $_POST['nueva_contrasena'] ?? '';

    // Actualizar tabla personas
    $sql1 = "UPDATE personas SET nombrecompletoper = ?, tipodocumento = ?, numerodoc = ? WHERE IDper = ?";
    $stmt1 = $conn->prepare($sql1);
    $stmt1->bind_param("ssii", $nombre, $tipoDoc, $numeroDoc, $idUsuario);
    $stmt1->execute();

    // Actualizar tabla contactos
    $sql2 = "UPDATE contactos SET correocont = ?, numerocont = ? WHERE IDperso = ?";
    $stmt2 = $conn->prepare($sql2);
    $stmt2->bind_param("sii", $correo, $telefono, $idUsuario);
    $stmt2->execute();

    // Actualizar contraseña si se escribió una nueva
    if (!empty($nuevaContrasena)) {
        $hash = password_hash($nuevaContrasena, PASSWORD_DEFAULT);
        $sql3 = "UPDATE cuentas SET contracue = ? WHERE numerodoc = ?";
        $stmt3 = $conn->prepare($sql3);
        $stmt3->bind_param("si", $hash, $numeroDoc);
        $stmt3->execute();
    }

    $mensaje = "Datos actualizados correctamente.";
}

// Obtener datos actuales
$sql = "SELECT 
            p.nombrecompletoper, 
            p.tipodocumento,
            p.numerodoc,
            c.correocont, 
            c.numerocont
        FROM personas p
        LEFT JOIN contactos c ON p.IDper = c.IDperso
        WHERE p.IDper = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idUsuario);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();

if (!$usuario) {
    echo "<div style='padding: 2rem; background: #fdd; color: red; font-weight: bold;'>
            No se encontró el perfil del usuario con ID $idUsuario.
          </div>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Editar Perfil - Loautech</title>
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
            <h1 class="mb-0 fs-4 fw-bold text-center flex-grow-1">EDITAR PERFIL</h1>
            <span class="fs-4 text-white" style="width: 32px;"></span>
        </div>

        <!-- Formulario -->
        <div class="container py-4 flex-grow-1 overflow-auto">
            <div class="row justify-content-center">
                <div class="col-lg-6 col-md-8">
                    <?php if (!empty($mensaje)): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle-fill me-2"></i><?= $mensaje ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <form method="POST" action="">
                                <div class="mb-3">
                                    <label for="nombre" class="form-label">Nombre completo</label>
                                    <input type="text" class="form-control" id="nombre" name="nombre" value="<?= htmlspecialchars($usuario['nombrecompletoper'] ?? '') ?>" required>
                                </div>

                                <div class="mb-3">
                                    <label for="tipodocumento" class="form-label">Tipo de Documento</label>
                                    <select class="form-select" id="tipodocumento" name="tipodocumento" required>
                                        <option value="TI" <?= ($usuario['tipodocumento'] ?? '') === 'TI' ? 'selected' : '' ?>>TI</option>
                                        <option value="CC" <?= ($usuario['tipodocumento'] ?? '') === 'CC' ? 'selected' : '' ?>>CC</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="numerodoc" class="form-label">Número de Documento</label>
                                    <input type="number" class="form-control" id="numerodoc" name="numerodoc" value="<?= htmlspecialchars($usuario['numerodoc'] ?? '') ?>" required>
                                </div>

                                <div class="mb-3">
                                    <label for="correo" class="form-label">Correo</label>
                                    <input type="email" class="form-control" id="correo" name="correo" value="<?= htmlspecialchars($usuario['correocont'] ?? '') ?>" required>
                                </div>

                                <div class="mb-3">
                                    <label for="telefono" class="form-label">Teléfono</label>
                                    <input type="text" class="form-control" id="telefono" name="telefono" value="<?= htmlspecialchars($usuario['numerocont'] ?? '') ?>" required>
                                </div>

                                <div class="mb-3">
                                    <label for="nueva_contrasena" class="form-label">Nueva Contraseña (opcional)</label>
                                    <input type="password" class="form-control" id="nueva_contrasena" name="nueva_contrasena" placeholder="Solo si deseas cambiarla">
                                </div>

                                <div class="d-flex justify-content-between mt-4">
                                    <a href="perfil-porteria.php" class="btn btn-secondary">
                                        <i class="bi bi-arrow-left-circle me-2"></i>Volver
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
