<?php
// Test de conexión y estructura de base de datos
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Test de Conexión a Base de Datos</h2>";

try {
    require_once 'conexion.php';
    echo "✅ Conexión exitosa<br>";
    
    // Test 1: Verificar tablas
    echo "<h3>1. Tablas disponibles:</h3>";
    $result = $conn->query("SHOW TABLES");
    while ($row = $result->fetch_array()) {
        echo "- " . $row[0] . "<br>";
    }
    
    // Test 2: Estructura de elementos
    echo "<h3>2. Estructura tabla 'elementos':</h3>";
    $result = $conn->query("DESCRIBE elementos");
    while ($row = $result->fetch_assoc()) {
        echo "- {$row['Field']} ({$row['Type']}) - Null: {$row['Null']}<br>";
    }
    
    // Test 3: Estructura de prestamos
    echo "<h3>3. Estructura tabla 'prestamos':</h3>";
    $result = $conn->query("DESCRIBE prestamos");
    while ($row = $result->fetch_assoc()) {
        echo "- {$row['Field']} ({$row['Type']}) - Null: {$row['Null']}<br>";
    }
    
    // Test 4: Estructura de autorizacion
    echo "<h3>4. Estructura tabla 'autorizacion':</h3>";
    $result = $conn->query("DESCRIBE autorizacion");
    while ($row = $result->fetch_assoc()) {
        echo "- {$row['Field']} ({$row['Type']}) - Null: {$row['Null']}<br>";
    }
    
    // Test 5: Datos de prueba
    echo "<h3>5. Datos de prueba:</h3>";
    $result = $conn->query("SELECT COUNT(*) as total FROM prestamos");
    $row = $result->fetch_assoc();
    echo "- Total préstamos: " . $row['total'] . "<br>";
    
    $result = $conn->query("SELECT COUNT(*) as total FROM autorizacion WHERE estadoaut = 'pendiente'");
    $row = $result->fetch_assoc();
    echo "- Autorizaciones pendientes: " . $row['total'] . "<br>";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
    echo "❌ Código: " . $e->getCode() . "<br>";
    echo "❌ Archivo: " . $e->getFile() . " línea " . $e->getLine() . "<br>";
}
?>
