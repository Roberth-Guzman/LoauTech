<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Registros</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 10pt;
        }
        h1 {
            text-align: center;
            font-size: 16pt;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        thead {
            background-color: #f2f2f2;
        }
        @page {
            size: A4 portrait;
            margin: 2cm;
        }
    </style>
</head>
<body>
    <h1>Informe de Registros - <?php echo htmlspecialchars($data['fecha_mostrada']); ?></h1>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Elemento</th>
                <th>Código/Serial</th>
                <th>Tipo</th>
                <th>Marca</th>
                <th>Modelo</th>
                <th>Fecha Ingreso</th>
                <th>Hora Ingreso</th>
                <th>Registrado por</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($data['registros'])): ?>
                <tr>
                    <td colspan="9" style="text-align: center;">No hay registros para la fecha seleccionada.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($data['registros'] as $index => $registro): ?>
                    <tr>
                        <td><?php echo $index + 1; ?></td>
                        <td><?php echo htmlspecialchars($registro->nombre); ?></td>
                        <td><?php echo htmlspecialchars($registro->codigo); ?></td>
                        <td><?php echo htmlspecialchars($registro->tipo); ?></td>
                        <td><?php echo htmlspecialchars($registro->marca); ?></td>
                        <td><?php echo htmlspecialchars($registro->modelo); ?></td>
                        <td><?php echo htmlspecialchars($data['fecha_mostrada']); ?></td>
                        <td><?php echo htmlspecialchars((new DateTime($registro->hora_registro))->format('h:i A')); ?></td>
                        <td><?php echo htmlspecialchars($registro->nombrecompletoper); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>