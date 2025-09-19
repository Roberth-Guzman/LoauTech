<?php
class LogoutController extends Controller {
    public function index() {

        // 1. Desvincular todas las variables de sesión
        $_SESSION = [];

        // 2. Si se desea destruir la sesión completamente, borra también la cookie de sesión.
        // Nota: ¡Esto destruirá la sesión, y no solo los datos de la sesión!
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        // 3. Finalmente, destruir la sesión.
        session_destroy();


        header('Location: ' . BASE_URL . '/home');
        exit();
    }
}