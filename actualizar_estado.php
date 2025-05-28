<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'recepcionista') {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id_habitacion']);
    $nuevo_estado = $_POST['nuevo_estado'];

    // Actualizar estado de habitación
    $conn->query("UPDATE habitaciones SET estado = '$nuevo_estado' WHERE id = $id");

    // Si pasa a disponible, eliminar reservas asociadas
    if ($nuevo_estado === 'disponible') {
        $conn->query("DELETE FROM reservas WHERE id_habitacion = $id");
    }

    // Redirigir con mensaje y tipo de estado
    header("Location: dashboard_recepcion.php?success=Habitación%20$id%20actualizada%20a%20$nuevo_estado&estado=$nuevo_estado");
    exit;
}