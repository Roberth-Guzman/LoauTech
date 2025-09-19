-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 19-09-2025 a las 19:08:27
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
CREATE DATABASE IF NOT EXISTS `loatech` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `loatech`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `aprobaciones`
--

DROP TABLE IF EXISTS `aprobaciones`;
CREATE TABLE `aprobaciones` (
  `id` int(11) NOT NULL,
  `IDprestamo` int(11) NOT NULL,
  `estado` enum('pendiente','aprobado','rechazado') NOT NULL,
  `fecha_creacion` datetime NOT NULL DEFAULT current_timestamp(),
  `fecha_aprobacion` datetime DEFAULT NULL,
  `fecha_rechazo` datetime DEFAULT NULL,
  `fecha_actualizacion` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `usuario` varchar(100) DEFAULT NULL COMMENT 'Usuario que realizó la acción',
  `tipo_aprobacion` enum('cuentadante','almacen','porteria') DEFAULT NULL,
  `aprobado_por` varchar(100) DEFAULT NULL,
  `ip_origen` varchar(45) DEFAULT NULL,
  `motivo` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Tabla para el seguimiento de aprobaciones de préstamos';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `auditoria_general`
--

DROP TABLE IF EXISTS `auditoria_general`;
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

DROP TABLE IF EXISTS `auditoria_login`;
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

DROP TABLE IF EXISTS `autorizacion`;
CREATE TABLE `autorizacion` (
  `IDaut` int(11) NOT NULL,
  `VoBoCuentadanteaut` varchar(250) DEFAULT NULL,
  `nomquienaturiza` varchar(255) DEFAULT NULL,
  `cargoquienautoriza` varchar(50) DEFAULT NULL,
  `firmaquienautoriza` varchar(50) DEFAULT NULL,
  `estadoaut` enum('pendiente','pendiente_cuentadante','pendiente_almacen','activo','aprobado','rechazado','inactivo','en_prestamo') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `codigos_barras`
--

DROP TABLE IF EXISTS `codigos_barras`;
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

DROP TABLE IF EXISTS `configuracion_correo`;
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

DROP TABLE IF EXISTS `contactos`;
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
(1, 3204441607, 'CRRA 4E 2A 104', 'adriiang322@gmail.com', 'activo', 1),
(2, 3144307202, 'Crra 4e 2a 93', 'pennamaria56@gmail.com', 'activo', 2),
(3, 3134071607, 'crra 2a 104 isidro', 'miguelcastiblanco@gmail.com', 'activo', 3),
(4, 3144096205, 'crra 4e 2a 104', 'fabisalazar1008@gmail.com', 'activo', 4),
(5, 3123426546, 'crra 2a 04 ', 'sernacordobalizethdayana@gmail.com', 'activo', 5);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cuentas`
--

DROP TABLE IF EXISTS `cuentas`;
CREATE TABLE `cuentas` (
  `IDcue` int(11) NOT NULL,
  `numerodoc` bigint(15) NOT NULL,
  `contracue` varchar(200) NOT NULL,
  `estadocue` enum('activo','inactivo') NOT NULL,
  `reset_token` varchar(255) DEFAULT NULL,
  `token_expira` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `cuentas`
--

INSERT INTO `cuentas` (`IDcue`, `numerodoc`, `contracue`, `estadocue`, `reset_token`, `token_expira`) VALUES
(1, 1081402721, '$2y$10$.YLX1BNQCT6dANn/QRFm7OQGuMrk3tondR2uSS63pK3dutwClXJJa', 'activo', NULL, NULL),
(2, 1081399491, '$2y$10$Y.wLa8I9JWCBQz7zkO3H6uUURAfls/2Qc16uVorbD7fUTXsUO57Y.', 'activo', NULL, NULL),
(3, 1081401474, '$2y$10$gtrbZPe5U.tnbL0EXU/MQOFlCotImb1.gcES2p.yJ6Cjh5Du2jdcG', 'activo', NULL, NULL),
(4, 16380375, '$2y$10$e7yHWbpMytcLgrITnHxvpeulaYsFXUb.F7RmYWHs38YgGrRPhTpxm', 'activo', NULL, NULL),
(5, 1081401177, '$2y$10$47OJgumF5SqO9xZLGbRfnOorm6rgN.zm4X287ZG2oUK8tQZTwVO5G', 'activo', NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detallesprestamo`
--

DROP TABLE IF EXISTS `detallesprestamo`;
CREATE TABLE `detallesprestamo` (
  `IDdetpre` int(11) NOT NULL,
  `descelementodetpre` varchar(250) NOT NULL,
  `codigoinvdetpre` int(11) NOT NULL,
  `estadocaprestamo` enum('activo','inactivo') NOT NULL,
  `estadoeningreso` enum('activo','inactivo') NOT NULL,
  `nombrecuentadante` varchar(250) NOT NULL,
  `idelementos` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `elementos`
--

DROP TABLE IF EXISTS `elementos`;
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
  `imagen` varchar(255) DEFAULT NULL,
  `cuentadante_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `fotos_perfil`
--

DROP TABLE IF EXISTS `fotos_perfil`;
CREATE TABLE `fotos_perfil` (
  `id` int(11) NOT NULL,
  `id_persona` int(11) NOT NULL,
  `ruta` varchar(255) NOT NULL,
  `es_actual` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ingresoelementos`
--

DROP TABLE IF EXISTS `ingresoelementos`;
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

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `marcaciones`
--

DROP TABLE IF EXISTS `marcaciones`;
CREATE TABLE `marcaciones` (
  `IDmarc` int(11) NOT NULL,
  `hfecsalidamarc` datetime NOT NULL,
  `hfecingresomarc` datetime DEFAULT NULL,
  `estadomarc` enum('activo','inactivo') NOT NULL,
  `IDpres` int(11) NOT NULL,
  `IDautori` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `marcador`
--

DROP TABLE IF EXISTS `marcador`;
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

DROP TABLE IF EXISTS `notificaciones`;
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

DROP TABLE IF EXISTS `personas`;
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
(1, 'Roberth Guzman Salazar', 'CC', 1081402721, '$2y$10$NkNesyLdRADviN6kefdPEuWm8SVvCRdW4ngmm5NVsCXfdQHJeS/pW'),
(2, 'Maria Luisa Penna', 'CC', 1081399491, '$2y$10$XJ2.XYnh6veJaEOREFSuTelElCBJBS4pklMaQX4GgBrP94Xo580d2'),
(3, 'Miguel Angel Castiblanco', 'CC', 1081401474, '$2y$10$cY2Wkg20jr0OI5z3tA4FOOdX71MWCKvEduCL4HpcJMRRSXivGvYle'),
(4, 'Fabiola Salazar', 'CC', 1081399180, '$2y$10$eJ4raKWDjTFvRAiXUfawEOVNh9ACvzqH5gGReL.PRvCgSK247l0Y2'),
(5, 'Lizeth Dayana Serna', 'TI', 1081401177, '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `prestamos`
--

DROP TABLE IF EXISTS `prestamos`;
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
  `aprobador_cuentadante_id` int(11) DEFAULT NULL,
  `motivo_rechazo` text DEFAULT NULL COMMENT 'Motivo por el cual se rechazó la solicitud',
  `fecha_salida` datetime DEFAULT NULL,
  `fecha_solicitud` datetime NOT NULL DEFAULT current_timestamp(),
  `observaciones_salida` text DEFAULT NULL,
  `fecha_devolucion` datetime DEFAULT NULL,
  `observaciones_devolucion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

DROP TABLE IF EXISTS `roles`;
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
(1, 'usuario', 'activo', 1),
(2, 'admin', 'activo', 2),
(3, 'usuario', 'activo', 3),
(4, 'usuario', 'activo', 4),
(5, 'usuario', 'activo', 5);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `vigilantes`
--

DROP TABLE IF EXISTS `vigilantes`;
CREATE TABLE `vigilantes` (
  `idvig` int(11) NOT NULL,
  `nomvigilantesalida` varchar(100) NOT NULL,
  `nomvigilanteingreso` varchar(255) DEFAULT NULL,
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
  MODIFY `IDaut` int(11) NOT NULL AUTO_INCREMENT;

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
  MODIFY `IDcont` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `cuentas`
--
ALTER TABLE `cuentas`
  MODIFY `IDcue` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `ingresoelementos`
--
ALTER TABLE `ingresoelementos`
  MODIFY `IDingele` int(11) NOT NULL AUTO_INCREMENT;

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
  MODIFY `IDper` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `prestamos`
--
ALTER TABLE `prestamos`
  MODIFY `IDpre` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `IDrol` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

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
