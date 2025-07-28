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
    if (!requestId) {
        console.error('ID de solicitud no proporcionado');
        return;
    }
    
    // Crear un formulario dinámico para enviar la solicitud
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = 'aprobar_prestamo.php';
    
    // Agregar el ID de la solicitud
    const idInput = document.createElement('input');
    idInput.type = 'hidden';
    idInput.name = 'id';
    idInput.value = requestId;
    form.appendChild(idInput);
    
    // Agregar el formulario al documento y enviarlo
    document.body.appendChild(form);
    form.submit();
}

function rejectRequest() {
    const requestId = document.getElementById('rejectRequestId').value;
    const reason = document.getElementById('rejectReason').value;
    
    // Validación básica
    if (!reason) {
        alert('Por favor seleccione un motivo de rechazo');
        return false; // Evita que el formulario se envíe
    }
    
    // Si llegamos aquí, la validación pasó
    // El formulario se enviará automáticamente
    return true;
}

// Configurar el evento de envío del formulario de rechazo
document.addEventListener('DOMContentLoaded', function() {
    const rejectForm = document.getElementById('rejectForm');
    if (rejectForm) {
        rejectForm.onsubmit = function() {
            return rejectRequest();
        };
    }
});