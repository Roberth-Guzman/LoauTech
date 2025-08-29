<?php require_once __DIR__ . "/../layouts/header.php"; ?>

<!-- HERO -->
<section id="inicio" class="relative hero h-screen flex items-center justify-center text-white pt-16" style="background: url('assets/images/biblioteca.jpg') no-repeat center center; background-size: cover;">
  <div class="absolute inset-0 bg-black bg-opacity-50"></div>
  <div class="container mx-auto hero-content text-center relative z-10">
    <h1 class="text-4xl md:text-5xl font-bold mb-3">SOBRE EL EQUIPO DE DESARROLLO</h1>
    <p class="text-lg">Conoce a las personas detrás del proyecto Loautech</p>
  </div>
</section>

<!-- ¿QUIÉNES SOMOS? -->
<section class="py-16 bg-white">
  <div class="max-w-7xl mx-auto px-4 grid md:grid-cols-2 gap-10 items-center">
    <img src="assets/images/equipo.jpeg" class="w-full rounded shadow" alt="Equipo Loautech" />
    <div>
      <h2 class="text-3xl font-bold mb-4">¿Quiénes somos?</h2>
      <p class="text-gray-600 mb-2">Somos cinco estudiantes del SENA...</p>
      <p><strong>Año de inicio:</strong> 2024</p>
    </div>
  </div>
</section>

<!-- MIEMBROS -->
<section class="py-16 bg-gray-100">
  <div class="max-w-7xl mx-auto px-4">
    <div class="text-center mb-12">
      <h2 class="text-3xl font-bold">Nuestro equipo</h2>
      <p class="text-gray-600">Estos son los integrantes que hicieron realidad Loautech</p>
    </div>
    <div class="grid grid-cols-2 md:grid-cols-5 gap-6 text-center">
      <?php
      $miembros = [
        ["Brando Garcia", "Líder de Proyecto"],
        ["Betsy Barreto", "Diseñadora"],
        ["Roberth Guzman", "Desarrollador"],
        ["Lizeth Serna", "Diseñadora"],
        ["Miguel Castiblanco", "Analista"]
      ];
      foreach ($miembros as $miembro) {
        echo '
        <div>
          <img src="assets/images/equipo.jpeg" class="rounded-full mx-auto mb-2 w-24 h-24 object-cover" />
          <h6 class="font-bold">' . $miembro[0] . '</h6>
          <p class="text-gray-600 text-sm">' . $miembro[1] . '</p>
        </div>';
      }
      ?>
    </div>
  </div>
</section>

<!-- CONTACTO -->
<section id="contacto" class="py-16 bg-blue-50">
  <div class="max-w-3xl mx-auto px-4">
    <div class="text-center mb-8">
      <h2 class="text-3xl font-bold">Contáctanos</h2>
      <p class="text-gray-600">¿Tienes dudas o sugerencias? Escríbenos</p>
    </div>
    <form class="space-y-6" method="POST" action="/Equipo/procesarContacto" novalidate>
      <div>
        <label for="nombre" class="block font-semibold mb-1">Nombre Completo</label>
        <input type="text" id="nombre" name="nombre" required class="w-full border border-gray-300 rounded px-4 py-2" />
      </div>
      <div>
        <label for="email" class="block font-semibold mb-1">Correo Electrónico</label>
        <input type="email" id="email" name="email" required class="w-full border border-gray-300 rounded px-4 py-2" />
      </div>
      <div>
        <label for="telefono" class="block font-semibold mb-1">Teléfono</label>
        <input type="tel" id="telefono" name="telefono" class="w-full border border-gray-300 rounded px-4 py-2" />
      </div>
      <div>
        <label for="mensaje" class="block font-semibold mb-1">Mensaje</label>
        <textarea id="mensaje" name="mensaje" rows="4" required class="w-full border border-gray-300 rounded px-4 py-2"></textarea>
      </div>
      <div class="flex items-center space-x-2">
        <input type="checkbox" id="privacidad" name="privacidad" required class="w-4 h-4 text-blue-600 border-gray-300 rounded" />
        <label for="privacidad" class="text-sm">Acepto términos y condiciones</label>
      </div>
      <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700">
        <i class="bi bi-send-fill mr-2"></i>Enviar Mensaje
      </button>
    </form>
  </div>
</section>

<?php require_once __DIR__ . "/../layouts/footer.php"; ?>
