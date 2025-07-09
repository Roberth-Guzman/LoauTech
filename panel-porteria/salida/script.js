// script.js
// Funciones específicas de portería
function authorizeExit(requestId) {
    console.log('Autorizando salida para:', requestId);
    // Aquí puedes agregar lógica para procesar el formulario
    // Por ejemplo, recoger los datos del formulario y enviarlos al servidor
    
    // Cerrar el modal
    var modal = bootstrap.Modal.getInstance(document.getElementById('confirmExitModal'));
    modal.hide();
    
    // Mostrar mensaje de éxito (opcional)
    alert('Salida autorizada correctamente para la solicitud #' + requestId);
    
    // Aquí podrías recargar la página o actualizar la tabla de registros
    // location.reload();
}

function holdItem(requestId) {
    console.log('Reteniendo elemento para:', requestId);
    // Aquí puedes agregar lógica para procesar el formulario de retención
    
    // Validar que se haya seleccionado un motivo
    const motivo = document.querySelector('#holdExitModal select').value;
    if (!motivo) {
        alert('Por favor seleccione un motivo de retención');
        return;
    }
    
    // Cerrar el modal
    var modal = bootstrap.Modal.getInstance(document.getElementById('holdExitModal'));
    modal.hide();
    
    // Mostrar mensaje de éxito (opcional)
    alert('Elemento retenido correctamente para la solicitud #' + requestId);
    
    // Aquí podrías recargar la página o actualizar la tabla de registros
    // location.reload();
}

// Inicializar tooltips de Bootstrap
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});