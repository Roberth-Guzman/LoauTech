<?php
class Session {
    /**
     * Inicializa la sesión si no está ya iniciada
     */
    public static function init() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Establece una variable de sesión
     */
    public static function set($key, $value) {
        self::init();
        $_SESSION[$key] = $value;
    }

    /**
     * Obtiene una variable de sesión
     */
    public static function get($key, $default = null) {
        self::init();
        return $_SESSION[$key] ?? $default;
    }

    /**
     * Elimina una variable de sesión
     */
    public static function remove($key) {
        self::init();
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }

    /**
     * Destruye la sesión
     */
    public static function destroy() {
        self::init();
        session_destroy();
        $_SESSION = [];
    }

    /**
     * Verifica si una clave existe en la sesión
     */
    public static function exists($key) {
        self::init();
        return isset($_SESSION[$key]);
    }
}
