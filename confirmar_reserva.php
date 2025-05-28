<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'cliente') {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['habitacion'])) {
    $id_usuario = $_SESSION['usuario_id'];
    $numero_habitacion = intval($_POST['habitacion']);

    // Obtener ID y estado de la habitación
    $stmt = $conn->prepare("SELECT id, estado FROM habitaciones WHERE numero = ?");
    $stmt->bind_param("i", $numero_habitacion);
    $stmt->execute();
    $stmt->bind_result($id_habitacion, $estado);
    $stmt->fetch();
    $stmt->close();

    if ($estado !== 'disponible') {
        unset($_SESSION['preseleccion']);
        header('Location: dashboard_cliente.php');
        exit;
    }

    // Fechas de reserva y expiración (12 horas) — cambia a "+1 minute" para pruebas
    $fecha_reserva = date("Y-m-d H:i:s");
    $fecha_expira = date("Y-m-d H:i:s", strtotime("+12 hours"));

    // Insertar reserva
    $stmt = $conn->prepare("INSERT INTO reservas (id_usuario, id_habitacion, fecha_reserva, fecha_expira) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiss", $id_usuario, $id_habitacion, $fecha_reserva, $fecha_expira);
    $stmt->execute();

    // Marcar la habitación como ocupada
    $stmt = $conn->prepare("UPDATE habitaciones SET estado = 'reservada' WHERE id = ?");
    $stmt->bind_param("i", $id_habitacion);
    $stmt->execute();

    // Guardar fecha de expiración en sesión
    $_SESSION['reserva_expira'] = $fecha_expira;

    unset($_SESSION['preseleccion']);
}

header('Location: dashboard_cliente.php');
exit;