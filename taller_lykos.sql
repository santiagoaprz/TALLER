-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 19-10-2025 a las 20:11:14
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
-- Base de datos: `taller_lykos`
--
CREATE DATABASE IF NOT EXISTS `taller_lykos` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `taller_lykos`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

DROP TABLE IF EXISTS `clientes`;
CREATE TABLE `clientes` (
  `id_cliente` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `direccion` text DEFAULT NULL,
  `rfc` varchar(20) DEFAULT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  `activo` tinyint(4) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `equipos`
--

DROP TABLE IF EXISTS `equipos`;
CREATE TABLE `equipos` (
  `id_equipo` int(11) NOT NULL,
  `id_cliente` int(11) NOT NULL,
  `tipo_equipo` enum('Laptop','Celular','Tablet','Videoconsola','PC Escritorio','Impresora','Otro') NOT NULL,
  `marca` varchar(50) DEFAULT NULL,
  `modelo` varchar(50) DEFAULT NULL,
  `numero_serie` varchar(100) DEFAULT NULL,
  `imei` varchar(20) DEFAULT NULL,
  `color` varchar(30) DEFAULT NULL,
  `capacidad_almacenamiento` varchar(50) DEFAULT NULL,
  `ram` varchar(20) DEFAULT NULL,
  `procesador` varchar(50) DEFAULT NULL,
  `sistema_operativo` varchar(50) DEFAULT NULL,
  `version_so` varchar(50) DEFAULT NULL,
  `estado_fisico` text DEFAULT NULL,
  `golpes` text DEFAULT NULL,
  `rayones` text DEFAULT NULL,
  `faltan_tornillos` text DEFAULT NULL,
  `desgaste_uso` text DEFAULT NULL,
  `enciende` tinyint(4) DEFAULT 0,
  `carga` tinyint(4) DEFAULT 0,
  `display_funciona` tinyint(4) DEFAULT 0,
  `accesorios_ingreso` text DEFAULT NULL,
  `tiene_cargador` tinyint(4) DEFAULT 0,
  `numero_serie_cargador` varchar(100) DEFAULT NULL,
  `estado_cargador` text DEFAULT NULL,
  `funda_proteccion` tinyint(4) DEFAULT 0,
  `cables_extra` text DEFAULT NULL,
  `otros_accesorios` text DEFAULT NULL,
  `problemas_reportados` text DEFAULT NULL,
  `bocinas_funcionan` tinyint(4) DEFAULT 1,
  `microfono_funciona` tinyint(4) DEFAULT 1,
  `camara_funciona` tinyint(4) DEFAULT 1,
  `botones_funcionan` tinyint(4) DEFAULT 1,
  `huella_funciona` tinyint(4) DEFAULT 1,
  `wifi_funciona` tinyint(4) DEFAULT 1,
  `señal_funciona` tinyint(4) DEFAULT 1,
  `sensores_funcionan` tinyint(4) DEFAULT 1,
  `reporte_robo` tinyint(4) DEFAULT 0,
  `usb_funciona` tinyint(4) DEFAULT 1,
  `hdmi_funciona` tinyint(4) DEFAULT 1,
  `vga_funciona` tinyint(4) DEFAULT 1,
  `ethernet_funciona` tinyint(4) DEFAULT 1,
  `wifi_pc_funciona` tinyint(4) DEFAULT 1,
  `bluetooth_funciona` tinyint(4) DEFAULT 1,
  `teclado_funciona` tinyint(4) DEFAULT 1,
  `touchpad_funciona` tinyint(4) DEFAULT 1,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `gastos`
--

DROP TABLE IF EXISTS `gastos`;
CREATE TABLE `gastos` (
  `id_gasto` int(11) NOT NULL,
  `descripcion` text NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `fecha_gasto` date NOT NULL,
  `categoria` enum('Herramientas','Renta','Software','Servicios','Materiales','Nómina','Otros') DEFAULT NULL,
  `comprobante` varchar(100) DEFAULT NULL,
  `observaciones` text DEFAULT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inventario`
--

DROP TABLE IF EXISTS `inventario`;
CREATE TABLE `inventario` (
  `id_pieza` int(11) NOT NULL,
  `nombre_pieza` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `categoria` enum('Pantalla','Batería','Cargador','Flex','Cámara','Placa','Memoria','Procesador','Otro') DEFAULT NULL,
  `cantidad_stock` int(11) DEFAULT 0,
  `stock_minimo` int(11) DEFAULT 0,
  `precio_compra` decimal(10,2) DEFAULT NULL,
  `precio_venta` decimal(10,2) DEFAULT NULL,
  `proveedor` varchar(100) DEFAULT NULL,
  `numero_parte` varchar(100) DEFAULT NULL,
  `compatible_con` text DEFAULT NULL,
  `ubicacion` varchar(100) DEFAULT NULL,
  `activo` tinyint(4) DEFAULT 1,
  `fecha_actualizacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ordenes_servicio`
--

DROP TABLE IF EXISTS `ordenes_servicio`;
CREATE TABLE `ordenes_servicio` (
  `id_orden` int(11) NOT NULL,
  `id_equipo` int(11) NOT NULL,
  `folio` varchar(20) DEFAULT NULL,
  `fecha_ingreso` date NOT NULL,
  `hora_ingreso` time DEFAULT NULL,
  `fecha_entrega_estimada` date DEFAULT NULL,
  `fecha_entrega_real` date DEFAULT NULL,
  `tecnico_asignado` varchar(100) DEFAULT 'Santiago',
  `estado_orden` enum('Recibido','En Diagnóstico','Diagnóstico Completado','Esperando Aprobación Cliente','En Reparación','Esperando Repuestos','Reparado','Pruebas Finales','Listo para Entregar','Entregado','No Reparado','Cancelado') DEFAULT 'Recibido',
  `diagnostico` text DEFAULT NULL,
  `trabajo_realizado` text DEFAULT NULL,
  `observaciones_internas` text DEFAULT NULL,
  `piezas_necesarias` text DEFAULT NULL,
  `dificultades_encontradas` text DEFAULT NULL,
  `costo_estimado` decimal(10,2) DEFAULT 0.00,
  `costo_final` decimal(10,2) DEFAULT 0.00,
  `anticipo` decimal(10,2) DEFAULT 0.00,
  `saldo_pendiente` decimal(10,2) DEFAULT 0.00,
  `firma_tecnico` text DEFAULT NULL,
  `firma_cliente` text DEFAULT NULL,
  `activa` tinyint(4) DEFAULT 1,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proveedores`
--

DROP TABLE IF EXISTS `proveedores`;
CREATE TABLE `proveedores` (
  `id_proveedor` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `contacto` varchar(100) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `direccion` text DEFAULT NULL,
  `productos_especialidad` text DEFAULT NULL,
  `calificacion` int(11) DEFAULT 5,
  `confiable` tinyint(4) DEFAULT 1,
  `observaciones` text DEFAULT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `proveedores`
--

INSERT INTO `proveedores` (`id_proveedor`, `nombre`, `contacto`, `telefono`, `email`, `direccion`, `productos_especialidad`, `calificacion`, `confiable`, `observaciones`, `fecha_registro`) VALUES
(1, 'prueba de edicion proveedor', 'elis hernandez', '5512345677', '', '', 'Pantallas y baterías para laptops y celulares', 1, 1, 'se fue juan perez, ingresa elis con cambio de num a 1 estrella para comprobar su calificacion', '2025-10-19 07:39:47'),
(2, 'Electrónica MX', 'María García', '5598765432', NULL, NULL, 'Componentes electrónicos y herramientas', 5, 1, NULL, '2025-10-19 07:39:47'),
(3, 'Proveedor Local Centro', 'Juan Pérez', '5512345678', NULL, NULL, 'Pantallas y baterías para laptops y celulares', 5, 1, NULL, '2025-10-19 07:43:10'),
(4, 'Electrónica MX', 'María García', '5598765432', NULL, NULL, 'Componentes electrónicos y herramientas', 5, 1, NULL, '2025-10-19 07:43:10'),
(5, 'Proveedor Local Centro', 'Juan Pérez', '5512345678', NULL, NULL, 'Pantallas y baterías para laptops y celulares', 5, 1, NULL, '2025-10-19 08:14:35'),
(6, 'Electrónica MX', 'María García', '5598765432', NULL, NULL, 'Componentes electrónicos y herramientas', 5, 1, NULL, '2025-10-19 08:14:35');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `seguimiento_orden`
--

DROP TABLE IF EXISTS `seguimiento_orden`;
CREATE TABLE `seguimiento_orden` (
  `id_seguimiento` int(11) NOT NULL,
  `id_orden` int(11) NOT NULL,
  `fecha_hora` timestamp NOT NULL DEFAULT current_timestamp(),
  `estado_anterior` varchar(50) DEFAULT NULL,
  `estado_nuevo` varchar(50) DEFAULT NULL,
  `observaciones` text DEFAULT NULL,
  `tecnico` varchar(100) DEFAULT NULL,
  `tipo_seguimiento` enum('Cambio Estado','Observación','Solicitud Piezas','Actualización Diagnóstico') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id_cliente`);

--
-- Indices de la tabla `equipos`
--
ALTER TABLE `equipos`
  ADD PRIMARY KEY (`id_equipo`),
  ADD KEY `id_cliente` (`id_cliente`);

--
-- Indices de la tabla `gastos`
--
ALTER TABLE `gastos`
  ADD PRIMARY KEY (`id_gasto`);

--
-- Indices de la tabla `inventario`
--
ALTER TABLE `inventario`
  ADD PRIMARY KEY (`id_pieza`);

--
-- Indices de la tabla `ordenes_servicio`
--
ALTER TABLE `ordenes_servicio`
  ADD PRIMARY KEY (`id_orden`),
  ADD UNIQUE KEY `folio` (`folio`),
  ADD KEY `id_equipo` (`id_equipo`);

--
-- Indices de la tabla `proveedores`
--
ALTER TABLE `proveedores`
  ADD PRIMARY KEY (`id_proveedor`);

--
-- Indices de la tabla `seguimiento_orden`
--
ALTER TABLE `seguimiento_orden`
  ADD PRIMARY KEY (`id_seguimiento`),
  ADD KEY `id_orden` (`id_orden`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id_cliente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `equipos`
--
ALTER TABLE `equipos`
  MODIFY `id_equipo` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `gastos`
--
ALTER TABLE `gastos`
  MODIFY `id_gasto` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `inventario`
--
ALTER TABLE `inventario`
  MODIFY `id_pieza` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `ordenes_servicio`
--
ALTER TABLE `ordenes_servicio`
  MODIFY `id_orden` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `proveedores`
--
ALTER TABLE `proveedores`
  MODIFY `id_proveedor` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `seguimiento_orden`
--
ALTER TABLE `seguimiento_orden`
  MODIFY `id_seguimiento` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `equipos`
--
ALTER TABLE `equipos`
  ADD CONSTRAINT `equipos_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`) ON DELETE CASCADE;

--
-- Filtros para la tabla `ordenes_servicio`
--
ALTER TABLE `ordenes_servicio`
  ADD CONSTRAINT `ordenes_servicio_ibfk_1` FOREIGN KEY (`id_equipo`) REFERENCES `equipos` (`id_equipo`) ON DELETE CASCADE;

--
-- Filtros para la tabla `seguimiento_orden`
--
ALTER TABLE `seguimiento_orden`
  ADD CONSTRAINT `seguimiento_orden_ibfk_1` FOREIGN KEY (`id_orden`) REFERENCES `ordenes_servicio` (`id_orden`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
