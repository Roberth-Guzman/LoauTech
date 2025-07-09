    <?php
// registrar-codigo.php
include 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $numerodoc = $conn->real_escape_string($_POST['numerodoc']);
    $codigo = $conn->real_escape_string($_POST['codigo']);
    
    // Verificar si la persona existe
    $sql_persona = "SELECT IDper FROM personas WHERE numerodoc = '$numerodoc'";
    $result = $conn->query($sql_persona);
    
    if ($result->num_rows > 0) {
        $persona = $result->fetch_assoc();
        $idper = $persona['IDper'];
        
        // Registrar código
        $sql = "INSERT INTO codigos_barras (codigo, numerodoc, idperlas) 
                VALUES ('$codigo', '$numerodoc', $idper)";
        
        if ($conn->query($sql)) {
            echo "Código registrado exitosamente";
        } else {
            echo "Error: " . $conn->error;
        }
    } else {
        echo "No se encontró una persona con ese documento";
    }
}
?>

<!-- Formulario simple para registrar códigos -->
<form method="POST">
    <input type="text" name="numerodoc" placeholder="Número de documento" required>
    <input type="text" name="codigo" placeholder="Código de barras" required>
    <button type="submit">Registrar</button>
</form>