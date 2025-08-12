<div class="container">
    <h2>Registro de Usuario</h2>

    <?php if (isset($error)): ?>
        <div class="error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form action="registro.php" method="POST" id="registro-form">
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
            <input type="password" id="password" name="password" required minlength="6">
            <small>Mínimo 6 caracteres</small>
        </div>
        
        <div>
            <label for="password_confirm">Confirmar Contraseña: *</label>
            <input type="password" id="password_confirm" name="password_confirm" required minlength="6">
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
        
        <button type="submit" id="submit-btn">Registrarse</button>
    </form>

    <p>¿Ya tienes una cuenta? <a href="login.php">Inicia sesión aquí</a></p>
</div>

<script>
// Validación adicional del lado cliente
document.getElementById('registro-form').addEventListener('submit', function(e) {
    const password = document.getElementById('password').value;
    const passwordConfirm = document.getElementById('password_confirm').value;
    
    if (password !== passwordConfirm) {
        e.preventDefault();
        alert('Las contraseñas no coinciden');
        document.getElementById('submit-btn').textContent = 'Registrarse';
        document.getElementById('submit-btn').disabled = false;
        return false;
    }
    
    if (password.length < 6) {
        e.preventDefault();
        alert('La contraseña debe tener al menos 6 caracteres');
        document.getElementById('submit-btn').textContent = 'Registrarse';
        document.getElementById('submit-btn').disabled = false;
        return false;
    }
});
</script>