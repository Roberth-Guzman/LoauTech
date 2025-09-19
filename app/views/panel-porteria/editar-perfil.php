<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Perfil</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">
    <div class="flex h-screen overflow-hidden">

        <?php require_once __DIR__ . '/includes/sidebar-porteria.php'; ?>

        <!-- Contenido principal -->
        <div class="flex-1 ml-64 overflow-auto">
            <!-- Barra superior -->
            <header class="bg-white shadow-sm">
                <div class="max-w-7xl mx-auto px-4 py-4 sm:px-6 lg:px-8">
                    <h1 class="text-xl font-semibold text-gray-900">Editar Perfil</h1>
                </div>
            </header>

            <!-- Contenido -->
            <main class="p-6">
                <div class="max-w-4xl mx-auto">
                    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Modificar Información de Contacto</h3>
                            <p class="mt-1 text-sm text-gray-500">Actualiza tu correo electrónico y número de teléfono.
                            </p>
                        </div>

                        <form action="<?php echo BASE_URL; ?>/porteria/editarPerfil" method="POST">
                            <div class="px-6 py-4">
                                <div class="space-y-6">

                                    <!-- Campo de Nombre Completo -->
                                    <div>
                                        <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre
                                            Completo</label>
                                        <div class="mt-1">
                                            <input type="text" name="nombre" id="nombre"
                                                class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                                value="<?php echo htmlspecialchars($data['usuario']->nombrecompletoper ?? ''); ?>"
                                                placeholder="Tu nombre completo">
                                        </div>
                                    </div>

                                    <!-- Campo de Documento -->
                                    <div>
                                        <label for="documento"
                                            class="block text-sm font-medium text-gray-700">Documento</label>
                                        <div class="mt-1">
                                            <input type="text" name="documento" id="documento"
                                                class="shadow-sm block w-full sm:text-sm border-gray-300 rounded-md bg-gray-100"
                                                value="<?php echo htmlspecialchars($data['usuario']->numerodoc ?? ''); ?>"
                                                readonly>
                                            <p class="mt-2 text-xs text-gray-500">El documento no se puede modificar.
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Campo de Correo Electrónico -->
                                    <div>
                                        <label for="email" class="block text-sm font-medium text-gray-700">Correo
                                            Electrónico</label>
                                        <div class="mt-1">
                                            <input type="email" name="email" id="email"
                                                class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                                value="<?php echo htmlspecialchars($data['usuario']->correocont ?? ''); ?>"
                                                placeholder="tu@email.com">
                                        </div>
                                    </div>

                                    <!-- Campo de Teléfono -->
                                    <div>
                                        <label for="telefono"
                                            class="block text-sm font-medium text-gray-700">Teléfono</label>
                                        <div class="mt-1">
                                            <input type="tel" name="telefono" id="telefono"
                                                class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                                value="<?php echo htmlspecialchars($data['usuario']->numerocont ?? ''); ?>"
                                                placeholder="3001234567">
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <div
                                class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end items-center space-x-3">
                                <a href="<?php echo BASE_URL; ?>/porteria/perfil"
                                    class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    Cancelar
                                </a>
                                <button type="submit"
                                    class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <i class="fas fa-save mr-2"></i>
                                    Guardar Cambios
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </main>

            <?php require_once __DIR__ . '/includes/footer-porteria.php'; ?>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
</body>

</html>