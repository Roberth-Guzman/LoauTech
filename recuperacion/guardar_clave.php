<?php
include '../conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'] ?? '';
    $nuevaClave = $_POST['clave'] ?? '';

    if (empty($token) || empty($nuevaClave)) {
        echo "Token o contraseña faltantes.";
        exit;
    }

    // Buscar cuenta con ese token
    $stmt = $conn->prepare("SELECT numerodoc, token_expira FROM cuentas WHERE reset_token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 1) {
        $fila = $resultado->fetch_assoc();

        // Validar fecha de expiración
        if (strtotime($fila['token_expira']) < time()) {
            echo "Este enlace ya expiró. Por favor, solicita uno nuevo.";
            exit;
        }

        $documento = $fila['numerodoc'];
        $claveEncriptada = password_hash($nuevaClave, PASSWORD_DEFAULT);

        // Actualizar contraseña y limpiar token
        $stmt2 = $conn->prepare("UPDATE cuentas SET contracue = ?, reset_token = NULL, token_expira = NULL WHERE numerodoc = ?");
        $stmt2->bind_param("si", $claveEncriptada, $documento);
        $stmt2->execute();

        echo "<p>¡Contraseña actualizada exitosamente!</p>";
        echo "<a href='../login.php'>Iniciar sesión</a>";
    } else {
        echo "Token inválido o ya utilizado.";
    }
}
?>
