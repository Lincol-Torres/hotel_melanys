<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Hotel Melanys</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f5f0eb;
            text-align: center;
            padding-top: 60px;
            font-family: 'Segoe UI', sans-serif;
        }
        .logo {
            max-width: 180px;
            margin-bottom: 20px;
        }
        .bienvenida {
            max-width: 600px;
            margin: auto;
        }
        .btn-custom {
            background-color: #4B2E1B;
            color: white;
            border: none;
        }
        .btn-custom:hover {
            background-color: #3a2314;
        }
    </style>
</head>
<body>
    <div class="container bienvenida">
        <img src="assets/images/logo.png" alt="Hotel Melanys" class="logo">
        <h1 class="mb-3" style="color: #4B2E1B;">Bienvenido al Hotel Melanys</h1>
        <p class="mb-4">Reserva tu habitación en línea o gestiona como personal autorizado.</p>

        <div class="d-grid gap-3 col-8 col-md-6 mx-auto">
            <a href="login.php" class="btn btn-custom btn-lg">Iniciar Sesión</a>
            <a href="registro.php" class="btn btn-outline-secondary btn-lg">Registrarse</a>

            <?php if (!isset($_SESSION['usuario_id'])): ?>
                <a href="admin_login.php" class="btn btn-outline-dark btn-lg">Acceso Administrador</a>
            <?php endif; ?>

            <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'administrador'): ?>
                <a href="admin_panel.php" class="btn btn-dark btn-lg">Panel de Administración</a>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>