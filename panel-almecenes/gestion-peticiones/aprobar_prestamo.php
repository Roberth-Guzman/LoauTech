<?php
// Configuración de errores
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error.log');

// Incluir archivo de conexión
require_once __DIR__ . '/../../conexion.php';

// Iniciar sesión
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar autenticación y permisos
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'cuentadante') {
    echo 'error_auth';
    exit;
}

// Verificar que la solicitud sea POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo 'error_method';
    exit;
}

// Obtener y validar datos
$idPrestamo = isset($_POST['id']) ? (int)$_POST['id'] : 0;

if ($idPrestamo <= 0) {
    echo 'error_id';
    exit;
}

try {
    // Iniciar transacción
    $conn->begin_transaction();

    // 1. Obtener información del préstamo
    $sql = "SELECT p.*, a.IDaut as id_autorizacion
            FROM prestamos p
            JOIN autorizacion a ON p.IDautorizacion = a.IDaut
            WHERE p.IDpre = ? AND a.estadoaut = 'pendiente' AND a.cargoquienautoriza = 'sistema'";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $idPrestamo);
    $stmt->execute();
    $prestamo = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$prestamo) {
        throw new Exception("Préstamo no encontrado o ya procesado");
    }

    // 2. Obtener información del elemento
    $sqlElem = "SELECT * FROM elementos WHERE IDele = ?";
    $stmtElem = $conn->prepare($sqlElem);
    $stmtElem->bind_param('i', $prestamo['IDelementos']);
    $stmtElem->execute();
    $elemento = $stmtElem->get_result()->fetch_assoc();
    $stmtElem->close();
    
    if (!$elemento) {
        throw new Exception("Elemento no encontrado");
    }

    // 3. Verificar que hay suficiente stock disponible
    $cantidadDisponible = $elemento['cantidadele'] ?? 0;
    $cantidadSolicitada = $prestamo['cantidad'];
    
    if ($cantidadDisponible < $cantidadSolicitada) {
        throw new Exception("Stock insuficiente. Disponible: $cantidadDisponible, Solicitado: $cantidadSolicitada");
    }

    // 4. Actualizar la autorización a 'pendiente_almacen' (NO 'activo')
    $usuario = $_SESSION['usuario']['nombre'] ?? 'Sistema';
    
    $sqlUpdateAuth = "UPDATE autorizacion 
                     SET estadoaut = 'pendiente_almacen',
                         nomquienaturiza = ?
                     WHERE IDaut = ?";
    
    $stmtAuth = $conn->prepare($sqlUpdateAuth);
    $stmtAuth->bind_param('si', $usuario, $prestamo['id_autorizacion']);
    $stmtAuth->execute();
    $stmtAuth->close();

    // 5. Insertar registro en la tabla aprobaciones
    $sqlAprobacion = "INSERT INTO aprobaciones (IDprestamo, estado, aprobado_por, tipo_aprobacion) 
                      VALUES (?, 'pendiente_almacen', ?, 'cuentadante')
                      ON DUPLICATE KEY UPDATE 
                      estado = VALUES(estado), 
                      fecha_actualizacion = NOW(),
                      aprobado_por = VALUES(aprobado_por),
                      tipo_aprobacion = VALUES(tipo_aprobacion)";
    
    $stmtAprobacion = $conn->prepare($sqlAprobacion);
    $stmtAprobacion->bind_param("is", $idPrestamo, $usuario);
    $stmtAprobacion->execute();
    $stmtAprobacion->close();

    // 6. NO descontar stock aquí - eso se hará cuando almacenes apruebe
    // Solo verificar que hay stock disponible

    // Confirmar transacción
    $conn->commit();
    
    // Respuesta exitosa con HTML
    echo '
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Préstamo Aprobado</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    </head>
    <body class="bg-light">
        <div class="container mt-5">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="card shadow">
                        <div class="card-body text-center">
                            <div class="mb-4">
                                <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                            </div>
                            <h3 class="text-success mb-3">¡Solicitud Aprobada!</h3>
                            <p class="text-muted mb-4">La solicitud ha sido aprobada y enviada a almacenes para su revisión final.</p>
                            <div class="d-grid">
                                <button onclick="window.location.href=\'panel-peticiones.php\'" class="btn btn-primary">
                                    <i class="fas fa-arrow-left me-2"></i>Regresar a Peticiones
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
    </html>';
    
} catch (Exception $e) {
    // Revertir transacción en caso de error
    $conn->rollback();
    
    // Log del error
    error_log("Error en aprobar_prestamo.php: " . $e->getMessage());
    
    // Respuesta de error con HTML
    echo '
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Error al Aprobar</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    </head>
    <body class="bg-light">
        <div class="container mt-5">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="card shadow">
                        <div class="card-body text-center">
                            <div class="mb-4">
                                <i class="fas fa-exclamation-triangle text-danger" style="font-size: 4rem;"></i>
                            </div>
                            <h3 class="text-danger mb-3">Error al Aprobar</h3>
                            <p class="text-muted mb-4">' . htmlspecialchars($e->getMessage()) . '</p>
                            <button onclick="window.location.href=\'panel-peticiones.php\'" class="btn btn-primary">
                                <i class="fas fa-arrow-left me-2"></i>Regresar a Peticiones
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
    </html>';
}

// Cerrar conexión
$conn->close();
?>
