-- Crear base de datos
CREATE DATABASE IF NOT EXISTS hotel_melanys_db;
USE hotel_melanys_db;

-- Tabla de usuarios (rol incluye 'administrador')
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    correo VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    rol ENUM('cliente', 'recepcionista', 'administrador') NOT NULL DEFAULT 'cliente',
    token_recuperacion VARCHAR(255),
    pregunta_secreta VARCHAR(255),
    respuesta_secreta VARCHAR(255),
    actualizado TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabla de habitaciones (20 habitaciones fijas)
CREATE TABLE habitaciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    numero INT UNIQUE NOT NULL,
    estado ENUM('disponible', 'ocupada', 'mantenimiento') NOT NULL DEFAULT 'disponible'
);

-- Tabla de reservas
CREATE TABLE reservas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    id_habitacion INT NOT NULL,
    fecha_reserva DATETIME NOT NULL,
    fecha_expira DATETIME NOT NULL,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (id_habitacion) REFERENCES habitaciones(id) ON DELETE CASCADE
);

-- Insertar las 20 habitaciones numeradas del 1 al 20
INSERT INTO habitaciones (numero) VALUES
(1), (2), (3), (4), (5),
(6), (7), (8), (9), (10),
(11), (12), (13), (14), (15),
(16), (17), (18), (19), (20);

ALTER TABLE habitaciones MODIFY estado ENUM('disponible', 'reservada', 'ocupada', 'mantenimiento') DEFAULT 'disponible';

-- Insertar cuenta de administrador
-- INSERT INTO usuarios (nombre, correo, password, rol, pregunta_secreta, respuesta_secreta)
-- VALUES (
--    'Dueño del Hotel',
--    'admin@melanys.com',
--    '$2y$10$nmRhk8xK7zrP0BxU1Z1GAOMDb3ow7TIXrJh5DL1VmYmFtowr8ZK2e', -- password: admin123
--    'administrador',
--    '¿Nombre de tu primer hotel?',
--    '$2y$10$Yx3cYjkEKdzEhlDGB0/VMeRbyGWe5OwDrm7j4spEQ/NKAX/8hMeF6' -- respuesta: melany
-- );

-- SELECT @@global.time_zone, @@session.time_zone;

-- SET GLOBAL time_zone = '-05:00'; -- Para Perú