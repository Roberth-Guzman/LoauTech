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
                        </div>
                    <?php endif; ?>
                    
                    <?php if (isset($_SESSION['exito'])): ?>
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline"><?= $_SESSION['exito'] ?></span>
                            <?php unset($_SESSION['exito']); ?>
                        </div>
                    <?php endif; ?>

                    <!-- Formulario de Registro -->
                    <div class="bg-white p-6 rounded-lg shadow-md">
                        <form action="<?= BASE_URL ?>usuario/guardarElemento" method="POST" class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="nombreingele" class="block text-sm font-medium text-gray-700">Nombre del Elemento *</label>
                                    <input type="text" name="nombreingele" id="nombreingele" required maxlength="250"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        value="<?= htmlspecialchars($datos['nombreingele'] ?? '') ?>">
                                    <p class="mt-1 text-xs text-gray-500">Máximo 250 caracteres</p>
                                </div>
                                
                                <div>
                                    <label for="tipoelemento" class="block text-sm font-medium text-gray-700">Tipo de Elemento *</label>
                                    <select name="tipoelemento" id="tipoelemento" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        <option value="">Seleccione un tipo</option>
                                        <option value="Equipos de Cómputo y Periféricos" <?= (isset($datos['tipoelemento']) && $datos['tipoelemento'] === 'Equipos de Cómputo y Periféricos') ? 'selected' : '' ?>>Equipos de Cómputo y Periféricos</option>
                                        <option value="Equipos de Audio y Video" <?= (isset($datos['tipoelemento']) && $datos['tipoelemento'] === 'Equipos de Audio y Video') ? 'selected' : '' ?>>Equipos de Audio y Video</option>
                                        <option value="Instrumentos Musicales" <?= (isset($datos['tipoelemento']) && $datos['tipoelemento'] === 'Instrumentos Musicales') ? 'selected' : '' ?>>Instrumentos Musicales</option>
                                        <option value="Herramientas y Kits Técnicos" <?= (isset($datos['tipoelemento']) && $datos['tipoelemento'] === 'Herramientas y Kits Técnicos') ? 'selected' : '' ?>>Herramientas y Kits Técnicos</option>
                                        <option value="Material Didáctico Específico" <?= (isset($datos['tipoelemento']) && $datos['tipoelemento'] === 'Material Didáctico Específico') ? 'selected' : '' ?>>Material Didáctico Específico</option>
                                        <option value="Otro" <?= (isset($datos['tipoelemento']) && $datos['tipoelemento'] === 'Otro') ? 'selected' : '' ?>>Otro</option>
                                    </select>
                                </div>
                                
                                <div>
                                    <label for="serial" class="block text-sm font-medium text-gray-700">Serial *</label>
                                    <input type="text" name="serial" id="serial" required maxlength="100"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        value="<?= htmlspecialchars($datos['serial'] ?? '') ?>">
                                    <p class="mt-1 text-xs text-gray-500">Máximo 100 caracteres</p>
                                </div>
                                
                                <div>
                                    <label for="descripcioningele" class="block text-sm font-medium text-gray-700">Descripción</label>
                                    <textarea name="descripcioningele" id="descripcioningele" rows="2" maxlength="250"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"><?= htmlspecialchars($datos['descripcioningele'] ?? '') ?></textarea>
                                    <p class="mt-1 text-xs text-gray-500">Máximo 250 caracteres</p>
                                </div>
                                
                                <div class="md:col-span-2">
                                    <label for="observacioningele" class="block text-sm font-medium text-gray-700">Observaciones</label>
                                    <textarea name="observacioningele" id="observacioningele" rows="3" maxlength="250"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"><?= htmlspecialchars($datos['observacioningele'] ?? '') ?></textarea>
                                    <p class="mt-1 text-xs text-gray-500">Máximo 250 caracteres</p>
                                </div>
                            </div>
                            
                            <div class="flex justify-end space-x-3">
                                <a href="<?= BASE_URL ?>usuario/panelPrincipal" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    Cancelar
                                </a>
                                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    Guardar Elemento
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