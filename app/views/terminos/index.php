<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Términos y Condiciones - Sistema de Gestión</title>
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
                <i class="fas fa-file-contract text-primary mr-3"></i>
                Términos y Condiciones
            </h1>
            <p class="text-lg text-gray-600">
                Por favor, lea atentamente nuestros términos y condiciones antes de utilizar nuestros servicios.
            </p>
        </div>

        <!-- Contenido de Términos y Condiciones -->
        <div class="space-y-8">
            <div class="bg-white rounded-lg shadow-md p-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">1. Aceptación de Términos</h2>
                <p class="text-gray-700 mb-4">
                    Al acceder y utilizar este sitio web, usted acepta estar sujeto a estos Términos y Condiciones de Uso, 
                    todas las leyes y regulaciones aplicables, y acepta que es responsable del cumplimiento de las leyes locales aplicables.
                </p>
            </div>

            <div class="bg-white rounded-lg shadow-md p-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">2. Uso de la Licencia</h2>
                <p class="text-gray-700 mb-4">
                    Se otorga permiso para descargar temporalmente una copia de los materiales en este sitio web solo para visualización personal y no comercial transitoria.
                </p>
            </div>

            <div class="bg-white rounded-lg shadow-md p-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">3. Limitaciones</h2>
                <p class="text-gray-700 mb-4">
                    En ningún caso, el sitio web o sus proveedores serán responsables por daños (incluyendo, sin limitación, daños por pérdida de datos o beneficio, o debido a la interrupción del negocio) que surjan del uso o la incapacidad de utilizar los materiales en este sitio web.
                </p>
            </div>

            <div class="bg-white rounded-lg shadow-md p-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">4. Enlaces</h2>
                <p class="text-gray-700 mb-4">
                    No hemos revisado todos los sitios vinculados a nuestro sitio web y no somos responsables del contenido de dichos sitios vinculados. La inclusión de cualquier enlace no implica la aprobación del sitio por parte nuestra.
                </p>
            </div>

             <div class="text-center mt-8 flex justify-center gap-4">
                <a href="<?= BASE_URL ?>" class="inline-flex items-center px-6 py-3 border border-gray-300 text-base font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                    <i class="fas fa-home mr-2"></i>
                    Volver al Inicio
                </a>
                <a href="<?= BASE_URL ?>terminos/tecnicos" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-primary hover:bg-secondary focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                    Ver Términos Técnicos Detallados
                    <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php require_once __DIR__ . '/../layouts/footer.php'; ?>
</body>
</html>
