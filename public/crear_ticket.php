<?php
session_start();

require_once __DIR__ . '/../src/models/Ticket.php';
require_once __DIR__ . '/../config/database.php'; // Agregar esta línea

// Proteger la página: el usuario debe estar logueado
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Opcional: restringir solo a clientes
if ($_SESSION['user_rol'] !== 'cliente') {
    // Redirigir al dashboard con un mensaje de error o simplemente mostrar un error
    header('Location: dashboard.php?error=unauthorized');
    exit();
}

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recoger y validar los datos del formulario
    $data = [
        'titulo' => trim($_POST['titulo'] ?? ''),
        'descripcion' => trim($_POST['descripcion'] ?? ''),
        'tipo' => $_POST['tipo'] ?? '',
        'prioridad' => $_POST['prioridad'] ?? '',
        'modulo_afectado' => trim($_POST['modulo_afectado'] ?? null),
        'pasos_reproducir' => trim($_POST['pasos_reproducir'] ?? null),
        'solicitante_id' => $_SESSION['user_id']
    ];

    // Validación básica
    if (empty($data['titulo']) || empty($data['descripcion']) || empty($data['tipo']) || empty($data['prioridad'])) {
        $error = "Los campos Título, Descripción, Tipo y Prioridad son obligatorios.";
    } else {
        $ticketModel = new Ticket();
        // Manejo de adjuntos antes de crear el ticket para validación
        $adjunto_validado = true;
        if (isset($_FILES['adjunto']) && $_FILES['adjunto']['error'] === UPLOAD_ERR_OK) {
            $max_size = 5 * 1024 * 1024; // 5 MB
            $allowed_types = ['image/jpeg', 'image/png', 'application/pdf', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
            $file_size = $_FILES['adjunto']['size'];
            $file_type = $_FILES['adjunto']['type'];

            if ($file_size > $max_size) {
                $error = "El archivo es demasiado grande. El tamaño máximo es 5 MB.";
                $adjunto_validado = false;
            } elseif (!in_array($file_type, $allowed_types)) {
                $error = "Tipo de archivo no permitido. Solo se aceptan JPG, PNG, PDF, DOCX.";
                $adjunto_validado = false;
            }
        }

        if ($adjunto_validado) {
            $ticketModel = new Ticket();
            $ticket_id = $ticketModel->create($data);

            if ($ticket_id) {
                // Si hay un adjunto, procesarlo
                if (isset($_FILES['adjunto']) && $_FILES['adjunto']['error'] === UPLOAD_ERR_OK) {
                    $uploadDir = __DIR__ . '/uploads/';
                    $nombre_original = basename($_FILES['adjunto']['name']);
                    $nombre_unico = uniqid() . '-' . $nombre_original;
                    $ruta_destino = $uploadDir . $nombre_unico;

                    if (move_uploaded_file($_FILES['adjunto']['tmp_name'], $ruta_destino)) {
                        $ticketModel->addAttachment($ticket_id, 'uploads/' . $nombre_unico, $nombre_original);
                        // Todo salió bien, redirigir
                        header('Location: dashboard.php?status=ticket_created');
                        exit();
                    } else {
                        $error = "El ticket fue creado, pero hubo un error al subir el archivo adjunto.";
                        // Opcional: considerar borrar el ticket si el adjunto es crítico
                    }
                } else {
                    // No hay adjunto, el ticket se creó bien, redirigir
                    header('Location: dashboard.php?status=ticket_created');
                    exit();
                }
            } else {
                $error = "Hubo un error al crear el ticket. Por favor, inténtelo de nuevo.";
            }
        }
    }
}

// Mostrar la vista para crear el ticket
$page_title = 'Crear Nuevo Ticket';
$view_content = __DIR__ . '/../src/views/tickets/create_view.php';
require_once __DIR__ . '/../src/views/_layout.php';

?>