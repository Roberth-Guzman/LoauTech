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

            <div class="bg-white shadow-md rounded-lg p-8 max-w-2xl mx-auto">
                <div class="flex items-center space-x-6 mb-6">
                    <img class="h-24 w-24 rounded-full object-cover" src="<?= BASE_URL ?>/uploads/avatars/<?= htmlspecialchars($data['usuario']->avatar ?? 'default.png') ?>" alt="Foto de perfil">
                    <div>
                        <h2 class="text-2xl font-bold"><?= htmlspecialchars($data['usuario']->nombrecompletoper) ?></h2>
                        <p class="text-gray-500"><?= htmlspecialchars(ucfirst($data['usuario']->rol ?? 'N/A')) ?></p>
                    </div>
                </div>

                <div>
                    <h3 class="text-xl font-semibold border-b pb-2 mb-4">Información de Contacto</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="font-medium text-gray-600">Correo Electrónico</p>
                            <p><?= htmlspecialchars($data['usuario']->correocont ?? 'No especificado') ?></p>
                        </div>
                        <div>
                            <p class="font-medium text-gray-600">Teléfono</p>
                            <p><?= htmlspecialchars($data['usuario']->numerocont ?? 'No especificado') ?></p>
                        </div>
                        <div>
                            <p class="font-medium text-gray-600">Documento</p>
                            <p><?= htmlspecialchars($data['usuario']->numerodoc ?? 'No especificado') ?></p>
                        </div>
                    </div>
                </div>

                <div class="mt-8 text-right">
                    <a href="<?= BASE_URL ?>/admin/editarPerfil" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                        <i class="fas fa-pencil-alt mr-2"></i> Editar Información
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>