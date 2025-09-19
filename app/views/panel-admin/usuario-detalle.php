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
            <div class="bg-white rounded shadow p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Nombre</p>
                        <p class="text-lg"><?= htmlspecialchars($data['usuario']->nombrecompletoper) ?></p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Documento</p>
                        <p class="text-lg"><?= htmlspecialchars($data['usuario']->tipodocumento) ?>: <?= htmlspecialchars($data['usuario']->numerodoc) ?></p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Correo</p>
                        <p class="text-lg"><?= htmlspecialchars($data['usuario']->correocont ?? '-') ?></p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Teléfono</p>
                        <p class="text-lg"><?= htmlspecialchars($data['usuario']->numerocont ?? '-') ?></p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Rol</p>
                        <p class="text-lg"><?= htmlspecialchars($data['usuario']->rol ?? 'usuario') ?></p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Estado cuenta</p>
                        <p class="text-lg"><?= htmlspecialchars($data['usuario']->estadocue ?? '-') ?></p>
                    </div>
                </div>
                <div class="mt-6">
                    <a href="<?= BASE_URL ?>/admin/usuarios" class="inline-flex items-center px-4 py-2 rounded bg-gray-600 hover:bg-gray-700 text-white"><i class="fas fa-arrow-left mr-2"></i>Volver</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>


