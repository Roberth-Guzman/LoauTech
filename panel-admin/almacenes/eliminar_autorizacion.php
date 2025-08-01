<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] != 'admin') {
    header('Location: ../login.php');
    exit();
}

// Incluir el archivo de conexión desde la raíz del proyecto
require_once __DIR__ . '/../../conexion.php';

// Verificar si la conexión se estableció correctamente
if (!isset($conn) || $conn->connect_error) {
    $_SESSION['mensaje_error'] = "Error de conexión a la base de datos";
    header('Location: listar_autorizaciones.php');
    exit();
}

// Verificar si se recibió un ID válido
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['mensaje_error'] = "ID de autorización no válido.";
    header('Location: listar_autorizaciones.php?tipo=error');
    exit();
}

$id = intval($_GET['id']);

try {
    // Verificar si la autorización existe antes de intentar eliminarla
    $query = "SELECT IDaut, nomquienaturiza FROM autorizacion WHERE IDaut = ?";
    $stmt = $conn->prepare($query);
    
    if ($stmt === false) {
        throw new Exception("Error en la preparación de la consulta: " . $conn->error);
    }
    
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    if ($result->num_rows === 0) {
        throw new Exception("La autorización que intenta eliminar no existe.");
    }

    $autorizacion = $result->fetch_assoc();

    // Iniciar transacción para asegurar la integridad de los datos
    $conn->begin_transaction();

    try {
        // Primero, eliminar cualquier registro relacionado en otras tablas (si existe)
        // Ejemplo: $conn->query("DELETE FROM tabla_relacionada WHERE autorizacion_id = $id");
        
        // Luego, eliminar la autorización
        $query = "DELETE FROM autorizacion WHERE IDaut = ?";
        $stmt = $conn->prepare($query);
        
        if ($stmt === false) {
            throw new Exception("Error en la preparación de la consulta de eliminación: " . $conn->error);
        }
        
        $stmt->bind_param("i", $id);
        $stmt->execute();
        
        if ($stmt->affected_rows === 0) {
            throw new Exception("No se pudo eliminar la autorización. El registro podría no existir.");
        }
        
        $stmt->close();
        
        // Si todo salió bien, confirmar la transacción
        $conn->commit();
        
        $_SESSION['mensaje'] = "Autorización de " . htmlspecialchars($autorizacion['nomquienaturiza']) . " eliminada correctamente.";
        $_SESSION['tipo_mensaje'] = 'exito';
        
    } catch (Exception $e) {
        // Si hay algún error, deshacer la transacción
        $conn->rollback();
        throw $e; // Relanzar la excepción para manejarla en el catch externo
    }
    
} catch (Exception $e) {
    $_SESSION['mensaje'] = "Error al eliminar la autorización: " . $e->getMessage();
    $_SESSION['tipo_mensaje'] = 'error';
    
    // Registrar el error para depuración (solo en entorno de desarrollo)
    error_log("Error en eliminar_autorizacion.php: " . $e->getMessage());
}

// Redirigir de vuelta al listado
header('Location: listar_autorizaciones.php');
exit();
?>
