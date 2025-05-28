<?php
session_start();
require_once 'db.php';
require_once 'limpiar_reservas.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'cliente') {
    header('Location: login.php');
    exit;
}

$habitaciones = $conn->query("SELECT * FROM habitaciones ORDER BY numero ASC");
$preseleccion = $_SESSION['preseleccion'] ?? null;
$nombre = $_SESSION['nombre'] ?? 'Cliente';

$reservas = $conn->query("SELECT r.*, h.numero FROM reservas r JOIN habitaciones h ON r.id_habitacion = h.id WHERE r.id_usuario = {$_SESSION['usuario_id']} ORDER BY r.fecha_expira ASC");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel Cliente - Hotel Melanys</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f5f0eb;
            font-family: 'Segoe UI', sans-serif;
        }
        .card.seleccionada {
            border-width: 3px !important;
            border-color: #4B2E1B !important;
        }
    </style>
</head>
<body>

<div class="container py-4">
    <?php if (isset($_SESSION['mensaje'])): ?>
        <div class="alert alert-success text-center">
            <?= $_SESSION['mensaje'] ?>
        </div>
        <?php unset($_SESSION['mensaje']); ?>
    <?php endif; ?>

    <div class="text-center mb-4">
        <h2>Bienvenido, <?= htmlspecialchars($nombre) ?> 👋</h2>
        <p>Selecciona una habitación disponible para tu reserva</p>
    </div>

    <?php while ($res = $reservas->fetch_assoc()): ?>
        <?php
            $estado_actual = $conn->query("SELECT estado FROM habitaciones WHERE id = {$res['id_habitacion']}")->fetch_assoc()['estado'];
            if ($estado_actual === 'ocupada') continue;
        ?>
        <div class="alert alert-warning text-center">
            🛫 <strong>Habitación <?= $res['numero'] ?></strong> &mdash; expira en:
            <span id="contador<?= $res['id'] ?>" style="font-weight: bold;"></span>
            <br>
            <a href="cancelar_individual.php?id=<?= $res['id'] ?>" 
               class="btn btn-outline-danger btn-sm mt-2"
               onclick="return confirm('¿Seguro que deseas cancelar esta reserva?')">
               Cancelar esta reserva
            </a>
        </div>
        <script>
            const expira<?= $res['id'] ?> = new Date("<?= $res['fecha_expira'] ?>").getTime();
            const timer<?= $res['id'] ?> = setInterval(() => {
                const now = new Date().getTime();
                const diff = expira<?= $res['id'] ?> - now;

                if (diff <= 0) {
                    clearInterval(timer<?= $res['id'] ?>);
                    document.getElementById("contador<?= $res['id'] ?>").innerHTML = "⚠️ Expirada";
                    setTimeout(() => window.location.reload(), 2000);
                    return;
                }

                const h = String(Math.floor((diff / (1000 * 60 * 60)) % 24)).padStart(2, '0');
                const m = String(Math.floor((diff / (1000 * 60)) % 60)).padStart(2, '0');
                const s = String(Math.floor((diff / 1000) % 60)).padStart(2, '0');

                document.getElementById("contador<?= $res['id'] ?>").innerHTML = `${h}:${m}:${s}`;
            }, 1000);
        </script>
    <?php endwhile; ?>

    <?php if ($preseleccion): ?>
        <div class="alert alert-info text-center">
            Has preseleccionado la habitación <strong><?= $preseleccion ?></strong>
            <form method="POST" action="confirmar_reserva.php" class="d-inline">
                <input type="hidden" name="habitacion" value="<?= $preseleccion ?>">
                <button type="submit" class="btn btn-success btn-sm ms-2">Confirmar reserva</button>
            </form>
            <form method="POST" action="desmarcar.php" class="d-inline">
                <button type="submit" class="btn btn-outline-danger btn-sm ms-2">Desmarcar</button>
            </form>
        </div>
    <?php endif; ?>

    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-4 g-4">
        <?php while ($h = $habitaciones->fetch_assoc()): ?>
            <?php
                $estado = $h['estado'];
                $numero = $h['numero'];
                $claseEstado = match ($estado) {
                    'disponible'    => 'border-success',
                    'reservada'     => 'border-primary bg-primary bg-opacity-10',
                    'ocupada'       => 'border-danger bg-danger bg-opacity-10',
                    'mantenimiento' => 'border-warning bg-warning bg-opacity-25',
                    default         => ''
                };
                $seleccionada = ($preseleccion == $numero) ? 'seleccionada' : '';
                $bloqueado = ($estado !== 'disponible');
            ?>
            <div class="col">
                <div class="card shadow-sm <?= $claseEstado ?> <?= $seleccionada ?>">
                    <div class="card-body text-center">
                        <h5 class="card-title">Habitación <?= $numero ?></h5>
                        <i class="bi bi-door-closed-fill fs-1 text-secondary"></i>
                        <p class="card-text text-muted">
                            <?= $estado === 'reservada' ? 'Reservada' : ucfirst($estado) ?>
                        </p>

                        <?php if (!$bloqueado): ?>
                            <form method="POST" action="preseleccionar.php">
                                <input type="hidden" name="habitacion" value="<?= $numero ?>">
                                <button type="submit" class="btn btn-outline-primary btn-sm">Seleccionar</button>
                            </form>
                        <?php else: ?>
                            <button class="btn btn-secondary btn-sm" disabled>
                                <?= $estado === 'reservada' ? 'Reservada' : 'Ocupada' ?>
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>

    <div class="text-center mt-4">
        <a href="logout.php" class="btn btn-secondary">Cerrar sesión</a>
        <a href="cuenta_cliente.php" class="btn btn-outline-dark ms-2">Mi cuenta</a>
    </div>
</div>

</body>
</html>