<?php
class LoginController extends Controller {
    private $userModel;

    public function __construct() {
        $this->userModel = $this->model('User');

        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function index() {
        // Si ya hay usuario logueado, redirigir (a menos que ya esté en login)
        if (isset($_SESSION['user_id'])) {
            $url_actual = $_SERVER['REQUEST_URI'];
            // Solo redirigir si NO estamos ya en la página de login
            if (strpos($url_actual, 'login') === false) {
                $this->redirigirSegunRol($_SESSION['user_role']);
                return;
            }
        }

        $data = [
            'error' => null,
            'titulo' => 'Login - Loautech'
        ];

        // Procesar login si se envió el formulario
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->procesarLogin();
            return;
        }

        $this->view('login/index', $data);
    }

    private function procesarLogin() {
        $documento = trim($_POST['numerodoc'] ?? '');
        $contrasena = $_POST['contrasena'] ?? '';

        $data = [
            'error' => null,
            'titulo' => 'Login - Loautech'
        ];

        if (empty($documento) || empty($contrasena)) {
            $data['error'] = "Por favor ingresa número de documento y contraseña.";
            $this->view('login/index', $data);
            return;
        }

        $usuario = $this->userModel->obtenerUsuarioActivoPorDocumento($documento);

        if (!$usuario || !password_verify($contrasena, $usuario->contracue)) {
            $data['error'] = "Documento o contraseña incorrectos.";
            $this->view('login/index', $data);
            return;
        }

        // Guardar datos del usuario en sesión 
        $_SESSION['user_id'] = $usuario->IDper;
        $_SESSION['rol_id'] = $usuario->rol_id; 
        $_SESSION['user_role'] = $usuario->rol; 
        $_SESSION['nombre'] = $usuario->nombrecompletoper; 
        $_SESSION['user_documento'] = $usuario->numerodoc;
        $_SESSION['user_telefono'] = $usuario->numerocont;

        // Redirigir según rol
        $this->redirigirSegunRol($usuario->rol);
    }

     private function redirigirSegunRol($rol) {
        $rutas = [
            'admin' => 'admin/index',
            'porteria' => 'porteria/porteria/panelPrincipal', 
            'usuario' => 'usuario/panelPrincipal',
            'cuentadante' => 'cuentadante/panelPrincipal',
            'almacenes' => 'almacen/almacen/panelPrincipal'
        ];

        $ruta = $rutas[$rol] ?? 'login';

        header('Location: ' . rtrim(BASE_URL, '/') . '/' . $ruta);
        exit();
    }
}