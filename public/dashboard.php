<?php
session_start();

// Cargar configuración ANTES de usar el layout
require_once __DIR__ . '/../config/database.php';

// Proteger la página
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Verificar si hay mensajes de estado
$status_message = null;
if (isset($_GET['status'])) {
    switch ($_GET['status']) {
        case 'ticket_created':
            $status_message = '¡Ticket creado exitosamente!';
            break;
        case 'success':
            $status_message = '¡Operación completada exitosamente!';
            break;
    }
}

if (isset($_GET['error'])) {
    switch ($_GET['error']) {
        case 'unauthorized':
            $status_message = 'No tienes permisos para realizar esa acción.';
            break;
    }
}

// Preparar los datos para la vista
$user_name = $_SESSION['user_nombre'];
$user_rol = $_SESSION['user_rol'];

// Configurar la vista
$page_title = 'Dashboard';
$view_content = __DIR__ . '/../src/views/dashboard_view.php';

// Incluir el layout
require_once __DIR__ . '/../src/views/_layout.php';
?>