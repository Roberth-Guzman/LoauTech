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

            <div class="mb-4">
                <a href="<?= BASE_URL ?>/admin/elementos" class="text-blue-600 hover:underline">
                    <i class="fas fa-arrow-left mr-2"></i> Volver a la lista
                </a>
            </div>

            <!-- Detalles del Elemento -->
            <div class="bg-white shadow-md rounded-lg overflow-hidden">
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Imagen del Elemento -->
                        <div class="md:col-span-1">
                            <?php if (!empty($data['elemento']->imagen)): ?>
                                <img src="<?= BASE_URL ?>/public/img/elementos/<?= htmlspecialchars($data['elemento']->imagen) ?>" alt="Imagen del elemento" class="w-full h-auto object-contain border rounded p-2">
                            <?php else: ?>
                                <div class="w-full h-64 bg-gray-200 flex items-center justify-center rounded">
                                    <i class="fas fa-image text-gray-400 text-5xl"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Información del Elemento -->
                        <div class="md:col-span-2">
                            <h2 class="text-2xl font-bold mb-4"><?= htmlspecialchars($data['elemento']->nombreele) ?></h2>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                                <div>
                                    <p class="text-gray-600">ID:</p>
                                    <p class="font-medium"><?= htmlspecialchars($data['elemento']->IDele) ?></p>
                                </div>
                                
                                <div>
                                    <p class="text-gray-600">Código:</p>
                                    <p class="font-medium"><?= htmlspecialchars($data['elemento']->codigoele) ?></p>
                                </div>
                                
                                <div>
                                    <p class="text-gray-600">Código de Inventario:</p>
                                    <p class="font-medium"><?= htmlspecialchars($data['elemento']->codigoinventario) ?></p>
                                </div>
                                
                                <div>
                                    <p class="text-gray-600">Cantidad:</p>
                                    <p class="font-medium"><?= htmlspecialchars($data['elemento']->cantidadele) ?></p>
                                </div>
                                
                                <div>
                                    <p class="text-gray-600">Estado:</p>
                                    <p class="font-medium">
                                        <?php
                                        $estadoClase = '';
                                        switch ($data['elemento']->estado) {
                                            case 'activo':
                                                $estadoClase = 'bg-green-100 text-green-800';
                                                break;
                                            case 'en prestamo':
                                                $estadoClase = 'bg-yellow-100 text-yellow-800';
                                                break;
                                            case 'inactivo':
                                                $estadoClase = 'bg-red-100 text-red-800';
                                                break;
                                        }
                                        ?>
                                        <span class="px-2 py-1 rounded-full <?= $estadoClase ?>">
                                            <?= ucfirst(htmlspecialchars($data['elemento']->estado)) ?>
                                        </span>
                                    </p>
                                </div>
                                
                                <div>
                                    <p class="text-gray-600">Estado del Elemento:</p>
                                    <p class="font-medium">
                                        <?php
                                        $estadoElementoClase = '';
                                        switch ($data['elemento']->estadoelemento) {
                                            case 'disponible':
                                                $estadoElementoClase = 'bg-green-100 text-green-800';
                                                break;
                                            case 'no disponible':
                                                $estadoElementoClase = 'bg-red-100 text-red-800';
                                                break;
                                            case 'en mantenimiento':
                                                $estadoElementoClase = 'bg-yellow-100 text-yellow-800';
                                                break;
                                        }
                                        ?>
                                        <span class="px-2 py-1 rounded-full <?= $estadoElementoClase ?>">
                                            <?= ucfirst(htmlspecialchars($data['elemento']->estadoelemento)) ?>
                                        </span>
                                    </p>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <h3 class="text-lg font-semibold mb-2">Descripción</h3>
                                <p class="text-gray-700"><?= nl2br(htmlspecialchars($data['elemento']->descripcionele)) ?></p>
                            </div>
                            
                            <div>
                                <h3 class="text-lg font-semibold mb-2">Características</h3>
                                <p class="text-gray-700"><?= nl2br(htmlspecialchars($data['elemento']->caracteristicasele)) ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-6 flex justify-end">
                        <a href="<?= BASE_URL ?>/admin/editarElemento/<?= $data['elemento']->IDele ?>" class="bg-yellow-500 text-white px-4 py-2 rounded-lg hover:bg-yellow-600 mr-2">
                            <i class="fas fa-edit mr-2"></i> Editar
                        </a>
                        <a href="<?= BASE_URL ?>/admin/eliminarElemento/<?= $data['elemento']->IDele ?>" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600" onclick="return confirm('¿Estás seguro de eliminar este elemento?')">
                            <i class="fas fa-trash mr-2"></i> Eliminar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</body>
</html>