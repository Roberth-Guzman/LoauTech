<?php
// Configuración de errores
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error.log');

// Configurar zona horaria
date_default_timezone_set('America/Bogota');

// Iniciar sesión
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar autenticación y permisos (portería)
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'porteria') {
    echo '
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Acceso Denegado</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body class="bg-light">
        <div class="container mt-5">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="card shadow">
                        <div class="card-body text-center">
                            <i class="fas fa-exclamation-triangle text-danger" style="font-size: 4rem;"></i>
                            <h3 class="text-danger mb-3">Acceso Denegado</h3>
                            <p class="text-muted mb-4">No tiene permisos para realizar esta acción.</p>
                            <button onclick="window.location.href=\'/loautech-main/login.php\'" class="btn btn-primary">
                                Iniciar Sesión
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
    </html>';
    exit;
}

// Verificar que la solicitud sea POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo 'error_method';
    exit;
}

// Incluir archivo de conexión
require_once __DIR__ . '/../conexion.php';

// Obtener y validar datos
$idPrestamo = isset($_POST['id_prestamo']) ? (int)$_POST['id_prestamo'] : 0;
$nombreVigilante = isset($_POST['nombre_vigilante']) ? trim($_POST['nombre_vigilante']) : '';
$observaciones = isset($_POST['observaciones']) ? trim($_POST['observaciones']) : '';

if ($idPrestamo <= 0 || empty($nombreVigilante)) {
    echo 'error_datos';
    exit;
}

