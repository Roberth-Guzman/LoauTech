<?php
session_start();
include 'conexion.php';

if (isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tipoIdentidad = $conn->real_escape_string($_POST['tipoIdentidad']);
    $numeroIdentidad = $conn->real_escape_string($_POST['numeroIdentidad']);
    $nombre = $conn->real_escape_string($_POST['nombre']);
    $correo = $conn->real_escape_string($_POST['correo']);
    $telefono = $conn->real_escape_string($_POST['telefono']);
    $direccion = $conn->real_escape_string($_POST['direccion']);
    $contrasena = password_hash($_POST['contrasena'], PASSWORD_DEFAULT);
    $rol = (int) $_POST['rol'];

    $conn->begin_transaction();

    try {
        $sqlPersona = "INSERT INTO personas (nombrecompletoper, tipodocumento, numerodoc) VALUES (?, ?, ?)";
        $stmtPersona = $conn->prepare($sqlPersona);
        $stmtPersona->bind_param("sss", $nombre, $tipoIdentidad, $numeroIdentidad);
        $stmtPersona->execute();
        $idPersona = $conn->insert_id;
        $stmtPersona->close();

        $roles = [1 => 'usuario', 2 => 'porteria', 3 => 'admin', 4 => 'cuentadante', 5 => 'almacenes'];
        $rolNombre = $roles[$rol] ?? 'usuario';

        $sqlRol = "INSERT INTO roles (rol, estadorol, idper) VALUES (?, 'activo', ?)";
        $stmtRol = $conn->prepare($sqlRol);
        $stmtRol->bind_param("si", $rolNombre, $idPersona);
        $stmtRol->execute();
        $stmtRol->close();

        $sqlCuenta = "INSERT INTO cuentas (numerodoc, contracue, estadocue) VALUES (?, ?, 'activo')";
        $stmtCuenta = $conn->prepare($sqlCuenta);
        $stmtCuenta->bind_param("ss", $numeroIdentidad, $contrasena);
        $stmtCuenta->execute();
        $stmtCuenta->close();

        $sqlContacto = "INSERT INTO contactos (numerocont, direccioncont, correocont, estadocont, IDperso) VALUES (?, ?, ?, 'activo', ?)";
        $stmtContacto = $conn->prepare($sqlContacto);
        $stmtContacto->bind_param("sssi", $telefono, $direccion, $correo, $idPersona);
        $stmtContacto->execute();
        $stmtContacto->close();

        $conn->commit();

        $_SESSION['usuario'] = [
            'id' => $idPersona,
            'nombre' => $nombre,
            'documento' => $numeroIdentidad,
            'rol' => $rolNombre
        ];

        $redirecciones = [
            'admin' => 'panel-admin/panel-principal.php',
            'porteria' => 'panel-porteria/panel-principal.php',
            'usuario' => 'panel-usuario/panel-principal.php',
            'cuentadante' => 'panel-almacenes/gestion-peticiones/panel-peticiones.php',
            'almacenes' => 'panel-almacenes/inventario/panel-solicitudes.php'
        ];
        $ruta = $redirecciones[$rolNombre] ?? 'login.php?registro=exito';

        if (!file_exists($ruta)) {
            $ruta = 'login.php?registro=exito';
        }

        header("Location: $ruta");
        exit();

    } catch (Exception $e) {
        $conn->rollback();
        $error = "Error en el registro: " . $e->getMessage();
        error_log($error);
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Registro - Loautech</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-4xl">
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="bg-blue-600 py-6 px-8 text-center relative">
                <!-- Flechita + texto "Home" a la izquierda -->
                <a href="index.php"
                    class="absolute left-6 top-1/2 transform -translate-y-1/2 flex items-center text-white hover:text-blue-200 text-sm"
                    title="Volver al inicio">
                    <i class="fas fa-arrow-left mr-2 text-lg"></i>
                    <span class="font-medium">HOME</span>
                </a>

                <!-- Título centrado -->
                <h1 class="text-2xl font-bold text-white">LOAUTECH</h1>
            </div>

            <div class="p-8">
                <h2 class="text-2xl font-bold text-gray-800 text-center mb-6">Registro de Usuario</h2>

                <?php if (isset($error)): ?>
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            <span><?= htmlspecialchars($error) ?></span>
                        </div>
                    </div>
                <?php endif; ?>

                <form method="POST" action="" class="grid grid-cols-1 md:grid-cols-2 gap-6" id="formRegistro">
                    <!-- Tipo y número de identidad -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de identidad *</label>
                        <div class="relative">
                            <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                                <i class="fas fa-id-card"></i>
                            </div>
                            <select name="tipoIdentidad" required class="w-full pl-10 pr-3 py-2 border rounded-lg">
                                <option value="" disabled selected>Seleccione...</option>
                                <option value="CC">Cédula de Ciudadanía</option>
                                <option value="TI">Tarjeta de Identidad</option>
                                <option value="CE">Cédula de Extranjería</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Número de identidad *</label>
                        <div class="relative">
                            <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                                <i class="fas fa-hashtag"></i>
                            </div>
                            <input type="text" name="numeroIdentidad" required pattern="[0-9]{6,12}"
                                class="w-full pl-10 pr-3 py-2 border rounded-lg" placeholder="Ej: 1234567890">
                        </div>
                    </div>

                    <!-- Nombre y correo -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre completo *</label>
                        <div class="relative">
                            <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                                <i class="fas fa-user"></i>
                            </div>
                            <input type="text" name="nombre" required class="w-full pl-10 pr-3 py-2 border rounded-lg"
                                placeholder="Nombre completo">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Correo electrónico *</label>
                        <div class="relative">
                            <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <input type="email" name="correo" required class="w-full pl-10 pr-3 py-2 border rounded-lg"
                                placeholder="correo@ejemplo.com">
                        </div>
                    </div>

                    <!-- Teléfono y dirección -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono *</label>
                        <div class="relative">
                            <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                                <i class="fas fa-phone"></i>
                            </div>
                            <input type="tel" name="telefono" required pattern="[0-9]{10,15}"
                                class="w-full pl-10 pr-3 py-2 border rounded-lg" placeholder="Ej: 3001234567">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Dirección *</label>
                        <div class="relative">
                            <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <input type="text" name="direccion" required
                                class="w-full pl-10 pr-3 py-2 border rounded-lg" placeholder="Ej: Calle 10 #20-30">
                        </div>
                    </div>

                    <!-- Contraseña y confirmar -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Contraseña *</label>
                        <div class="relative">
                            <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                                <i class="fas fa-lock"></i>
                            </div>
                            <input type="password" name="contrasena" id="contrasena" required minlength="8"
                                class="w-full pl-10 pr-3 py-2 border rounded-lg" placeholder="Crea una contraseña">
                        </div>
                        <div class="text-xs text-gray-500 mt-1">Mínimo 8 caracteres</div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Confirmar contraseña *</label>
                        <div class="relative">
                            <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                                <i class="fas fa-lock"></i>
                            </div>
                            <input type="password" id="confirmar_contrasena" required minlength="8"
                                class="w-full pl-10 pr-3 py-2 border rounded-lg" placeholder="Repite la contraseña">
                        </div>
                    </div>

                    <!-- Rol y botón -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de usuario *</label>
                        <div class="relative">
                            <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                                <i class="fas fa-user-tag"></i>
                            </div>
                            <select name="rol" required class="w-full pl-10 pr-3 py-2 border rounded-lg">
                                <option value="1">Usuario Normal</option>
                                <option value="2">Portería</option>
                                <option value="3">Administrador</option>
                                <option value="4">cuentadante</option>
                                <option value="5">almacenes</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex items-end justify-end pt-4">
                        <button type="submit"
                            class="w-full flex justify-center items-center py-3 px-4 rounded-lg text-white bg-blue-600 hover:bg-blue-700 transition">
                            <i class="fas fa-user-plus mr-2"></i> Registrarse
                        </button>
                    </div>
                </form>

                <!-- Más visible -->
                <div class="mt-8 text-center">
                    <p class="text-base text-gray-700">
                        ¿Ya tienes cuenta?
                        <a href="login.php" class="text-blue-600 font-semibold hover:text-blue-500 transition">
                            Inicia sesión aquí
                        </a>
                    </p>
                </div>
            </div>

            <div class="bg-gray-50 px-8 py-4 text-center">
                <p class="text-xs text-gray-500">
                    &copy; <?= date('Y') ?> LOAUTECH. Todos los derechos reservados.
                </p>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('formRegistro').addEventListener('submit', function (e) {
            const contrasena = document.getElementById('contrasena').value;
            const confirmar = document.getElementById('confirmar_contrasena').value;

            if (contrasena !== confirmar) {
                e.preventDefault();
                alert('Las contraseñas no coinciden.');
            }
        });
    </script>
</body>

</html>