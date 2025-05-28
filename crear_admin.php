<?php
require_once 'db.php';

$nombre = 'Dueño del Hotel';
$correo = 'admin@melanys.com';
$passwordPlano = 'admin123';
$passwordHash = password_hash($passwordPlano, PASSWORD_DEFAULT);
$rol = 'administrador';
$pregunta = '¿Nombre de tu primer hotel?';
$respuesta = password_hash('melany', PASSWORD_DEFAULT);

$stmt = $conn->prepare("INSERT INTO usuarios (nombre, correo, password, rol, pregunta_secreta, respuesta_secreta) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssss", $nombre, $correo, $passwordHash, $rol, $pregunta, $respuesta);

if ($stmt->execute()) {
    echo "✅ Administrador creado correctamente.";
} else {
    echo "❌ Error: " . $stmt->error;
}