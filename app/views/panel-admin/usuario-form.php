<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($data['titulo']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="flex min-h-screen">
        <?php require_once __DIR__ . '/includes/sidebar-admin.php'; ?>
        <div class="flex-1 p-10">
            <h1 class="text-3xl font-bold mb-6"><?= htmlspecialchars($data['titulo']) ?></h1>
            
            <?php if (!empty($data['error'])): ?>
                <div class="mb-4 p-3 rounded bg-red-100 text-red-800"><?= htmlspecialchars($data['error']) ?></div>
            <?php endif; ?>
            
            <form method="POST" class="bg-white rounded shadow p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Nombre -->
                    <div class="mb-4">
                        <label class="block text-sm text-gray-600 mb-1">Nombre completo *</label>
                        <input type="text" name="nombre" class="w-full border rounded p-2" 
                               value="<?= htmlspecialchars($data['nombre'] ?? '') ?>" required>
                    </div>
                    
                    <!-- Correo -->
                    <div class="mb-4">
                        <label class="block text-sm text-gray-600 mb-1">Correo electrónico *</label>
                        <input type="email" name="email" class="w-full border rounded p-2" 
                               value="<?= htmlspecialchars($data['email'] ?? '') ?>" required>
                    </div>
                    
                    <!-- Contraseña -->
                    <div class="mb-4">
                        <label class="block text-sm text-gray-600 mb-1">Contraseña <?= !isset($data['usuario']) ? '*' : '(Opcional)' ?></label>
                        <div class="relative">
                            <input type="password" name="password" id="password" 
                                   class="w-full border rounded p-2 pr-10" <?= !isset($data['usuario']) ? 'required minlength="6"' : '' ?>>
                            <button type="button" onclick="togglePassword('password', this)" 
                                    class="absolute right-2 top-2 text-gray-600">
                                <i class="far fa-eye"></i>
                            </button>
                        </div>
                        <?php if(isset($data['usuario'])): ?>
                            <p class="text-xs text-gray-500 mt-1">Dejar en blanco para no cambiar la contraseña.</p>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Tipo de documento -->
                    <div class="mb-4">
                        <label class="block text-sm text-gray-600 mb-1">Tipo de documento</label>
                        <select name="tipo_documento" class="w-full border rounded p-2">
                            <option value="CC" <?= (isset($data['tipo_documento']) && $data['tipo_documento'] === 'CC') ? 'selected' : '' ?>>Cédula de Ciudadanía</option>
                            <option value="TI" <?= (isset($data['tipo_documento']) && $data['tipo_documento'] === 'TI') ? 'selected' : '' ?>>Tarjeta de Identidad</option>
                            <option value="CE" <?= (isset($data['tipo_documento']) && $data['tipo_documento'] === 'CE') ? 'selected' : '' ?>>Cédula de Extranjería</option>
                        </select>
                    </div>
                    
                    <!-- Número de documento -->
                    <div class="mb-4">
                        <label class="block text-sm text-gray-600 mb-1">Número de documento</label>
                        <input type="text" name="numero_documento" class="w-full border rounded p-2" 
                               value="<?= htmlspecialchars($data['numero_documento'] ?? '') ?>">
                    </div>
                    
                    <!-- Teléfono -->
                    <div class="mb-4">
                        <label class="block text-sm text-gray-600 mb-1">Teléfono</label>
                        <input type="tel" name="telefono" class="w-full border rounded p-2" 
                               value="<?= htmlspecialchars($data['telefono'] ?? '') ?>">
                    </div>
                    
                    <!-- Dirección -->
                    <div class="mb-4 md:col-span-2">
                        <label class="block text-sm text-gray-600 mb-1">Dirección</label>
                        <input type="text" name="direccion" class="w-full border rounded p-2" 
                               value="<?= htmlspecialchars($data['direccion'] ?? '') ?>">
                    </div>
                    
                    <!-- Rol -->
                    <div class="mb-4">
                        <label class="block text-sm text-gray-600 mb-1">Rol</label>
                        <select name="rol" class="w-full border rounded p-2">
                            <?php foreach ($data['roles'] as $rol): ?>
                                <option value="<?= htmlspecialchars($rol->rol) ?>"
                                    <?= (isset($data['rol']) && $data['rol'] === $rol->rol) ? 'selected' : '' ?>>
                                    <?= ucfirst(htmlspecialchars($rol->rol)) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <!-- Botones -->
                <div class="mt-6 flex justify-end space-x-4">
                    <a href="<?= BASE_URL ?>/admin/usuarios" 
                       class="px-4 py-2 border border-gray-300 rounded text-gray-700 hover:bg-gray-50">
                        Cancelar
                    </a>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                        <?= isset($data['usuario']) ? 'Guardar Cambios' : 'Guardar Usuario' ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
    <script>
        function togglePassword(id, button) {
            const passwordInput = document.getElementById(id);
            const icon = button.querySelector('i');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        // Validar formulario
        document.querySelector('form').addEventListener('submit', function(e) {
            const password = document.getElementById('password');
            // Solo validar longitud si es un usuario nuevo (campo requerido)
            if (password.hasAttribute('required') && password.value.length < 6) {
                e.preventDefault();
                alert('La contraseña debe tener al menos 6 caracteres');
                password.focus();
            }
        });
    </script>