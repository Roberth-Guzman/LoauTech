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

// Verificar autenticación y permisos (almacén)
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'almacen') {
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
require_once __DIR__ . '/../../conexion.php';

// Obtener y validar datos
$idPrestamo = isset($_POST['id_prestamo']) ? (int)$_POST['id_prestamo'] : 0;
$accion = isset($_POST['accion']) ? $_POST['accion'] : '';
$motivo = isset($_POST['motivo']) ? trim($_POST['motivo']) : '';

if ($idPrestamo <= 0 || empty($accion)) {
    echo 'error_datos';
    exit;
}

try {
    // Iniciar transacción
    $conn->begin_transaction();

    // 1. Verificar que la solicitud existe y está en estado 'activo' (aprobada por cuentadante)
    $sql = "SELECT p.*, a.IDaut as id_autorizacion, a.estadoaut
            FROM prestamos p
            JOIN autorizacion a ON p.IDautorizacion = a.IDaut
            WHERE p.IDpre = ? AND a.estadoaut = 'activo'";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $idPrestamo);
    $stmt->execute();
    $prestamo = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$prestamo) {
        throw new Exception("Solicitud no encontrada o ya procesada");
    }

    $usuario = $_SESSION['usuario']['nombre'] ?? 'Almacén';

    if ($accion === 'aprobar_salida') {
        // Aprobar para salida - La solicitud irá a portería
        // Necesitamos crear un nuevo estado o usar una tabla adicional para tracking
        // Por ahora, vamos a usar un campo en la tabla autorizacion o crear una tabla de estados
        
        // Opción: Agregar un campo 'aprobado_almacen' a la tabla autorizacion
        // O crear una tabla 'estados_solicitud' para mejor tracking
        
        // Por simplicidad, vamos a usar un campo adicional en autorizacion
        $sqlUpdate = "UPDATE autorizacion 
                     SET VoBoCuentadanteaut = CONCAT(IFNULL(VoBoCuentadanteaut, ''), ' | Aprobado por almacén: $usuario - ', NOW())
                     WHERE IDaut = ?";
        
        $stmtUpdate = $conn->prepare($sqlUpdate);
        $stmtUpdate->bind_param('i', $prestamo['id_autorizacion']);
        $stmtUpdate->execute();
        $stmtUpdate->close();

        // Log de la acción
        error_log("Solicitud #{$idPrestamo} aprobada para salida por almacén: $usuario");

        $mensaje = "¡Solicitud Aprobada para Salida!";
        $descripcion = "La solicitud ha sido aprobada por almacén y enviada a portería para autorización final de salida.";
        $tipo = "success";
        $icono = "fas fa-check-circle";
        $color = "text-success";

    } elseif ($accion === 'rechazar_final') {
        // Rechazo final por almacén
        if (empty($motivo)) {
            throw new Exception("El motivo del rechazo es obligatorio");
        }

        // Cambiar estado a 'inactivo' (rechazado final)
        $sqlUpdate = "UPDATE autorizacion 
                     SET estadoaut = 'inactivo',
                         VoBoCuentadanteaut = CONCAT(IFNULL(VoBoCuentadanteaut, ''), ' | Rechazado por almacén: $usuario - ', NOW(), ' - Motivo: $motivo')
                     WHERE IDaut = ?";
        
        $stmtUpdate = $conn->prepare($sqlUpdate);
        $stmtUpdate->bind_param('i', $prestamo['id_autorizacion']);
        $stmtUpdate->execute();
        $stmtUpdate->close();

        // Guardar motivo en la tabla prestamos
        $sqlMotivo = "UPDATE prestamos SET motivo_rechazo = ? WHERE IDpre = ?";
        $stmtMotivo = $conn->prepare($sqlMotivo);
        $stmtMotivo->bind_param('si', $motivo, $idPrestamo);
        $stmtMotivo->execute();
        $stmtMotivo->close();

        // Log de la acción
        error_log("Solicitud #{$idPrestamo} rechazada finalmente por almacén: $usuario. Motivo: $motivo");

        $mensaje = "Solicitud Rechazada";
        $descripcion = "La solicitud ha sido rechazada finalmente por almacén. Motivo: " . htmlspecialchars($motivo);
        $tipo = "warning";
        $icono = "fas fa-times-circle";
        $color = "text-warning";

    } else {
        throw new Exception("Acción no válida");
    }

    // Confirmar transacción
    $conn->commit();
    
    // Respuesta exitosa con HTML
    echo "
    <!DOCTYPE html>
    <html lang=\"es\">
    <head>
        <meta charset=\"UTF-8\">
        <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
        <title>Acción Completada</title>
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
                                <i class=\"$icono $color\" style=\"font-size: 4rem;\"></i>
                            </div>
                            <h3 class=\"$color mb-3\">$mensaje</h3>
                            <p class=\"text-muted mb-4\">$descripcion</p>
                            <div class=\"d-grid\">
                                <button onclick=\"window.location.href='panel-solicitudes.php'\" class=\"btn btn-primary\">
                                    <i class=\"fas fa-arrow-left me-2\"></i>Regresar a Solicitudes
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
    error_log("Error en procesar_almacen.php: " . $e->getMessage());
    
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
                            <h3 class=\"text-danger mb-3\">Error al Procesar</h3>
                            <p class=\"text-muted mb-4\">" . htmlspecialchars($e->getMessage()) . "</p>
                            <button onclick=\"window.location.href='panel-solicitudes.php'\" class=\"btn btn-primary\">
                                <i class=\"fas fa-arrow-left me-2\"></i>Regresar a Solicitudes
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
