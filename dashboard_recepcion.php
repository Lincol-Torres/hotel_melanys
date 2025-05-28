<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'recepcionista') {
    header('Location: login.php');
    exit;
}

// Eliminar reservas expiradas
$conn->query("DELETE FROM reservas WHERE fecha_expira < NOW()");

$sql = "
SELECT h.id AS id_habitacion, h.numero, h.estado, r.id AS id_reserva, u.nombre, r.fecha_reserva, r.fecha_expira
FROM habitaciones h
LEFT JOIN reservas r ON r.id_habitacion = h.id
LEFT JOIN usuarios u ON r.id_usuario = u.id
ORDER BY h.numero
";
$resultado = $conn->query($sql);
$habitaciones = $resultado->fetch_all(MYSQLI_ASSOC);

function getAlertClass($estado) {
    return match($estado) {
        'disponible' => 'success',
        'ocupada' => 'info',
        'mantenimiento' => 'warning',
        default => 'secondary',
    };
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recepción - Hotel Melanys</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', sans-serif;
        }
        .habitacion-card {
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
        }
        .disponible { background-color: #d4edda; border-left: 5px solid #28a745; }
        .ocupada { background-color: #f8d7da; border-left: 5px solid #dc3545; }
        .mantenimiento { background-color: #fff3cd; border-left: 5px solid #ffc107; }
        .card-title {
            font-weight: bold;
        }
        .estado-icon {
            font-size: 2rem;
            margin-right: 10px;
        }
        .disponible-icon { color: #28a745; }
        .ocupada-icon { color: #dc3545; }
        .mantenimiento-icon { color: #ffc107; }
    </style>
</head>
<body class="container py-4">
    <h2 class="mb-4">🛎️ Panel de Recepción</h2>

    <?php if (isset($_GET['success']) && isset($_GET['estado'])): ?>
        <div class="alert alert-<?= getAlertClass($_GET['estado']) ?> alert-dismissible fade show" role="alert">
            ✅ <?= htmlspecialchars($_GET['success']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
    <?php endif; ?>

    <div class="row row-cols-1 row-cols-md-2 g-4">
        <?php foreach ($habitaciones as $h): ?>
            <div class="col">
                <div class="habitacion-card shadow-sm <?= $h['estado'] ?>">
                    <h5 class="card-title">
                        <?php if ($h['estado'] === 'ocupada'): ?>
                            <span class="estado-icon ocupada-icon">🛏️</span>
                        <?php elseif ($h['estado'] === 'disponible'): ?>
                            <span class="estado-icon disponible-icon">✅</span>
                        <?php elseif ($h['estado'] === 'mantenimiento'): ?>
                            <span class="estado-icon mantenimiento-icon">🛠️</span>
                        <?php endif; ?>
                        Habitación <?= $h['numero'] ?> <span class="text-muted">(<?= ucfirst($h['estado']) ?>)</span>
                    </h5>

                    <?php if ($h['estado'] === 'ocupada' && $h['nombre']): ?>
                        <p class="mb-1">👤 <strong>Cliente:</strong> <?= htmlspecialchars($h['nombre']) ?></p>
                        <p class="mb-1">🕒 <strong>Desde:</strong> <?= $h['fecha_reserva'] ?></p>
                        <p class="mb-2">⏳ <strong>Hasta:</strong> <?= $h['fecha_expira'] ?></p>
                        <form method="POST" action="cancelar.php" class="d-inline">
                            <input type="hidden" name="id_reserva" value="<?= $h['id_reserva'] ?>">
                            <button class="btn btn-outline-danger btn-sm">Cancelar Reserva</button>
                        </form>
                    <?php endif; ?>

                    <form method="POST" action="actualizar_estado.php" class="d-flex align-items-center mt-2">
                        <input type="hidden" name="id_habitacion" value="<?= $h['id_habitacion'] ?>">
                        <select name="nuevo_estado" class="form-select me-2" style="width: auto;">
                            <option value="disponible" <?= $h['estado'] === 'disponible' ? 'selected' : '' ?>>Disponible</option>
                            <option value="ocupada" <?= $h['estado'] === 'ocupada' ? 'selected' : '' ?>>Ocupada</option>
                            <option value="mantenimiento" <?= $h['estado'] === 'mantenimiento' ? 'selected' : '' ?>>Mantenimiento</option>
                        </select>
                        <button class="btn btn-primary btn-sm">Cambiar estado</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="text-center mt-4">
        <a href="logout.php" class="btn btn-secondary">Cerrar sesión</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>