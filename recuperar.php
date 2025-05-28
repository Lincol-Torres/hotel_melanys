<?php
session_start();
require_once 'db.php';

$mensaje = '';
$correo = '';
$pregunta = '';
$mostrar_formulario_respuesta = false;

// Paso 1: usuario envía su correo
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['buscar_correo'])) {
    $correo = trim($_POST['correo']);

    $stmt = $conn->prepare("SELECT pregunta_secreta FROM usuarios WHERE correo = ?");
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($pregunta);
        $stmt->fetch();
        $mostrar_formulario_respuesta = true;
    } else {
        $mensaje = "Correo no encontrado.";
    }
}

// Paso 2: usuario responde la pregunta
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['verificar_respuesta'])) {
    $correo = trim($_POST['correo']);
    $respuesta_usuario = trim($_POST['respuesta']);

    $stmt = $conn->prepare("SELECT respuesta_secreta FROM usuarios WHERE correo = ?");
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($respuesta_hash);
        $stmt->fetch();

        if (password_verify($respuesta_usuario, $respuesta_hash)) {
            // Generar nueva contraseña
            $nueva = substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"), 0, 8);
            $nueva_hash = password_hash($nueva, PASSWORD_DEFAULT);

            $update = $conn->prepare("UPDATE usuarios SET password = ? WHERE correo = ?");
            $update->bind_param("ss", $nueva_hash, $correo);
            $update->execute();

            $mensaje = "Tu nueva contraseña es: <strong>$nueva</strong><br>Inicia sesión y cámbiala desde tu cuenta.";
        } else {
            $mensaje = "Respuesta incorrecta.";
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
    <title>Recuperar Contraseña - Hotel Melanys</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
</head>
<body class="container mt-5">
    <h2>Recuperar Contraseña</h2>

    <?php if (!$mostrar_formulario_respuesta): ?>
        <!-- Paso 1: ingresar correo -->
        <form method="POST">
            <div class="mb-3">
                <label>Correo registrado:</label>
                <input type="email" name="correo" class="form-control" required>
            </div>
            <button type="submit" name="buscar_correo" class="btn btn-primary">Continuar</button>
            <a href="login.php" class="btn btn-secondary">Cancelar</a>
        </form>
    <?php else: ?>
        <!-- Paso 2: mostrar pregunta secreta y pedir respuesta -->
        <form method="POST">
            <input type="hidden" name="correo" value="<?php echo htmlspecialchars($correo); ?>">
            <div class="mb-3">
                <label><strong>Pregunta secreta:</strong></label>
                <input type="text" class="form-control" value="<?php echo $pregunta; ?>" disabled>
            </div>
            <div class="mb-3">
                <label>Tu respuesta:</label>
                <input type="text" name="respuesta" class="form-control" required>
            </div>
            <button type="submit" name="verificar_respuesta" class="btn btn-success">Verificar y recuperar</button>
        </form>
    <?php endif; ?>

    <div class="mt-3 text-success"><?php echo $mensaje; ?></div>
</body>
</html>