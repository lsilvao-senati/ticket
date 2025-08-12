<?php
// Script para probar el registro de usuarios
require_once 'src/models/Usuario.php';

echo "<h1>Test de Registro de Usuario</h1>";

try {
    $usuarioModel = new Usuario();
    echo "<p style='color: green;'>✓ Modelo Usuario creado exitosamente</p>";

    // Datos de prueba
    $nombre_completo = "Usuario de Prueba";
    $email = "test@ejemplo.com";
    $password = "123456";
    $rol = "cliente";

    echo "<h2>Intentando crear usuario de prueba:</h2>";
    echo "Nombre: $nombre_completo<br>";
    echo "Email: $email<br>";
    echo "Rol: $rol<br>";

    // Verificar si ya existe
    $existingUser = $usuarioModel->findByEmail($email);
    if ($existingUser) {
        echo "<p style='color: orange;'>⚠ Usuario ya existe, eliminándolo primero...</p>";
        
        // Eliminar usuario existente para la prueba
        require_once 'config/database.php';
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        $stmt = $conn->prepare("DELETE FROM usuarios WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $conn->close();
        
        echo "<p style='color: green;'>✓ Usuario anterior eliminado</p>";
    }

    // Intentar crear el usuario
    $result = $usuarioModel->create($nombre_completo, $email, $password, $rol);
    
    if ($result) {
        echo "<p style='color: green;'><strong>✓ Usuario creado exitosamente!</strong></p>";
        
        // Verificar que se creó correctamente
        $newUser = $usuarioModel->findByEmail($email);
        if ($newUser) {
            echo "<h3>Datos del usuario creado:</h3>";
            echo "<table border='1'>";
            echo "<tr><th>Campo</th><th>Valor</th></tr>";
            foreach ($newUser as $key => $value) {
                if ($key !== 'password') { // No mostrar la contraseña hasheada
                    echo "<tr><td>$key</td><td>$value</td></tr>";
                }
            }
            echo "</table>";
            
            // Probar verificación de contraseña
            if (password_verify($password, $newUser['password'])) {
                echo "<p style='color: green;'>✓ Contraseña hasheada correctamente</p>";
            } else {
                echo "<p style='color: red;'>✗ Error en el hashing de contraseña</p>";
            }
        }
    } else {
        echo "<p style='color: red;'><strong>✗ Error al crear el usuario</strong></p>";
        
        // Intentar obtener más información del error
        require_once 'config/database.php';
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        echo "<p>Error MySQL: " . $conn->error . "</p>";
        $conn->close();
    }

} catch (Exception $e) {
    echo "<p style='color: red;'><strong>Excepción:</strong> " . $e->getMessage() . "</p>";
    echo "<p><strong>Trace:</strong><br><pre>" . $e->getTraceAsString() . "</pre></p>";
}

echo "<hr>";
echo "<p><a href='public/index.php'>Volver a la aplicación</a></p>";
?>