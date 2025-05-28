<?php
session_start();
require_once 'db.php';

$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = trim($_POST['correo']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, nombre, password, rol FROM usuarios WHERE correo = ?");
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($id, $nombre, $hash, $rol);
        $stmt->fetch();

        if ($rol !== 'administrador') {
            $mensaje = "Acceso denegado. No eres administrador.";
        } elseif (password_verify($password, $hash)) {
            $_SESSION['usuario_id'] = $id;
            $_SESSION['nombre'] = $nombre;
            $_SESSION['rol'] = $rol;
            header('Location: admin_panel.php');
            exit;
        } else {
            $mensaje = "Contraseña incorrecta.";
        }
    } else {
        $mensaje = "Correo no encontrado.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Admin Login - Hotel Melanys</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f5f0eb;
            text-align: center;
            padding-top: 50px;
            font-family: 'Segoe UI', sans-serif;
        }
        .login-box {
            max-width: 400px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        .btn-admin {
            background-color: #4B2E1B;
            color: white;
        }
        .btn-admin:hover {
            background-color: #3a2314;
        }
        .logo {
            max-height: 80px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="login-box">
        <img src="assets/images/logo.png" class="logo" alt="Hotel Melanys">
        <h3 class="mb-4" style="color:#4B2E1B;">Acceso Administrador</h3>

        <form method="POST">
            <div class="mb-3">
                <input type="email" name="correo" class="form-control" placeholder="Correo administrador" required>
            </div>
            <div class="mb-3">
                <input type="password" name="password" class="form-control" placeholder="Contraseña" required>
            </div>
            <button type="submit" class="btn btn-admin w-100">Ingresar</button>
            <a href="index.php" class="btn btn-link mt-3">← Volver al inicio</a>
        </form>

        <?php if ($mensaje): ?>
            <div class="alert alert-danger mt-3"><?php echo $mensaje; ?></div>
        <?php endif; ?>
    </div>
</body>
</html>