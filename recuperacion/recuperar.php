<?php include '../conexion.php'; ?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recuperar contraseña - Loautech</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">
    <div class="bg-white shadow-lg rounded-lg p-8 max-w-md w-full">
        <h2 class="text-2xl font-bold text-center mb-4">Recuperar contraseña</h2>

        <form method="POST" action="procesar_recuperar.php" class="space-y-4">
            <div>
                <label for="correo" class="block text-sm font-medium text-gray-700">Correo registrado:</label>
                <input type="email" name="correo" id="correo" required
                       class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <button type="submit"
                    class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition">
                Enviar enlace de recuperación
            </button>
        </form>

        <div class="mt-4 text-center">
            <a href="../login.php" class="text-blue-600 hover:underline">Volver al login</a>
        </div>
    </div>
</body>
</html>
