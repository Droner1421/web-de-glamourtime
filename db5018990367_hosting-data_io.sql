-- phpMyAdmin SQL Dump
-- version 4.9.11
-- https://www.phpmyadmin.net/
--
-- Servidor: db5018990367.hosting-data.io
-- Tiempo de generación: 08-12-2025 a las 08:30:22
-- Versión del servidor: 10.11.14-MariaDB-log
-- Versión de PHP: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `glamourtime`
--
CREATE DATABASE IF NOT EXISTS `glamourtime` DEFAULT CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci;
USE `glamourtime`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `citas`
--

CREATE TABLE `citas` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `telefono` varchar(20) NOT NULL,
  `servicios` text NOT NULL,
  `estilista` varchar(100) NOT NULL,
  `tipo_servicio` varchar(20) NOT NULL,
  `fecha` date NOT NULL,
  `hora` time NOT NULL,
  `notas` text DEFAULT NULL,
  `fecha_creacion` datetime DEFAULT current_timestamp(),
  `estado` varchar(20) DEFAULT 'pendiente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `citas`
--

INSERT INTO `citas` (`id`, `usuario_id`, `nombre`, `telefono`, `servicios`, `estilista`, `tipo_servicio`, `fecha`, `hora`, `notas`, `fecha_creacion`, `estado`) VALUES
(1, 8, 'valeria peña', '9874563210', 'corte', 'ana', 'adulto', '2025-12-02', '10:00:00', 'wqw', '2025-12-02 02:56:08', 'pendiente');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id_producto` int(11) NOT NULL,
  `nombre_producto` varchar(255) NOT NULL,
  `descripcion` text NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `imagen_url` varchar(500) DEFAULT NULL,
  `categoria` varchar(100) NOT NULL,
  `stock` int(11) DEFAULT 0,
  `activo` tinyint(1) DEFAULT 1,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id_producto`, `nombre_producto`, `descripcion`, `precio`, `imagen_url`, `categoria`, `stock`, `activo`, `fecha_creacion`, `fecha_actualizacion`) VALUES
