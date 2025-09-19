<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $data['titulo']; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100">

<div class="min-h-screen flex">
    <!-- Sidebar -->
    <?php require __DIR__ . '/includes/sidebar-admin.php'; ?>
    
    <!-- Main content -->
    <div class="flex-1 flex flex-col overflow-hidden">
        
        <!-- Page content -->
        <main class="flex-1 overflow-y-auto bg-gray-50 p-6">
            <div class="max-w-4xl mx-auto">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-bold text-gray-800"><?php echo $data['titulo']; ?></h1>
                    <a href="/mvc_dev/admin/gestionarAdmin" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <i class="fas fa-arrow-left mr-2"></i> Volver a Administradores
                    </a>
                </div>
                
                <!-- Mostrar mensajes de sesión -->
                <?php if(isset($_SESSION['mensaje'])): ?>
                    <div class="mb-4 p-4 rounded-md <?php echo $_SESSION['tipo_mensaje'] === 'success' ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-red-50 text-red-700 border border-red-200'; ?>">
                        <?php echo $_SESSION['mensaje']; ?>
                        <?php unset($_SESSION['mensaje'], $_SESSION['tipo_mensaje']); ?>
                    </div>
                <?php endif; ?>
                
                <!-- Formulario de edición -->
                <div class="bg-white shadow-sm rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <form method="POST" action="/mvc_dev/admin/editarAdmin/<?php echo $data['admin']->IDper; ?>" class="space-y-6">
                            <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-2">
                                <!-- Nombre completo -->
                                <div class="sm:col-span-2">
                                    <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre completo *</label>
                                    <div class="mt-1">
                                        <input type="text" name="nombre" id="nombre" value="<?php echo htmlspecialchars($data['nombre']); ?>" required
                                               class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                    </div>
                                </div>

                                <!-- Tipo de documento -->
                                <div>
                                    <label for="tipo_documento" class="block text-sm font-medium text-gray-700">Tipo de documento *</label>
                                    <div class="mt-1">
                                        <select name="tipo_documento" id="tipo_documento" required
                                                class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                            <option value="">Seleccionar tipo</option>
                                            <option value="CC" <?php echo $data['tipo_documento'] === 'CC' ? 'selected' : ''; ?>>Cédula de Ciudadanía</option>
                                            <option value="CE" <?php echo $data['tipo_documento'] === 'CE' ? 'selected' : ''; ?>>Cédula de Extranjería</option>
                                            <option value="TI" <?php echo $data['tipo_documento'] === 'TI' ? 'selected' : ''; ?>>Tarjeta de Identidad</option>
                                            <option value="PAS" <?php echo $data['tipo_documento'] === 'PAS' ? 'selected' : ''; ?>>Pasaporte</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Número de documento -->
                                <div>
                                    <label for="numero_documento" class="block text-sm font-medium text-gray-700">Número de documento *</label>
                                    <div class="mt-1">
                                        <input type="text" name="numero_documento" id="numero_documento" value="<?php echo htmlspecialchars($data['numero_documento']); ?>" required
                                               class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                    </div>
                                </div>

                                <!-- Email -->
                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700">Correo electrónico *</label>
                                    <div class="mt-1">
                                        <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($data['email']); ?>" required
                                               class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                    </div>
                                </div>

                                <!-- Teléfono -->
                                <div>
                                    <label for="telefono" class="block text-sm font-medium text-gray-700">Teléfono *</label>
                                    <div class="mt-1">
                                        <input type="tel" name="telefono" id="telefono" value="<?php echo htmlspecialchars($data['telefono']); ?>" required
                                               class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                    </div>
                                </div>

                                <!-- Dirección -->
                                <div class="sm:col-span-2">
                                    <label for="direccion" class="block text-sm font-medium text-gray-700">Dirección *</label>
                                    <div class="mt-1">
                                        <input type="text" name="direccion" id="direccion" value="<?php echo htmlspecialchars($data['direccion']); ?>" required
                                               class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                    </div>
                                </div>

                                <!-- Rol -->
                                <div>
                                    <label for="rol" class="block text-sm font-medium text-gray-700">Rol *</label>
                                    <div class="mt-1">
                                        <select name="rol" id="rol" required
                                                class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                            <?php if (!empty($data['roles'])): ?>
                                                <?php foreach ($data['roles'] as $rol): ?>
                                                    <option value="<?php echo htmlspecialchars($rol->rol); ?>" <?php echo $data['rol'] === $rol->rol ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars(ucfirst($rol->rol)); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <option value="admin" <?php echo $data['rol'] === 'admin' ? 'selected' : ''; ?>>Administrador</option>
                                                <option value="usuario" <?php echo $data['rol'] === 'usuario' ? 'selected' : ''; ?>>Usuario</option>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                </div>

                                <!-- Nueva contraseña (opcional) -->
                                <div>
                                    <label for="password" class="block text-sm font-medium text-gray-700">Nueva contraseña (opcional)</label>
                                    <div class="mt-1">
                                        <input type="password" name="password" id="password" placeholder="Dejar en blanco para mantener la actual"
                                               class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                    </div>
                                    <p class="mt-2 text-sm text-gray-500">Deja este campo vacío si no deseas cambiar la contraseña.</p>
                                </div>
                            </div>

                            <!-- Botones de acción -->
                            <div class="flex justify-end space-x-3">
                                <a href="/mvc_dev/admin/gestionarAdmin" 
                                   class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Cancelar
                                </a>
                                <button type="submit" 
                                        class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    <i class="fas fa-save mr-2"></i>
                                    Actualizar Administrador
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<script>
// Validación básica del formulario
document.querySelector('form').addEventListener('submit', function(e) {
    const nombre = document.getElementById('nombre').value.trim();
    const tipoDoc = document.getElementById('tipo_documento').value;
    const numeroDoc = document.getElementById('numero_documento').value.trim();
    const email = document.getElementById('email').value.trim();
    const telefono = document.getElementById('telefono').value.trim();
    const direccion = document.getElementById('direccion').value.trim();
    
    if (!nombre || !tipoDoc || !numeroDoc || !email || !telefono || !direccion) {
        e.preventDefault();
        alert('Por favor, completa todos los campos obligatorios.');
        return false;
    }
    
    // Validar formato de email
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        e.preventDefault();
        alert('Por favor, ingresa un email válido.');
        return false;
    }
});
</script>

</body>
</html>