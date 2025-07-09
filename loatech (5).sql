-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 04-07-2025 a las 05:28:41
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
-- Estructura de tabla para la tabla `autorizacion`
--

CREATE TABLE `autorizacion` (
  `IDaut` int(11) NOT NULL,
  `VoBoCuentadanteaut` varchar(250) NOT NULL,
  `firmasolicitanteaut` varchar(50) NOT NULL,
  `nomvigilantesalidaaut` varchar(250) NOT NULL,
  `nomvigilanteingresoaut` varchar(250) NOT NULL,
  `fechaautorizaciom` date NOT NULL,
  `cargoquienautoriza` varchar(50) NOT NULL,
  `firmaquienautoriza` varchar(50) NOT NULL,
  `estadoaut` enum('pendiente','activo','inactivo') NOT NULL,
  `IDnoti` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(29, 3204441607, 'crra 4e 2a 67', 'adriiang322@gmail.com', 'activo', 30);

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
(29, 1081402721, '$2y$10$gl6bDo5MLMhU.imDFCFwr.9VuSjQGJ3bxXHYqim2aI7T9.wvI9jjO', 'activo', NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detallesprestamo`
--

CREATE TABLE `detallesprestamo` (
  `IDdetpre` int(11) NOT NULL,
  `cantidaddetpre` int(11) NOT NULL,
  `descelementodetpre` varchar(250) NOT NULL,
  `codigoinvdetpre` int(11) NOT NULL,
  `estadocaprestamo` enum('activo','inactivo') NOT NULL,
  `estadoeningreso` enum('activo','inactivo') NOT NULL,
  `nombrecuentadante` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `elementos`
--

CREATE TABLE `elementos` (
  `IDele` int(11) NOT NULL,
  `nombreele` varchar(50) NOT NULL,
  `codigoele` int(11) NOT NULL,
  `descripcionele` varchar(250) NOT NULL,
  `caracteristicasele` varchar(250) NOT NULL,
  `estado` enum('activo','inactivo') NOT NULL,
  `estadoelemento` enum('activo','inactivo') NOT NULL,
  `IDdetalles` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `IDmarcador` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `estadomar` enum('activo','inactivo') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notificaciones`
--

CREATE TABLE `notificaciones` (
  `IDnot` int(11) NOT NULL,
  `Tiponot` varchar(50) NOT NULL,
  `estadonot` enum('activo','inactivo') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(30, 'Roberth Guzman Salazar', 'CC', 1081402721, '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `prestamos`
--

CREATE TABLE `prestamos` (
  `IDpre` int(11) NOT NULL,
  `nomquienaturiza` varchar(250) NOT NULL,
  `formacionodependencia` varchar(250) NOT NULL,
  `cargopre` varchar(50) NOT NULL,
  `lugardetraslado` varchar(250) NOT NULL,
  `IDdetalle` int(11) NOT NULL,
  `IDautorizacion` int(11) NOT NULL,
  `IDelementos` int(11) NOT NULL,
  `IDpersonas` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(30, 'admin', 'activo', 30);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `autorizacion`
--
ALTER TABLE `autorizacion`
  ADD PRIMARY KEY (`IDaut`),
  ADD KEY `IDnoti` (`IDnoti`);

--
-- Indices de la tabla `codigos_barras`
--
ALTER TABLE `codigos_barras`
  ADD PRIMARY KEY (`IDcodigo`),
  ADD UNIQUE KEY `codigo` (`codigo`),
  ADD KEY `numerodoc` (`numerodoc`),
  ADD KEY `idperlas` (`idperlas`);

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
  ADD PRIMARY KEY (`IDdetpre`);

--
-- Indices de la tabla `elementos`
--
ALTER TABLE `elementos`
  ADD PRIMARY KEY (`IDele`),
  ADD KEY `IDdetalles` (`IDdetalles`);

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
  ADD KEY `IDPER` (`IDPER`),
  ADD KEY `IDmarcador` (`IDmarcador`);

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
  ADD PRIMARY KEY (`IDmar`);

--
-- Indices de la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  ADD PRIMARY KEY (`IDnot`);

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
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `autorizacion`
--
ALTER TABLE `autorizacion`
  MODIFY `IDaut` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `codigos_barras`
--
ALTER TABLE `codigos_barras`
  MODIFY `IDcodigo` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `contactos`
--
ALTER TABLE `contactos`
  MODIFY `IDcont` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT de la tabla `cuentas`
--
ALTER TABLE `cuentas`
  MODIFY `IDcue` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT de la tabla `detallesprestamo`
--
ALTER TABLE `detallesprestamo`
  MODIFY `IDdetpre` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `elementos`
--
ALTER TABLE `elementos`
  MODIFY `IDele` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `fotos_perfil`
--
ALTER TABLE `fotos_perfil`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `ingresoelementos`
--
ALTER TABLE `ingresoelementos`
  MODIFY `IDingele` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

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
  MODIFY `IDnot` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `personas`
--
ALTER TABLE `personas`
  MODIFY `IDper` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT de la tabla `prestamos`
--
ALTER TABLE `prestamos`
  MODIFY `IDpre` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `IDrol` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `autorizacion`
--
ALTER TABLE `autorizacion`
  ADD CONSTRAINT `autorizacion_ibfk_1` FOREIGN KEY (`IDnoti`) REFERENCES `notificaciones` (`IDnot`);

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
-- Filtros para la tabla `elementos`
--
ALTER TABLE `elementos`
  ADD CONSTRAINT `elementos_ibfk_1` FOREIGN KEY (`IDdetalles`) REFERENCES `detallesprestamo` (`IDdetpre`);

--
-- Filtros para la tabla `fotos_perfil`
--
ALTER TABLE `fotos_perfil`
  ADD CONSTRAINT `fotos_perfil_ibfk_1` FOREIGN KEY (`id_persona`) REFERENCES `personas` (`IDper`);

--
-- Filtros para la tabla `ingresoelementos`
--
ALTER TABLE `ingresoelementos`
  ADD CONSTRAINT `ingresoelementos_ibfk_1` FOREIGN KEY (`IDPER`) REFERENCES `personas` (`IDper`),
  ADD CONSTRAINT `ingresoelementos_ibfk_2` FOREIGN KEY (`IDmarcador`) REFERENCES `marcador` (`IDmar`);

--
-- Filtros para la tabla `marcaciones`
--
ALTER TABLE `marcaciones`
  ADD CONSTRAINT `marcaciones_ibfk_1` FOREIGN KEY (`IDautori`) REFERENCES `autorizacion` (`IDaut`),
  ADD CONSTRAINT `marcaciones_ibfk_2` FOREIGN KEY (`IDpres`) REFERENCES `prestamos` (`IDpre`);

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
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
