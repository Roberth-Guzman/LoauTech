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

<?php require_once 'app/views/panel-cuentadante/includes/navbar.php'; ?>

<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6 text-gray-800">Solicitudes de Préstamo Pendientes</h1>

    <?php if (empty($data['solicitudes'])): ?>
        <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded relative" role="alert">
            <strong class="font-bold">¡Todo al día!</strong>
            <span class="block sm:inline">No tienes solicitudes pendientes de aprobación.</span>
        </div>
    <?php else: ?>
        <div class="overflow-x-auto bg-white shadow-md rounded-lg">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Solicitante</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Elemento</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cantidad</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha Solicitud</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($data['solicitudes'] as $solicitud): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($solicitud->nombre_solicitante); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($solicitud->nombreele); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($solicitud->cantidad); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap"><?php echo date('d/m/Y H:i', strtotime($solicitud->fecha_solicitud)); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <form action="/mvc_dev/cuentadante/procesarSolicitud" method="POST" class="inline-block">
                                    <input type="hidden" name="prestamo_id" value="<?php echo $solicitud->IDpre; ?>">
                                    <button type="submit" name="accion" value="aprobar" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">Aprobar</button>
                                </form>
                                <form action="/mvc_dev/cuentadante/procesarSolicitud" method="POST" class="inline-block">
                                    <input type="hidden" name="prestamo_id" value="<?php echo $solicitud->IDpre; ?>">
                                    <button type="submit" name="accion" value="rechazar" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">Rechazar</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

</body>
</html>