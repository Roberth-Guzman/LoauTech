<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: ../login.php");
    exit;
}

include '../../conexion.php';

$idUsuario = $_SESSION['usuario']['IDper'];

// Iniciar transacción para asegurar la integridad de los datos
$conn->begin_transaction();

try {
    // Obtener la información de la foto actual
    $sql = "SELECT id, ruta FROM fotos_perfil WHERE id_persona = ? AND es_actual = 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $idUsuario);
    $stmt->execute();
    $result = $stmt->get_result();
    $foto = $result->fetch_assoc();

    if ($foto) {
        $rutaCompleta = __DIR__ . '/../../' . $foto['ruta'];
        
        // Eliminar el archivo físico si existe
        if (file_exists($rutaCompleta) && is_file($rutaCompleta)) {
            unlink($rutaCompleta);
        }
        
        // Eliminar el registro de la base de datos
        $stmt = $conn->prepare("DELETE FROM fotos_perfil WHERE id = ?");
        $stmt->bind_param("i", $foto['id']);
        $stmt->execute();
        
        // Verificar si hay más fotos del usuario para marcar la más reciente como actual
        $sqlUltimaFoto = "SELECT id FROM fotos_perfil 
                         WHERE id_persona = ? 
                         ORDER BY id DESC 
                         LIMIT 1";
        $stmt = $conn->prepare($sqlUltimaFoto);
        $stmt->bind_param("i", $idUsuario);
        $stmt->execute();
        $result = $stmt->get_result();
        $ultimaFoto = $result->fetch_assoc();
        
        if ($ultimaFoto) {
            // Marcar la foto más reciente como actual
            $stmt = $conn->prepare("UPDATE fotos_perfil SET es_actual = 1 WHERE id = ?");
            $stmt->bind_param("i", $ultimaFoto['id']);
            $stmt->execute();
        }
        
        $conn->commit();
        $_SESSION['mensaje'] = "Foto de perfil eliminada correctamente.";
    } else {
        $conn->commit();
        $_SESSION['error'] = "No se encontró una foto de perfil para eliminar.";
    }
} catch (Exception $e) {
    $conn->rollback();
    $_SESSION['error'] = "Error al intentar eliminar la foto de perfil: " . $e->getMessage();
}

header("Location: ../perfil-porteria.php");
exit;
?>
