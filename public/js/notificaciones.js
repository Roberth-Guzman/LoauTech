class Notificaciones {
    constructor() {
        this.notificacionesContainer = document.getElementById('notificaciones-container');
        this.contadorNotificaciones = document.getElementById('contador-notificaciones');
        this.notificationButton = document.getElementById('notification-button');
        this.notificationDropdown = document.getElementById('notification-dropdown');
        this.userId = document.documentElement.getAttribute('data-user-id');
        
        if (this.notificationButton) {
            this.init();
        }
    }

    init() {
        console.log('Inicializando notificaciones...');
        
        // Verificar que los elementos del DOM existan
        if (!this.notificationButton) {
            console.error('No se encontró el botón de notificaciones');
            return;
        }
        
        if (!this.notificationDropdown) {
            console.error('No se encontró el menú desplegable de notificaciones');
            return;
        }
        
        // Cargar notificaciones al cargar la página
        this.cargarNotificaciones();
        
        // Configurar el intervalo para actualizar notificaciones cada minuto
        setInterval(() => this.cargarNotificaciones(), 60000);
        
        // Toggle dropdown
        this.notificationButton.onclick = (e) => {
            console.log('Clic en el botón de notificaciones');
            e.stopPropagation();
            e.preventDefault();
            
            // Alternar la visibilidad del menú desplegable
            const isHidden = this.notificationDropdown.classList.contains('hidden');
            console.log('Estado actual del menú:', isHidden ? 'oculto' : 'visible');
            
            // Si el menú está oculto, mostrarlo
            if (isHidden) {
                this.notificationDropdown.classList.remove('hidden');
                console.log('Mostrando menú de notificaciones');
                this.marcarTodasLeidas();
            } else {
                this.notificationDropdown.classList.add('hidden');
                console.log('Ocultando menú de notificaciones');
            }
        };
        
        // Cerrar al hacer clic fuera
        document.onclick = (e) => {
            if (!this.notificationButton.contains(e.target) && !this.notificationDropdown.contains(e.target)) {
                console.log('Clic fuera del menú, ocultando...');
                this.notificationDropdown.classList.add('hidden');
            }
        };
        
        // Delegación de eventos para marcar como leídas
        document.addEventListener('click', (e) => {
            if (e.target.closest('.marcar-leida')) {
                e.preventDefault();
                const notificacionId = e.target.closest('.marcar-leida').dataset.id;
                console.log('Marcando notificación como leída:', notificacionId);
                this.marcarComoLeida(notificacionId);
            } else if (e.target.closest('#marcar-todas-leidas')) {
                e.preventDefault();
                console.log('Marcando todas las notificaciones como leídas');
                this.marcarTodasLeidas();
            }
        });
        
        console.log('Eventos de notificaciones configurados correctamente');
    }

    async cargarNotificaciones() {
        try {
            // Mostrar el contador por defecto
            if (this.contadorNotificaciones) {
                this.contadorNotificaciones.style.display = 'flex';
            }
            
            const response = await fetch(`${BASE_URL}/notificacion/obtenerNotificaciones`, {
                headers: {
                    'Cache-Control': 'no-cache, no-store, must-revalidate',
                    'Pragma': 'no-cache',
                    'Expires': '0',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            if (!response.ok) {
                const errorText = await response.text();
                console.error('Error en la respuesta del servidor:', response.status, errorText);
                throw new Error(`HTTP error! status: ${response.status}, ${errorText}`);
            }
            
            const data = await response.json();
            console.log('Datos de notificaciones recibidos:', data);
            
            if (data.error) {
                console.error('Error en la respuesta de la API:', data.error);
                if (this.notificacionesContainer) {
                    this.notificacionesContainer.innerHTML = `
                        <div class="p-4 text-center text-sm text-red-600">
                            Error al cargar notificaciones: ${data.error}
                        </div>`;
                }
                return;
            }
            
            if (!data.notificaciones) {
                console.warn('No se recibió el arreglo de notificaciones en la respuesta');
                data.notificaciones = [];
            }
            
            console.log(`Mostrando ${data.notificaciones.length} notificaciones`);
            this.actualizarInterfaz({
                notificaciones: data.notificaciones,
                sinLeer: data.sinLeer || 0
            });
            
        } catch (error) {
            console.error('Error al cargar notificaciones:', error);
            if (this.notificacionesContainer) {
                this.notificacionesContainer.innerHTML = `
                    <div class="p-4 text-center text-sm text-red-600">
                        No se pudieron cargar las notificaciones. ${error.message}
                        <div class="mt-2 text-xs text-gray-500">
                            URL: ${BASE_URL}/notificacion/obtenerNotificaciones
                        </div>
                    </div>`;
            }
        }
    }

    actualizarInterfaz(data) {
        console.log('Actualizando interfaz con datos:', data);
        
        // Actualizar contador
        this.actualizarContador(data.sinLeer);
        
        // Actualizar lista de notificaciones
        if (this.notificacionesContainer) {
            if (!data.notificaciones || data.notificaciones.length === 0) {
                console.log('No hay notificaciones para mostrar');
                this.notificacionesContainer.innerHTML = `
                    <div class="p-4 text-center text-sm text-gray-500">
                        No hay notificaciones nuevas.
                    </div>`;
            } else {
                console.log(`Generando HTML para ${data.notificaciones.length} notificaciones`);
                this.notificacionesContainer.innerHTML = this.generarHTMLNotificaciones(data.notificaciones);
            }
        }
    }

    actualizarContador(sinLeer) {
        if (this.contadorNotificaciones) {
            this.contadorNotificaciones.textContent = sinLeer > 0 ? sinLeer : '';
            this.contadorNotificaciones.style.display = sinLeer > 0 ? 'flex' : 'none';
        }
    }

    generarHTMLNotificaciones(notificaciones) {
        if (!notificaciones || notificaciones.length === 0) {
            return `
                <div class="p-4 text-center text-gray-500 text-sm">
                    No tienes notificaciones nuevas
                </div>`;
        }
        
        return `
            <div class="divide-y divide-gray-100">
                ${notificaciones.map(notif => `
                    <div class="px-4 py-3 hover:bg-gray-50 ${notif.estado === 'no_leida' ? 'bg-blue-50' : ''}">
                        <div class="flex items-start">
                            <div class="flex-shrink-0 pt-0.5">
                                <span class="h-6 w-6 rounded-full flex items-center justify-center 
                                    ${notif.estado === 'no_leida' ? 'bg-blue-100 text-blue-600' : 'bg-gray-100 text-gray-500'}">
                                    <i class="fas ${this.getNotificationIcon(notif.tipo)} text-xs"></i>
                                </span>
                            </div>
                            <div class="ml-3 flex-1 min-w-0">
                                <p class="text-sm text-gray-800">
                                    ${notif.mensaje}
                                </p>
                                <div class="mt-1 flex justify-between items-center">
                                    <p class="text-xs text-gray-500">
                                        ${this.formatearFecha(notif.fecha_creacion)}
                                    </p>
                                    ${notif.estado === 'no_leida' ? `
                                        <button class="text-xs text-blue-600 hover:text-blue-800 marcar-leida" 
                                                data-id="${notif.IDnot}" 
                                                title="Marcar como leída">
                                            Marcar como leída
                                        </button>` : ''
                                    }
                                </div>
                            </div>
                        </div>
                    </div>
                `).join('')}
            </div>`;
    }

    async marcarComoLeida(idNotificacion) {
        try {
            const response = await fetch(`${BASE_URL}/notificacion/marcarLeida/${idNotificacion}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                }
            });
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();
            
            if (data.success) {
                // Actualizar la interfaz sin recargar todas las notificaciones
                const notificacionElement = document.querySelector(`.marcar-leida[data-id="${idNotificacion}"]`);
                if (notificacionElement) {
                    const item = notificacionElement.closest('.hover\:bg-gray-50');
                    if (item) {
                        item.classList.remove('bg-blue-50');
                        notificacionElement.remove();
                        
                        // Actualizar el contador
                        if (this.contadorNotificaciones) {
                            const currentCount = parseInt(this.contadorNotificaciones.textContent || '0');
                            if (currentCount > 0) {
                                this.contadorNotificaciones.textContent = currentCount - 1;
                                if (currentCount - 1 === 0) {
                                    this.contadorNotificaciones.style.display = 'none';
                                }
                            }
                        }
                    }
                }
            } else {
                console.error('Error al marcar notificación como leída:', data.error);
                this.mostrarMensaje(data.error || 'Error al marcar la notificación como leída', 'error');
            }
        } catch (error) {
            console.error('Error al marcar notificación como leída:', error);
            this.mostrarMensaje('Error al marcar la notificación como leída', 'error');
        }
    }

    async marcarTodasLeidas() {
        try {
            const response = await fetch(`${BASE_URL}/notificacion/marcarTodasLeidas`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                }
            });
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();
            
            if (data.success) {
                // Actualizar la interfaz
                document.querySelectorAll('.bg-blue-50').forEach(el => {
                    el.classList.remove('bg-blue-50');
                });
                
                document.querySelectorAll('.marcar-leida').forEach(btn => {
                    btn.remove();
                });
                
                // Actualizar el contador
                this.actualizarContador(0);
                
                // Mostrar mensaje de éxito
                this.mostrarMensaje('Todas las notificaciones se han marcado como leídas', 'success');
            } else {
                console.error('Error al marcar todas las notificaciones como leídas:', data.error);
                this.mostrarMensaje('Error al marcar las notificaciones como leídas', 'error');
            }
        } catch (error) {
            console.error('Error al marcar todas las notificaciones como leídas:', error);
            this.mostrarMensaje('Error al procesar la solicitud', 'error');
        }
    }
    
    mostrarMensaje(mensaje, tipo = 'info') {
        // Implementar lógica para mostrar mensajes al usuario
        // Por ejemplo, usando un toast o una notificación
        console.log(`[${tipo.toUpperCase()}] ${mensaje}`);
        
        // Opcional: Mostrar un toast o notificación en la interfaz
        const toast = document.createElement('div');
        toast.className = `fixed bottom-4 right-4 px-4 py-2 rounded-md text-white ${
            tipo === 'error' ? 'bg-red-500' : 
            tipo === 'success' ? 'bg-green-500' : 
            'bg-blue-500'
        }`;
        toast.textContent = mensaje;
        document.body.appendChild(toast);
        
        // Eliminar el mensaje después de 3 segundos
        setTimeout(() => {
            toast.remove();
        }, 3000);
    }

    getNotificationIcon(tipo) {
        const iconMap = {
            'prestamo_aprobado': 'fa-check-circle',
            'prestamo_rechazado': 'fa-times-circle',
            'recordatorio': 'fa-bell',
            'sistema': 'fa-info-circle',
            'default': 'fa-bell'
        };
        return iconMap[tipo] || iconMap['default'];
    }
    
    formatearFecha(fechaStr) {
        const fecha = new Date(fechaStr);
        const ahora = new Date();
        const diffEnMinutos = Math.floor((ahora - fecha) / (1000 * 60));
        
        if (diffEnMinutos < 1) return 'Hace unos segundos';
        if (diffEnMinutos < 60) return `Hace ${diffEnMinutos} minuto${diffEnMinutos > 1 ? 's' : ''}`;
        
        const diffEnHoras = Math.floor(diffEnMinutos / 60);
        if (diffEnHoras < 24) return `Hace ${diffEnHoras} hora${diffEnHoras > 1 ? 's' : ''}`;
        
        const diffEnDias = Math.floor(diffEnHoras / 24);
        if (diffEnDias < 7) return `Hace ${diffEnDias} día${diffEnDias > 1 ? 's' : ''}`;
        
        return fecha.toLocaleDateString('es-ES', {
            day: '2-digit',
            month: '2-digit',
            year: '2-digit'
        });
    }
}

// Hacer que la clase Notificaciones esté disponible globalmente
window.Notificaciones = Notificaciones;

// Inicializar cuando el DOM esté listo
function inicializarNotificaciones() {
    if (document.getElementById('notificaciones-container')) {
        window.notificaciones = new Notificaciones();
    }
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', inicializarNotificaciones);
} else {
    // El DOM ya está listo
    inicializarNotificaciones();
}