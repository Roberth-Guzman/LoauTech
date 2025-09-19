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
        $cuentadante_id = $_SESSION['user_id'];

        // 1. Obtener las solicitudes que están pendientes de la aprobación del cuentadante
        $solicitudes_pendientes = $this->peticionModel->obtenerSolicitudesPendientesCuentadante($cuentadante_id);

        // 2. Obtener las estadísticas para las tarjetas
        $estadisticas = $this->peticionModel->obtenerEstadisticasCuentadante($cuentadante_id);

        // 3. Obtener el historial de peticiones ya procesadas por el cuentadante (para paginación)
        $total_peticiones_historial = $this->peticionModel->contarPeticionesHistorialCuentadante($cuentadante_id);
        $total_paginas = ceil($total_peticiones_historial / $registros_por_pagina);
        $peticiones_historial = $this->peticionModel->obtenerPeticionesHistorialCuentadante($cuentadante_id, $registros_por_pagina, $offset);

        // Preparar los datos para la vista
        $data = [
            'page_title' => 'Panel de Cuentadante',
            'active_menu' => 'peticiones',
            'solicitudes_pendientes' => $solicitudes_pendientes, // Para la nueva tabla de pendientes
            'peticiones' => $peticiones_historial, // Para la tabla de historial
            'stats' => $estadisticas, // Para las tarjetas de estadísticas
            'paginacion' => [
                'total_paginas' => $total_paginas,
                'pagina_actual' => $pagina_actual
            ]
        ];

        // Cargar la vista del panel principal del cuentadante
        $this->view('panel-cuentadante/panel-principal', $data);
    }

    public function solicitudes()
    {
        // Redirigir al panel principal, ya que ahora se gestiona todo allí.
        header('Location: ' . BASE_URL . '/cuentadante/panelPrincipal');
        exit;
    }

    public function procesarSolicitud()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $aprobacion_id = $_POST['id_aprobacion'];
            $accion = $_POST['accion'];

            if ($accion == 'aprobar') {
                if ($this->peticionModel->aprobarPeticionCuentadante($aprobacion_id)) {
                    // Éxito
                    header('Location: ' . BASE_URL . '/cuentadante/panelPrincipal?exito=aprobacion');
                } else {
                    // Error
                    header('Location: ' . BASE_URL . '/cuentadante/panelPrincipal?error=aprobacion');
                }
            } elseif ($accion == 'rechazar') {
                $motivo = $_POST['motivo'] ?? 'Rechazado por cuentadante';
                if ($this->peticionModel->rechazarPeticionCuentadante($aprobacion_id, $motivo)) {
                    // Éxito
                    header('Location: ' . BASE_URL . '/cuentadante/panelPrincipal?exito=rechazo');
                } else {
                    // Error
                    header('Location: ' . BASE_URL . '/cuentadante/panelPrincipal?error=rechazo');
                }
            }
            exit;
        } else {
            header('Location: ' . BASE_URL . '/cuentadante/panelPrincipal');
            exit;
        }
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