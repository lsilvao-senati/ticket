// JavaScript básico para el sistema de tickets
document.addEventListener('DOMContentLoaded', function() {
    
    // Confirmar acciones importantes
    const deleteButtons = document.querySelectorAll('.delete-btn');
    deleteButtons.forEach(function(button) {
        button.addEventListener('click', function(e) {
            if (!confirm('¿Estás seguro de que quieres eliminar este elemento?')) {
                e.preventDefault();
            }
        });
    });

    // Limpiar mensajes de error/éxito después de 5 segundos
    const messages = document.querySelectorAll('.error, .success');
    messages.forEach(function(message) {
        setTimeout(function() {
            message.style.opacity = '0';
            setTimeout(function() {
                message.remove();
            }, 500);
        }, 5000);
    });

    // Validación mejorada para formularios específicos
    const registroForm = document.getElementById('registro-form');
    if (registroForm) {
        registroForm.addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const passwordConfirm = document.getElementById('password_confirm').value;
            const submitBtn = registroForm.querySelector('button[type="submit"]');
            
            // Validar contraseñas
            if (password !== passwordConfirm) {
                e.preventDefault();
                alert('Las contraseñas no coinciden');
                return false;
            }
            
            if (password.length < 6) {
                e.preventDefault();
                alert('La contraseña debe tener al menos 6 caracteres');
                return false;
            }
            
            // Solo cambiar el botón si todo está bien
            const originalText = submitBtn.textContent;
            submitBtn.textContent = 'Procesando...';
            submitBtn.disabled = true;
            
            // Restaurar el botón después de 10 segundos por seguridad
            setTimeout(function() {
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
            }, 10000);
        });
    }

    // Validación básica para otros formularios
    const otherForms = document.querySelectorAll('form:not(#registro-form)');
    otherForms.forEach(function(form) {
        form.addEventListener('submit', function(e) {
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;

            requiredFields.forEach(function(field) {
                if (!field.value.trim()) {
                    field.style.borderColor = '#d32f2f';
                    isValid = false;
                } else {
                    field.style.borderColor = '#ddd';
                }
            });

            if (!isValid) {
                e.preventDefault();
                alert('Por favor, completa todos los campos obligatorios.');
            }
        });
    });
});

// Función para mostrar/ocultar campos condicionales
function toggleConditionalFields() {
    const tipoSelect = document.getElementById('tipo');
    const pasosField = document.getElementById('pasos_reproducir');
    
    if (tipoSelect && pasosField) {
        if (tipoSelect.value === 'error') {
            pasosField.parentElement.style.display = 'block';
            pasosField.required = true;
        } else {
            pasosField.parentElement.style.display = 'none';
            pasosField.required = false;
        }
    }
}

// Aplicar la función al cargar la página y cuando cambie el tipo
if (document.getElementById('tipo')) {
    document.getElementById('tipo').addEventListener('change', toggleConditionalFields);
    toggleConditionalFields(); // Llamar al cargar la página
}