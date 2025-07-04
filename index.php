<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LoauTech</title>
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css"
      rel="stylesheet"
    />
</head>
  <body>    
<nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top shadow-sm">
  <div class="container">
    <a class="navbar-brand" href="#">
      <img src="assets/images/logo_loautech_white.png" alt="Loautech" width="50" class="d-inline-block align-text-top me-2" />
      <strong>LOAUTECH</strong>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="#nosotros">Nosotros</a></li>
        <li class="nav-item"><a class="nav-link" href="#sena">Empresa</a></li>
        <li class="nav-item"><a class="nav-link" href="#contacto">Contacto</a></li>
        <li class="nav-item">
    <a href="login.php" class="btn btn-outline-light ms-lg-3">Ingresar</a>
</li>
      </ul>
    </div>
  </div>
</nav>
<!--fin del navbar-->
<!--inico del contenido de la pagina-->
<!-- Hero Section Modificada -->
<section
  id="inicio"
  class="hero-section position-relative overflow-hidden vh-100 pt-5"
>
  <!-- Imagen de fondo -->
  <div class="position-absolute top-0 start-0 h-100 w-100">
    <img
      src="assets/images/biblioteca.jpg"
      alt="Fondo Loautech"
      class="h-100 w-100 object-fit-cover"
      style="filter: brightness(0.7);"
    />
  </div>

  <!-- Contenido superpuesto -->
  <div class="container h-100 position-relative">
    <div class="row align-items-center h-100">
      <div class="col-lg-6 col-xl-5 text-white">
        <div class="bg-dark bg-opacity-75 p-4 p-lg-5 rounded-3 shadow-lg">
          <h1 class="display-3 fw-bold mb-4">Loautech</h1>
          <p class="lead mb-4 fs-5">
            Software dedicado a la entrada y salida de elementos en el Centro de
            Desarrollo Agroempresarial y Turístico del Huila. Nuestra plataforma ofrece una interfaz sencilla e intuitiva, que facilita su uso desde el primer momento.
          </p>
        </div>
      </div>
    </div>
  </div>
</section>

<div class="my-5"></div>
<!--fin de seccion de introduccion -->
<!-- inicio Seccion de Nosotros -->
<section id="nosotros" class="py-5 bg-white">
  <div class="container">
    <!-- Encabezado -->
    <div class="text-center mb-5">
      <h2 class="display-5 fw-bold">Nosotros</h2>
    </div>

    <div class="row g-5 align-items-center">
      <!-- Columna de imagen-->
      <div class="col-lg-6 order-1">
        <div class="position-relative">
          <img
            src="assets/images/equipo.jpeg"
            alt="imagen del grupo loautech"
            class="img-fluid rounded-3 shadow-lg"
          />
        </div>
      </div>

      <!-- Columna de texto  -->
      <div class="col-lg-6 order-2 order-lg-2">
        <h3 class="h4 fw-bold mb-4 text-end">¿Quiénes somos?</h3>
        <p class="text-muted mb-4 text-end">Somos cinco estudiantes del SENA, del Centro de Desarrollo Agroempresarial y Turístico del Huila (CDATH), apasionados por la tecnología y el desarrollo de soluciones prácticas. Juntos creamos Loautech para facilitar la gestión de entrada y salida de elementos, aplicando lo aprendido para mejorar los procesos del centro.</p>

        <!-- Línea de tiempo -->
        <div class="timeline">
          <div class="timeline-item">
            <div class="timeline-content">
              <span class="text-primary fw-bold d-block text-end">2024</span>
              <p class="text-end">
                Inicio del desarrollo de <strong>Loautech</strong>
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<!-- Sección de Equipo -->
<section id="equipo" class="py-5 bg-light">
  <div class="container">
    <div class="text-center mb-5">
      <h2 class="display-5 fw-bold">Nuestro Equipo</h2>
      <p class="lead">Conoce a los miembros del equipo detrás de Loautech</p>
    </div>

    <div class="row justify-content-center g-4">
      <!-- Brando -->
      <div class="col-md-4 col-lg-2 text-center">
        <div class="mx-auto mb-3" style="width: 120px; height: 120px">
          <img
            src="assets/images/equipo.jpeg"
            alt="Miembro del equipo 1"
            class="rounded-circle img-fluid h-100 w-100 object-fit-cover"
          />
        </div>
        <h5 class="fw-bold mb-1">Brando Garcia</h5>
        <p class="text-muted small">Lider de Proyecto</p>
      </div>

      <!-- Betsy-->
      <div class="col-md-4 col-lg-2 text-center">
        <div class="mx-auto mb-3" style="width: 120px; height: 120px">
          <img
            src="assets/images/equipo.jpeg"
            alt="Miembro del equipo 2"
            class="rounded-circle img-fluid h-100 w-100 object-fit-cover"
          />
        </div>
        <h5 class="fw-bold mb-1">Betsy Barreto</h5>
        <p class="text-muted small">Diseñadora</p>
      </div>

      <!-- Roberth -->
      <div class="col-md-4 col-lg-2 text-center">
        <div class="mx-auto mb-3" style="width: 120px; height: 120px">
          <img
            src="assets/images/equipo.jpeg"
            alt="Miembro del equipo 3"
            class="rounded-circle img-fluid h-100 w-100 object-fit-cover"
          />
        </div>
        <h5 class="fw-bold mb-1">Roberth Guzman</h5>
        <p class="text-muted small">Desarrollador</p>
      </div>

      <!-- Lizeth-->
      <div class="col-md-4 col-lg-2 text-center">
        <div class="mx-auto mb-3" style="width: 120px; height: 120px">
          <img
            src="assets/images/equipo.jpeg"
            alt="Miembro del equipo 4"
            class="rounded-circle img-fluid h-100 w-100 object-fit-cover"
          />
        </div>
        <h5 class="fw-bold mb-1">Lizeth Serna</h5>
        <p class="text-muted small">Diseñadora</p>
      </div>

      <!-- Miguel -->
      <div class="col-md-4 col-lg-2 text-center">
        <div class="mx-auto mb-3" style="width: 120px; height: 120px">
          <img
            src="assets/images/equipo.jpeg"
            alt="Miembro del equipo 5"
            class="rounded-circle img-fluid h-100 w-100 object-fit-cover"
          />
        </div>
        <h5 class="fw-bold mb-1">Miguel Castiblanco</h5>
        <p class="text-muted small">Analista</p>
      </div>
    </div>
  </div>
