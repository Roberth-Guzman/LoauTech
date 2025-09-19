<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $data['titulo']; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="w-full max-w-md bg-white rounded-lg shadow-md p-8">
        <div class="text-center mb-6">
            <img src="<?php echo BASE_URL; ?>/public/img/logo_loautech.png" alt="Logo Loautech" class="mx-auto h-16 w-auto">
            <h2 class="mt-4 text-2xl font-bold text-gray-900">
                Restablecer tu Contraseña
            </h2>
        </div>

        <form action="<?php echo BASE_URL; ?>/login/resetPassword/<?php echo $data['token']; ?>" method="POST">
            <input type="hidden" name="token" value="<?php echo $data['token']; ?>">

            <?php if (!empty($data['error'])): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline"><?php echo $data['error']; ?></span>
                </div>
            <?php endif; ?>

            <div class="mb-4">
                <label for="password" class="block text-sm font-medium text-gray-700">Nueva Contraseña</label>
                <input type="password" id="password" name="password" required
                       class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
            </div>

            <div class="mb-6">
                <label for="password_confirm" class="block text-sm font-medium text-gray-700">Confirmar Nueva Contraseña</label>
                <input type="password" id="password_confirm" name="password_confirm" required
                       class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
            </div>

            <div>
                <button type="submit"
                        class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Cambiar Contraseña
                </button>
            </div>
        </form>
    </div>
</body>
</html>