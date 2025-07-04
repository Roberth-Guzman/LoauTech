<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once '../vendor/autoload.php'; // Autoload de Composer

function enviarCorreoRecuperacion($para, $enlace) {
    $mail = new PHPMailer(true);

    try {
        // Configura tu servidor SMTP (Gmail en este ejemplo)
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'loautech.soporte@gmail.com';     
        $mail->Password   = 'aapdlcoilljxzqgb';    
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        // Remitente y destinatario
        $mail->setFrom('loautech.soporte@gmail.com', 'Loautech');
        $mail->addAddress($para);

        // Contenido
        $mail->isHTML(true);
        $mail->Subject = 'Recuperacion de contraseña - Loautech';
        $mail->Body    = "
            <h3>Hola</h3>
            <p>Haz clic en este enlace para cambiar tu contraseña:</p>
            <a href='$enlace'>$enlace</a>
            <p>Este enlace expirará en 1 hora.</p>
        ";

        $mail->send();
        return true;

    } catch (Exception $e) {
    echo "❌ Error PHPMailer: " . $mail->ErrorInfo;
    return false;
}

}
