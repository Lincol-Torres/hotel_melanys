<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'cliente') {
    header('Location: login.php');
    exit;
}

if (isset($_GET['id'])) {
    $reserva_id = intval($_GET['id']);
    $id_usuario = $_SESSION['usuario_id'];

    $stmt = $conn->prepare("SELECT id_habitacion FROM reservas WHERE id = ? AND id_usuario = ?");
    $stmt->bind_param("ii", $reserva_id, $id_usuario);
    $stmt->execute();
    $stmt->bind_result($id_habitacion);
    $stmt->fetch();
    $stmt->close();

    if ($id_habitacion) {
        $conn->query("DELETE FROM reservas WHERE id = $reserva_id");
        $conn->query("UPDATE habitaciones SET estado = 'disponible' WHERE id = $id_habitacion");

        $_SESSION['mensaje'] = "Reserva cancelada correctamente.";
    }
}

header('Location: dashboard_cliente.php');
exit;