<?php
session_start();
include '../../conexion.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['rol'] != 'admin') {
    header("Location: ../../login.php");
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: consultar.php?error=ID inválido");
    exit;
}

$id = $_GET['id'];

// Iniciar transacción para eliminar todos los datos relacionados
$conn->begin_transaction();

try {
    // 1. Eliminar de contactos
    $conn->query("DELETE FROM contactos WHERE IDperso = $id");
    
    // 2. Eliminar de roles
    $conn->query("DELETE FROM roles WHERE idper = $id");
    
    // 3. Obtener número de documento para eliminar de cuentas
    $result = $conn->query("SELECT numerodoc FROM personas WHERE IDper = $id");
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $numerodoc = $row['numerodoc'];
        $conn->query("DELETE FROM cuentas WHERE numerodoc = '$numerodoc'");
    }
    
    // 4. Finalmente eliminar de personas
    $conn->query("DELETE FROM personas WHERE IDper = $id");
    
    $conn->commit();
    header("Location: consultar.php?success=Usuario eliminado correctamente");
} catch (Exception $e) {
    $conn->rollback();
    header("Location: consultar.php?error=Error al eliminar usuario: " . urlencode($e->getMessage()));
}

exit;