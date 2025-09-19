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

            <?php if(isset($_SESSION['mensaje'])): ?>
                <div class="bg-<?= $_SESSION['tipo_mensaje'] == 'success' ? 'green' : 'red' ?>-100 border-l-4 border-<?= $_SESSION['tipo_mensaje'] == 'success' ? 'green' : 'red' ?>-500 text-<?= $_SESSION['tipo_mensaje'] == 'success' ? 'green' : 'red' ?>-700 p-4 mb-4" role="alert">
                    <p><?= $_SESSION['mensaje'] ?></p>
                </div>
                <?php 
                    unset($_SESSION['mensaje']);
                    unset($_SESSION['tipo_mensaje']);
                endif; 
            ?>

            <!-- Formulario de Agregar Elemento -->
            <div class="bg-white shadow-md rounded-lg p-6">
                <form action="<?= BASE_URL ?>/admin/agregarElemento" method="POST" enctype="multipart/form-data">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="nombreele" class="block text-gray-700 font-medium mb-2">Nombre del Elemento</label>
                            <input type="text" name="nombreele" id="nombreele" value="<?= htmlspecialchars($data['nombreele']) ?>" required class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        
                        <div>
                            <label for="cantidadele" class="block text-gray-700 font-medium mb-2">Cantidad</label>
                            <input type="number" name="cantidadele" id="cantidadele" value="<?= htmlspecialchars($data['cantidadele']) ?>" required min="1" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        
                        <div>
                            <label for="codigoele" class="block text-gray-700 font-medium mb-2">Código</label>
                            <input type="text" name="codigoele" id="codigoele" value="<?= htmlspecialchars($data['codigoele']) ?>" required class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        
                        <div>
                            <label for="codigoinventario" class="block text-gray-700 font-medium mb-2">Código de Inventario</label>
                            <input type="text" name="codigoinventario" id="codigoinventario" value="<?= htmlspecialchars($data['codigoinventario']) ?>" required class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        
                        <div>
                            <label for="estado" class="block text-gray-700 font-medium mb-2">Estado</label>
                            <select name="estado" id="estado" required class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="activo" <?= $data['estado'] == 'activo' ? 'selected' : '' ?>>Activo</option>
                                <option value="inactivo" <?= $data['estado'] == 'inactivo' ? 'selected' : '' ?>>Inactivo</option>
                                <option value="en prestamo" <?= $data['estado'] == 'en prestamo' ? 'selected' : '' ?>>En Préstamo</option>
                            </select>
                        </div>
                        
                        <div>
                            <label for="estadoelemento" class="block text-gray-700 font-medium mb-2">Estado del Elemento</label>
                            <select name="estadoelemento" id="estadoelemento" required class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="disponible" <?= $data['estadoelemento'] == 'disponible' ? 'selected' : '' ?>>Disponible</option>
                                <option value="no disponible" <?= $data['estadoelemento'] == 'no disponible' ? 'selected' : '' ?>>No Disponible</option>
                                <option value="en mantenimiento" <?= $data['estadoelemento'] == 'en mantenimiento' ? 'selected' : '' ?>>En Mantenimiento</option>
                            </select>
                        </div>
                        
                        <div class="md:col-span-2">
                            <label for="descripcionele" class="block text-gray-700 font-medium mb-2">Descripción</label>
                            <textarea name="descripcionele" id="descripcionele" rows="3" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"><?= htmlspecialchars($data['descripcionele']) ?></textarea>
                        </div>
                        
                        <div class="md:col-span-2">
                            <label for="caracteristicasele" class="block text-gray-700 font-medium mb-2">Características</label>
                            <textarea name="caracteristicasele" id="caracteristicasele" rows="3" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"><?= htmlspecialchars($data['caracteristicasele']) ?></textarea>
                        </div>
                        
                        <div class="md:col-span-2">
                            <label for="imagen" class="block text-gray-700 font-medium mb-2">Imagen</label>
                            <input type="file" name="imagen" id="imagen" accept="image/*" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <p class="text-sm text-gray-500 mt-1">Formatos aceptados: JPG, PNG, GIF. Tamaño máximo: 2MB</p>
                        </div>
                    </div>
                    
                    <div class="mt-6 flex justify-end">
                        <a href="<?= BASE_URL ?>/admin/elementos" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 mr-2">
                            Cancelar
                        </a>
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                            <i class="fas fa-save mr-2"></i> Guardar Elemento
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</body>
</html>