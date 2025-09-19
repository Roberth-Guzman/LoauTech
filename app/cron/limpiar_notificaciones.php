<?php
/**
 * Script para limpiar notificaciones antiguas del sistema
 * Debe ejecutarse periódicamente mediante un programador de tareas (cron)
 * Ejemplo de uso en cron: php /ruta/al/proyecto/app/cron/limpiar_notificaciones.php
 */

// Establecer el límite de tiempo de ejecución (en segundos)
set_time_limit(300); // 5 minutos

// Incluir el cargador de la aplicación
require_once __DIR__ . '/../config/loader.php';

// Registrar la función de autocarga de clases
spl_autoload_register(function($className) {
    $file = __DIR__ . '/../' . str_replace('\\', '/', $className) . '.php';
    if (file_exists($file)) {
        require $file;
    }
});

// Iniciar sesión si no está iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Establecer el ID de usuario del sistema (puede ser un usuario administrador o un usuario del sistema)
$_SESSION['user_id'] = 1; // Ajustar según corresponda
$_SESSION['user_role'] = 'admin'; // Asegurar permisos de administrador

try {
    // Registrar inicio del proceso
    error_log("Iniciando limpieza de notificaciones antiguas - " . date('Y-m-d H:i:s'));
    
    // Crear instancia del modelo
    $notificacionModel = new Notificacion();
    
    // Número de días a conservar (por defecto 30 días)
    $dias = 30;
    
    // Obtener el número de días desde el parámetro de la línea de comandos si está presente
    if (isset($argv[1]) && is_numeric($argv[1]) && $argv[1] > 0) {
        $dias = (int)$argv[1];
    }
    
    // Ejecutar la limpieza
    $eliminadas = $notificacionModel->limpiarNotificacionesAntiguas($dias);
    
    // Registrar resultado
    $mensaje = "Limpieza completada: Se eliminaron $eliminadas notificaciones con más de $dias días de antigüedad";
    error_log($mensaje);
    
    // Mostrar resultado (útil cuando se ejecuta manualmente)
    if (php_sapi_name() === 'cli') {
        echo $mensaje . "\n";
    }
    
    exit(0); // Éxito
    
} catch (Exception $e) {
    // Registrar error
    $mensajeError = "Error al limpiar notificaciones: " . $e->getMessage();
    error_log($mensajeError);
    
    // Mostrar error (útil cuando se ejecuta manualmente)
    if (php_sapi_name() === 'cli') {
        echo $mensajeError . "\n";
    }
    
    exit(1); // Error
}
