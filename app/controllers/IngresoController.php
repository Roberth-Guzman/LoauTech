<?php

class IngresoController extends Controller {
    public function registrar() {
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            // Si no es POST, redirigir o mostrar error
            header('Location: ' . BASE_URL . '/usuario/registrarElemento');
            exit();
        }

        session_start();

        // Validar que el usuario esté logueado
        if (!isset($_SESSION['usuario']) || !isset($_SESSION['usuario']['IDper'])) {
            // Guardar datos del formulario en sesión para no perderlos
            $_SESSION['form_data'] = $_POST;
            header('Location: ' . BASE_URL . '/login?error=sesion_expirada');
            exit();
        }

        // Recuperar datos del formulario
        $nombre = $_POST['nombre'] ?? '';
        $tipo_elemento = $_POST['tipo_elemento'] ?? '';
        $descripcion = $_POST['descripcion'] ?? '';
        $observacion = $_POST['observacion'] ?? '';
        $serial = $_POST['serial'] ?? '';
        $usuario_id = $_SESSION['usuario']['IDper'];

        // Cargar el modelo
        $ingresoModel = $this->model('Ingreso');

        // Intentar registrar el elemento
        $resultado = $ingresoModel->registrarElemento($nombre, $tipo_elemento, $descripcion, $observacion, $usuario_id, $serial);

        if (!$resultado) {
            // Manejar el error de registro
            header('Location: ' . BASE_URL . '/usuario/registrarElemento?error=1');
            exit();
        }

        // Redirigir a la página de registro con un mensaje
        header('Location: ' . BASE_URL . '/usuario/registrarElemento?exito=1');
        exit();
    }

    public function registrarSalida() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['registro_salida_id'])) {
            session_start();
            
            // Verificar que el usuario esté logueado
            if (!isset($_SESSION['usuario'])) {
                header('Location: ' . BASE_URL . '/login');
                exit();
            }

            $ingresoModel = $this->model('Ingreso');
            $id = $_POST['registro_salida_id'];

            if ($ingresoModel->registrarSalida($id)) {
                // Redirigir a "Mis Ingresos" con mensaje de éxito
                header('Location: ' . BASE_URL . '/usuario/misIngresos?salida_exitosa=1');
            } else {
                // Redirigir con mensaje de error
                header('Location: ' . BASE_URL . '/usuario/misIngresos?error_salida=1');
            }
            exit();
        }
        // Si no es POST, redirigir
        header('Location: ' . BASE_URL . '/usuario/misIngresos');
        exit();
    }
}