(1, 'Medicasp', 'shampoo', '200.00', 'src/img/productos/20251120185148_691fa9949ab91.jpg', 'cabello', 0, 1, '2025-11-21 04:51:48', '2025-11-21 04:51:48'),
(3, 'Tratamiento de nutrición', 'Tratamiento en Aceite Elvive Óleo Extraordinario pelo más suave y con brillo intenso con 6 óleos de flores preciosas, 100 ml', '200.00', 'src/img/productos/20251120190042_691fabaa9a745.webp', 'cabello', 0, 1, '2025-11-21 05:00:42', '2025-11-21 05:00:42'),
(4, 'Mascarilla nutrición', 'Mascarilla Nutrición Intensiva para cabello seco y maltratado Nutre e Hidrata Pequi & Aguacate 300 ml', '98.00', 'src/img/productos/20251120190209_691fac0139bc1.webp', 'cabello', 0, 1, '2025-11-21 05:02:09', '2025-11-21 05:02:09'),
(5, 'Mascarilla capilar', 'Mascarilla para Cabello - Anti Frizz Cabello | Crema para Peinar - Activador de Rizos | Belleza Mujer - Tratamiento para Cabello Maltratado con Vitaminas para el Cabello', '300.00', 'src/img/productos/20251120190304_691fac3854339.webp', 'cabello', 0, 1, '2025-11-21 05:03:04', '2025-11-21 05:03:04'),
(6, 'Anti Frizz', 'Mist Anti Frizz Cabello | Aceite para Cabello - Complementar con Tratamiento para Cabello Maltratado | Belleza Mujer - Aceite para Cabello | Aporta Vitaminas para el Cabello - 300ml', '150.00', 'src/img/productos/20251120190356_691fac6c38249.webp', 'cabello', 0, 1, '2025-11-21 05:03:56', '2025-11-21 05:03:56'),
(7, 'Champú anticaida', 'Champú Anticaída, Ayuda a Reducir la Caída del Cabello y Estimula el Crecimiento Capilar, 200ml', '313.00', 'src/img/productos/20251120190557_691face527565.webp', 'cabello', 0, 1, '2025-11-21 05:05:57', '2025-11-21 05:05:57'),
(8, 'Shampoo natural', 'Shampoo de Romero y Cebolla Revitalizante Kaba By D\'Luchi para un Cabello Más Grueso, Abundante, Fuerte y Saludable, Libre de Sulfatos, Colorantes y Parabenos 230ml', '148.00', 'src/img/productos/20251120190658_691fad22a0c10.webp', 'cabello', 0, 1, '2025-11-21 05:06:58', '2025-11-21 05:06:58'),
(9, 'Tratamiento para pestañas', 'Tratamiento para Crecimiento de Pestañas con Pantenol y Biotina - Lash Serum Para Pestañas y Cejas más Fuertes, Voluminosas y Largas - Suero de Pestañas desde la Raíz', '559.00', 'src/img/productos/20251120190758_691fad5eabe32.webp', 'maquillaje', 0, 1, '2025-11-21 05:07:58', '2025-11-21 05:07:58'),
(10, 'Crema facial', 'Crema Hidratante Facial | Resveratrol y Acido Hialuronico |Hidrata profundamente y rejuvenece tu pie | combatiendo el envejecimiento prematuro y mejorando la elasticidad | skin care', '586.00', 'src/img/productos/20251120190855_691fad970cb3d.webp', 'facial', 0, 1, '2025-11-21 05:08:55', '2025-11-21 05:08:55'),
(11, 'Crema coreana', 'Crema Hidratante con Niacinamide, Centella Asiática, Colágeno, Hialuronato de Sodio, 100% Natural, Hipoalergénica, Crema Facial para Todo Tipo de Piel (100g/3.53oz)', '685.00', 'src/img/productos/20251120190940_691fadc48ed36.webp', 'facial', 0, 1, '2025-11-21 05:09:40', '2025-11-21 05:09:40'),
(12, 'Crema base mate', 'CC Cream Base de Maquillaje 7 en 1 con Protector Solar FPS 40 Cobertura Completa para Piel Grasa, Acabado Mate, Control de Brillo, 32 ml, Light Medium', '350.00', 'src/img/productos/20251120191049_691fae09b6728.webp', 'maquillaje', 0, 1, '2025-11-21 05:10:49', '2025-11-21 05:10:49'),
(13, 'Polvo translúcido', 'Polvo Traslúcido Suelto para Fijar el Maquillaje con Acabado Airbrush, Péptidos, Seda y Colágeno, para Todo Tipo de Piel, 7 g', '250.00', 'src/img/productos/20251120191138_691fae3a08d55.webp', 'maquillaje', 0, 1, '2025-11-21 05:11:38', '2025-11-21 05:11:38'),
(14, 'Primer de maquillaje', 'IT Cosmetics Your Skin But Better, Makeup Primer de Maquillaje, Oil Free, 30 ml', '249.00', 'src/img/productos/20251120191234_691fae723bafb.webp', 'maquillaje', 0, 1, '2025-11-21 05:12:34', '2025-11-21 05:12:34'),
(15, 'Iluminadores', 'Bratz x Beauty Creations | Paleta de Iluminadores Keep On Playin\' 4 Tonos, Alta Pigmentación', '250.00', 'src/img/productos/20251120191453_691faefd952b5.jpg', 'maquillaje', 0, 1, '2025-11-21 05:14:53', '2025-11-21 05:14:53'),
(16, 'Base en polvo', 'Beauty Creations Base En Polvo Flawless Stay Polvo Compacto (FSP8.0)', '147.00', 'src/img/productos/20251120191544_691faf30bf130.webp', 'maquillaje', 0, 1, '2025-11-21 05:15:44', '2025-11-21 05:15:44'),
(17, 'Polvo fijador', 'Polvo Fijador Traslucido Bye Filter Beauty Creations 3 tonalidades (PINK CLOUD)', '180.00', 'src/img/productos/20251120191647_691faf6fcd203.webp', 'maquillaje', 0, 1, '2025-11-21 05:16:47', '2025-11-21 05:16:47'),
(18, 'Corrector', 'Corrector cobertura completa Beauty Creations (C11)', '120.00', 'src/img/productos/20251120191831_691fafd78d733.webp', 'maquillaje', 0, 1, '2025-11-21 05:18:31', '2025-11-21 05:18:31'),
(19, 'Labial mate', 'Beauty Creations Balm N\' Cute Bálsamo Labial Con Color 6 Tonos Distintos (Coconut)', '160.00', 'src/img/productos/20251120191918_691fb0068a541.webp', 'maquillaje', 0, 1, '2025-11-21 05:19:18', '2025-11-21 05:19:18'),
(20, 'Paleta de sombras', 'PALETA DE SOMBRAS THE LEGEND BEAUTY CREATIONS', '500.00', 'src/img/productos/20251120192014_691fb03e30381.webp', 'maquillaje', 0, 1, '2025-11-21 05:20:14', '2025-11-21 05:20:14'),
(21, 'Paleta de sombras', 'Paleta de sombras 35 Tonos Tiana Beauty Creations®', '689.00', 'src/img/productos/20251120192054_691fb0661f28e.webp', 'maquillaje', 0, 1, '2025-11-21 05:20:54', '2025-11-21 05:20:54'),
(22, 'Paleta de sombras floral', 'Beauty Creations - Floral, flor para ojos - 12 colores', '568.00', 'src/img/productos/20251120192143_691fb097d6867.webp', 'maquillaje', 0, 1, '2025-11-21 05:21:43', '2025-11-21 05:21:43'),
(23, 'Sombra individual', 'Beauty Creations – Sombra de Ojos Individual Riding Solo (Lucky) – Acabado Suave y con Brillo Marfil', '150.00', 'src/img/productos/20251120192248_691fb0d851c50.webp', 'maquillaje', 0, 1, '2025-11-21 05:22:48', '2025-11-21 05:22:48'),
(24, 'Rubores', 'Beauty Creations Paleta De Rubores Pink Dream Blushes Rosy McMichael X Beauty Creations Vol 2 Incluye 3 Tonos', '570.00', 'src/img/productos/20251120192340_691fb10cedff2.webp', 'maquillaje', 0, 1, '2025-11-21 05:23:40', '2025-11-21 05:23:40'),
(25, 'Paleta completa', 'Beauty Creations - Floral Bloom Highlight & Contour – Polvo iluminador y contorno, tonos neutros, cálidos y fríos, con Acabado brillante para todo tipo de piel – 18 g', '850.00', 'src/img/productos/20251120192428_691fb13ce3416.webp', 'maquillaje', 0, 1, '2025-11-21 05:24:28', '2025-11-21 05:24:28'),
(26, 'Paleta de maquillaje', 'Paleta de Rubor, Paleta de Maquillaje Resaltado con Espejo - Paleta de Contorno de 6 Colores - Kit de Contorno de Rubor para Regalos de Navidad de Año Nuevo (Foundation 1, 6 color)', '500.00', 'src/img/productos/20251120192527_691fb177d4274.webp', 'maquillaje', 0, 1, '2025-11-21 05:25:27', '2025-11-21 05:25:27'),
(27, 'Fijador de maquillaje', 'Fijador de Maquillaje, Resistente al Agua, Transferencias y Desvanecimientos, Duración 24H, 60 ml', '120.00', 'src/img/productos/20251120192644_691fb1c49d808.webp', 'maquillaje', 0, 1, '2025-11-21 05:26:44', '2025-11-21 05:26:44'),
(28, 'Suero facial', 'Suero Facial Hidratante en Gel con Ácido Hialurónico, Colágeno, Elastina y Té Blanco – Oil-Free, Rápida Absorción, Ilumina y Suaviza la Piel de Rostro, Cuello, Escote y Manos, 125 ml', '569.00', 'src/img/productos/20251120192857_691fb24928ff1.webp', 'facial', 0, 1, '2025-11-21 05:28:57', '2025-11-21 05:28:57'),
(29, 'Masajeador facial', 'Masajeador facial eléctrico microcorrientes - Dispositivo radiofrecuencia y alta frecuencia - Cuidado facial - Lavander', '1990.00', 'src/img/productos/20251120193026_691fb2a2aef8d.webp', 'facial', 0, 1, '2025-11-21 05:30:26', '2025-11-21 05:30:26'),
(30, 'Gua Sha', 'Herramienta facial Gua Sha de acero inoxidable, herramienta de masaje de piel para cara, cuerpo, ojos y cuello, forma de corazón con bolsa de viaje, regalo para el cuidado de la piel', '379.00', 'src/img/productos/20251120193139_691fb2eba8447.webp', 'facial', 0, 1, '2025-11-21 05:31:39', '2025-11-21 05:31:39'),
(31, 'Mascarilla facial', 'Madagascar Centella Poremizing Quick Clay Stick Mask 27g', '290.00', 'src/img/productos/20251120193247_691fb32f17d9e.webp', 'facial', 0, 1, '2025-11-21 05:32:47', '2025-11-21 05:32:47'),
(32, 'Mascarilla facial', 'ascarillas Faciales,Mascarillas Faciales Coreanas de Colágeno Nocturnas,Hidrogel con Tecnología Coreana para Minimizar Poros + Hidratación Profunda. (Rosa-5 piezas)', '560.00', 'src/img/productos/20251120193354_691fb372970ca.webp', 'facial', 0, 1, '2025-11-21 05:33:54', '2025-11-21 05:33:54'),
(33, 'Uñas Press On', 'Uñas Postizas de Cortas Largas de Francesa, Adhesivas Uñas Press on Nails de Cobertura Completa, Falsas Decoradas Acrílico Natural para Mujeres y (A)', '250.00', 'src/img/productos/20251120193605_691fb3f5edd1c.webp', 'unas', 0, 1, '2025-11-21 05:36:05', '2025-11-21 05:36:05'),
(34, 'Uñas Press On', 'Uñas Postizas de Cortas Largas de Francesa, Adhesivas Uñas Press on Nails de Cobertura Completa, Falsas Decoradas Acrílico Natural para Mujeres y(C)', '230.00', 'src/img/productos/20251120193753_691fb4612f563.webp', 'unas', 0, 1, '2025-11-21 05:37:53', '2025-11-21 05:37:53'),
(35, 'Uñas Press On Navideñas', 'uñas postizas navideñas, con 24 pestañas adhesivas, pegamento para uñas, lima pequeña, palito de manicura Uñas Adhesivas Press on Nails de Cobertura Completa para Mujeres y Niñas', '280.00', 'src/img/productos/20251120193843_691fb493c8b5b.webp', 'unas', 0, 1, '2025-11-21 05:38:43', '2025-11-21 05:38:43'),
(36, 'Uñas Press On Navideñas (Blancas)', 'uñas postizas navideñas, con 24 pestañas adhesivas, pegamento para uñas, lima pequeña, palito de manicura Uñas Adhesivas Press on Nails de Cobertura Completa para Mujeres y Niñas', '260.00', 'src/img/productos/20251120193937_691fb4c9c087d.jpg', 'unas', 0, 1, '2025-11-21 05:39:37', '2025-11-21 05:39:37'),
(37, 'Uñas postizas', 'Hemobllo Uñas Postizas Navideñas para Niñas 72 Piezas Tamaño Mezcla Diseño de Puntas de Uñas Infantiles para Fiestas y Decoración Temática de Navidad', '125.00', 'src/img/productos/20251120194030_691fb4fee4c56.webp', 'unas', 0, 1, '2025-11-21 05:40:30', '2025-11-21 05:40:30'),
(38, 'Uñas Press On', 'Healvian 24 Piezas Manicura De Uñas Postizas Uñas Rojizo Completas Extraíbles Para Mujeres Niñas Diy Decoración De Color Vino', '180.00', 'src/img/productos/20251120194118_691fb52e98d31.webp', 'unas', 0, 1, '2025-11-21 05:41:18', '2025-11-21 05:41:18'),
(39, 'Uñas Press On', '24 Uñas postizas 3D en forma de almendra, uñas acrílicas simples a presión, cubiertas completas brillantes para mujeres y niñas', '139.00', 'src/img/productos/20251120194724_691fb69cea0e9.webp', 'unas', 0, 1, '2025-11-21 05:47:24', '2025-11-21 05:47:24'),
(40, 'Uñas Press On', 'Tips Uñas Press On Nails Mariposa Purpurina Con Pegamento', '129.00', 'src/img/productos/20251120195004_691fb73cc3057.webp', 'unas', 0, 1, '2025-11-21 05:50:04', '2025-11-21 05:50:04'),
(41, 'peine adicion', 'Nuevo adicion', '300.00', 'src/img/productos/20251206165202_6934a5827affc.png', 'cabello', 0, 1, '2025-12-06 21:52:02', '2025-12-06 21:52:37');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(3) NOT NULL,
  `nombres` varchar(255) NOT NULL,
  `apellidopat` varchar(266) NOT NULL,
  `apellidomat` varchar(266) NOT NULL,
  `correo` varchar(266) NOT NULL,
  `telefono` int(10) NOT NULL,
  `contrasena` varchar(266) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombres`, `apellidopat`, `apellidomat`, `correo`, `telefono`, `contrasena`) VALUES
(1, 'Nicolasa', 'patricia', 'Angel', 'qweqwqq@ghasb.com', 1234567890, '12'),
(2, 'Prueba', 'segunda', 'pruebas', 'adad@gmail.com', 1234567890, '123'),
(4, 'Admin', 'Glamour', 'Time', 'glamourtim3@gmail.com', 0, 'admin'),
(5, 'Prueba', 'segunda', 'asadasd', 'adad@ad.com', 1234567890, '123'),
(6, 'CASSANDRA', 'MEDINA', 'LERMA', 'daskdjadsk@dka', 0, 'admin'),
(7, 'kARLA', 'SALDOVA', 'CAMPOS', 'gato12@gamil.com', 0, '123456789'),
(8, 'valeria', 'peña', 'romero', 'vale2396@gmail.com', 2147483647, '123'),
(9, 'Yo', '..', '...', 'revision@gmail.com', 123456789, '123'),
(10, 'Usuario', '2', '..', 'usuario@gamil.com', 2147483647, '123');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `citas`
--
ALTER TABLE `citas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id_producto`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `citas`
--
ALTER TABLE `citas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id_producto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(3) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
