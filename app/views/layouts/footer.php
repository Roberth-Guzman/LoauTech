<footer class="bg-blue-600 text-white py-8">
  <div class="max-w-7xl mx-auto px-4">
    <div class="flex flex-col md:flex-row justify-between mb-6 text-center md:text-left">
      <div class="mb-4 md:mb-0">
        <h5 class="font-bold mb-2">Dirección</h5>
        <p class="text-sm">Carrera 10 # 20-30<br>Neiva, Huila</p>
      </div>
      <div>
        <h5 class="font-bold mb-2">Contacto</h5>
        <p class="text-sm">info<br>+57 300 123 4567</p>
      </div>
    </div>
    <hr class="border-white opacity-30 mt-6">
    <div class="text-center mt-6 space-y-4">
      <div class="space-x-6">
        <a href="<?= BASE_URL ?>terminos" class="text-white hover:underline text-base">
          Términos y Condiciones
        </a>
        <a href="<?= BASE_URL ?>terminos/tecnicos#cookies" class="text-white hover:underline text-base">
          Política de Cookies
        </a>
      </div>
      <p class="text-sm mt-4">&copy; <?= date('Y') ?> LOAUTECH. Todos los derechos reservados.</p>
    </div>
  </div>
</footer>

<!-- Modal de Cookies -->
<div id="modalCookies" class="fixed bottom-0 left-0 right-0 bg-gray-800 text-white p-4 hidden z-50">
    <div class="container mx-auto flex flex-col md:flex-row items-center justify-between">
        <div class="mb-4 md:mb-0 md:mr-8">
            <h3 class="text-lg font-semibold mb-2">Uso de Cookies</h3>
            <p class="text-sm">Utilizamos cookies propias y de terceros para mejorar nuestros servicios y mostrarle publicidad relacionada con sus preferencias mediante el análisis de sus hábitos de navegación.</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-2">
            <button onclick="aceptarCookies()" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded transition-colors">
                Aceptar cookies
            </button>
            <a href="<?= BASE_URL ?>terminos/tecnicos#cookies" class="bg-gray-700 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded text-center transition-colors">
                Más información
            </a>
        </div>
    </div>
</div>

<!-- Script de términos y cookies -->
<script src="<?= BASE_URL ?>public/js/terminos.js"></script>
