<?php
class UsuarioController extends Controller {
    
    public function __construct() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // Verificar con las NUEVAS variables de sesión
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'usuario') {
            header('Location: ' . BASE_URL . '/login?error=acceso_no_autorizado');
            exit();
        }
    }
    
    // Método por defecto que redirige al panel principal
    public function index() {
        header('Location: ' . BASE_URL . 'usuario/panelPrincipal');
        exit();
    }

    // Método para mostrar el panel principal del usuario
    public function panelPrincipal() {
        $data = [
            'titulo' => 'Panel de Usuario - Loautech',
            // Se usa la clave de sesión correcta.
            'nombre_usuario' => $_SESSION['nombre'],
            'active_menu' => 'inicio'
        ];

        $this->view('panel-usuario/panel-principal', $data);
    }
    
    public function inventario() {
        $elementoModel = $this->model('Elemento');
        $elementos = $elementoModel->obtenerElementosDisponibles();

        $data = [
            'titulo' => 'Inventario - Loautech',
            // Se usa la clave de sesión correcta.
            'nombre_usuario' => $_SESSION['nombre'],
            'active_menu' => 'inventario',
            'elementos' => $elementos,
            'success' => $_GET['success'] ?? null,
            'error' => $_GET['error'] ?? null
        ];

        $this->view('panel-usuario/inventario', $data);
    }

    public function registrarElemento() {
        $data = [
            'titulo' => 'Registrar Elemento - Loautech',
            // Se usa la clave de sesión correcta.
            'nombre_usuario' => $_SESSION['nombre'],
            'active_menu' => 'registrar_elemento'
        ];
        $this->view('panel-usuario/registrar-elemento', $data);
    }

    public function misIngresos() {
        $ingresoModel = $this->model('Ingreso');
        $usuario_id = $_SESSION['user_id']; // Usar la nueva variable
        $elementos = $ingresoModel->obtenerIngresosPorUsuario($usuario_id);

        $data = [
            'titulo' => 'Mis Ingresos - Loautech',
            //Se usa la clave de sesión correcta.
            'nombre_usuario' => $_SESSION['nombre'],
            'active_menu' => 'mis_ingresos',
            'elementos' => $elementos
        ];
        $this->view('panel-usuario/mis-ingresos', $data);
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
        // Verificar si se ha enviado un archivo
        if (!isset($_FILES['avatar']) || $_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
            echo json_encode([
                'success' => false,
                'error' => 'No se ha seleccionado ningún archivo o hubo un error en la carga.'
            ]);
            return;
        }

        $file = $_FILES['avatar'];
        $userId = $_SESSION['user_id'];
        
        // Validar el tipo de archivo
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($file['type'], $allowedTypes)) {
            echo json_encode([
                'success' => false,
                'error' => 'Formato de archivo no permitido. Solo se permiten imágenes JPG, PNG o GIF.'
            ]);
            return;
        }
        
        // Tamaño máximo de archivo: 5MB
        $maxFileSize = 5 * 1024 * 1024;
        if ($file['size'] > $maxFileSize) {
            echo json_encode([
                'success' => false,
                'error' => 'El archivo es demasiado grande. El tamaño máximo permitido es 5MB.'
            ]);
            return;
        }
        
        // Crear directorio de avatares si no existe
        $uploadDir = 'public/uploads/avatars/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        // Generar un nombre único para el archivo
        $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $fileName = 'avatar_' . $userId . '_' . time() . '.' . $fileExtension;
        $filePath = $uploadDir . $fileName;
        
        // Mover el archivo subido
        if (move_uploaded_file($file['tmp_name'], $filePath)) {
            // Actualizar la ruta del avatar en la base de datos
            $userModel = $this->model('User');
            if ($userModel->actualizarAvatar($userId, $filePath)) {
                // Devolver la URL completa del avatar
                $avatarUrl = BASE_URL . '/' . $filePath;
                echo json_encode([
                    'success' => true,
                    'avatar_url' => $avatarUrl
                ]);
                return;
            }
        }
        
        // Si llegamos aquí, hubo un error
        echo json_encode([
            'success' => false,
            'error' => 'Error al guardar el archivo. Por favor, inténtalo de nuevo.'
        ]);
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