<?php
session_start();
include '../../conexion.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['rol'] != 'admin') {
    header("Location: ../../login.php");
    exit;
}

// Configurar headers para descarga
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="loatech_backup_'.date('Y-m-d').'.sql"');

// Encabezado SQL
echo "-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: ".date('d-m-Y H:i:s')."
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: ".phpversion()."

SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";
START TRANSACTION;
SET time_zone = \"+00:00\";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `loatech`
--
CREATE DATABASE IF NOT EXISTS `loatech` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `loatech`;

";

// Obtener todas las tablas
$tables = array();
$result = $conn->query("SHOW TABLES");
while ($row = $result->fetch_row()) {
    $tables[] = $row[0];
}

foreach ($tables as $table) {
    // Estructura de la tabla
    $output = "--\n-- Estructura de tabla para la tabla `$table`\n--\n\n";
    $output .= "DROP TABLE IF EXISTS `$table`;\n";
    $create = $conn->query("SHOW CREATE TABLE `$table`")->fetch_row();
    $output .= $create[1] . ";\n\n";
    
    // Datos de la tabla
    $result_data = $conn->query("SELECT * FROM `$table`");
    if ($result_data->num_rows > 0) {
        $output .= "--\n-- Volcado de datos para la tabla `$table`\n--\n\n";
        $output .= "INSERT INTO `$table` VALUES ";
        
        $rows = array();
        while ($row_data = $result_data->fetch_assoc()) {
            $values = array_map(function($v) use ($conn) { 
                return "'" . $conn->real_escape_string($v) . "'"; 
            }, array_values($row_data));
            $rows[] = "(".implode(", ", $values).")";
        }
        $output .= implode(",\n", $rows) . ";\n\n";
    }
    
    echo $output;
}

echo "COMMIT;\n\n";
echo "/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;\n";
echo "/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;\n";
echo "/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;\n";

exit;
?>