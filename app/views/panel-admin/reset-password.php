<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $data['titulo']; ?> - Panel de Administración</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .search-results {
            position: absolute;
            width: 100%;
            background-color: white;
            border: 1px solid #cbd5e0;
            border-radius: 0.375rem;
            margin-top: 0.25rem;
            z-index: 10;
            max-height: 200px;
            overflow-y: auto;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
        .search-item {
            padding: 0.75rem 1rem;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        .search-item:hover {
            background-color: #f0f4f8;
        }
        .input-icon {
            position: absolute;
            left: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
        }
        .search-input {
            padding-left: 2.5rem; /* Make space for icon */
        }
    </style>
</head>
<body class="bg-gray-100 font-sans leading-normal tracking-normal">

    <div class="flex min-h-screen">
        
        <?php require_once __DIR__ . '/includes/sidebar-admin.php'; ?>

        <div class="flex-1 p-10">
            <main class="bg-white rounded-lg shadow-lg p-8">
                <h1 class="text-3xl font-bold mb-6 text-gray-800"><?php echo $data['titulo']; ?></h1>

                <div class="max-w-md mx-auto">
                    
                    <?php if (isset($_SESSION['reset_success'])): ?>
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline"><?php echo $_SESSION['reset_success']; unset($_SESSION['reset_success']); ?></span>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['reset_error'])): ?>
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline"><?php echo $_SESSION['reset_error']; unset($_SESSION['reset_error']); ?></span>
                        </div>
                    <?php endif; ?>

                    <!-- Paso 1: Búsqueda de Usuario -->
                    <div id="search-container">
                        <label for="search_usuario" class="block text-sm font-medium text-gray-700 mb-1">Buscar Usuario</label>
                        <div class="relative rounded-md shadow-sm">
                            <div class="input-icon">
                                <i class="fas fa-search"></i>
                            </div>
                            <input type="text" id="search_usuario" name="search_usuario" class="search-input mt-1 block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="Buscar por nombre, email o documento..." autocomplete="off">
                            <div id="search_results" class="search-results hidden"></div>
                        </div>
                    </div>

                    <!-- Paso 2: Confirmación y Formulario de Reseteo -->
                    <form id="reset-form" method="POST" action="<?php echo BASE_URL; ?>/admin/forceResetPassword" class="space-y-6 hidden mt-6">
                        <input type="hidden" id="usuario_id" name="usuario_id">

                        <div id="success-message" class="hidden bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                            <span class="block sm:inline"></span>
                        </div>

                        <!-- Info del usuario seleccionado -->
                        <div id="user-confirmation" class="p-4 bg-indigo-50 rounded-lg border border-indigo-200">
                            <div class="flex justify-between items-center">
                                <div>
                                    <h3 class="text-lg font-medium text-gray-900">Usuario Seleccionado</h3>
                                    <p id="user-name" class="mt-1 font-semibold text-gray-700"></p>
                                    <p id="user-email" class="text-sm text-gray-600"></p>
                                </div>
                                <button type="button" id="change-user-btn" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                                    <i class="fas fa-exchange-alt mr-1"></i>Cambiar
                                </button>
                            </div>
                        </div>

                        <div id="password-error" class="hidden bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                            <span class="block sm:inline"></span>
                        </div>

                        <div>
                            <label for="new_password" class="block text-sm font-medium text-gray-700">Nueva Contraseña</label>
                            <input type="password" id="new_password" name="new_password" required class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                        </div>

                        <div>
                            <label for="confirm_password" class="block text-sm font-medium text-gray-700">Confirmar Nueva Contraseña</label>
                            <input type="password" id="confirm_password" name="confirm_password" required class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                        </div>
                        
                        <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <i class="fas fa-key mr-2"></i> Restablecer Contraseña
                        </button>
                    </form>
                </div>
            </main>
        </div>
    </div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchContainer = document.getElementById('search-container');
    const searchInput = document.getElementById('search_usuario');
    const resultsContainer = document.getElementById('search_results');
    
    const resetForm = document.getElementById('reset-form');
    const userIdInput = document.getElementById('usuario_id');
    
    const userConfirmation = document.getElementById('user-confirmation');
    const userNameP = document.getElementById('user-name');
    const userEmailP = document.getElementById('user-email');
    const changeUserBtn = document.getElementById('change-user-btn');

    const newPasswordInput = document.getElementById('new_password');
    const confirmPasswordInput = document.getElementById('confirm_password');
    const passwordErrorDiv = document.getElementById('password-error');

    searchInput.addEventListener('keyup', function() {
        const query = searchInput.value;
        if (query.length < 2) {
            resultsContainer.classList.add('hidden');
            return;
        }
        fetch(`<?php echo BASE_URL; ?>/admin/buscarUsuarios?term=${query}`)
            .then(response => response.json())
            .then(data => {
                resultsContainer.innerHTML = '';
                resultsContainer.classList.remove('hidden');
                if (data.length > 0) {
                    data.forEach(user => {
                        const item = document.createElement('div');
                        item.classList.add('search-item');
                        item.innerHTML = `<strong>${user.nombrecompletoper}</strong><br><span class="text-sm text-gray-500">${user.correocont}</span>`;
                        
                        item.addEventListener('click', function() {
                            // Ocultar búsqueda y mostrar formulario
                            searchContainer.classList.add('hidden');
                            resetForm.classList.remove('hidden');
                            
                            // Poblar datos
                            userIdInput.value = user.IDcue;
                            userNameP.textContent = `${user.nombrecompletoper}`;
                            userEmailP.textContent = `${user.correocont}`;
                            
                            resultsContainer.classList.add('hidden');
                        });
                        resultsContainer.appendChild(item);
                    });
                } else {
                    resultsContainer.innerHTML = '<div class="search-item">No se encontraron usuarios</div>';
                }
            });
    });

    changeUserBtn.addEventListener('click', function() {
        // Ocultar formulario y mostrar búsqueda
        resetForm.classList.add('hidden');
        searchContainer.classList.remove('hidden');
        
        // Limpiar campos
        searchInput.value = '';
        userIdInput.value = '';
        newPasswordInput.value = '';
        confirmPasswordInput.value = '';
        passwordErrorDiv.classList.add('hidden');
    });

    resetForm.addEventListener('submit', function(e) {
        e.preventDefault(); // Prevenir envío automático

        const newPassword = newPasswordInput.value;
        const confirmPassword = confirmPasswordInput.value;
        
        passwordErrorDiv.classList.add('hidden');
        document.getElementById('success-message').classList.add('hidden');

        if (newPassword.length < 8) {
            passwordErrorDiv.querySelector('span').textContent = 'La nueva contraseña debe tener al menos 8 caracteres.';
            passwordErrorDiv.classList.remove('hidden');
            return;
        }

        if (newPassword !== confirmPassword) {
            passwordErrorDiv.querySelector('span').textContent = 'Las contraseñas no coinciden.';
            passwordErrorDiv.classList.remove('hidden');
            return;
        }

        // Si la validación es exitosa, enviar el formulario con fetch
        const formData = new FormData(this);

        fetch(this.action, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const successMessage = document.getElementById('success-message');
                successMessage.querySelector('span').textContent = data.message;
                successMessage.classList.remove('hidden');
                
                // Opcional: resetear el formulario y volver a la búsqueda después de un momento
                setTimeout(() => {
                    resetForm.classList.add('hidden');
                    searchContainer.classList.remove('hidden');
                    searchInput.value = '';
                    newPasswordInput.value = '';
                    confirmPasswordInput.value = '';
                    successMessage.classList.add('hidden');
                }, 3000); // 3 segundos

            } else {
                passwordErrorDiv.querySelector('span').textContent = data.message || 'Ocurrió un error al restablecer la contraseña.';
                passwordErrorDiv.classList.remove('hidden');
            }
        })
        .catch(error => {
            passwordErrorDiv.querySelector('span').textContent = 'Error de conexión. Inténtalo de nuevo.';
            passwordErrorDiv.classList.remove('hidden');
        });
    });

    document.addEventListener('click', function(e) {
        if (!resultsContainer.contains(e.target) && !searchInput.contains(e.target)) {
            resultsContainer.classList.add('hidden');
        }
    });
});
</script>

</body>
</html>