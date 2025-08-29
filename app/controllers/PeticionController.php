<?php
class PeticionController extends Controller {

    private $elementoModel;
    private $peticionModel;

    public function __construct() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // Asegurarse de que el usuario esté logueado
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/login');
            exit();
        }

        $this->elementoModel = $this->model('Elemento');
        $this->peticionModel = $this->model('Peticion');
    }

    // Muestra el formulario para registrar la petición
    public function registrar() {
        // Validar que se pasó un ID de elemento
        if (!isset($_GET['elemento_id']) || !is_numeric($_GET['elemento_id'])) {
            header('Location: ' . BASE_URL . '/usuario/inventario?error=elemento_invalido');
            exit();
        }

        $idElemento = intval($_GET['elemento_id']);
        $elemento = $this->elementoModel->obtenerElementoPorId($idElemento);

        // Verificar que el elemento exista
        if (!$elemento) {
            header('Location: ' . BASE_URL . '/usuario/inventario?error=elemento_no_encontrado');
            exit();
        }

        $data = [
            'titulo' => 'Registrar Petición',
            'elemento' => $elemento,
            'nombre_usuario' => $_SESSION['nombre']
        ];

        $this->view('peticion/registrar', $data);
    }

    // Procesa el formulario de registro de la petición
    public function procesar() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/usuario/inventario');
            exit();
        }

        // Recolectar y sanear los datos del POST
        $data = [
            'id_elemento' => intval($_POST['idele']),
            'id_usuario' => intval($_SESSION['user_id']),
            'cantidad' => intval($_POST['cantidad_solicitada']),
            'formacionodependencia' => trim($_POST['cargo']),
            'cargopre' => trim($_POST['cargo']), // Asumiendo que 'cargo' es el campo correcto
            'lugardetraslado' => trim($_POST['lugar_uso'])
        ];

        // Validar campos
        if (empty($data['formacionodependencia']) || empty($data['cargopre']) || empty($data['lugardetraslado']) || $data['cantidad'] <= 0) {
            // Si hay un error de validación, redirigir de nuevo al formulario con un mensaje
            header('Location: ' . BASE_URL . '/peticion/registrar?elemento_id=' . $data['id_elemento'] . '&error=campos_obligatorios');
            exit();
        }

        try {
            // Llamar al método del modelo que encapsula toda la transacción
            if ($this->peticionModel->crearPeticionCompleta($data)) {
                // Si todo va bien, redirigir al inventario con mensaje de éxito
                header('Location: ' . BASE_URL . '/usuario/inventario?success=peticion_enviada');
                exit();
            } else {
                // Si el modelo devuelve false, hubo un error
                throw new Exception("No se pudo procesar la petición.");
            }
        } catch (Exception $e) {
            // Capturar cualquier excepción (ej. stock insuficiente) y redirigir con el mensaje de error
            $error_message = urlencode($e->getMessage());
            header('Location: ' . BASE_URL . '/peticion/registrar?elemento_id=' . $data['id_elemento'] . '&error=' . $error_message);
            exit();
        }
    }
}