<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'cliente') {
    header('Location: login.php');
    exit;
}

$id = $_SESSION['usuario_id'];
$mensaje = '';

// Actualizar nombre/correo
if (isset($_POST['update_perfil'])) {
    $nuevo_nombre = trim($_POST['nombre']);
    $nuevo_correo = trim($_POST['correo']);

    // Verificar si el correo ya existe para otro usuario
    $verificar = $conn->prepare("SELECT id FROM usuarios WHERE correo = ? AND id != ?");
    $verificar->bind_param("si", $nuevo_correo, $id);
    $verificar->execute();
    $verificar->store_result();

    if ($verificar->num_rows > 0) {
        $mensaje = "❌ El correo ingresado ya está en uso.";
    } else {
        $update = $conn->prepare("UPDATE usuarios SET nombre = ?, correo = ? WHERE id = ?");
        $update->bind_param("ssi", $nuevo_nombre, $nuevo_correo, $id);
        $update->execute();
        $_SESSION['nombre'] = $nuevo_nombre;
        $mensaje = "✅ Datos actualizados correctamente.";
    }
}

// Cambiar contraseña
if (isset($_POST['update_password'])) {
    $actual = $_POST['actual'];
    $nueva = $_POST['nueva'];
    $confirmar = $_POST['confirmar'];

    $stmt = $conn->prepare("SELECT password FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($hash);
    $stmt->fetch();
    $stmt->close();

    if (!password_verify($actual, $hash)) {
        $mensaje = "❌ La contraseña actual es incorrecta.";
    } elseif ($nueva !== $confirmar) {
        $mensaje = "❌ Las nuevas contraseñas no coinciden.";
    } else {
        $nuevoHash = password_hash($nueva, PASSWORD_DEFAULT);
        $update = $conn->prepare("UPDATE usuarios SET password = ? WHERE id = ?");
        $update->bind_param("si", $nuevoHash, $id);
        $update->execute();
        $mensaje = "✅ Contraseña actualizada correctamente.";
    }
}

// Obtener datos del usuario actualizados
$stmt = $conn->prepare("SELECT nombre, correo FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($nombre, $correo);
$stmt->fetch();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mi cuenta</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f5f0eb;
            font-family: 'Segoe UI', sans-serif;
        }
        .box {
            max-width: 550px;
            margin: 40px auto;
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h2 {
            color: #4B2E1B;
        }
    </style>
</head>
<body>

<div class="box">
    <h2 class="text-center mb-4">Mi cuenta</h2>

    <!-- Formulario para actualizar perfil -->
    <form method="POST">
        <h5 class="mb-3">Información de perfil</h5>
        <div class="mb-3">
            <label>Nombre:</label>
            <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($nombre) ?>" required>
        </div>
        <div class="mb-3">
            <label>Correo:</label>
            <input type="email" name="correo" class="form-control" value="<?= htmlspecialchars($correo) ?>" required>
        </div>
        <button type="submit" name="update_perfil" class="btn btn-primary w-100 mb-3">Actualizar perfil</button>
    </form>

    <hr>

    <!-- Formulario para cambiar contraseña -->
    <form method="POST">
        <h5 class="mb-3">Cambiar contraseña</h5>
        <div class="mb-3">
            <label>Contraseña actual:</label>
            <input type="password" name="actual" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Nueva contraseña:</label>
            <input type="password" name="nueva" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Confirmar nueva contraseña:</label>
            <input type="password" name="confirmar" class="form-control" required>
        </div>
        <button type="submit" name="update_password" class="btn btn-success w-100">Actualizar contraseña</button>
    </form>

    <?php if ($mensaje): ?>
        <div class="alert alert-info text-center mt-3"><?= $mensaje ?></div>
    <?php endif; ?>

    <div class="text-center mt-4">
        <a href="dashboard_cliente.php" class="btn btn-outline-secondary">← Volver al panel</a>
    </div>
</div>

</body>
</html>