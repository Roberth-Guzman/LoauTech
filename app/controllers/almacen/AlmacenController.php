<?php
class AlmacenController extends Controller {
    
    private $peticionModel;
    private $elementoModel;

    public function __construct() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // Verifica que el usuario tenga el rol de 'almacenes'
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'almacenes') {
            header('Location: ' . BASE_URL . '/login?error=acceso_no_autorizado');
            exit();
        }
        
        $this->peticionModel = $this->model('Peticion');
        $this->elementoModel = $this->model('Elemento');
    }

    // Muestra el panel principal con las solicitudes pendientes
    public function panelPrincipal() {
        $peticiones = $this->peticionModel->obtenerPeticionesPendientes();

        $data = [
            'titulo' => 'Solicitudes Pendientes - Loautech',
            'nombre_usuario' => $_SESSION['nombre'],
            'active_menu' => 'solicitudes',
            'peticiones' => $peticiones,
            'success' => $_GET['success'] ?? null,
            'error' => $_GET['error'] ?? null
        ];

        $this->view('panel-almacenes/panel-solicitudes', $data);
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
                // 'cantidadest' => trim($_POST['cantidadest']), // <--- LÍNEA ELIMINADA
                'codigoele' => trim($_POST['codigoele']),
                'codigoinventario' => trim($_POST['codigoinventario']),
                'descripcionele' => trim($_POST['descripcionele']),
                'caracteristicasele' => trim($_POST['caracteristicasele']),
                'estado' => trim($_POST['estado']),
                'estadoelemento' => trim($_POST['estadoelemento']),
                'imagen' => $imagen_path,
                'error' => ''
            ];

            if (empty($data['nombreele']) || empty($data['codigoele'])) {
                $data['error'] = 'El nombre y el código son obligatorios.';
                // Si hay error, recargamos la vista con los datos y el error
                $elemento = $this->elementoModel->obtenerElementoPorId($id);
                $data['page_title'] = 'Editar Elemento';
                $data['titulo'] = 'Editar Elemento: ' . $elemento->nombreele;
                $data['nombre_usuario'] = $_SESSION['nombre'];
                $data['active_menu'] = 'visualizacion';
                $data['elemento'] = $elemento;
                $this->view('panel-almacenes/editar-elemento', $data);
            } else {
                if ($this->elementoModel->actualizarElemento($data)) {
                    header('Location: ' . BASE_URL . '/almacen/almacen/detalleElemento/' . $id . '?success=actualizado');
                    exit;
                } else {
                    $data['error'] = 'Error al actualizar el elemento.';
                    $elemento = $this->elementoModel->obtenerElementoPorId($id);
                    $data['page_title'] = 'Editar Elemento';
                    $data['titulo'] = 'Editar Elemento: ' . $elemento->nombreele;
                    $data['nombre_usuario'] = $_SESSION['nombre'];
                    $data['active_menu'] = 'visualizacion';
                    $data['elemento'] = $elemento;
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

            $data = [
                'page_title' => 'Editar Elemento',
                'titulo' => 'Editar Elemento: ' . htmlspecialchars($elemento->nombreele),
                'nombre_usuario' => $_SESSION['nombre'],
                'active_menu' => 'visualizacion',
                'elemento' => $elemento,
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
}
?>