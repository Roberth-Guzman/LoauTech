<?php

class AlmacenController extends Controller
{
    private $peticionModel;
    private $elementoModel;
    private $adminModel; 
    private $userModel;

    public function __construct()
    {
        Session::init();

        // Verifica que el usuario tenga el rol de 'almacen'
        if (!Session::isLoggedIn() || Session::getUserRole() !== 'almacenes') {
            $this->redirect('login?error=acceso_no_autorizado');
            exit();
        }
        
        $this->peticionModel = $this->model('Peticion');
        $this->elementoModel = $this->model('Elemento');
        $this->adminModel = $this->model('Admin');
        $this->userModel = $this->model('User');
    }

    // Muestra el panel principal con las solicitudes pendientes
    public function panelPrincipal($pagina = 1)
    {
        // Configuración de paginación
        $registros_por_pagina = 10;
        $offset = ($pagina - 1) * $registros_por_pagina;
        
        // Obtener el total de peticiones para la paginación
        $total_peticiones = $this->peticionModel->contarPeticionesPendientes();
        $total_paginas = ceil($total_peticiones / $registros_por_pagina);
        
        // Obtener las peticiones para la página actual
        $peticiones = $this->peticionModel->obtenerPeticionesPaginadas($offset, $registros_por_pagina);
        
        $data = [
            'titulo' => 'Solicitudes Pendientes - Loautech',
            'nombre_usuario' => $_SESSION['nombre'],
            'active_menu' => 'solicitudes',
            'solicitudes' => $peticiones,
            'pagina_actual' => $pagina,
            'total_paginas' => $total_paginas,
            'total_registros' => $total_peticiones,
            'success' => $_GET['success'] ?? null,
            'error' => $_GET['error'] ?? null
        ];
        
        $this->view('panel-almacenes/panel-solicitudes', $data);
    }
    public function editarPerfil()
    {
        // Inicia la sesión para obtener el user_id
        Session::init();
        $this->userModel = $this->model('User');
        $userId = Session::get('user_id');

        // Si no hay un usuario logueado, redirige al login
        if (!$userId) {
            $this->redirect('login');
            return;
        }

        // Si se envía el formulario (POST)
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Sanitizar los datos del formulario
            $data = [
                'nombre' => trim($_POST['nombre']),
                'email' => trim($_POST['email']),
                'telefono' => trim($_POST['telefono']),
                'id' => $userId,
                'nombre_err' => '',
                'email_err' => '',
                'telefono_err' => ''
            ];

            // Llama al método del modelo para actualizar el perfil
            if ($this->userModel->actualizarPerfil($data)) {
                // Si la actualización es exitosa, redirige al perfil de almacén
                Session::set('profile_update_success', 'Tu perfil ha sido actualizado correctamente.');
                $this->redirect('almacen/perfil');
            } else {
                // Si falla, puedes mostrar un error
                die('Algo salió mal al actualizar el perfil.');
            }

        } else {
            // Si no es POST, simplemente muestra el formulario con los datos del usuario
            $usuario = $this->userModel->findUserById($userId);
            $data = [
                'usuario' => $usuario
            ];
            $this->view('panel-almacenes/editar-perfil', $data);
        }
    }
    // Muestra la página de inventario
    public function inventario() {
        // --- Lógica de Paginación ---
        $elementos_por_pagina = 10; // Puedes ajustar este número
        $total_elementos = $this->elementoModel->contarTodosLosElementos();
        $total_paginas = ceil($total_elementos / $elementos_por_pagina);
        
        // Obtenemos la página actual de la URL, asegurándonos de que sea un número válido
        $pagina_actual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
        if ($pagina_actual < 1) {
            $pagina_actual = 1;
        } elseif ($pagina_actual > $total_paginas && $total_paginas > 0) {
            $pagina_actual = $total_paginas;
        }

        // Obtenemos los elementos para la página actual
        $elementos = $this->elementoModel->obtenerTodosLosElementosPaginados($pagina_actual, $elementos_por_pagina);

        $data = [
            'titulo' => 'Inventario General - Loautech',
            'nombre_usuario' => $_SESSION['nombre'],
            'active_menu' => 'inventario',
            'elementos' => $elementos,
            'pagina_actual' => $pagina_actual,
            'total_paginas' => $total_paginas,
            'success' => $_GET['success'] ?? null,
            'error' => $_GET['error'] ?? null
        ];

        $this->view('panel-almacenes/inventario', $data);
    }

    // Muestra la página de visualización
    public function visualizacion() {
        $elementos = $this->elementoModel->obtenerTodosLosElementos();
        $categorias = $this->elementoModel->obtenerCategoriasDistintas();

        $data = [
            'titulo' => 'Visualización de Inventario - Loautech',
            'nombre_usuario' => $_SESSION['nombre'],
            'active_menu' => 'visualizacion',
            'elementos' => $elementos,
            'categorias' => $categorias,
            'success' => $_GET['success'] ?? null,
            'error' => $_GET['error'] ?? null
        ];

        $this->view('panel-almacenes/visualizacion', $data);
    }

    public function detalleElemento($id = 0) {
        if (!is_numeric($id) || $id <= 0) {
            header('Location: ' . BASE_URL . '/almacen/almacen/visualizacion');
            exit();
        }

        $elemento = $this->elementoModel->obtenerElementoPorId($id);

        if (!$elemento) {
            header('Location: ' . BASE_URL . '/almacen/almacen/visualizacion');
            exit();
        }

        $data = [
            'page_title' => 'Detalles del Elemento',
            'titulo' => 'Detalles del Elemento: ' . htmlspecialchars($elemento->nombreele),
            'nombre_usuario' => $_SESSION['nombre'],
            'active_menu' => 'visualizacion',
            'elemento' => $elemento
        ];

        $this->view('panel-almacenes/detalle-elemento', $data);
    }

     public function editarElemento($id = 0) {
        if (!is_numeric($id) || $id <= 0) {
            header('Location: ' . BASE_URL . '/almacen/almacen/visualizacion');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Procesar el formulario
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            $imagen_actual = $_POST['imagen_actual'] ?? '';
            $imagen_path = $imagen_actual;

            // Gestión de la subida de nueva imagen
            if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = 'uploads/inventario/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                $imageName = uniqid('item_') . '_' . basename($_FILES['imagen']['name']);
                $targetPath = $uploadDir . $imageName;

                if (move_uploaded_file($_FILES['imagen']['tmp_name'], $targetPath)) {
                    // Si se sube una nueva imagen, eliminar la anterior si existe y no es la imagen por defecto
                    if (!empty($imagen_actual) && file_exists($imagen_actual)) {
                        unlink($imagen_actual);
                    }
                    $imagen_path = $targetPath; // Guardar la nueva ruta
                }
            }

            $data = [
                'id' => $id,
                'nombreele' => trim($_POST['nombreele']),
                'cantidadele' => intval($_POST['cantidadele']),
                'codigoele' => trim($_POST['codigoele']),
                'codigoinventario' => trim($_POST['codigoinventario']),
                'descripcionele' => trim($_POST['descripcionele']),
                'caracteristicasele' => trim($_POST['caracteristicasele']),
                'estado' => trim($_POST['estado']),
                'estadoelemento' => trim($_POST['estadoelemento']),
                'cuentadante_id' => empty($_POST['cuentadante_id']) ? null : (int)$_POST['cuentadante_id'], // <-- AÑADIDO
                'imagen' => $imagen_path,
                'error' => ''
            ];

            if (empty($data['nombreele']) || empty($data['codigoele'])) {
                $data['error'] = 'El nombre y el código son obligatorios.';
                // Si hay error, recargamos la vista con los datos y el error
                $elemento = $this->elementoModel->obtenerElementoPorId($id);
                $cuentadantes = $this->userModel->obtenerCuentadantes(); // <-- AÑADIDO

                $data['page_title'] = 'Editar Elemento';
                $data['titulo'] = 'Editar Elemento: ' . $elemento->nombreele;
                $data['nombre_usuario'] = $_SESSION['nombre'];
                $data['active_menu'] = 'visualizacion';
                $data['elemento'] = $elemento;
                $data['cuentadantes'] = $cuentadantes; // <-- AÑADIDO
                $this->view('panel-almacenes/editar-elemento', $data);
            } else {
                // NOTA: El método actualizarElemento en el modelo Elemento.php deberá ser modificado para guardar el 'cuentadante_id'
                if ($this->elementoModel->actualizarElemento($data)) {
                    header('Location: ' . BASE_URL . '/almacen/almacen/detalleElemento/' . $id . '?success=actualizado');
                    exit;
                } else {
                    $data['error'] = 'Error al actualizar el elemento.';
                    $elemento = $this->elementoModel->obtenerElementoPorId($id);
                    $cuentadantes = $this->userModel->obtenerCuentadantes(); // <-- AÑADIDO

                    $data['page_title'] = 'Editar Elemento';
                    $data['titulo'] = 'Editar Elemento: ' . $elemento->nombreele;
                    $data['nombre_usuario'] = $_SESSION['nombre'];
                    $data['active_menu'] = 'visualizacion';
                    $data['elemento'] = $elemento;
                    $data['cuentadantes'] = $cuentadantes; // <-- AÑADIDO
                    $this->view('panel-almacenes/editar-elemento', $data);
                }
            }
        } else {
            // Método GET: Mostrar el formulario con los datos actuales
            $elemento = $this->elementoModel->obtenerElementoPorId($id);
            if (!$elemento) {
                header('Location: ' . BASE_URL . '/almacen/almacen/visualizacion');
                exit;
            }

            // Obtener la lista de cuentadantes
            $cuentadantes = $this->userModel->obtenerCuentadantes(); // <-- AÑADIDO

            $data = [
                'page_title' => 'Editar Elemento',
                'titulo' => 'Editar Elemento: ' . htmlspecialchars($elemento->nombreele),
                'nombre_usuario' => $_SESSION['nombre'],
                'active_menu' => 'visualizacion',
                'elemento' => $elemento,
                'cuentadantes' => $cuentadantes, // <-- AÑADIDO
                'error' => ''
            ];
            $this->view('panel-almacenes/editar-elemento', $data);
        }
    }

    // Aprobar una petición
    public function aprobar($id) {
        if ($this->peticionModel->cambiarEstadoPeticion($id, 'Aprobado')) {
            header('Location: ' . BASE_URL . '/almacen/almacen/panelPrincipal?success=peticion_aprobada');
        } else {
            header('Location: ' . BASE_URL . '/almacen/almacen/panelPrincipal?error=aprobacion_fallida');
        }
        exit();
    }

    // Procesar aprobación o rechazo de préstamo
     public function aprobarRechazar()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            header('Content-Type: application/json');
            $peticionId = $_POST['peticion_id'] ?? null;
            $accion = $_POST['accion'] ?? null;

            if (!$peticionId || !$accion) {
                echo json_encode(['status' => 'error', 'message' => 'Datos incompletos.']);
                return;
            }

            $this->peticionModel = $this->model('Peticion');
            $success = false;

            if ($accion == 'aprobar') {
                $success = $this->peticionModel->aprobarPeticionAlmacen($peticionId);
            } else {
                $motivo = $_POST['motivo'] ?? 'Rechazado por almacén';
                $success = $this->peticionModel->rechazarPeticionAlmacen($peticionId, $motivo);
            }

            if ($success) {
                echo json_encode(['status' => 'success']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Error al procesar la solicitud.']);
            }
        } else {
            // No es una solicitud POST
            echo json_encode(['status' => 'error', 'message' => 'Método no permitido.']);
        }
    }

    
    // Rechazar una petición
    public function rechazar($id) {
        if ($this->peticionModel->rechazarPeticion($id)) {
            header('Location: ' . BASE_URL . '/almacen/almacen/panelPrincipal?success=peticion_rechazada');
        } else {
            header('Location: ' . BASE_URL . '/almacen/almacen/panelPrincipal?error=rechazo_fallido');
        }
        exit();
    }

    public function perfil() {
        // Cargar el modelo de usuario
        $userModel = $this->model('User');

        // Obtener el ID del usuario de la sesión
        $idUsuario = $_SESSION['user_id'] ?? 0;

        // Obtener los datos completos del perfil del usuario
        $perfil = $userModel->obtenerPerfilCompletoPorId($idUsuario);

        if (!$perfil) {
            // Redirigir o mostrar un error si el perfil no se encuentra
            header('Location: ' . BASE_URL . '/login?error=perfil_no_encontrado');
            exit();
        }

        $data = [
            'titulo' => 'Mi Perfil - Loautech',
            'nombre_usuario' => $_SESSION['nombre'],
            'active_menu' => 'perfil',
            'perfil' => $perfil,
            'success' => $_GET['success'] ?? null,
            'error' => $_GET['error'] ?? null
        ];

       $this->view('panel-almacenes/perfil', $data);
    }

    // --- NUEVO MÉTODO PARA EL HISTORIAL ---
    public function historial($pagina = 1) {
        // 1. Asegurarse de que la página sea un número válido y siempre >= 1
        $pagina = filter_var($pagina, FILTER_VALIDATE_INT, ['options' => ['default' => 1, 'min_range' => 1]]);
        if ($pagina === false || $pagina < 1) {
            $pagina = 1;
        }

        $registros_por_pagina = 15; // O el número que prefieras
        $offset = ($pagina - 1) * $registros_por_pagina;

        // Obtener el total de registros para la paginación
        $total_registros = $this->adminModel->obtenerTotalHistorialPeticiones();
        $total_paginas = ceil($total_registros / $registros_por_pagina);

        // 2. Corregir el orden de los parámetros: primero va el offset, luego el límite
        $historial = $this->adminModel->obtenerHistorialPeticiones($offset, $registros_por_pagina);

        $data = [
            'titulo' => 'Historial de Solicitudes - Loautech',
            'nombre_usuario' => $_SESSION['nombre'],
            'active_menu' => 'historial', 
            'historial' => $historial,
            'pagina_actual' => $pagina,
            'total_paginas' => $total_paginas,
            'total_registros' => $total_registros,
            'success' => $_GET['success'] ?? null,
            'error' => $_GET['error'] ?? null
        ];

        $this->view('panel-almacenes/historial', $data);

    }

    public function agregarElemento() {

        // Comprobar si la petición es de tipo POST (cuando se envía el formulario)
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Sanitizar los datos del formulario para evitar inyecciones de código
            $nombre = filter_input(INPUT_POST, 'nombreele', FILTER_SANITIZE_STRING);
            $cantidad = filter_input(INPUT_POST, 'cantidadele', FILTER_SANITIZE_NUMBER_INT);
            $codigo = filter_input(INPUT_POST, 'codigoele', FILTER_SANITIZE_STRING);
            $codigoInventario = filter_input(INPUT_POST, 'codigoinventario', FILTER_SANITIZE_STRING);
            $estado = filter_input(INPUT_POST, 'estado', FILTER_SANITIZE_STRING);
            $estadoElemento = filter_input(INPUT_POST, 'estadoelemento', FILTER_SANITIZE_STRING);
            $descripcion = filter_input(INPUT_POST, 'descripcionele', FILTER_SANITIZE_STRING);
            $caracteristicas = filter_input(INPUT_POST, 'caracteristicasele', FILTER_SANITIZE_STRING);

            // Preparar los datos para enviarlos al modelo
            $datosElemento = [
                'nombreele' => $nombre,
                'cantidadele' => $cantidad,
                'codigoele' => $codigo,
                'codigoinventario' => $codigoInventario,
                'estado' => $estado,
                'estadoelemento' => $estadoElemento,
                'descripcionele' => $descripcion,
                'caracteristicasele' => $caracteristicas,
                'imagen' => $_FILES['imagen'] // Se manejará la subida de imagen en el modelo
            ];

            // Llamar al método del modelo para crear el elemento
            if ($this->elementoModel->crearElemento($datosElemento)) {
                // Si se crea con éxito, guardar un mensaje de éxito en la sesión
                Session::set('mensaje', 'Elemento agregado exitosamente.');
                Session::set('tipo_mensaje', 'success');
                // Redirigir a la página de inventario
                $this->redirect('almacen/inventario');
            } else {
                // Si falla, guardar un mensaje de error en la sesión
                Session::set('mensaje', 'Error al agregar el elemento.');
                Session::set('tipo_mensaje', 'danger');
                // Redirigir de vuelta al formulario de agregar
                $this->redirect('almacen/agregarElemento');
            }
        } else {
            // Si la petición es GET, preparar los datos para la vista
            $data = [
                'titulo' => 'Agregar Nuevo Elemento',
                'nombreele' => '',
                'cantidadele' => '',
                'codigoele' => '',
                'codigoinventario' => '',
                'estado' => 'activo',
                'estadoelemento' => 'disponible',
                'descripcionele' => '',
                'caracteristicasele' => ''
            ];
            // Cargar la vista del formulario
            $this->view('panel-almacenes/agregar-elemento', $data);
        }
    }
}

?>