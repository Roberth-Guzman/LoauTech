<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($data['titulo'] ?? 'Perfil de Almacén'); ?> - LOAUTECH</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome para los iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-100">

<?php 
require_once __DIR__ . '/includes/navbar.php'; 
?>

<div class="container mx-auto p-4 md:p-8">
    <h1 class="text-3xl font-bold text-center text-gray-800 mb-8"><?php echo htmlspecialchars($data['titulo']); ?></h1>

    <!-- Alertas de éxito o error con Tailwind -->
    <?php if (isset($data['success'])) : ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline"><?php echo htmlspecialchars($data['success']); ?></span>
        </div>
    <?php endif; ?>
    <?php if (isset($data['error'])) : ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline"><?php echo htmlspecialchars($data['error']); ?></span>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-lg shadow-lg p-6 md:p-8">
        <div class="flex flex-col md:flex-row items-center md:items-start gap-8">
            <!-- Columna de la foto de perfil -->
            <div class="w-full md:w-1/3 text-center">
                <?php
                $foto_ruta_url = BASE_URL . '/public/img/default-user.png'; 
                if (isset($data['perfil']->foto_ruta) && !empty($data['perfil']->foto_ruta)) {
                    $foto_path_servidor = dirname(dirname(dirname(dirname(__DIR__)))) . '/public/' . $data['perfil']->foto_ruta;
                    if (file_exists($foto_path_servidor)) {
                        $foto_ruta_url = BASE_URL . '/public/' . $data['perfil']->foto_ruta;
                    }
                }
                ?>
                <img src="<?php echo $foto_ruta_url; ?>" alt="Foto de Perfil" class="w-48 h-48 rounded-full mx-auto object-cover border-4 border-gray-200 shadow-md">
                
                <!-- Botón para abrir el modal -->
                <button id="openModalBtn" class="mt-4 bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg transition duration-300">
                    Cambiar Foto
                </button>
            </div>

            <!-- Columna de la información del perfil -->
            <div class="w-full md:w-2/3">
                <div class="border-b border-gray-200 pb-4 mb-4">
                    <h3 class="text-2xl font-bold text-gray-800"><?php echo htmlspecialchars($data['perfil']->nombrecompletoper ?? 'No disponible'); ?></h3>
                    <p class="text-gray-500"><?php echo htmlspecialchars(ucfirst($data['perfil']->rol ?? 'No disponible')); ?></p>
                </div>
                <div class="space-y-4 text-gray-700">
                    <p><strong>Tipo de Documento:</strong> <?php echo htmlspecialchars(strtoupper($data['perfil']->tipodocumento ?? 'No disponible')); ?></p>
                    <p><strong>Número de Documento:</strong> <?php echo htmlspecialchars($data['perfil']->numerodoc ?? 'No disponible'); ?></p>
                    <hr>
                    <p><strong>Correo Electrónico:</strong> <?php echo htmlspecialchars($data['perfil']->correocont ?? 'No disponible'); ?></p>
                    <p><strong>Teléfono:</strong> <?php echo htmlspecialchars($data['perfil']->numerocont ?? 'No disponible'); ?></p>
                    <p><strong>Dirección:</strong> <?php echo htmlspecialchars($data['perfil']->direccioncont ?? 'No disponible'); ?></p>
                </div>
                <div class="text-right mt-6">
                    <a href="<?php echo BASE_URL; ?>/almacen/editarPerfil" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg transition duration-300">Editar Información</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para subir foto con Tailwind -->
<div id="uploadPhotoModal" class="fixed z-10 inset-0 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                            Subir Nueva Foto de Perfil
                        </h3>
                        <div class="mt-4">
                            <form action="<?php echo BASE_URL; ?>/usuario/subirFoto" method="post" enctype="multipart/form-data">
                                <div class="mb-3">
                                    <label for="profile_image" class="block text-sm font-medium text-gray-700">Selecciona una imagen</label>
                                    <input type="file" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" id="profile_image" name="profile_image" required>
                                </div>
                                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                                        Subir Foto
                                    </button>
                                    <button type="button" id="closeModalBtn" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                        Cancelar
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Script para controlar el modal con JavaScript puro
    const openModalBtn = document.getElementById('openModalBtn');
    const closeModalBtn = document.getElementById('closeModalBtn');
    const modal = document.getElementById('uploadPhotoModal');

    openModalBtn.addEventListener('click', () => {
        modal.classList.remove('hidden');
    });

    closeModalBtn.addEventListener('click', () => {
        modal.classList.add('hidden');
    });

    // Cierra el modal si se hace clic fuera de él
    window.addEventListener('click', (event) => {
        if (event.target === modal) {
            modal.classList.add('hidden');
        }
    });
</script>

</body>
</html>