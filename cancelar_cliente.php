<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'cliente') {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_reserva'])) {
    $id_reserva = intval($_POST['id_reserva']);
    $id_usuario = $_SESSION['usuario_id'];

    // Obtener la habitación de la reserva
    $stmt = $conn->prepare("SELECT id_habitacion FROM reservas WHERE id = ? AND id_usuario = ?");
    $stmt->bind_param("ii", $id_reserva, $id_usuario);
    $stmt->execute();
    $stmt->bind_result($id_habitacion);

    if ($stmt->fetch()) {
        $stmt->close();  // ✅ CERRAR antes de ejecutar otro query

        // Eliminar reserva
        $conn->query("DELETE FROM reservas WHERE id = $id_reserva");

        // Liberar habitación
        $conn->query("UPDATE habitaciones SET estado = 'disponible' WHERE id = $id_habitacion");
    } else {
        $stmt->close();
    }
}

header("Location: dashboard_cliente.php");
exit;