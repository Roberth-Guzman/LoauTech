<?php
session_start();
require_once 'includes/MailerContacto.php';

// Configurar el correo de destino
$correo_destino = 'loautech.soporte@gmail.com';

// Verificar si se envió el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener y limpiar los datos del formulario
    $nombre = trim($_POST['nombre'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $mensaje = trim($_POST['mensaje'] ?? '');
    $privacidad = isset($_POST['privacidad']) ? true : false;
    
    // Validar los campos requeridos
    $errores = [];
    
    if (empty($nombre)) {
        $errores[] = "El nombre es requerido";
    }
    
    if (empty($email)) {
        $errores[] = "El correo electrónico es requerido";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errores[] = "El correo electrónico no es válido";
    }
    
    if (empty($mensaje)) {
        $errores[] = "El mensaje es requerido";
    }
    
    if (!$privacidad) {
        $errores[] = "Debes aceptar los términos y condiciones";
    }
    
    // Si no hay errores, enviar el correo
    if (empty($errores)) {
        try {
            // Crear instancia del mailer de contacto
            $mailer = new MailerContacto();
            
            // Enviar el correo usando el método específico
            $resultado = $mailer->enviarContacto($nombre, $email, $telefono, $mensaje);
            
            if ($resultado) {
                $_SESSION['mensaje_contacto'] = [
                    'tipo' => 'exito',
                    'texto' => '¡Mensaje enviado exitosamente! Nos pondremos en contacto contigo pronto.'
                ];
            } else {
                $_SESSION['mensaje_contacto'] = [
                    'tipo' => 'error',
                    'texto' => 'Error al enviar el mensaje. Por favor, intenta nuevamente.'
                ];
            }
            
        } catch (Exception $e) {
            $_SESSION['mensaje_contacto'] = [
                'tipo' => 'error',
                'texto' => 'Error en el sistema. Por favor, intenta más tarde.'
            ];
        }
    } else {
        $_SESSION['mensaje_contacto'] = [
            'tipo' => 'error',
            'texto' => 'Por favor, corrige los siguientes errores: ' . implode(', ', $errores)
        ];
    }
    
    // Redirigir de vuelta al formulario
    if (!headers_sent()) {
        header('Location: equipo.php#contacto');
        exit;
    } else {
        // Si ya se enviaron headers, usar JavaScript para redirigir
        echo '<script>window.location.href = "equipo.php#contacto";</script>';
        echo '<p>Redirigiendo... <a href="equipo.php#contacto">Haz clic aquí si no redirige automáticamente</a></p>';
        exit;
    }
} else {
    // Si no es POST, redirigir a la página principal
    if (!headers_sent()) {
        header('Location: equipo.php');
        exit;
    } else {
        echo '<script>window.location.href = "equipo.php";</script>';
        echo '<p>Redirigiendo... <a href="equipo.php">Haz clic aquí si no redirige automáticamente</a></p>';
        exit;
    }
}
?> 