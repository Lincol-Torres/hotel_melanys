<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'recepcionista') {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_reserva'])) {
    $id_reserva = intval($_POST['id_reserva']);

    // Obtener la habitación asociada a la reserva
    $stmt = $conn->prepare("SELECT id_habitacion FROM reservas WHERE id = ?");
    $stmt->bind_param("i", $id_reserva);
    $stmt->execute();
    $stmt->bind_result($id_habitacion);
    $stmt->fetch();
    $stmt->close();

    // Eliminar la reserva
    $conn->query("DELETE FROM reservas WHERE id = $id_reserva");

    // Cambiar estado de la habitación a disponible
    $conn->query("UPDATE habitaciones SET estado = 'disponible' WHERE id = $id_habitacion");
}

header("Location: dashboard_recepcion.php");
exit;