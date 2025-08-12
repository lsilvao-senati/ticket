<?php

require_once __DIR__ . '/Database.php';

class Usuario extends Database {

    /**
     * Encuentra un usuario por su dirección de correo electrónico.
     * @param string $email El correo electrónico del usuario.
     * @return array|null Los datos del usuario como un array asociativo, o null si no se encuentra.
     */
    public function findByEmail($email) {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM usuarios WHERE email = ?");
            if (!$stmt) {
                error_log("Error preparing statement: " . $this->conn->error);
                return null;
            }
            
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                return $result->fetch_assoc();
            } else {
                return null;
            }
        } catch (Exception $e) {
            error_log("Error in findByEmail: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Crea un nuevo usuario en la base de datos.
     * @param string $nombre_completo El nombre completo del usuario.
     * @param string $email El correo electrónico del usuario.
     * @param string $password La contraseña en texto plano.
     * @param string $rol El rol del usuario ('cliente' o 'desarrollador').
     * @return bool True si la creación fue exitosa, False en caso contrario.
     */
    public function create($nombre_completo, $email, $password, $rol) {
        try {
            // Validar que la conexión existe
            if (!$this->conn) {
                error_log("Error: No hay conexión a la base de datos");
                return false;
            }

            // Validar parámetros
            if (empty($nombre_completo) || empty($email) || empty($password) || empty($rol)) {
                error_log("Error: Parámetros vacíos en create()");
                return false;
            }

            // Validar que el rol sea válido
            if (!in_array($rol, ['cliente', 'desarrollador'])) {
                error_log("Error: Rol inválido: $rol");
                return false;
            }

            // Hashear la contraseña por seguridad
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            if (!$hashed_password) {
                error_log("Error: No se pudo hashear la contraseña");
                return false;
            }

            // Preparar la consulta
            $stmt = $this->conn->prepare(
                "INSERT INTO usuarios (nombre_completo, email, password, rol) VALUES (?, ?, ?, ?)"
            );
            
            if (!$stmt) {
                error_log("Error preparing statement: " . $this->conn->error);
                return false;
            }

            $stmt->bind_param("ssss", $nombre_completo, $email, $hashed_password, $rol);

            if ($stmt->execute()) {
                $stmt->close();
                return true;
            } else {
                error_log("Error executing statement: " . $stmt->error);
                error_log("MySQL Error: " . $this->conn->error);
                $stmt->close();
                return false;
            }
        } catch (Exception $e) {
            error_log("Excepción en create(): " . $e->getMessage());
            return false;
        }
    }

    /**
     * Función de depuración para verificar la conexión
     */
    public function testConnection() {
        if ($this->conn) {
            return "Conexión activa. Server info: " . $this->conn->server_info;
        } else {
            return "No hay conexión";
        }
    }

    /**
     * Obtener el último error de MySQL
     */
    public function getLastError() {
        return $this->conn ? $this->conn->error : "No connection";
    }
}
?>