</section>

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

<section id="contacto" class="py-5 bg-white">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-8">
        <div class="text-center mb-5">
          <h2 class="display-5 fw-bold">Contáctanos</h2>
        </div>

        <form class="needs-validation" novalidate>
          <!-- Nombre Completo -->
          <div class="mb-3">
            <label for="nombre" class="form-label">Nombre Completo</label>
            <input type="text" class="form-control" id="nombre" required />
          </div>

          <!-- Correo Electrónico -->
          <div class="mb-3">
            <label for="email" class="form-label">Correo Electrónico</label>
            <input type="email" class="form-control" id="email" required />
            <div class="invalid-feedback">Ingresa un correo válido</div>
          </div>

          <!-- Teléfono -->
          <div class="mb-3">
            <label for="telefono" class="form-label">Teléfono</label>
            <input
              type="tel"
              class="form-control"
              id="telefono"
              title="Ingresa un número de 10 dígitos"
            />
            <div class="invalid-feedback">Formato: 10 dígitos sin espacios</div>
          </div>
          <!-- Mensaje -->
          <div class="mb-4">
            <label for="mensaje" class="form-label">Mensaje</label>
            <textarea
              class="form-control"
              id="mensaje"
              rows="5"
              required
            ></textarea>
            <div class="invalid-feedback">Escribe tu mensaje</div>
          </div>

          <!-- Checkbox de Aceptación -->
          <div class="mb-4 form-check">
            <input
              type="checkbox"
              class="form-check-input"
              id="privacidad"
              required
            />
            <label class="form-check-label" for="checkDefault">
              Acepto <strong>terminos y condiciones </strong>
            </label>
            <div class="invalid-feedback">Debes aceptar las políticas</div>
          </div>
          <!-- Botón de Envío -->
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

<footer class="bg-primary text-white text-center text-lg-start border-top mt-5">
  <div class="container py-4">
    <div class="row justify-content-center">
      <div class="col-md-4 mb-4 text-center text-md-start">
        <h5 class="fw-bold mb-3">Dirección</h5>
        <p class="mb-0">Carrera 10 # 20-30<br />Neiva, Huila</p>
      </div>
      <!-- Columna de Redes Sociales -->
      <div class="col-md-4 mb-4 text-center">
        <h5 class="fw-bold mb-3">Redes Sociales</h5>
        <div class="d-flex justify-content-center gap-3">
          <a href="#" class="text-secondary"
            ><i class="bi bi-facebook fs-5"></i
          ></a>
          <a href="#" class="text-secondary"
            ><i class="bi bi-instagram fs-5"></i
          ></a>
          <a href="#" class="text-secondary"
            ><i class="bi bi-linkedin fs-5"></i
          ></a>
        </div>
      </div>
      <!-- Columna de Contacto -->
      <div class="col-md-4 mb-4 text-center text-md-end">
        <h5 class="fw-bold mb-3">Contacto</h5>
        <p class="mb-0">info<br />+57 300 123 4567</p>
      </div>
    </div>
    <div class="text-center pt-3 border-top mt-4 small text-white">
      &copy; 2025 LOAUTECH. Todos los derechos reservados.
    </div>
  </div>
</footer>
 <script
      src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"
      integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq"
      crossorigin="anonymous"
    ></script>
</body>
</html>