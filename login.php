<?php
session_start();
require_once 'db.php';

$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = trim($_POST['correo']);
    $password = $_POST['password'];
    $rol_ingresado = $_POST['rol'];

    $stmt = $conn->prepare("SELECT id, nombre, password, rol FROM usuarios WHERE correo = ?");
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($id, $nombre, $hash, $rol_bd);
        $stmt->fetch();

        if ($rol_bd !== $rol_ingresado) {
            $mensaje = "El usuario no corresponde al tipo seleccionado.";
        } elseif (password_verify($password, $hash)) {
            $_SESSION['usuario_id'] = $id;
            $_SESSION['nombre'] = $nombre;
            $_SESSION['rol'] = $rol_bd;

            if ($rol_bd === 'cliente') {
                header('Location: dashboard_cliente.php');
            } elseif ($rol_bd === 'recepcionista') {
                header('Location: dashboard_recepcion.php');
            }
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
    <title>Iniciar Sesión - Hotel Melanys</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f5f0eb;
            font-family: 'Segoe UI', sans-serif;
        }
        .login-box {
            max-width: 450px;
            margin: 50px auto;
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 0 12px rgba(0,0,0,0.1);
        }
        h2 {
            color: #4B2E1B;
        }
        .btn-login {
            background-color: #4B2E1B;
            color: white;
        }
        .btn-login:hover {
            background-color: #3a2314;
        }
    </style>
</head>
<body>

<div class="login-box">
    <div class="text-center mb-4">
        <img src="assets/images/logo.png" alt="Hotel Melanys" style="max-height: 80px;">
        <h2 class="mt-2">Iniciar Sesión</h2>
    </div>

    <form method="POST">
        <div class="mb-3">
            <label for="rol" class="form-label">Tipo de usuario:</label>
            <select name="rol" id="rol" class="form-select" required>
                <option value="cliente">Cliente</option>
                <option value="recepcionista">Recepcionista</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="correo" class="form-label">Correo electrónico:</label>
            <input type="email" name="correo" id="correo" class="form-control" placeholder="correo@ejemplo.com" required>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Contraseña:</label>
            <input type="password" name="password" id="password" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-login w-100">Ingresar</button>
    </form>

    <div class="text-center mt-3">
        <a href="registro.php" class="btn btn-link">Registrarse</a> |
        <a href="recuperar.php" class="btn btn-link">¿Olvidaste tu contraseña?</a>
        <br>
        <a href="index.php" class="btn btn-sm btn-outline-secondary mt-3">← Volver al inicio</a>
    </div>

    <?php if ($mensaje): ?>
        <div class="alert alert-danger mt-3 text-center"><?php echo $mensaje; ?></div>
    <?php endif; ?>
</div>

</body>
</html>