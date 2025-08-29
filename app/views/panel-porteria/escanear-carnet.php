<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($data['titulo']); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/css/styles.css"> 
    <style>
        body {
            display: flex;
            background-color: #f4f7f6;
        }
        .main-content-area {
            flex-grow: 1;
            padding-left: 16rem; 
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .page-content-wrapper {
            flex-grow: 1;
            padding: 2rem;
        }
    </style>
</head>
<body>

    <?php require_once __DIR__ . '/includes/sidebar-porteria.php'; ?>

    <!-- Contenido Principal -->
    <div class="main-content-area">
        <div class="page-content-wrapper">
            
            <!-- Placeholder para la futura funcionalidad -->
            <div class="text-center p-10 border-2 border-dashed border-gray-300 rounded-lg h-full flex flex-col justify-center items-center">
                <h2 class="text-2xl font-semibold text-gray-700">Funcionalidad de Escáner de Carnet</h2>
                <p class="mt-2 text-gray-500">Aquí se implementará la funcionalidad para escanear el carnet.</p>
            </div>

        </div>
        <!-- Fin del contenido de la página -->

        <?php require_once __DIR__ . '/includes/footer-porteria.php'; ?>
    </div>

</body>
</html>