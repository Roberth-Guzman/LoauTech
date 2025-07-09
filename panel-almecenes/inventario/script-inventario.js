// Funciones para manejar modales
function openModal(modalId, itemId = null) {
    const modal = document.getElementById(modalId);
    modal.classList.remove('hidden');
    
    // Configurar el ID del elemento en el modal correspondiente
    if (itemId) {
        if (modalId === 'editItemModal') {
            document.getElementById('editItemId').value = itemId;
            // Aquí podrías cargar los datos del elemento con AJAX
            // Ejemplo simplificado:
            if (itemId === 'LAP001') {
                document.getElementById('editItemCode').value = 'LAP001';
                document.getElementById('editItemName').value = 'Laptop Dell XPS';
                document.getElementById('editItemCategory').value = 'computers';
                document.getElementById('editItemStock').value = '5';
                document.getElementById('editItemStatus').value = 'available';
                document.getElementById('editItemDescription').value = 'Laptop de alto rendimiento para tareas de oficina';
            }
        } else if (modalId === 'deleteItemModal') {
            document.getElementById('deleteItemId').value = itemId;
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

// Funciones específicas de gestión de inventario
function addInventoryItem() {
    const form = document.getElementById('addItemForm');
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    
    const code = document.getElementById('itemCode').value;
    const name = document.getElementById('itemName').value;
    const category = document.getElementById('itemCategory').value;
    const stock = document.getElementById('itemStock').value;
    const status = document.getElementById('itemStatus').value;
    const description = document.getElementById('itemDescription').value;
    
    console.log('Agregando nuevo elemento:', {code, name, category, stock, status, description});
    
    // Lógica para agregar elemento (AJAX al servidor)
    alert(`Elemento ${name} agregado al inventario`);
    
    // Cerrar modal y limpiar formulario
    closeModal('addItemModal');
    form.reset();
}

function updateInventoryItem() {
    const form = document.getElementById('editItemForm');
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    
    const id = document.getElementById('editItemId').value;
    const name = document.getElementById('editItemName').value;
    const category = document.getElementById('editItemCategory').value;
    const stock = document.getElementById('editItemStock').value;
    const status = document.getElementById('editItemStatus').value;
    const description = document.getElementById('editItemDescription').value;
    
    console.log('Actualizando elemento:', {id, name, category, stock, status, description});
    
    // Lógica para actualizar elemento (AJAX al servidor)
    alert(`Elemento ${name} actualizado`);
    
    // Cerrar modal
    closeModal('editItemModal');
}

function deleteInventoryItem(itemId) {
    console.log('Eliminando elemento:', itemId);
    
    // Lógica para eliminar elemento (AJAX al servidor)
    alert(`Elemento #${itemId} eliminado del inventario`);
    
    // Cerrar modal
    closeModal('deleteItemModal');
    
    // Actualizar la interfaz (podrías recargar la página o actualizar solo el elemento)
    // location.reload();
}