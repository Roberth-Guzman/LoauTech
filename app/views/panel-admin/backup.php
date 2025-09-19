<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($data['titulo'] ?? 'Respaldo de Base de Datos'); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="flex min-h-screen">
        <?php require_once __DIR__ . '/includes/sidebar-admin.php'; ?>
        <div class="flex-1 p-8">
            <h1 class="text-2xl font-bold text-gray-800 mb-4"><?php echo htmlspecialchars($data['titulo'] ?? 'Respaldo de Base de Datos'); ?></h1>

            <?php if (!empty($data['success'])): ?>
                <div class="mb-4 p-3 rounded bg-green-100 text-green-800">
                    <?php if ($data['success'] === 'import_ok'): ?>
                        Importación realizada correctamente.
                    <?php elseif ($data['success'] === 'drop_ok'): ?>
                        Base de datos eliminada correctamente. <a class="underline" href="<?php echo BASE_URL; ?>/admin/backup">Importar una copia</a>.
                    <?php else: ?>
                        Operación exitosa.
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            <?php if (!empty($data['error'])): ?>
                <div class="mb-4 p-3 rounded bg-red-100 text-red-800">
                    Ocurrió un error. Código: <?php echo htmlspecialchars($data['error']); ?>
                </div>
            <?php endif; ?>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white p-6 rounded shadow">
                    <h2 class="font-semibold mb-2"><i class="fas fa-download mr-2"></i>Exportar Base de Datos</h2>
                    <p class="text-sm text-gray-600 mb-3">Descarga un respaldo completo de la base de datos actual.</p>
                    <a href="<?php echo BASE_URL; ?>/admin/exportarBD" class="inline-flex items-center px-4 py-2 rounded bg-blue-600 hover:bg-blue-700 text-white">
                        Descargar loatech.sql
                    </a>
                </div>

                <div class="bg-white p-6 rounded shadow">
                    <h2 class="font-semibold mb-2"><i class="fas fa-upload mr-2"></i>Importar Base de Datos</h2>
                    <p class="text-sm text-gray-600 mb-3">Sube un archivo .sql para restaurar la base de datos.</p>
                    <form action="<?php echo BASE_URL; ?>/admin/importarBD" method="POST" enctype="multipart/form-data" class="space-y-3">
                        <input type="file" name="archivo_sql" accept=".sql" required class="block w-full text-sm border rounded p-2">
                        <button type="submit" class="inline-flex items-center px-4 py-2 rounded bg-green-600 hover:bg-green-700 text-white">Importar</button>
                    </form>
                </div>

                <div class="bg-white p-6 rounded shadow">
                    <h2 class="font-semibold mb-2"><i class="fas fa-trash-alt mr-2"></i>Eliminar Base de Datos</h2>
                    <p class="text-sm text-red-700 mb-3">Esta acción eliminará todas las tablas y datos de la base de datos actual.</p>
                    <form action="<?php echo BASE_URL; ?>/admin/eliminarBD" method="POST" onsubmit="return confirm('¿Seguro que deseas eliminar TODA la base de datos? Esta acción no se puede deshacer.');">
                        <button type="submit" class="inline-flex items-center px-4 py-2 rounded bg-red-600 hover:bg-red-700 text-white">Eliminar BD</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>


