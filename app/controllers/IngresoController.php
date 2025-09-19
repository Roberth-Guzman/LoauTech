<?php

class IngresoController extends Controller {
    public function registrar() {
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            // Si no es POST, redirigir o mostrar error
            header('Location: /mvc_dev/usuario/registrarElemento');
            exit();
        }

        // Se corrige la comprobación para usar 'user_id'
        if (!isset($_SESSION['user_id'])) {

            $_SESSION['form_data'] = $_POST;
            header('Location: /mvc_dev/login?error=sesion_expirada'); 
            exit();
        }
        
        // Cargar el modelo
        $ingresoModel = $this->model('Ingreso');

        // Preparar datos para el modelo
        $datos = [
            'nombre' => $_POST['nombre'] ?? '',
            'tipo' => $_POST['tipo_elemento'] ?? '',
            'descripcion' => $_POST['descripcion'] ?? '',
            'observacion' => $_POST['observacion'] ?? '',
            'serial' => $_POST['serial'] ?? '',
            'id_persona' => $_SESSION['user_id']
        ];

        // Intentar registrar el elemento
        $resultado = $ingresoModel->registrarElemento($datos);

        if (!$resultado) {
            // Manejar el error de registro
            header('Location: /mvc_dev/usuario/registrarElemento?error=1');
            exit();
        }

        // Redirigir a la página de registro con un mensaje
        header('Location: /mvc_dev/usuario/registrarElemento?exito=1');
        exit();
    }

    public function registrarSalida() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['registro_salida_id'])) {
            
            // Se corrige la comprobación para usar 'user_id'
            if (!isset($_SESSION['user_id'])) {
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
        header('Location: /mvc_dev/usuario/misIngresos');
        exit();
    }

    public function registrarIngresoDiario() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user_id'])) {
            header('Location: /mvc_dev/login');
            exit;
        }

        $elementos = $_POST['elementos'] ?? [];
        $observaciones = $_POST['observaciones'] ?? '';
        $usuario_id = $_SESSION['user_id'];

        if (empty($elementos)) {
            header('Location: /mvc_dev/usuario/misIngresos?error=no_seleccion');
            exit;
        }

        $ingresoModel = $this->model('Ingreso');
        $errores = 0;

        foreach ($elementos as $identificador => $json_value) {
            $item = json_decode($json_value, true);
            
            $datos = [
                'nombre' => $item['nombre'],
                'tipo' => $item['tipo'],
                'descripcion' => $item['descripcion'],
                'serial' => $item['serial'],
                'observacion' => $observaciones,
                'id_persona' => $usuario_id
            ];

            if (!$ingresoModel->registrarElemento($datos)) {
                $errores++;
            }
        }

        if ($errores > 0) {
            header('Location: /mvc_dev/usuario/misIngresos?error=registro_parcial');
        } else {
            header('Location: /mvc_dev/usuario/misIngresos?exito=registro_completo');
        }
        exit;
    }
}