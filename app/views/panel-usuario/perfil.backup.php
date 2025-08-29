<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Perfil de Usuario - Loautech</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body class="bg-gray-100">
  <div class="flex h-screen">
    <?php include_once __DIR__ . '/includes/sidebar-usuario.php'; ?>

    <!-- Contenido principal -->
    <div class="flex-1 ml-64 overflow-auto">
      <div class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
          <h1 class="text-xl font-bold text-gray-900">Perfil de Usuario</h1>
          <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
            <?= ucfirst(htmlspecialchars($data['perfil']->rol ?? 'usuario')) ?>
          </span>
        </div>
      </div>

      <?php if (!empty($data['mensaje_exito'])): ?>
      <div class="max-w-4xl mx-auto mt-4">
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
          <?= htmlspecialchars($data['mensaje_exito']) ?>
        </div>
      </div>
      <?php endif; ?>

      <?php if (!empty($data['mensaje_error'])):
      ?>
      <div class="max-w-4xl mx-auto mt-4">
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
          <?= htmlspecialchars($data['mensaje_error']) ?>
        </div>
      </div>
      <?php endif; ?>

      <main class="p-6">
        <div class="max-w-4xl mx-auto">
          <div class="bg-white rounded-lg shadow-sm overflow-hidden mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
              <h3 class="text-lg font-medium text-gray-900">Información del Perfil</h3>
              <p class="mt-1 text-sm text-gray-500">Detalles personales y datos de contacto.</p>
            </div>
            <div class="px-6 py-4">
              <div class="flex flex-col md:flex-row items-center md:items-start space-y-6 md:space-y-0 md:space-x-6">
                <!-- Foto de perfil -->
                <div class="flex-shrink-0">
                  <div class="relative group">
                    <?php 
                      $foto = $data['perfil']->foto_ruta ?? null;
                      if ($foto): 
                    ?>
                      <img src="<?= BASE_URL ?>/<?= htmlspecialchars($foto) ?>" alt="Foto de perfil" class="h-40 w-40 rounded-full object-cover border-4 border-white shadow-md">
                    <?php else: ?>
                      <div class="h-40 w-40 rounded-full bg-gray-200 flex items-center justify-center">
                        <i class="fas fa-user text-gray-400 text-6xl"></i>
                      </div>
                    <?php endif; ?>
                    <div class="absolute inset-0 bg-black bg-opacity-50 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                      <div class="flex space-x-2">
                        <a href="#" class="bg-white p-2 rounded-full text-gray-800 hover:bg-gray-100" onclick="openModal()" title="Cambiar foto"><i class="fas fa-camera"></i></a>
                        <?php if ($foto): ?>
                        <a href="#" class="bg-white p-2 rounded-full text-red-600 hover:bg-red-50" onclick="return confirm('¿Estás seguro de que quieres eliminar tu foto de perfil?');" title="Eliminar foto"><i class="fas fa-trash"></i></a>
                        <?php endif; ?>
                      </div>
                    </div>
                  </div>
                  <div class="mt-4 text-center">
                    <button type="button" class="text-sm text-blue-600 hover:text-blue-800 font-medium" onclick="openModal()">
                      Cambiar foto
                    </button>
                  </div>
                </div>

                <!-- Información del usuario -->
                <div class="flex-1 w-full">
                  <!-- Pestañas de navegación -->
                  <div class="border-b border-gray-200 mb-6">
                    <nav class="-mb-px flex space-x-8">
                      <button onclick="mostrarSeccion('perfil-info')" class="nav-perfil whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm border-blue-500 text-blue-600">
                        Información del Perfil
                      </button>
                      <button onclick="mostrarSeccion('editar-perfil')" class="nav-perfil whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                        Editar Perfil
                      </button>
                      <button onclick="mostrarSeccion('cambiar-password')" class="nav-perfil whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                        Cambiar Contraseña
                      </button>
                    </nav>
                  </div>
                  
                  <!-- Sección de Información del Perfil -->
                  <div id="perfil-info" class="seccion-perfil space-y-4">
                    <div>
                      <h4 class="text-lg font-medium text-gray-900"><?= htmlspecialchars($data['perfil']->nombrecompletoper ?? '') ?></h4>
                      <p class="text-sm text-gray-500"><?= ucfirst(htmlspecialchars($data['perfil']->rol ?? 'usuario')) ?></p>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                      <div><p class="text-sm font-medium text-gray-500">Tipo de Documento</p><p class="text-sm text-gray-900"><?= htmlspecialchars($data['perfil']->tipodocumento ?? 'No especificado') ?></p></div>
                      <div><p class="text-sm font-medium text-gray-500">Número de Documento</p><p class="text-sm text-gray-900"><?= htmlspecialchars($data['perfil']->numerodoc ?? 'No especificado') ?></p></div>
                      <div><p class="text-sm font-medium text-gray-500">Correo Electrónico</p><p class="text-sm text-gray-900"><?= htmlspecialchars($data['perfil']->correocont ?? 'No especificado') ?></p></div>
                      <div><p class="text-sm font-medium text-gray-500">Teléfono</p><p class="text-sm text-gray-900"><?= htmlspecialchars($data['perfil']->numerocont ?? 'No especificado') ?></p></div>
                    </div>
                  </div>
              </div>
            </div>
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end">
              <a href="#" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700"><i class="fas fa-edit mr-2"></i>Editar Perfil</a>
            </div>
          </div>
        </div>
      </main>
    </div>
  </div>

  <!-- Modal de subida de foto -->
  <div id="modalSubirFoto" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
      <div class="mt-3">
        <form action="#" method="post" enctype="multipart/form-data">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-medium text-gray-900">Cambiar Foto de Perfil</h3>
            <button type="button" onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
              <i class="fas fa-times text-xl"></i>
            </button>
          </div>
          <div class="mb-4">
            <label for="foto_perfil" class="block text-sm font-medium text-gray-700 mb-2">Seleccionar imagen</label>
            <input type="file" name="foto_perfil" id="foto_perfil" accept="image/*" required 
                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            <p class="mt-1 text-xs text-gray-500">Formatos permitidos: JPG, PNG, GIF. Tamaño máximo: 5MB</p>
          </div>
          <div class="flex justify-end space-x-3">
            <button type="button" onclick="closeModal()" 
                    class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
              Cancelar
            </button>
            <button type="submit" 
                    class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
              Subir Foto
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- JavaScript para el modal -->
  <script>
    // Función para mostrar/ocultar secciones
    function mostrarSeccion(idSeccion) {
      // Ocultar todas las secciones
      document.querySelectorAll('.seccion-perfil').forEach(seccion => {
        seccion.classList.add('hidden');
      });
      
      // Mostrar la sección seleccionada
      document.getElementById(idSeccion).classList.remove('hidden');
      
      // Actualizar clases de los botones
      document.querySelectorAll('.nav-perfil').forEach(boton => {
        boton.classList.remove('border-blue-500', 'text-blue-600', 'bg-blue-50');
        boton.classList.add('border-transparent', 'text-gray-600', 'hover:text-gray-800', 'hover:border-gray-300');
      });
      
      // Resaltar el botón activo
      document.querySelector(`[onclick*="${idSeccion}"]`).classList.remove('border-transparent', 'text-gray-600', 'hover:text-gray-800', 'hover:border-gray-300');
      document.querySelector(`[onclick*="${idSeccion}"]`).classList.add('border-blue-500', 'text-blue-600', 'bg-blue-50');
    }
    
    // Funciones para el modal de subida de foto
    function openModal() {
      document.getElementById('modalSubirFoto').classList.remove('hidden');
    }
    
    function closeModal() {
      document.getElementById('modalSubirFoto').classList.add('hidden');
    }
    
    // Función para mostrar/ocultar contraseña
    function togglePassword(inputId) {
      const input = document.getElementById(inputId);
      const icon = document.querySelector(`[onclick="togglePassword('${inputId}')"]`);
      if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
      } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
      }
    }
    
    // Inicialización al cargar la página
    document.addEventListener('DOMContentLoaded', function() {
      // Cerrar modal al hacer clic fuera de él
      const modal = document.getElementById('modalSubirFoto');
      if (modal) {
        modal.addEventListener('click', function(e) {
          if (e.target === this) {
            closeModal();
          }
        });
      }
      
      // Mostrar vista de perfil por defecto
      mostrarSeccion('perfil-info');
    });
  </script>

<?php require_once __DIR__ . '/includes/footer-usuario.php'; ?>
</body>
</html>