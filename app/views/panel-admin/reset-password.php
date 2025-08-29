<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $data['titulo']; ?> - Panel de Administración</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-100 font-sans leading-normal tracking-normal">

    <div class="flex min-h-screen">
        
        <?php require_once __DIR__ . '/includes/sidebar-admin.php'; ?>

        <div class="flex-1 p-10">
            <main class="bg-white rounded-lg shadow-lg p-8">
                <h1 class="text-3xl font-bold mb-6 text-gray-800"><?php echo $data['titulo']; ?></h1>

                <div class="max-w-md mx-auto">
                    
                    <?php if (!empty($data['success'])): ?>
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline"><?php echo $data['success']; ?></span>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($data['error'])): ?>
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline"><?php echo $data['error']; ?></span>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($data['usuarios'])): ?>
                        <form method="POST" action="<?php echo BASE_URL; ?>/admin/resetPassword" class="space-y-6">
                            <div>
                                <label for="usuario_id" class="block text-sm font-medium text-gray-700">Seleccione un Usuario</label>
                                <select id="usuario_id" name="usuario_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                    <?php foreach ($data['usuarios'] as $usuario): ?>
                                        <option value="<?php echo htmlspecialchars($usuario->IDper); ?>">
                                            <?php echo htmlspecialchars($usuario->nombrecompletoper); ?> (<?php echo htmlspecialchars($usuario->email); ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <i class="fas fa-key mr-2"></i> Restablecer Contraseña
                            </button>
                            
                            <p class="text-center text-sm text-gray-500 mt-4">
                                La contraseña se restablecerá a: <strong class="font-mono text-indigo-600">temp123</strong>
                            </p>
                        </form>
                    <?php else: ?>
                        <div class="text-center py-8 px-4 border-2 border-dashed border-gray-300 rounded-lg">
                            <i class="fas fa-users-slash fa-3x text-gray-400 mb-4"></i>
                            <h3 class="text-lg font-medium text-gray-700">No hay usuarios disponibles</h3>
                            <p class="text-sm text-gray-500 mt-1">No se encontraron usuarios para restablecer la contraseña en este momento.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </main>
        </div>
    </div>

</body>
</html>