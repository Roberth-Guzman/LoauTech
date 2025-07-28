-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 25-07-2025 a las 08:21:25
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
  `estadoaut` enum('pendiente','activo','inactivo') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `autorizacion`
--

INSERT INTO `autorizacion` (`IDaut`, `VoBoCuentadanteaut`, `nomquienaturiza`, `cargoquienautoriza`, `firmaquienautoriza`, `estadoaut`) VALUES
(4, 'veronica', 'julia', 'biblioteca', 'lucas', ''),
(6, 'Pendiente', 'Sistema', 'sistema', 'pendiente', 'pendiente'),
(7, 'Pendiente', 'Sistema', 'sistema', 'pendiente', 'pendiente'),
(8, 'Pendiente', 'Sistema', 'sistema', 'pendiente', 'pendiente'),
(9, 'Pendiente', 'Sistema', 'sistema', 'pendiente', 'pendiente'),
(10, 'Pendiente', 'Sistema', 'sistema', 'pendiente', 'pendiente'),
(11, 'Pendiente', 'Sistema', 'sistema', 'pendiente', 'pendiente');

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
(40, 3209526705, 'calle 3 a sur', 'brangariza@gmail.com', 'activo', 41);

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
(40, 1014862802, '$2y$10$1X1W4tXlibamhAVuP0dd6OkyMGMtnEtfzAwost3ER/eIhoSizWR/a', 'activo', NULL, NULL);

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
(8, 'pc - bueno', 99999, 'activo', 'inactivo', 'Pendiente de asignación', 13);

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
(12, 'computador', 2, 'activo', 1111111, 'buen estado', 'Computadores', 'activo', 'activo', 666666, 'uploads/inventario/item_68831dd066372_New logo.png'),
(13, 'pc', 0, 'activo', 3333333, 'bueno', 'malo', 'en prestamo', 'activo', 99999, NULL);

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
  `IDPER` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `ingresoelementos`
--

INSERT INTO `ingresoelementos` (`IDingele`, `nombreingele`, `tipoelemento`, `descripcioningele`, `observacioningele`, `IDPER`) VALUES
(6, 'compotador', 'electronico', 'bueno', 'bueno', 32);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `marcaciones`
--

CREATE TABLE `marcaciones` (
  `IDmarc` int(11) NOT NULL,
  `hfecsalidamarc` datetime NOT NULL,
  `hfecingresomarc` datetime NOT NULL,
  `estadomarc` enum('activo','inactivo') NOT NULL,
  `IDpres` int(11) NOT NULL,
  `IDautori` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(30, 'Roberth Guzman Salazar', 'CC', 1081402721, ''),
(31, 'Roberth Adrian Guzman Salazar', 'CC', 55555555, ''),
(32, 'locas', 'TI', 3333665544, ''),
(33, 'julias', 'TI', 666666998877, ''),
(34, 'lucas', 'CC', 2211447733, ''),
(36, 'brando', 'CC', 1014862578, ''),
(37, 'brando', 'CC', 1014862578, ''),
(38, 'gasca ramirez', 'TI', 1014862578, ''),
(39, 'brandod', 'TI', 8888885544, ''),
(40, 'pablo', 'CC', 88888111, ''),
(41, 'brando garcia', 'TI', 1014862802, '');

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
  `motivo_rechazo` text DEFAULT NULL COMMENT 'Motivo por el cual se rechazó la solicitud'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `prestamos`
--

INSERT INTO `prestamos` (`IDpre`, `cantidad`, `formacionodependencia`, `cargopre`, `lugardetraslado`, `IDdetalle`, `IDautorizacion`, `IDelementos`, `IDpersonas`, `motivo_rechazo`) VALUES
(9, 1, 'adso', 'funcionario', 'la casa', 3, 6, 13, 32, NULL),
(10, 5, 'adso', 'funcionario', 'la casa', 4, 7, 13, 41, NULL),
(11, 2, 'adso', 'funcionario', 'la casa', 5, 8, 13, 41, NULL),
(12, 2, 'adso', 'instructor', 'la casa', 6, 9, 12, 41, NULL),
(13, 2, 'adso', 'funcionario', 'la casa', 7, 10, 12, 41, NULL),
(14, 1, 'adso', 'funcionario', 'la casa', 8, 11, 13, 41, NULL);

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
(25, 'usuario', 'activo', 25),
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
(41, 'usuario', 'activo', 41);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `vigilantes`
--

CREATE TABLE `vigilantes` (
  `idvig` int(11) NOT NULL,
  `nomvigilantesalida` varchar(100) NOT NULL,
  `nomvigilanteingreso` varchar(100) NOT NULL,
  `firmasolicitante` varchar(100) NOT NULL,
  `fechaautorizacion` date NOT NULL,
  `idautorizacion` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `autorizacion`
--
ALTER TABLE `autorizacion`
  MODIFY `IDaut` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

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
  MODIFY `IDcont` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT de la tabla `cuentas`
--
ALTER TABLE `cuentas`
  MODIFY `IDcue` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT de la tabla `detallesprestamo`
--
ALTER TABLE `detallesprestamo`
  MODIFY `IDdetpre` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `elementos`
--
ALTER TABLE `elementos`
  MODIFY `IDele` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `fotos_perfil`
--
ALTER TABLE `fotos_perfil`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `ingresoelementos`
--
ALTER TABLE `ingresoelementos`
  MODIFY `IDingele` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `marcaciones`
--
ALTER TABLE `marcaciones`
  MODIFY `IDmarc` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `marcador`
--
ALTER TABLE `marcador`
  MODIFY `IDmar` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  MODIFY `IDnot` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `personas`
--
ALTER TABLE `personas`
  MODIFY `IDper` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT de la tabla `prestamos`
--
ALTER TABLE `prestamos`
  MODIFY `IDpre` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `IDrol` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT de la tabla `vigilantes`
--
ALTER TABLE `vigilantes`
  MODIFY `idvig` int(11) NOT NULL AUTO_INCREMENT;

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
