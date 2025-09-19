<?php
// Iniciar el buffer de salida para capturar el contenido de la vista
ob_start();
?>

<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white shadow-xl rounded-2xl overflow-hidden">
        <!-- Header with tabs -->
        <div class="border-b border-gray-200">
            <div class="px-6 py-4 flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Centro de Notificaciones</h1>
                    <p class="mt-1 text-sm text-gray-500">Revisa tus notificaciones recientes</p>
                </div>
                <div class="flex space-x-2">
                    <button type="button" id="marcar-todas-leidas" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-check-double mr-2"></i> Marcar todas como leídas
                    </button>
                    <button type="button" id="recargar-notificaciones" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-sync-alt mr-2"></i> Actualizar
                    </button>
                </div>
            </div>
            
            <!-- Tabs -->
            <div class="px-6 border-b border-gray-200">
                <nav class="-mb-px flex space-x-8">
                    <a href="#" class="border-blue-500 text-blue-600 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        Todas
                    </a>
                    <a href="#" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        No leídas
                    </a>
                    <a href="#" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        Sistema
                    </a>
                    <a href="#" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        Préstamos
                    </a>
                </nav>
            </div>
        </div>

        <!-- Notifications List -->
        <div class="divide-y divide-gray-200">
            <?php if (empty($data['notificaciones'])): ?>
                <div class="p-12 text-center">
                    <i class="fas fa-bell-slash text-4xl text-gray-300 mb-3"></i>
                    <h3 class="text-lg font-medium text-gray-900">No hay notificaciones</h3>
                    <p class="mt-1 text-sm text-gray-500">Cuando tengas notificaciones, aparecerán aquí.</p>
                </div>
            <?php else: ?>
                <?php foreach ($data['notificaciones'] as $notificacion): 
                    $icon = 'bell';
                    $iconColor = 'bg-blue-100 text-blue-600';
                    
                    if (strpos($notificacion->tipo, 'prestamo') !== false) {
                        $icon = 'hand-holding-usd';
                        $iconColor = 'bg-green-100 text-green-600';
                    } elseif (strpos($notificacion->tipo, 'sistema') !== false) {
                        $icon = 'cog';
                        $iconColor = 'bg-purple-100 text-purple-600';
                    } elseif (strpos($notificacion->tipo, 'alerta') !== false) {
                        $icon = 'exclamation-triangle';
                        $iconColor = 'bg-yellow-100 text-yellow-600';
                    }
                ?>
                    <div class="p-5 hover:bg-gray-50 transition-colors duration-150 <?= $notificacion->estado === 'no_leida' ? 'bg-blue-50' : '' ?>" id="notificacion-<?= $notificacion->IDnot ?>">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <span class="h-10 w-10 rounded-full <?= $iconColor ?> flex items-center justify-center">
                                    <i class="fas fa-<?= $icon ?> text-sm"></i>
                                </span>
                            </div>
                            <div class="ml-4 flex-1 min-w-0">
                                <p class="text-base font-medium text-gray-900">
                                    <?= ucfirst(htmlspecialchars($notificacion->tipo)) ?>
                                </p>
                                <p class="text-sm text-gray-600 mt-1">
                                    <?= htmlspecialchars($notificacion->mensaje) ?>
                                </p>
                                <div class="mt-2 flex items-center text-xs text-gray-500">
                                    <i class="far fa-clock mr-1"></i>
                                    <time datetime="<?= $notificacion->fecha_creacion ?>">
                                        <?= htmlspecialchars(date('d/m/Y h:i A', strtotime($notificacion->fecha_creacion))) ?>
                                    </time>
                                    <?php if ($notificacion->IDprestamo): ?>
                                        <span class="mx-2">•</span>
                                        <a href="<?= BASE_URL ?>/usuario/prestamos/<?= $notificacion->IDprestamo ?>" class="text-blue-600 hover:text-blue-800 hover:underline">
                                            Ver préstamo <i class="fas fa-external-link-alt text-xs ml-1"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="ml-4 flex-shrink-0 flex">
                                <?php if ($notificacion->estado === 'no_leida'): ?>
                                    <button type="button" class="marcar-leida text-gray-400 hover:text-gray-500 p-1 rounded-full" data-id="<?= $notificacion->IDnot ?>" title="Marcar como leída">
                                        <i class="fas fa-check-circle"></i>
                                        <span class="sr-only">Marcar como leída</span>
                                    </button>
                                <?php endif; ?>
                                <button type="button" class="ml-2 text-gray-400 hover:text-red-500 p-1 rounded-full eliminar-notificacion" data-id="<?= $notificacion->IDnot ?>" title="Eliminar notificación">
                                    <i class="far fa-trash-alt"></i>
                                    <span class="sr-only">Eliminar notificación</span>
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Pagination -->
        <?php if (isset($data['totalPaginas']) && $data['totalPaginas'] > 1): ?>
            <div class="px-6 py-3 border-t border-gray-200">
                <nav class="flex items-center justify-between" aria-label="Pagination">
                    <div class="flex-1 flex justify-between sm:justify-end">
                        <?php if ($data['paginaActual'] > 1): ?>
                            <a href="?pagina=<?= $data['paginaActual'] - 1 ?>" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                Anterior
                            </a>
                        <?php else: ?>
                            <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-300 bg-white cursor-not-allowed">
                                Anterior
                            </span>
                        <?php endif; ?>
                        
                        <div class="ml-3 flex items-center">
                            <span class="text-sm text-gray-700">
                                Página <span class="font-medium"><?= $data['paginaActual'] ?></span> de <span class="font-medium"><?= $data['totalPaginas'] ?></span>
                            </span>
                        </div>
                        
                        <?php if ($data['paginaActual'] < $data['totalPaginas']): ?>
                            <a href="?pagina=<?= $data['paginaActual'] + 1 ?>" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                Siguiente
                            </a>
                        <?php else: ?>
                            <span class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-300 bg-white cursor-not-allowed">
                                Siguiente
                            </span>
                        <?php endif; ?>
                    </div>
                </nav>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    function showToast(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = `fixed top-5 right-5 px-6 py-3 rounded-lg shadow-lg z-50 flex items-center text-white ${
            type === 'success' ? 'bg-green-500' : 'bg-red-500'
        }`;
        toast.innerHTML = `<i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'} mr-2"></i><span>${message}</span>`;
        document.body.appendChild(toast);
        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transition = 'opacity 0.5s';
            setTimeout(() => toast.remove(), 500);
        }, 3000);
    }

    async function marcarComoLeida(notificacionId) {
        try {
            const response = await fetch(`<?= BASE_URL ?>/notificacion/marcarLeida/${notificacionId}`, { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
            const data = await response.json();
            if (data.status === 'success') {
                const notificacion = document.getElementById(`notificacion-${notificacionId}`);
                if (notificacion) {
                    notificacion.classList.remove('bg-blue-50');
                    const marcarBtn = notificacion.querySelector('.marcar-leida');
                    if (marcarBtn) marcarBtn.remove();
                    const contador = document.getElementById('notification-count');
                    if (contador) {
                        const count = parseInt(contador.textContent) - 1;
                        contador.textContent = count > 0 ? count : '';
                        if (count <= 0) contador.classList.add('hidden');
                    }
                    showToast('Notificación marcada como leída', 'success');
                }
            } else { throw new Error(data.message || 'Error al marcar como leída'); }
        } catch (error) {
            console.error('Error:', error);
            showToast(error.message || 'Error al procesar la solicitud', 'error');
        }
    }
    
    async function eliminarNotificacion(notificacionId) {
        if (!confirm('¿Estás seguro de que deseas eliminar esta notificación?')) return;
        try {
            const response = await fetch(`<?= BASE_URL ?>/notificacion/eliminar/${notificacionId}`, { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
            const data = await response.json();
            if (data.status === 'success') {
                const notificacion = document.getElementById(`notificacion-${notificacionId}`);
                if (notificacion) {
                    notificacion.style.opacity = '0';
                    notificacion.style.transition = 'opacity 0.3s';
                    setTimeout(() => {
                        notificacion.remove();
                        if (!document.querySelector('[id^="notificacion-"]')) {
                            const container = document.querySelector('.divide-y');
                            container.innerHTML = `<div class="p-12 text-center"><i class="fas fa-bell-slash text-4xl text-gray-300 mb-3"></i><h3 class="text-lg font-medium text-gray-900">No hay notificaciones</h3><p class="mt-1 text-sm text-gray-500">Cuando tengas notificaciones, aparecerán aquí.</p></div>`;
                        }
                    }, 300);
                    showToast('Notificación eliminada', 'success');
                }
            } else { throw new Error(data.message || 'Error al eliminar'); }
        } catch (error) {
            console.error('Error:', error);
            showToast(error.message || 'Error al procesar la solicitud', 'error');
        }
    }
    
    async function marcarTodasLeidas() {
        const boton = document.getElementById('marcar-todas-leidas');
        const textoOriginal = boton.innerHTML;
        try {
            boton.disabled = true;
            boton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Procesando...';
            const response = await fetch('<?= BASE_URL ?>/notificacion/marcarTodasLeidas', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
            const data = await response.json();
            if (data.status === 'success') {
                document.querySelectorAll('[id^="notificacion-"]').forEach(notif => {
                    notif.classList.remove('bg-blue-50');
                    const btn = notif.querySelector('.marcar-leida');
                    if (btn) btn.remove();
                });
                const contador = document.getElementById('notification-count');
                if (contador) {
                    contador.textContent = '';
                    contador.classList.add('hidden');
                }
                showToast('Todas las notificaciones marcadas como leídas', 'success');
            } else { throw new Error(data.message || 'Error al marcar como leídas'); }
        } catch (error) {
            console.error('Error:', error);
            showToast(error.message || 'Error al procesar la solicitud', 'error');
        } finally {
            boton.disabled = false;
            boton.innerHTML = textoOriginal;
        }
    }
    
    document.addEventListener('click', function(e) {
        if (e.target.closest('.marcar-leida')) {
            marcarComoLeida(e.target.closest('.marcar-leida').dataset.id);
        }
        if (e.target.closest('.eliminar-notificacion')) {
            eliminarNotificacion(e.target.closest('.eliminar-notificacion').dataset.id);
        }
    });
    
    document.getElementById('marcar-todas-leidas').addEventListener('click', marcarTodasLeidas);
    document.getElementById('recargar-notificaciones').addEventListener('click', () => window.location.reload());
});
</script>

<?php
// Capturar el contenido del buffer y guardarlo en la variable $content
$content = ob_get_clean();

// Incluir el layout principal que ahora usará la variable $content
require_once __DIR__ . '/../layouts/user_panel_layout.php';
?>