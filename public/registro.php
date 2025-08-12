<?php
// Habilitar reporte de errores para debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

require_once __DIR__ . '/../src/models/Usuario.php';
require_once __DIR__ . '/../config/database.php';

// Debug: mostrar que el archivo se está ejecutando
echo "<!-- DEBUG: registro.php iniciado -->\n";

// Si el usuario ya está logueado, redirigir al dashboard
if (isset($_SESSION['user_id'])) {
    echo "<!-- DEBUG: Usuario ya logueado, redirigiendo -->\n";
    header('Location: dashboard.php');
    exit();
}

$error = null;
$success = null;
$debug_info = [];

// Debug: verificar método de solicitud
$debug_info[] = "Método de solicitud: " . $_SERVER['REQUEST_METHOD'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $debug_info[] = "Procesando POST";
    
    // Mostrar todos los datos POST recibidos
    $debug_info[] = "Datos POST recibidos: " . print_r($_POST, true);
    
    // Recoger datos del formulario
    $nombre_completo = trim($_POST['nombre_completo'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';
    $rol = $_POST['rol'] ?? '';

    $debug_info[] = "Datos procesados: nombre='$nombre_completo', email='$email', rol='$rol'";

    // Validación básica
    if (empty($nombre_completo)) {
        $error = "El nombre completo es obligatorio.";
        $debug_info[] = "Error: nombre vacío";
    } elseif (empty($email)) {
        $error = "El email es obligatorio.";
        $debug_info[] = "Error: email vacío";
    } elseif (empty($password)) {
        $error = "La contraseña es obligatoria.";
        $debug_info[] = "Error: password vacío";
    } elseif (empty($rol)) {
        $error = "El rol es obligatorio.";
        $debug_info[] = "Error: rol vacío";
    } elseif ($password !== $password_confirm) {
        $error = "Las contraseñas no coinciden.";
        $debug_info[] = "Error: passwords no coinciden";
    } elseif (strlen($password) < 6) {
        $error = "La contraseña debe tener al menos 6 caracteres.";
        $debug_info[] = "Error: password muy corto";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "El formato del correo electrónico no es válido.";
        $debug_info[] = "Error: email inválido";
    } elseif (!in_array($rol, ['cliente', 'desarrollador'])) {
        $error = "El rol seleccionado no es válido.";
        $debug_info[] = "Error: rol inválido";
    } else {
        $debug_info[] = "Validación pasada, intentando crear usuario";
        
        // Intentar crear el usuario
        try {
            $usuarioModel = new Usuario();
            $debug_info[] = "Modelo Usuario creado";
            
            // Verificar si el email ya existe
            $existingUser = $usuarioModel->findByEmail($email);
            if ($existingUser) {
                $error = "El correo electrónico ya está registrado.";
                $debug_info[] = "Error: email ya existe";
            } else {
                $debug_info[] = "Email disponible, creando usuario";
                
                // Crear el usuario
                $result = $usuarioModel->create($nombre_completo, $email, $password, $rol);
                $debug_info[] = "Resultado create(): " . ($result ? 'true' : 'false');
                
                if ($result) {
                    $debug_info[] = "Usuario creado exitosamente, redirigiendo";
                    // Éxito - redirigir a login
                    header('Location: login.php?status=success');
                    exit();
                } else {
                    $error = "Error al crear la cuenta. Error del sistema: " . $usuarioModel->getLastError();
                    $debug_info[] = "Error en create(): " . $usuarioModel->getLastError();
                }
            }
        } catch (Exception $e) {
            $error = "Error del sistema: " . $e->getMessage();
            $debug_info[] = "Excepción: " . $e->getMessage();
        }
    }
} else {
    $debug_info[] = "Solicitud GET - mostrando formulario";
}

// Mostrar la vista
$page_title = 'Registro de Usuario';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - Sistema de Tickets</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .debug-info {
            background: #f0f0f0;
            border: 1px solid #ccc;
            padding: 10px;
            margin: 10px 0;
            font-family: monospace;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <header>
        <nav>
            <a href="index.php"><h1><?php echo APP_NAME; ?></h1></a>
            <ul>
                <li><a href="login.php">Iniciar Sesión</a></li>
                <li><a href="registro.php">Registrarse</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <div class="container">
            <h2>Registro de Usuario</h2>

            <!-- Información de debug -->
            <?php if (!empty($debug_info)): ?>
                <div class="debug-info">
                    <strong>Información de Debug:</strong><br>
                    <?php foreach ($debug_info as $info): ?>
                        • <?php echo htmlspecialchars($info); ?><br>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>

            <form method="POST" action="registro.php">
                <div>
                    <label for="nombre_completo">Nombre Completo: *</label>
                    <input type="text" id="nombre_completo" name="nombre_completo" 
                           value="<?php echo htmlspecialchars($_POST['nombre_completo'] ?? ''); ?>" required>
                </div>
                
                <div>
                    <label for="email">Correo Electrónico: *</label>
                    <input type="email" id="email" name="email" 
                           value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                </div>
                
                <div>
                    <label for="password">Contraseña: *</label>
                    <input type="password" id="password" name="password" required>
                    <small>Mínimo 6 caracteres</small>
                </div>
                
                <div>
                    <label for="password_confirm">Confirmar Contraseña: *</label>
                    <input type="password" id="password_confirm" name="password_confirm" required>
                </div>
                
                <div>
                    <label for="rol">Tipo de Usuario: *</label>
                    <select id="rol" name="rol" required>
                        <option value="">Selecciona un rol</option>
                        <option value="cliente" <?php echo (($_POST['rol'] ?? '') === 'cliente') ? 'selected' : ''; ?>>
                            Cliente (Crear tickets)
                        </option>
                        <option value="desarrollador" <?php echo (($_POST['rol'] ?? '') === 'desarrollador') ? 'selected' : ''; ?>>
                            Desarrollador (Gestionar tickets)
                        </option>
                    </select>
                </div>
                
                <button type="submit">Registrarse</button>
            </form>

            <p>¿Ya tienes una cuenta? <a href="login.php">Inicia sesión aquí</a></p>
        </div>
    </main>
    <footer>
        <p>&copy; <?php echo date('Y'); ?> Mi Empresa de Software</p>
    </footer>

    <!-- NO JavaScript por ahora para debugging -->
</body>
</html>