<?php
class UsuarioController extends Controller {

    private $notificacionModel;

    public function __construct() {
        // Solo verificar que el usuario esté logueado, sin importar el rol
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/login?error=acceso_no_autorizado');
            exit();
        }
        
        $this->notificacionModel = $this->model('Notificacion');
    }

    // Nueva función para verificar si el rol es 'usuario'
    private function verificarAccesoUsuario() {
        if ($_SESSION['user_role'] !== 'usuario') {
            header('Location: ' . BASE_URL . '/login?error=acceso_no_autorizado');
            exit();
        }
    }

    // Método por defecto que redirige al panel principal
    public function index() {
        $this->verificarAccesoUsuario(); // Proteger método
        header('Location: ' . BASE_URL . 'usuario/panelPrincipal');
        exit();
    }
    public function panelPrincipal() {
        $this->verificarAccesoUsuario(); // Proteger método
        $data = [
            'titulo' => 'Panel de Usuario - Loautech',
            'nombre_usuario' => $_SESSION['nombre'],
            'active_menu' => 'inicio'
        ];

        $this->view('panel-usuario/panel-principal', $data);
    }
    
    /**
     * Muestra la página de notificaciones del usuario
     */
    public function notificaciones() {
        $this->verificarAccesoUsuario(); // Proteger método
        // Configuración de paginación
        $porPagina = 10;
        $pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
        $offset = ($pagina - 1) * $porPagina;
        
        // Obtener notificaciones paginadas
        $totalNotificaciones = $this->notificacionModel->contarNotificacionesUsuario($_SESSION['user_id']);
        $notificaciones = $this->notificacionModel->obtenerNotificacionesUsuario(
            $_SESSION['user_id'], 
            $offset, 
            $porPagina
        );
        
        // Calcular total de páginas
        $totalPaginas = ceil($totalNotificaciones / $porPagina);
        
        $data = [
            'titulo' => 'Mis Notificaciones - Loautech',
            'nombre_usuario' => $_SESSION['nombre'],
            'active_menu' => 'notificaciones',
            'notificaciones' => $notificaciones,
            'totalNotificaciones' => $totalNotificaciones,
            'paginaActual' => $pagina,
            'totalPaginas' => $totalPaginas,
            'porPagina' => $porPagina,
            'success' => $_GET['success'] ?? null,
            'error' => $_GET['error'] ?? null
        ];
        
        $this->view('usuario/notificaciones', $data);
    }
    
    /**
     * Marca una notificación como leída
     */
    public function marcarNotificacionLeida($id) {
        $this->verificarAccesoUsuario(); // Proteger método
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($this->notificacionModel->marcarNotificacionLeida($id, $_SESSION['user_id'])) {
                echo json_encode(['success' => true]);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Error al marcar la notificación como leída']);
            }
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
        }
    }
    
    /**
     * Marca todas las notificaciones como leídas
     */
    public function marcarTodasLeidas() {
        $this->verificarAccesoUsuario(); // Proteger método
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($this->notificacionModel->marcarTodasLeidas($_SESSION['user_id'])) {
                echo json_encode(['success' => true]);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Error al marcar las notificaciones como leídas']);
            }
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
        }
    }
    
    public function inventario() {
        $this->verificarAccesoUsuario(); // Proteger método
        $elementoModel = $this->model('Elemento');
        $elementos = $elementoModel->obtenerElementosDisponibles();

        $data = [
            'titulo' => 'Inventario - Loautech',
            'nombre_usuario' => $_SESSION['nombre'],
            'active_menu' => 'inventario',
            'elementos' => $elementos,
            'success' => $_GET['success'] ?? null,
            'error' => $_GET['error'] ?? null
        ];

        $this->view('panel-usuario/inventario', $data);
    }
    
    /**
     * Muestra el formulario para registrar un nuevo elemento
     */
    public function registrarElemento() {
        $this->verificarAccesoUsuario(); // Proteger método
        $data = [
            'titulo' => 'Registrar Elemento - Loautech',
            'nombre_usuario' => $_SESSION['nombre'],
            'active_menu' => 'registrar_elemento',
            'success' => $_GET['success'] ?? null,
            'error' => $_GET['error'] ?? null,
            'datos' => [
                'nombreingele' => '',
                'tipoelemento' => '',
                'serial' => '',
                'descripcioningele' => '',
                'observacioningele' => ''
            ]
        ];

        $this->view('panel-usuario/registrar-elemento', $data);
    }
    
    /**
     * Procesa el formulario de registro de elemento
     */
    public function guardarElemento() {
        $this->verificarAccesoUsuario(); // Proteger método
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validar datos
            $errores = [];
            
            if (empty($_POST['nombreingele'])) {
                $errores[] = 'El nombre del elemento es requerido';
            }
            
            if (empty($_POST['tipoelemento'])) {
                $errores[] = 'El tipo de elemento es requerido';
            }
            
            if (empty($_POST['serial'])) {
                $errores[] = 'El serial es requerido';
            }
            
            if (empty($errores)) {
                $ingresoModel = $this->model('IngresoElemento');
                
                $datos = [
                    'nombreingele' => trim($_POST['nombreingele']),
                    'tipoelemento' => trim($_POST['tipoelemento']),
                    'serial' => trim($_POST['serial'] ?? ''),
                    'descripcioningele' => trim($_POST['descripcioningele'] ?? ''),
                    'observacioningele' => trim($_POST['observacioningele'] ?? '')
                ];
                
                // Validar longitudes máximas según la estructura de la tabla
                if (strlen($datos['nombreingele']) > 250) {
                    $errores[] = 'El nombre del elemento no puede tener más de 250 caracteres';
                }
                
                if (strlen($datos['tipoelemento']) > 200) {
                    $errores[] = 'El tipo de elemento no puede tener más de 200 caracteres';
                }
                
                if (strlen($datos['descripcioningele']) > 250) {
                    $errores[] = 'La descripción no puede tener más de 250 caracteres';
                }
                
                if (strlen($datos['observacioningele']) > 250) {
                    $errores[] = 'Las observaciones no pueden tener más de 250 caracteres';
                }
                
                if (strlen($datos['serial']) > 100) {
                    $errores[] = 'El serial no puede tener más de 100 caracteres';
                }
                
                if ($ingresoModel->guardar($datos)) {
                    $_SESSION['exito'] = 'Elemento registrado exitosamente';
                    header('Location: ' . BASE_URL . 'usuario/panelPrincipal');
                    exit();
                } else {
                    $errores[] = 'Error al guardar el elemento: ' . $ingresoModel->getError();
                }
            }
            
            // Si hay errores, volver a mostrar el formulario
            $data = [
                'titulo' => 'Registrar Elemento - Loautech',
                'nombre_usuario' => $_SESSION['nombre'],
                'active_menu' => 'registrar_elemento',
                'error' => !empty($errores) ? implode('<br>', $errores) : null,
                'datos' => $_POST
            ];
            
            $this->view('panel-usuario/registrar-elemento', $data);
        } else {
            header('Location: ' . BASE_URL . 'usuario/registrarElemento');
            exit();
        }
    }


    public function misIngresos()
    {
        // Asegurarse de que el usuario está logueado
        if (!Session::get('user_id')) {
            header('Location: /mvc_dev/login');
            exit;
        }

        // Cargar el modelo de Ingreso
        $ingresoModel = $this->model('Ingreso');
        $usuario_id = $_SESSION['user_id'];

         // 1. Obtener el inventario completo y los ingresos de hoy usando los métodos específicos
        $inventarioCompleto = $ingresoModel->obtenerInventarioPorUsuario($usuario_id);
        $elementosIngresadosHoy = $ingresoModel->obtenerIngresosDeHoyPorUsuario($usuario_id);

        // 2. Crear un array de búsqueda con los identificadores de los elementos pendientes de salida
        $identificadoresIngresadosHoy = [];
        foreach ($elementosIngresadosHoy as $ingreso) {
            // Solo considerar los que no tienen hora de salida para ocultarlos del checklist
            if ($ingreso->hora_salida === null) {
                $identificador = !empty($ingreso->serial) ? $ingreso->serial : $ingreso->nombreingele;
                $identificadoresIngresadosHoy[$identificador] = true;
            }
        }

        // 3. Filtrar el inventario para no mostrar los elementos que ya fueron ingresados hoy y están pendientes
        $inventarioParaChecklist = array_filter($inventarioCompleto, function ($item) use ($identificadoresIngresadosHoy) {
            return !isset($identificadoresIngresadosHoy[$item->identificador_unico]);
        });

        // 4. Preparar los datos para la vista
        $datos = [
            'titulo' => 'Mis Ingresos',
            'elementos' => $elementosIngresadosHoy, // Corregido: ahora siempre tendrá los datos de hoy
            'inventario' => $inventarioParaChecklist,
            'nombre_usuario' => $_SESSION['nombre'],
        ];

        // Cargar la vista
        $this->view('panel-usuario/mis-ingresos', $datos);
    }

    public function misPeticiones()
    {
        $peticionModel = $this->model('Peticion');
        $idUsuario = $_SESSION['user_id'];

        // --- Lógica de Paginación y Filtros ---
        $paginaActual = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        if ($paginaActual < 1) $paginaActual = 1;
        
        $resultadosPorPagina = 5; // 5 resultados por página como solicitaste
        $offset = ($paginaActual - 1) * $resultadosPorPagina;

        // Recogemos los filtros de la URL (si existen)
        $filtros = [
            'search' => $_GET['search'] ?? '',
            'status' => $_GET['status'] ?? ''
        ];

        // Obtenemos las peticiones para la página actual, aplicando filtros
        $peticiones = $peticionModel->getPeticionesPaginadasPorUsuario($idUsuario, $resultadosPorPagina, $offset, $filtros);

        // Contamos el total de resultados que coinciden con los filtros para la paginación
        $totalPeticiones = $peticionModel->contarPeticionesFiltradas($idUsuario, $filtros);
        $totalPaginas = ceil($totalPeticiones / $resultadosPorPagina);
        // --- Fin de la lógica ---

        $data = [
            'titulo' => 'Mis Peticiones - Loautech',
            // CORREGIDO: Se usa la clave de sesión correcta.
            'nombre_usuario' => $_SESSION['nombre'],
            'active_menu' => 'mis_peticiones',
            'peticiones' => $peticiones,
            'paginaActual' => $paginaActual,
            'totalPaginas' => $totalPaginas,
            'filtros' => $filtros // Pasamos los filtros a la vista para mantener su estado
        ];

        $this->view('panel-usuario/mis-peticiones', $data);
    }

    /**
     * Maneja la subida del avatar del usuario
     */
    public function actualizarAvatar() {
        try {
            // Verificar si se ha enviado un archivo
            if (!isset($_FILES['avatar'])) {
                throw new Exception('No se recibió ningún archivo');
            }

            $file = $_FILES['avatar'];
            
            // Verificar errores de subida
            if ($file['error'] !== UPLOAD_ERR_OK) {
                $errorMessages = [
                    UPLOAD_ERR_INI_SIZE => 'El archivo excede el tamaño máximo permitido por el servidor.',
                    UPLOAD_ERR_FORM_SIZE => 'El archivo excede el tamaño máximo permitido.',
                    UPLOAD_ERR_PARTIAL => 'El archivo solo se subió parcialmente.',
                    UPLOAD_ERR_NO_FILE => 'No se seleccionó ningún archivo.',
                    UPLOAD_ERR_NO_TMP_DIR => 'Falta la carpeta temporal.',
                    UPLOAD_ERR_CANT_WRITE => 'Error al escribir el archivo en el disco.',
                    UPLOAD_ERR_EXTENSION => 'Una extensión de PHP detuvo la subida del archivo.'
                ];
                
                $error = $errorMessages[$file['error']] ?? 'Error desconocido al subir el archivo';
                throw new Exception($error);
            }

            $userId = $_SESSION['user_id'];
            
            // Validar el tipo de archivo
            $allowedTypes = [
                'image/jpeg' => 'jpg',
                'image/png' => 'png',
                'image/gif' => 'gif'
            ];
            
            $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($fileInfo, $file['tmp_name']);
            finfo_close($fileInfo);
            
            if (!array_key_exists($mimeType, $allowedTypes)) {
                throw new Exception('Formato de archivo no permitido. Solo se permiten imágenes JPG, PNG o GIF.');
            }
            
            // Tamaño máximo de archivo: 5MB
            $maxFileSize = 5 * 1024 * 1024;
            if ($file['size'] > $maxFileSize) {
                throw new Exception('El archivo es demasiado grande. El tamaño máximo permitido es 5MB.');
            }
            
            // Crear directorio de avatares si no existe
            $uploadDir = 'public/uploads/avatars/';
            if (!is_dir($uploadDir)) {
                if (!mkdir($uploadDir, 0777, true)) {
                    throw new Exception('No se pudo crear el directorio para guardar el avatar.');
                }
            }
            
            // Verificar permisos del directorio
            if (!is_writable($uploadDir)) {
                throw new Exception('El directorio de avatares no tiene permisos de escritura.');
            }
            
            // Generar un nombre único para el archivo
            $fileExtension = $allowedTypes[$mimeType];
            $fileName = 'avatar_' . $userId . '_' . time() . '.' . $fileExtension;
            $filePath = $uploadDir . $fileName;
            
            // Mover el archivo subido
            if (!move_uploaded_file($file['tmp_name'], $filePath)) {
                throw new Exception('Error al mover el archivo subido.');
            }
            
            // Actualizar la ruta del avatar en la base de datos
            $userModel = $this->model('User');
            if (!$userModel->actualizarAvatar($userId, $filePath)) {
                // Si falla la actualización en la BD, eliminar el archivo subido
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
                throw new Exception($userModel->getError() ?: 'Error al actualizar el avatar en la base de datos');
            }
            
            // Devolver la URL completa del avatar
            $avatarUrl = BASE_URL . '/' . $filePath;
            echo json_encode([
                'success' => true,
                'avatar_url' => $avatarUrl
            ]);
            
        } catch (Exception $e) {
            error_log('Error en actualizarAvatar: ' . $e->getMessage());
            error_log('Trace: ' . $e->getTraceAsString());
            
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }
    
    public function perfil() {
        $userModel = $this->model('User');
        $idUsuario = $_SESSION['user_id'];
        
        // Manejar la actualización del perfil
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            error_log('Datos POST recibidos: ' . print_r($_POST, true));
            
            $datos = [
                'id' => $idUsuario,
                'email' => trim($_POST['email'] ?? ''),
                'telefono' => trim($_POST['telefono'] ?? ''),
                'password_actual' => $_POST['password_actual'] ?? '',
                'nueva_password' => $_POST['nueva_password'] ?? '',
                'confirmar_password' => $_POST['confirmar_password'] ?? ''
            ];
            
            error_log('Datos procesados: ' . print_r($datos, true));
            
            // Validar y actualizar datos básicos
            if (!empty($datos['email']) || !empty($datos['telefono'])) {
                error_log('Intentando actualizar perfil...');
                if ($userModel->actualizarPerfil($datos)) {
                    $_SESSION['exito'] = 'Perfil actualizado correctamente';
                    error_log('Perfil actualizado correctamente');
                } else {
                    $error = $userModel->getError() ?? 'Error desconocido al actualizar el perfil';
                    $_SESSION['error'] = $error;
                    error_log('Error al actualizar perfil: ' . $error);
                }
            }
            
            // Validar y actualizar contraseña
            if (!empty($datos['nueva_password'])) {
                error_log('Intentando cambiar contraseña...');
                if ($userModel->cambiarPassword($datos)) {
                    $_SESSION['exito'] = 'Contraseña actualizada correctamente';
                    error_log('Contraseña actualizada correctamente');
                } else {
                    $error = $userModel->getError() ?? 'Error desconocido al cambiar la contraseña';
                    $_SESSION['error'] = $error;
                    error_log('Error al cambiar contraseña: ' . $error);
                }
            }
            
            // Manejar la subida de avatar
            if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
                error_log('Subiendo archivo de avatar...');
                $this->subirAvatar($_FILES['avatar'], $idUsuario);
            }
            
            // Redirigir para evitar reenvío del formulario
            error_log('Redirigiendo a: ' . BASE_URL . 'usuario/perfil');
            header('Location: ' . BASE_URL . 'usuario/perfil');
            exit();
        }
        
        // Obtener datos actualizados del perfil
        $perfilUsuario = $userModel->obtenerPerfilUsuario($idUsuario);

        if (!$perfilUsuario) {
            header('Location: ' . BASE_URL . '/login?error=perfil_no_encontrado');
            exit();
        }

        $data = [
            'titulo' => 'Mi Perfil - Loautech',
            'nombre_usuario' => $_SESSION['nombre'],
            'perfil' => $perfilUsuario,
            'active_menu' => 'perfil',
            'mensaje_exito' => $_SESSION['exito'] ?? null,
            'mensaje_error' => $_SESSION['error'] ?? null,
            'avatar_url' => $this->obtenerAvatarUrl($perfilUsuario->IDper ?? 0)
        ];

        unset($_SESSION['exito'], $_SESSION['error']);

        $this->view('panel-usuario/perfil', $data);
    }

     public function historialIngresos($pagina = 1)
    {
        Session::init();
        if (Session::get('user_id') === null) {
            header('Location: /mvc_dev/login');
            exit;
        }

        $idUsuario = Session::get('user_id');
        $ingresoModel = $this->model('Ingreso');

        $registrosPorPagina = 10;
        $paginaActual = filter_var($pagina, FILTER_VALIDATE_INT) ? (int)$pagina : 1;
        $offset = ($paginaActual - 1) * $registrosPorPagina;

        $totalRegistros = $ingresoModel->contarHistorialIngresos($idUsuario);
        $totalPaginas = ceil($totalRegistros / $registrosPorPagina);

        $historial = $ingresoModel->obtenerHistorialIngresos($idUsuario, $registrosPorPagina, $offset);

        $data = [
            'titulo' => 'Historial de Ingresos',
            'historial' => $historial,
            'paginaActual' => $paginaActual,
            'totalPaginas' => $totalPaginas
        ];

        $this->view('panel-usuario/historial-ingresos', $data);
    }
    
    /**
     * Maneja la subida del avatar del usuario
     */
    private function subirAvatar($archivo, $usuarioId) {
        $carpetaDestino = 'public/uploads/avatares/';
        
        // Crear directorio si no existe
        if (!file_exists($carpetaDestino)) {
            mkdir($carpetaDestino, 0777, true);
        }
        
        // Validar tipo de archivo
        $tipoPermitidos = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($archivo['type'], $tipoPermitidos)) {
            $_SESSION['error'] = 'Formato de archivo no permitido. Solo se permiten JPG, PNG y GIF.';
            return false;
        }
        
        // Validar tamaño (máx 2MB)
        $tamanoMaximo = 2 * 1024 * 1024; // 2MB
        if ($archivo['size'] > $tamanoMaximo) {
            $_SESSION['error'] = 'El archivo es demasiado grande. Tamaño máximo permitido: 2MB';
            return false;
        }
        
        // Generar nombre único para el archivo
        $extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);
        $nombreArchivo = 'avatar_' . $usuarioId . '_' . time() . '.' . $extension;
        $rutaDestino = $carpetaDestino . $nombreArchivo;
        
        // Mover archivo subido
        if (move_uploaded_file($archivo['tmp_name'], $rutaDestino)) {
            // Actualizar la ruta del avatar en la base de datos
            $userModel = $this->model('User');
            return $userModel->actualizarAvatar($usuarioId, $rutaDestino);
        }
        
        $_SESSION['error'] = 'Error al subir el archivo';
        return false;
    }
    
    /**
     * Obtiene la URL del avatar del usuario
     */
    private function obtenerAvatarUrl($usuarioId) {
        $rutaAvatar = 'public/uploads/avatares/avatar_' . $usuarioId . '.jpg';
        
        // Verificar si existe un avatar personalizado
        if (file_exists($rutaAvatar)) {
            return BASE_URL . $rutaAvatar . '?v=' . filemtime($rutaAvatar);
        }
        
        // Si no hay avatar, usar uno por defecto
        return BASE_URL . 'public/img/avatar-default.png';
    }
}