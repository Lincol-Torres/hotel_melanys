<?php
session_start();
require_once 'db.php';

$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $correo = trim($_POST['correo']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $rol = 'cliente'; // ✅ FORZAMOS el rol a cliente
    $pregunta = trim($_POST['pregunta_secreta']);
    $respuesta = password_hash(trim($_POST['respuesta_secreta']), PASSWORD_DEFAULT);

    $check = $conn->prepare("SELECT id FROM usuarios WHERE correo = ?");
    $check->bind_param("s", $correo);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $mensaje = "El correo ya está registrado.";
    } else {
        $stmt = $conn->prepare("INSERT INTO usuarios (nombre, correo, password, rol, pregunta_secreta, respuesta_secreta) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $nombre, $correo, $password, $rol, $pregunta, $respuesta);
        if ($stmt->execute()) {
            $mensaje = "✅ Registro exitoso. Ahora puedes iniciar sesión.";
        } else {
            $mensaje = "❌ Error al registrar: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro - Hotel Melanys</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f5f0eb;
            font-family: 'Segoe UI', sans-serif;
        }
        .card {
            max-width: 600px;
            margin: auto;
            margin-top: 30px;
            padding: 30px;
            border: none;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        .btn-primary {
            background-color: #4B2E1B;
            border-color: #4B2E1B;
        }
        .btn-primary:hover {
            background-color: #3a2314;
        }
        .form-label {
            font-weight: 600;
            color: #4B2E1B;
        }
        .logo {
            max-height: 80px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

    <div class="card">
        <div class="text-center">
            <img src="assets/images/logo.png" alt="Hotel Melanys" class="logo">
            <h3 class="mb-4 text-uppercase" style="color:#4B2E1B;">Registro de Cuenta</h3>
        </div>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Nombre completo:</label>
                <input type="text" name="nombre" class="form-control" placeholder="Ej. Juan Pérez" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Correo electrónico:</label>
                <input type="email" name="correo" class="form-control" placeholder="correo@ejemplo.com" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Contraseña:</label>
                <input type="password" name="password" class="form-control" placeholder="••••••••" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Pregunta secreta:</label>
                <input type="text" name="pregunta_secreta" class="form-control" placeholder="Ej. ¿Nombre de tu mascota?" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Respuesta secreta:</label>
                <input type="text" name="respuesta_secreta" class="form-control" required>
            </div>

            <!-- Ocultamos el campo de rol -->
            <input type="hidden" name="rol" value="cliente">

            <div class="d-grid mt-4">
                <button type="submit" class="btn btn-primary">Crear Cuenta</button>
            </div>

            <div class="text-center mt-3">
                <a href="login.php" class="btn btn-link">¿Ya tienes una cuenta? Inicia sesión</a>
                <br>
                <a href="javascript:history.back()" class="btn btn-sm btn-outline-secondary mt-2">← Volver</a>
            </div>

            <?php if ($mensaje): ?>
                <div class="alert alert-info text-center mt-3"><?php echo $mensaje; ?></div>
            <?php endif; ?>
        </form>
    </div>

</body>
</html>