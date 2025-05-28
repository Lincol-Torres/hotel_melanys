<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'cliente') {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_habitacion'])) {
    $id_habitacion = intval($_POST['id_habitacion']);
    $id_usuario = $_SESSION['usuario_id'];

    // Verificar si la habitación está disponible
    $stmt = $conn->prepare("SELECT estado FROM habitaciones WHERE id = ?");
    $stmt->bind_param("i", $id_habitacion);
    $stmt->execute();
    $stmt->bind_result($estado);
    $stmt->fetch();
    $stmt->close();

    if ($estado === 'disponible') {
        $fecha_reserva = date('Y-m-d H:i:s');
        $fecha_expira = date('Y-m-d H:i:s', strtotime('+12 hours'));

        // Insertar reserva
        $stmt = $conn->prepare("INSERT INTO reservas (id_usuario, id_habitacion, fecha_reserva, fecha_expira) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiss", $id_usuario, $id_habitacion, $fecha_reserva, $fecha_expira);
        $stmt->execute();

        // Marcar habitación como ocupada
        $conn->query("UPDATE habitaciones SET estado = 'ocupada' WHERE id = $id_habitacion");
    }
}

header("Location: dashboard_cliente.php");
exit;