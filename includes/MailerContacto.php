<?php
require_once 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class MailerContacto {
    private $mail;
    private $logFile;

    public function __construct() {
        $this->mail = new PHPMailer(true);
        $this->logFile = __DIR__ . '/../logs/contacto_errors.log';
        $this->configurarSMTP();
    }

    private function configurarSMTP() {
        try {
            // Cargar configuración
            require_once __DIR__ . '/../config_contacto.php';
            
            $this->mail->isSMTP();
            $this->mail->Host = SMTP_HOST;
            $this->mail->Port = SMTP_PORT;
            $this->mail->SMTPAuth = true;
            $this->mail->Username = SMTP_USER;
            $this->mail->Password = SMTP_PASS;
            $this->mail->SMTPSecure = SMTP_SECURE;
            $this->mail->CharSet = 'UTF-8';
            $this->mail->SMTPDebug = SMTP_DEBUG;
            $this->mail->setFrom(FROM_EMAIL, FROM_NAME);
            $this->mail->isHTML(true);
            
            // Configuraciones adicionales para evitar errores SSL
            $this->mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );
        } catch (Exception $e) {
            $this->logError("Error al configurar SMTP: " . $e->getMessage());
            throw $e;
        }
    }

    public function enviarContacto($nombre, $email, $telefono, $mensaje) {
        try {
            // Limpiar direcciones previas
            $this->mail->clearAddresses();
            
            // Configurar destinatario
            $this->mail->addAddress(CONTACTO_EMAIL, 'Soporte Loautech');
            
            // Configurar asunto
            $asunto = "Nuevo mensaje de contacto - Loautech";
            
            // Crear el mensaje HTML
            $mensaje_html = $this->crearMensajeHTML($nombre, $email, $telefono, $mensaje);
            
            // Crear el mensaje de texto plano
            $mensaje_texto = $this->crearMensajeTexto($nombre, $email, $telefono, $mensaje);
            
            // Configurar asunto y mensaje
            $this->mail->Subject = $asunto;
            $this->mail->Body = $mensaje_html;
            $this->mail->AltBody = $mensaje_texto;
            
            // Enviar el correo
            $result = $this->mail->send();
            
            if (!$result) {
                throw new Exception($this->mail->ErrorInfo);
            }
            
            return true;
        } catch (Exception $e) {
            $this->logError("Error al enviar correo de contacto: " . $e->getMessage());
            return false;
        }
    }
    
    private function crearMensajeHTML($nombre, $email, $telefono, $mensaje) {
        return "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background-color: #007bff; color: white; padding: 20px; text-align: center; border-radius: 5px 5px 0 0; }
                .content { padding: 20px; background-color: #f8f9fa; border: 1px solid #dee2e6; }
                .field { margin-bottom: 15px; }
                .label { font-weight: bold; color: #007bff; }
                .value { margin-left: 10px; }
                .mensaje-box { margin-top: 10px; padding: 15px; background-color: white; border-left: 4px solid #007bff; border-radius: 3px; }
                .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; background-color: #e9ecef; border-radius: 0 0 5px 5px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h2>📧 Nuevo Mensaje de Contacto</h2>
                    <p><strong>Sistema Loautech</strong></p>
                </div>
                <div class='content'>
                    <div class='field'>
                        <span class='label'>👤 Nombre:</span>
                        <span class='value'>" . htmlspecialchars($nombre) . "</span>
                    </div>
                    <div class='field'>
                        <span class='label'>📧 Correo Electrónico:</span>
                        <span class='value'>" . htmlspecialchars($email) . "</span>
                    </div>
                    <div class='field'>
                        <span class='label'>📞 Teléfono:</span>
                        <span class='value'>" . htmlspecialchars($telefono) . "</span>
                    </div>
                    <div class='field'>
                        <span class='label'>💬 Mensaje:</span>
                        <div class='mensaje-box'>
                            " . nl2br(htmlspecialchars($mensaje)) . "
                        </div>
                    </div>
                </div>
                <div class='footer'>
                    <p>Este mensaje fue enviado desde el formulario de contacto de Loautech</p>
                    <p>📅 Fecha: " . date('d/m/Y H:i:s') . "</p>
                    <p>🌐 IP del remitente: " . $_SERVER['REMOTE_ADDR'] . "</p>
                </div>
            </div>
        </body>
        </html>";
    }
    
    private function crearMensajeTexto($nombre, $email, $telefono, $mensaje) {
        return "
NUEVO MENSAJE DE CONTACTO - LOAUTECH
=====================================

👤 Nombre: " . $nombre . "
📧 Correo Electrónico: " . $email . "
📞 Teléfono: " . $telefono . "

💬 Mensaje:
" . $mensaje . "

📅 Fecha: " . date('d/m/Y H:i:s') . "
🌐 IP del remitente: " . $_SERVER['REMOTE_ADDR'] . "

---
Este mensaje fue enviado desde el formulario de contacto de Loautech
        ";
    }
    
    private function logError($message) {
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[$timestamp] $message\n";
        
        // Asegurarse de que el directorio de logs existe
        $logDir = dirname($this->logFile);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
        
        // Escribir en el archivo de log
        error_log($logMessage, 3, $this->logFile);
    }
}
?> 