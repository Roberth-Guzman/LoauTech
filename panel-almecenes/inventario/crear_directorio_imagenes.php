<?php
// Ruta del directorio donde se guardarán las imágenes de los elementos
$directorio = __DIR__ . '/../../uploads/elementos/';

// Verificar si el directorio no existe y crearlo
if (!file_exists($directorio)) {
    if (mkdir($directorio, 0755, true)) {
        echo "Directorio de imágenes creado exitosamente: " . $directorio . "\n";
    } else {
        die("Error: No se pudo crear el directorio de imágenes. Verifica los permisos.");
    }
} else {
    echo "El directorio de imágenes ya existe: " . $directorio . "\n";
}

echo "Ahora necesitas ejecutar el siguiente comando SQL en tu base de datos para agregar el campo de imagen a la tabla elementos:\n";
echo "ALTER TABLE `elementos` ADD `imagen` VARCHAR(255) NULL DEFAULT NULL AFTER `codigoinventario`;\n";

echo "\nUna vez ejecutado el comando SQL, podrás continuar con la implementación del CRUD con imágenes.";
?>
