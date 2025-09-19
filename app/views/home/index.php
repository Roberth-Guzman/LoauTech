<!-- app/views/home/index.php -->

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Loautech</title>
<link rel="icon" href="<?php echo BASE_URL; ?>/public/img/logo_loautech_white.png" type="image/x-icon">
  <style>
    html {
      -webkit-text-size-adjust: 100%;
      text-size-adjust: 100%;
    }
  </style>

  <!-- Cargar Tailwind CSS -->
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-white text-gray-900">
  <?php include __DIR__ . '/../layouts/header.php'; ?>

  <!-- Hero Section -->
  <section id="inicio" class="relative overflow-hidden h-screen pt-20">
    <!-- Imagen de fondo -->
    <div class="absolute top-0 left-0 w-full h-full">
      <img src="/mvc_dev/public/img/biblioteca.jpg" alt="Fondo Loautech" class="w-full h-full object-cover brightness-75">
    </div>

    <!-- Contenido -->
    <div class="relative z-10 h-full flex items-center justify-start">
      <div class="pl-6 sm:pl-12 md:pl-20 lg:pl-32 max-w-3xl">
        <div class="bg-black bg-opacity-70 text-white p-8 sm:p-10 md:p-12 rounded-lg shadow-xl">
          <h1 class="text-5xl font-bold mb-4">Loautech</h1>
          <p class="text-lg leading-relaxed">
            Software dedicado a la entrada y salida de elementos en el Centro de Desarrollo Agroempresarial y Turístico del Huila. Nuestra plataforma ofrece una interfaz sencilla e intuitiva, que facilita su uso desde el primer momento.
          </p>
        </div>
      </div>
    </div>
  </section>

  <!-- Sobre el SENA -->
  <section id="sena" class="py-16 bg-gray-100">
    <div class="max-w-7xl mx-auto px-4">
      <div class="text-center mb-12">
        <h2 class="text-4xl font-bold">Sobre el SENA</h2>
        <p class="text-lg text-gray-600 mt-2">
          El Servicio Nacional de Aprendizaje (SENA) es la entidad pública líder en formación técnica y tecnológica en Colombia.
        </p>
      </div>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- Misión -->
        <div class="bg-white rounded-lg shadow p-6 text-center">
          <div class="text-green-600 text-4xl mb-3">
            <i class="bi bi-bullseye"></i>
          </div>
          <h3 class="text-xl font-bold mb-2">Misión</h3>
          <p class="text-gray-600 text-sm">
            El SENA está encargado de cumplir la función que le corresponde al Estado de invertir en el desarrollo social y técnico de los trabajadores colombianos, ofreciendo y ejecutando la formación profesional integral, para la incorporación y el desarrollo de las personas en actividades productivas que contribuyan al desarrollo social, económico y tecnológico del país (Ley 119/1994).
          </p>
        </div>
        <!-- Visión -->
        <div class="bg-white rounded-lg shadow p-6 text-center">
          <div class="text-blue-500 text-4xl mb-3">
            <i class="bi bi-eye"></i>
          </div>
          <h3 class="text-xl font-bold mb-2">Visión</h3>
          <p class="text-gray-600 text-sm">
            Para el año 2026, el Servicio Nacional de Aprendizaje - SENA estará a la vanguardia de la cualificación del talento humano, tanto a nivel nacional como internacional. Esto se logrará a través de la formación profesional integral, el empleo, el emprendimiento y el reconocimiento de aprendizajes previos...
          </p>
        </div>
        <!-- Valores -->
        <div class="bg-white rounded-lg shadow p-6 text-center">
          <div class="text-yellow-500 text-4xl mb-3">
            <i class="bi bi-stars"></i>
          </div>
          <h3 class="text-xl font-bold mb-2">Valores Institucionales</h3>
          <p class="text-gray-600 text-sm mb-2">
            El SENA se fundamenta en valores que guían el comportamiento de sus funcionarios y aprendices:
          </p>
          <ul class="text-gray-600 text-sm list-disc list-inside text-left">
            <li>Honestidad</li>
            <li>Respeto</li>
            <li>Compromiso</li>
            <li>Diligencia</li>
            <li>Justicia</li>
            <li>Solidaridad</li>
            <li>Lealtad</li>
          </ul>
        </div>
      </div>
    </div>
  </section>

  <?php include __DIR__ . '/../layouts/footer.php'; ?>

</body>
</html>
