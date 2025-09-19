<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Administrador</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-100">

    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <?php require_once __DIR__ . '/includes/sidebar-admin.php'; ?>

        <!-- Contenido Principal -->
        <div class="flex-1 p-10">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-3xl font-bold">Crear Nuevo Administrador</h1>
                <a href="<?php echo BASE_URL; ?>/admin/gestionarAdmin" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    <i class="fas fa-arrow-left mr-2"></i> Volver a Administradores
                </a>
            </div>

            <div class="bg-white shadow-md rounded-lg p-8"> 
            
                <?php if (!empty($data['error'])): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
                        <strong class="font-bold">Error:</strong>
                        <span class="block sm:inline"><?php echo htmlspecialchars($data['error']); ?></span>
                    </div>
                <?php endif; ?>

                <form action="<?php echo BASE_URL; ?>/admin/crearAdmin" method="POST" class="space-y-6">
                    
                    <div>
                        <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre Completo</label>
                        <input type="text" name="nombre" id="nombre" required class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" value="<?php echo htmlspecialchars($data['nombre'] ?? ''); ?>">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="tipo_documento" class="block text-sm font-medium text-gray-700">Tipo de Documento</label>
                            <select name="tipo_documento" id="tipo_documento" required class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <option value="">Seleccione...</option>
                                <option value="CC" <?php echo (isset($data['tipo_documento']) && $data['tipo_documento'] == 'CC') ? 'selected' : ''; ?>>Cédula de Ciudadanía (CC)</option>
                                <option value="TI" <?php echo (isset($data['tipo_documento']) && $data['tipo_documento'] == 'TI') ? 'selected' : ''; ?>>Tarjeta de Identidad (TI)</option>
                            </select>
                        </div>
                        <div>
                            <label for="numero_documento" class="block text-sm font-medium text-gray-700">Número de Documento</label>
                            <input type="number" name="numero_documento" id="numero_documento" required class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" value="<?php echo htmlspecialchars($data['numero_documento'] ?? ''); ?>">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">Correo Electrónico</label>
                            <input type="email" name="email" id="email" required class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" value="<?php echo htmlspecialchars($data['email'] ?? ''); ?>">
                        </div>
                        <div>
                            <label for="telefono" class="block text-sm font-medium text-gray-700">Teléfono</label>
                            <input type="tel" name="telefono" id="telefono" required class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" value="<?php echo htmlspecialchars($data['telefono'] ?? ''); ?>">
                        </div>
                    </div>

                    <div>
                        <label for="direccion" class="block text-sm font-medium text-gray-700">Dirección</label>
                        <input type="text" name="direccion" id="direccion" required class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" value="<?php echo htmlspecialchars($data['direccion'] ?? ''); ?>">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700">Contraseña</label>
                            <input type="password" name="password" id="password" required minlength="8" class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <p class="mt-2 text-sm text-gray-500">Debe tener al menos 8 caracteres.</p>
                        </div>
                        <div>
                            <label for="confirm_password" class="block text-sm font-medium text-gray-700">Confirmar Contraseña</label>
                            <input type="password" name="confirm_password" id="confirm_password" required class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>
                    </div>

                    <!-- Rol (automático como admin) -->
                    <input type="hidden" name="rol" value="admin">

                    <!-- Botón de envío -->
                    <div class="flex justify-end pt-4">
                        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <i class="fas fa-save mr-2"></i> Crear Administrador
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>

</body>
</html>