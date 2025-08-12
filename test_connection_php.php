<?php
// Script para verificar la conexión a la base de datos
require_once 'config/database.php';

echo "<h1>Test de Conexión a la Base de Datos</h1>";

// Mostrar configuración (sin contraseña)
echo "<h2>Configuración:</h2>";
echo "Host: " . DB_HOST . "<br>";
echo "Usuario: " . DB_USER . "<br>";
echo "Base de datos: " . DB_NAME . "<br>";
echo "URL de la aplicación: " . APP_URL . "<br>";

// Probar conexión
try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($conn->connect_error) {
        echo "<p style='color: red;'><strong>Error de conexión:</strong> " . $conn->connect_error . "</p>";
        exit();
    }
    
    echo "<p style='color: green;'><strong>✓ Conexión exitosa a MySQL</strong></p>";
    
    // Verificar que las tablas existan
    $tables = ['usuarios', 'tickets', 'comentarios', 'adjuntos'];
    echo "<h2>Verificando tablas:</h2>";
    
    foreach ($tables as $table) {
        $result = $conn->query("SHOW TABLES LIKE '$table'");
        if ($result->num_rows > 0) {
            echo "<p style='color: green;'>✓ Tabla '$table' existe</p>";
            
            // Mostrar estructura de la tabla usuarios para verificar
            if ($table === 'usuarios') {
                $result = $conn->query("DESCRIBE $table");
                echo "<h3>Estructura de la tabla usuarios:</h3>";
                echo "<table border='1'>";
                echo "<tr><th>Campo</th><th>Tipo</th><th>Null</th><th>Key</th><th>Default</th></tr>";
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row['Field'] . "</td>";
                    echo "<td>" . $row['Type'] . "</td>";
                    echo "<td>" . $row['Null'] . "</td>";
                    echo "<td>" . $row['Key'] . "</td>";
                    echo "<td>" . $row['Default'] . "</td>";
                    echo "</tr>";
                }
                echo "</table>";
            }
        } else {
            echo "<p style='color: red;'>✗ Tabla '$table' NO existe</p>";
        }
    }
    
    // Verificar si hay usuarios existentes
    $result = $conn->query("SELECT COUNT(*) as count FROM usuarios");
    if ($result) {
        $row = $result->fetch_assoc();
        echo "<h2>Usuarios existentes:</h2>";
        echo "<p>Total de usuarios en la base de datos: " . $row['count'] . "</p>";
        
        if ($row['count'] > 0) {
            $users = $conn->query("SELECT id, nombre_completo, email, rol, fecha_creacion FROM usuarios ORDER BY fecha_creacion DESC LIMIT 5");
            echo "<h3>Últimos 5 usuarios:</h3>";
            echo "<table border='1'>";
            echo "<tr><th>ID</th><th>Nombre</th><th>Email</th><th>Rol</th><th>Fecha Creación</th></tr>";
            while ($user = $users->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $user['id'] . "</td>";
                echo "<td>" . $user['nombre_completo'] . "</td>";
                echo "<td>" . $user['email'] . "</td>";
                echo "<td>" . $user['rol'] . "</td>";
                echo "<td>" . $user['fecha_creacion'] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
    }
    
    $conn->close();
    
} catch (Exception $e) {
    echo "<p style='color: red;'><strong>Error:</strong> " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><a href='public/index.php'>Volver a la aplicación</a></p>";
?>