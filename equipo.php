<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Equipo de Desarrollo - Loautech</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
  <style>
    .hero {
      background: url('assets/images/biblioteca.jpg') no-repeat center center;
      background-size: cover;
      position: relative;
    }
    .hero::after {
      content: "";
      position: absolute;
      inset: 0;
      background-color: rgba(0, 0, 0, 0.5);
    }
    .hero-content {
      position: relative;
      z-index: 1;
    }
  </style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top shadow-sm">
  <div class="container">
    <a class="navbar-brand" href="index.php">
      <img src="assets/images/logo_loautech_white.png" alt="Loautech" width="50" class="me-2" />
      <strong>LOAUTECH</strong>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link active" href="#inicio">Inicio</a></li>
        <li class="nav-item"><a class="nav-link" href="#contacto">Contacto</a></li>
      </ul>
    </div>
  </div>
</nav>

<!-- HERO -->
<section id="inicio" class="hero vh-100 d-flex align-items-center text-white">
  <div class="container hero-content text-center">
    <h1 class="display-4 fw-bold mb-3">SOBRE EL EQUIPO DE DESAROLLO </h1>
    <p class="lead">Conoce a las personas detrás del proyecto Loautech</p>
  </div>
</section>

<!-- ¿QUIÉNES SOMOS? -->
<section class="py-5 bg-white">
  <div class="container">
    <div class="row align-items-center g-5">
      <div class="col-md-6">
        <img src="assets/images/equipo.jpeg" class="img-fluid rounded shadow" alt="Equipo Loautech" />
      </div>
      <div class="col-md-6">
        <h2 class="fw-bold">¿Quiénes somos?</h2>
        <p class="text-muted">Somos cinco estudiantes del SENA, del Centro de Desarrollo Agroempresarial y Turístico del Huila (CDATH), apasionados por la tecnología. Creamos Loautech para facilitar la gestión de entrada y salida de elementos, aplicando lo aprendido durante nuestra formación.</p>
        <p><strong>Año de inicio:</strong> 2024</p>
      </div>
    </div>
  </div>
</section>

<!-- MIEMBROS -->
<section class="py-5 bg-light">
  <div class="container">
    <div class="text-center mb-5">
      <h2 class="fw-bold">Nuestro equipo</h2>
      <p class="text-muted">Estos son los integrantes que hicieron realidad Loautech</p>
    </div>
    <div class="row justify-content-center g-4 text-center">
      <!-- Miembro -->
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
        <div class="col-md-2">
          <img src="assets/images/equipo.jpeg" class="rounded-circle mb-2" width="100" height="100" />
          <h6 class="fw-bold">' . $miembro[0] . '</h6>
          <p class="text-muted small">' . $miembro[1] . '</p>
        </div>';
      }
      ?>
    </div>
  </div>
</section>

<!-- CONTACTO -->
<section id="contacto" class="py-5 bg-primary-subtle">
  <div class="container">
    <div class="text-center mb-5">
      <h2 class="fw-bold">Contáctanos</h2>
      <p class="text-muted">¿Tienes dudas o sugerencias? Escríbenos</p>
    </div>
    <div class="row justify-content-center">
      <div class="col-lg-8">
        <?php
        // Mostrar mensajes de éxito o error
        if (isset($_SESSION['mensaje_contacto'])) {
            $mensaje = $_SESSION['mensaje_contacto'];
            $clase = $mensaje['tipo'] === 'exito' ? 'alert-success' : 'alert-danger';
            echo '<div class="alert ' . $clase . ' alert-dismissible fade show mb-4" role="alert">
                    ' . htmlspecialchars($mensaje['texto']) . '
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                  </div>';
            unset($_SESSION['mensaje_contacto']);
        }
        ?>
        <form class="needs-validation" method="POST" action="procesar_contacto.php" novalidate>
          <div class="mb-3">
            <label for="nombre" class="form-label">Nombre Completo</label>
            <input type="text" class="form-control" id="nombre" name="nombre" required />
            <div class="invalid-feedback">Por favor ingresa tu nombre completo</div>
          </div>
          <div class="mb-3">
            <label for="email" class="form-label">Correo Electrónico</label>
            <input type="email" class="form-control" id="email" name="email" required />
            <div class="invalid-feedback">Ingresa un correo válido</div>
          </div>
          <div class="mb-3">
            <label for="telefono" class="form-label">Teléfono</label>
            <input type="tel" class="form-control" id="telefono" name="telefono" title="Ingresa un número de 10 dígitos" />
          </div>
          <div class="mb-3">
            <label for="mensaje" class="form-label">Mensaje</label>
            <textarea class="form-control" id="mensaje" name="mensaje" rows="4" required></textarea>
            <div class="invalid-feedback">Por favor ingresa tu mensaje</div>
          </div>
          <div class="form-check mb-4">
            <input class="form-check-input" type="checkbox" id="privacidad" name="privacidad" required />
            <label class="form-check-label" for="privacidad">Acepto términos y condiciones</label>
            <div class="invalid-feedback">Debes aceptar los términos y condiciones</div>
          </div>
          <div class="d-grid">
            <button type="submit" class="btn btn-primary btn-lg">
              <i class="bi bi-send-fill me-2"></i>Enviar Mensaje
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</section>

<!-- FOOTER -->
<footer class="bg-primary text-white text-center py-4">
  <div class="container">
    <h5 class="fw-bold mb-2">Equipo Loautech</h5>
    <p class="mb-1 small">&copy; 2025 Todos los derechos reservados.</p>
    <a href="index.php" class="text-white text-decoration-underline small">Volver a la página principal</a>
  </div>
</footer>

<!-- Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>

<!-- Validación del formulario -->
<script>
// Ejemplo de validación del formulario
(function() {
    'use strict';
    window.addEventListener('load', function() {
        // Fetch all the forms we want to apply custom Bootstrap validation styles to
        var forms = document.getElementsByClassName('needs-validation');
        // Loop over them and prevent submission
        var validation = Array.prototype.filter.call(forms, function(form) {
            form.addEventListener('submit', function(event) {
                if (form.checkValidity() === false) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    }, false);
})();
</script>
</body>
</html>
