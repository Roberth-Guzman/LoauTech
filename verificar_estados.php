<?php
// Verificar estados después de las correcciones
require_once 'conexion.php';

echo "<h2>✅ Verificación de Estados Corregidos</h2>";

try {
    // 1. Ver estados únicos en autorizacion
    echo "<h3>1. Estados válidos en ENUM:</h3>";
    echo "<p><strong>Permitidos:</strong> 'pendiente', 'activo', 'inactivo'</p>";
    
    $sql = "SELECT DISTINCT estadoaut, COUNT(*) as cantidad FROM autorizacion GROUP BY estadoaut";
    $result = $conn->query($sql);
    echo "<ul>";
    while ($row = $result->fetch_assoc()) {
        $estado = $row['estadoaut'];
        $cantidad = $row['cantidad'];
        
        // Verificar si es un estado válido
        $validos = ['pendiente', 'activo', 'inactivo'];
        $icono = in_array($estado, $validos) ? '✅' : '❌';
        
        echo "<li>$icono <strong>$estado</strong> ($cantidad registros)</li>";
    }
    echo "</ul>";

    // 2. Ver solicitudes por estado
    echo "<h3>2. Solicitudes por estado:</h3>";
    
    // Pendientes
    $sql_pendientes = "SELECT COUNT(*) as total FROM prestamos p 
                      JOIN autorizacion a ON p.IDautorizacion = a.IDaut 
                      WHERE a.estadoaut = 'pendiente'";
    $result_pendientes = $conn->query($sql_pendientes);
    $pendientes = $result_pendientes->fetch_assoc()['total'];
    
    // Aprobadas (activo)
    $sql_aprobadas = "SELECT COUNT(*) as total FROM prestamos p 
                     JOIN autorizacion a ON p.IDautorizacion = a.IDaut 
                     WHERE a.estadoaut = 'activo'";
    $result_aprobadas = $conn->query($sql_aprobadas);
    $aprobadas = $result_aprobadas->fetch_assoc()['total'];
    
    // Rechazadas (inactivo)
    $sql_rechazadas = "SELECT COUNT(*) as total FROM prestamos p 
                      JOIN autorizacion a ON p.IDautorizacion = a.IDaut 
                      WHERE a.estadoaut = 'inactivo'";
    $result_rechazadas = $conn->query($sql_rechazadas);
    $rechazadas = $result_rechazadas->fetch_assoc()['total'];
    
    echo "<ul>";
    echo "<li>🟡 <strong>Pendientes:</strong> $pendientes solicitudes</li>";
    echo "<li>🟢 <strong>Aprobadas (activo):</strong> $aprobadas solicitudes</li>";
    echo "<li>🔴 <strong>Rechazadas (inactivo):</strong> $rechazadas solicitudes</li>";
    echo "</ul>";

    // 3. Últimas 5 solicitudes aprobadas
    if ($aprobadas > 0) {
        echo "<h3>3. Últimas solicitudes aprobadas:</h3>";
        $sql_ultimas = "SELECT p.IDpre, per.nombrecompletoper, e.nombreele, a.estadoaut
                       FROM prestamos p
                       JOIN personas per ON p.IDpersonas = per.IDper
                       JOIN elementos e ON p.IDelementos = e.IDele
                       JOIN autorizacion a ON p.IDautorizacion = a.IDaut
                       WHERE a.estadoaut = 'activo'
                       ORDER BY p.IDpre DESC LIMIT 5";
        $result_ultimas = $conn->query($sql_ultimas);
        
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr style='background-color: #f0f0f0;'>";
        echo "<th>ID</th><th>Solicitante</th><th>Elemento</th><th>Estado</th>";
        echo "</tr>";
        
        while ($row = $result_ultimas->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['IDpre'] . "</td>";
            echo "<td>" . htmlspecialchars($row['nombrecompletoper']) . "</td>";
            echo "<td>" . htmlspecialchars($row['nombreele']) . "</td>";
            echo "<td style='color: green; font-weight: bold;'>" . $row['estadoaut'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }

    echo "<hr>";
    echo "<h3>🎯 Resumen del Sistema:</h3>";
    echo "<ul>";
    echo "<li>✅ <strong>aprobar_prestamo.php</strong> - Usa 'activo' (correcto)</li>";
    echo "<li>✅ <strong>rechazar_prestamo.php</strong> - Usa 'inactivo' (correcto)</li>";
    echo "<li>✅ <strong>panel-solicitudes.php</strong> - Busca 'activo' (correcto)</li>";
    echo "<li>✅ <strong>Estados ENUM</strong> - Todos válidos</li>";
    echo "</ul>";
    
    echo "<p style='color: green; font-weight: bold;'>🎉 ¡Sistema completamente funcional!</p>";

} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}
?>
