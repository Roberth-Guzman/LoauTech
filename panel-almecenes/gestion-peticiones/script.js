// Funciones para manejar modales
function openModal(modalId, requestId = null) {
    const modal = document.getElementById(modalId);
    modal.classList.remove('hidden');
    
    // Configurar el ID de la solicitud en el modal correspondiente
    if (requestId) {
        if (modalId === 'approveModal') {
            document.getElementById('approveRequestId').value = requestId;
        } else if (modalId === 'rejectModal') {
            document.getElementById('rejectRequestId').value = requestId;
            // Resetear el formulario de rechazo cada vez que se abre
            document.getElementById('rejectReason').value = '';
            document.getElementById('rejectDetails').value = '';
            document.getElementById('notifyApplicant').checked = false;
        }
    }
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
}

// Cerrar modal al hacer clic fuera del contenido
window.onclick = function(event) {
    if (event.target.classList.contains('fixed')) {
        const modals = document.querySelectorAll('.fixed.inset-0.z-50');
        modals.forEach(modal => {
            if (!modal.classList.contains('hidden')) {
                modal.classList.add('hidden');
            }
        });
    }
}

// Cerrar modal con la tecla Escape
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        const modals = document.querySelectorAll('.fixed.inset-0.z-50');
        modals.forEach(modal => {
            if (!modal.classList.contains('hidden')) {
                modal.classList.add('hidden');
            }
        });
    }
});

// Funciones específicas de gestión de peticiones
function approveRequest(requestId) {
    console.log('Aprobando petición:', requestId);
    // Aquí iría la lógica para aprobar la petición
    // Por ejemplo, una llamada AJAX al servidor
    
    // Mostrar mensaje de éxito (puedes implementar un toast o alerta)
    alert(`Petición #${requestId} aprobada con éxito`);
    
    // Cerrar el modal
    closeModal('approveModal');
    
    // Actualizar la interfaz (podrías recargar la página o actualizar solo el elemento)
    // location.reload();
}

function rejectRequest(requestId) {
    const reason = document.getElementById('rejectReason').value;
    const details = document.getElementById('rejectDetails').value;
    const notifyApplicant = document.getElementById('notifyApplicant').checked;
    
    // Validación básica
    if (!reason) {
        alert('Por favor seleccione un motivo de rechazo');
        return;
    }
    
    console.log('Rechazando petición:', requestId, 'Motivo:', reason, 'Detalles:', details, 'Notificar:', notifyApplicant);
    
    // Aquí iría la lógica para rechazar la petición
    // Por ejemplo, una llamada AJAX al servidor con el motivo
    
    // Mostrar mensaje de éxito
    alert(`Petición #${requestId} rechazada. Motivo: ${reason}`);
    
    // Cerrar el modal y limpiar el formulario
    closeModal('rejectModal');
    document.getElementById('rejectReason').value = '';
    document.getElementById('rejectDetails').value = '';
    document.getElementById('notifyApplicant').checked = false;
    
    // Actualizar la interfaz
    // location.reload();
}