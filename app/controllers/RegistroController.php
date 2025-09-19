<?php
// app/controllers/RegistroController.php

class RegistroController extends Controller {
    protected $model;

    public function __construct() {
        parent::__construct(); // Asegurarse de llamar al constructor padre
        
        // Inicializar el modelo
        $this->model = $this->model('RegistroModel');
        
        // Inicializar la sesión si no está ya iniciada
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function index() {
        $data = [
            'titulo' => 'Registro - Loautech',
            'error' => null
        ];

        $this->view('register/index', $data);
    }

    public function exito() {
        $data = [
            'titulo' => 'Registro Exitoso - Loautech',
            'mensaje' => 'Tu cuenta ha sido creada exitosamente. Ahora puedes iniciar sesión.'
        ];
        $this->view('register/exito', $data);
    }

    public function guardar() {
        error_log('Método guardar() ejecutándose. Método de solicitud: ' . $_SERVER['REQUEST_METHOD']);
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            error_log('Error: Intento de acceso al método guardar() con método ' . $_SERVER['REQUEST_METHOD']);
            $this->view('error/404');
            return;
        }
        
        error_log('Datos POST recibidos: ' . print_r($_POST, true));

        $errores = [];

        // Validar términos y condiciones
        if (!isset($_POST['aceptar_terminos']) || $_POST['aceptar_terminos'] !== 'on') {
            $errores[] = 'Debe aceptar los términos y condiciones para continuar con el registro.';
        }

        // Validar cookies
        if (!isset($_POST['aceptar_cookies']) || $_POST['aceptar_cookies'] !== 'on') {
            $errores[] = 'Debe aceptar el uso de cookies para continuar con el registro.';
        }
        
        // Validar campos obligatorios
        $campos_requeridos = [
            'nombre' => 'El nombre completo es obligatorio',
            'tipoIdentidad' => 'El tipo de identidad es obligatorio',
            'numeroIdentidad' => 'El número de identidad es obligatorio',
            'correo' => 'El correo electrónico es obligatorio',
            'telefono' => 'El teléfono es obligatorio',
            'password' => 'La contraseña es obligatoria',
            'confirm_password' => 'Debe confirmar la contraseña'
        ];

        foreach ($campos_requeridos as $campo => $mensaje) {
            if (empty($_POST[$campo])) {
                $errores[] = $mensaje;
            }
        }

        // Validar que las contraseñas coincidan
        if (isset($_POST['password']) && isset($_POST['confirm_password']) && 
            $_POST['password'] !== $_POST['confirm_password']) {
            $errores[] = 'Las contraseñas no coinciden';
        }

        // Validar formato de correo
        if (isset($_POST['correo']) && !filter_var($_POST['correo'], FILTER_VALIDATE_EMAIL)) {
            $errores[] = 'El formato del correo electrónico no es válido';
        }

        // Verificar si el correo o número de identidad ya existen
        $correo = $_POST['correo'] ?? '';
        $numero_identidad = $_POST['numeroIdentidad'] ?? '';
        
        if (empty($errores)) {
            if ($this->model->verificarCorreoExistente($correo)) {
                $errores[] = 'El correo electrónico ya está registrado';
            }

            if ($this->model->verificarIdentidadExistente($numero_identidad)) {
                $errores[] = 'El número de identidad ya está registrado';
            }

            if (empty($errores)) {
                // Preparar datos para el modelo
                $datos = [
                    'nombre' => $_POST['nombre'],
                    'tipoIdentidad' => $_POST['tipoIdentidad'],
                    'numeroIdentidad' => $numero_identidad,
                    'correo' => $correo,
                    'telefono' => $_POST['telefono'],
                    'direccion' => $_POST['direccion'] ?? 'Sin dirección',
                    'password' => $_POST['password']
                ];

                // Intentar registrar al usuario
                error_log('Intentando registrar usuario con datos: ' . print_r($datos, true));
                try {
                    $id_persona = $this->model->registrarUsuario($datos);
                    error_log('Resultado de registrarUsuario(): ' . ($id_persona ? 'Éxito, ID: ' . $id_persona : 'Fallo'));
                } catch (Exception $e) {
                    error_log('Error en registrarUsuario: ' . $e->getMessage());
                    $errores[] = 'Error al procesar el registro: ' . $e->getMessage();
                    $id_persona = false;
                }

                if ($id_persona) {
                    // Iniciar sesión automáticamente 
                    $usuario = [
                        'id' => $id_persona,
                        'nombre' => trim($_POST['nombre']),
                        'email' => $correo,
                        'rol' => 'usuario'
                    ];
                    
                    // Mostrar mensaje de éxito y redirigir al login
                    echo '<script>
                        alert("¡Registro exitoso! Ahora puedes iniciar sesión con tus credenciales.");
                        window.location.href = "' . BASE_URL . 'login";
                    </script>';
                    exit();
                } else {
                    $error = 'Error al procesar el registro. Por favor, inténtalo de nuevo.';
                    error_log($error);
                    $errores[] = $error;
                }
            }
        }
        
        // Si hay errores, volver a mostrar el formulario con los errores
        $data = [
            'titulo' => 'Error en el registro - Loautech',
            'error' => is_array($errores) ? implode('<br>', $errores) : $errores,
            'form_data' => $_POST
        ];
        $this->view('register/index', $data);
    }
}