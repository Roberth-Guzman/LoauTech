<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Elemento - Sistema de Inventario</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body class="bg-gray-100">

    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold mb-6 text-gray-800">Editar Elemento</h1>

        <?php if (!empty($data['error_message'])) : ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">¡Error de validación!</strong>
                <span class="block sm:inline"><?php echo htmlspecialchars($data['error_message']); ?></span>
            </div>
        <?php endif; ?>

        <div class="bg-white p-8 rounded-lg shadow-2xl">
            <form action="<?php echo BASE_URL; ?>/almacen/almacen/editarElemento/<?php echo htmlspecialchars($data['elemento']->IDele); ?>" method="post" enctype="multipart/form-data" id="edit-form">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                   <!-- Columna Izquierda: Campos de texto -->
                    <div class="md:col-span-2">
                        <!-- Nombre del Elemento -->
                        <div class="mb-4">
                            <label for="nombreele" class="block text-gray-700 text-sm font-bold mb-2">Nombre del Elemento:</label>
                            <input type="text" id="nombreele" name="nombreele" value="<?php echo htmlspecialchars($data['elemento']->nombreele ?? ''); ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                        </div>

                        <!-- Cantidad Total -->
                        <div class="mb-4">
                            <label for="cantidadele" class="block text-gray-700 text-sm font-bold mb-2">Cantidad Total:</label>
                            <input type="number" id="cantidadele" name="cantidadele" value="<?php echo htmlspecialchars($data['elemento']->cantidadele ?? 0); ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                        </div>

                        <!-- Código y Código de Inventario -->
                        <div class="flex flex-wrap -mx-2 mb-4">
                            <div class="w-full md:w-1/2 px-2 mb-4 md:mb-0">
                                <label for="codigoele" class="block text-gray-700 text-sm font-bold mb-2">Código del Elemento:</label>
                                <input type="text" id="codigoele" name="codigoele" value="<?php echo htmlspecialchars($data['elemento']->codigoele ?? ''); ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            </div>
                            <div class="w-full md:w-1/2 px-2">
                                <label for="codigoinventario" class="block text-gray-700 text-sm font-bold mb-2">Código de Inventario:</label>
                                <input type="text" id="codigoinventario" name="codigoinventario" value="<?php echo htmlspecialchars($data['elemento']->codigoinventario ?? ''); ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            </div>
                        </div>

                        <!-- Descripción -->
                        <div class="mb-4">
                            <label for="descripcionele" class="block text-gray-700 text-sm font-bold mb-2">Descripción:</label>
                            <textarea id="descripcionele" name="descripcionele" rows="4" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required><?php echo htmlspecialchars($data['elemento']->descripcionele ?? ''); ?></textarea>
                        </div>

                        <!-- Categoría -->
                        <div class="mb-4">
                            <label for="caracteristicasele" class="block text-gray-700 text-sm font-bold mb-2">Categoría:</label>
                            <input type="text" id="caracteristicasele" name="caracteristicasele" value="<?php echo htmlspecialchars($data['elemento']->caracteristicasele ?? ''); ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        </div>

                        <!-- Estado y Estado del Elemento -->
                        <div class="flex flex-wrap -mx-2 mb-4">
                            <div class="w-full md:w-1/2 px-2 mb-4 md:mb-0">
                                <label for="estado" class="block text-gray-700 text-sm font-bold mb-2">Disponibilidad:</label>
                                <select id="estado" name="estado" class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                    <option value="activo" <?php echo (isset($data['elemento']->estado) && $data['elemento']->estado === 'activo') ? 'selected' : ''; ?>>Activo</option>
                                    <option value="inactivo" <?php echo (isset($data['elemento']->estado) && $data['elemento']->estado === 'inactivo') ? 'selected' : ''; ?>>Inactivo</option>
                                </select>
                            </div>
        
                        </div>

                        <!-- Asignar Cuentadante -->
                        <div class="mb-4">
                            <label for="cuentadante_id" class="block text-gray-700 text-sm font-bold mb-2">Asignar Cuentadante:</label>
                            <select id="cuentadante_id" name="cuentadante_id" class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                <option value="">Sin Asignar</option>
                                <?php if (isset($data['cuentadantes']) && is_array($data['cuentadantes'])): ?>
                                    <?php foreach ($data['cuentadantes'] as $cuentadante): ?>
                                        <option value="<?php echo $cuentadante->IDper; ?>" <?php echo (isset($data['elemento']->cuentadante_id) && $data['elemento']->cuentadante_id == $cuentadante->IDper) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($cuentadante->nombrecompletoper); ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>
                    <!-- Columna Derecha: Imagen -->
                    <div class="flex flex-col items-center justify-center">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Imagen del Elemento:</label>
                        <img id="image-preview" src="<?php echo BASE_URL . '/public/' . htmlspecialchars($data['elemento']->imagen ?? 'img/placeholder.png'); ?>" alt="Imagen del elemento" class="w-64 h-64 object-cover rounded-lg shadow-md mb-4 border">
                        <input type="file" id="imagen" name="imagen" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        <p class="text-xs text-gray-500 mt-1">Sube una nueva imagen para reemplazar la actual.</p>
                    </div>
                </div>

                <!-- Botones -->
                <div class="flex items-center justify-end mt-6 border-t pt-6">
                    <a href="<?php echo BASE_URL; ?>/almacen/almacen/detalleElemento/<?php echo htmlspecialchars($data['elemento']->IDele); ?>" class="text-gray-600 hover:text-gray-800 font-semibold py-2 px-4 mr-4">
                        Cancelar
                    </a>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        <i class="fas fa-save mr-2"></i>Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Script para previsualizar la imagen seleccionada
        document.getElementById('imagen').addEventListener('change', function(event) {
            const [file] = event.target.files;
            if (file) {
                document.getElementById('image-preview').src = URL.createObjectURL(file);
            }
        });
    </script>

</body>
</html>