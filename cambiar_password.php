<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_usuario = $_SESSION['usuario_id'];
    $actual = $_POST['password_actual'];
    $nueva = $_POST['nueva_password'];
    $confirmar = $_POST['confirmar_password'];

    // Validar nueva y confirmar
    if ($nueva !== $confirmar) {
        $mensaje = "Las contraseñas no coinciden.";
    } else {
        // Obtener contraseña actual
        $stmt = $conn->prepare("SELECT password FROM usuarios WHERE id = ?");
        $stmt->bind_param("i", $id_usuario);
        $stmt->execute();
        $stmt->bind_result($hash_actual);
        $stmt->fetch();
        $stmt->close();

        if (password_verify($actual, $hash_actual)) {
            // Actualizar nueva contraseña
            $nueva_hash = password_hash($nueva, PASSWORD_DEFAULT);
            $update = $conn->prepare("UPDATE usuarios SET password = ? WHERE id = ?");
            $update->bind_param("si", $nueva_hash, $id_usuario);
            if ($update->execute()) {
                $mensaje = "Contraseña actualizada correctamente.";
            } else {
                $mensaje = "Error al actualizar contraseña.";
            }
        } else {
            $mensaje = "Contraseña actual incorrecta.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Cambiar Contraseña - Hotel Melanys</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
</head>
<body class="container mt-5">
    <h2>Cambiar Contraseña</h2>
    <form method="POST" action="">
        <div class="mb-3">
            <label>Contraseña actual:</label>
            <input type="password" name="password_actual" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Nueva contraseña:</label>
            <input type="password" name="nueva_password" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Confirmar nueva contraseña:</label>
            <input type="password" name="confirmar_password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Actualizar</button>
        <a href="dashboard_cliente.php" class="btn btn-secondary">Volver</a>
    </form>
    <p class="mt-3 text-danger"><?php echo $mensaje; ?></p>
</body>
</html>