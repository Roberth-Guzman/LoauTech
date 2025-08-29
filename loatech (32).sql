-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 19-08-2025 a las 11:58:36
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `loatech`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `aprobaciones`
--

CREATE TABLE `aprobaciones` (
  `id` int(11) NOT NULL,
  `IDprestamo` int(11) NOT NULL,
  `estado` enum('pendiente_almacen','aprobado','rechazado','pendiente') NOT NULL DEFAULT 'pendiente',
  `fecha_creacion` datetime NOT NULL DEFAULT current_timestamp(),
  `fecha_aprobacion` datetime DEFAULT NULL,
  `fecha_rechazo` datetime DEFAULT NULL,
  `fecha_actualizacion` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `usuario` varchar(100) DEFAULT NULL COMMENT 'Usuario que realizó la acción',
  `tipo_aprobacion` enum('cuentadante','almacen') DEFAULT NULL,
  `aprobado_por` varchar(100) DEFAULT NULL,
  `ip_origen` varchar(45) DEFAULT NULL,
  `motivo` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Tabla para el seguimiento de aprobaciones de préstamos';

--
-- Volcado de datos para la tabla `aprobaciones`
--

INSERT INTO `aprobaciones` (`id`, `IDprestamo`, `estado`, `fecha_creacion`, `fecha_aprobacion`, `fecha_rechazo`, `fecha_actualizacion`, `usuario`, `tipo_aprobacion`, `aprobado_por`, `ip_origen`, `motivo`) VALUES
(1, 21, 'rechazado', '2025-07-25 11:09:17', NULL, NULL, '2025-07-28 07:45:07', NULL, 'almacen', 'usuario pruebas', NULL, 'xd'),
(5, 19, 'rechazado', '2025-07-25 16:15:27', NULL, NULL, '2025-07-28 07:31:35', NULL, 'almacen', 'usuario pruebas', NULL, '45'),
(10, 22, 'rechazado', '2025-07-27 16:20:19', NULL, NULL, '2025-07-28 07:44:58', NULL, 'almacen', 'usuario pruebas', NULL, 'xd'),
(16, 23, 'rechazado', '2025-07-27 16:50:30', NULL, NULL, '2025-07-28 07:37:47', NULL, 'almacen', 'usuario pruebas', NULL, 'xd'),
(18, 24, 'rechazado', '2025-07-28 06:50:32', NULL, NULL, '2025-07-28 07:36:35', NULL, 'almacen', 'usuario pruebas', NULL, 'xd'),
(24, 25, 'rechazado', '2025-07-28 07:10:53', NULL, NULL, '2025-07-28 07:31:22', NULL, 'almacen', 'usuario pruebas', NULL, 'no tiene stock'),
(45, 27, 'rechazado', '2025-07-28 07:47:15', NULL, NULL, NULL, NULL, 'almacen', 'usuario pruebas', NULL, 'xd'),
(46, 26, 'aprobado', '2025-07-28 07:47:18', NULL, NULL, '2025-07-28 07:47:20', NULL, 'almacen', 'usuario pruebas', NULL, ''),
(48, 29, 'aprobado', '2025-07-28 09:00:29', NULL, NULL, NULL, NULL, 'almacen', 'usuario pruebas', NULL, ''),
(49, 30, 'aprobado', '2025-07-28 09:01:54', NULL, NULL, NULL, NULL, 'almacen', 'usuario pruebas', NULL, ''),
(50, 31, 'rechazado', '2025-07-30 16:04:05', NULL, NULL, NULL, NULL, 'almacen', 'usuario pruebas', NULL, 'no'),
(51, 32, 'aprobado', '2025-07-30 16:04:09', NULL, NULL, '2025-08-01 10:54:49', NULL, 'almacen', 'usuario pruebas', NULL, ''),
(52, 35, 'rechazado', '2025-07-30 16:07:30', NULL, NULL, '2025-08-01 06:36:52', NULL, 'cuentadante', 'Roberth Adrian Guzman Salazar', NULL, 'xd'),
(53, 36, 'aprobado', '2025-07-30 16:12:35', NULL, NULL, '2025-07-30 16:26:13', NULL, 'almacen', 'usuario pruebas', NULL, ''),
(54, 9, 'rechazado', '2025-07-30 16:17:35', NULL, NULL, '2025-08-03 16:14:50', NULL, 'cuentadante', 'Roberth Adrian Guzman Salazar', NULL, 'a'),
(55, 10, 'pendiente', '2025-07-30 16:17:35', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(56, 11, 'pendiente', '2025-07-30 16:17:35', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(57, 12, 'pendiente', '2025-07-30 16:17:35', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(58, 13, 'pendiente', '2025-07-30 16:17:35', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(59, 14, 'pendiente', '2025-07-30 16:17:35', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(60, 15, 'pendiente', '2025-07-30 16:17:35', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(61, 16, 'pendiente', '2025-07-30 16:17:35', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(62, 17, 'pendiente', '2025-07-30 16:17:35', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(63, 18, 'pendiente', '2025-07-30 16:17:35', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(64, 20, 'pendiente', '2025-07-30 16:17:35', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(65, 28, 'pendiente', '2025-07-30 16:17:35', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(66, 33, 'rechazado', '2025-07-30 16:17:35', NULL, NULL, '2025-07-30 16:19:08', NULL, 'almacen', 'usuario pruebas', NULL, 'x'),
(67, 34, 'rechazado', '2025-07-30 16:17:35', NULL, NULL, '2025-07-30 16:19:11', NULL, 'almacen', 'usuario pruebas', NULL, 'a'),
(71, 37, 'aprobado', '2025-07-30 16:19:46', NULL, NULL, '2025-07-30 16:20:10', NULL, 'almacen', 'usuario pruebas', NULL, ''),
(75, 38, 'aprobado', '2025-07-30 22:53:13', NULL, NULL, '2025-07-30 22:56:16', NULL, 'almacen', 'usuario pruebas', NULL, '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `auditoria_general`
--

CREATE TABLE `auditoria_general` (
  `IDauditoria` int(11) NOT NULL,
  `numerodoc` bigint(20) NOT NULL,
  `tablaafectada` varchar(50) NOT NULL,
  `idregistroafectado` int(11) NOT NULL,
  `accion` enum('insert','update','delete') NOT NULL,
  `descripcion` text NOT NULL,
  `fechaevento` datetime NOT NULL,
  `IDautori` int(11) NOT NULL,
  `IDpresta` int(11) NOT NULL,
  `IDingresoele` int(11) NOT NULL,
  `IDelemento` int(11) NOT NULL,
  `IDdetallespresta` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `auditoria_login`
--

CREATE TABLE `auditoria_login` (
  `IDlog` int(11) NOT NULL,
  `numerodoc` bigint(20) NOT NULL,
  `fecha_login` datetime NOT NULL,
  `exito` tinyint(1) NOT NULL,
  `ipOrigen` varchar(45) NOT NULL,
  `navegador` varchar(200) NOT NULL,
  `observaciones` text NOT NULL,
  `Idcuen` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `autorizacion`
--

CREATE TABLE `autorizacion` (
  `IDaut` int(11) NOT NULL,
  `VoBoCuentadanteaut` varchar(250) NOT NULL,
  `nomquienaturiza` varchar(255) NOT NULL,
  `cargoquienautoriza` varchar(50) NOT NULL,
  `firmaquienautoriza` varchar(50) NOT NULL,
  `estadoaut` enum('pendiente','pendiente_almacen','activo','aprobado','rechazado','inactivo','en_prestamo') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `autorizacion`
--

INSERT INTO `autorizacion` (`IDaut`, `VoBoCuentadanteaut`, `nomquienaturiza`, `cargoquienautoriza`, `firmaquienautoriza`, `estadoaut`) VALUES
(4, 'veronica', 'julia', 'biblioteca', 'lucas', 'pendiente'),
(6, 'Pendiente', 'Roberth Adrian Guzman Salazar', 'sistema', 'pendiente', 'rechazado'),
(7, 'Pendiente', 'brandod', 'sistema', 'pendiente', 'pendiente'),
(8, 'Pendiente', 'brandod', 'sistema', 'pendiente', 'pendiente'),
(9, 'Pendiente', 'brandod', 'sistema', 'pendiente', 'pendiente'),
(10, 'Pendiente', 'brandod', 'sistema', 'pendiente', 'pendiente'),
(11, 'Pendiente', 'brandod', 'sistema', 'pendiente', 'pendiente'),
(12, 'Pendiente', 'brandod', 'sistema', 'pendiente', 'pendiente'),
(13, 'Pendiente', 'brandod', 'sistema', 'pendiente', 'pendiente'),
(14, 'Pendiente', 'brandod', 'sistema', 'pendiente', 'pendiente'),
(15, 'Pendiente', 'brandod', 'sistema', 'pendiente', 'pendiente'),
(16, 'Pendiente', 'brandod', 'sistema', 'pendiente', 'pendiente'),
(17, 'Pendiente', 'brandod', 'sistema', 'pendiente', 'pendiente'),
(18, 'Pendiente', 'brandod', 'sistema', 'pendiente', 'pendiente'),
(19, 'Pendiente', 'Roberth Adrian Guzman Salazar', 'sistema', 'pendiente', 'pendiente'),
(20, 'Pendiente | Salida autorizada por portería: porteria robsito - 2025-07-27 17:59:02', 'Roberth Adrian Guzman Salazar', 'sistema', 'pendiente', 'pendiente'),
(21, 'Pendiente', 'Roberth Adrian Guzman Salazar', 'sistema', 'pendiente', 'pendiente'),
(22, 'Pendiente', 'Roberth Adrian Guzman Salazar', 'sistema', 'pendiente', 'pendiente'),
(23, 'Pendiente', 'Roberth Adrian Guzman Salazar', 'sistema', 'pendiente', 'pendiente'),
(24, 'Pendiente', 'Roberth Adrian Guzman Salazar', 'sistema', 'pendiente', 'pendiente'),
(25, 'Pendiente | Salida autorizada por portería: porteria robsito - 2025-07-28 08:25:22', 'Roberth Adrian Guzman Salazar', 'sistema', 'pendiente', 'en_prestamo'),
(26, 'Pendiente', 'Roberth Adrian Guzman Salazar', 'sistema', 'pendiente', 'pendiente'),
(27, 'Pendiente', 'Roberth Adrian Guzman Salazar', 'sistema', 'pendiente', 'pendiente'),
(28, 'Pendiente', 'Roberth Adrian Guzman Salazar', 'sistema', 'pendiente', 'pendiente'),
(29, 'Pendiente', 'Roberth Adrian Guzman Salazar', 'sistema', 'pendiente', 'aprobado'),
(30, 'Pendiente', 'Roberth Adrian Guzman Salazar', 'sistema', 'pendiente', 'rechazado'),
(31, 'Pendiente', 'Roberth Adrian Guzman Salazar', 'sistema', 'pendiente', 'rechazado'),
(32, 'Pendiente', 'Roberth Adrian Guzman Salazar', 'sistema', 'pendiente', 'rechazado'),
(33, 'Pendiente', 'Roberth Adrian Guzman Salazar', 'sistema', 'pendiente', 'aprobado'),
(34, 'Pendiente', 'Roberth Adrian Guzman Salazar', 'sistema', 'pendiente', 'aprobado'),
(35, 'Pendiente | Salida autorizada por portería: porteria robsito - 2025-08-01 10:55:22', 'Roberth Adrian Guzman Salazar', 'sistema', 'pendiente', 'en_prestamo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `codigos_barras`
--

CREATE TABLE `codigos_barras` (
  `IDcodigo` int(11) NOT NULL,
  `codigo` varchar(100) NOT NULL,
  `numerodoc` bigint(15) NOT NULL,
  `fecha_registro` datetime NOT NULL DEFAULT current_timestamp(),
  `idperlas` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `configuracion_correo`
--

CREATE TABLE `configuracion_correo` (
  `id` int(11) NOT NULL,
  `servidor_smtp` varchar(100) DEFAULT NULL,
  `puerto` int(11) DEFAULT NULL,
  `usuario` varchar(100) DEFAULT NULL,
  `contrasena` varchar(255) DEFAULT NULL,
  `cifrado` varchar(10) DEFAULT NULL,
  `correo_remitente` varchar(100) DEFAULT NULL,
  `nombre_remitente` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `contactos`
--

CREATE TABLE `contactos` (
  `IDcont` int(11) NOT NULL,
  `numerocont` bigint(10) NOT NULL,
  `direccioncont` varchar(50) NOT NULL,
  `correocont` varchar(50) NOT NULL,
  `estadocont` enum('activo','inactivo') NOT NULL,
  `IDperso` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `contactos`
--

INSERT INTO `contactos` (`IDcont`, `numerocont`, `direccioncont`, `correocont`, `estadocont`, `IDperso`) VALUES
(13, 321131212111, 'calle 5 a sur', 'branii@gmail.com', 'activo', 14),
(15, 3205589997, 'calle 4 a sur', 'brani@gmail.com', 'activo', 16),
(16, 3205589988, 'calle 3 a sur', 'bran@gmail.com', 'activo', 17),
(17, 3211312121, 'calle 4 a sur', 'branaa@gmail.com', 'activo', 18),
(18, 3204887815, 'carrera 8: 22', 'miguelcastiblanco2468@gmail.com', 'activo', 19),
(19, 3204887815, 'calle 3 a sur', 'miguelcastiblanco2468@gmail.com', 'activo', 20),
(24, 3211231234, 'crar 3 a54 1', 'usuario@gmail.com', 'activo', 25),
(25, 3334333333, 'crra 4a 2a 1', 'rob@gmail.com', 'activo', 26),
(27, 3123426546, 'calle 1 sur # 3-46', 'sernacordobalizethdayana@gmail.com', 'activo', 28),
(28, 32401848122, 'crra 3a 1 bogota', 'miguela@gmail.com', 'activo', 29),
(29, 3204441607, 'crra 4e 2a 67', 'adriiang322@gmail.com', 'activo', 30),
(30, 3204441606, 'crra 3a 2a 104', 'robguzmandev@gmail.com', 'activo', 31),
(31, 3211312120, 'calle 4 a sur', 'branssss@gmail.com', 'activo', 32),
(32, 6666888333, 'calle 5 a sur', 'braniill@gmail.com', 'activo', 33),
(33, 96321473266, 'calle 4', 'branaaff@gmail.com', 'activo', 34),
(35, 3209526705, 'calle 4 a sur', 'brangariza@gmail.com', 'activo', 36),
(36, 3211312121, 'calle 3 a sur', 'branaa@gmail.com', 'activo', 37),
(37, 6666666677, 'calle 3 a sur', 'branaa@gmail.com', 'activo', 38),
(38, 3211312121, 'calle 3 a sur', 'brangariza@gmail.com', 'activo', 39),
(39, 3211312121, 'calle 3 a sur', 'bran@gmail.com', 'activo', 40),
(40, 3209526705, 'calle 3 a sur', 'brangariza@gmail.com', 'activo', 41),
(41, 3104441607, 'crra 4a 2a 12a', 'adriiang222@gmail.com', 'activo', 42);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cuentas`
--

CREATE TABLE `cuentas` (
  `IDcue` int(11) NOT NULL,
  `numerodoc` bigint(15) NOT NULL,
  `contracue` varchar(200) NOT NULL,
  `estadocue` enum('activo','inactivo') NOT NULL,
  `reset_token` varchar(64) DEFAULT NULL,
  `token_expira` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `cuentas`
--

INSERT INTO `cuentas` (`IDcue`, `numerodoc`, `contracue`, `estadocue`, `reset_token`, `token_expira`) VALUES
(13, 1014862578, '$2y$10$PSqqvFGPMv0nW0x5FkqAtOdQ2jMpuoZwZgO7uZ/qCBis5/CYOJ6SO', 'activo', NULL, NULL),
(15, 12365478, '$2y$10$tjoUJ3zn1G2Ok67NcKt66ur8cmcEeh4nmh.NEh0cI61yZMXRo7dBS', 'activo', NULL, NULL),
(16, 10148999, '$2y$10$GYAr20jymPZQHav4yaiaf.dYCBtsWoXrEx8h11WfeDUiH.566L7nm', 'activo', NULL, NULL),
(17, 99995999, '$2y$10$gBrUaM2JaP/ClfBfVu4L5.UDg6Dz/oDPkeX.R9ZaP5SVqcTOLt45y', 'activo', NULL, NULL),
(24, 22222222, '$2y$10$ZdZcHqbWgfui9gEyixa7feB7OeNgMg5R32YMhh6I71EWu7qaGSeii', 'activo', NULL, NULL),
(25, 33333333, '$2y$10$iGP.3E1qxiYiGYg8ZOWEMOHxVehn3IuvJ7EDat4EiR5DOGe2Uc3QW', 'activo', NULL, NULL),
(27, 1081401177, '$2y$10$ngOUKPeWwoQ9OeCy8GrBVOt8ZFv/Vv4eYQyMrWolFFeKIn.umAoiy', 'activo', NULL, NULL),
(28, 1081721028, '$2y$10$ezcBzZWcBLfVoI5RuSSvY.u7TA4gWKsCxDFrUxqsgaf1vnEGcP96W', 'activo', NULL, NULL),
(29, 1081402721, '$2y$10$q79cxlS8zMyen/erQ66jJuR7pW2aAxm/sX7iW.4pk3GfnBEoI.jqS', 'activo', NULL, NULL),
(30, 55555555, '$2y$10$k1DEmxaa9c4CFKuyHXvNuOOSVBvYhRZxgS1ZGhdYcwiI2Nft9NZU.', 'activo', NULL, NULL),
(31, 3333665544, '$2y$10$v0PHp.z8uXSIJzGnBR0yN.nhcOpFgQDW0Ecz3c8wAtcUMN7XlVo6u', 'activo', NULL, NULL),
(32, 666666998877, '$2y$10$qItX562ueKbDqoZd8P7ufOi6O/n/6NHD.Ys8KePqt5EhrPe/3P7la', 'activo', NULL, NULL),
(33, 2211447733, '$2y$10$9gTtB4vBkidcfkx3rNnwzOzjQ/qeN0T2aCEHSqJA5JJxUHr6DUeSO', 'activo', NULL, NULL),
(34, 101486325, '$2y$10$DJg2zys/4qfkI7EV2HZvuuVwOv1cdvTCN93VzuTt3xBqjwO7EbXgO', 'activo', NULL, NULL),
(35, 1014862578, '$2y$10$CsJYay9xtr65uLKjfdu1EuNzDB7OWCMdmTbtRrfpyvvh9/UggBa.6', 'activo', NULL, NULL),
(36, 1014862578, '$2y$10$61.s6Ork4cT5jiR.Zbe5V.cakEFhMhlmR6.vaEHvscLz0CqgI7Mee', 'activo', NULL, NULL),
(37, 1014862578, '$2y$10$X0d2wPDoWdKD5NrzmYMXAeCpMsNjQC3Ot7wviXvyqQo4OZAMCP9ae', 'activo', NULL, NULL),
(38, 8888885544, '$2y$10$WbAX1kPT7YX8OgjfEh4MJOBka3a8QQh6Wem9YDAMSr05l.TIHI6fm', 'activo', NULL, NULL),
(39, 88888111, '$2y$10$bLix.eW3/5tb95gvvjE8DObfcEOzyhqsdZvfcqMaITlD.TGtb7ig.', 'activo', NULL, NULL),
(40, 1014862802, '$2y$10$1X1W4tXlibamhAVuP0dd6OkyMGMtnEtfzAwost3ER/eIhoSizWR/a', 'activo', NULL, NULL),
(41, 44444444, '$2y$10$l.qn2XTnhR3bdZNQ9DosG.5KCpYAQyeDVGdPF7O80s4ZXhcfgQLKu', 'activo', NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detallesprestamo`
--

CREATE TABLE `detallesprestamo` (
  `IDdetpre` int(11) NOT NULL,
  `descelementodetpre` varchar(250) NOT NULL,
  `codigoinvdetpre` int(11) NOT NULL,
  `estadocaprestamo` enum('activo','inactivo') NOT NULL,
  `estadoeningreso` enum('activo','inactivo') NOT NULL,
  `nombrecuentadante` varchar(250) NOT NULL,
  `idelementos` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `detallesprestamo`
--

INSERT INTO `detallesprestamo` (`IDdetpre`, `descelementodetpre`, `codigoinvdetpre`, `estadocaprestamo`, `estadoeningreso`, `nombrecuentadante`, `idelementos`) VALUES
(3, 'pc - bueno', 99999, 'activo', 'inactivo', 'Pendiente de asignación', 13),
(4, 'pc - bueno', 99999, 'activo', 'inactivo', 'Pendiente de asignación', 13),
(5, 'pc - bueno', 99999, 'activo', 'inactivo', 'Pendiente de asignación', 13),
(6, 'computador - buen estado', 666666, 'activo', 'inactivo', 'Pendiente de asignación', 12),
(7, 'computador - buen estado', 666666, 'activo', 'inactivo', 'Pendiente de asignación', 12),
(8, 'pc - bueno', 99999, 'activo', 'inactivo', 'Pendiente de asignación', 13),
(9, 'computador - buen estado', 666666, 'activo', 'inactivo', 'Pendiente de asignación', 12),
(10, 'pc - buen estado pc DELL 2019', 11231, 'activo', 'inactivo', 'Pendiente de asignación', 14),
(11, 'pc - buen estado pc DELL 2019', 11231, 'activo', 'inactivo', 'Pendiente de asignación', 14),
(12, 'pc - buen estado pc DELL 2019', 11231, 'activo', 'inactivo', 'Pendiente de asignación', 14),
(13, 'pc - buen estado pc DELL 2019', 11231, 'activo', 'inactivo', 'Pendiente de asignación', 14),
(14, 'pc - buen estado pc DELL 2019', 11231, 'activo', 'inactivo', 'Pendiente de asignación', 14),
(15, 'pc - buen estado pc DELL 2019', 11231, 'activo', 'inactivo', 'Pendiente de asignación', 14),
(16, 'computador portatil HP 15 - xd', 112312, 'activo', 'inactivo', 'Pendiente de asignación', 15),
(17, 'computador portatil HP 15 - xd', 112312, 'activo', 'inactivo', 'Pendiente de asignación', 15),
(18, 'pc gamer pa el clash - super good', 12, 'activo', 'inactivo', 'Pendiente de asignación', 16),
(19, 'pc gamer pa el clash - super good', 12, 'activo', 'inactivo', 'Pendiente de asignación', 16),
(20, 'pc gamer pa el clash - super good', 12, 'activo', 'inactivo', 'Pendiente de asignación', 16),
(21, 'pc gamer pa el clash - super good', 12, 'activo', 'inactivo', 'Pendiente de asignación', 16),
(22, 'computador portatil HP 15 - xd', 112312, 'activo', 'inactivo', 'Pendiente de asignación', 15),
(23, 'el super computador - xddddddddd', 22, 'activo', 'inactivo', 'Pendiente de asignación', 17),
(24, 'computador portatil HP 15 - xd', 112312, 'activo', 'inactivo', 'Pendiente de asignación', 15),
(25, 'el super computador - xddddddddd', 22, 'activo', 'inactivo', 'Pendiente de asignación', 17),
(26, 'computador portatil HP 15 - xd', 112312, 'activo', 'inactivo', 'Pendiente de asignación', 15),
(27, 'el super computador - xddddddddd', 22, 'activo', 'inactivo', 'Pendiente de asignación', 17),
(28, 'computador portatil HP 15 - xd', 112312, 'activo', 'inactivo', 'Pendiente de asignación', 15),
(29, 'el super computador - xddddddddd', 22, 'activo', 'inactivo', 'Pendiente de asignación', 17),
(30, 'el super computador - xddddddddd', 22, 'activo', 'inactivo', 'Pendiente de asignación', 17),
(31, 'el super computador - xddddddddd', 22, 'activo', 'inactivo', 'Pendiente de asignación', 17),
(32, 'computador portatil HP 15 - xd', 112312, 'activo', 'inactivo', 'Pendiente de asignación', 15);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `elementos`
--

CREATE TABLE `elementos` (
  `IDele` int(11) NOT NULL,
  `nombreele` varchar(50) NOT NULL,
  `cantidadele` int(11) NOT NULL,
  `cantidadest` enum('activo','inactivo') NOT NULL,
  `codigoele` int(11) NOT NULL,
  `descripcionele` varchar(250) NOT NULL,
  `caracteristicasele` varchar(250) NOT NULL,
  `estado` enum('en prestamo','inactivo','activo') NOT NULL,
  `estadoelemento` enum('activo','inactivo') NOT NULL,
  `codigoinventario` bigint(20) NOT NULL,
  `imagen` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `elementos`
--

INSERT INTO `elementos` (`IDele`, `nombreele`, `cantidadele`, `cantidadest`, `codigoele`, `descripcionele`, `caracteristicasele`, `estado`, `estadoelemento`, `codigoinventario`, `imagen`) VALUES
(12, 'computador', 0, 'activo', 1111111, 'buen estado', 'Computadores', 'en prestamo', 'activo', 666666, 'uploads/inventario/item_68831dd066372_New logo.png'),
(13, 'pc', 0, 'activo', 3333333, 'bueno', 'malo', 'en prestamo', 'activo', 99999, NULL),
(14, 'pc', 2, 'activo', 111222333, 'buen estado pc DELL 2019', 'Computadores', 'en prestamo', 'activo', 11231, ''),
(15, 'computador portatil HP 15', 5, 'activo', 33233333, 'xd', '', 'activo', 'activo', 112312, ''),
(16, 'pc gamer pa el clash', 0, 'activo', 111222, 'super good', 'Otros', 'en prestamo', 'activo', 12, ''),
(17, 'el super computador', 0, 'activo', 12345, 'xddddddddd', 'Computadores', 'en prestamo', 'activo', 22, '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `fotos_perfil`
--

CREATE TABLE `fotos_perfil` (
  `id` int(11) NOT NULL,
  `id_persona` int(11) NOT NULL,
  `ruta` varchar(255) NOT NULL,
  `es_actual` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `fotos_perfil`
--

INSERT INTO `fotos_perfil` (`id`, `id_persona`, `ruta`, `es_actual`) VALUES
(1, 26, 'uploads/fotos_perfil/porteria_26_1749522918.jpg', 1),
(2, 25, 'uploads/fotos_perfil/perfil_25_1749601088.jpg', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ingresoelementos`
--

CREATE TABLE `ingresoelementos` (
  `IDingele` int(11) NOT NULL,
  `nombreingele` varchar(250) NOT NULL,
  `tipoelemento` varchar(200) NOT NULL,
  `descripcioningele` varchar(250) NOT NULL,
  `observacioningele` varchar(250) NOT NULL,
  `IDPER` int(11) NOT NULL,
  `serial` varchar(100) DEFAULT NULL,
  `hora_entrada` datetime DEFAULT NULL,
  `hora_salida` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `ingresoelementos`
--

INSERT INTO `ingresoelementos` (`IDingele`, `nombreingele`, `tipoelemento`, `descripcioningele`, `observacioningele`, `IDPER`, `serial`, `hora_entrada`, `hora_salida`) VALUES
(8, 'computador hp 15', 'Computadores', 'computador marca hp15', 'buen estado', 30, '10112', '2025-07-29 18:22:34', '2025-07-29 18:22:42'),
(9, 'computador hp 15', 'Computadores', 'computador portatil hp 15', 'buen estado', 30, 'fc0112', '2025-07-30 06:12:27', '2025-07-30 07:05:28'),
(10, 'tambor', 'Impresoras', 'tambor, color cafe', 'buen estado', 30, '1121', '2025-08-01 06:33:37', '2025-08-01 06:34:09'),
(16, 'ddd', 'Computadores', 'bbb', 'ccc', 30, 'aaa', '2025-08-15 16:45:32', '2025-08-15 17:04:38');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `marcaciones`
--

CREATE TABLE `marcaciones` (
  `IDmarc` int(11) NOT NULL,
  `hfecsalidamarc` datetime NOT NULL,
  `hfecingresomarc` datetime DEFAULT NULL,
  `estadomarc` enum('activo','inactivo') NOT NULL,
  `IDpres` int(11) NOT NULL,
  `IDautori` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `marcaciones`
--

INSERT INTO `marcaciones` (`IDmarc`, `hfecsalidamarc`, `hfecingresomarc`, `estadomarc`, `IDpres`, `IDautori`) VALUES
(2, '2025-07-27 17:59:02', NULL, 'activo', 23, 20),
(3, '2025-07-28 08:25:22', NULL, 'activo', 28, 25),
(4, '2025-08-01 22:55:00', NULL, 'activo', 38, 35);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `marcador`
--

CREATE TABLE `marcador` (
  `IDmar` int(11) NOT NULL,
  `horaentradamar` time NOT NULL,
  `horasalidamar` time NOT NULL,
  `fechamar` date NOT NULL,
  `estadomar` enum('activo','inactivo') NOT NULL,
  `IDingresoele` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notificaciones`
--

CREATE TABLE `notificaciones` (
  `IDnot` int(11) NOT NULL,
  `Tiponot` varchar(50) NOT NULL COMMENT 'Tipo de notificación (ej: prestamo_aprobado)',
  `estadonot` varchar(20) DEFAULT 'pendiente' COMMENT 'Estado de la notificación',
  `idautori` int(11) NOT NULL COMMENT 'ID de la autorización relacionada',
  `fechacreacion` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Fecha de creación del registro',
  `fechalectura` timestamp NULL DEFAULT NULL COMMENT 'Fecha en que se leyó la notificación',
  `leido` tinyint(1) DEFAULT 0 COMMENT 'Indica si la notificación fue leída',
  `mensaje` text DEFAULT NULL COMMENT 'Mensaje detallado de la notificación',
  `idusuario` int(11) DEFAULT NULL COMMENT 'ID del usuario destinatario (opcional)',
  `enlace` varchar(255) DEFAULT NULL COMMENT 'Enlace relacionado con la notificación'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabla para almacenar notificaciones del sistema';

--
-- Volcado de datos para la tabla `notificaciones`
--

INSERT INTO `notificaciones` (`IDnot`, `Tiponot`, `estadonot`, `idautori`, `fechacreacion`, `fechalectura`, `leido`, `mensaje`, `idusuario`, `enlace`) VALUES
(7, 'peticion_pendiente', 'pendiente', 12, '2025-07-25 09:04:37', NULL, 0, NULL, NULL, NULL),
(8, 'peticion_pendiente', 'pendiente', 13, '2025-07-25 09:13:57', NULL, 0, NULL, NULL, NULL),
(9, 'peticion_pendiente', 'pendiente', 14, '2025-07-25 09:50:59', NULL, 0, NULL, NULL, NULL),
(10, 'peticion_pendiente', 'pendiente', 15, '2025-07-25 10:12:28', NULL, 0, NULL, NULL, NULL),
(11, 'peticion_pendiente', 'pendiente', 16, '2025-07-25 10:33:48', NULL, 0, NULL, NULL, NULL),
(12, 'peticion_pendiente', 'pendiente', 17, '2025-07-25 10:51:58', NULL, 0, NULL, NULL, NULL),
(13, 'peticion_pendiente', 'pendiente', 18, '2025-07-25 11:08:51', NULL, 0, NULL, NULL, NULL),
(14, 'peticion_pendiente', 'pendiente', 19, '2025-07-27 16:17:14', NULL, 0, NULL, NULL, NULL),
(15, 'peticion_pendiente', 'pendiente', 20, '2025-07-27 16:49:53', NULL, 0, NULL, NULL, NULL),
(16, 'peticion_pendiente', 'pendiente', 21, '2025-07-28 06:44:34', NULL, 0, NULL, NULL, NULL),
(17, 'peticion_pendiente', 'pendiente', 22, '2025-07-28 06:59:14', NULL, 0, NULL, NULL, NULL),
(18, 'peticion_pendiente', 'pendiente', 23, '2025-07-28 07:06:57', NULL, 0, NULL, NULL, NULL),
(19, 'peticion_pendiente', 'pendiente', 24, '2025-07-28 07:46:14', NULL, 0, NULL, NULL, NULL),
(20, 'peticion_pendiente', 'pendiente', 25, '2025-07-28 08:22:31', NULL, 0, NULL, NULL, NULL),
(21, 'peticion_pendiente', 'pendiente', 26, '2025-07-28 08:59:25', NULL, 0, NULL, NULL, NULL),
(22, 'peticion_pendiente', 'pendiente', 27, '2025-07-28 09:01:26', NULL, 0, NULL, NULL, NULL),
(23, 'peticion_pendiente', 'pendiente', 28, '2025-07-28 10:24:34', NULL, 0, NULL, NULL, NULL),
(24, 'peticion_pendiente', 'pendiente', 29, '2025-07-28 16:47:05', NULL, 0, NULL, NULL, NULL),
(25, 'peticion_pendiente', 'pendiente', 30, '2025-07-30 07:09:30', NULL, 0, NULL, NULL, NULL),
(26, 'peticion_pendiente', 'pendiente', 31, '2025-07-30 16:01:30', NULL, 0, NULL, NULL, NULL),
(27, 'peticion_pendiente', 'pendiente', 32, '2025-07-30 16:07:10', NULL, 0, NULL, NULL, NULL),
(28, 'peticion_pendiente', 'pendiente', 33, '2025-07-30 16:12:17', NULL, 0, NULL, NULL, NULL),
(29, 'peticion_pendiente', 'pendiente', 34, '2025-07-30 16:19:34', NULL, 0, NULL, NULL, NULL),
(30, 'peticion_pendiente', 'pendiente', 35, '2025-07-30 22:52:56', NULL, 0, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `personas`
--

CREATE TABLE `personas` (
  `IDper` int(11) NOT NULL,
  `nombrecompletoper` varchar(250) NOT NULL,
  `tipodocumento` enum('TI','CC') NOT NULL,
  `numerodoc` bigint(15) NOT NULL,
  `contrasenaper` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `personas`
--

INSERT INTO `personas` (`IDper`, `nombrecompletoper`, `tipodocumento`, `numerodoc`, `contrasenaper`) VALUES
(14, 'gasca ramire', 'TI', 1014862578, ''),
(16, 'computador', 'TI', 12365478, ''),
(17, 'holamundo', 'TI', 10148999, ''),
(18, 'mauserr', 'TI', 99995999, ''),
(19, 'Miguel Ángel Castiblanco Rivera', 'CC', 1081401474, ''),
(20, 'Miguel Ángel Castiblanco Rivera', 'CC', 1081401474, ''),
(25, 'usuario pruebas', 'CC', 22222222, ''),
(26, 'porteria robsito', 'CC', 33333333, ''),
(28, 'Lizeth Dayana Serna', 'CC', 1081401177, ''),
(29, 'miguel garcia', 'TI', 1081721028, ''),
(30, 'Roberth Adrian Guzman Salazar', 'CC', 1081402721, ''),
(31, 'Roberth Adrian Guzman Salazar', 'CC', 55555555, ''),
(32, 'locas', 'TI', 3333665544, ''),
(33, 'julias', 'TI', 666666998877, ''),
(34, 'lucas', 'CC', 2211447733, ''),
(36, 'brando', 'CC', 1014862578, ''),
(37, 'brando', 'CC', 1014862578, ''),
(38, 'gasca ramirez', 'TI', 1014862578, ''),
(39, 'brandod', 'TI', 8888885544, ''),
(40, 'pablo', 'CC', 88888111, ''),
(41, 'brando garcia', 'TI', 1014862802, ''),
(42, 'Roberth Guzman Salazar', 'CC', 44444444, '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `prestamos`
--

CREATE TABLE `prestamos` (
  `IDpre` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `formacionodependencia` varchar(250) NOT NULL,
  `cargopre` varchar(50) NOT NULL,
  `lugardetraslado` varchar(250) NOT NULL,
  `IDdetalle` int(11) NOT NULL,
  `IDautorizacion` int(11) NOT NULL,
  `IDelementos` int(11) NOT NULL,
  `IDpersonas` int(11) NOT NULL,
  `estado_autorizacion` varchar(20) NOT NULL DEFAULT 'pendiente',
  `motivo_rechazo` text DEFAULT NULL COMMENT 'Motivo por el cual se rechazó la solicitud',
  `fecha_solicitud` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `prestamos`
--

INSERT INTO `prestamos` (`IDpre`, `cantidad`, `formacionodependencia`, `cargopre`, `lugardetraslado`, `IDdetalle`, `IDautorizacion`, `IDelementos`, `IDpersonas`, `estado_autorizacion`, `motivo_rechazo`, `fecha_solicitud`) VALUES
(9, 1, 'adso', 'funcionario', 'la casa', 3, 6, 13, 32, 'pendiente', 'a', '2025-07-27 17:15:57'),
(10, 5, 'adso', 'funcionario', 'la casa', 4, 7, 13, 41, 'pendiente', NULL, '2025-07-27 17:15:57'),
(11, 2, 'adso', 'funcionario', 'la casa', 5, 8, 13, 41, 'pendiente', NULL, '2025-07-27 17:15:57'),
(12, 2, 'adso', 'instructor', 'la casa', 6, 9, 12, 41, 'pendiente', NULL, '2025-07-27 17:15:57'),
(13, 2, 'adso', 'funcionario', 'la casa', 7, 10, 12, 41, 'pendiente', NULL, '2025-07-27 17:15:57'),
(14, 1, 'adso', 'funcionario', 'la casa', 8, 11, 13, 41, 'pendiente', NULL, '2025-07-27 17:15:57'),
(15, 1, 'ADSO', 'funcionario', 'crra 4e 2a 104', 9, 12, 12, 30, 'pendiente', NULL, '2025-07-27 17:15:57'),
(16, 1, 'ADSO', 'funcionario', 'crra 4e 2a 104', 10, 13, 14, 30, 'pendiente', NULL, '2025-07-27 17:15:57'),
(17, 1, 'ADSO', 'funcionario', 'crra 4e 2a 104', 11, 14, 14, 30, 'pendiente', NULL, '2025-07-27 17:15:57'),
(18, 1, 'ADSO', 'funcionario', 'crra 4e 2a 104', 12, 15, 14, 30, 'pendiente', NULL, '2025-07-27 17:15:57'),
(19, 1, 'ADSO', 'instructor', 'crra 4e 2a 104', 13, 16, 14, 30, 'pendiente', '45', '2025-07-27 17:15:57'),
(20, 1, 'ADSO', 'funcionario', 'crra 4e 2a 104', 14, 17, 14, 30, 'pendiente', NULL, '2025-07-27 17:15:57'),
(21, 1, 'ADSO', 'funcionario', 'crra 4e 2a 105', 15, 18, 14, 30, 'pendiente', 'xd', '2025-07-27 17:15:57'),
(22, 1, 'ADSO', 'APRENDIZ', 'mi casa papi', 16, 19, 15, 30, 'pendiente', 'xd', '2025-07-27 17:15:57'),
(23, 1, 'ADSO 2874006', 'APRENDIZ', 'ambiente 9', 17, 20, 15, 30, 'pendiente', 'xd', '2025-07-27 17:15:57'),
(24, 1, 'ingenieria de software', 'aprendiz', 'salon 08', 18, 21, 16, 30, 'pendiente', 'xd', '2025-07-28 06:44:34'),
(25, 1, 'ingenieria de sistemas', 'aprendiz', 'ambiente 09', 19, 22, 16, 30, 'pendiente', 'no tiene stock', '2025-07-28 06:59:14'),
(26, 1, 'ingenieria de informatica', 'aprendiz', 'ambiente 07', 20, 23, 16, 30, 'pendiente', NULL, '2025-07-28 07:06:57'),
(27, 1, 'adso', 'funcionario', 'salon 05', 21, 24, 16, 30, 'pendiente', 'xd', '2025-07-28 07:46:14'),
(28, 1, 'informatica', 'aprendiz', 'ambiente 07', 22, 25, 15, 30, 'pendiente', NULL, '2025-07-28 08:22:31'),
(29, 1, 'ADSO  MAÑANA 2874006', 'APRENDIZ', 'ambiente 09', 23, 26, 17, 30, 'pendiente', NULL, '2025-07-28 08:59:25'),
(30, 1, 'ADSO  MAÑANA 2874006', 'APRENDIZ', 'crra 4e 2a 104', 24, 27, 15, 30, 'pendiente', NULL, '2025-07-28 09:01:26'),
(31, 1, 'ing sistemas', 'APRENDIZ', 'crra 4e 2a 104', 25, 28, 17, 30, 'pendiente', 'no', '2025-07-28 10:24:34'),
(32, 1, 'ADSO', 'APRENDIZ', 'ambiente 09', 26, 29, 15, 30, 'pendiente', 'z', '2025-07-28 16:47:05'),
(33, 1, 'ADSO', 'APRENDIZ', 'ambiente 09', 27, 30, 17, 30, 'pendiente', 'x', '2025-07-30 07:09:30'),
(34, 1, 'adso', 'aprendiz', 'ambiente 09', 28, 31, 15, 30, 'pendiente', 'a', '2025-07-30 16:01:30'),
(35, 1, 'a', 'se', 'xa', 29, 32, 17, 30, 'pendiente', 'xd', '2025-07-30 16:07:10'),
(36, 1, 'a', 'b', 'cdd', 30, 33, 17, 30, 'pendiente', NULL, '2025-07-30 16:12:17'),
(37, 1, 'xx', 'x', 'xx', 31, 34, 17, 30, 'pendiente', NULL, '2025-07-30 16:19:34'),
(38, 1, 'ADSO  MAÑANA 2874006', 'APRENDIZ', 'ambiente 09', 32, 35, 15, 30, 'pendiente', NULL, '2025-07-30 22:52:56');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `IDrol` int(11) NOT NULL,
  `rol` varchar(50) NOT NULL,
  `estadorol` enum('activo','inactivo') NOT NULL,
  `idper` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`IDrol`, `rol`, `estadorol`, `idper`) VALUES
(14, 'usuario', 'activo', 14),
(16, 'admin', 'activo', 16),
(17, 'porteria', 'activo', 17),
(18, 'admin', 'activo', 18),
(19, 'usuario', 'activo', 19),
(20, 'admin', 'activo', 20),
(25, 'almacenes', 'activo', 25),
(26, 'porteria', 'activo', 26),
(28, 'admin', 'activo', 28),
(29, 'admin', 'activo', 29),
(30, 'usuario', 'activo', 30),
(31, 'cuentadante', 'activo', 31),
(32, 'usuario', 'activo', 32),
(33, 'porteria', 'activo', 33),
(34, 'porteria', 'activo', 34),
(36, 'cuentadante', 'activo', 36),
(37, 'cuentadante', 'activo', 37),
(38, 'cuentadante', 'activo', 38),
(39, 'cuentadante', 'activo', 39),
(40, 'almacenes', 'activo', 40),
(41, 'usuario', 'activo', 41),
(42, 'admin', 'activo', 42);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  `activo` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `vigilantes`
--

CREATE TABLE `vigilantes` (
  `idvig` int(11) NOT NULL,
  `nomvigilantesalida` varchar(100) NOT NULL,
  `nomvigilanteingreso` varchar(255) DEFAULT NULL,
  `firmasolicitante` varchar(100) NOT NULL,
  `fechaautorizacion` date NOT NULL,
  `idautorizacion` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `vigilantes`
--

INSERT INTO `vigilantes` (`idvig`, `nomvigilantesalida`, `nomvigilanteingreso`, `firmasolicitante`, `fechaautorizacion`, `idautorizacion`) VALUES
(3, 'porteria robsito', NULL, 'Roberth Guzman Salazar', '2025-07-27', 20),
(4, 'porteria robsito', NULL, 'Roberth Guzman Salazar', '2025-07-28', 25),
(5, 'porteria robsito', NULL, 'Roberth Adrian Guzman Salazar', '2025-08-01', 35);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `aprobaciones`
--
ALTER TABLE `aprobaciones`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `IDprestamo` (`IDprestamo`),
  ADD KEY `idx_estado` (`estado`),
  ADD KEY `idx_idprestamo` (`IDprestamo`),
  ADD KEY `idx_tipo_aprobacion` (`tipo_aprobacion`);

--
-- Indices de la tabla `auditoria_general`
--
ALTER TABLE `auditoria_general`
  ADD PRIMARY KEY (`IDauditoria`),
  ADD KEY `IDautori` (`IDautori`),
  ADD KEY `IDdetallespresta` (`IDdetallespresta`),
  ADD KEY `IDelemento` (`IDelemento`),
  ADD KEY `IDingresoele` (`IDingresoele`),
  ADD KEY `IDpresta` (`IDpresta`);

--
-- Indices de la tabla `auditoria_login`
--
ALTER TABLE `auditoria_login`
  ADD PRIMARY KEY (`IDlog`),
  ADD KEY `Idcuen` (`Idcuen`);

--
-- Indices de la tabla `autorizacion`
--
ALTER TABLE `autorizacion`
  ADD PRIMARY KEY (`IDaut`);

--
-- Indices de la tabla `codigos_barras`
--
ALTER TABLE `codigos_barras`
  ADD PRIMARY KEY (`IDcodigo`),
  ADD UNIQUE KEY `codigo` (`codigo`),
  ADD KEY `numerodoc` (`numerodoc`),
  ADD KEY `idperlas` (`idperlas`);

--
-- Indices de la tabla `configuracion_correo`
--
ALTER TABLE `configuracion_correo`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `contactos`
--
ALTER TABLE `contactos`
  ADD PRIMARY KEY (`IDcont`),
  ADD KEY `IDperso` (`IDperso`);

--
-- Indices de la tabla `cuentas`
--
ALTER TABLE `cuentas`
  ADD PRIMARY KEY (`IDcue`);

--
-- Indices de la tabla `detallesprestamo`
--
ALTER TABLE `detallesprestamo`
  ADD PRIMARY KEY (`IDdetpre`),
  ADD KEY `idelementos` (`idelementos`);

--
-- Indices de la tabla `elementos`
--
ALTER TABLE `elementos`
  ADD PRIMARY KEY (`IDele`);

--
-- Indices de la tabla `fotos_perfil`
--
ALTER TABLE `fotos_perfil`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_persona` (`id_persona`);

--
-- Indices de la tabla `ingresoelementos`
--
ALTER TABLE `ingresoelementos`
  ADD PRIMARY KEY (`IDingele`),
  ADD KEY `IDPER` (`IDPER`);

--
-- Indices de la tabla `marcaciones`
--
ALTER TABLE `marcaciones`
  ADD PRIMARY KEY (`IDmarc`),
  ADD KEY `IDautori` (`IDautori`),
  ADD KEY `IDpres` (`IDpres`);

--
-- Indices de la tabla `marcador`
--
ALTER TABLE `marcador`
  ADD PRIMARY KEY (`IDmar`),
  ADD KEY `IDingresoele` (`IDingresoele`);

--
-- Indices de la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  ADD PRIMARY KEY (`IDnot`),
  ADD KEY `idx_estado` (`estadonot`),
  ADD KEY `idx_idautori` (`idautori`),
  ADD KEY `idx_leido` (`leido`),
  ADD KEY `idx_fechacreacion` (`fechacreacion`);

--
-- Indices de la tabla `personas`
--
ALTER TABLE `personas`
  ADD PRIMARY KEY (`IDper`);

--
-- Indices de la tabla `prestamos`
--
ALTER TABLE `prestamos`
  ADD PRIMARY KEY (`IDpre`),
  ADD KEY `IDautorizacion` (`IDautorizacion`),
  ADD KEY `IDdetalle` (`IDdetalle`),
  ADD KEY `IDelementos` (`IDelementos`),
  ADD KEY `IDpersonas` (`IDpersonas`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`IDrol`),
  ADD KEY `idper` (`idper`),
  ADD KEY `idper_2` (`idper`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indices de la tabla `vigilantes`
--
ALTER TABLE `vigilantes`
  ADD PRIMARY KEY (`idvig`),
  ADD KEY `idautorizacion` (`idautorizacion`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `aprobaciones`
--
ALTER TABLE `aprobaciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=81;

--
-- AUTO_INCREMENT de la tabla `auditoria_general`
--
ALTER TABLE `auditoria_general`
  MODIFY `IDauditoria` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `auditoria_login`
--
ALTER TABLE `auditoria_login`
  MODIFY `IDlog` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `autorizacion`
--
ALTER TABLE `autorizacion`
  MODIFY `IDaut` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT de la tabla `codigos_barras`
--
ALTER TABLE `codigos_barras`
  MODIFY `IDcodigo` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `configuracion_correo`
--
ALTER TABLE `configuracion_correo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `contactos`
--
ALTER TABLE `contactos`
  MODIFY `IDcont` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT de la tabla `cuentas`
--
ALTER TABLE `cuentas`
  MODIFY `IDcue` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT de la tabla `detallesprestamo`
--
ALTER TABLE `detallesprestamo`
  MODIFY `IDdetpre` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT de la tabla `elementos`
--
ALTER TABLE `elementos`
  MODIFY `IDele` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT de la tabla `fotos_perfil`
--
ALTER TABLE `fotos_perfil`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `ingresoelementos`
--
ALTER TABLE `ingresoelementos`
  MODIFY `IDingele` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `marcaciones`
--
ALTER TABLE `marcaciones`
  MODIFY `IDmarc` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `marcador`
--
ALTER TABLE `marcador`
  MODIFY `IDmar` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  MODIFY `IDnot` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT de la tabla `personas`
--
ALTER TABLE `personas`
  MODIFY `IDper` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT de la tabla `prestamos`
--
ALTER TABLE `prestamos`
  MODIFY `IDpre` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `IDrol` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `vigilantes`
--
ALTER TABLE `vigilantes`
  MODIFY `idvig` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `aprobaciones`
--
ALTER TABLE `aprobaciones`
  ADD CONSTRAINT `aprobaciones_ibfk_1` FOREIGN KEY (`IDprestamo`) REFERENCES `prestamos` (`IDpre`) ON DELETE CASCADE;

--
-- Filtros para la tabla `codigos_barras`
--
ALTER TABLE `codigos_barras`
  ADD CONSTRAINT `codigos_barras_ibfk_1` FOREIGN KEY (`idperlas`) REFERENCES `personas` (`IDper`);

--
-- Filtros para la tabla `contactos`
--
ALTER TABLE `contactos`
  ADD CONSTRAINT `contactos_ibfk_1` FOREIGN KEY (`IDperso`) REFERENCES `personas` (`IDper`);

--
-- Filtros para la tabla `detallesprestamo`
--
ALTER TABLE `detallesprestamo`
  ADD CONSTRAINT `detallesprestamo_ibfk_1` FOREIGN KEY (`idelementos`) REFERENCES `elementos` (`IDele`);

--
-- Filtros para la tabla `fotos_perfil`
--
ALTER TABLE `fotos_perfil`
  ADD CONSTRAINT `fotos_perfil_ibfk_1` FOREIGN KEY (`id_persona`) REFERENCES `personas` (`IDper`);

--
-- Filtros para la tabla `ingresoelementos`
--
ALTER TABLE `ingresoelementos`
  ADD CONSTRAINT `ingresoelementos_ibfk_1` FOREIGN KEY (`IDPER`) REFERENCES `personas` (`IDper`);

--
-- Filtros para la tabla `marcaciones`
--
ALTER TABLE `marcaciones`
  ADD CONSTRAINT `marcaciones_ibfk_1` FOREIGN KEY (`IDautori`) REFERENCES `autorizacion` (`IDaut`),
  ADD CONSTRAINT `marcaciones_ibfk_2` FOREIGN KEY (`IDpres`) REFERENCES `prestamos` (`IDpre`);

--
-- Filtros para la tabla `marcador`
--
ALTER TABLE `marcador`
  ADD CONSTRAINT `marcador_ibfk_1` FOREIGN KEY (`IDingresoele`) REFERENCES `ingresoelementos` (`IDingele`);

--
-- Filtros para la tabla `prestamos`
--
ALTER TABLE `prestamos`
  ADD CONSTRAINT `prestamos_ibfk_1` FOREIGN KEY (`IDautorizacion`) REFERENCES `autorizacion` (`IDaut`),
  ADD CONSTRAINT `prestamos_ibfk_2` FOREIGN KEY (`IDdetalle`) REFERENCES `detallesprestamo` (`IDdetpre`),
  ADD CONSTRAINT `prestamos_ibfk_3` FOREIGN KEY (`IDelementos`) REFERENCES `elementos` (`IDele`),
  ADD CONSTRAINT `prestamos_ibfk_4` FOREIGN KEY (`IDpersonas`) REFERENCES `personas` (`IDper`);

--
-- Filtros para la tabla `roles`
--
ALTER TABLE `roles`
  ADD CONSTRAINT `roles_ibfk_1` FOREIGN KEY (`idper`) REFERENCES `personas` (`IDper`);

--
-- Filtros para la tabla `vigilantes`
--
ALTER TABLE `vigilantes`
  ADD CONSTRAINT `vigilantes_ibfk_1` FOREIGN KEY (`idautorizacion`) REFERENCES `autorizacion` (`IDaut`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
