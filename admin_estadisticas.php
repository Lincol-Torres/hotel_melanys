<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'administrador') {
    header('Location: login.php');
    exit;
}

// 📅 Reservas por día (últimos 7 días)
$reservas_dia = $conn->query("
    SELECT DATE(fecha_reserva) AS dia, COUNT(*) AS total
    FROM reservas
    GROUP BY dia
    ORDER BY dia DESC
    LIMIT 7
");

$labels_dia = [];
$data_dia = [];

while ($row = $reservas_dia->fetch_assoc()) {
    $labels_dia[] = $row['dia'];
    $data_dia[] = $row['total'];
}

// 🏨 Habitaciones más reservadas
$habitaciones = $conn->query("
    SELECT h.numero AS habitacion, COUNT(*) AS total
    FROM reservas r
    JOIN habitaciones h ON r.id_habitacion = h.id
    GROUP BY h.numero
    ORDER BY total DESC
    LIMIT 5
");

$labels_habitacion = [];
$data_habitacion = [];

while ($row = $habitaciones->fetch_assoc()) {
    $labels_habitacion[] = "Hab " . $row['habitacion'];
    $data_habitacion[] = $row['total'];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Estadísticas - Hotel Melanys</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
            margin-bottom: 30px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2><i class="bi bi-bar-chart-line-fill"></i> Estadísticas del Sistema</h2>

    <div class="mb-5">
        <h5>📅 Reservas por día</h5>
        <canvas id="graficoReservasDia"></canvas>
    </div>

    <div class="mb-5">
        <h5>🏨 Habitaciones más reservadas</h5>
        <canvas id="graficoHabitaciones"></canvas>
    </div>

    <div class="text-center">
        <a href="admin_panel.php" class="btn btn-secondary">← Volver al panel</a>
    </div>
</div>

<script>
const ctx1 = document.getElementById('graficoReservasDia');
const graficoReservasDia = new Chart(ctx1, {
    type: 'bar',
    data: {
        labels: <?= json_encode(array_reverse($labels_dia)) ?>,
        datasets: [{
            label: 'Reservas',
            data: <?= json_encode(array_reverse($data_dia)) ?>,
            backgroundColor: '#4B2E1B'
        }]
    }
});

const ctx2 = document.getElementById('graficoHabitaciones');
const graficoHabitaciones = new Chart(ctx2, {
    type: 'pie',
    data: {
        labels: <?= json_encode($labels_habitacion) ?>,
        datasets: [{
            label: 'Veces reservada',
            data: <?= json_encode($data_habitacion) ?>,
            backgroundColor: ['#4B2E1B', '#6F4E37', '#A0522D', '#D2691E', '#C19A6B']
        }]
    }
});
</script>

</body>
</html>