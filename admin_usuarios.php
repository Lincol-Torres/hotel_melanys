<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: login.php");
    exit;
}

// Cambiar rol si se solicita
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_usuario'], $_POST['nuevo_rol'])) {
    $id_usuario = intval($_POST['id_usuario']);
    $nuevo_rol = $_POST['nuevo_rol'];

    if (in_array($nuevo_rol, ['cliente', 'recepcionista', 'administrador'])) {
        $stmt = $conn->prepare("UPDATE usuarios SET rol = ? WHERE id = ?");
        $stmt->bind_param("si", $nuevo_rol, $id_usuario);
        $stmt->execute();
    }
}

// Eliminar usuario
if (isset($_GET['eliminar'])) {
    $id_eliminar = intval($_GET['eliminar']);
    $conn->query("DELETE FROM usuarios WHERE id = $id_eliminar");
}

// Obtener usuarios
$usuarios = $conn->query("SELECT id, nombre, correo, rol FROM usuarios ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestionar Usuarios</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f5f0eb;
            font-family: 'Segoe UI', sans-serif;
        }
        .container {
            margin-top: 50px;
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h2 {
            color: #4B2E1B;
            margin-bottom: 20px;
        }
        table th {
            background-color: #4B2E1B;
            color: white;
        }
    </style>
</head>
<body>

<div class="container">
    <h2><i class="bi bi-people-fill"></i> Gestión de Usuarios</h2>

    <table class="table table-bordered table-hover align-middle">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Correo</th>
                <th>Rol</th>
                <th>Cambiar Rol</th>
                <th>Eliminar</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($u = $usuarios->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $u['id']; ?></td>
                    <td><?php echo htmlspecialchars($u['nombre']); ?></td>
                    <td><?php echo htmlspecialchars($u['correo']); ?></td>
                    <td><strong><?php echo $u['rol']; ?></strong></td>
                    <td>
                        <?php if ($u['id'] != $_SESSION['usuario_id']): ?>
                            <form method="POST" class="d-flex">
                                <input type="hidden" name="id_usuario" value="<?php echo $u['id']; ?>">
                                <select name="nuevo_rol" class="form-select me-2" required>
                                    <option value="cliente" <?= $u['rol'] === 'cliente' ? 'selected' : '' ?>>Cliente</option>
                                    <option value="recepcionista" <?= $u['rol'] === 'recepcionista' ? 'selected' : '' ?>>Recepcionista</option>
                                    <option value="administrador" <?= $u['rol'] === 'administrador' ? 'selected' : '' ?>>Administrador</option>
                                </select>
                                <button class="btn btn-sm btn-primary" type="submit">Aplicar</button>
                            </form>
                        <?php else: ?>
                            <em>Tu cuenta</em>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($u['id'] != $_SESSION['usuario_id']): ?>
                            <a href="?eliminar=<?php echo $u['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Estás seguro de eliminar este usuario?')">Eliminar</a>
                        <?php else: ?>
                            <em>No permitido</em>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <div class="text-center">
        <a href="admin_panel.php" class="btn btn-secondary">← Volver al panel</a>
    </div>
</div>

</body>
</html>