<?php
// This is a temporary file that will be used to update perfil.php
?>
<!-- Sección de Editar Perfil -->
<div id="editar-perfil" class="seccion-perfil hidden">
  <form method="POST" action="<?= BASE_URL ?>/usuario/perfil" class="space-y-6">
    <input type="hidden" name="actualizar_perfil" value="1">
    
    <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
      <div class="sm:col-span-6">
        <label for="email" class="block text-sm font-medium text-gray-700">Correo Electrónico</label>
        <input type="email" name="email" id="email" value="<?= htmlspecialchars($data['perfil']->correocont ?? '') ?>" autocomplete="email" required
               class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
      </div>
      
      <div class="sm:col-span-6">
        <label for="telefono" class="block text-sm font-medium text-gray-700">Teléfono</label>
        <input type="tel" name="telefono" id="telefono" value="<?= htmlspecialchars($data['perfil']->numerocont ?? '') ?>" autocomplete="tel"
               class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
      </div>
    </div>
    
    <div class="pt-5">
      <div class="flex justify-end space-x-3">
        <button type="button" onclick="mostrarSeccion('perfil-info')" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
          Cancelar
        </button>
        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
          Guardar Cambios
        </button>
      </div>
    </div>
  </form>
</div>

<!-- Sección de Cambiar Contraseña -->
<div id="cambiar-password" class="seccion-perfil hidden">
  <form method="POST" action="<?= BASE_URL ?>/usuario/perfil" class="space-y-6">
    <input type="hidden" name="cambiar_password" value="1">
    
    <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
      <div class="sm:col-span-6">
        <label for="password_actual" class="block text-sm font-medium text-gray-700">Contraseña Actual</label>
        <div class="mt-1 relative rounded-md shadow-sm">
          <input type="password" name="password_actual" id="password_actual" required
                 class="block w-full pr-10 border border-gray-300 rounded-md py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
          <div class="absolute inset-y-0 right-0 pr-3 flex items-center cursor-pointer" onclick="togglePassword('password_actual')">
            <i class="fas fa-eye text-gray-400"></i>
          </div>
        </div>
      </div>
      
      <div class="sm:col-span-6">
        <label for="nueva_password" class="block text-sm font-medium text-gray-700">Nueva Contraseña</label>
        <div class="mt-1 relative rounded-md shadow-sm">
          <input type="password" name="nueva_password" id="nueva_password" minlength="8" required
                 class="block w-full pr-10 border border-gray-300 rounded-md py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
          <div class="absolute inset-y-0 right-0 pr-3 flex items-center cursor-pointer" onclick="togglePassword('nueva_password')">
            <i class="fas fa-eye text-gray-400"></i>
          </div>
        </div>
        <p class="mt-1 text-xs text-gray-500">Mínimo 8 caracteres</p>
      </div>
      
      <div class="sm:col-span-6">
        <label for="confirmar_password" class="block text-sm font-medium text-gray-700">Confirmar Nueva Contraseña</label>
        <div class="mt-1 relative rounded-md shadow-sm">
          <input type="password" name="confirmar_password" id="confirmar_password" minlength="8" required
                 class="block w-full pr-10 border border-gray-300 rounded-md py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
          <div class="absolute inset-y-0 right-0 pr-3 flex items-center cursor-pointer" onclick="togglePassword('confirmar_password')">
            <i class="fas fa-eye text-gray-400"></i>
          </div>
        </div>
      </div>
    </div>
    
    <div class="pt-5">
      <div class="flex justify-end space-x-3">
        <button type="button" onclick="mostrarSeccion('perfil-info')" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
          Cancelar
        </button>
        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
          Cambiar Contraseña
        </button>
      </div>
    </div>
  </form>
</div>
