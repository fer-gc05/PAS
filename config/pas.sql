-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 04-06-2024 a las 21:46:21
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
-- Base de datos: `pas`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `alarm_logs`
--

CREATE TABLE `alarm_logs` (
  `id` int(11) NOT NULL,
  `alarm_id` int(11) DEFAULT NULL,
  `action` varchar(255) DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `alarm_logs`
--

INSERT INTO `alarm_logs` (`id`, `alarm_id`, `action`, `timestamp`) VALUES
(228, 1, 'Alarma laser activada por automatización', '2024-06-01 20:31:03'),
(229, 1, 'Alarma laser desactivada por automatización', '2024-06-01 20:32:00'),
(230, 1, 'Alarma laser activada por automatización', '2024-06-01 20:37:06'),
(231, 1, 'Alarma laser desactivada por automatización', '2024-06-01 20:39:05'),
(232, 1, 'Alarma laser activada por automatización', '2024-06-01 20:42:00'),
(233, 1, 'Alarma laser desactivada por automatización', '2024-06-01 20:43:00'),
(234, 1, 'Alarma laser activada por interfaz web', '2024-06-02 14:38:38'),
(235, 1, 'Alarma laser desactivada por interfaz web', '2024-06-02 14:39:51'),
(236, 1, 'Alarma laser activada por automatización', '2024-06-02 14:41:00'),
(237, 1, 'Alarma laser desactivada por interfaz web', '2024-06-02 14:41:20'),
(238, 1, 'Alarma laser activada por automatización', '2024-06-02 14:42:00'),
(239, 1, 'Alarma laser desactivada por automatización', '2024-06-02 14:46:00'),
(240, 1, 'Alarma laser activada por automatización', '2024-06-02 14:51:04'),
(241, 1, 'Alarma laser desactivada por automatización', '2024-06-02 15:05:00'),
(242, 1, 'Alarma laser activada por automatización', '2024-06-02 15:12:00'),
(243, 1, 'Alarma laser desactivada por automatización', '2024-06-02 15:14:00'),
(244, 1, 'Alarma laser activada por interfaz web', '2024-06-02 15:14:31'),
(245, 1, 'Alarma laser desactivada por interfaz web', '2024-06-02 15:14:43'),
(246, 1, 'Alarma laser activada por interfaz web', '2024-06-02 15:14:52'),
(247, 1, 'Alarma laser desactivada por interfaz web', '2024-06-02 15:25:35'),
(248, 1, 'Alarma laser activada por interfaz web', '2024-06-02 15:26:06'),
(249, 1, 'Alarma laser desactivada por interfaz web', '2024-06-02 15:28:51'),
(250, 1, 'Alarma laser activada por interfaz web', '2024-06-02 15:29:02'),
(251, 1, 'Alarma laser desactivada por interfaz web', '2024-06-02 15:30:20'),
(252, 1, 'Alarma laser activada por interfaz web', '2024-06-02 15:30:26'),
(253, 1, 'Alarma laser desactivada por interfaz web', '2024-06-02 15:31:40'),
(254, 1, 'Alarma laser activada por interfaz web', '2024-06-02 15:31:51'),
(255, 1, 'Alarma laser desactivada por interfaz web', '2024-06-02 15:34:57'),
(256, 1, 'Alarma laser activada por interfaz web', '2024-06-02 15:35:02'),
(257, 1, 'Alarma laser desactivada por interfaz web', '2024-06-02 15:51:37'),
(258, 1, 'Alarma laser activada por automatización', '2024-06-02 15:58:17'),
(259, 1, 'Alarma laser desactivada por automatización', '2024-06-02 16:00:51'),
(260, 1, 'Alarma laser activada por interfaz web', '2024-06-02 16:03:33'),
(261, 1, 'Alarma laser desactivada por interfaz web', '2024-06-02 16:04:23'),
(262, 1, 'Alarma laser activada por interfaz web', '2024-06-02 16:04:30'),
(263, 1, 'Alarma laser desactivada por interfaz web', '2024-06-02 16:04:40'),
(264, 1, 'Alarma laser activada por interfaz web', '2024-06-02 16:04:49'),
(265, 1, 'Alarma laser desactivada por interfaz web', '2024-06-02 16:05:02'),
(266, 1, 'Alarma laser activada por interfaz web', '2024-06-02 16:05:06'),
(267, 1, 'Alarma laser desactivada por interfaz web', '2024-06-02 16:05:25');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `automation`
--

CREATE TABLE `automation` (
  `id` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `turnOnHour` time NOT NULL,
  `turnOffHour` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `automation`
--

INSERT INTO `automation` (`id`, `status`, `turnOnHour`, `turnOffHour`) VALUES
(1, 0, '10:58:00', '11:00:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detection_logs`
--

CREATE TABLE `detection_logs` (
  `id` int(11) NOT NULL,
  `alarm_id` int(11) NOT NULL,
  `action` varchar(255) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `detection_logs`
--

INSERT INTO `detection_logs` (`id`, `alarm_id`, `action`, `timestamp`) VALUES
(1, 1, 'Intruso detectado', '2024-05-31 02:58:19'),
(2, 1, 'Intruso detectado', '2024-06-02 15:25:05'),
(3, 1, 'Intruso detectado', '2024-06-02 15:26:17'),
(4, 1, 'Intruso detectado', '2024-06-02 15:27:50'),
(5, 1, 'Intruso detectado', '2024-06-02 15:29:07'),
(6, 1, 'Intruso detectado', '2024-06-02 15:29:56'),
(7, 1, 'Intruso detectado', '2024-06-02 15:30:30'),
(8, 1, 'Intruso detectado', '2024-06-02 15:34:36'),
(9, 1, 'Intruso detectado', '2024-06-02 15:35:05'),
(10, 1, 'Intruso detectado', '2024-06-02 15:41:49'),
(11, 1, 'Intruso detectado', '2024-06-02 15:48:54'),
(12, 1, 'Intruso detectado', '2024-06-02 16:00:32'),
(13, 1, 'Intruso detectado', '2024-06-02 16:03:38'),
(14, 1, 'Intruso detectado', '2024-06-02 16:04:35'),
(15, 1, 'Intruso detectado', '2024-06-02 16:04:54'),
(16, 1, 'Intruso detectado', '2024-06-02 16:05:11');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `devices`
--

CREATE TABLE `devices` (
  `id` int(11) NOT NULL,
  `name` varchar(44) NOT NULL,
  `status` int(11) NOT NULL,
  `activationPassword` varchar(220) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `devices`
--

INSERT INTO `devices` (`id`, `name`, `status`, `activationPassword`) VALUES
(1, 'laserAlarm', 0, '1234');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `alarm_logs`
--
ALTER TABLE `alarm_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `automation`
--
ALTER TABLE `automation`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `detection_logs`
--
ALTER TABLE `detection_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `devices`
--
ALTER TABLE `devices`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `alarm_logs`
--
ALTER TABLE `alarm_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=268;

--
-- AUTO_INCREMENT de la tabla `detection_logs`
--
ALTER TABLE `detection_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
