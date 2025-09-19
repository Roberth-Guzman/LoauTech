<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $data['titulo'] ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" href="<?php echo BASE_URL; ?>/public/img/logo_loautech_white.png" type="image/x-icon">
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-2xl">
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="bg-green-500 py-6 px-8 text-center">
                <i class="fas fa-check-circle text-5xl text-white mb-4"></i>
                <h1 class="text-2xl font-bold text-white">¡Registro Exitoso!</h1>
            </div>

            <div class="p-8 text-center">
                <p class="text-gray-700 text-lg mb-8">
                    <?= $data['mensaje'] ?>
                </p>
                
                <a href="<?= BASE_URL ?>" 
                   class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-lg transition-colors">
                    Volver al Inicio
                </a>
            </div>
        </div>
    </div>
</body>
</html>
