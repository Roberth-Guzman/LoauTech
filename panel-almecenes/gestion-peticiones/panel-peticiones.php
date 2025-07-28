<?php
// Configuración de errores para desarrollo
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Configurar zona horaria
date_default_timezone_set('America/Bogota');

session_start();

// 1. Validar sesión
if (!isset($_SESSION['usuario'])) {
    header('Location: /loautech-main/login.php'); 
    exit();
}

// 2. Validar rol 'cuentadante'
if ($_SESSION['usuario']['rol'] !== 'cuentadante') {
    header('Location: /loautech-main/login.php?error=acceso_no_autorizado');
    exit();
}

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
    file_put_contents($logDir . '/panel_errores.log', $logMessage, FILE_APPEND);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gestión de Peticiones</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <style>
    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }
    .animate-spin {
      animation: spin 1s linear infinite;
    }
  </style>
</head>
<body class="bg-gray-100">
  <!-- Contenido existente del navbar -->
  <nav class="bg-gray-800 text-white shadow-lg">
    <div class="container mx-auto px-4 py-3 flex justify-between items-center">
      <div class="flex items-center space-x-2">
        <i class="fas fa-boxes"></i>
        <span class="font-bold">Gestión de Peticiones</span>
      </div>
      <div class="flex items-center space-x-4">
        <button id="notificationBtn" class="relative p-2 rounded-full hover:bg-gray-700" onclick="toggleNotifications()">
          <i class="fas fa-bell"></i>
          <span id="notificationCount" class="absolute -top-1 -right-1 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs">
            <?php
            // Contar notificaciones pendientes
            try {
                $sql_count = "SELECT COUNT(*) as total FROM prestamos p 
                            JOIN autorizacion a ON p.IDautorizacion = a.IDaut 
                            WHERE a.estadoaut = 'pendiente' AND a.cargoquienautoriza = 'sistema'";
                $result_count = $conn->query($sql_count);
                $count = $result_count ? $result_count->fetch_assoc()['total'] : 0;
                echo $count;
            } catch (Exception $e) {
                logError("Error contando notificaciones: " . $e->getMessage());
                echo '0';
            }
            ?>
          </span>
        </button>
        
        <!-- Panel de notificaciones -->
        <div id="notificationPanel" class="absolute right-0 top-16 w-80 bg-white rounded-lg shadow-lg border hidden z-50">
          <div class="p-4 border-b">
            <h3 class="font-bold text-gray-800">Notificaciones</h3>
          </div>
          <div class="max-h-64 overflow-y-auto">
            <?php
            try {
                $sql_notif = "SELECT p.IDpre, per.nombrecompletoper, e.nombreele 
                            FROM prestamos p
                            JOIN elementos e ON p.IDelementos = e.IDele
                            JOIN personas per ON p.IDpersonas = per.IDper
                            JOIN autorizacion a ON p.IDautorizacion = a.IDaut
                            WHERE a.estadoaut = 'pendiente' AND a.cargoquienautoriza = 'sistema'
                            ORDER BY p.IDpre DESC LIMIT 5";
                $result_notif = $conn->query($sql_notif);
                
                if ($result_notif && $result_notif->num_rows > 0) {
                    while ($notif = $result_notif->fetch_assoc()) {
                        echo '<div class="p-3 border-b hover:bg-gray-50">';
                        echo '<p class="text-sm font-medium text-gray-800">Nueva petición #' . $notif['IDpre'] . '</p>';
                        echo '<p class="text-xs text-gray-600">' . htmlspecialchars($notif['nombrecompletoper']) . ' - ' . htmlspecialchars($notif['nombreele']) . '</p>';
                        echo '</div>';
                    }
                } else {
                    echo '<div class="p-3 text-center text-gray-500">';
                    echo '<p class="text-sm">No hay notificaciones</p>';
                    echo '</div>';
                }
            } catch (Exception $e) {
                logError("Error cargando notificaciones: " . $e->getMessage());
                echo '<div class="p-3 text-center text-red-500">';
                echo '<p class="text-sm">Error al cargar notificaciones</p>';
                echo '</div>';
            }
            ?>
          </div>
        </div>
        
        <a href="perfil-cuentadante.php" class="flex items-center space-x-2 hover:bg-gray-700 px-3 py-2 rounded">
          <i class="fas fa-user-circle"></i>
          <span>Usuarios</span>
        </a>
      </div>
    </div>
  </nav>

  <div class="container mx-auto px-4 py-6">
    <div class="bg-white rounded-lg shadow overflow-hidden">
      <div class="bg-gradient-to-r from-blue-600 to-purple-700 text-white px-6 py-4">
        <div class="flex items-center justify-between">
          <div class="flex items-center space-x-2">
            <i class="fas fa-clipboard-list"></i>
            <h2 class="text-xl font-bold">Gestión de Peticiones</h2>
          </div>
          <button onclick="refreshPage()" class="text-white hover:text-gray-200">
            <i id="refreshIcon" class="fas fa-sync-alt"></i>
          </button>
        </div>
      </div>
      
      <div class="p-6">
        <!-- Filtros -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
          <select id="estadoFilter" class="border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="">Todos los estados</option>
            <option value="pendiente">Pendiente</option>
            <option value="aprobado">Aprobado</option>
            <option value="rechazado">Rechazado</option>
          </select>
          <input type="text" id="searchInput" placeholder="Buscar por nombre o ID..." 
                 class="border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        
        <!-- Tabla de solicitudes -->
        <div class="overflow-x-auto">
          <table class="min-w-full bg-white">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Solicitante</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Elemento</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cantidad</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Formación/Dependencia</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
              </tr>
            </thead>
            <tbody id="requestsTableBody" class="bg-white divide-y divide-gray-200">
              <?php
              try {
                  // Consulta corregida con los nombres de columna reales de la base de datos
                  $sql = "SELECT p.IDpre, p.formacionodependencia, p.cargopre, p.lugardetraslado,
                                 per.nombrecompletoper, e.nombreele, a.estadoaut, a.cargoquienautoriza,
                                 e.codigoinventario, e.descripcionele, p.cantidad
                          FROM prestamos p
                          JOIN personas per ON p.IDpersonas = per.IDper
                          JOIN elementos e ON p.IDelementos = e.IDele
                          JOIN autorizacion a ON p.IDautorizacion = a.IDaut
                          WHERE a.estadoaut = 'pendiente' 
                          AND a.cargoquienautoriza = 'sistema'
                          ORDER BY p.IDpre DESC";
                  
                  // Registrar la consulta en el log para depuración
                  logError("Consulta SQL: " . $sql);
                  
                  $result = $conn->query($sql);
                  
                  if ($result && $result->num_rows > 0) {
                      while ($row = $result->fetch_assoc()) {
                          // Registrar cada fila para depuración
                          logError("Fila encontrada - ID: " . $row['IDpre'] . ", Estado: " . $row['estadoaut']);
                          
                          echo '<tr class="hover:bg-gray-50" data-id="' . $row['IDpre'] . '" data-estado="' . $row['estadoaut'] . '">';
                          echo '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">' . $row['IDpre'] . '</td>';
                          echo '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">' . htmlspecialchars($row['nombrecompletoper']) . '</td>';
                          echo '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">' . 
                               htmlspecialchars($row['nombreele']) . ' (' . htmlspecialchars($row['codigoinventario']) . ')' . 
                               '<br><span class="text-xs text-gray-500">' . 
                               htmlspecialchars($row['descripcionele']) . 
                               '</span></td>';
                          echo '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">' . 
                               htmlspecialchars($row['cantidad']) . ' unidad(es)' . 
                               '<br><span class="text-xs text-gray-500">' . 
                               htmlspecialchars($row['formacionodependencia']) . 
                               '</span></td>';
                          echo '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">' . htmlspecialchars($row['formacionodependencia']) . '</td>';
                          
                          // Estado
                          $estadoClass = '';
                          $estadoTexto = '';
                          switch(strtolower($row['estadoaut'])) {
                              case 'aprobado':
                                  $estadoClass = 'bg-green-100 text-green-800';
                                  $estadoTexto = 'Aprobado';
                                  break;
                              case 'rechazado':
                                  $estadoClass = 'bg-red-100 text-red-800';
                                  $estadoTexto = 'Rechazado';
                                  break;
                              case 'pendiente':
                              default:
                                  $estadoClass = 'bg-yellow-100 text-yellow-800';
                                  $estadoTexto = 'Pendiente';
                          }
                          
                          echo '<td class="px-6 py-4 whitespace-nowrap">';
                          echo '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ' . $estadoClass . '">';
                          echo $estadoTexto;
                          echo '</span>';
                          echo '</td>';
                          
                          // Acciones
                          echo '<td class="px-6 py-4 whitespace-nowrap text-sm font-medium">';
                          if (strtolower($row['estadoaut']) === 'pendiente') {
                              echo '<button onclick="openModal(\'approveModal\', ' . $row['IDpre'] . ')" ';
                              echo 'class="text-green-600 hover:text-green-900 mr-3">';
                              echo '<i class="fas fa-check"></i> Aprobar';
                              echo '</button>';
                              
                              echo '<button onclick="openModal(\'rejectModal\', ' . $row['IDpre'] . ')" ';
                              echo 'class="text-red-600 hover:text-red-900">';
                              echo '<i class="fas fa-times"></i> Rechazar';
                              echo '</button>';
                          } else {
                              echo '<span class="text-gray-400">No hay acciones disponibles</span>';
                          }
                          echo '</td>';
                          echo '</tr>';
                      }
                  } else {
                      echo '<tr><td colspan="7" class="px-6 py-4 text-center text-gray-500">No se encontraron solicitudes pendientes</td></tr>';
                      logError("No se encontraron solicitudes pendientes en la base de datos");
                  }
              } catch (Exception $e) {
                  $errorMsg = "Error cargando solicitudes: " . $e->getMessage();
                  logError($errorMsg);
                  echo '<tr><td colspan="7" class="px-6 py-4 text-center text-red-500">Error al cargar las solicitudes. Por favor, intente de nuevo más tarde.</td></tr>';
              }
              ?>
            </tbody>
          </table>
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
          <button type="button" onclick="closeModal('approveModal')" 
                  class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
            Cancelar
          </button>
          <button type="button" id="confirmApproveBtn" 
                  class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
            Aprobar
          </button>
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
          <textarea id="rejectReason" rows="4" class="w-full border border-gray-300 rounded-md p-2 focus:ring-blue-500 focus:border-blue-500" 
                    placeholder="Por favor, indique el motivo del rechazo..." required></textarea>
        </div>
        <div class="flex justify-end space-x-3">
          <button type="button" onclick="closeModal('rejectModal')" 
                  class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
            Cancelar
          </button>
          <button type="button" id="confirmRejectBtn" 
                  class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
            Rechazar
          </button>
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
  // Variables globales
  let currentRequestId = null;
  let isProcessing = false;

  // Inicialización
  document.addEventListener('DOMContentLoaded', function() {
    // Configurar manejadores de eventos para los botones de confirmación
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

    // Configurar filtros
    const estadoFilter = document.getElementById('estadoFilter');
    const searchInput = document.getElementById('searchInput');
    
    if (estadoFilter) {
      estadoFilter.addEventListener('change', filterRequests);
    }
    if (searchInput) {
      searchInput.addEventListener('input', filterRequests);
    }
  });

  // Función para abrir modales
  function openModal(modalId, requestId) {
    currentRequestId = requestId;
    const modal = document.getElementById(modalId);
    if (modal) {
      modal.classList.remove('hidden');
      document.body.style.overflow = 'hidden'; // Prevenir scroll del fondo
    }
  }

  // Función para cerrar modales
  function closeModal(modalId) {
    if (isProcessing) return; // Evitar cerrar mientras se procesa
    
    const modal = document.getElementById(modalId);
    if (modal) {
      modal.classList.add('hidden');
      document.body.style.overflow = ''; // Restaurar scroll
    }
    
    // Limpiar campos del formulario de rechazo
    if (modalId === 'rejectModal') {
      document.getElementById('rejectReason').value = '';
    }
  }

  // Función para mostrar notificaciones toast
  function showToast(message, type = 'info') {
    const toast = document.getElementById('toast');
    const toastMessage = document.getElementById('toastMessage');
    
    if (toast && toastMessage) {
      // Configurar colores según el tipo
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
      
      // Ocultar automáticamente después de 5 segundos
      setTimeout(() => {
        toast.classList.add('hidden');
      }, 5000);
    }
  }

  // Función para aprobar solicitud
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
    form.action = 'aprobar_prestamo.php';
    form.style.display = 'none';
    
    // Agregar campo ID con el nombre correcto
    const idInput = document.createElement('input');
    idInput.type = 'hidden';
    idInput.name = 'id';
    idInput.value = id;
    form.appendChild(idInput);
    
    // Agregar al documento y enviar
    document.body.appendChild(form);
    form.submit();
  }

  // Función para rechazar solicitud
  function rejectRequest(id, motivo) {
    if (isProcessing) return;
    isProcessing = true;
    
    console.log('Iniciando rechazo de solicitud:', { id, motivo });
    
    const btn = document.getElementById('confirmRejectBtn');
    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Procesando...';
    
    try {
      // Crear formulario dinámico
      const form = document.createElement('form');
      form.method = 'POST';
      form.action = 'rechazar_prestamo.php';
      form.style.display = 'none';
      
      // Agregar campos
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
      
      // Agregar al documento
      document.body.appendChild(form);
      
      console.log('Formulario creado:', {
        action: form.action,
        method: form.method,
        data: {
          id_prestamo: id,
          motivo: motivo
        }
      });
      
      // Enviar formulario
      form.submit();
    } catch (error) {
      console.error('Error al enviar el formulario:', error);
      isProcessing = false;
      btn.disabled = false;
      btn.innerHTML = originalText;
      alert('Error al procesar la solicitud. Por favor, intente nuevamente.');
    }
  }

  // Función para filtrar solicitudes en la tabla
  function filterRequests() {
    const estado = document.getElementById('estadoFilter').value.toLowerCase();
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const rows = document.querySelectorAll('#requestsTableBody tr');
    
    rows.forEach(row => {
      const estadoRow = row.getAttribute('data-estado') || '';
      const textContent = row.textContent.toLowerCase();
      
      const estadoMatch = !estado || estadoRow === estado;
      const searchMatch = !searchTerm || textContent.includes(searchTerm);
      
      if (estadoMatch && searchMatch) {
        row.style.display = '';
      } else {
        row.style.display = 'none';
      }
    });
  }

  // Función para refrescar la página
  function refreshPage() {
    const icon = document.getElementById('refreshIcon');
    if (icon) {
      icon.classList.add('animate-spin');
      setTimeout(() => location.reload(), 500);
    } else {
      location.reload();
    }
  }

  // Función para mostrar/ocultar panel de notificaciones
  function toggleNotifications() {
    const panel = document.getElementById('notificationPanel');
    if (panel) {
      panel.classList.toggle('hidden');
    }
  }

  // Cerrar panel de notificaciones al hacer clic fuera
  document.addEventListener('click', function(event) {
    const panel = document.getElementById('notificationPanel');
    const btn = document.getElementById('notificationBtn');
    
    if (panel && btn && !panel.contains(event.target) && !btn.contains(event.target)) {
      panel.classList.add('hidden');
    }
  });
  </script>
</body>
</html>