try {
    // Iniciar transacción
    $conn->begin_transaction();

    // 1. Verificar que la solicitud existe y está aprobada por almacén
$sql = "SELECT p.*, a.IDaut as id_autorizacion, a.estadoaut, a.VoBoCuentadanteaut,
               per.nombrecompletoper, per.numerodoc,
               e.nombreele, e.codigoele
        FROM prestamos p
        JOIN autorizacion a ON p.IDautorizacion = a.IDaut
        JOIN personas per ON p.IDpersonas = per.IDper
        JOIN elementos e ON p.IDelementos = e.IDele
        WHERE p.IDpre = ? 
          AND a.estadoaut = 'activo'";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $idPrestamo);
    $stmt->execute();
    $prestamo = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$prestamo) {
        throw new Exception("Solicitud no encontrada o no está aprobada para salida");
    }

    $idAutorizacion = $prestamo['id_autorizacion'];

    // 2. Verificar que no se haya registrado ya la salida
    $sqlCheck = "SELECT COUNT(*) as count FROM vigilantes WHERE idautorizacion = ?";
    $stmtCheck = $conn->prepare($sqlCheck);
    $stmtCheck->bind_param('i', $idAutorizacion);
    $stmtCheck->execute();
    $checkResult = $stmtCheck->get_result()->fetch_assoc();
    $stmtCheck->close();

    if ($checkResult['count'] > 0) {
        throw new Exception("Esta solicitud ya tiene registrada la salida");
    }

    $fechaHoraSalida = date('Y-m-d H:i:s');
    $vigilanteSalida = $_SESSION['usuario']['nombre'] ?? $nombreVigilante;

    // 3. Crear registro en tabla vigilantes
    // Nota: Los campos de ingreso se mantienen NULL hasta que el elemento regrese
    $sqlVigilante = "INSERT INTO vigilantes 
                    (nomvigilantesalida, firmasolicitante, fechaautorizacion, idautorizacion, nomvigilanteingreso) 
                    VALUES (?, ?, ?, ?, NULL)";
    
    $stmtVigilante = $conn->prepare($sqlVigilante);
    $stmtVigilante->bind_param('sssi', $vigilanteSalida, $prestamo['nombrecompletoper'], $fechaHoraSalida, $idAutorizacion);
    $stmtVigilante->execute();
    $idVigilante = $conn->insert_id;
    $stmtVigilante->close();

    // 4. Crear registro en tabla marcaciones
    // Nota: Los campos de ingreso se mantienen NULL hasta que el elemento regrese
    $sqlMarcacion = "INSERT INTO marcaciones 
    (hfecsalidamarc, estadomarc, IDpres, IDautori, hfecingresomarc) 
    VALUES (?, 'activo', ?, ?, NULL)";
    
    $stmtMarcacion = $conn->prepare($sqlMarcacion);
    $stmtMarcacion->bind_param('sii', $fechaHoraSalida, $idPrestamo, $idAutorizacion);
    $stmtMarcacion->execute();
    $stmtMarcacion->close();

    // 5. Actualizar autorización para marcar como completamente procesada
    $sqlUpdateAuth = "UPDATE autorizacion 
                     SET VoBoCuentadanteaut = CONCAT(IFNULL(VoBoCuentadanteaut, ''), ' | Salida autorizada por portería: $vigilanteSalida - ', NOW())
                     WHERE IDaut = ?";
    
    $stmtUpdateAuth = $conn->prepare($sqlUpdateAuth);
    $stmtUpdateAuth->bind_param('i', $prestamo['id_autorizacion']);
    $stmtUpdateAuth->execute();
    $stmtUpdateAuth->close();

    // 6. Cambiar estado de la autorización a 'en_prestamo'
    $updateEstado = $conn->prepare("UPDATE autorizacion SET estadoaut = 'en_prestamo' WHERE IDaut = ?");
    $updateEstado->bind_param('i', $idAutorizacion);
    $updateEstado->execute();
    $updateEstado->close();

    // 7. Log de la acción
    error_log("Salida autorizada - Solicitud #{$idPrestamo} - Vigilante: $vigilanteSalida - Fecha: $fechaHoraSalida");

    // Confirmar transacción
    $conn->commit();
    
    // Respuesta exitosa con HTML
    echo "
    <!DOCTYPE html>
    <html lang=\"es\">
    <head>
        <meta charset=\"UTF-8\">
        <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
        <title>Salida Autorizada</title>
        <link href=\"https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css\" rel=\"stylesheet\">
        <link href=\"https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css\" rel=\"stylesheet\">
    </head>
    <body class=\"bg-light\">
        <div class=\"container mt-5\">
            <div class=\"row justify-content-center\">
                <div class=\"col-md-8\">
                    <div class=\"card shadow\">
                        <div class=\"card-body text-center\">
                            <div class=\"mb-4\">
                                <i class=\"fas fa-check-circle text-success\" style=\"font-size: 4rem;\"></i>
                            </div>
                            <h3 class=\"text-success mb-3\">¡Salida Autorizada!</h3>
                            <p class=\"text-muted mb-4\">La salida del elemento ha sido autorizada exitosamente.</p>
                            
                            <!-- Resumen de la autorización -->
                            <div class=\"bg-light p-4 rounded mb-4\">
                                <h5 class=\"mb-3\">Resumen de la Autorización</h5>
                                <div class=\"row text-start\">
                                    <div class=\"col-md-6\">
                                        <p class=\"mb-2\"><strong>Solicitud:</strong> #{$prestamo['IDpre']}</p>
                                        <p class=\"mb-2\"><strong>Solicitante:</strong> {$prestamo['nombrecompletoper']}</p>
                                        <p class=\"mb-2\"><strong>Documento:</strong> {$prestamo['numerodoc']}</p>
                                        <p class=\"mb-2\"><strong>Elemento:</strong> {$prestamo['nombreele']}</p>
                                    </div>
                                    <div class=\"col-md-6\">
                                        <p class=\"mb-2\"><strong>Código:</strong> {$prestamo['codigoele']}</p>
                                        <p class=\"mb-2\"><strong>Cantidad:</strong> {$prestamo['cantidad']}</p>
                                        <p class=\"mb-2\"><strong>Vigilante:</strong> $vigilanteSalida</p>
                                        <p class=\"mb-2\"><strong>Fecha/Hora:</strong> $fechaHoraSalida</p>
                                    </div>
                                </div>
                                " . (!empty($observaciones) ? "<p class=\"mb-0 mt-3\"><strong>Observaciones:</strong> " . htmlspecialchars($observaciones) . "</p>" : "") . "
                            </div>
                            
                            <div class=\"alert alert-info\">
                                <i class=\"fas fa-info-circle me-2\"></i>
                                <strong>Importante:</strong> El registro de ingreso se completará cuando el elemento sea devuelto.
                            </div>
                            
                            <div class=\"d-grid gap-2 d-md-flex justify-content-md-center\">
                                <button onclick=\"window.location.href='aceptar-peticiones.php'\" class=\"btn btn-primary\">
                                    <i class=\"fas fa-arrow-left me-2\"></i>Regresar a Portería
                                </button>
                                <button onclick=\"window.print()\" class=\"btn btn-outline-secondary\">
                                    <i class=\"fas fa-print me-2\"></i>Imprimir Comprobante
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
    </html>";
    
} catch (Exception $e) {
    // Revertir transacción en caso de error
    $conn->rollback();
    
    // Log del error
    error_log("Error en registrar_salida.php: " . $e->getMessage());
    
    // Respuesta de error con HTML
    echo "
    <!DOCTYPE html>
    <html lang=\"es\">
    <head>
        <meta charset=\"UTF-8\">
        <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
        <title>Error</title>
        <link href=\"https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css\" rel=\"stylesheet\">
        <link href=\"https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css\" rel=\"stylesheet\">
    </head>
    <body class=\"bg-light\">
        <div class=\"container mt-5\">
            <div class=\"row justify-content-center\">
                <div class=\"col-md-6\">
                    <div class=\"card shadow\">
                        <div class=\"card-body text-center\">
                            <div class=\"mb-4\">
                                <i class=\"fas fa-exclamation-triangle text-danger\" style=\"font-size: 4rem;\"></i>
                            </div>
                            <h3 class=\"text-danger mb-3\">Error al Autorizar Salida</h3>
                            <p class=\"text-muted mb-4\">" . htmlspecialchars($e->getMessage()) . "</p>
                            <button onclick=\"window.location.href='aceptar-peticiones.php'\" class=\"btn btn-primary\">
                                <i class=\"fas fa-arrow-left me-2\"></i>Regresar a Portería
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
    </html>";
}

// Cerrar conexión
$conn->close();
?>
