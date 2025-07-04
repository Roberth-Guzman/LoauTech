<?php
session_start();
include '../conexion.php';

// Verificar si el usuario es de portería
if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit;
}

if ($_SESSION['user']['rol'] !== 'porteria') {
    header("Location: ../panel-usuario/panel-principal.php");
    exit;
}

// Obtener todos los registros
$sql = "SELECT p.nombrecompletoper, p.numerodoc, m.horaentradamar, m.horasalidamar, m.fechamar
        FROM marcador m
        JOIN ingresoelementos ie ON m.IDmar = ie.IDmarcador
        JOIN personas p ON ie.IDPER = p.IDper
        ORDER BY m.fechamar DESC, m.horaentradamar DESC";
$registros = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registros - Loautech</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body class="bg-light">
    <div class="container-fluid p-0 min-vh-100 d-flex flex-column">
        <!-- Header -->
        <div class="bg-primary py-3 d-flex justify-content-between align-items-center px-4">
            <a href="panel-principal.php" class="text-white fs-4"><i class="bi bi-arrow-left"></i></a>
            <h4 class="text-white text-center mb-0 flex-grow-1">REGISTROS DE MOVIMIENTOS</h4>
            <a href="../logout.php" class="text-white fs-4"><i class="bi bi-box-arrow-right"></i></a>
        </div>
        
        <!-- Contenido -->
        <div class="flex-grow-1 bg-white">
            <div class="container py-4">
                <!-- Filtros -->
                <div class="card mb-4 shadow-sm">
                    <div class="card-body">
                        <form id="filtroForm">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="fecha" class="form-label">Fecha:</label>
                                    <input type="date" class="form-control" id="fecha" name="fecha">
                                </div>
                                <div class="col-md-6">
                                    <label for="documento" class="form-label">Documento:</label>
                                    <input type="text" class="form-control" id="documento" name="documento" placeholder="Buscar por documento">
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Tabla de registros -->
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-primary">
                            <tr>
                                <th>Nombre</th>
                                <th>Documento</th>
                                <th>Fecha</th>
                                <th>Hora Entrada</th>
                                <th>Hora Salida</th>
                                <th>Tiempo</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($registro = $registros->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($registro['nombrecompletoper']) ?></td>
                                <td><?= htmlspecialchars($registro['numerodoc']) ?></td>
                                <td><?= htmlspecialchars($registro['fechamar']) ?></td>
                                <td><?= htmlspecialchars($registro['horaentradamar']) ?></td>
                                <td><?= htmlspecialchars($registro['horasalidamar'] ?: '--') ?></td>
                                <td>
                                    <?php if ($registro['horasalidamar']): 
                                        $entrada = new DateTime($registro['horaentradamar']);
                                        $salida = new DateTime($registro['horasalidamar']);
                                        $intervalo = $entrada->diff($salida);
                                        echo $intervalo->format('%H:%I:%S');
                                    else: ?>
                                        --
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Filtrado en tiempo real
        document.getElementById('fecha').addEventListener('change', filtrarRegistros);
        document.getElementById('documento').addEventListener('input', filtrarRegistros);
        
        function filtrarRegistros() {
            const fecha = document.getElementById('fecha').value.toLowerCase();
            const documento = document.getElementById('documento').value.toLowerCase();
            
            document.querySelectorAll('tbody tr').forEach(fila => {
                const filaFecha = fila.cells[2].textContent.toLowerCase();
                const filaDocumento = fila.cells[1].textContent.toLowerCase();
                
                const coincideFecha = fecha === '' || filaFecha.includes(fecha);
                const coincideDocumento = documento === '' || filaDocumento.includes(documento);
                
                fila.style.display = (coincideFecha && coincideDocumento) ? '' : 'none';
            });
        }
    </script>
</body>
</html>