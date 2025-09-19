<?php
class IngresoElementoController extends Controller {
    
    public function __construct() {
        // Verificar sesión
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/login');
            exit();
        }
    }
    
    // Mostrar el formulario de registro de elementos
    public function registrar() {
        $ingresoModel = $this->model('Ingreso');
        $tipos_elementos = $ingresoModel->obtenerTiposElementos();

        $this->view('panel-usuario/registrar-elemento', [
            'titulo' => 'Registrar Nuevo Elemento',
            'active_menu' => 'registrar_elemento',
            'tipos_elementos' => $tipos_elementos
        ]);
    }
    // Procesar el formulario de registro
    public function guardar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $datos = [
                'id_elemento' => $_POST['id_elemento'] ?? null,
                'cantidad' => $_POST['cantidad'] ?? 1,
                'fecha_ingreso' => $_POST['fecha_ingreso'] ?? date('Y-m-d H:i:s'),
                'id_usuario' => $_SESSION['user_id'],
                'observaciones' => $_POST['observaciones'] ?? '',
                'estado' => 'pendiente' 
            ];
            
            if (empty($datos['id_elemento'])) {
                $_SESSION['error'] = 'Debe seleccionar un elemento';
                $this->view('panel-usuario/registrar-elemento', [
                    'titulo' => 'Registrar Nuevo Elemento',
                    'active_menu' => 'registrar_elemento',
                    'datos' => $datos
                ]);
                return;
            }
            // Cargar modelo y guardar 
            $ingresoModel = $this->model('IngresoElemento');
            
            if ($ingresoModel->guardar($datos)) {
                $_SESSION['exito'] = 'Elemento registrado exitosamente';
                header('Location: ' . BASE_URL . '/usuario/panelPrincipal');
            } else {
                $_SESSION['error'] = 'Error al registrar el elemento: ' . $ingresoModel->getError();
                $this->view('panel-usuario/registrar-elemento', [
                    'titulo' => 'Registrar Nuevo Elemento',
                    'active_menu' => 'registrar_elemento',
                    'datos' => $datos
                ]);
            }
        } else {
            header('Location: ' . BASE_URL . '/usuario/registrarElemento');
        }
    }
}
