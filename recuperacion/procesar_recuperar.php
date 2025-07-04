<?php
include '../conexion.php';
require_once 'enviar_correo.php'; // Asegúrate de tener este archivo

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = trim($_POST['correo']);

    // Buscar el correo en la tabla contactos
    $stmt = $conn->prepare("SELECT IDperso FROM contactos WHERE correocont = ? AND estadocont = 'activo'");
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 1) {
        $fila = $resultado->fetch_assoc();
        $idPersona = $fila['IDperso'];

        // Buscar el número de documento en personas
        $stmt2 = $conn->prepare("SELECT numerodoc FROM personas WHERE IDper = ?");
        $stmt2->bind_param("i", $idPersona);
        $stmt2->execute();
        $res2 = $stmt2->get_result();
        $persona = $res2->fetch_assoc();

        if ($persona) {
            $numerodoc = $persona['numerodoc'];
            $token = bin2hex(random_bytes(32));
            $expira = date('Y-m-d H:i:s', strtotime('+1 hour'));

            // Guardar token y expiración en la tabla cuentas
            $stmt3 = $conn->prepare("UPDATE cuentas SET reset_token = ?, token_expira = ? WHERE numerodoc = ?");
            $stmt3->bind_param("ssi", $token, $expira, $numerodoc);
            $stmt3->execute();

            // Generar el enlace
            $enlace = "http://localhost/LoauTech-main/recuperacion/nueva_clave.php?token=$token";

            // Enviar el correo
            if (enviarCorreoRecuperacion($correo, $enlace)) {
                echo "<p>✅ Se ha enviado un enlace a tu correo. Revisa tu bandeja de entrada.</p>";
            } else {
                echo "<p>❌ Ocurrió un error al enviar el correo.</p>";
            }
        } else {
            echo "<p>Error: No se encontró documento asociado a este correo.</p>";
        }

    } else {
        // Mensaje genérico por seguridad
        echo "<p>✅ Si el correo está registrado, se ha enviado un enlace.</p>";
    }
}
?>
