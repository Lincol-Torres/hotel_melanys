<?php
session_start();

if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'cliente') {
    header('Location: login.php');
    exit;
}

unset($_SESSION['preseleccion']);

header('Location: dashboard_cliente.php');
exit;