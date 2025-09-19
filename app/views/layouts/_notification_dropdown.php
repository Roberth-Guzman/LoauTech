<?php if (isset($_SESSION['user_id'])): ?>
    <script>
        // Hacer que la constante PHP BASE_URL esté disponible en JavaScript
        window.BASE_URL = '<?= BASE_URL ?>';
    </script>
    <!-- Notifications Dropdown -->
    <div class="relative mr-4">
        <button type="button" 
                class="p-2 text-gray-700 hover:text-blue-600 focus:outline-none relative"
                id="notification-button">
            <i class="fas fa-bell text-xl"></i>
            <span class="absolute -top-1 -right-1 h-5 w-5 rounded-full bg-red-500 text-white text-xs flex items-center justify-center" 
                  id="contador-notificaciones">0</span>
        </button>

        <!-- Dropdown menu -->
        <div class="absolute right-0 z-50 mt-2 w-80 bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5 hidden" 
             id="notification-dropdown">
            <div class="py-1">
                <div class="px-4 py-2 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="text-sm font-medium text-gray-900">Notificaciones</h3>
                    <div>
                        <button type="button" 
                                class="text-xs text-blue-600 hover:text-blue-800"
                                id="marcar-todas-leidas">
                            Marcar todas como leídas
                        </button>
                    </div>
                </div>
                
                <!-- Notifications container -->
                <div class="max-h-96 overflow-y-auto" id="notificaciones-container">
                    <div class="px-4 py-3 text-center text-sm text-gray-500">
                        Cargando notificaciones...
                    </div>
                </div>
                
                <!-- Footer -->
                <div class="border-t border-gray-200 px-4 py-2 text-center">
                    <a href="<?= BASE_URL ?>/usuario/notificaciones" class="text-xs text-blue-600 hover:text-blue-800">
                        Ver todas las notificaciones
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Cargar el script de notificaciones dinámicamente
        function cargarScriptNotificaciones() {
            return new Promise((resolve, reject) => {
                console.log('Iniciando carga de script de notificaciones...');
                
                // Verificar si el script ya está cargado
                if (window.Notificaciones) {
                    console.log('Script de notificaciones ya cargado');
                    resolve();
                    return;
                }
                
                const script = document.createElement('script');
                script.src = '<?= BASE_URL ?>/public/js/notificaciones.js';
                script.onload = function() {
                    console.log('Script de notificaciones cargado correctamente');
                    resolve();
                };
                script.onerror = function(error) {
                    console.error('Error al cargar el script de notificaciones:', error);
                    reject(new Error('Error al cargar el script de notificaciones'));
                };
                
                console.log('Añadiendo script al documento...');
                document.head.appendChild(script);
            });
        }

        // Función para inicializar las notificaciones
        function inicializarNotificaciones() {
            console.log('Inicializando notificaciones...');
            
            // Verificar que los elementos del DOM existan
            const notificacionesContainer = document.getElementById('notificaciones-container');
            const notificationButton = document.getElementById('notification-button');
            const notificationDropdown = document.getElementById('notification-dropdown');
            
            if (!notificacionesContainer || !notificationButton || !notificationDropdown) {
                console.error('No se encontraron todos los elementos necesarios para las notificaciones');
                return;
            }
            
            console.log('Elementos del DOM encontrados, creando instancia de Notificaciones...');
            
            try {
                window.notificaciones = new Notificaciones();
                console.log('Notificaciones inicializadas correctamente');
                
                // Forzar la carga inicial de notificaciones
                window.notificaciones.cargarNotificaciones();
            } catch (error) {
                console.error('Error al inicializar notificaciones:', error);
            }
        }

        // Inicializar cuando el DOM esté listo
        document.addEventListener('DOMContentLoaded', async function() {
            console.log('DOM completamente cargado, inicializando notificaciones...');
            
            try {
                await cargarScriptNotificaciones();
                inicializarNotificaciones();
            } catch (error) {
                console.error('Error durante la inicialización de notificaciones:', error);
                
                // Mostrar mensaje de error en la interfaz
                const container = document.getElementById('notificaciones-container');
                if (container) {
                    container.innerHTML = `
                        <div class="p-4 text-center text-sm text-red-600">
                            Error al cargar las notificaciones. Por favor, recarga la página.
                        </div>`;
                }
            }
        });
    </script>
    
    <style>
        /* Custom scrollbar for notifications */
        #notificaciones-container::-webkit-scrollbar {
            width: 6px;
        }
        #notificaciones-container::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        #notificaciones-container::-webkit-scrollbar-thumb {
            background: #cbd5e0;
            border-radius: 10px;
        }
        #notificaciones-container::-webkit-scrollbar-thumb:hover {
            background: #a0aec0;
        }
        
        /* Animation for new notifications */
        @keyframes newNotification {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
        
        .new-notification {
            animation: newNotification 0.5s ease-in-out;
        }
    </style>
<?php endif; ?>
