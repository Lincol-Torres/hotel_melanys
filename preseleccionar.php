<?php
session_start();

if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'cliente') {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['habitacion'])) {
    $_SESSION['preseleccion'] = $_POST['habitacion'];
}

header('Location: dashboard_cliente.php');
exit;