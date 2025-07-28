<?php
require 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mailer {
    private $mail;
    private $logFile;
    private $config;

    public function __construct() {
        $this->mail = new PHPMailer(true);
        $this->logFile = __DIR__ . '/../logs/email_errors.log';
        $this->cargarConfiguracion();
        $this->configurarSMTP();
    }

    private function cargarConfiguracion() {
        // Configuración por defecto (puede ser sobrescrita por la base de datos)
        $this->config = [
            'smtp_host' => 'smtp.gmail.com',
            'smtp_port' => 587,
            'smtp_secure' => 'tls',
            'smtp_user' => 'notificaciones@tudominio.com',
            'smtp_pass' => 'tu_contraseña_segura',
            'from_email' => 'notificaciones@tudominio.com',
            'from_name' => 'Sistema Loatech',
            'debug' => 0 // 0 = sin debug, 1 = mensajes del cliente, 2 = mensajes del cliente y servidor
        ];

        // Intenta cargar configuración desde la base de datos (si existe)
        $this->cargarConfiguracionBD();
    }

    private function cargarConfiguracionBD() {
        // Ejemplo de cómo cargar configuración desde la base de datos
        try {
            global $conn; // Asumiendo que tienes una conexión global
            
            // Verificar si la tabla de configuración existe
            $result = $conn->query("SHOW TABLES LIKE 'configuracion_correo'");
            if ($result && $result->num_rows > 0) {
                $sql = "SELECT * FROM configuracion_correo WHERE id = 1 LIMIT 1";
                $result = $conn->query($sql);
                
                if ($result && $result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $this->config = array_merge($this->config, [
                        'smtp_host' => $row['servidor_smtp'] ?? $this->config['smtp_host'],
                        'smtp_port' => $row['puerto'] ?? $this->config['smtp_port'],
                        'smtp_secure' => $row['cifrado'] ?? $this->config['smtp_secure'],
                        'smtp_user' => $row['usuario'] ?? $this->config['smtp_user'],
                        'smtp_pass' => $row['contrasena'] ?? $this->config['smtp_pass'],
                        'from_email' => $row['correo_remitente'] ?? $this->config['from_email'],
                        'from_name' => $row['nombre_remitente'] ?? $this->config['from_name']
                    ]);
                }
            }
        } catch (Exception $e) {
            $this->logError("Error al cargar configuración de BD: " . $e->getMessage());
        }
    }

    private function configurarSMTP() {
        try {
            $this->mail->isSMTP();
            $this->mail->Host = $this->config['smtp_host'];
            $this->mail->Port = $this->config['smtp_port'];
            $this->mail->SMTPAuth = true;
            $this->mail->Username = $this->config['smtp_user'];
            $this->mail->Password = $this->config['smtp_pass'];
            $this->mail->SMTPSecure = $this->config['smtp_secure'];
            $this->mail->CharSet = 'UTF-8';
            $this->mail->SMTPDebug = $this->config['debug'];
            $this->mail->setFrom($this->config['from_email'], $this->config['from_name']);
            $this->mail->isHTML(true);
        } catch (Exception $e) {
            $this->logError("Error al configurar SMTP: " . $e->getMessage());
            throw $e;
        }
    }

    public function send($to, $subject, $message, $altBody = '', $attachments = []) {
        try {
            // Limpiar direcciones y adjuntos previos
            $this->mail->clearAddresses();
            $this->mail->clearAttachments();
            
            // Configurar destinatario
            if (is_array($to)) {
                foreach ($to as $email => $name) {
                    $this->mail->addAddress($email, $name);
                }
            } else {
                $this->mail->addAddress($to);
            }
            
            // Configurar asunto y mensaje
            $this->mail->Subject = $subject;
            $this->mail->Body = $message;
            $this->mail->AltBody = !empty($altBody) ? $altBody : strip_tags($message);
            
            // Adjuntar archivos si los hay
            foreach ($attachments as $attachment) {
                if (file_exists($attachment['path'])) {
                    $this->mail->addAttachment(
                        $attachment['path'],
                        $attachment['name'] ?? basename($attachment['path'])
                    );
                }
            }
            
            // Enviar el correo
            $result = $this->mail->send();
            
            if (!$result) {
                throw new Exception($this->mail->ErrorInfo);
            }
            
            return true;
        } catch (Exception $e) {
            $this->logError("Error al enviar correo a $to: " . $e->getMessage());
            return false;
        }
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