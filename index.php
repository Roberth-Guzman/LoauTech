<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>LoauTech</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
  <nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top shadow-sm">
    <div class="container">
      <a class="navbar-brand" href="#">
        <img src="assets/images/logo_loautech_white.png" alt="Loautech" width="50"
          class="d-inline-block align-text-top me-2" />
        <strong>LOAUTECH</strong>
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item"><a class="nav-link px-3" href="#sena">Empresa</a></li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle text-white rounded px-3 " href="#" id="ingresarDropdown"
              role="button" data-bs-toggle="dropdown" aria-expanded="false">
              Ingresar
            </a>
            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-dark">
              <li><a class="dropdown-item" href="login.php">Como Usuario</a></li>
              <li><a class="dropdown-item" href="invitado.php">Como Invitado</a></li>
            </ul>
          </li>


        </ul>
      </div>
    </div>
  </nav>
  <!--fin del navbar-->
  <!--inico del contenido de la pagina-->
  <section id="inicio" class="hero-section position-relative overflow-hidden vh-100 pt-5">
    <!-- Imagen de fondo -->
    <div class="position-absolute top-0 start-0 h-100 w-100">
      <img src="assets/images/biblioteca.jpg" alt="Fondo Loautech" class="h-100 w-100 object-fit-cover"
        style="filter: brightness(0.7);" />
    </div>

    <!-- Contenido superpuesto -->
    <div class="container h-100 position-relative">
      <div class="row align-items-center h-100">
        <div class="col-lg-6 col-xl-5 text-white">
          <div class="bg-dark bg-opacity-75 p-4 p-lg-5 rounded-3 shadow-lg">
            <h1 class="display-3 fw-bold mb-4">Loautech</h1>
            <p class="lead mb-4 fs-5">
              Software dedicado a la entrada y salida de elementos en el Centro de
              Desarrollo Agroempresarial y Turístico del Huila. Nuestra plataforma ofrece una interfaz sencilla e
              intuitiva, que facilita su uso desde el primer momento.
            </p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <div class="my-5"></div>
  <!--fin de seccion de introduccion -->
  <section id="sena" class="py-5 bg-light">
    <div class="container">
      <div class="text-center mb-5">
        <h2 class="display-5 fw-bold">Sobre el SENA</h2>
        <p class="lead mb-0">
          El Servicio Nacional de Aprendizaje (SENA) es la entidad pública líder
          en formación técnica y tecnológica en Colombia.
        </p>
      </div>
      <div class="row justify-content-center g-4">
        <!-- Misión -->
        <div class="col-md-4">
          <div class="card h-100 shadow-sm border-0">
            <div class="card-body text-center">
              <i class="bi bi-bullseye display-4 text-success mb-3"></i>
              <h3 class="h5 fw-bold">Misión</h3>
              <p class="text-muted">
                El SENA está encargado de cumplir la función que le corresponde al
                Estado de invertir en el desarrollo social y técnico de los
                trabajadores colombianos, ofreciendo y ejecutando la formación
                profesional integral, para la incorporación y el desarrollo de las
                personas en actividades productivas que contribuyan al desarrollo
                social, económico y tecnológico del país (Ley 119/1994).
              </p>
            </div>
          </div>
        </div>
        <!-- Visión -->
        <div class="col-md-4">
          <div class="card h-100 shadow-sm border-0">
            <div class="card-body text-center">
              <i class="bi bi-eye display-4 text-info mb-3"></i>
              <h3 class="h5 fw-bold">Visión</h3>
              <p class="text-muted">
                Para el año 2026, el Servicio Nacional de Aprendizaje - SENA
                estará a la vanguardia de la cualificación del talento humano,
                tanto a nivel nacional como internacional. Esto se logrará a
                través de la formación profesional integral, el empleo, el
                emprendimiento y el reconocimiento de aprendizajes previos.
                Nuestro objetivo es generar valor público y fortalecer la economía
                campesina, popular, verde y digital, siempre con un enfoque
                diferencial orientado a la construcción del cambio, la
                transformación productiva, la soberanía alimentaria y la
                consolidación de una paz total, materializando así la autonomía
                territorial, y promoviendo la justicia social, ambiental y
                económica.
              </p>
            </div>
          </div>
        </div>
        <!-- Valores-->
        <div class="col-md-4">
          <div class="card h-100 shadow-sm border-0">
            <div class="card-body text-center">
              <i class="bi bi-stars display-4 text-warning mb-3"></i>
              <h3 class="h5 fw-bold">Valores Institucionales</h3>
              <p class="text-muted">
                El SENA se fundamenta en valores que guían el comportamiento de
                sus funcionarios y aprendices:
              </p>
              <ul class="list-unstyled text-muted mb-0">
                <li><strong>Honestidad</strong></li>
                <li><strong>Respeto</strong></li>
                <li><strong>Compromiso</strong></li>
                <li><strong>Diligencia</strong></li>
                <li><strong>Justicia</strong></li>
                <li><strong>Solidaridad</strong></li>
                <li><strong>Lealtad</strong></li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <footer class="bg-primary text-white text-center text-lg-start border-top mt-5">
    <div class="container py-4">
      <div class="row text-center text-md-start justify-content-between">
        <!-- Dirección -->
        <div class="col-md-6 mb-4">
          <h5 class="fw-bold mb-2">Dirección</h5>
          <p class="mb-0">Carrera 10 # 20-30<br>Neiva, Huila</p>
        </div>

        <!-- Contacto -->
        <div class="col-md-6 mb-4 text-md-end">
          <h5 class="fw-bold mb-2">Contacto</h5>
          <p class="mb-0">info<br>+57 300 123 4567</p>
        </div>
      </div>
      <hr class="border-light opacity-25">

      <!-- About us destacado -->
      <div class="text-center">
        <a href="equipo.php" class="text-white fw-bold fs-5 text-decoration-underline">
          About us / Acerca del equipo de desarrollo
        </a>
        <p class="small mt-2 mb-0">&copy; 2025 LOAUTECH. Todos los derechos reservados.</p>
      </div>
    </div>
  </footer>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq"
    crossorigin="anonymous"></script>
</body>
</html>