<?php
session_start();
require_once 'db.php';

// Verificar si es administrador
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: login.php");
    exit;
}

// Consultar todas las reservas con usuarios y habitaciones
$sql = "
    SELECT r.id, u.nombre AS cliente, h.numero AS habitacion, r.fecha_reserva, r.fecha_expira
    FROM reservas r
    INNER JOIN usuarios u ON r.id_usuario = u.id
    INNER JOIN habitaciones h ON r.id_habitacion = h.id
    ORDER BY r.fecha_reserva DESC
";
$reservas = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Historial de Reservas - Hotel Melanys</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f5f0eb;
            font-family: 'Segoe UI', sans-serif;
        }
        .container {
            margin-top: 40px;
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h2 {
            color: #4B2E1B;
            margin-bottom: 20px;
        }
        table th {
            background-color: #4B2E1B;
            color: white;
        }
    </style>
</head>
<body>

<div class="container">
    <h2><i class="bi bi-calendar3"></i> Historial de Reservas</h2>

    <table class="table table-bordered table-hover align-middle">
        <thead>
            <tr>
                <th>ID</th>
                <th>Cliente</th>
                <th>Habitación</th>
                <th>Fecha de Reserva</th>
                <th>Expira en</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($reservas->num_rows > 0): ?>
                <?php while ($r = $reservas->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $r['id']; ?></td>
                        <td><?php echo htmlspecialchars($r['cliente']); ?></td>
                        <td>Habitación <?php echo $r['habitacion']; ?></td>
                        <td><?php echo $r['fecha_reserva']; ?></td>
                        <td><?php echo $r['fecha_expira']; ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="5" class="text-center">No hay reservas registradas.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="text-center mt-3">
        <a href="admin_panel.php" class="btn btn-secondary">← Volver al panel</a>
    </div>
</div>

</body>
</html>