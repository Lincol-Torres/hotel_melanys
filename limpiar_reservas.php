<?php
require_once 'db.php';

// 1. Buscar todas las reservas vencidas
$reservas_vencidas = $conn->query("
    SELECT id, id_habitacion
    FROM reservas
    WHERE fecha_expira < NOW()
");

while ($reserva = $reservas_vencidas->fetch_assoc()) {
    $id_reserva = $reserva['id'];
    $id_habitacion = $reserva['id_habitacion'];

    // 2. Eliminar la reserva
    $conn->query("DELETE FROM reservas WHERE id = $id_reserva");

    // 3. Liberar la habitación
    $conn->query("UPDATE habitaciones SET estado = 'disponible' WHERE id = $id_habitacion");
}