<?php
include '../conexion.php';

$token = $_GET['token'] ?? '';

if (empty($token)) {
    echo "Token no válido o faltante.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Restablecer contraseña - Loautech</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">
    <div class="bg-white shadow-lg rounded-lg p-8 max-w-md w-full">
        <h2 class="text-2xl font-bold text-center mb-4">Nueva contraseña</h2>

        <form method="POST" action="guardar_clave.php" class="space-y-4">
            <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

            <div>
                <label for="clave" class="block text-sm font-medium text-gray-700">Nueva contraseña:</label>
                <input type="password" name="clave" id="clave" required minlength="8"
                       class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500">
            </div>

            <button type="submit"
                    class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition">
                Guardar nueva contraseña
            </button>
        </form>

        <div class="mt-4 text-center">
            <a href="../login.php" class="text-blue-600 hover:underline">Volver al login</a>
        </div>
    </div>
</body>
</html>
