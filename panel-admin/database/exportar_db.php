<?php
session_start();
include '../../conexion.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['rol'] != 'admin') {
    header("Location: ../../login.php");
    exit;
}

// Obtener el nombre de la base de datos actual
$db_name = 'loatech';

// Configurar headers para descarga
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . $db_name . '.sql"');

// Función para formatear valores SQL
function format_sql_value($value) {
    if ($value === null) return 'NULL';
    return "'" . addslashes($value) . "'";
}

// Obtener versión del servidor MySQL
$version = $conn->server_info;

// Generar encabezado SQL
echo "-- phpMyAdmin SQL Dump\n";
echo "-- version 5.2.1\n";
echo "-- https://www.phpmyadmin.net/\n";
echo "--\n";
echo "-- Servidor: localhost\n";
echo "-- Tiempo de generación: " . date('d-m-Y H:i:s') . "\n";
echo "-- Versión del servidor: " . $version . "\n";
echo "-- Versión de PHP: " . phpversion() . "\n\n";

echo "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\n";
echo "START TRANSACTION;\n";
echo "SET time_zone = \"+00:00\";\n\n";

echo "/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;\n";
echo "/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;\n";
echo "/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;\n";
echo "/*!40101 SET NAMES utf8mb4 */;\n\n";

// Crear base de datos si no existe
echo "--\n";
echo "-- Base de datos: `$db_name`\n";
echo "--\n\n";
echo "CREATE DATABASE IF NOT EXISTS `$db_name` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;\n";
echo "USE `$db_name`;\n\n";

// Obtener todas las tablas
$tables = array();
$result = $conn->query("SHOW FULL TABLES WHERE Table_Type = 'BASE TABLE'");
while ($row = $result->fetch_row()) {
    $tables[] = $row[0];
}

// Exportar estructura y datos de cada tabla
foreach ($tables as $table) {
    // Obtener estructura de la tabla
    $create_table = $conn->query("SHOW CREATE TABLE `$table`")->fetch_row();
    
    echo "--\n";
    echo "-- Estructura de tabla para la tabla `$table`\n";
    echo "--\n\n";
    
    echo "DROP TABLE IF EXISTS `$table`;\n";
    echo $create_table[1] . ";\n\n";
    
    // Exportar datos de la tabla
    $result_data = $conn->query("SELECT * FROM `$table`");
    if ($result_data->num_rows > 0) {
        echo "--\n";
        echo "-- Volcado de datos para la tabla `$table`\n";
        echo "--\n\n";
        
        $columns = array();
        $columns_result = $conn->query("SHOW COLUMNS FROM `$table`");
        while ($col = $columns_result->fetch_assoc()) {
            $columns[] = '`' . $col['Field'] . '`';
        }
        
        echo "INSERT INTO `$table` (" . implode(', ', $columns) . ") VALUES\n";
        
        $first = true;
        while ($row = $result_data->fetch_assoc()) {
            if (!$first) {
                echo ",\n";
            }
            $first = false;
            
            $values = array();
            foreach ($row as $value) {
                $values[] = format_sql_value($value);
            }
            
            echo "(" . implode(', ', $values) . ")";
        }
        echo ";\n\n";
    }
    
    // Exportar triggers de la tabla
    $triggers = $conn->query("SHOW TRIGGERS LIKE '$table'");
    if ($triggers->num_rows > 0) {
        echo "--\n";
        echo "-- Disparadores `$table`\n";
        echo "--\n\n";
        
        while ($trigger = $triggers->fetch_assoc()) {
            $trigger_name = $trigger['Trigger'];
            $trigger_result = $conn->query("SHOW CREATE TRIGGER `$trigger_name`")->fetch_row();
            echo $trigger_result[2] . ";\n\n";
        }
    }
}

// Exportar procedimientos almacenados
$procedures = $conn->query("SHOW PROCEDURE STATUS WHERE Db = '$db_name'");
if ($procedures->num_rows > 0) {
    echo "--\n";
    echo "-- Procedimientos\n";
    echo "--\n\n";
    
    while ($proc = $procedures->fetch_assoc()) {
        $proc_name = $proc['Name'];
        $proc_result = $conn->query("SHOW CREATE PROCEDURE `$proc_name`")->fetch_row();
        echo "DELIMITER //\n";
        echo $proc_result[2] . "//\n";
        echo "DELIMITER ;\n\n";
    }
}

// Exportar funciones
$functions = $conn->query("SHOW FUNCTION STATUS WHERE Db = '$db_name'");
if ($functions->num_rows > 0) {
    echo "--\n";
    echo "-- Funciones\n";
    echo "--\n\n";
    
    while ($func = $functions->fetch_assoc()) {
        $func_name = $func['Name'];
        $func_result = $conn->query("SHOW CREATE FUNCTION `$func_name`")->fetch_row();
        echo "DELIMITER //\n";
        echo $func_result[2] . "//\n";
        echo "DELIMITER ;\n\n";
    }
}

// Exportar eventos
$events = $conn->query("SHOW EVENTS WHERE Db = '$db_name'");
if ($events->num_rows > 0) {
    echo "--\n";
    echo "-- Eventos\n";
    echo "--\n\n";
    
    while ($event = $events->fetch_assoc()) {
        $event_name = $event['Name'];
        $event_result = $conn->query("SHOW CREATE EVENT `$event_name`")->fetch_row();
        echo $event_result[3] . ";\n\n";
    }
}

echo "--\n";
echo "-- Índices para tablas volcadas\n";
echo "--\n\n";

// Agregar claves foráneas al final
foreach ($tables as $table) {
    $create_table = $conn->query("SHOW CREATE TABLE `$table`")->fetch_row();
    if (strpos($create_table[1], 'CONSTRAINT') !== false) {
        echo "--\n";
        echo "-- Filtros para la tabla `$table`\n";
        echo "--\n\n";
        
        // Extraer solo las líneas de CONSTRAINT
        $lines = explode("\n", $create_table[1]);
        $constraints = array();
        $in_constraint = false;
        $constraint = '';
        
        foreach ($lines as $line) {
            $trimmed = trim($line);
            if (strpos($trimmed, 'CONSTRAINT') === 0) {
                $in_constraint = true;
                $constraint = $trimmed;
            } elseif ($in_constraint) {
                $constraint .= ' ' . $trimmed;
                if (substr($trimmed, -1) === ',') {
                    $constraint = substr($constraint, 0, -1);
                    $constraints[] = $constraint;
                    $in_constraint = false;
                } elseif (substr($trimmed, -1) === ';') {
                    $constraints[] = $constraint;
                    $in_constraint = false;
                }
            }
        }
        
        // Generar ALTER TABLE para cada restricción
        foreach ($constraints as $constraint) {
            echo "ALTER TABLE `$table` ADD $constraint;\n";
        }
        echo "\n";
    }
}

echo "--\n";
echo "-- Restablecer AUTO_INCREMENT para tablas volcadas\n";
echo "--\n\n";

// Restablecer AUTO_INCREMENT para cada tabla
foreach ($tables as $table) {
    $result = $conn->query("SHOW TABLE STATUS LIKE '$table'");
    if ($result && $status = $result->fetch_assoc()) {
        if ($status['Auto_increment'] > 1) {
            echo "ALTER TABLE `$table` AUTO_INCREMENT = " . $status['Auto_increment'] . ";\n";
        }
    }
}

echo "\n";
echo "COMMIT;\n\n";
echo "/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;\n";
echo "/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;\n";
echo "/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;\n";

exit;
?>