<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Loautech</title>

    <style>
        html {
            -webkit-text-size-adjust: 100%;
            text-size-adjust: 100%;
        }
    </style>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body class="bg-white text-gray-900">

<?php
$url = isset($_GET['url']) ? rtrim($_GET['url'], '/') : 'home';
$pagina = explode('/', $url)[0];
?>

<nav class="bg-blue-600 text-white shadow z-50 relative">
  <div class="container mx-auto flex items-center justify-between px-4 py-3">
    <a href="/mvc_dev/home" class="flex items-center font-bold text-lg">
      <img src="/mvc_dev/public/img/logo_loautech_white.png" alt="Loautech" class="h-10 w-auto mr-2" />
      Loautech
    </a>

    <?php if ($pagina === 'home'): ?>
      <ul class="flex gap-4 items-center">
        <li><a href="/mvc_dev/home#sena" class="hover:underline">Empresa</a></li>
        <li class="relative">
          <button id="dropdownBtn" class="hover:underline focus:outline-none">
            Ingresar ▾
          </button>
          <ul id="dropdownMenu" class="absolute right-0 mt-2 w-40 bg-white text-black rounded shadow hidden z-50">
            <li><a href="/mvc_dev/login" class="block px-4 py-2 hover:bg-gray-100">Como Usuario</a></li>
            <li><a href="/mvc_dev/invitado" class="block px-4 py-2 hover:bg-gray-100">Como Invitado</a></li>
          </ul>
        </li>
      </ul>

    <?php elseif ($pagina === 'equipo'): ?>
      <ul class="flex gap-4">
        <li><a href="/mvc_dev/home" class="hover:underline">Inicio</a></li>
        <li><a href="/mvc_dev/home#contacto" class="hover:underline">Contacto</a></li>
      </ul>

    <?php elseif ($pagina === 'invitado'): ?>
      <a href="/mvc_dev/login" class="px-4 py-2 border border-white rounded font-semibold hover:bg-white hover:text-blue-600 transition">
        Ingresa
      </a>

    <?php else: ?>
      <ul class="flex gap-4">
        <li><a href="/mvc_dev/home" class="hover:underline">Inicio</a></li>
      </ul>
    <?php endif; ?>
  </div>
</nav>

<script>
  document.addEventListener("DOMContentLoaded", function () {
    const btn = document.getElementById("dropdownBtn");
    const menu = document.getElementById("dropdownMenu");

    let open = false;

    btn.addEventListener("click", function (e) {
      e.stopPropagation();
      open = !open;
      menu.classList.toggle("hidden", !open);
    });

    document.addEventListener("click", function () {
      if (open) {
        menu.classList.add("hidden");
        open = false;
      }
    });

    menu.addEventListener("click", function (e) {
      e.stopPropagation();
    });
  });
</script>
