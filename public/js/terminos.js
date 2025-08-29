document.addEventListener('DOMContentLoaded', function() {
    // Función para mostrar el modal de cookies si no se ha aceptado
    function mostrarModalCookies() {
        if (!localStorage.getItem('cookiesAceptadas')) {
            const modalCookies = document.getElementById('modalCookies');
            if (modalCookies) {
                modalCookies.classList.remove('hidden');
            }
        }
    }

    // Función para aceptar las cookies
    window.aceptarCookies = function() {
        localStorage.setItem('cookiesAceptadas', 'true');
        const modalCookies = document.getElementById('modalCookies');
        if (modalCookies) {
            modalCookies.classList.add('hidden');
            // Mostrar mensaje de confirmación
            const mensajeConfirmacion = document.createElement('div');
            mensajeConfirmacion.className = 'fixed bottom-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg flex items-center';
            mensajeConfirmacion.innerHTML = `
                <i class="fas fa-check-circle mr-2"></i>
                <span>¡Gracias por aceptar nuestras cookies!</span>
            `;
            document.body.appendChild(mensajeConfirmacion);
            
            // Ocultar el mensaje después de 3 segundos
            setTimeout(() => {
                mensajeConfirmacion.style.transition = 'opacity 0.5s';
                mensajeConfirmacion.style.opacity = '0';
                setTimeout(() => {
                    mensajeConfirmacion.remove();
                }, 500);
            }, 3000);
        }
    }

    // Función para validar el formulario de registro
    function validarFormularioRegistro() {
        const form = document.getElementById('registroForm');
        if (!form) return;

        form.addEventListener('submit', function(e) {
            const terminosAceptados = document.getElementById('aceptar_terminos');
            const cookiesAceptadas = document.getElementById('aceptar_cookies');
            
            if (!terminosAceptados.checked) {
                e.preventDefault();
                alert('Debe aceptar los términos y condiciones para continuar con el registro.');
                return false;
            }
            
            if (!cookiesAceptadas.checked) {
                e.preventDefault();
                alert('Debe aceptar el uso de cookies para continuar con el registro.');
                return false;
            }
            
            return true;
        });
    }

    // Inicializar las funciones
    mostrarModalCookies();
    validarFormularioRegistro();

    // Manejar el enlace de términos y condiciones
    const enlaceTerminos = document.querySelector('a[href*="terminos"]');
    if (enlaceTerminos) {
        enlaceTerminos.addEventListener('click', function(e) {
            e.preventDefault();
            window.open(this.href, '_blank', 'width=1000,height=800');
        });
    }
});
