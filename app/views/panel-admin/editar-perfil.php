<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($data['titulo']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <?php require_once __DIR__ . '/includes/sidebar-admin.php'; ?>

        <!-- Contenido Principal -->
        <div class="flex-1 p-10">
            <h1 class="text-3xl font-bold mb-6"><?= htmlspecialchars($data['titulo']) ?></h1>

            <?php if(isset($_SESSION['mensaje'])): ?>
                <div class="bg-<?= $_SESSION['tipo_mensaje'] == 'success' ? 'green' : 'red' ?>-100 border-l-4 border-<?= $_SESSION['tipo_mensaje'] == 'success' ? 'green' : 'red' ?>-500 text-<?= $_SESSION['tipo_mensaje'] == 'success' ? 'green' : 'red' ?>-700 p-4 mb-4" role="alert">
                    <p><?= $_SESSION['mensaje'] ?></p>
                </div>
                <?php 
                    unset($_SESSION['mensaje']);
                    unset($_SESSION['tipo_mensaje']);
                endif; 
            ?>

            <div class="bg-white shadow-md rounded-lg p-8 max-w-2xl mx-auto">
                <form action="<?= BASE_URL ?>/admin/editarPerfil" method="POST" enctype="multipart/form-data">
                    <div class="mb-4">
                        <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre Completo</label>
                        <input type="text" id="nombre" name="nombre" value="<?= htmlspecialchars($data['usuario']->nombrecompletoper) ?>" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div class="mb-4">
                        <label for="email" class="block text-sm font-medium text-gray-700">Correo Electrónico</label>
                        <input type="email" id="email" name="email" value="<?= htmlspecialchars($data['usuario']->correocont) ?>" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div class="mb-4">
                        <label for="telefono" class="block text-sm font-medium text-gray-700">Teléfono</label>
                        <input type="tel" id="telefono" name="telefono" value="<?= htmlspecialchars($data['usuario']->numerocont ?? '') ?>" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div class="mb-6">
                        <label for="avatar" class="block text-sm font-medium text-gray-700">Foto de Perfil</label>
                        <div class="mt-2 flex items-center space-x-4">
                            <img id="avatar-preview" src="<?= BASE_URL ?>/uploads/avatars/<?= htmlspecialchars($data['usuario']->avatar ?? 'default.png') ?>" alt="Vista previa del avatar" class="h-24 w-24 rounded-full object-cover">
                            <input type="file" id="avatar" name="avatar" class="block w-full text-sm text-gray-500
                                file:mr-4 file:py-2 file:px-4
                                file:rounded-full file:border-0
                                file:text-sm file:font-semibold
                                file:bg-blue-50 file:text-blue-700
                                hover:file:bg-blue-100"
                                onchange="previewAvatar(event)">
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end">
                        <a href="<?= BASE_URL ?>/admin/perfil" class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600 mr-4">
                            Cancelar
                        </a>
                        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                            <i class="fas fa-save mr-2"></i> Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('avatar').addEventListener('change', function(event) {
            const [file] = event.target.files;
            if (file) {
                document.getElementById('avatar-preview').src = URL.createObjectURL(file);
            }
        });
    </script>
</body>
</html>