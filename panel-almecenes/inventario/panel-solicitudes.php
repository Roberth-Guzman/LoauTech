<?php
// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario'])) {
    header('Location: /loautech-main/login.php');
    exit();
}

// Incluir el archivo de conexión a la base de datos
require_once $_SERVER['DOCUMENT_ROOT'] . '/loautech-main/conexion.php';

// Crear directorio de logs si no existe
$logDir = __DIR__ . '/logs';
if (!file_exists($logDir)) {
    mkdir($logDir, 0777, true);
}

// Función para registrar errores
function logError($message) {
    global $logDir;
    $logMessage = "[" . date('Y-m-d H:i:s') . "] ERROR: " . $message . "\n";
    file_put_contents($logDir . '/error.log', $logMessage, FILE_APPEND);
}

try {
    // Consulta para obtener solicitudes aprobadas
    $sql = "SELECT 
                p.IDpre as id_prestamo,
                p.cantidad,
                p.formacionodependencia,
                p.cargopre,
                p.lugardetraslado,
                per.nombrecompletoper as solicitante,
                per.numerodoc as documento,
                e.nombreele as elemento,
                e.codigoele as codigo_elemento,
                a.estadoaut as estado_autorizacion
            FROM prestamos p
            JOIN personas per ON p.IDpersonas = per.IDper
            JOIN elementos e ON p.IDelementos = e.IDele
            JOIN autorizacion a ON p.IDautorizacion = a.IDaut
            WHERE a.estadoaut = 'activo'
            ORDER BY p.IDpre DESC";

    $result = $conn->query($sql);
    
    if (!$result) {
        throw new Exception("Error en la consulta: " . $conn->error);
    }
    
    // Almacenar los resultados en un array para usarlos más tarde
    $solicitudes = [];
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $solicitudes[] = $row;
        }
    }
    
} catch (Exception $e) {
    logError($e->getMessage());
    $error = "Error al cargar las solicitudes. Por favor, intente más tarde.";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Solicitudes - Loatech</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="min-h-screen">
        <!-- Navbar -->
        <nav class="bg-gray-800 text-white shadow-lg">
            <div class="container mx-auto px-4 py-3 flex justify-between items-center">
                <div class="flex items-center space-x-2">
                    <i class="fas fa-boxes"></i>
                    <span class="font-bold">Gestión de Inventario</span>
                </div>
                <div class="hidden md:flex space-x-6">
                    <a href="panel-solicitudes.php" class="px-3 py-2 rounded-md text-white bg-blue-700 hover:bg-blue-800 transition-colors">
                        <i class="fas fa-clipboard-list mr-1"></i> Solicitudes
                    </a>
                    <a href="elementos/panel-inventario.php" class="px-3 py-2 rounded-md text-white hover:bg-gray-700 transition-colors">
                        <i class="fas fa-boxes mr-1"></i> Inventario
                    </a>
                    <a href="visualizacion-inventario.php" class="px-3 py-2 rounded-md text-white hover:bg-gray-700 transition-colors">
                        <i class="fas fa-eye mr-1"></i> Visualización
                    </a>
                </div>
                <!-- User Profile Dropdown -->
                <div class="relative ml-4">
                    <button id="user-menu-button" class="flex items-center space-x-2 text-white hover:bg-gray-700 px-3 py-2 rounded-md">
                        <i class="fas fa-user-circle text-xl"></i>
                        <span class="hidden md:inline"><?= htmlspecialchars($_SESSION['usuario']['nombre'] ?? 'Usuario') ?></span>
                        <i class="fas fa-chevron-down text-xs ml-1"></i>
                    </button>
                    <div id="user-dropdown" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
                        <a href="perfil-almacenes.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-user-circle mr-2"></i> Mi Perfil
                        </a>
                        <a href="cambiar-contrasena.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-key mr-2"></i> Cambiar Contraseña
                        </a>
                        <div class="border-t border-gray-100 my-1"></div>
                        <a href="../../logout.php" class="block px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                            <i class="fas fa-sign-out-alt mr-2"></i> Cerrar Sesión
                        </a>
                    </div>
                </div>
            </div>
        </nav>

        <div class="container mx-auto px-4 py-6">
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="bg-gradient-to-r from-blue-600 to-purple-700 text-white px-6 py-4">
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-clipboard-check"></i>
                        <h2 class="text-xl font-bold">Solicitudes Aprobadas</h2>
                    </div>
                </div>
                
                <!-- Tabs de navegación -->
                <div class="border-b border-gray-200 px-6 pt-4">
                    <nav class="flex space-x-2">
                        <button class="py-2 px-4 border-b-2 font-medium text-sm border-blue-500 text-blue-600">
                            Solicitudes Pendientes
                        </button>
                    </nav>
                </div>

                <div class="p-6">
                    <?php if (isset($error)): ?>
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline"><?php echo $error; ?></span>
                        </div>
                    <?php elseif (!empty($solicitudes)): ?>
                        <div class="space-y-4">
                            <?php foreach($solicitudes as $solicitud): ?>
                                <div class="border border-gray-200 rounded-lg overflow-hidden">
                                    <div class="bg-gray-800 text-white px-4 py-3 flex justify-between items-center">
                                        <h3 class="font-bold">Solicitud #<?php echo $solicitud['id_prestamo']; ?> - <?php echo htmlspecialchars($solicitud['solicitante']); ?></h3>
                                        <?php if ($solicitud['estado_autorizacion'] === 'activo'): ?>
                                            <div class="flex gap-2">
                                                <button onclick="openModal('approveModal', <?php echo $solicitud['id_prestamo']; ?>)" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded font-semibold"><i class="fas fa-check"></i> Aprobar</button>
                                                <button onclick="openModal('rejectModal', <?php echo $solicitud['id_prestamo']; ?>)" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded font-semibold"><i class="fas fa-times"></i> Rechazar</button>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="p-4">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <h4 class="font-semibold text-gray-700">Información del Solicitante</h4>
                                                <p class="text-sm"><strong>Documento:</strong> <?php echo htmlspecialchars($solicitud['documento']); ?></p>
                                                <p class="text-sm"><strong>Cargo:</strong> <?php echo htmlspecialchars($solicitud['cargopre']); ?></p>
                                                <p class="text-sm"><strong>Formación/Dependencia:</strong> <?php echo htmlspecialchars($solicitud['formacionodependencia']); ?></p>
                                                <p class="text-sm"><strong>Lugar de traslado:</strong> <?php echo htmlspecialchars($solicitud['lugardetraslado']); ?></p>
                                            </div>
                                            <div>
                                                <h4 class="font-semibold text-gray-700">Detalles del Préstamo</h4>
                                                <p class="text-sm"><strong>Elemento:</strong> <?php echo htmlspecialchars($solicitud['elemento']); ?></p>
                                                <p class="text-sm"><strong>Código:</strong> <?php echo htmlspecialchars($solicitud['codigo_elemento']); ?></p>
                                                <p class="text-sm"><strong>Cantidad:</strong> <?php echo $solicitud['cantidad']; ?></p>
                                                <p class="text-sm"><strong>ID de solicitud:</strong> #<?php echo $solicitud['id_prestamo']; ?></p>
                                                <p class="text-sm"><strong>Estado:</strong> <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs"><?php echo htmlspecialchars($solicitud['estado_autorizacion']); ?></span></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <div class="text-center py-8">
                            <i class="fas fa-inbox text-4xl text-gray-400 mb-2"></i>
                            <p class="text-gray-500">No hay solicitudes aprobadas para mostrar.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <!-- MODALES -->
    <!-- Modal de Aprobar -->
    <div id="approveModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-black bg-opacity-50">
      <div class="flex items-center justify-center min-h-screen">
        <div class="bg-white rounded-lg p-6 w-full max-w-lg">
          <h3 class="text-lg font-bold mb-4">Confirmar Aprobación</h3>
          <p class="mb-4">¿Está seguro que desea aprobar esta solicitud?</p>
          <input type="hidden" id="approveRequestId">
          <div class="flex justify-end space-x-3">
            <button type="button" onclick="closeModal('approveModal')" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">Cancelar</button>
            <button type="button" id="confirmApproveBtn" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">Aprobar</button>
          </div>
        </div>
      </div>
    </div>
    <!-- Modal de Rechazar -->
    <div id="rejectModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-black bg-opacity-50">
      <div class="flex items-center justify-center min-h-screen">
        <div class="bg-white rounded-lg p-6 w-full max-w-lg">
          <h3 class="text-lg font-bold mb-4">Rechazar Solicitud</h3>
          <input type="hidden" id="rejectRequestId">
          <div class="mb-4">
            <label for="rejectReason" class="block text-sm font-medium text-gray-700 mb-1">Motivo del rechazo:</label>
            <textarea id="rejectReason" rows="4" class="w-full border border-gray-300 rounded-md p-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Por favor, indique el motivo del rechazo..." required></textarea>
          </div>
          <div class="flex justify-end space-x-3">
            <button type="button" onclick="closeModal('rejectModal')" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">Cancelar</button>
            <button type="button" id="confirmRejectBtn" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">Rechazar</button>
          </div>
        </div>
      </div>
    </div>
    <!-- Toast de notificación -->
    <div id="toast" class="fixed bottom-4 right-4 bg-gray-800 text-white px-4 py-2 rounded-lg shadow-lg hidden">
      <div class="flex items-center">
        <span id="toastMessage">Mensaje de notificación</span>
        <button onclick="document.getElementById('toast').classList.add('hidden')" class="ml-4 text-gray-300 hover:text-white">
          <i class="fas fa-times"></i>
        </button>
      </div>
    </div>
    <script>
        // Toggle user dropdown menu
        const userMenuButton = document.getElementById('user-menu-button');
        const userDropdown = document.getElementById('user-dropdown');

        userMenuButton.addEventListener('click', () => {
            userDropdown.classList.toggle('hidden');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (!userMenuButton.contains(e.target) && !userDropdown.contains(e.target)) {
                userDropdown.classList.add('hidden');
            }
        });
    </script>
    <script>
    // Variables globales
    let currentRequestId = null;
    let isProcessing = false;

    document.addEventListener('DOMContentLoaded', function() {
      document.getElementById('confirmApproveBtn').addEventListener('click', function() {
        if (currentRequestId && !isProcessing) {
          approveRequest(currentRequestId);
        }
      });
      document.getElementById('confirmRejectBtn').addEventListener('click', function() {
        if (currentRequestId && !isProcessing) {
          const motivo = document.getElementById('rejectReason').value.trim();
          if (!motivo) {
            showToast('Por favor ingrese el motivo del rechazo', 'error');
            return;
          }
          rejectRequest(currentRequestId, motivo);
        }
      });
    });
    function openModal(modalId, requestId) {
      currentRequestId = requestId;
      const modal = document.getElementById(modalId);
      if (modal) {
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
      }
    }
    function closeModal(modalId) {
      if (isProcessing) return;
      const modal = document.getElementById(modalId);
      if (modal) {
        modal.classList.add('hidden');
        document.body.style.overflow = '';
      }
      if (modalId === 'rejectModal') {
        document.getElementById('rejectReason').value = '';
      }
    }
    function showToast(message, type = 'info') {
      const toast = document.getElementById('toast');
      const toastMessage = document.getElementById('toastMessage');
      if (toast && toastMessage) {
        switch(type) {
          case 'success':
            toast.className = 'fixed bottom-4 right-4 bg-green-600 text-white px-4 py-2 rounded-lg shadow-lg flex items-center';
            break;
          case 'error':
            toast.className = 'fixed bottom-4 right-4 bg-red-600 text-white px-4 py-2 rounded-lg shadow-lg flex items-center';
            break;
          case 'warning':
            toast.className = 'fixed bottom-4 right-4 bg-yellow-500 text-white px-4 py-2 rounded-lg shadow-lg flex items-center';
            break;
          default:
            toast.className = 'fixed bottom-4 right-4 bg-gray-800 text-white px-4 py-2 rounded-lg shadow-lg flex items-center';
        }
        toastMessage.textContent = message;
        toast.classList.remove('hidden');
        setTimeout(() => {
          toast.classList.add('hidden');
        }, 5000);
      }
    }
    function approveRequest(id) {
      if (isProcessing) return;
      isProcessing = true;
      const btn = document.getElementById('confirmApproveBtn');
      const originalText = btn.innerHTML;
      btn.disabled = true;
      btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Procesando...';
      // Crear formulario dinámico
      const form = document.createElement('form');
      form.method = 'POST';
      form.action = 'procesar_aprobacion.php';
      form.style.display = 'none';
      const idInput = document.createElement('input');
      idInput.type = 'hidden';
      idInput.name = 'id';
      idInput.value = id;
      form.appendChild(idInput);
      // Agregar campo 'accion' con valor 'aprobar'
      const accionInput = document.createElement('input');
      accionInput.type = 'hidden';
      accionInput.name = 'accion';
      accionInput.value = 'aprobar';
      form.appendChild(accionInput);
      document.body.appendChild(form);
      form.submit();
    }
    function rejectRequest(id, motivo) {
      if (isProcessing) return;
      isProcessing = true;
      const btn = document.getElementById('confirmRejectBtn');
      const originalText = btn.innerHTML;
      btn.disabled = true;
      btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Procesando...';
      try {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = 'procesar_aprobacion.php';
        form.style.display = 'none';
        const idInput = document.createElement('input');
        idInput.type = 'hidden';
        idInput.name = 'id';
        idInput.value = id;
        form.appendChild(idInput);
        const motivoInput = document.createElement('input');
        motivoInput.type = 'hidden';
        motivoInput.name = 'motivo';
        motivoInput.value = motivo;
        form.appendChild(motivoInput);
        const accionInput = document.createElement('input');
        accionInput.type = 'hidden';
        accionInput.name = 'accion';
        accionInput.value = 'rechazar';
        form.appendChild(accionInput);
        document.body.appendChild(form);
        form.submit();
      } catch (error) {
        isProcessing = false;
        btn.disabled = false;
        btn.innerHTML = originalText;
        showToast('Error al procesar la solicitud. Por favor, intente nuevamente.', 'error');
      }
    }
    </script>
</body>
</html>