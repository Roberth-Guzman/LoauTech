<?php

class CuentadanteController extends Controller
{
    private $peticionModel;
    private $userModel;

    public function __construct()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $this->peticionModel = $this->model('Peticion');
        $this->userModel = $this->model('User');

        // Redirige si el usuario no tiene el rol de cuentadante
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'cuentadante') {
            header('Location: ' . BASE_URL . '/login/acceso_no_autorizado');
            exit;
        }
    }

    // Método por defecto que carga el panel principal
    public function index()
    {
        $this->panelPrincipal();
    }

    // Carga el panel principal del cuentadante
    public function panelPrincipal()
    {
        // Configuración de paginación
        $pagina_actual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
        $registros_por_pagina = 10; // O el número que prefieras
        $offset = ($pagina_actual - 1) * $registros_por_pagina;

        // Obtener total de peticiones para el cuentadante
        $total_peticiones = $this->peticionModel->contarPeticionesParaCuentadante();
        $total_paginas = ceil($total_peticiones / $registros_por_pagina);

        // Obtener peticiones paginadas
        $peticiones = $this->peticionModel->obtenerPeticionesParaCuentadante($registros_por_pagina, $offset);

        // Obtener estadísticas
        $estadisticas = $this->peticionModel->obtenerEstadisticasCuentadante();

        // Preparar los datos para la vista
        $data = [
            'page_title' => 'Panel de Cuentadante',
            'active_menu' => 'peticiones',
            'peticiones' => $peticiones,
            'estadisticas' => $estadisticas,
            'paginacion' => [
                'total_paginas' => $total_paginas,
                'pagina_actual' => $pagina_actual
            ]
        ];

        // Cargar la vista del panel principal del cuentadante
        $this->view('panel-cuentadante/panel-principal', $data);
    }

    public function perfil()
    {
        // Asegurarse de que el usuario esté logueado
        if (!isset($_SESSION['user_id'])) {
            // Redirigir al login si no está logueado
            header('Location: ' . BASE_URL . '/login');
            exit();
        }

        // Obtener el ID del usuario de la sesión
        $userId = $_SESSION['user_id'];

        // Obtener los datos del perfil del usuario
        $usuario = $this->userModel->obtenerPerfilCompletoPorId($userId);

        // Preparar los datos para la vista
        $data = [
            'page_title' => 'Mi Perfil',
            'usuario' => $usuario
        ];

        // Cargar la vista de perfil con los datos
        $this->view('panel-cuentadante/perfil', $data);
    }
}