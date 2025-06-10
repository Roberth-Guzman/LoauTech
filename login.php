<?php
session_start();
session_destroy();
session_start();

require_once 'conexion.php';

$rolesValidos = ['admin', 'porteria', 'usuario'];

function redirigirSegunRol($rolesValidos)
{
    if (empty($_SESSION['usuario']['rol'])) {
        header('Location: login.php');
        exit();
    }

    if (!in_array($_SESSION['usuario']['rol'], $rolesValidos)) {
        error_log("Intento de acceso con rol inválido: " . $_SESSION['usuario']['rol']);
        header('Location: login.php?error=rol_invalido');
        exit();
    }

    $rutas = [
        'admin' => 'panel-admin/panel-principal.php',
        'porteria' => 'panel-porteria/panel-principal.php',
        'usuario' => 'panel-usuario/panel-principal.php'
    ];

    $ruta = $rutas[$_SESSION['usuario']['rol']] ?? 'login.php';

    if (!file_exists($ruta)) {
        error_log("Archivo de panel no encontrado: $ruta");
        header('Location: login.php?error=panel_no_encontrado');
        exit();
    }

    header("Location: $ruta");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $documento = filter_input(INPUT_POST, 'numerodoc', FILTER_SANITIZE_NUMBER_INT);

    $stmt = $conn->prepare("SELECT c.*, p.IDper, p.nombrecompletoper, r.rol 
                            FROM cuentas c
                            JOIN personas p ON c.numerodoc = p.numerodoc
                            JOIN roles r ON p.IDper = r.idper
                            WHERE c.numerodoc = ? AND c.estadocue = 'activo'");

    $stmt->bind_param("i", $documento);
    $stmt->execute();
    $usuario = $stmt->get_result()->fetch_assoc();

    if ($usuario && password_verify($_POST['contrasena'], $usuario['contracue'])) {
        $_SESSION['usuario'] = [
            'IDper' => $usuario['IDper'],
            'nombre' => $usuario['nombrecompletoper'],
            'documento' => $usuario['numerodoc'],
            'rol' => $usuario['rol']
        ];

        error_log("Login exitoso para usuario: " . $usuario['numerodoc'] . " rol: " . $usuario['rol']);
        redirigirSegunRol($rolesValidos);
    } else {
        error_log("Intento fallido de login para documento: " . ($documento ?? ''));
        $error = "Credenciales incorrectas";
    }
}

if (!empty($_SESSION['usuario'])) {
    redirigirSegunRol($rolesValidos);
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Loatech</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .shake {
            animation: shake 0.5s;
        }

        @keyframes shake {

            0%,
            100% {
                transform: translateX(0);
            }

            25% {
                transform: translateX(-5px);
            }

            75% {
                transform: translateX(5px);
            }
        }
    </style>
</head>

<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <!-- Encabezado con logo -->
            <div class="bg-blue-600 py-6 px-8 text-center">
                <h1 class="text-2xl font-bold text-white">LOAUTECH</h1>
                <p class="text-blue-100 mt-1">Sistema de gestión integral</p>
            </div>

            <!-- Contenedor del formulario -->
            <div class="p-8">
                <h2 class="text-2xl font-bold text-gray-800 text-center mb-6">Iniciar Sesión</h2>

                <?php if (isset($error)): ?>
                    <div id="error-message" class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            <span><?php echo htmlspecialchars($error); ?></span>
                        </div>
                    </div>
                <?php endif; ?>

                <form method="POST" action="login.php" class="space-y-6">
                    <div>
                        <label for="numerodoc" class="block text-sm font-medium text-gray-700 mb-1">Número de
                            Documento</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-id-card text-gray-400"></i>
                            </div>
                            <input type="number" id="numerodoc" name="numerodoc" required
                                class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Ej: 123456789" min="1">
                        </div>
                    </div>

                    <div>
                        <label for="contrasena" class="block text-sm font-medium text-gray-700 mb-1">Contraseña</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-gray-400"></i>
                            </div>
                            <input type="password" id="contrasena" name="contrasena" required
                                class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Ingresa tu contraseña" minlength="8">
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input id="remember-me" name="remember-me" type="checkbox"
                                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="remember-me" class="ml-2 block text-sm text-gray-700">
                                Recordarme
                            </label>
                        </div>

                        <div class="text-sm">
                            <a href="#" class="font-medium text-blue-600 hover:text-blue-500">
                                ¿Olvidaste tu contraseña?
                            </a>
                        </div>
                    </div>

                    <div>
                        <button type="submit"
                            class="w-full flex justify-center items-center py-2 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <i class="fas fa-sign-in-alt mr-2"></i>
                            Ingresar
                        </button>
                    </div>
                </form>

                <div class="mt-5 text-center">
                    <p class="text-sm text-gray-600">
                        ¿No tienes una cuenta?
                        <a href="registro.php" class="font-medium text-blue-600 hover:text-blue-500">
                            Regístrate aquí
                        </a>
                    </p>

                    <a href="index.php"
                        class="inline-flex flex-col items-center justify-center mt-4 text-blue-600 hover:text-blue-800 text-sm">
                        <i class="fas fa-arrow-left text-lg"></i>
                    </a>
                </div>
            </div>

            <!-- Pie de página -->
            <div class="bg-gray-50 px-8 py-4 text-center">
                <p class="text-xs text-gray-500">
                    &copy; <?php echo date('Y'); ?> LOAUTECH. Todos los derechos reservados.
                </p>
            </div>
        </div>
    </div>

    <script>
        <?php if (isset($error)): ?>
            document.getElementById('error-message').classList.add('shake');
        <?php endif; ?>

        document.querySelector('form').addEventListener('submit', function (e) {
            const password = document.getElementById('contrasena');
            if (password.value.length < 8) {
                e.preventDefault();
                alert('La contraseña debe tener al menos 8 caracteres');
                password.focus();
            }
        });
    </script>
</body>

</html>