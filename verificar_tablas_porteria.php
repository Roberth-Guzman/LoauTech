<?php
// Verificar estructura de tablas para el flujo de portería
require_once 'conexion.php';

echo "<h2>🔍 Verificación de Tablas para Portería</h2>";

try {
    // 1. Verificar si existe tabla vigilantes
    echo "<h3>1. Tabla 'vigilantes':</h3>";
    $result = $conn->query("SHOW TABLES LIKE 'vigilantes'");
    if ($result->num_rows > 0) {
        echo "✅ Tabla 'vigilantes' existe<br>";
        $desc = $conn->query("DESCRIBE vigilantes");
        echo "<table border='1'><tr><th>Campo</th><th>Tipo</th><th>Null</th><th>Key</th></tr>";
        while ($row = $desc->fetch_assoc()) {
            echo "<tr><td>{$row['Field']}</td><td>{$row['Type']}</td><td>{$row['Null']}</td><td>{$row['Key']}</td></tr>";
        }
        echo "</table>";
    } else {
        echo "❌ Tabla 'vigilantes' NO existe<br>";
    }

    // 2. Verificar si existe tabla marcaciones
    echo "<h3>2. Tabla 'marcaciones':</h3>";
    $result = $conn->query("SHOW TABLES LIKE 'marcaciones'");
    if ($result->num_rows > 0) {
        echo "✅ Tabla 'marcaciones' existe<br>";
        $desc = $conn->query("DESCRIBE marcaciones");
        echo "<table border='1'><tr><th>Campo</th><th>Tipo</th><th>Null</th><th>Key</th></tr>";
        while ($row = $desc->fetch_assoc()) {
            echo "<tr><td>{$row['Field']}</td><td>{$row['Type']}</td><td>{$row['Key']}</td></tr>";
        }
        echo "</table>";
    } else {
        echo "❌ Tabla 'marcaciones' NO existe<br>";
    }

    // 3. Verificar si existe tabla aprobaciones
    echo "<h3>3. Tabla 'aprobaciones':</h3>";
    $result = $conn->query("SHOW TABLES LIKE 'aprobaciones'");
    if ($result->num_rows > 0) {
        echo "✅ Tabla 'aprobaciones' existe<br>";
        $desc = $conn->query("DESCRIBE aprobaciones");
        echo "<table border='1'><tr><th>Campo</th><th>Tipo</th><th>Null</th><th>Key</th></tr>";
        while ($row = $desc->fetch_assoc()) {
            echo "<tr><td>{$row['Field']}</td><td>{$row['Type']}</td><td>{$row['Null']}</td><td>{$row['Key']}</td></tr>";
        }
        echo "</table>";
    } else {
        echo "❌ Tabla 'aprobaciones' NO existe<br>";
    }

    // 4. Mostrar todas las tablas disponibles
    echo "<h3>4. Todas las tablas disponibles:</h3>";
    $result = $conn->query("SHOW TABLES");
    echo "<ul>";
    while ($row = $result->fetch_array()) {
        echo "<li>" . $row[0] . "</li>";
    }
    echo "</ul>";

    // 5. Verificar solicitudes activas (aprobadas por almacén)
    echo "<h3>5. Solicitudes aprobadas por almacén (estado 'activo'):</h3>";
    $sql = "SELECT p.IDpre, per.nombrecompletoper, e.nombreele, a.estadoaut
            FROM prestamos p
            JOIN personas per ON p.IDpersonas = per.IDper
            JOIN elementos e ON p.IDelementos = e.IDele
            JOIN autorizacion a ON p.IDautorizacion = a.IDaut
            WHERE a.estadoaut = 'activo'
            ORDER BY p.IDpre DESC LIMIT 5";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        echo "<table border='1'><tr><th>ID</th><th>Solicitante</th><th>Elemento</th><th>Estado</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr><td>{$row['IDpre']}</td><td>{$row['nombrecompletoper']}</td><td>{$row['nombreele']}</td><td>{$row['estadoaut']}</td></tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No hay solicitudes aprobadas por almacén</p>";
    }

} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}
?>
