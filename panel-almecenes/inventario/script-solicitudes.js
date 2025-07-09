// Funciones para manejar modales
function openModal(modalId, itemId = null) {
    const modal = document.getElementById(modalId);
    modal.classList.remove('hidden');
    
    // Configurar el ID del elemento en el modal correspondiente
    if (itemId) {
        if (modalId === 'confirmInventoryModal' || modalId === 'denyInventoryModal') {
            document.getElementById('confirmRequestId').value = itemId;
            document.getElementById('denyRequestId').value = itemId;
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

// Funciones específicas de inventario para solicitudes
function confirmInventory(requestId) {
    const location = document.getElementById('itemLocation').value;
    const condition = document.getElementById('itemCondition').value;
    const notes = document.getElementById('inventoryNotes').value;
    
    console.log('Confirmando inventario para:', requestId, 'Ubicación:', location, 'Estado:', condition, 'Notas:', notes);
    
    // Lógica para confirmar inventario (AJAX al servidor)
    alert(`Inventario confirmado para solicitud #${requestId}`);
    
    // Cerrar modal y limpiar formulario
    closeModal('confirmInventoryModal');
    document.getElementById('itemLocation').value = '';
    document.getElementById('inventoryNotes').value = '';
}

function denyInventory(requestId) {
    const reason = document.getElementById('unavailableReason').value;
    const details = document.getElementById('unavailableDetails').value;
    const alternative = document.getElementById('alternativeSuggestion').value;
    
    if (!reason) {
        alert('Por favor seleccione un motivo');
        return;
    }
    
    console.log('Denegando inventario para:', requestId, 'Motivo:', reason, 'Detalles:', details, 'Alternativa:', alternative);
    
    // Lógica para denegar inventario (AJAX al servidor)
    alert(`Inventario denegado para solicitud #${requestId}. Motivo: ${reason}`);
    
    // Cerrar modal y limpiar formulario
    closeModal('denyInventoryModal');
    document.getElementById('unavailableReason').value = '';
    document.getElementById('unavailableDetails').value = '';
    document.getElementById('alternativeSuggestion').value = '';
}