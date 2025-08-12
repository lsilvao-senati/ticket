<div class="container">
    <h2>Bienvenido, <?php echo htmlspecialchars($user_name); ?>!</h2>
    
    <?php if (isset($status_message) && $status_message): ?>
        <div class="success"><?php echo htmlspecialchars($status_message); ?></div>
    <?php endif; ?>

    <div class="dashboard-info">
        <p><strong>Rol:</strong> <?php echo htmlspecialchars(ucfirst($user_rol)); ?></p>
        <p>Desde aquí puedes gestionar los tickets del sistema.</p>
    </div>

    <div class="dashboard-actions">
        <h3>Acciones Disponibles</h3>
        
        <div class="action-cards">
            <?php if ($user_rol === 'cliente'): ?>
                <div class="action-card">
                    <h4>Crear Ticket</h4>
                    <p>Crea una nueva solicitud de mejora, corrección o nueva funcionalidad.</p>
                    <a href="crear_ticket.php" class="btn btn-primary">Crear Nuevo Ticket</a>
                </div>
            <?php endif; ?>

            <div class="action-card">
                <h4>Ver Tickets</h4>
                <p>Consulta todos los tickets <?php echo $user_rol === 'cliente' ? 'que has creado' : 'del sistema'; ?>.</p>
                <a href="tickets.php?action=list" class="btn btn-secondary">Ver Lista de Tickets</a>
            </div>

            <?php if ($user_rol === 'desarrollador'): ?>
                <div class="action-card">
                    <h4>Tickets Asignados</h4>
                    <p>Revisa los tickets que tienes asignados para trabajar.</p>
                    <a href="tickets.php?action=assigned" class="btn btn-info">Mis Asignaciones</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="dashboard-stats">
        <h3>Estadísticas Rápidas</h3>
        <div class="stats-grid">
            <div class="stat-item">
                <span class="stat-number">--</span>
                <span class="stat-label">Total Tickets</span>
            </div>
            <div class="stat-item">
                <span class="stat-number">--</span>
                <span class="stat-label">Abiertos</span>
            </div>
            <div class="stat-item">
                <span class="stat-number">--</span>
                <span class="stat-label">En Proceso</span>
            </div>
            <div class="stat-item">
                <span class="stat-number">--</span>
                <span class="stat-label">Resueltos</span>
            </div>
        </div>
        <small><em>* Las estadísticas se implementarán en una versión futura</em></small>
    </div>
</div>