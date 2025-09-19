<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
class LoginController extends Controller
{
    private $userModel;

    public function __construct()
    {
        $this->userModel = $this->model('User');
    }

    public function index()
    {
        // Si ya hay usuario logueado, redirigir
        if (isset($_SESSION['user_id'])) {
            $url_actual = $_SERVER['REQUEST_URI'];
            // Solo redirigir si NO estamos ya en la página de login
            if (strpos($url_actual, 'login') === false) {
                $this->redirigirSegunRol($_SESSION['user_role']);
                return;
            }
        }

        $data = [
            'error' => null,
            'titulo' => 'Login - Loautech'
        ];

        // Procesar login si se envió el formulario
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->procesarLogin();
            return;
        }

        $this->view('login/index', $data);
    }

    public function forgotPassword()
    {
        $data = [
            'titulo' => 'Recuperar Contraseña',
            'error' => '',
            'success' => ''
        ];
        $this->view('login/forgot_password', $data);
    }

    public function enviarEmailRestablecimiento()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // --- MEJORA DE SEGURIDAD 1: LÍMITE DE INTENTOS (Rate Limiting) ---
            if (!isset($_SESSION['reset_attempts'])) {
                $_SESSION['reset_attempts'] = [];
            }
            // Limpiar intentos de más de 10 minutos
            $_SESSION['reset_attempts'] = array_filter($_SESSION['reset_attempts'], function($timestamp) {
                return (time() - $timestamp) < 600; // 10 minutos
            });

            if (count($_SESSION['reset_attempts']) >= 3) {
                $data['error'] = 'Has excedido el número de intentos. Por favor, espera 10 minutos.';
                $this->view('login/forgot_password', $data);
                return;
            }
            // --- FIN MEJORA 1 ---

            $email = trim($_POST['email']);
            $data = ['titulo' => 'Recuperar Contraseña', 'error' => '', 'success' => ''];

            if (empty($email)) {
                $data['error'] = 'Por favor, introduce tu dirección de correo electrónico.';
                $this->view('login/forgot_password', $data);
                return;
            }

            $usuario = $this->userModel->obtenerUsuarioPorEmail($email);

            if ($usuario) {
                // Generar token
                $token = bin2hex(random_bytes(50));
                // --- MEJORA DE SEGURIDAD 2: EXPIRACIÓN DE TOKEN MÁS CORTA ---
                // Token expira en 15 minutos (antes 1 hora)
                $expiracion = date('Y-m-d H:i:s', time() + 900);

                if ($this->userModel->guardarToken($usuario->IDcue, $token, $expiracion)) {
                    // Registrar el intento después de una solicitud válida
                    $_SESSION['reset_attempts'][] = time();
                    
                    // Enviar correo
                    $mail = new PHPMailer(true);
                    try {
                        // Configuración del servidor
                        $mail->isSMTP();
                        $mail->Host = 'smtp.gmail.com'; // Reemplaza con tu host SMTP
                        $mail->SMTPAuth = true;
                        $mail->Username = 'loautech.soporte@gmail.com'; // Reemplaza con tu email
                        $mail->Password = 'cepc gqdx nrjs isnt';
                        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                        $mail->Port = 587;
                        $mail->CharSet = 'UTF-8';

                        // Destinatarios
                        $mail->setFrom('no-reply@loautech.com', 'Soporte Loautech');
                        $mail->addAddress($usuario->correocont, $usuario->nombrecompletoper);

                        // Contenido
                        $mail->isHTML(true);
                        $mail->Subject = 'Restablecimiento de Contraseña - Loautech';
                        $resetLink = BASE_URL . '/login/resetPassword/' . $token;
                        $mail->Body = "Hola {$usuario->nombrecompletoper},<br><br>" .
                            "Has solicitado restablecer tu contraseña. Haz clic en el siguiente enlace para continuar:<br>" .
                            "<a href='{$resetLink}'>Restablecer Contraseña</a><br><br>" .
                            "Este enlace expirará en 1 hora.<br><br>" .
                            "Si no solicitaste esto, puedes ignorar este correo.<br><br>" .
                            "Gracias,<br>El equipo de Loautech";
                        $mail->AltBody = "Para restablecer tu contraseña, copia y pega el siguiente enlace en tu navegador: {$resetLink}";

                        $mail->send();
                    } catch (Exception $e) {
                        // Loggear el error real para depuración, pero no mostrarlo al usuario
                        error_log("Error al enviar correo: {$mail->ErrorInfo}");
                    }
                }
            }

            // Por seguridad, siempre muestra el mismo mensaje, exista o no el correo.
            $data['success'] = 'Si tu correo electrónico está en nuestros registros, recibirás un enlace para restablecer tu contraseña.';
            $this->view('login/forgot_password', $data);

        } else {
            header('Location: ' . BASE_URL . '/login/forgotPassword');
            exit();
        }
    }

    public function resetPassword($token = '')
    {
        if (empty($token)) {
            header('Location: ' . BASE_URL . '/login');
            exit();
        }

        $usuario = $this->userModel->obtenerUsuarioPorToken($token);

        if (!$usuario) {
            // Token inválido o expirado
            die('El enlace de restablecimiento no es válido o ha expirado. Por favor, solicita uno nuevo.');
        }

        $data = [
            'titulo' => 'Restablecer Contraseña',
            'token' => $token,
            'error' => '',
            'success' => ''
        ];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $password = $_POST['password'];
            $password_confirm = $_POST['password_confirm'];

            if (empty($password) || empty($password_confirm)) {
                $data['error'] = 'Ambos campos de contraseña son obligatorios.';
            } elseif ($password !== $password_confirm) {
                $data['error'] = 'Las contraseñas no coinciden.';
            // --- MEJORA DE SEGURIDAD 3: VALIDACIÓN DE CONTRASEÑA FUERTE ---
            } elseif (strlen($password) < 8) {
                $data['error'] = 'La contraseña debe tener al menos 8 caracteres.';
            } elseif (!preg_match('/[A-Z]/', $password)) {
                $data['error'] = 'La contraseña debe contener al menos una letra mayúscula.';
            } elseif (!preg_match('/[a-z]/', $password)) {
                $data['error'] = 'La contraseña debe contener al menos una letra minúscula.';
            } elseif (!preg_match('/[0-9]/', $password)) {
                $data['error'] = 'La contraseña debe contener al menos un número.';
            }
            // --- FIN MEJORA 3 ---

            if (!empty($data['error'])) {
                $this->view('login/reset_password', $data);
                return;
            }

            // Actualizar contraseña y limpiar token
            if ($this->userModel->actualizarContrasenaPorId($usuario->IDcue, $password)) {
                $this->userModel->limpiarToken($usuario->IDcue);

                // --- MEJORA DE SEGURIDAD 4: NOTIFICAR CAMBIO DE CONTRASEÑA ---
                $this->enviarEmailConfirmacionCambio($usuario->correocont, $usuario->nombrecompletoper);
                // --- FIN MEJORA 4 ---

                // Redirigir al login con mensaje de éxito
                header('Location: ' . BASE_URL . '/login?reset=success');
                exit();
            } else {
                $data['error'] = 'Hubo un error al actualizar la contraseña. Inténtalo de nuevo.';
                $this->view('login/reset_password', $data);
            }

        } else {
            $this->view('login/reset_password', $data);
        }
    }

    private function procesarLogin()
    {
        $documento = trim($_POST['numerodoc'] ?? '');
        $contrasena = $_POST['contrasena'] ?? '';

        $data = [
            'error' => null,
            'titulo' => 'Login - Loautech'
        ];

        if (empty($documento) || empty($contrasena)) {
            $data['error'] = "Por favor ingresa número de documento y contraseña.";
            $this->view('login/index', $data);
            return;
        }

        $usuario = $this->userModel->obtenerUsuarioActivoPorDocumento($documento);

        if (!$usuario || !password_verify($contrasena, $usuario->contracue)) {
            $data['error'] = "Documento o contraseña incorrectos.";
            $this->view('login/index', $data);
            return;
        }

        // Guardar datos del usuario en sesión 
        $_SESSION['user_id'] = $usuario->IDper;
        $_SESSION['rol_id'] = $usuario->rol_id;
        $_SESSION['user_role'] = $usuario->rol;
        $_SESSION['nombre'] = $usuario->nombrecompletoper;
        $_SESSION['user_documento'] = $usuario->numerodoc;
        $_SESSION['user_telefono'] = $usuario->numerocont;

        // Redirigir según rol
        $this->redirigirSegunRol($usuario->rol);
    }

    private function redirigirSegunRol($rol)
    {
        $rutas = [
            'admin' => 'admin/index',
            'porteria' => 'porteria/porteria/panelPrincipal',
            'usuario' => 'usuario/panelPrincipal',
            'cuentadante' => 'cuentadante/panelPrincipal',
            'almacenes' => 'almacen/almacen/panelPrincipal'
        ];

        $ruta = $rutas[$rol] ?? 'login';

        header('Location: ' . rtrim(BASE_URL, '/') . '/' . $ruta);
        exit();
    }

    /**
     * Envía un correo de confirmación cuando la contraseña ha sido cambiada.
     */
    private function enviarEmailConfirmacionCambio($email, $nombre)
    {
        $mail = new PHPMailer(true);
        try {
            // Configuración del servidor
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'loautech.soporte@gmail.com'; // Reemplaza con tu email
            $mail->Password = 'cepc gqdx nrjs isnt'; // Reemplaza con tu contraseña de aplicación
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
            $mail->CharSet = 'UTF-8';

            // Destinatarios
            $mail->setFrom('no-reply@loautech.com', 'Soporte Loautech');
            $mail->addAddress($email, $nombre);

            // Contenido
            $mail->isHTML(true);
            $mail->Subject = 'Confirmación de Cambio de Contraseña - Loautech';
            $mail->Body = "Hola {$nombre},<br><br>" .
                "Te informamos que la contraseña de tu cuenta ha sido cambiada exitosamente.<br><br>" .
                "Si no reconoces esta actividad, por favor, contacta a nuestro equipo de soporte de inmediato.<br><br>" .
                "Gracias,<br>El equipo de Loautech";
            $mail->AltBody = "Te informamos que la contraseña de tu cuenta ha sido cambiada exitosamente. Si no reconoces esta actividad, por favor, contacta a nuestro equipo de soporte de inmediato.";

            $mail->send();
        } catch (Exception $e) {
            // No es crítico si este correo falla, así que solo lo registramos en los logs.
            error_log("Error al enviar correo de confirmación de cambio: {$mail->ErrorInfo}");
        }
    }
}