<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/loautech-main/conexion.php';

header('Content-Type: application/json');

// Verificar autenticación y permisos
if (!isset($_SESSION['usuario'])) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit();
}

// Verificar que se reciba una acción válida
if (!isset($_POST['accion']) || !in_array($_POST['accion'], ['aprobar', 'rechazar'])) {
    echo json_encode(['success' => false, 'message' => 'Acción no válida']);
    exit();
}

$accion = $_POST['accion'];
$idPrestamo = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$motivo = isset($_POST['motivo']) ? trim($_POST['motivo']) : '';
$usuario = $_SESSION['usuario']['nombre'] ?? 'Sistema';
$rol = $_SESSION['usuario']['rol'] ?? '';

if ($idPrestamo <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID de préstamo no válido']);
    exit();
}

try {
    $conn->begin_transaction();

    // Verificar si el préstamo existe y obtener información del solicitante
    $sqlInfo = "SELECT p.*, per.nombrecompletoper as nombre_solicitante,
                       cont.correocont as correo, cont.numerocont as telefono
                FROM prestamos p 
                JOIN personas per ON p.IDpersonas = per.IDper
                LEFT JOIN contactos cont ON per.IDper = cont.IDperso
                WHERE p.IDpre = ?";
    $stmtInfo = $conn->prepare($sqlInfo);
    $stmtInfo->bind_param("i", $idPrestamo);
    $stmtInfo->execute();
    $prestamo = $stmtInfo->get_result()->fetch_assoc();

    if (!$prestamo) {
        throw new Exception("Préstamo no encontrado");
    }

    // Determinar el nuevo estado según la acción y el rol
    $nuevoEstado = '';
    $asunto = '';
    $mensaje = '';
    $tipoAprobacion = '';
    $correoDestino = $prestamo['correo'] ?? '';
    $telefono = $prestamo['telefono'] ?? '';

    if ($accion === 'aprobar') {
        if ($rol === 'cuentadante') {
            $nuevoEstado = 'pendiente_almacen';
            $asunto = "Solicitud Aprobada por Cuentadante";
            $mensaje = "Su solicitud #$idPrestamo ha sido aprobada por el cuentadante y está pendiente de revisión por almacén.";
            $tipoAprobacion = 'cuentadante';
        } elseif ($rol === 'almacenes') {
            $nuevoEstado = 'aprobado';
            $asunto = "Solicitud Aprobada por Almacén";
            $mensaje = "Su solicitud #$idPrestamo ha sido aprobada por el área de almacén. Puede pasar a recoger los elementos solicitados.";
            $tipoAprobacion = 'almacen';

            // === DESCONTAR STOCK SOLO AL APROBAR EN ALMACÉN ===
            // Obtener información del elemento y cantidad solicitada
            $sqlElemento = "SELECT IDelementos, cantidad FROM prestamos WHERE IDpre = ?";
            $stmtElemento = $conn->prepare($sqlElemento);
            $stmtElemento->bind_param("i", $idPrestamo);
            $stmtElemento->execute();
            $resElemento = $stmtElemento->get_result()->fetch_assoc();
            $stmtElemento->close();

            $idElemento = $resElemento['IDelementos'] ?? 0;
            $cantidadSolicitada = $resElemento['cantidad'] ?? 0;

            if ($idElemento > 0 && $cantidadSolicitada > 0) {
                // Verificar stock actual
                $sqlStock = "SELECT cantidadele FROM elementos WHERE IDele = ?";
                $stmtStock = $conn->prepare($sqlStock);
                $stmtStock->bind_param("i", $idElemento);
                $stmtStock->execute();
                $resStock = $stmtStock->get_result()->fetch_assoc();
                $stmtStock->close();

                $stockActual = $resStock['cantidadele'] ?? 0;
                if ($stockActual < $cantidadSolicitada) {
                    throw new Exception("Stock insuficiente para aprobar la solicitud. Disponible: $stockActual, Solicitado: $cantidadSolicitada");
                }

                // Descontar stock
                $sqlDescontar = "UPDATE elementos SET cantidadele = cantidadele - ? WHERE IDele = ?";
                $stmtDescontar = $conn->prepare($sqlDescontar);
                $stmtDescontar->bind_param("ii", $cantidadSolicitada, $idElemento);
                if (!$stmtDescontar->execute()) {
                    throw new Exception("Error al descontar el stock del elemento: " . $conn->error);
                }
                $stmtDescontar->close();

                // Si el stock llega a 0, actualizar estado a 'en prestamo'
                $nuevoStock = $stockActual - $cantidadSolicitada;
                if ($nuevoStock <= 0) {
                    $sqlEstado = "UPDATE elementos SET estado = 'en prestamo' WHERE IDele = ?";
                    $stmtEstado = $conn->prepare($sqlEstado);
                    $stmtEstado->bind_param("i", $idElemento);
                    $stmtEstado->execute();
                    $stmtEstado->close();
                }
            }
            // === FIN DESCONTAR STOCK ===
        }
    } else { // rechazar
        if (empty($motivo)) {
            throw new Exception("Se requiere un motivo para el rechazo");
        }
        $nuevoEstado = 'rechazado';
        $tipoAprobacion = ($rol === 'cuentadante') ? 'cuentadante' : 'almacen';
        $asunto = "Solicitud Rechazada";
        $mensaje = "Lamentamos informarle que su solicitud #$idPrestamo ha sido rechazada.\n\nMotivo del rechazo: $motivo\n\nSi tiene alguna duda, por favor comuníquese con el área correspondiente.";
    }

    // Insertar o actualizar en la tabla de aprobaciones
    $sqlAprobacion = "INSERT INTO aprobaciones (IDprestamo, estado, motivo, aprobado_por, tipo_aprobacion) 
                      VALUES (?, ?, ?, ?, ?)
                      ON DUPLICATE KEY UPDATE 
                      estado = VALUES(estado), 
                      motivo = VALUES(motivo),
                      fecha_actualizacion = NOW(),
                      aprobado_por = VALUES(aprobado_por),
                      tipo_aprobacion = VALUES(tipo_aprobacion)";
    
    $stmtAprobacion = $conn->prepare($sqlAprobacion);
    $stmtAprobacion->bind_param("issss", $idPrestamo, $nuevoEstado, $motivo, $usuario, $tipoAprobacion);
    
    if (!$stmtAprobacion->execute()) {
        throw new Exception("Error al actualizar el estado de aprobación: " . $conn->error);
    }

    // Si es un rechazo, actualizar también el préstamo con el motivo
    if ($accion === 'rechazar') {
        $sqlUpdatePrestamo = "UPDATE prestamos SET motivo_rechazo = ? WHERE IDpre = ?";
        $stmtUpdate = $conn->prepare($sqlUpdatePrestamo);
        $stmtUpdate->bind_param("si", $motivo, $idPrestamo);
        
        if (!$stmtUpdate->execute()) {
            throw new Exception("Error al actualizar el préstamo: " . $conn->error);
        }
    }

    // Enviar notificación por correo si hay dirección de correo
    if (!empty($correoDestino) && filter_var($correoDestino, FILTER_VALIDATE_EMAIL)) {
        enviarNotificacionCorreo($correoDestino, $prestamo['nombre_solicitante'], $asunto, $mensaje);
    }

    // Si hay número de teléfono, podrías agregar aquí el envío de SMS si lo deseas
    // enviarNotificacionSMS($telefono, $mensaje);

    $conn->commit();
    echo json_encode(['success' => true, 'message' => 'Solicitud procesada correctamente']);

} catch (Exception $e) {
    $conn->rollback();
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

/**
 * Función para enviar notificaciones por correo electrónico
 */
function enviarNotificacionCorreo($destinatario, $nombre, $asunto, $mensaje) {
    $headers = "From: sistema@loatech.com\r\n";
    $headers .= "Reply-To: no-responder@loatech.com\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

    $mensajeHTML = "
    <html>
    <head>
        <title>$asunto</title>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px; }
            .header { background-color: #4a86e8; color: white; padding: 15px; border-radius: 5px 5px 0 0; }
            .content { padding: 20px; }
            .footer { margin-top: 20px; font-size: 12px; color: #777; text-align: center; }
            .motivo { background-color: #f5f5f5; padding: 10px; border-left: 4px solid #e74c3c; margin: 10px 0; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h2>Sistema de Gestión de Almacén - Loatech</h2>
            </div>
            <div class='content'>
                <p>Estimado/a $nombre,</p>
                <p>" . nl2br(htmlspecialchars($mensaje)) . "</p>
                <p>Para más información, por favor ingrese al sistema.</p>
            </div>
            <div class='footer'>
                <p>Este es un correo automático, por favor no responda a este mensaje.</p>
                <p>&copy; " . date('Y') . " Loatech - Todos los derechos reservados</p>
            </div>
        </div>
    </body>
    </html>";

    // Descomentar para habilitar el envío de correos real
    // mail($destinatario, $asunto, $mensajeHTML, $headers);
    
    // Para depuración, guardamos el correo en un archivo
    $logDir = __DIR__ . '/logs';
    if (!file_exists($logDir)) {
        mkdir($logDir, 0777, true);
    }
    
    file_put_contents(
        $logDir . '/correos_enviados.log', 
        "Fecha: " . date('Y-m-d H:i:s') . 
        "\nDestinatario: $destinatario" .
        "\nAsunto: $asunto" .
        "\nMensaje: $mensaje" .
        "\n----------------------------------------\n\n", 
        FILE_APPEND
    );
}

/**
 * Función para enviar notificaciones por SMS (opcional)
 */
function enviarNotificacionSMS($telefono, $mensaje) {
    if (empty($telefono)) {
        return;
    }
    
    // Implementar lógica de envío de SMS aquí
    // Esto es solo un ejemplo y necesitarás integrar con un proveedor de SMS
    
    $logDir = __DIR__ . '/logs';
    if (!file_exists($logDir)) {
        mkdir($logDir, 0777, true);
    }
    
    file_put_contents(
        $logDir . '/sms_enviados.log',
        "Fecha: " . date('Y-m-d H:i:s') .
        "\nTeléfono: $telefono" .
        "\nMensaje: $mensaje" .
        "\n----------------------------------------\n\n",
        FILE_APPEND
    );
}
?>
