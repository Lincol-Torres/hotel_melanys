<?php
session_start();
require_once 'db.php';

// Solo administrador puede acceder
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'administrador') {
    header('Location: login.php');
    exit;
}

$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $correo = trim($_POST['correo']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $pregunta = trim($_POST['pregunta_secreta']);
    $respuesta = password_hash(trim($_POST['respuesta_secreta']), PASSWORD_DEFAULT);
    $rol = 'recepcionista';

    // Verificar si el correo ya existe
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
            $mensaje = "✅ Recepcionista registrado correctamente.";
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
    <title>Registrar Recepcionista</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f5f0eb;
            font-family: 'Segoe UI', sans-serif;
        }
        .form-box {
            max-width: 600px;
            margin: 40px auto;
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        h2 {
            color: #4B2E1B;
            margin-bottom: 20px;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="form-box">
    <h2><i class="bi bi-person-plus-fill"></i> Registrar Recepcionista</h2>

    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Nombre completo:</label>
            <input type="text" name="nombre" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Correo electrónico:</label>
            <input type="email" name="correo" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Contraseña:</label>
            <input type="password" name="password" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Pregunta secreta:</label>
            <input type="text" name="pregunta_secreta" class="form-control" placeholder="Ej. ¿Nombre de tu mascota?" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Respuesta secreta:</label>
            <input type="text" name="respuesta_secreta" class="form-control" required>
        </div>

        <div class="d-grid mt-4">
            <button type="submit" class="btn btn-success">Registrar Recepcionista</button>
        </div>
    </form>

    <div class="text-center mt-3">
        <a href="admin_panel.php" class="btn btn-link">← Volver al panel</a>
    </div>

    <?php if ($mensaje): ?>
        <div class="alert alert-info mt-3 text-center"><?php echo $mensaje; ?></div>
    <?php endif; ?>
</div>

</body>
</html>