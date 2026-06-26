-- ============================================
-- HotelMS - Schema completo
-- Base de datos: hotelms
-- Motor: InnoDB, charset: utf8mb4
-- ============================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- -------------------------------------------
-- Tabla: user (Administradores)
-- -------------------------------------------
CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_user_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Usuario admin por defecto (password: admin123 bcrypt)
INSERT INTO `user` (`username`, `email`, `password`) VALUES
('christine', 'christine@hotelms.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('harryden', 'harryden@hotelms.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- -------------------------------------------
-- Tabla: staff_type (Tipos de empleado)
-- -------------------------------------------
CREATE TABLE IF NOT EXISTS `staff_type` (
  `staff_type_id` int(11) NOT NULL AUTO_INCREMENT,
  `staff_type` varchar(100) NOT NULL,
  PRIMARY KEY (`staff_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `staff_type` (`staff_type`) VALUES
('Manager'), ('Receptionist'), ('Housekeeping'), ('Chef'), ('Waiter'), ('Security');

-- -------------------------------------------
-- Tabla: shift (Turnos)
-- -------------------------------------------
CREATE TABLE IF NOT EXISTS `shift` (
  `shift_id` int(11) NOT NULL AUTO_INCREMENT,
  `shift` varchar(50) NOT NULL,
  `shift_timing` varchar(100) NOT NULL,
  PRIMARY KEY (`shift_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `shift` (`shift`, `shift_timing`) VALUES
('Morning', '8:00 AM - 4:00 PM'),
('Evening', '4:00 PM - 12:00 AM'),
('Night', '12:00 AM - 8:00 AM');

-- -------------------------------------------
-- Tabla: id_card_type (Tipos de documento)
-- -------------------------------------------
CREATE TABLE IF NOT EXISTS `id_card_type` (
  `id_card_type_id` int(11) NOT NULL AUTO_INCREMENT,
  `id_card_type` varchar(50) NOT NULL,
  PRIMARY KEY (`id_card_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `id_card_type` (`id_card_type`) VALUES
('CC'), ('TI'), ('Pasaporte'), ('CE'), ('PPT');

-- -------------------------------------------
-- Tabla: staff (Empleados)
-- -------------------------------------------
CREATE TABLE IF NOT EXISTS `staff` (
  `emp_id` int(11) NOT NULL AUTO_INCREMENT,
  `emp_name` varchar(200) NOT NULL,
  `staff_type_id` int(11) NOT NULL,
  `shift_id` int(11) NOT NULL,
  `id_card_type` int(11) NOT NULL DEFAULT 1,
  `id_card_no` varchar(50) NOT NULL,
  `address` text,
  `contact_no` varchar(20) NOT NULL,
  `salary` int(11) NOT NULL DEFAULT 0,
  `joining_date` date DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`emp_id`),
  KEY `fk_staff_type` (`staff_type_id`),
  KEY `fk_staff_shift` (`shift_id`),
  CONSTRAINT `fk_staff_type` FOREIGN KEY (`staff_type_id`) REFERENCES `staff_type`(`staff_type_id`),
  CONSTRAINT `fk_staff_shift` FOREIGN KEY (`shift_id`) REFERENCES `shift`(`shift_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -------------------------------------------
-- Tabla: emp_history (Historial de turnos)
-- -------------------------------------------
CREATE TABLE IF NOT EXISTS `emp_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `emp_id` int(11) NOT NULL,
  `shift_id` int(11) NOT NULL,
  `from_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `to_date` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_hist_emp` (`emp_id`),
  KEY `fk_hist_shift` (`shift_id`),
  CONSTRAINT `fk_hist_emp` FOREIGN KEY (`emp_id`) REFERENCES `staff`(`emp_id`),
  CONSTRAINT `fk_hist_shift` FOREIGN KEY (`shift_id`) REFERENCES `shift`(`shift_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -------------------------------------------
-- Tabla: room_type (Tipos de habitación)
-- -------------------------------------------
CREATE TABLE IF NOT EXISTS `room_type` (
  `room_type_id` int(11) NOT NULL AUTO_INCREMENT,
  `room_type` varchar(100) NOT NULL,
  `price` int(11) NOT NULL,
  `max_person` int(11) NOT NULL DEFAULT 1,
  `description` text,
  `image` varchar(255) DEFAULT NULL,
  `amenities` text,
  PRIMARY KEY (`room_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `room_type` (`room_type`, `price`, `max_person`, `description`, `amenities`) VALUES
('Single', 1000, 1, 'Habitación individual para viajeros solos.', 'WiFi, TV, Aire Acondicionado, Baño privado'),
('Double', 1500, 2, 'Habitación doble ideal para parejas.', 'WiFi, TV, Aire Acondicionado, Baño privado, Minibar'),
('Triple', 2000, 3, 'Habitación triple para grupos pequeños.', 'WiFi, TV, Aire Acondicionado, Baño privado'),
('Family', 3000, 4, 'Habitación familiar para familias de hasta 4 personas.', 'WiFi, TV, Aire Acondicionado, Baño privado, Sala de estar'),
('King Sized', 5500, 4, 'Suite premium con cama king size y jacuzzi.', 'WiFi, TV, Aire Acondicionado, Baño privado, Jacuzzi, Minibar'),
('Master Suite', 6500, 6, 'La suite más amplia del hotel.', 'WiFi, TV, Aire Acondicionado, Baño privado, Sala, Comedor, Jacuzzi'),
('Mini-Suite', 3600, 3, 'Mini-suite con sala de estar separada.', 'WiFi, TV, Aire Acondicionado, Baño privado, Sala de estar'),
('Connecting Rooms', 8000, 6, 'Dos habitaciones comunicadas con puerta interior.', 'WiFi, TV, Aire Acondicionado, 2 Baños privados'),
('Presidential Suite', 21000, 4, 'Suite presidencial con acabados de lujo.', 'WiFi, TV, Aire Acondicionado, Baño privado, Sala, Comedor, Jacuzzi privado, Vista panorámica'),
('Murphy Room', 6900, 3, 'Habitación con cama abatible Murphy.', 'WiFi, TV, Aire Acondicionado, Baño privado, Diseño moderno');

-- -------------------------------------------
-- Tabla: room (Habitaciones individuales)
-- -------------------------------------------
CREATE TABLE IF NOT EXISTS `room` (
  `room_id` int(11) NOT NULL AUTO_INCREMENT,
  `room_type_id` int(11) NOT NULL,
  `room_no` varchar(20) NOT NULL,
  `status` int(11) DEFAULT NULL COMMENT 'NULL=disponible, 1=reservada',
  `deleteStatus` int(11) NOT NULL DEFAULT 0 COMMENT '0=activa, 1=eliminada',
  `check_in_status` int(11) NOT NULL DEFAULT 0,
  `check_out_status` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`room_id`),
  KEY `fk_room_type` (`room_type_id`),
  CONSTRAINT `fk_room_type` FOREIGN KEY (`room_type_id`) REFERENCES `room_type`(`room_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Habitaciones de ejemplo
INSERT INTO `room` (`room_type_id`, `room_no`, `status`, `deleteStatus`) VALUES
(1, '101', NULL, 0), (1, '102', NULL, 0), (1, '103', NULL, 0), (1, '104', NULL, 0), (1, '105', NULL, 0),
(2, '201', NULL, 0), (2, '202', NULL, 0), (2, '203', NULL, 0), (2, '204', NULL, 0),
(2, '205', NULL, 0), (2, '206', NULL, 0), (2, '207', NULL, 0), (2, '208', NULL, 0),
(3, '301', NULL, 0), (3, '302', NULL, 0), (3, '303', NULL, 0), (3, '304', NULL, 0),
(4, '401', NULL, 0), (4, '402', NULL, 0), (4, '403', NULL, 0),
(5, '501', NULL, 0), (5, '502', NULL, 0),
(6, '601', NULL, 0), (6, '602', NULL, 0),
(7, '701', NULL, 0), (7, '702', NULL, 0), (7, '703', NULL, 0),
(8, '801', NULL, 0), (8, '802', NULL, 0),
(9, '901', NULL, 0),
(10, '1001', NULL, 0), (10, '1002', NULL, 0);

-- -------------------------------------------
-- Tabla: customer (Clientes)
-- -------------------------------------------
CREATE TABLE IF NOT EXISTS `customer` (
  `customer_id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_name` varchar(200) NOT NULL,
  `contact_no` varchar(20) NOT NULL,
  `email` varchar(150) DEFAULT NULL,
  `id_card_type_id` int(11) NOT NULL DEFAULT 1,
  `id_card_no` varchar(50) DEFAULT NULL,
  `address` text,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`customer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -------------------------------------------
-- Tabla: booking (Reservas)
-- -------------------------------------------
CREATE TABLE IF NOT EXISTS `booking` (
  `booking_id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `check_in` date NOT NULL,
  `check_out` date NOT NULL,
  `total_price` decimal(10,2) NOT NULL DEFAULT 0,
  `remaining_price` decimal(10,2) NOT NULL DEFAULT 0,
  `payment_status` int(11) NOT NULL DEFAULT 0 COMMENT '0=pendiente, 1=pagado',
  `invoice_no` varchar(20) DEFAULT NULL,
  `booking_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `notes` text,
  PRIMARY KEY (`booking_id`),
  KEY `fk_booking_customer` (`customer_id`),
  KEY `fk_booking_room` (`room_id`),
  CONSTRAINT `fk_booking_customer` FOREIGN KEY (`customer_id`) REFERENCES `customer`(`customer_id`),
  CONSTRAINT `fk_booking_room` FOREIGN KEY (`room_id`) REFERENCES `room`(`room_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -------------------------------------------
-- Tabla: payments (Pagos)
-- -------------------------------------------
CREATE TABLE IF NOT EXISTS `payments` (
  `payment_id` int(11) NOT NULL AUTO_INCREMENT,
  `booking_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(50) NOT NULL DEFAULT 'Efectivo',
  `payment_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `notes` text,
  PRIMARY KEY (`payment_id`),
  KEY `fk_payment_booking` (`booking_id`),
  CONSTRAINT `fk_payment_booking` FOREIGN KEY (`booking_id`) REFERENCES `booking`(`booking_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -------------------------------------------
-- Tabla: complaint (Quejas)
-- -------------------------------------------
CREATE TABLE IF NOT EXISTS `complaint` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `complainant_name` varchar(200) NOT NULL,
  `complaint_type` varchar(100) NOT NULL,
  `complaint` text NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `resolve_status` int(11) NOT NULL DEFAULT 0 COMMENT '0=pendiente, 1=resuelto',
  `resolve_date` datetime DEFAULT NULL,
  `budget` decimal(10,2) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -------------------------------------------
-- Tabla: blog (Entradas de blog)
-- -------------------------------------------
CREATE TABLE IF NOT EXISTS `blog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `excerpt` text,
  `content` longtext,
  `color` varchar(20) DEFAULT '#1a5276',
  `color2` varchar(20) DEFAULT '#2980b9',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_blog_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -------------------------------------------
-- Tabla: services (Servicios)
-- -------------------------------------------
CREATE TABLE IF NOT EXISTS `services` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `service_name` varchar(200) NOT NULL,
  `description` text,
  `icon` varchar(50) DEFAULT 'fa-star',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `services` (`service_name`, `description`, `icon`) VALUES
('WiFi Gratuito', 'Conexión de alta velocidad en todas las áreas del hotel.', 'fa-wifi'),
('Restaurante', 'Cocina internacional con los mejores chefes.', 'fa-utensils'),
('Piscina', 'Piscina climatizada con área de descanso.', 'fa-swimming-pool'),
('Gimnasio', 'Equipo moderno y profesional para ejercicios.', 'fa-dumbbell'),
('Estacionamiento', 'Estacionamiento vigilado las 24 horas.', 'fa-parking'),
('Room Service', 'Servicio a la habitación disponible 24 horas.', 'fa-concierge-bell');

-- -------------------------------------------
-- Tabla: site_settings (Configuración del sitio)
-- -------------------------------------------
CREATE TABLE IF NOT EXISTS `site_settings` (
  `key_name` varchar(100) NOT NULL,
  `key_value` text,
  PRIMARY KEY (`key_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `site_settings` (`key_name`, `key_value`) VALUES
('site_name', 'Hotel Paraíso'),
('site_email', 'info@hotelparaiso.com'),
('site_phone', '+1 234 567 890'),
('site_address', 'Av. Principal 123, Centro, Ciudad'),
('currency', '$'),
('currency_code', 'USD'),
('stripe_public_key', ''),
('stripe_secret_key', ''),
('stripe_webhook_secret', ''),
('paypal_client_id', ''),
('paypal_secret', ''),
('paypal_mode', 'sandbox'),
('mercadopago_public_key', ''),
('mercadopago_access_token', '');

-- -------------------------------------------
-- Tabla: contact_messages (Mensajes de contacto)
-- -------------------------------------------
CREATE TABLE IF NOT EXISTS `contact_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  `email` varchar(150) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `message` text NOT NULL,
  `is_read` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

SET FOREIGN_KEY_CHECKS = 1;
