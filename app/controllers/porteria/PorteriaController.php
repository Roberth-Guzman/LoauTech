<?php

class PorteriaController extends Controller
{
    private $peticionModel;
    private $userModel;
    private $ingresoModel;

    public function __construct()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'porteria') {
            header('Location: ' . BASE_URL . '/login');
            exit();
        }

        $this->peticionModel = $this->model('Peticion');
        $this->userModel = $this->model('User');
        $this->ingresoModel = $this->model('Ingreso');
    }

    public function index()
    {
        $data = [
            'titulo' => 'Panel de Portería - LOAUTECH',
            'nombre_usuario' => $_SESSION['user_name'] ?? 'Usuario'
        ];
 
        $this->view('panel-porteria/panel-principal', $data);
    }

    public function registros()
    {
        $registros = $this->ingresoModel->obtenerTodosLosIngresos();

        $data = [
            'titulo' => 'Registros de Ingresos - LOAUTECH',
            'nombre_usuario' => $_SESSION['user_name'] ?? 'Usuario',
            'registros' => $registros
        ];

        $this->view('panel-porteria/registros', $data);
    }

    public function perfil()
    {
        $usuario = $this->userModel->obtenerPerfilUsuario($_SESSION['user_id']);

        $data = [
            'titulo' => 'Perfil de Usuario - LOAUTECH',
            'nombre_usuario' => $_SESSION['user_name'] ?? 'Usuario',
            'usuario' => $usuario
        ];

        $this->view('panel-porteria/perfil', $data);
    }

    public function peticiones($page = 1)
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/login');
            exit();
        }

        $paginaActual = filter_var($page, FILTER_VALIDATE_INT) ? (int)$page : 1;
        if ($paginaActual < 1) {
            $paginaActual = 1;
        }

        $peticionesPorPagina = 1;
        $offset = ($paginaActual - 1) * $peticionesPorPagina;

        $totalPeticiones = $this->peticionModel->contarPeticionesAprobadas();
        $totalPaginas = ceil($totalPeticiones / $peticionesPorPagina);

        $peticionesAprobadas = $this->peticionModel->obtenerPeticionesAprobadasPaginadas($peticionesPorPagina, $offset);
        
        $salidasHoy = $this->peticionModel->obtenerSalidasDelDia();

        $data = [
            'titulo' => 'Autorización de Salidas',
            'nombre_usuario' => $_SESSION['user_name'] ?? 'Usuario',
            'peticionesAprobadas' => $peticionesAprobadas,
            'salidasHoy' => $salidasHoy,
            'totalPeticionesAprobadas' => $totalPeticiones,
            'paginaActual' => $paginaActual,
            'totalPaginas' => $totalPaginas
        ];

        $this->view('panel-porteria/peticiones', $data);
    }

    public function registrarSalida()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            $datosSalida = [
                'id_prestamo' => trim($_POST['id_prestamo']),
                'fecha_salida' => trim($_POST['hora_salida'])
            ];

            if (empty($datosSalida['id_prestamo']) || empty($datosSalida['fecha_salida'])) {
                header('Location: ' . BASE_URL . '/porteria/peticiones?error=faltan_datos');
                exit();
            }
            
            if ($this->peticionModel->registrarSalidaElemento($datosSalida['id_prestamo'], $datosSalida['fecha_salida'])) {
                header('Location: ' . BASE_URL . '/porteria/peticiones?exito=salida_registrada');
                exit();
            } else {
                header('Location: ' . BASE_URL . '/porteria/peticiones?error=registro_fallido');
                exit();
            }

        } else {
            header('Location: ' . BASE_URL . '/porteria/peticiones');
            exit();
        }
    }
}