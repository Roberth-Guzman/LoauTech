<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($data['titulo'] ?? 'Registrar Elemento') ?> - LOAUTECH</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="flex h-screen">
        <?php include __DIR__ . '/includes/sidebar-usuario.php'; ?>

        <!-- Contenido principal -->
        <div class="flex-1 flex flex-col overflow-hidden ml-64">
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-6">
                <div class="container mx-auto">
                    <h1 class="text-2xl font-bold text-gray-800 mb-4"><?= htmlspecialchars($data['titulo'] ?? 'Registrar Elemento') ?></h1>

                    <?php if (isset($_SESSION['mensaje_registro'])) : ?>
                        <div class="mb-4 p-4 rounded-md bg-<?= $_SESSION['mensaje_registro']['tipo'] === 'exito' ? 'green' : 'red' ?>-100 text-<?= $_SESSION['mensaje_registro']['tipo'] === 'exito' ? 'green' : 'red' ?>-700">
                            <?= htmlspecialchars($_SESSION['mensaje_registro']['texto']) ?>
                        </div>
                        <?php unset($_SESSION['mensaje_registro']); ?>
                    <?php endif; ?>

                    <!-- Formulario de Registro -->
                    <div class="bg-white p-6 rounded-lg shadow-md">
                        <form action="/mvc_dev/ingreso/registrar" method="POST" class="space-y-6">


                            <!-- Nombre del Elemento -->
                            <div>
                                <label for="nombreingele" class="block text-sm font-medium text-gray-700">Nombre del Elemento *</label>
                                <input type="text" id="nombreingele" name="nombreingele" required class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                            </div>

                            <!-- Tipo de Elemento -->
                            <div>
                                <label for="tipoelemento" class="block text-sm font-medium text-gray-700">Tipo de Elemento *</label>
                                <select id="tipoelemento" name="tipoelemento" required class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="" disabled selected>Seleccione una categoría</option>
                                    <?php
                                    $categorias = ['Computadores', 'Monitores', 'Teclados', 'Mouse', 'Impresoras', 'Muebles', 'Otros'];
                                    foreach ($categorias as $cat) {
                                        echo "<option value='{$cat}'>{$cat}</option>";
                                    }
                                    ?>
                                </select>
                            </div>

                            <!-- Serial -->
                            <div>
                                <label for="serial" class="block text-sm font-medium text-gray-700">Serial *</label>
                                <input type="text" id="serial" name="serial" required class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                            </div>

                            <!-- Descripción -->
                            <div>
                                <label for="descripcioningele" class="block text-sm font-medium text-gray-700">Descripción</label>
                                <textarea id="descripcioningele" name="descripcioningele" rows="3" class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                            </div>

                            <!-- Observaciones -->
                            <div>
                                <label for="observacioningele" class="block text-sm font-medium text-gray-700">Observaciones</label>
                                <textarea id="observacioningele" name="observacioningele" rows="2" class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                            </div>

                            <!-- Botones -->
                            <div class="flex justify-end space-x-3 pt-4">
                                <a href="../usuario/panelPrincipal" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">Cancelar</a>
                                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Registrar Elemento
                                </button>
                            </div>

                        </form>
                    </div>
                </div>
            </main>
            <?php include __DIR__ . '/includes/footer-usuario.php'; ?>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
</body>
</html>