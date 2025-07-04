<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: ../login.php");
    exit;
}

include '../conexion.php';

$idUsuario = $_SESSION['usuario']['IDper'];

$sql = "SELECT 
            p.nombrecompletoper, 
            p.tipodocumento, 
            p.numerodoc,
            c.correocont, 
            c.numerocont,
            r.rol,
            fp.ruta AS foto_ruta
        FROM personas p
        LEFT JOIN contactos c ON p.IDper = c.IDperso
        LEFT JOIN roles r ON p.IDper = r.idper
        LEFT JOIN fotos_perfil fp ON p.IDper = fp.id_persona AND fp.es_actual = 1
        WHERE p.IDper = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idUsuario);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();

if (!$usuario) {
    echo "<div style='padding: 2rem; background: #fdd; color: red; font-weight: bold;'>
            No se encontró información del usuario con ID $idUsuario.
          </div>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Perfil de Portería - Loautech</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" />
</head>
<body class="bg-light">
    <div class="container-fluid vh-100 d-flex flex-column p-0">
        <div class="bg-primary text-white p-3 d-flex justify-content-between align-items-center">
            <a href="panel-principal.php" class="text-white fs-4 text-decoration-none">
                <i class="bi bi-arrow-left"></i>
            </a>
            <h1 class="mb-0 fs-4 fw-bold text-center flex-grow-1">PERFIL DE PORTERÍA</h1>
            <a href="../logout.php" class="text-white fs-4 text-decoration-none" title="Cerrar sesión">
                <i class="bi bi-box-arrow-right"></i>
            </a>
        </div>

        <div class="container py-4 flex-grow-1 overflow-auto">
            <div class="row justify-content-center">
                <div class="col-lg-8 col-md-10">
                    <div class="text-center mb-4">
                        <div class="position-relative d-inline-block" style="width: 150px; height: 150px;">
                            <div class="border border-3 border-dark rounded-circle p-1 mx-auto" style="width: 150px; height: 150px;">
                                <div class="bg-light rounded-circle d-flex justify-content-center align-items-center w-100 h-100 overflow-hidden">
                                    <?php 
                                    $foto = $usuario['foto_ruta'] ?? null;
                                    if ($foto && file_exists("../" . $foto)) {
                                        echo "<img src='../" . htmlspecialchars($foto) . "' alt='Foto de perfil' class='rounded-circle' style='width: 100%; height: 100%; object-fit: cover;'>";
                                    } else {
                                        echo "<i class='bi bi-person text-secondary' style='font-size: 80px;'></i>";
                                    }
                                    ?>
                                </div>
                            </div>
                            <div class="dropdown position-absolute bottom-0 end-0">
                                <button class="btn btn-sm btn-secondary rounded-circle" type="button" id="menuFoto" data-bs-toggle="dropdown" aria-expanded="false" title="Opciones de foto">
                                    <i class="bi bi-camera"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="menuFoto">
                                    <li>
                                        <a class="dropdown-item" href="foto/ver-foto.php" target="_blank" rel="noopener noreferrer">Ver foto</a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#modalSubirFoto">Modificar / Subir foto</a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <a class="dropdown-item text-danger" href="foto/borrar-foto.php" onclick="return confirm('¿Seguro que quieres borrar tu foto de perfil?');">Borrar foto</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <h5 class="mt-3 fw-bold"><?= htmlspecialchars($usuario['nombrecompletoper'] ?? 'No disponible') ?></h5>
                        <p class="text-muted mb-0"><?= htmlspecialchars($usuario['correocont'] ?? 'No disponible') ?></p>
                    </div>

                    <div class="mb-4">
                        <h5 class="text-primary mb-4">INFORMACIÓN PERSONAL - PORTERIA</h5>
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="d-flex align-items-center mb-3">
                                    <span class="me-2"><i class="bi bi-pencil-fill text-warning"></i></span>
                                    <div class="w-100">
                                        <small class="text-muted d-block">Tipo de Identidad</small>
                                        <div class="border-bottom pb-1"><?= htmlspecialchars($usuario['tipodocumento'] ?? 'No disponible') ?></div>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center mb-3">
                                    <span class="me-2"><i class="bi bi-pencil-fill text-warning"></i></span>
                                    <div class="w-100">
                                        <small class="text-muted d-block">Número de Identidad</small>
                                        <div class="border-bottom pb-1"><?= htmlspecialchars($usuario['numerodoc'] ?? 'No disponible') ?></div>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center mb-3">
                                    <span class="me-2"><i class="bi bi-pencil-fill text-warning"></i></span>
                                    <div class="w-100">
                                        <small class="text-muted d-block">Nombre</small>
                                        <div class="border-bottom pb-1"><?= htmlspecialchars($usuario['nombrecompletoper'] ?? 'No disponible') ?></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center mb-3">
                                    <span class="me-2"><i class="bi bi-pencil-fill text-warning"></i></span>
                                    <div class="w-100">
                                        <small class="text-muted d-block">Correo</small>
                                        <div class="border-bottom pb-1"><?= htmlspecialchars($usuario['correocont'] ?? 'No disponible') ?></div>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center mb-3">
                                    <span class="me-2"><i class="bi bi-pencil-fill text-warning"></i></span>
                                    <div class="w-100">
                                        <small class="text-muted d-block">Teléfono</small>
                                        <div class="border-bottom pb-1"><?= htmlspecialchars($usuario['numerocont'] ?? 'No disponible') ?></div>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center mb-3">
                                    <span class="me-2"><i class="bi bi-pencil-fill text-warning"></i></span>
                                    <div class="w-100">
                                        <small class="text-muted d-block">Contraseña</small>
                                        <div class="border-bottom pb-1">*********</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-grid gap-2 mb-4">
                        <a href="editar-portero.php" class="btn btn-primary">
                            <i class="bi bi-pencil-square me-2"></i>Editar Perfil
                        </a>
                    </div>

                    <div class="modal fade" id="modalSubirFoto" tabindex="-1" aria-labelledby="modalSubirFotoLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <form action="foto/subir-foto.php" method="post" enctype="multipart/form-data" class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="modalSubirFotoLabel">Cambiar Foto de Perfil</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                </div>
                                <div class="modal-body">
                                    <input type="file" name="foto_perfil" accept="image/*" required>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                    <button type="submit" class="btn btn-primary">Subir Foto</button>
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
