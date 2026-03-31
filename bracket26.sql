-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 30-03-2026 a las 17:13:08
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
-- Base de datos: `bracket26`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `equipos`
--

CREATE TABLE `equipos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `bandera_url` varchar(255) DEFAULT NULL,
  `grupo` varchar(1) DEFAULT NULL,
  `puntos_totales` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `equipos`
--

INSERT INTO `equipos` (`id`, `nombre`, `bandera_url`, `grupo`, `puntos_totales`) VALUES
(1, 'México', 'https://flagcdn.com/w40/mx.png', 'A', 0),
(2, 'Sudáfrica', 'https://flagcdn.com/w40/za.png', 'A', 0),
(3, 'Corea del Sur', 'https://flagcdn.com/w40/kr.png', 'A', 0),
(4, 'Por clasificar', 'https://upload.wikimedia.org/wikipedia/commons/thumb/d/d9/Icon-round-Question_mark.svg/40px-Icon-round-Question_mark.svg.png', 'A', 0),
(5, 'Canadá', 'https://flagcdn.com/w40/ca.png', 'B', 0),
(6, 'Suiza', 'https://flagcdn.com/w40/ch.png', 'B', 0),
(7, 'Catar', 'https://flagcdn.com/w40/qa.png', 'B', 0),
(8, 'Por clasificar', 'https://upload.wikimedia.org/wikipedia/commons/thumb/d/d9/Icon-round-Question_mark.svg/40px-Icon-round-Question_mark.svg.png', 'B', 0),
(9, 'Brasil', 'https://flagcdn.com/w40/br.png', 'C', 0),
(10, 'Marruecos', 'https://flagcdn.com/w40/ma.png', 'C', 0),
(11, 'Escocia', 'https://flagcdn.com/w40/gb-sct.png', 'C', 0),
(12, 'Haití', 'https://flagcdn.com/w40/ht.png', 'C', 0),
(13, 'Estados Unidos', 'https://flagcdn.com/w40/us.png', 'D', 0),
(14, 'Paraguay', 'https://flagcdn.com/w40/py.png', 'D', 0),
(15, 'Australia', 'https://flagcdn.com/w40/au.png', 'D', 0),
(16, 'Por clasificar', 'https://upload.wikimedia.org/wikipedia/commons/thumb/d/d9/Icon-round-Question_mark.svg/40px-Icon-round-Question_mark.svg.png', 'D', 0),
(17, 'España', 'https://flagcdn.com/w40/es.png', 'H', 0),
(18, 'Uruguay', 'https://flagcdn.com/w40/uy.png', 'H', 0),
(19, 'Arabia Saudí', 'https://flagcdn.com/w40/sa.png', 'H', 0),
(20, 'Cabo Verde', 'https://flagcdn.com/w40/cv.png', 'H', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `partidos`
--

CREATE TABLE `partidos` (
  `id` int(11) NOT NULL,
  `equipo_local_id` int(11) DEFAULT NULL,
  `equipo_visitante_id` int(11) DEFAULT NULL,
  `goles_local` int(11) DEFAULT NULL,
  `goles_visitante` int(11) DEFAULT NULL,
  `fase` varchar(50) DEFAULT NULL,
  `estado` varchar(20) DEFAULT 'pendiente',
  `fecha_hora` datetime DEFAULT NULL,
  `siguiente_partido_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `partidos`
--

