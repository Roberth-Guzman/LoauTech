<?php
// Debug para verificar estados de solicitudes
require_once 'conexion.php';

echo "<h2>Debug - Estados de Solicitudes</h2>";

try {
    // 1. Ver todos los préstamos
    echo "<h3>1. Todos los préstamos:</h3>";
    $sql = "SELECT p.IDpre, p.cantidad, a.estadoaut, a.cargoquienautoriza 
            FROM prestamos p 
            JOIN autorizacion a ON p.IDautorizacion = a.IDaut 
            ORDER BY p.IDpre DESC LIMIT 10";
    $result = $conn->query($sql);
    echo "<table border='1'>";
    echo "<tr><th>ID Préstamo</th><th>Cantidad</th><th>Estado Auth</th><th>Cargo Autoriza</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['IDpre'] . "</td>";
        echo "<td>" . $row['cantidad'] . "</td>";
        echo "<td>" . $row['estadoaut'] . "</td>";
        echo "<td>" . $row['cargoquienautoriza'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";

    // 2. Ver específicamente los aprobados
    echo "<h3>2. Solicitudes con estado 'aprobado':</h3>";
    $sql2 = "SELECT p.IDpre, per.nombrecompletoper, e.nombreele, a.estadoaut
             FROM prestamos p
             JOIN personas per ON p.IDpersonas = per.IDper
             JOIN elementos e ON p.IDelementos = e.IDele
             JOIN autorizacion a ON p.IDautorizacion = a.IDaut
             WHERE a.estadoaut = 'aprobado'
             ORDER BY p.IDpre DESC";
    $result2 = $conn->query($sql2);
    
    if ($result2->num_rows > 0) {
        echo "<table border='1'>";
        echo "<tr><th>ID</th><th>Solicitante</th><th>Elemento</th><th>Estado</th></tr>";
        while ($row = $result2->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['IDpre'] . "</td>";
            echo "<td>" . $row['nombrecompletoper'] . "</td>";
            echo "<td>" . $row['nombreele'] . "</td>";
            echo "<td>" . $row['estadoaut'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>❌ No hay solicitudes con estado 'aprobado'</p>";
    }

    // 3. Ver estados únicos en autorizacion
    echo "<h3>3. Estados únicos en tabla autorizacion:</h3>";
    $sql3 = "SELECT DISTINCT estadoaut, COUNT(*) as cantidad FROM autorizacion GROUP BY estadoaut";
    $result3 = $conn->query($sql3);
    echo "<ul>";
    while ($row = $result3->fetch_assoc()) {
        echo "<li>" . $row['estadoaut'] . " (" . $row['cantidad'] . " registros)</li>";
    }
    echo "</ul>";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
