<?php
// Zona horaria local (Perú)
date_default_timezone_set('America/Lima');

// Conexión a MySQL
$host = 'localhost';
$usuario = 'root';
$password = '';
$base_datos = 'hotel_melanys_db';

$conn = new mysqli($host, $usuario, $password, $base_datos);

// Verificar conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Opcional: forzar charset UTF-8
$conn->set_charset("utf8");