<?php
session_start();
include '../../conexion.php';

// Verificar autenticación y permisos
if (!isset($_SESSION['user']) || $_SESSION['user']['rol'] != 'admin') {
    header("Location: ../../login.php");
    exit;
}

// Validar ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: consultar.php?error=ID inválido");
    exit;
}

$id = intval($_GET['id']);

// Verificar si el elemento existe antes de intentar eliminarlo
$check_query = "SELECT IDele FROM elementos WHERE IDele = ?";
$check_stmt = $conn->prepare($check_query);
$check_stmt->bind_param("i", $id);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows === 0) {
    header("Location: consultar.php?error=El elemento no existe o ya ha sido eliminado");
    exit;
}

// Intentar eliminar el elemento
$delete_query = "DELETE FROM elementos WHERE IDele = ?";
$stmt = $conn->prepare($delete_query);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        header("Location: consultar.php?success=Elemento eliminado correctamente");
    } else {
        header("Location: consultar.php?warning=No se realizaron cambios. El elemento podría no existir.");
    }
} else {
    // Manejar error de restricción de clave foránea u otro error de base de datos
    if ($conn->errno == 1451) {
        header("Location: consultar.php?error=No se puede eliminar el elemento porque tiene registros relacionados");
    } else {
        header("Location: consultar.php?error=Error al eliminar el elemento: " . urlencode($conn->error));
    }
}

exit;
?>