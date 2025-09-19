// Importar funciones de exportación
import { exportToPDF, exportToExcel } from './export-functions.js';

document.addEventListener('DOMContentLoaded', function() {
    // Inicializar variables
    const modal = document.getElementById('modal-detalles');
    const btnExportarPDF = document.getElementById('btn-exportar-pdf');
    const btnExportarExcel = document.getElementById('btn-exportar-excel');
    const buscarInput = document.getElementById('buscar-codigo');
    const contadorRegistros = document.getElementById('contador-registros');
    const fechaFiltro = document.getElementById('fecha');
    const tipoElementoFiltro = document.getElementById('tipo-elemento');
    const formFiltro = document.getElementById('filtro-fecha');
    
    // Configurar eventos
    configurarEventos();
    actualizarHoraGeneracion();
    
    // Actualizar la hora cada minuto
    setInterval(actualizarHoraGeneracion, 60000);
    
    /**
     * Actualiza la hora de generación en la interfaz
     */
    function actualizarHoraGeneracion() {
        const ahora = new Date();
        const opciones = { 
            hour: '2-digit', 
            minute: '2-digit',
            hour12: true,
            timeZone: 'America/Bogota'
        };
        const horaElemento = document.getElementById('hora-generacion');
        if (horaElemento) {
            horaElemento.textContent = ahora.toLocaleTimeString('es-ES', opciones);
        }
    }
    
    /**
     * Inicializa las fechas en los filtros
     */
    function inicializarFechas() {
        const hoy = new Date().toISOString().split('T')[0];
        
        // Establecer fecha actual si no hay valor
        if (fechaFiltro && !fechaFiltro.value) {
            fechaFiltro.value = hoy;
        }
        
        // Establecer valor máximo como hoy
        if (fechaFiltro) {
            fechaFiltro.max = hoy;
        }
    }
    
    /**
     * Configura los eventos de la aplicación
     */
    function configurarEventos() {
        // Búsqueda en tiempo real
        if (buscarInput) {
            buscarInput.addEventListener('input', function() {
                const busqueda = this.value.trim().toLowerCase();
                const elementos = document.querySelectorAll('.elemento-card');
                let visibles = 0;
                
                elementos.forEach(elemento => {
                    const textoElemento = [
                        elemento.getAttribute('data-codigo') || '',
                        elemento.getAttribute('data-tipo') || '',
                        elemento.getAttribute('data-marca') || '',
                        elemento.getAttribute('data-modelo') || ''
                    ].join(' ').toLowerCase();
                    
                    if (textoElemento.includes(busqueda)) {
                        elemento.style.display = '';
                        visibles++;
                    } else {
                        elemento.style.display = 'none';
                    }
                });
                
                // Actualizar contador
                if (contadorRegistros) {
                    contadorRegistros.textContent = `(${visibles} de ${elementos.length} elementos)`;
                }
            });
        }
        
        // Configurar modal de detalles
        configurarModalDetalles();
        
        // Configurar exportación
        if (btnExportarPDF) {
            btnExportarPDF.addEventListener('click', exportToPDF);
        }
        
        if (btnExportarExcel) {
            btnExportarExcel.addEventListener('click', exportToExcel);
        }
        
        // Configurar envío del formulario
        if (formFiltro) {
            formFiltro.addEventListener('submit', function(e) {
                e.preventDefault();
                // Aquí podrías agregar lógica adicional antes de enviar el formulario
                this.submit();
            });
        }
    }
    
    /**
     * Configura el modal de detalles
     */
    function configurarModalDetalles() {
        const btnCerrar = document.getElementById('cerrar-modal');
        const btnCerrar2 = document.getElementById('btn-cerrar-modal');
        
        if (!modal) return;
        
        // Manejador para el botón de ver detalles
        document.addEventListener('click', function(e) {
            const boton = e.target.closest('.ver-detalle');
            if (boton) {
                e.preventDefault();
                mostrarDetalles(boton);
            }
        });
        
        /**
         * Muestra el modal con los detalles del elemento
         * @param {HTMLElement} boton - Botón que activó el modal
         */
        function mostrarDetalles(boton) {
            try {
                // Obtener datos de los atributos data
                const codigo = boton.getAttribute('data-codigo') || 'N/A';
                const tipo = boton.getAttribute('data-tipo') || 'No especificado';
                const marca = boton.getAttribute('data-marca') || 'No especificada';
                const modelo = boton.getAttribute('data-modelo') || 'No especificado';
                const hora = boton.getAttribute('data-hora') || 'No registrada';
                const estado = boton.getAttribute('data-estado') || 'Desconocido';
                const observaciones = boton.getAttribute('data-observaciones') || 'No hay observaciones registradas.';
                const nombrePersona = boton.getAttribute('data-nombre-persona') || 'No disponible';
                const documentoPersona = boton.getAttribute('data-documento-persona') || 'No disponible';
                
                // Actualizar el modal con los datos
                actualizarElemento('detalle-codigo', codigo);
                actualizarElemento('detalle-tipo', tipo);
                actualizarElemento('detalle-marca', marca);
                actualizarElemento('detalle-modelo', modelo);
                actualizarElemento('detalle-hora', hora);
                actualizarElemento('detalle-observaciones', observaciones);
                actualizarElemento('detalle-nombre-persona', nombrePersona);
                actualizarElemento('detalle-documento-persona', documentoPersona);
                
                // Actualizar el estado con el estilo adecuado
                const elementoEstado = document.getElementById('detalle-estado');
                if (elementoEstado) {
                    elementoEstado.textContent = estado;
                    elementoEstado.className = 'px-3 py-1 rounded-full text-xs font-medium';
                    
                    // Establecer clase según el estado
                    const clasesEstado = {
                        'Registrado': 'bg-green-100 text-green-800',
                        'Pendiente': 'bg-yellow-100 text-yellow-800',
                        'Incumplido': 'bg-red-100 text-red-800',
                        'default': 'bg-gray-100 text-gray-800'
                    };
                    
                    const claseEstado = clasesEstado[estado] || clasesEstado.default;
                    elementoEstado.className += ' ' + claseEstado;
                }
                
                // Mostrar el modal con animación
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
                
                // Enfocar el botón de cierre para mejor accesibilidad
                const btnCerrar = modal.querySelector('[data-dismiss="modal"]');
                if (btnCerrar) {
                    setTimeout(() => btnCerrar.focus(), 100);
                }
                
            } catch (error) {
                console.error('Error al mostrar detalles:', error);
                alert('Ocurrió un error al cargar los detalles del registro.');
            }
        }
        
        /**
         * Actualiza el contenido de un elemento del DOM
         * @param {string} id - ID del elemento
         * @param {string} valor - Valor a establecer
         */
        function actualizarElemento(id, valor) {
            const elemento = document.getElementById(id);
            if (elemento) {
                elemento.textContent = valor;
            }
        }
        
        /**
         * Cierra el modal de detalles
         */
        function cerrarModal() {
            if (!modal) return;
            
            modal.classList.add('opacity-0');
            document.body.style.overflow = '';
            
            // Esperar a que termine la animación antes de ocultar
            setTimeout(() => {
                modal.classList.add('hidden');
                modal.classList.remove('opacity-0');
            }, 200);
        }
        
        // Cerrar el modal al hacer clic en los botones de cerrar
        if (btnCerrar) btnCerrar.addEventListener('click', cerrarModal);
        if (btnCerrar2) btnCerrar2.addEventListener('click', cerrarModal);
        
        // Cerrar al hacer clic fuera del modal
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                cerrarModal();
            }
        });
        
        // Cerrar con la tecla ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
                cerrarModal();
            }
        });
    }
    
    // Exportar a PDF
    function configurarExportarPDF() {
        const btnExportarPDF = document.getElementById('btn-exportar-pdf');
        if (!btnExportarPDF) return;
        
        btnExportarPDF.addEventListener('click', function() {
            mostrarCargando('Generando archivo PDF...');
            // Simular generación de PDF
            setTimeout(() => {
                ocultarCargando();
                mostrarMensaje('El archivo PDF se ha generado correctamente.', 'success');
                // Aquí iría la lógica real para generar el PDF
                // window.open('ruta/al/archivo.pdf', '_blank');
            }, 1500);
        });
    }
    
    // Exportar a Excel
    function configurarExportarExcel() {
        const btnExportarExcel = document.getElementById('btn-exportar-excel');
        if (!btnExportarExcel) return;
        
        btnExportarExcel.addEventListener('click', function() {
            mostrarCargando('Generando archivo Excel...');
            // Simular generación de Excel
            setTimeout(() => {
                ocultarCargando();
                mostrarMensaje('El archivo Excel se ha generado correctamente.', 'success');
                // Aquí iría la lógica real para generar el Excel
                // window.open('ruta/al/archivo.xlsx', '_blank');
            }, 1500);
        });
    }
    
    // Mostrar mensaje de carga
    function mostrarCargando(mensaje = 'Cargando...') {
        let cargando = document.getElementById('cargando');
        if (!cargando) {
            cargando = document.createElement('div');
            cargando.id = 'cargando';
            cargando.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
            cargando.innerHTML = `
                <div class="bg-white p-6 rounded-lg shadow-xl text-center">
                    <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-indigo-600 mx-auto mb-4"></div>
                    <p class="text-gray-700">${mensaje}</p>
                </div>
            `;
            document.body.appendChild(cargando);
        } else {
            cargando.querySelector('p').textContent = mensaje;
            cargando.style.display = 'flex';
        }
        
        document.body.style.overflow = 'hidden';
    }
    
    // Ocultar mensaje de carga
    function ocultarCargando() {
        const cargando = document.getElementById('cargando');
        if (cargando) {
            cargando.style.display = 'none';
            document.body.style.overflow = '';
        }
    }
    
    // Mostrar mensaje de notificación
    function mostrarMensaje(mensaje, tipo = 'info') {
        // Eliminar notificaciones existentes
        const notificacionesExistentes = document.querySelectorAll('.notificacion-flotante');
        notificacionesExistentes.forEach(notif => notif.remove());
        
        // Crear elemento de notificación
        const notificacion = document.createElement('div');
        notificacion.className = `notificacion-flotante fixed top-4 right-4 px-6 py-3 rounded-md shadow-lg text-white font-medium z-50 animate-fade-in-up ${tipo === 'success' ? 'bg-green-500' : 'bg-blue-500'}`;
        notificacion.textContent = mensaje;
        
        // Añadir al cuerpo del documento
        document.body.appendChild(notificacion);
        
        // Eliminar después de 3 segundos
        setTimeout(() => {
            notificacion.classList.add('opacity-0', 'translate-y-2', 'transition-all', 'duration-300');
            setTimeout(() => {
                notificacion.remove();
            }, 300);
        }, 3000);
    }
    
    // Inicialización
    inicializarFechas();
    
    // Agregar estilos de animación si no existen
    if (!document.getElementById('estilos-animaciones')) {
        const estilos = document.createElement('style');
        estilos.id = 'estilos-animaciones';
        estilos.textContent = `
            @keyframes fadeInUp {
                from { opacity: 0; transform: translateY(10px); }
                to { opacity: 1; transform: translateY(0); }
            }
            .animate-fade-in-up {
                animation: fadeInUp 0.3s ease-out forwards;
            }
        `;
        document.head.appendChild(estilos);
    }
    
    // Actualizar contador de registros
    if (contadorRegistros) {
        const totalElementos = document.querySelectorAll('.elemento-card').length;
        contadorRegistros.textContent = `(${totalElementos} elementos)`;
    }
});