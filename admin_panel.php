<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'administrador') {
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Administración - Hotel Melanys</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f5f0eb;
            font-family: 'Segoe UI', sans-serif;
        }
        .panel {
            max-width: 800px;
            margin: 50px auto;
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        h2 {
            color: #4B2E1B;
            text-align: center;
            margin-bottom: 30px;
        }
        .card-option {
            transition: transform 0.2s;
        }
        .card-option:hover {
            transform: scale(1.02);
        }
        .btn-logout {
            background-color: #6c757d;
            color: white;
        }
        .btn-logout:hover {
            background-color: #5a6268;
        }
    </style>
</head>
<body>

<div class="panel">
    <h2><i class="bi bi-gear-fill"></i> Panel de Administración</h2>

    <div class="row row-cols-1 row-cols-md-2 g-4">

        <div class="col">
            <div class="card card-option h-100 border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="bi bi-person-plus-fill fs-2 text-success mb-2"></i>
                    <h5 class="card-title">Crear Recepcionista</h5>
                    <p class="card-text">Agregar nuevos empleados al sistema.</p>
                    <a href="admin_crear_usuario.php" class="btn btn-success">Ir</a>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card card-option h-100 border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="bi bi-people-fill fs-2 text-primary mb-2"></i>
                    <h5 class="card-title">Gestionar Usuarios</h5>
                    <p class="card-text">Ver y modificar roles de cuentas registradas.</p>
                    <a href="admin_usuarios.php" class="btn btn-primary">Ir</a>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card card-option h-100 border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="bi bi-calendar3 fs-2 text-info mb-2"></i>
                    <h5 class="card-title">Historial de Reservas</h5>
                    <p class="card-text">Consulta completa de todas las reservas.</p>
                    <a href="admin_reservas.php" class="btn btn-info text-white">Ir</a>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card card-option h-100 border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="bi bi-bar-chart-line-fill fs-2 text-warning mb-2"></i>
                    <h5 class="card-title">Estadísticas</h5>
                    <p class="card-text">Datos visuales sobre el uso del sistema.</p>
                    <a href="admin_estadisticas.php" class="btn btn-warning">Ir</a>
                </div>
            </div>
        </div>

    </div>

    <div class="text-center mt-4">
        <a href="logout.php" class="btn btn-logout">Cerrar sesión</a>
    </div>
</div>

</body>
</html>