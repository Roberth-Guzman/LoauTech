<?php
// Iniciar la sesión para poder usar variables $_SESSION
session_start();

// Cargar la configuración de la aplicación (constantes como BASE_URL, DB_HOST, etc.)
require_once 'app/config.php';

/**
 * Autoloader para las clases del Core y Controladores.
 */
spl_autoload_register(function ($className) {
    // Buscar en core
    $coreFile = __DIR__ . '/app/core/' . $className . '.php';
    if (file_exists($coreFile)) {
        require_once $coreFile;
        return;
    }
    
    // Buscar en controladores
    $controllerFile = __DIR__ . '/app/controllers/' . $className . '.php';
    if (file_exists($controllerFile)) {
        require_once $controllerFile;
        return;
    }
    
    // Buscar en modelos
    $modelFile = __DIR__ . '/app/models/' . $className . '.php';
    if (file_exists($modelFile)) {
        require_once $modelFile;
        return;
    }
});

// Iniciar la aplicación.
$app = new App();