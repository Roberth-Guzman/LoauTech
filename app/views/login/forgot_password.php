<!DOCTYPE html>
<html lang="es" class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $data['titulo'] ?? 'Recuperar Contraseña'; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" href="<?php echo BASE_URL; ?>/public/img/logo_loautech_white.png" type="image/x-icon">
</head>
<body class="h-full">
<div class="min-h-full flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <img class="mx-auto h-20 w-auto" src="<?php echo BASE_URL; ?>/public/img/logo_loautech.png" alt="Loautech">
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Recuperar tu contraseña
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Ingresa tu correo y te enviaremos las instrucciones.
            </p>
        </div>

        <?php if (!empty($data['success'])): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <strong class="font-bold">¡Éxito!</strong>
                <span class="block sm:inline"><?php echo htmlspecialchars($data['success']); ?></span>
            </div>
            <div class="text-sm text-center">
                <a href="<?php echo BASE_URL; ?>/login" class="font-medium text-blue-600 hover:text-blue-500">
                    Volver al inicio de sesión
                </a>
            </div>
        <?php else: ?>
            <?php if (!empty($data['error'])): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <strong class="font-bold">Error:</strong>
                    <span class="block sm:inline"><?php echo htmlspecialchars($data['error']); ?></span>
                </div>
            <?php endif; ?>

            <form class="mt-8 space-y-6" action="<?php echo BASE_URL; ?>/login/enviarEmailRestablecimiento" method="POST">
                <input type="hidden" name="remember" value="true">
                <div class="rounded-md shadow-sm -space-y-px">
                    <div>
                        <label for="email-address" class="sr-only">Correo electrónico</label>
                        <input id="email-address" name="email" type="email" autocomplete="email" required
                               class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm"
                               placeholder="Correo electrónico">
                    </div>
                </div>

                <div>
                    <button type="submit"
                            class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Enviar enlace de recuperación
                    </button>
                </div>
            </form>
            <div class="text-sm text-center">
                <a href="<?php echo BASE_URL; ?>/login" class="font-medium text-blue-600 hover:text-blue-500">
                    Volver al login
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>
</body>
</html>