-- Este script contiene las sentencias SQL para actualizar la base de datos
-- y añadir la funcionalidad de adjuntos y estados de tickets.

-- Crear la tabla de adjuntos si no existe
CREATE TABLE IF NOT EXISTS adjuntos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ticket_id INT NOT NULL,
    ruta_archivo VARCHAR(255) NOT NULL,
    nombre_archivo VARCHAR(255) NOT NULL,
    fecha_subida TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ticket_id) REFERENCES tickets(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Añadir la columna de estado a la tabla de tickets si no existe
-- NOTA: La siguiente consulta fallará si la columna ya existe.
-- Ejecutar solo si la columna 'estado' no está presente en la tabla 'tickets'.
ALTER TABLE tickets ADD COLUMN estado ENUM('abierto', 'en_proceso', 'resuelto', 'cerrado') NOT NULL DEFAULT 'abierto';

-- Si necesita una forma de ejecutarlo que no falle, puede hacerlo con un procedimiento,
-- pero para un script de configuración simple, esto suele ser suficiente.
-- Ejemplo de cómo verificar si la columna existe antes de añadirla (requiere más permisos y es más complejo):
/*
IF NOT EXISTS(SELECT * FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_NAME = 'tickets' AND COLUMN_NAME = 'estado' AND TABLE_SCHEMA = DATABASE())
THEN
    ALTER TABLE `tickets`
    ADD COLUMN `estado` ENUM('abierto', 'en_proceso', 'resuelto', 'cerrado') NOT NULL DEFAULT 'abierto';
END IF;
*/
