// Variables globales
let currentRequestId = null;
let currentEmail = null;
let currentPhone = null;

// Funciones para manejar modales
function openModal(modalId, itemId = null) {
    const modal = document.getElementById(modalId);
    modal.classList.remove('hidden');
    
    if (itemId) {
        if (modalId === 'rechazoModal') {
            currentRequestId = itemId;
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

// Función para aprobar una solicitud
function aprobarSolicitud(requestId, email, phone) {
    if (confirm('¿Está seguro de aprobar esta solicitud?')) {
        enviarAccionSolicitud(requestId, 'aprobar', email, phone);
    }
}

// Función para rechazar una solicitud
function rechazarSolicitud(requestId, email, phone) {
    currentRequestId = requestId;
    currentEmail = email;
    currentPhone = phone;
    openModal('rechazoModal');
}

// Función para confirmar el rechazo
function confirmarRechazo() {
    const motivo = document.getElementById('motivoRechazo').value.trim();
    
    if (!motivo) {
        alert('Por favor ingrese el motivo del rechazo');
        return;
    }
    
    enviarAccionSolicitud(currentRequestId, 'rechazar', currentEmail, currentPhone, motivo);
    closeModal('rechazoModal');
}

// Función para enviar la acción al servidor
function enviarAccionSolicitud(requestId, accion, email, phone, motivo = '') {
    // Mostrar indicador de carga
    const botonAccion = document.querySelector(`button[onclick*="${requestId}"]`);
    const textoOriginal = botonAccion ? botonAccion.innerHTML : '';
    
    if (botonAccion) {
        botonAccion.disabled = true;
        botonAccion.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Procesando...';
    }

    // Crear formulario para enviar los datos
    const formData = new FormData();
    formData.append('id', requestId);
    formData.append('accion', accion);
    formData.append('motivo', motivo);
    formData.append('email', email);
    formData.append('telefono', phone);

    // Enviar la petición al servidor
    fetch('procesar_aprobacion.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Mostrar mensaje de éxito
            alert(data.message);
            
            // Si la acción fue exitosa, recargar la página o actualizar la interfaz
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            // Mostrar mensaje de error
            alert('Error: ' + (data.message || 'Ocurrió un error al procesar la solicitud'));
            
            // Restaurar el botón
            if (botonAccion) {
                botonAccion.disabled = false;
                botonAccion.innerHTML = textoOriginal;
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al procesar la solicitud. Por favor, intente nuevamente.');
        
        // Restaurar el botón en caso de error
        if (botonAccion) {
            botonAccion.disabled = false;
            botonAccion.innerHTML = textoOriginal;
        }
    });
}

// Función para enviar notificación por correo (llamada desde el servidor)
function enviarNotificacion(email, telefono, mensaje, esAprobacion = true) {
    // Esta función se llamará desde el servidor
    // La lógica de envío de correo y SMS debe estar en el backend
    console.log(`Notificación enviada a ${email} (${telefono}): ${mensaje}`);
}