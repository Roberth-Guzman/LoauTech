<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de Cuentadante - Loautech</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex flex-col">
        <!-- Header -->
        <header class="bg-gray-800 text-white p-4 flex justify-between items-center shadow-lg">
            <a href="<?= BASE_URL ?>/cuentadante/panelPrincipal" class="text-xl hover:text-gray-300">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h1 class="text-xl font-bold text-center flex-grow">PERFIL DE CUENTADANTE</h1>
            <a href="<?= BASE_URL ?>/logout" class="text-xl hover:text-gray-300" title="Cerrar sesión">
                <i class="fas fa-sign-out-alt"></i>
            </a>
        </header>

        <!-- Main Content -->
        <main class="container mx-auto p-4 md:p-6 flex-grow">
            <div class="flex justify-center">
                <div class="w-full lg:w-8/12 md:w-10/12">
                    <!-- Profile Picture Section -->
                    <div class="text-center mb-6">
                        <div class="relative inline-block w-36 h-36">
                            <div class="border-4 border-gray-300 rounded-full p-1">
                                <div class="bg-gray-200 rounded-full flex justify-center items-center w-full h-full overflow-hidden">
                                    <?php
                                    // Asumiendo que $data['usuario'] contiene los datos del usuario
                                    $usuario = $data['usuario'] ?? new stdClass();
                                    $foto = $usuario->foto_ruta ?? null; // p. ej. 'public/img/user.jpg'

                                    if ($foto && file_exists($foto)) {
                                        echo "<img src='" . BASE_URL . "/" . htmlspecialchars($foto) . "' alt='Foto de perfil' class='rounded-full w-full h-full object-cover'>";
                                    } else {
                                        echo "<i class='fas fa-user text-gray-500' style='font-size: 80px;'></i>";
                                    }
                                    ?>
                                </div>
                            </div>
                            <!-- Photo Options Dropdown -->
                            <div class="absolute bottom-2 right-2">
                                <button id="photo-menu-button" class="bg-gray-600 hover:bg-gray-700 text-white w-10 h-10 rounded-full flex items-center justify-center" title="Opciones de foto">
                                    <i class="fas fa-camera"></i>
                                </button>
                                <div id="photo-dropdown" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
                                    <a href="#" id="view-photo-link" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Ver foto</a>
                                    <a href="#" id="upload-photo-link" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Modificar / Subir foto</a>
                                    <div class="border-t border-gray-200 my-1"></div>
                                    <a href="#" id="delete-photo-link" class="block px-4 py-2 text-sm text-red-600 hover:bg-gray-100">Borrar foto</a>
                                </div>
                            </div>
                        </div>
                        <h2 class="mt-4 text-2xl font-bold"><?= htmlspecialchars($usuario->nombrecompletoper ?? 'No disponible') ?></h2>
                        <p class="text-gray-500"><?= htmlspecialchars($usuario->correocont ?? 'No disponible') ?></p>
                    </div>

                    <!-- Personal Information Section -->
                    <div class="bg-white p-6 rounded-lg shadow-md mb-6">
                        <h3 class="text-blue-600 text-lg font-semibold mb-4">INFORMACIÓN PERSONAL</h3>
                        <div class="grid md:grid-cols-2 gap-6">
                            <!-- Left Column -->
                            <div>
                                <div class="flex items-center mb-4">
                                    <i class="fas fa-id-card text-gray-400 mr-3"></i>
                                    <div>
                                        <small class="text-gray-500 block">Tipo de Identidad</small>
                                        <p class="font-medium"><?= htmlspecialchars($usuario->tipodocumento ?? 'No disponible') ?></p>
                                    </div>
                                </div>
                                <div class="flex items-center mb-4">
                                    <i class="fas fa-hashtag text-gray-400 mr-3"></i>
                                    <div>
                                        <small class="text-gray-500 block">Número de Identidad</small>
                                        <p class="font-medium"><?= htmlspecialchars($usuario->numerodoc ?? 'No disponible') ?></p>
                                    </div>
                                </div>
                                <div class="flex items-center mb-4">
                                    <i class="fas fa-user text-gray-400 mr-3"></i>
                                    <div>
                                        <small class="text-gray-500 block">Nombre</small>
                                        <p class="font-medium"><?= htmlspecialchars($usuario->nombrecompletoper ?? 'No disponible') ?></p>
                                    </div>
                                </div>
                            </div>
                            <!-- Right Column -->
                            <div>
                                <div class="flex items-center mb-4">
                                    <i class="fas fa-envelope text-gray-400 mr-3"></i>
                                    <div>
                                        <small class="text-gray-500 block">Correo</small>
                                        <p class="font-medium"><?= htmlspecialchars($usuario->correocont ?? 'No disponible') ?></p>
                                    </div>
                                </div>
                                <div class="flex items-center mb-4">
                                    <i class="fas fa-phone text-gray-400 mr-3"></i>
                                    <div>
                                        <small class="text-gray-500 block">Teléfono</small>
                                        <p class="font-medium"><?= htmlspecialchars($usuario->numerocont ?? 'No disponible') ?></p>
                                    </div>
                                </div>
                                <div class="flex items-center mb-4">
                                    <i class="fas fa-lock text-gray-400 mr-3"></i>
                                    <div>
                                        <small class="text-gray-500 block">Contraseña</small>
                                        <p class="font-medium">*********</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Edit Profile Button -->
                    <div class="mt-6">
                        <a href="#" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg flex items-center justify-center">
                            <i class="fas fa-pencil-alt mr-2"></i>Editar Perfil
                        </a>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal for Uploading Photo -->
    <div id="upload-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
        <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md">
            <div class="flex justify-between items-center border-b pb-3 mb-4">
                <h4 class="text-lg font-semibold">Cambiar Foto de Perfil</h4>
                <button id="close-modal-button" class="text-gray-500 hover:text-gray-800 text-2xl">&times;</button>
            </div>
            <form action="#" method="post" enctype="multipart/form-data">
                <div class="modal-body">
                    <p class="mb-4">Selecciona una imagen para tu perfil.</p>
                    <input type="file" name="foto_perfil" accept="image/*" required class="w-full border border-gray-300 rounded-lg p-2">
                </div>
                <div class="modal-footer mt-6 flex justify-end space-x-4">
                    <button type="button" id="cancel-upload-button" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded-lg">Cancelar</button>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">Subir Foto</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Dropdown menu logic
            const photoMenuButton = document.getElementById('photo-menu-button');
            const photoDropdown = document.getElementById('photo-dropdown');

            if (photoMenuButton) {
                photoMenuButton.addEventListener('click', function(event) {
                    photoDropdown.classList.toggle('hidden');
                    event.stopPropagation();
                });
            }

            // Modal logic
            const uploadPhotoLink = document.getElementById('upload-photo-link');
            const uploadModal = document.getElementById('upload-modal');
            const closeModalButton = document.getElementById('close-modal-button');
            const cancelUploadButton = document.getElementById('cancel-upload-button');

            function showModal() {
                if(uploadModal) uploadModal.classList.remove('hidden');
            }

            function hideModal() {
                if(uploadModal) uploadModal.classList.add('hidden');
            }

            if (uploadPhotoLink) {
                uploadPhotoLink.addEventListener('click', function(e) {
                    e.preventDefault();
                    showModal();
                    if (photoDropdown) photoDropdown.classList.add('hidden');
                });
            }
            
            if (closeModalButton) closeModalButton.addEventListener('click', hideModal);
            if (cancelUploadButton) cancelUploadButton.addEventListener('click', hideModal);

            // Hide dropdown/modal when clicking outside
            document.addEventListener('click', function(event) {
                if (photoDropdown && !photoDropdown.classList.contains('hidden') && !photoMenuButton.contains(event.target)) {
                    photoDropdown.classList.add('hidden');
                }
                if (uploadModal && !uploadModal.classList.contains('hidden') && event.target === uploadModal) {
                    hideModal();
                }
            });
        });
    </script>
</body>
</html>