<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Términos y Condiciones Técnicos - Sistema de Gestión</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#2563eb',
                        secondary: '#1e40af',
                        accent: '#3b82f6'
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <!-- Main Content -->
    <div class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">
                <i class="fas fa-shield-alt text-primary mr-3"></i>
                Términos y Condiciones Técnicos
            </h1>
            <p class="text-lg text-gray-600">
                Detalles técnicos sobre el uso de nuestro sistema y servicios.
            </p>
        </div>

        <!-- Sección de Navegación -->
        <div class="mb-8 bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-xl font-semibold mb-4 text-gray-800">Contenido</h2>
            <ul class="space-y-2">
                <li><a href="#datos" class="text-primary hover:underline">Manejo de Datos Personales</a></li>
                <li><a href="#cookies" class="text-primary hover:underline">Política de Cookies</a></li>
                <li><a href="#sesiones" class="text-primary hover:underline">Gestión de Sesiones</a></li>
                <li><a href="#tecnologia" class="text-primary hover:underline">Tecnologías Utilizadas</a></li>
                <li><a href="#privacidad" class="text-primary hover:underline">Protección de Privacidad</a></li>
                <li><a href="#responsabilidades" class="text-primary hover:underline">Responsabilidades</a></li>
                <li><a href="#contacto" class="text-primary hover:underline">Información de Contacto</a></li>
            </ul>
        </div>

        <!-- Sección de Datos Personales -->
        <div id="datos" class="bg-white rounded-lg shadow-md p-8 mb-8">
            <h2 class="text-3xl font-bold text-gray-900 mb-6">
                <i class="fas fa-database text-primary mr-3"></i>
                Manejo de Datos Personales
            </h2>
            <div class="space-y-4 text-gray-700">
                <p>Nuestra aplicación implementa estrictas medidas de seguridad para proteger sus datos personales:</p>
                <ul class="list-disc pl-6 space-y-2">
                    <li>Almacenamiento seguro con cifrado de extremo a extremo</li>
                    <li>Acceso restringido solo a personal autorizado</li>
                    <li>Cumplimiento con las regulaciones de protección de datos aplicables</li>
                    <li>Procesamiento de datos únicamente para los fines autorizados</li>
                </ul>
                <p>Puede solicitar la eliminación de sus datos personales en cualquier momento contactando a nuestro equipo de soporte.</p>
            </div>
        </div>

        <!-- Sección de Cookies -->
        <div id="cookies" class="bg-white rounded-lg shadow-md p-8 mb-8">
            <h2 class="text-3xl font-bold text-gray-900 mb-6">
                <i class="fas fa-cookie-bite text-primary mr-3"></i>
                Política de Cookies
            </h2>
            <div class="space-y-4 text-gray-700">
                <p>Utilizamos cookies para mejorar su experiencia en nuestro sitio web:</p>
                <ul class="list-disc pl-6 space-y-2">
                    <li>Cookies de sesión para mantener su inicio de sesión</li>
                    <li>Cookies de preferencias para recordar sus configuraciones</li>
                    <li>Cookies analíticas para entender cómo se utiliza nuestro sitio</li>
                </ul>
                <p>Puede gestionar sus preferencias de cookies en cualquier momento a través de la configuración de su navegador.</p>
            </div>
        </div>

        <!-- Sección de Sesiones -->
        <div id="sesiones" class="bg-white rounded-lg shadow-md p-8 mb-8">
            <h2 class="text-3xl font-bold text-gray-900 mb-6">
                <i class="fas fa-key text-primary mr-3"></i>
                Gestión de Sesiones
            </h2>
            <div class="space-y-4 text-gray-700">
                <p>Para garantizar la seguridad de su cuenta, implementamos:</p>
                <ul class="list-disc pl-6 space-y-2">
                    <li>Inicio de sesión seguro con autenticación de dos factores</li>
                    <li>Cierre de sesión automático después de períodos de inactividad</li>
                    <li>Notificaciones de inicio de sesión sospechoso</li>
                    <li>Registro de actividad de la cuenta</li>
                </ul>
            </div>
        </div>

        <!-- Sección de Tecnologías -->
        <div id="tecnologia" class="bg-white rounded-lg shadow-md p-8 mb-8">
            <h2 class="text-3xl font-bold text-gray-900 mb-6">
                <i class="fas fa-microchip text-primary mr-3"></i>
                Tecnologías Utilizadas
            </h2>
            <div class="space-y-4 text-gray-700">
                <p>Nuestra plataforma utiliza tecnologías modernas y seguras:</p>
                <ul class="list-disc pl-6 space-y-2">
                    <li>PHP 8.1+ para el procesamiento del lado del servidor</li>
                    <li>MySQL 8.0+ para el almacenamiento de datos</li>
                    <li>HTML5, CSS3 y JavaScript para la interfaz de usuario</li>
                    <li>Framework MVC personalizado para una arquitectura limpia</li>
                </ul>
            </div>
        </div>

        <!-- Sección de Privacidad -->
        <div id="privacidad" class="bg-white rounded-lg shadow-md p-8 mb-8">
            <h2 class="text-3xl font-bold text-gray-900 mb-6">
                <i class="fas fa-user-shield text-primary mr-3"></i>
                Protección de Privacidad
            </h2>
            <div class="space-y-4 text-gray-700">
                <p>Nos comprometemos a proteger su privacidad:</p>
                <ul class="list-disc pl-6 space-y-2">
                    <li>No compartimos su información personal con terceros sin su consentimiento</li>
                    <li>Implementamos medidas de seguridad físicas y digitales</li>
                    <li>Realizamos auditorías periódicas de seguridad</li>
                    <li>Cumplimos con las regulaciones de privacidad aplicables</li>
                </ul>
            </div>
        </div>

        <!-- Sección de Responsabilidades -->
        <div id="responsabilidades" class="bg-white rounded-lg shadow-md p-8 mb-8">
            <h2 class="text-3xl font-bold text-gray-900 mb-6">
                <i class="fas fa-balance-scale text-primary mr-3"></i>
                Responsabilidades
            </h2>
            <div class="space-y-4 text-gray-700">
                <p>Como usuario de nuestra plataforma, usted es responsable de:</p>
                <ul class="list-disc pl-6 space-y-2">
                    <li>Mantener la confidencialidad de sus credenciales de acceso</li>
                    <li>Notificarnos cualquier actividad sospechosa en su cuenta</li>
                    <li>Utilizar la plataforma de acuerdo con estos términos</li>
                    <li>No realizar actividades que puedan dañar el sistema o a otros usuarios</li>
                </ul>
            </div>
        </div>

        <!-- Sección de Contacto -->
        <div id="contacto" class="bg-white rounded-lg shadow-md p-8 mb-8">
            <h2 class="text-3xl font-bold text-gray-900 mb-6">
                <i class="fas fa-envelope text-primary mr-3"></i>
                Información de Contacto
            </h2>
            <div class="space-y-4 text-gray-700">
                <p>Si tiene preguntas sobre estos términos técnicos, puede contactarnos a través de:</p>
                <ul class="list-disc pl-6 space-y-2">
                    <li>Email: soporte@ejemplo.com</li>
                    <li>Teléfono: +1 (555) 123-4567</li>
                    <li>Horario de atención: Lunes a Viernes, 9:00 AM - 6:00 PM</li>
                </ul>
                <p>Nos comprometemos a responder a todas las consultas en un plazo máximo de 48 horas hábiles.</p>
            </div>
        </div>
        <!-- Botón de regreso -->
        <div class="text-center mt-8 flex justify-center gap-4">
            <a href="<?= BASE_URL ?>terminos" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-primary hover:bg-secondary focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                <i class="fas fa-arrow-left mr-2"></i>
                Volver a Términos Generales
            </a>
            <a href="<?= BASE_URL ?>" class="inline-flex items-center px-6 py-3 border border-gray-300 text-base font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                <i class="fas fa-home mr-2"></i>
                Volver al Inicio
            </a>
        </div>
    </div>

    <!-- Footer -->
    <?php require_once __DIR__ . '/../layouts/footer.php'; ?>
</body>
</html>
