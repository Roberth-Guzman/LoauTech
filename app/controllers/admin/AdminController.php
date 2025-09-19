<?php

class AdminController extends Controller
{
    private $adminModel;
    private $userModel;

    public function __construct()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }


        // Si la seguridad pasa, cargamos el modelo.
        $this->adminModel = $this->model('Admin');
        $this->userModel = $this->model('User');
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

    public function estadisticas()
    {
        $data = $this->adminModel->obtenerEstadisticasAvanzadas();
        $data['titulo'] = 'Estadísticas Avanzadas';
        $this->view('panel-admin/estadisticas', $data);
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

    public function verUsuario($id)
    {
        $usuario = $this->adminModel->obtenerUsuarioPorId($id);
        if (!$usuario) {
            header('Location: ' . BASE_URL . '/admin/usuarios?error=no_encontrado');
            exit();
        }
        $data = [
            'titulo' => 'Detalle de Usuario',
            'usuario' => $usuario,
        ];
        $this->view('panel-admin/usuario-detalle', $data);
    }

    public function agregarUsuario()
    {
        $data = [
            'titulo' => 'Agregar Usuario',
            'roles' => $this->adminModel->obtenerRoles(),
            'usuario' => null,
            'error' => ''
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $datos = [
                'nombre' => trim($_POST['nombre'] ?? ''),
                'tipo_documento' => trim($_POST['tipo_documento'] ?? ''),
                'numero_documento' => trim($_POST['numero_documento'] ?? ''),
                'email' => trim($_POST['email'] ?? ''),
                'telefono' => trim($_POST['telefono'] ?? ''),
                'direccion' => trim($_POST['direccion'] ?? ''),
                'rol' => trim($_POST['rol'] ?? 'usuario'),
                'password' => $_POST['password'] ?? ''
            ];

            // Validación simple
            if (empty($datos['nombre']) || empty($datos['email']) || empty($datos['password'])) {
                $data['error'] = 'Los campos nombre, email y contraseña son obligatorios';
            } else {
                // Hashear la contraseña
                $datos['password'] = password_hash($datos['password'], PASSWORD_DEFAULT);

                if ($this->adminModel->crearUsuarioCompleto($datos)) {
                    header('Location: ' . BASE_URL . '/admin/usuarios?success=creado');
                    exit();
                } else {
                    $data['error'] = 'Error al crear el usuario';
                }
            }

            // Pasar los datos del formulario de vuelta
            $data = array_merge($data, $datos);
        }

        $this->view('panel-admin/usuario-form', $data);
    }

    public function editarUsuario($id = null)
    {
        if (!$id) {
            header('Location: ' . BASE_URL . '/admin/usuarios?error=id_no_valido');
            exit();
        }

        $usuario = $this->adminModel->obtenerUsuarioPorId($id);
        if (!$usuario) {
            header('Location: ' . BASE_URL . '/admin/usuarios?error=no_encontrado');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $datos = [
                'id' => (int) $id,
                'nombre' => trim($_POST['nombre'] ?? ''),
                'tipo_documento' => trim($_POST['tipo_documento'] ?? ''),
                'numero_documento' => trim($_POST['numero_documento'] ?? ''),
                'email' => trim($_POST['email'] ?? ''),
                'telefono' => trim($_POST['telefono'] ?? ''),
                'direccion' => trim($_POST['direccion'] ?? ''),
                'rol' => trim($_POST['rol'] ?? 'usuario'),
            ];
            $password = !empty($_POST['password']) ? trim($_POST['password']) : null;
            $error = '';

            // Validaciones básicas
            if (
                empty($datos['nombre']) || empty($datos['tipo_documento']) || empty($datos['numero_documento']) ||
                empty($datos['email']) || empty($datos['telefono']) || empty($datos['direccion'])
            ) {
                $error = 'Todos los campos son obligatorios, excepto la contraseña.';
            } elseif (!filter_var($datos['email'], FILTER_VALIDATE_EMAIL)) {
                $error = 'El formato del correo electrónico no es válido';
            } elseif ($password !== null && strlen($password) < 6) {
                $error = 'La nueva contraseña debe tener al menos 6 caracteres.';
            } else {
                // Verificar si el correo ya existe en otro usuario
                $usuarioExistente = $this->adminModel->obtenerUsuarioPorEmail($datos['email']);
                if ($usuarioExistente && $usuarioExistente->IDper != $id) {
                    $error = 'El correo electrónico ya está en uso por otro usuario';
                }

                // Verificar si el número de documento ya existe en otro usuario
                if (empty($error)) {
                    $docExistente = $this->adminModel->obtenerUsuarioPorDocumento($datos['numero_documento']);
                    if ($docExistente && $docExistente->IDper != $id) {
                        $error = 'El número de documento ya está en uso por otro usuario';
                    }
                }
            }

            if (empty($error)) {
                // *** INICIO DE LA CORRECCIÓN ***
                // Se crea un array específico para el modelo, mapeando los campos correctamente.
                $datos_para_actualizar = [
                    'id' => $datos['id'],
                    'nombre' => $datos['nombre'],
                    'tipo_documento' => $datos['tipo_documento'],
                    'numerodoc' => $datos['numero_documento'], // Aquí está la corrección
                    'email' => $datos['email'],
                    'telefono' => $datos['telefono'],
                    'direccion' => $datos['direccion'],
                    'rol' => $datos['rol']
                ];

                // Añadir contraseña al array de datos solo si se proporcionó una nueva
                if ($password !== null) {
                    $datos_para_actualizar['password'] = $password;
                }

                if ($this->adminModel->actualizarUsuario($datos_para_actualizar)) {
                    header('Location: ' . BASE_URL . '/admin/usuarios?success=actualizado');
                    exit();
                } else {
                    $error = 'Ocurrió un error al actualizar el usuario. Por favor, intente de nuevo.';
                }
            }

            // Si hay error, mostrar el formulario con los datos ingresados
            $data = array_merge($datos, [
                'titulo' => 'Editar Usuario',
                'roles' => $this->adminModel->obtenerRoles(),
                'usuario' => (object) $datos, // Para que la vista sepa que es una edición
                'error' => $error
            ]);
            $this->view('panel-admin/usuario-form', $data);
            return;
        }

        // GET: Mostrar formulario con datos actuales
        $data = [
            'titulo' => 'Editar Usuario',
            'roles' => $this->adminModel->obtenerRoles(),
            'usuario' => $usuario,
            'nombre' => $usuario->nombrecompletoper,
            'tipo_documento' => $usuario->tipodocumento,
            'numero_documento' => $usuario->numerodoc,
            'email' => $usuario->correocont,
            'telefono' => $usuario->numerocont,
            'direccion' => $usuario->direccioncont,
            'rol' => $usuario->rol ?? 'usuario',
        ];
        $this->view('panel-admin/usuario-form', $data);
    }


    public function eliminarUsuario($id)
    {
        if ($this->adminModel->eliminarUsuarioPorId((int) $id)) {
            header('Location: ' . BASE_URL . '/admin/usuarios?success=eliminado');
        } else {
            header('Location: ' . BASE_URL . '/admin/usuarios?error=no_eliminado');
        }
        exit();
    }

    public function resetPassword()
    {
        $data = [
            'titulo' => 'Restablecer Contraseña de Usuario'
        ];
        $this->view('panel-admin/reset-password', $data);
    }

    public function buscarUsuarios()
    {
        if (isset($_GET['term'])) {
            $termino = $_GET['term'];
            $usuarios = $this->userModel->buscarUsuariosPorTermino($termino);
            header('Content-Type: application/json');
            echo json_encode($usuarios);
        }
    }

    public function forceResetPassword()
    {
        header('Content-Type: application/json');
        $response = ['success' => false, 'message' => 'Petición inválida.'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $idCuenta = $_POST['usuario_id'] ?? null;
            $nuevaPassword = $_POST['new_password'] ?? '';
            $confirmarPassword = $_POST['confirm_password'] ?? '';

            if (empty($idCuenta) || empty($nuevaPassword) || empty($confirmarPassword)) {
                $response['message'] = 'Todos los campos son obligatorios.';
            } elseif ($nuevaPassword !== $confirmarPassword) {
                $response['message'] = 'Las contraseñas no coinciden.';
            } elseif (strlen($nuevaPassword) < 8 || !preg_match('/[A-Z]/', $nuevaPassword) || !preg_match('/[a-z]/', $nuevaPassword) || !preg_match('/[0-9]/', $nuevaPassword)) {
                $response['message'] = 'La contraseña debe tener al menos 8 caracteres, una mayúscula, una minúscula y un número.';
            } else {
                if ($this->userModel->actualizarContrasenaPorId($idCuenta, $nuevaPassword)) {
                    $response['success'] = true;
                    $response['message'] = 'Contraseña actualizada correctamente.';
                } else {
                    $response['message'] = 'No se pudo actualizar la contraseña.';
                }
            }
        }

        echo json_encode($response);
        exit();
    }

    private function redirectConMensaje($vista, $tipo, $mensaje)
    {
        $_SESSION[$tipo] = $mensaje;
        header('Location: ' . BASE_URL . '/admin/' . $vista);
        exit();
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

    public function agregarElemento()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Procesar el formulario
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            // Manejar la carga de imagen si existe
            $imagen = null;
            if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
                $target_dir = PUBLIC_PATH . "/img/elementos/";
                if (!file_exists($target_dir)) {
                    mkdir($target_dir, 0777, true);
                }

                $extension = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
                $filename = uniqid() . '.' . $extension;
                $target_file = $target_dir . $filename;

                if (move_uploaded_file($_FILES['imagen']['tmp_name'], $target_file)) {
                    $imagen = $filename;
                }
            }

            $data = [
                'nombreele' => trim($_POST['nombreele']),
                'cantidadele' => trim($_POST['cantidadele']),
                'codigoele' => trim($_POST['codigoele']),
                'descripcionele' => trim($_POST['descripcionele']),
                'caracteristicasele' => trim($_POST['caracteristicasele']),
                'estado' => trim($_POST['estado']),
                'estadoelemento' => trim($_POST['estadoelemento']),
                'codigoinventario' => trim($_POST['codigoinventario']),
                'imagen' => $imagen
            ];

            // Crear elemento usando el modelo
            $elementoModel = $this->model('Elemento');
            if ($elementoModel->crearElemento($data)) {
                $_SESSION['mensaje'] = 'Elemento agregado correctamente';
                $_SESSION['tipo_mensaje'] = 'success';
                header('Location: ' . BASE_URL . '/admin/elementos');
                exit();
            } else {
                $_SESSION['mensaje'] = 'Error al agregar el elemento';
                $_SESSION['tipo_mensaje'] = 'danger';
                $this->view('panel-admin/agregar-elemento', $data);
            }
        } else {
            $data = [
                'titulo' => 'Agregar Elemento',
                'nombreele' => '',
                'cantidadele' => '',
                'codigoele' => '',
                'descripcionele' => '',
                'caracteristicasele' => '',
                'estado' => 'activo',
                'estadoelemento' => 'disponible',
                'codigoinventario' => '',
                'imagen' => null
            ];

            $this->view('panel-admin/agregar-elemento', $data);
        }
    }

    public function editarElemento($id = null)
    {
        $elementoModel = $this->model('Elemento');

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Procesar el formulario
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            // Manejar la carga de imagen si existe
            $elemento = $elementoModel->obtenerElementoPorId($_POST['id']);
            $imagen = $elemento->imagen;

            if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
                $target_dir = PUBLIC_PATH . "/img/elementos/";
                if (!file_exists($target_dir)) {
                    mkdir($target_dir, 0777, true);
                }

                $extension = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
                $filename = uniqid() . '.' . $extension;
                $target_file = $target_dir . $filename;

                if (move_uploaded_file($_FILES['imagen']['tmp_name'], $target_file)) {
                    // Eliminar imagen anterior si existe
                    if ($elemento->imagen && file_exists(PUBLIC_PATH . "/img/elementos/" . $elemento->imagen)) {
                        unlink(PUBLIC_PATH . "/img/elementos/" . $elemento->imagen);
                    }
                    $imagen = $filename;
                }
            }

            $data = [
                'id' => $_POST['id'],
                'nombreele' => trim($_POST['nombreele']),
                'cantidadele' => trim($_POST['cantidadele']),
                'codigoele' => trim($_POST['codigoele']),
                'descripcionele' => trim($_POST['descripcionele']),
                'caracteristicasele' => trim($_POST['caracteristicasele']),
                'estado' => trim($_POST['estado']),
                'estadoelemento' => trim($_POST['estadoelemento']),
                'codigoinventario' => trim($_POST['codigoinventario']),
                'imagen' => $imagen
            ];

            // Actualizar elemento usando el modelo
            if ($elementoModel->actualizarElemento($data)) {
                $_SESSION['mensaje'] = 'Elemento actualizado correctamente';
                $_SESSION['tipo_mensaje'] = 'success';
                header('Location: ' . BASE_URL . '/admin/elementos');
                exit();
            } else {
                $_SESSION['mensaje'] = 'Error al actualizar el elemento';
                $_SESSION['tipo_mensaje'] = 'danger';
                $this->view('panel-admin/editar-elemento', $data);
            }
        } else {
            // Verificar si existe el ID
            if (!$id) {
                header('Location: ' . BASE_URL . '/admin/elementos');
                exit();
            }

            // Obtener elemento por ID
            $elemento = $elementoModel->obtenerElementoPorId($id);

            // Verificar si existe el elemento
            if (!$elemento) {
                header('Location: ' . BASE_URL . '/admin/elementos');
                exit();
            }

            $data = [
                'titulo' => 'Editar Elemento',
                'id' => $elemento->IDele,
                'nombreele' => $elemento->nombreele,
                'cantidadele' => $elemento->cantidadele,
                'codigoele' => $elemento->codigoele,
                'descripcionele' => $elemento->descripcionele,
                'caracteristicasele' => $elemento->caracteristicasele,
                'estado' => $elemento->estado,
                'estadoelemento' => $elemento->estadoelemento,
                'codigoinventario' => $elemento->codigoinventario,
                'imagen' => $elemento->imagen
            ];

            $this->view('panel-admin/editar-elemento', $data);
        }
    }

    public function verElemento($id = null)
    {
        // Verificar si existe el ID
        if (!$id) {
            header('Location: ' . BASE_URL . '/admin/elementos');
            exit();
        }

        $elementoModel = $this->model('Elemento');
        $elemento = $elementoModel->obtenerElementoPorId($id);

        // Verificar si existe el elemento
        if (!$elemento) {
            header('Location: ' . BASE_URL . '/admin/elementos');
            exit();
        }

        $data = [
            'titulo' => 'Detalles del Elemento',
            'elemento' => $elemento
        ];

        $this->view('panel-admin/ver-elemento', $data);
    }

    public function eliminarElemento($id = null)
    {
        // Verificar si se proporcionó un ID
        if (!$id) {
            $_SESSION['mensaje'] = 'ID de elemento no válido.';
            $_SESSION['tipo_mensaje'] = 'danger';
            header('Location: ' . BASE_URL . '/admin/elementos');
            exit();
        }

        $elementoModel = $this->model('Elemento');

        if ($elementoModel->eliminarElemento($id)) {
            $_SESSION['mensaje'] = 'Elemento desactivado correctamente.';
            $_SESSION['tipo_mensaje'] = 'success';
        } else {
            $_SESSION['mensaje'] = 'Error al desactivar el elemento.';
            $_SESSION['tipo_mensaje'] = 'danger';
        }

        header('Location: ' . BASE_URL . '/admin/elementos');
        exit();
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

    public function verAutorizacion($id = null)
    {
        if (!$id) {
            $_SESSION['mensaje'] = 'ID de autorización no especificado';
            $_SESSION['tipo_mensaje'] = 'danger';
            header('Location: ' . BASE_URL . '/admin/almacenes');
            exit();
        }

        $autorizacion = $this->adminModel->obtenerAutorizacionPorId($id);

        if (!$autorizacion) {
            $_SESSION['mensaje'] = 'Autorización no encontrada';
            $_SESSION['tipo_mensaje'] = 'danger';
            header('Location: ' . BASE_URL . '/admin/almacenes');
            exit();
        }

        $data = [
            'titulo' => 'Ver Autorización',
            'autorizacion' => $autorizacion
        ];

        $this->view('panel-admin/ver-autorizacion', $data);
    }

    public function editarAutorizacion($id = null)
    {
        if (!$id) {
            $_SESSION['mensaje'] = 'ID de autorización no especificado';
            $_SESSION['tipo_mensaje'] = 'danger';
            header('Location: ' . BASE_URL . '/admin/almacenes');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            $data = [
                'id' => $id,
                'VoBoCuentadanteaut' => trim($_POST['VoBoCuentadanteaut']),
                'nomquienaturiza' => trim($_POST['nomquienaturiza']),
                'cargoquienautoriza' => trim($_POST['cargoquienautoriza']),
                'firmaquienautoriza' => trim($_POST['firmaquienautoriza']),
                'estadoaut' => trim($_POST['estadoaut'])
            ];

            if ($this->adminModel->actualizarAutorizacion($data)) {
                $_SESSION['mensaje'] = 'Autorización actualizada correctamente';
                $_SESSION['tipo_mensaje'] = 'success';
                header('Location: ' . BASE_URL . '/admin/almacenes');
                exit();
            } else {
                $_SESSION['mensaje'] = 'Error al actualizar la autorización';
                $_SESSION['tipo_mensaje'] = 'danger';
                $this->view('panel-admin/editar-autorizacion', $data);
            }
        } else {
            $autorizacion = $this->adminModel->obtenerAutorizacionPorId($id);

            if (!$autorizacion) {
                $_SESSION['mensaje'] = 'Autorización no encontrada';
                $_SESSION['tipo_mensaje'] = 'danger';
                header('Location: ' . BASE_URL . '/admin/almacenes');
                exit();
            }

            $data = [
                'titulo' => 'Editar Autorización',
                'id' => $autorizacion->IDaut,
                'VoBoCuentadanteaut' => $autorizacion->VoBoCuentadanteaut,
                'nomquienaturiza' => $autorizacion->nomquienaturiza,
                'cargoquienautoriza' => $autorizacion->cargoquienautoriza,
                'firmaquienautoriza' => $autorizacion->firmaquienautoriza,
                'estadoaut' => $autorizacion->estadoaut
            ];

            $this->view('panel-admin/editar-autorizacion', $data);
        }
    }

    public function eliminarAutorizacion($id = null)
    {
        if (!$id) {
            $_SESSION['mensaje'] = 'ID de autorización no especificado';
            $_SESSION['tipo_mensaje'] = 'danger';
            header('Location: ' . BASE_URL . '/admin/almacenes');
            exit();
        }

        // Verificar si la autorización existe antes de intentar eliminarla
        $autorizacion = $this->adminModel->obtenerAutorizacionPorId($id);
        if (!$autorizacion) {
            $_SESSION['mensaje'] = 'La autorización no existe';
            $_SESSION['tipo_mensaje'] = 'danger';
            header('Location: ' . BASE_URL . '/admin/almacenes');
            exit();
        }

        // Verificar si la autorización está siendo utilizada
        if ($this->adminModel->autorizacionEnUso($id)) {
            $_SESSION['mensaje'] = 'No se puede eliminar la autorización porque está siendo utilizada en otros registros (préstamos, marcaciones o vigilantes)';
            $_SESSION['tipo_mensaje'] = 'warning';
            header('Location: ' . BASE_URL . '/admin/almacenes');
            exit();
        }

        // Intentar eliminar y guardar el resultado
        $resultado = $this->adminModel->eliminarAutorizacion($id);

        if ($resultado) {
            $_SESSION['mensaje'] = 'Autorización eliminada correctamente';
            $_SESSION['tipo_mensaje'] = 'success';
        } else {
            $_SESSION['mensaje'] = 'No se pudo eliminar la autorización. Es posible que esté siendo utilizada en otros registros.';
            $_SESSION['tipo_mensaje'] = 'danger';
        }

        header('Location: ' . BASE_URL . '/admin/almacenes');
        exit();
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

    public function verAdmin($id)
    {
        $admin = $this->adminModel->obtenerUsuarioPorId($id);
        if (!$admin) {
            $_SESSION['mensaje'] = 'Administrador no encontrado';
            $_SESSION['tipo_mensaje'] = 'danger';
            header('Location: ' . BASE_URL . '/admin/gestionarAdmin');
            exit();
        }

        $data = [
            'titulo' => 'Detalle del Administrador',
            'admin' => $admin
        ];

        $this->view('panel-admin/ver-admin', $data);
    }

    public function editarAdmin($id = null)
    {
        if (!$id) {
            $_SESSION['mensaje'] = 'ID de administrador no válido';
            $_SESSION['tipo_mensaje'] = 'danger';
            header('Location: ' . BASE_URL . '/admin/gestionarAdmin');
            exit();
        }

        $admin = $this->adminModel->obtenerUsuarioPorId($id);
        if (!$admin) {
            $_SESSION['mensaje'] = 'Administrador no encontrado';
            $_SESSION['tipo_mensaje'] = 'danger';
            header('Location: ' . BASE_URL . '/admin/gestionarAdmin');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $datos = [
                'id' => (int) $id,
                'nombre' => trim($_POST['nombre'] ?? ''),
                'tipo_documento' => trim($_POST['tipo_documento'] ?? ''),
                'numero_documento' => trim($_POST['numero_documento'] ?? ''),
                'email' => trim($_POST['email'] ?? ''),
                'telefono' => trim($_POST['telefono'] ?? ''),
                'direccion' => trim($_POST['direccion'] ?? ''),
                'rol' => trim($_POST['rol'] ?? 'admin'),
                'password' => !empty($_POST['password']) ? $_POST['password'] : null,
            ];

            // Validaciones básicas
            if (
                empty($datos['nombre']) || empty($datos['tipo_documento']) || empty($datos['numero_documento']) ||
                empty($datos['email']) || empty($datos['telefono']) || empty($datos['direccion'])
            ) {
                $_SESSION['mensaje'] = 'Todos los campos son obligatorios';
                $_SESSION['tipo_mensaje'] = 'danger';
            } elseif (!filter_var($datos['email'], FILTER_VALIDATE_EMAIL)) {
                $_SESSION['mensaje'] = 'El email no es válido';
                $_SESSION['tipo_mensaje'] = 'danger';
            } else {
                // Verificar si el documento ya existe (excluyendo el usuario actual)
                if ($this->adminModel->verificarDocumentoExistente($datos['numero_documento'], $datos['id'])) {
                    $_SESSION['mensaje'] = 'El número de documento ya está registrado';
                    $_SESSION['tipo_mensaje'] = 'danger';
                } elseif ($this->adminModel->verificarEmailExistente($datos['email'], $datos['id'])) {
                    $_SESSION['mensaje'] = 'El email ya está registrado';
                    $_SESSION['tipo_mensaje'] = 'danger';
                } else {
                    // Si se proporciona nueva contraseña, hashearla
                    if ($datos['password']) {
                        $datos['password'] = password_hash($datos['password'], PASSWORD_DEFAULT);
                    }

                    if ($this->adminModel->actualizarUsuario($datos)) {
                        $_SESSION['mensaje'] = 'Administrador actualizado correctamente';
                        $_SESSION['tipo_mensaje'] = 'success';
                        header('Location: ' . BASE_URL . '/admin/gestionarAdmin');
                        exit();
                    } else {
                        $_SESSION['mensaje'] = 'Error al actualizar el administrador';
                        $_SESSION['tipo_mensaje'] = 'danger';
                    }
                }
            }
        }

        // Mostrar formulario con datos actuales
        $data = [
            'titulo' => 'Editar Administrador',
            'roles' => $this->adminModel->obtenerRoles(),
            'admin' => $admin,
            'nombre' => $admin->nombrecompletoper,
            'tipo_documento' => $admin->tipodocumento,
            'numero_documento' => $admin->numerodoc,
            'email' => $admin->correocont,
            'telefono' => $admin->numerocont,
            'direccion' => $admin->direccioncont,
            'rol' => $admin->rol ?? 'admin',
        ];

        $this->view('panel-admin/editar-admin', $data);
    }

    public function eliminarAdmin($id)
    {
        if (!$id) {
            $_SESSION['mensaje'] = 'ID de administrador no válido';
            $_SESSION['tipo_mensaje'] = 'danger';
            header('Location: ' . BASE_URL . '/admin/gestionarAdmin');
            exit();
        }

        // Verificar que no se esté eliminando a sí mismo
        if ($id == $_SESSION['user_id']) {
            $_SESSION['mensaje'] = 'No puedes eliminar tu propia cuenta';
            $_SESSION['tipo_mensaje'] = 'danger';
            header('Location: ' . BASE_URL . '/admin/gestionarAdmin');
            exit();
        }

        if ($this->adminModel->eliminarUsuarioPorId((int) $id)) {
            $_SESSION['mensaje'] = 'Administrador eliminado correctamente';
            $_SESSION['tipo_mensaje'] = 'success';
        } else {
            $_SESSION['mensaje'] = 'Error al eliminar el administrador';
            $_SESSION['tipo_mensaje'] = 'danger';
        }

        header('Location: ' . BASE_URL . '/admin/gestionarAdmin');
        exit();
    }

    public function roles_permisos()
    {
        // Ya no se necesita un chequeo de seguridad aquí.
        $data = [
            'titulo' => 'Gestión de Roles y Permisos'
        ];
        $this->view('panel-admin/roles-permisos', $data);
    }
    public function perfil()
    {
        // Asegurarse de que la sesión está iniciada
        Session::init();
        // Validar que el usuario esté logueado y tenga el rol de 'admin'
        if (!Session::isLoggedIn() || Session::getUserRole() !== 'admin') {
            $this->redirect('login');
        }

        // Obtener el ID de usuario de la sesión
        $userId = Session::get('user_id');
        // Cargar los datos del usuario usando el modelo
       $user = $this->userModel->findUserById($userId);

        // Preparar los datos para la vista
        $data = [
            'titulo' => 'Perfil de Administrador',
            'usuario' => $user
        ];

        // Cargar la vista del perfil
        $this->view('panel-admin/perfil', $data);
    }
 public function editarPerfil() {
        Session::init();
        if (!Session::isLoggedIn() || Session::getUserRole() !== 'admin') {
            $this->redirect('login');
        }

        $userId = Session::get('user_id');

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Procesa el formulario de actualización
            $datos = [
                'id' => $userId,
                'nombre' => trim($_POST['nombre']),
                'email' => trim($_POST['email']),
                'telefono' => trim($_POST['telefono']),
                'avatar' => $_FILES['avatar']
            ];

            // Sanitizar datos para seguridad
            $datos['nombre'] = filter_var($datos['nombre'], FILTER_SANITIZE_STRING);
            $datos['email'] = filter_var($datos['email'], FILTER_SANITIZE_EMAIL);
            $datos['telefono'] = filter_var($datos['telefono'], FILTER_SANITIZE_STRING);

            // Llamar al método del modelo para actualizar
            if ($this->userModel->actualizarPerfil($datos)) {
                Session::set('mensaje', 'Perfil actualizado correctamente.');
                Session::set('tipo_mensaje', 'success');
                $this->redirect('admin/perfil');
            } else {
                Session::set('mensaje', 'Error al actualizar el perfil.');
                Session::set('tipo_mensaje', 'danger');
                $this->redirect('admin/editarPerfil');
            }
        } else {
            // Muestra el formulario con los datos actuales
            $user = $this->userModel->findUserById($userId); // <-- CORRECCIÓN APLICADA AQUÍ
            
            if (!$user) {
                Session::set('mensaje', 'Usuario no encontrado.');
                Session::set('tipo_mensaje', 'danger');
                $this->redirect('admin/perfil');
            }

            $data = [
                'titulo' => 'Editar Perfil',
                'usuario' => $user
            ];
            $this->view('panel-admin/editar-perfil', $data);
        }
    }

    public function crearAdmin()
    {
        $data = [
            'titulo' => 'Crear Nuevo Administrador',
            'error' => '',
            'nombre' => '',
            'tipo_documento' => '',
            'numero_documento' => '',
            'email' => '',
            'telefono' => '',
            'direccion' => ''
        ];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Sanitizar y procesar los datos del formulario
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            $data = [
                'titulo' => 'Crear Nuevo Administrador',
                'error' => '',
                'nombre' => trim($_POST['nombre']),
                'tipo_documento' => trim($_POST['tipo_documento']),
                'numero_documento' => trim($_POST['numero_documento']),
                'email' => trim($_POST['email']),
                'telefono' => trim($_POST['telefono']),
                'direccion' => trim($_POST['direccion']),
                'password' => $_POST['password'],
                'confirm_password' => $_POST['confirm_password'],
                'rol' => 'admin' // Rol asignado automáticamente
            ];

            // --- Validación ---
            if (empty($data['nombre']) || empty($data['numero_documento']) || empty($data['email']) || empty($data['telefono']) || empty($data['password'])) {
                $data['error'] = 'Por favor, complete todos los campos obligatorios.';
            } elseif ($data['password'] !== $data['confirm_password']) {
                $data['error'] = 'Las contraseñas no coinciden.';
            } elseif (strlen($data['password']) < 8) {
                $data['error'] = 'La contraseña debe tener al menos 8 caracteres.';
            }

            // Si no hay errores, proceder a crear el usuario
            if (empty($data['error'])) {
                // Hashear la contraseña
                $data['password_hashed'] = password_hash($data['password'], PASSWORD_DEFAULT);

                // Cargar el modelo de Admin
                $adminModel = $this->model('Admin');

                // Intentar crear el usuario (asumiendo que el modelo tiene un método para esto)
                if ($adminModel->crearUsuarioCompleto($data)) { // Se asume un método como este
                    $_SESSION['mensaje_exito'] = 'Administrador creado correctamente.';
                    header('Location: ' . BASE_URL . '/admin/gestionarAdmin');
                    exit;
                } else {
                    $data['error'] = 'Ocurrió un error al crear el administrador. Intente de nuevo.';
                    $this->view('panel-admin/crear-admin', $data);
                }
            } else {
                // Si hay errores de validación, mostrar el formulario de nuevo con los errores
                $this->view('panel-admin/crear-admin', $data);
            }

        } else {
            // Mostrar el formulario por primera vez (método GET)
            $data['titulo'] = 'Crear Nuevo Administrador';
            $this->view('panel-admin/crear-admin', $data);
        }
    }


    public function backup()
    {
        // Verificar el estado de la base de datos
        $estadoBD = $this->adminModel->verificarEstadoBaseDeDatos();

        $data = [
            'titulo' => 'Respaldo de Base de Datos',
            'estado_bd' => $estadoBD,
            'success' => $_GET['success'] ?? null,
            'error' => $_GET['error'] ?? null
        ];

        // Si la base de datos está vacía, mostrar página especial
        if ($estadoBD['vacia']) {
            $this->view('panel-admin/backup-vacio', $data);
        } else {
            $this->view('panel-admin/backup', $data);
        }
    }

    public function exportarBD()
    {
        try {
            // Verificar estado de la base de datos
            $estadoBD = $this->adminModel->verificarEstadoBaseDeDatos();

            if ($estadoBD['vacia']) {
                header('Location: ' . BASE_URL . '/admin/backup?error=bd_vacia');
                exit();
            }

            $dump = $this->adminModel->exportarBaseDeDatos();

            if ($dump === '' || strlen($dump) < 100) {
                error_log('Error: Dump de BD vacío o muy pequeño');
                header('Location: ' . BASE_URL . '/admin/backup?error=export_fail');
                exit();
            }

            $filename = DB_NAME . '_' . date('Y-m-d_H-i-s') . '.sql';

            // Headers para descarga
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Content-Length: ' . strlen($dump));
            header('Cache-Control: must-revalidate');
            header('Pragma: public');

            echo $dump;
            exit();

        } catch (Exception $e) {
            error_log('Error exportando BD: ' . $e->getMessage());
            header('Location: ' . BASE_URL . '/admin/backup?error=export_exception');
            exit();
        }
    }

    public function importarBD()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/admin/backup?error=metodo_invalido');
            exit();
        }

        if (!isset($_FILES['archivo_sql'])) {
            header('Location: ' . BASE_URL . '/admin/backup?error=no_archivo');
            exit();
        }

        $file = $_FILES['archivo_sql'];

        // Validar errores de subida
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errorMsg = $this->getUploadErrorMessage($file['error']);
            header('Location: ' . BASE_URL . '/admin/backup?error=upload_error&msg=' . urlencode($errorMsg));
            exit();
        }

        // Validar extensión
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if ($extension !== 'sql') {
            header('Location: ' . BASE_URL . '/admin/backup?error=extension_invalida');
            exit();
        }

        // Validar tamaño (máximo 50MB)
        if ($file['size'] > 50 * 1024 * 1024) {
            header('Location: ' . BASE_URL . '/admin/backup?error=archivo_muy_grande');
            exit();
        }

        try {
            $contenido = file_get_contents($file['tmp_name']);

            if (empty($contenido)) {
                header('Location: ' . BASE_URL . '/admin/backup?error=archivo_vacio');
                exit();
            }

            $resultado = $this->adminModel->importarBaseDeDatos($contenido);

            if ($resultado['success']) {
                $successMsg = 'Base de datos importada exitosamente';
                if (isset($resultado['warnings']) && !empty($resultado['warnings'])) {
                    $successMsg .= ' con advertencias';
                }
                header('Location: ' . BASE_URL . '/admin/backup?success=' . urlencode($successMsg));
            } else {
                $errorMsg = $resultado['error'] ?? 'Error desconocido al importar';
                header('Location: ' . BASE_URL . '/admin/backup?error=import_fail&msg=' . urlencode($errorMsg));
            }

        } catch (Exception $e) {
            error_log('Error importando BD: ' . $e->getMessage());
            header('Location: ' . BASE_URL . '/admin/backup?error=excepcion&msg=' . urlencode($e->getMessage()));
        }

        exit();
    }

    private function getUploadErrorMessage($errorCode)
    {
        switch ($errorCode) {
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                return 'El archivo es demasiado grande';
            case UPLOAD_ERR_PARTIAL:
                return 'El archivo se subió parcialmente';
            case UPLOAD_ERR_NO_FILE:
                return 'No se seleccionó ningún archivo';
            case UPLOAD_ERR_NO_TMP_DIR:
                return 'Falta la carpeta temporal';
            case UPLOAD_ERR_CANT_WRITE:
                return 'Error al escribir el archivo';
            case UPLOAD_ERR_EXTENSION:
                return 'Extensión de archivo no permitida';
            default:
                return 'Error desconocido al subir archivo';
        }
    }

    public function eliminarBD()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/admin/backup?error=metodo_invalido');
            exit();
        }

        try {
            // Verificar estado antes de eliminar
            $estadoAntes = $this->adminModel->verificarEstadoBaseDeDatos();

            if ($estadoAntes['vacia']) {
                header('Location: ' . BASE_URL . '/admin/backup?error=bd_ya_vacia');
                exit();
            }

            $resultado = $this->adminModel->eliminarBaseDeDatos();

            if ($resultado['success']) {
                // Verificar que realmente se eliminó
                $estadoDespues = $this->adminModel->verificarEstadoBaseDeDatos();

                if ($estadoDespues['vacia']) {
                    // Redirigir a la página de backup que mostrará la vista de importación
                    header('Location: ' . BASE_URL . '/admin/backup?success=bd_eliminada');
                } else {
                    header('Location: ' . BASE_URL . '/admin/backup?error=eliminacion_incompleta');
                }
            } else {
                $errorMsg = $resultado['error'] ?? 'Error desconocido al eliminar';
                header('Location: ' . BASE_URL . '/admin/backup?error=drop_fail&msg=' . urlencode($errorMsg));
            }

        } catch (Exception $e) {
            error_log('Error eliminando BD: ' . $e->getMessage());
            header('Location: ' . BASE_URL . '/admin/backup?error=excepcion_eliminar&msg=' . urlencode($e->getMessage()));
        }

        exit();
    }
}