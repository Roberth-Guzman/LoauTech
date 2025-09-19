<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Perfil - Panel de Almacén</title>
    <!-- Tailwind CSS y Bootstrap Icons -->
    <script src="https://cdn.tailwindcss.com"></script>
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body class="bg-gray-100">
<?php require_once __DIR__ . '/includes/navbar.php'; ?>
<div class="flex">
    
    <!-- Contenido Principal -->
    <div class="flex-1 p-6 md:p-10">
        <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-lg p-6">
            
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-gray-800">Editar Perfil</h1>
                <a href="<?php echo BASE_URL; ?>/almacen/perfil" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded transition duration-300">
                    Cancelar
                </a>
            </div>

            <!-- Formulario de Edición -->
            <form action="<?php echo BASE_URL; ?>/almacen/editarPerfil" method="POST">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    <!-- Campo Nombre Completo -->
                    <div class="mb-4">
                        <label for="nombre" class="block text-gray-700 text-sm font-bold mb-2">Nombre Completo:</label>
                        <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($data['usuario']->nombrecompletoper); ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                    </div>

                    <!-- Campo Documento (Solo Lectura) -->
                    <div class="mb-4">
                        <label for="documento" class="block text-gray-700 text-sm font-bold mb-2">Documento:</label>
                        <input type="text" id="documento" name="documento" value="<?php echo htmlspecialchars($data['usuario']->numerodoc); ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight bg-gray-200" readonly>
                    </div>

                    <!-- Campo Correo Electrónico -->
                    <div class="mb-4">
                        <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Correo Electrónico:</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($data['usuario']->correocont); ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                    </div>

                    <!-- Campo Teléfono -->
                    <div class="mb-4">
                        <label for="telefono" class="block text-gray-700 text-sm font-bold mb-2">Teléfono:</label>
                        <input type="tel" id="telefono" name="telefono" value="<?php echo htmlspecialchars($data['usuario']->numerocont); ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>

                </div>

                <!-- Botón de Guardar -->
                <div class="mt-6 flex justify-end">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded focus:outline-none focus:shadow-outline transition duration-300">
                        Guardar Cambios
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

</body>
</html>