<?php

class AdminController extends Controller
{
    private $adminModel;

    public function __construct()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // Centralizamos la seguridad aquí.
        // Si el usuario no tiene una sesión o su rol no es 'admin',
        // se le redirige fuera del panel de administración.
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            // Redirigir a la página de login.
            header('Location: ' . BASE_URL . '/login');
            exit();
        }

        // Si la seguridad pasa, cargamos el modelo.
        $this->adminModel = $this->model('Admin');
    }

    public function index()
    {
        // Ya no se necesita un chequeo de seguridad aquí.
        $estadisticas = $this->adminModel->obtenerEstadisticas();
        $ultimos_usuarios = $this->adminModel->obtenerUltimosUsuarios();
        $ultimos_elementos = $this->adminModel->obtenerUltimosElementos();

        $data = [
            'titulo' => 'Panel de Administración',
            'estadisticas' => $estadisticas,
            'ultimos_usuarios' => $ultimos_usuarios,
            'ultimos_elementos' => $ultimos_elementos,
            'nombre_admin' => $_SESSION['nombre_usuario'] ?? 'Admin'
        ];

        $this->view('panel-admin/panel-principal', $data);
    }

    public function usuarios($pagina = 1)
    {
        // Ya no se necesita un chequeo de seguridad aquí.
        $registros_por_pagina = 10;
        $pagina_actual = filter_var($pagina, FILTER_VALIDATE_INT) ? (int) $pagina : 1;

        if ($pagina_actual < 1) {
            $pagina_actual = 1;
        }

        $offset = ($pagina_actual - 1) * $registros_por_pagina;
        $total_usuarios = $this->adminModel->contarTotalUsuarios();
        $total_paginas = ceil($total_usuarios / $registros_por_pagina);
        $usuarios = $this->adminModel->obtenerTodosLosUsuarios($registros_por_pagina, $offset);

        $data = [
            'titulo' => 'Gestión de Usuarios',
            'usuarios' => $usuarios,
            'pagina_actual' => $pagina_actual,
            'total_paginas' => $total_paginas
        ];

        $this->view('panel-admin/usuarios', $data);
    }

    public function elementos($pagina = 1)
    {
        // Ya no se necesita un chequeo de seguridad aquí.
        $registros_por_pagina = 10;
        $pagina_actual = filter_var($pagina, FILTER_VALIDATE_INT) ? (int) $pagina : 1;

        if ($pagina_actual < 1) {
            $pagina_actual = 1;
        }

        $offset = ($pagina_actual - 1) * $registros_por_pagina;
        $total_elementos = $this->adminModel->contarTotalElementos();
        $total_paginas = ceil($total_elementos / $registros_por_pagina);
        $elementos = $this->adminModel->obtenerTodosLosElementos($registros_por_pagina, $offset);

        $data = [
            'titulo' => 'Gestión de Elementos',
            'elementos' => $elementos,
            'pagina_actual' => $pagina_actual,
            'total_paginas' => $total_paginas
        ];

        $this->view('panel-admin/elementos', $data);
    }

    public function almacenes()
    {
        // Ya no se necesita un chequeo de seguridad aquí.
        $pagina_actual = isset($_GET['pagina']) ? (int) $_GET['pagina'] : 1;
        $registros_por_pagina = 10;

        $total_autorizaciones = $this->adminModel->contarTotalAutorizaciones();
        $total_paginas = ceil($total_autorizaciones / $registros_por_pagina);
        $offset = ($pagina_actual - 1) * $registros_por_pagina;
        $autorizaciones = $this->adminModel->obtenerTodasLasAutorizaciones($registros_por_pagina, $offset);

        $data = [
            'titulo' => 'Gestión de Autorizaciones',
            'autorizaciones' => $autorizaciones,
            'pagina_actual' => $pagina_actual,
            'total_paginas' => $total_paginas
        ];

        $this->view('panel-admin/almacenes', $data);
    }

    public function gestionarAdmin()
    {
        $administradores = $this->adminModel->obtenerAdministradores();

        $data = [
            'titulo' => 'Gestionar Administradores',
            'administradores' => $administradores
        ];

        $this->view('panel-admin/gestionar-admin', $data);
    }

    public function roles_permisos()
    {
        // Ya no se necesita un chequeo de seguridad aquí.
        $data = [
            'titulo' => 'Gestión de Roles y Permisos'
        ];
        $this->view('panel-admin/roles-permisos', $data);
    }

    public function crearAdmin()
    {
        // La seguridad ya fue manejada en el constructor.
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $datos = [
                'nombre' => trim($_POST['nombre']),
                'tipo_documento' => trim($_POST['tipo_documento']),
                'numero_documento' => trim($_POST['numero_documento']),
                'email' => trim($_POST['email']),
                'telefono' => trim($_POST['telefono']),
                'direccion' => trim($_POST['direccion']),
                'rol' => trim($_POST['rol']),
                'password' => $_POST['password'],
                'confirm_password' => $_POST['confirm_password'],
                'error' => ''
            ];

            // Cambiada la validación para 'rol'
            if (empty($datos['nombre']) || empty($datos['tipo_documento']) || empty($datos['numero_documento']) || empty($datos['email']) || empty($datos['telefono']) || empty($datos['direccion']) || empty($datos['rol']) || empty($datos['password'])) {
                $datos['error'] = 'Todos los campos son obligatorios';
            } elseif (!filter_var($datos['email'], FILTER_VALIDATE_EMAIL)) {
                $datos['error'] = 'El formato del correo electrónico no es válido';
            } elseif ($this->adminModel->verificarDocumentoExistente($datos['numero_documento'])) {
                $datos['error'] = 'El número de documento ya está registrado';
            } elseif ($this->adminModel->verificarEmailExistente($datos['email'])) {
                $datos['error'] = 'El correo electrónico ya está registrado';
            } elseif (strlen($datos['password']) < 8) {
                $datos['error'] = 'La contraseña debe tener al menos 8 caracteres';
            } elseif ($datos['password'] !== $datos['confirm_password']) {
                $datos['error'] = 'Las contraseñas no coinciden';
            }

            if (empty($datos['error'])) {
                $datos['password'] = password_hash($datos['password'], PASSWORD_DEFAULT);

                if ($this->adminModel->crearUsuarioCompleto($datos)) {
                    header('Location: ' . BASE_URL . '/admin/usuarios?success=1');
                    exit();
                } else {
                    $datos['error'] = 'Ocurrió un error al crear el usuario. Por favor, inténtelo de nuevo.';
                }
            }

            $datos['roles'] = $this->adminModel->obtenerRoles();
            $datos['titulo'] = 'Crear Nuevo Usuario';
            $this->view('panel-admin/crear-admin', $datos);

        } else {
            $data = [
                'titulo' => 'Crear Nuevo Usuario',
                'roles' => $this->adminModel->obtenerRoles(),
                'nombre' => '',
                'tipo_documento' => '',
                'numero_documento' => '',
                'email' => '',
                'telefono' => '',
                'direccion' => '',
                'rol' => '',
                'error' => ''
            ];
            $this->view('panel-admin/crear-admin', $data);
        }
    }

    public function resetPassword()
    {
        $data = [
            'titulo' => 'Restablecer Contraseñas',
            'usuarios' => [],
            'error' => '',
            'success' => ''
        ];

        // Si se envía el formulario para restablecer
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $usuario_id = filter_input(INPUT_POST, 'usuario_id', FILTER_VALIDATE_INT);

            if ($usuario_id && $usuario_id > 0) {
                if ($this->adminModel->restablecerPasswordUsuario($usuario_id)) {
                    $data['success'] = 'Contraseña restablecida exitosamente. La nueva contraseña temporal es: <strong>temp123</strong>';
                } else {
                    $data['error'] = 'No se pudo actualizar la contraseña. Intente de nuevo.';
                }
            } else {
                $data['error'] = 'ID de usuario no válido.';
            }
        }

        // Cargar siempre la lista de usuarios para el formulario
        $data['usuarios'] = $this->adminModel->obtenerUsuariosParaReset();

        $this->view('panel-admin/reset-password', $data);
    }

    public function estadisticas()
    {
        // Cargar el modelo de Admin
        $adminModel = $this->model('Admin');

        // Obtener los datos del dashboard desde el modelo
        $data = $adminModel->getSuperAdminDashboardData();

        // Pasar los datos a la vista
        $this->view('panel-admin/estadisticas', $data);
    }
}