INSERT INTO `partidos` (`id`, `equipo_local_id`, `equipo_visitante_id`, `goles_local`, `goles_visitante`, `fase`, `estado`, `fecha_hora`, `siguiente_partido_id`) VALUES
(1, 1, 2, NULL, NULL, 'grupo', 'pendiente', '2026-06-11 18:00:00', NULL),
(2, 3, 4, NULL, NULL, 'grupo', 'pendiente', '2026-06-11 18:00:00', NULL),
(3, 1, 3, NULL, NULL, 'grupo', 'pendiente', '2026-06-15 18:00:00', NULL),
(4, 2, 4, NULL, NULL, 'grupo', 'pendiente', '2026-06-15 18:00:00', NULL),
(5, 1, 4, NULL, NULL, 'grupo', 'pendiente', '2026-06-19 18:00:00', NULL),
(6, 2, 3, NULL, NULL, 'grupo', 'pendiente', '2026-06-19 18:00:00', NULL),
(7, 5, 6, NULL, NULL, 'grupo', 'pendiente', '2026-06-12 18:00:00', NULL),
(8, 7, 8, NULL, NULL, 'grupo', 'pendiente', '2026-06-12 18:00:00', NULL),
(9, 5, 7, NULL, NULL, 'grupo', 'pendiente', '2026-06-16 18:00:00', NULL),
(10, 6, 8, NULL, NULL, 'grupo', 'pendiente', '2026-06-16 18:00:00', NULL),
(11, 5, 8, NULL, NULL, 'grupo', 'pendiente', '2026-06-20 18:00:00', NULL),
(12, 6, 7, NULL, NULL, 'grupo', 'pendiente', '2026-06-20 18:00:00', NULL),
(13, 9, 10, NULL, NULL, 'grupo', 'pendiente', '2026-06-13 18:00:00', NULL),
(14, 11, 12, NULL, NULL, 'grupo', 'pendiente', '2026-06-13 18:00:00', NULL),
(15, 9, 11, NULL, NULL, 'grupo', 'pendiente', '2026-06-17 18:00:00', NULL),
(16, 10, 12, NULL, NULL, 'grupo', 'pendiente', '2026-06-17 18:00:00', NULL),
(17, 9, 12, NULL, NULL, 'grupo', 'pendiente', '2026-06-21 18:00:00', NULL),
(18, 10, 11, NULL, NULL, 'grupo', 'pendiente', '2026-06-21 18:00:00', NULL),
(19, 13, 14, NULL, NULL, 'grupo', 'pendiente', '2026-06-14 18:00:00', NULL),
(20, 15, 16, NULL, NULL, 'grupo', 'pendiente', '2026-06-14 18:00:00', NULL),
(21, 13, 15, NULL, NULL, 'grupo', 'pendiente', '2026-06-18 18:00:00', NULL),
(22, 14, 16, NULL, NULL, 'grupo', 'pendiente', '2026-06-18 18:00:00', NULL),
(23, 13, 16, NULL, NULL, 'grupo', 'pendiente', '2026-06-22 18:00:00', NULL),
(24, 14, 15, NULL, NULL, 'grupo', 'pendiente', '2026-06-22 18:00:00', NULL),
(25, 17, 18, NULL, NULL, 'grupo', 'pendiente', '2026-06-15 18:00:00', NULL),
(26, 19, 20, NULL, NULL, 'grupo', 'pendiente', '2026-06-15 18:00:00', NULL),
(27, 17, 19, NULL, NULL, 'grupo', 'pendiente', '2026-06-19 18:00:00', NULL),
(28, 18, 20, NULL, NULL, 'grupo', 'pendiente', '2026-06-19 18:00:00', NULL),
(29, 17, 20, NULL, NULL, 'grupo', 'pendiente', '2026-06-23 18:00:00', NULL),
(30, 18, 19, NULL, NULL, 'grupo', 'pendiente', '2026-06-23 18:00:00', NULL),
(35, NULL, NULL, NULL, NULL, '1/16', 'pendiente', NULL, NULL),
(36, NULL, NULL, NULL, NULL, '1/16', 'pendiente', NULL, NULL),
(37, NULL, NULL, NULL, NULL, '1/16', 'pendiente', NULL, NULL),
(38, NULL, NULL, NULL, NULL, '1/16', 'pendiente', NULL, NULL),
(39, NULL, NULL, NULL, NULL, '1/16', 'pendiente', NULL, NULL),
(40, NULL, NULL, NULL, NULL, '1/16', 'pendiente', NULL, NULL),
(41, NULL, NULL, NULL, NULL, '1/16', 'pendiente', NULL, NULL),
(42, NULL, NULL, NULL, NULL, '1/16', 'pendiente', NULL, NULL),
(43, NULL, NULL, NULL, NULL, '1/16', 'pendiente', NULL, NULL),
(44, NULL, NULL, NULL, NULL, '1/16', 'pendiente', NULL, NULL),
(45, NULL, NULL, NULL, NULL, '1/16', 'pendiente', NULL, NULL),
(46, NULL, NULL, NULL, NULL, '1/16', 'pendiente', NULL, NULL),
(47, NULL, NULL, NULL, NULL, '1/16', 'pendiente', NULL, NULL),
(48, NULL, NULL, NULL, NULL, '1/16', 'pendiente', NULL, NULL),
(49, NULL, NULL, NULL, NULL, '1/16', 'pendiente', NULL, NULL),
(50, NULL, NULL, NULL, NULL, '1/16', 'pendiente', NULL, NULL),
(51, NULL, NULL, NULL, NULL, 'Octavos', 'pendiente', NULL, NULL),
(52, NULL, NULL, NULL, NULL, 'Octavos', 'pendiente', NULL, NULL),
(53, NULL, NULL, NULL, NULL, 'Octavos', 'pendiente', NULL, NULL),
(54, NULL, NULL, NULL, NULL, 'Octavos', 'pendiente', NULL, NULL),
(55, NULL, NULL, NULL, NULL, 'Octavos', 'pendiente', NULL, NULL),
(56, NULL, NULL, NULL, NULL, 'Octavos', 'pendiente', NULL, NULL),
(57, NULL, NULL, NULL, NULL, 'Octavos', 'pendiente', NULL, NULL),
(58, NULL, NULL, NULL, NULL, 'Octavos', 'pendiente', NULL, NULL),
(59, NULL, NULL, NULL, NULL, 'Cuartos', 'pendiente', NULL, NULL),
(60, NULL, NULL, NULL, NULL, 'Cuartos', 'pendiente', NULL, NULL),
(61, NULL, NULL, NULL, NULL, 'Cuartos', 'pendiente', NULL, NULL),
(62, NULL, NULL, NULL, NULL, 'Cuartos', 'pendiente', NULL, NULL),
(63, NULL, NULL, NULL, NULL, 'Semis', 'pendiente', NULL, NULL),
(64, NULL, NULL, NULL, NULL, 'Semis', 'pendiente', NULL, NULL),
(65, NULL, NULL, NULL, NULL, 'Final', 'pendiente', NULL, NULL);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `equipos`
--
ALTER TABLE `equipos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `partidos`
--
ALTER TABLE `partidos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `equipo_local_id` (`equipo_local_id`),
  ADD KEY `equipo_visitante_id` (`equipo_visitante_id`),
  ADD KEY `siguiente_partido_id` (`siguiente_partido_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `equipos`
--
ALTER TABLE `equipos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de la tabla `partidos`
--
ALTER TABLE `partidos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `partidos`
--
ALTER TABLE `partidos`
  ADD CONSTRAINT `partidos_ibfk_1` FOREIGN KEY (`equipo_local_id`) REFERENCES `equipos` (`id`),
  ADD CONSTRAINT `partidos_ibfk_2` FOREIGN KEY (`equipo_visitante_id`) REFERENCES `equipos` (`id`),
  ADD CONSTRAINT `partidos_ibfk_3` FOREIGN KEY (`siguiente_partido_id`) REFERENCES `partidos` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
