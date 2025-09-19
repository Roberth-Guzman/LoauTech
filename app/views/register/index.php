<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Registro - Loautech</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" href="<?php echo BASE_URL; ?>/public/img/logo_loautech_white.png" type="image/x-icon">
</head>

<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-4xl">
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="bg-blue-600 py-6 px-8 text-center relative">
                <!-- Botón de volver al inicio -->
                <a href="<?= BASE_URL ?>"
                    class="absolute left-6 top-1/2 transform -translate-y-1/2 flex items-center text-white hover:text-blue-200 text-sm"
                    title="Volver al inicio">
                    <i class="fas fa-arrow-left mr-2 text-lg"></i>
                    <span class="font-medium">HOME</span>
                </a>

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

                <form method="POST" action="<?= BASE_URL ?>registro/guardar" class="grid grid-cols-1 md:grid-cols-2 gap-6" id="formRegistro">
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
                            <input type="password" name="password" id="contrasena" required minlength="8"
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
                            <input type="password" name="confirm_password" id="confirmar_contrasena" required minlength="8"
                                class="w-full pl-10 pr-3 py-2 border rounded-lg" placeholder="Repite la contraseña">
                        </div>
                    </div>

                     <!-- Rol (oculto) -->
                    <input type="hidden" name="rol" value="1">

                    <!-- Términos y Condiciones -->
                    <div class="md:col-span-2 mt-4 space-y-4">
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input id="aceptar_terminos" name="aceptar_terminos" type="checkbox" required
                                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="aceptar_terminos" class="font-medium text-gray-700">
                                    Acepto los <a href="<?= BASE_URL ?>terminos/index.php" target="_blank" class="text-blue-600 hover:text-blue-800 hover:underline">Términos y Condiciones</a> de uso
                                </label>
                                <p class="text-gray-500">Al marcar esta casilla, confirmas que has leído y aceptas nuestros términos y condiciones de uso.</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input id="aceptar_cookies" name="aceptar_cookies" type="checkbox" required
                                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="aceptar_cookies" class="font-medium text-gray-700">
                                    Acepto el uso de cookies
                                </label>
                                <p class="text-gray-500">Utilizamos cookies para mejorar tu experiencia en nuestro sitio.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Botón de registro -->
                    <div class="md:col-span-2 mt-6">
                        <button type="submit"
                            class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Registrarse
                        </button>
                    </div>
                </form>

                <!-- Más visible -->
                <div class="mt-8 text-center">
                    <p class="text-base text-gray-700">
                        ¿Ya tienes cuenta?
                        <a href="/mvc_dev/login" class="text-blue-600 font-semibold hover:text-blue-500 transition">
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

    <script src="<?= BASE_URL ?>public/js/terminos.js"></script>

    <script>
        document.getElementById('formRegistro').addEventListener('submit', function (e) {
            const contrasena = document.getElementById('contrasena').value;
            const confirmarContrasena = document.getElementById('confirmar_contrasena').value;
            const terminosAceptados = document.getElementById('aceptar_terminos').checked;
            const cookiesAceptadas = document.getElementById('aceptar_cookies').checked;

            // Validar contraseñas
            if (contrasena !== confirmarContrasena) {
                e.preventDefault();
                alert('Las contraseñas no coinciden');
                return false;
            }

            // Validar términos y condiciones
            if (!terminosAceptados) {
                e.preventDefault();
                alert('Debe aceptar los términos y condiciones para continuar con el registro.');
                return false;
            }

            // Validar cookies
            if (!cookiesAceptadas) {
                e.preventDefault();
                alert('Debe aceptar el uso de cookies para continuar con el registro.');
                return false;
            }

            return true;
        });
    </script>
</body>

</html>