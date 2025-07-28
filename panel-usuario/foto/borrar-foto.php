<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: ../login.php");
    exit;
}

include '../../conexion.php';

$idUsuario = $_SESSION['usuario']['IDper'];

// Obtener la información de la foto actual
$sql = "SELECT id, ruta FROM fotos_perfil WHERE id_persona = ? AND es_actual = 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idUsuario);
$stmt->execute();
$result = $stmt->get_result();
$foto = $result->fetch_assoc();

if (!$foto) {
    $_SESSION['error'] = "No se encontró ninguna foto de perfil para eliminar.";
    header("Location: ../perfil.php");
    exit;
}

$rutaCompleta = __DIR__ . '/../../' . $foto['ruta'];
$exito = true;

// Iniciar transacción
$conn->begin_transaction();

try {
    // Eliminar el registro de la base de datos
    $stmt = $conn->prepare("DELETE FROM fotos_perfil WHERE id = ?");
    $stmt->bind_param("i", $foto['id']);
    $stmt->execute();
    
    // Verificar si se eliminó correctamente
    if ($stmt->affected_rows === 0) {
        throw new Exception("No se pudo eliminar el registro de la foto.");
    }
    
    // Confirmar la transacción
    $conn->commit();
    
    // Intentar eliminar el archivo físico si existe
    if (file_exists($rutaCompleta)) {
        if (!unlink($rutaCompleta)) {
            // Si no se puede eliminar el archivo físico, registrar el error pero no revertir la transacción
            error_log("No se pudo eliminar el archivo físico: " . $rutaCompleta);
        }
    }
    
    $_SESSION['mensaje'] = "Foto de perfil eliminada correctamente.";
} catch (Exception $e) {
    // Revertir la transacción en caso de error
    $conn->rollback();
    $_SESSION['error'] = "Error al eliminar la foto de perfil: " . $e->getMessage();
}

header("Location: ../perfil.php");
exit;
