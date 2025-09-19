<?php
class NotificacionController extends Controller {
    private $notificacionModel;

    public function __construct() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // Asegurarse de que el usuario esté logueado
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/login');
            exit();
        }

        $this->notificacionModel = $this->model('Notificacion');
    }

    /**
     * Obtiene las notificaciones del usuario actual
     * Ruta: GET /notificacion/obtenerNotificaciones
     */
    public function obtenerNotificaciones() {
        header('Content-Type: application/json');
        
        try {
            error_log('Solicitud de notificaciones recibida');
            
            if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
                error_log('Error: Método no permitido');
                throw new Exception('Método no permitido', 405);
            }

            if (!isset($_SESSION['user_id'])) {
                error_log('Error: Usuario no autenticado');
                throw new Exception('Usuario no autenticado', 401);
            }
            
            $userId = $_SESSION['user_id'];
            error_log("Obteniendo notificaciones para el usuario ID: $userId");

            // Obtener notificaciones del usuario
            $notificaciones = $this->notificacionModel->obtenerNotificacionesUsuario(
                $userId,
                0, // offset
                10 // límite
            );
            
            if ($notificaciones === false) {
                throw new Exception('Error al obtener las notificaciones');
            }
            
            // Contar notificaciones no leídas
            $sinLeer = $this->notificacionModel->contarNotificacionesNoLeidas($userId);
            
            error_log("Notificaciones encontradas: " . count($notificaciones) . ", sin leer: $sinLeer");
            
            // Formatear fechas para mejor legibilidad
            $notificacionesFormateadas = array_map(function($notif) {
                $notif->fecha_formateada = $this->formatearFecha($notif->fecha_creacion);
                error_log("Notificación ID: {$notif->IDnot}, Tipo: {$notif->tipo}, Estado: {$notif->estado}");
                return $notif;
            }, $notificaciones);
            
            $response = [
                'success' => true,
                'notificaciones' => $notificacionesFormateadas,
                'sinLeer' => $sinLeer,
                'timestamp' => date('Y-m-d H:i:s')
            ];
            
            error_log('Enviando respuesta: ' . json_encode($response));
            
            echo json_encode($response);
            exit;
            
        } catch (Exception $e) {
            $this->responderJSON([
                'success' => false,
                'error' => $e->getMessage()
            ], $e->getCode() ?: 500);
        }
    }

    /**
     * Marca una notificación como leída
     * Ruta: POST /notificacion/marcarLeida/{id}
     */
    public function marcarLeida($id_notificacion) {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Método no permitido', 405);
            }

            // Validar que el ID sea numérico
            if (!is_numeric($id_notificacion)) {
                throw new Exception('ID de notificación no válido', 400);
            }

            // Marcar como leída
            $resultado = $this->notificacionModel->marcarNotificacionLeida(
                $id_notificacion,
                $_SESSION['user_id']
            );
            
            if (!$resultado) {
                throw new Exception('No se pudo marcar la notificación como leída', 500);
            }
            
            // Obtener el nuevo conteo de notificaciones no leídas
            $sinLeer = $this->notificacionModel->contarNotificacionesNoLeidas($_SESSION['user_id']);
            
            $this->responderJSON([
                'success' => true,
                'sinLeer' => $sinLeer
            ]);
            
        } catch (Exception $e) {
            $this->responderJSON([
                'success' => false,
                'error' => $e->getMessage()
            ], $e->getCode() ?: 500);
        }
    }

    /**
     * Marca todas las notificaciones del usuario como leídas
     * Ruta: POST /notificacion/marcarTodasLeidas
     */
    public function marcarTodasLeidas() {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Método no permitido', 405);
            }

            // Marcar todas como leídas
            $resultado = $this->notificacionModel->marcarTodasLeidas($_SESSION['user_id']);
            
            if (!$resultado) {
                throw new Exception('No se pudieron marcar las notificaciones como leídas', 500);
            }
            
            $this->responderJSON([
                'success' => true,
                'sinLeer' => 0
            ]);
            
        } catch (Exception $e) {
            $this->responderJSON([
                'success' => false,
                'error' => $e->getMessage()
            ], $e->getCode() ?: 500);
        }
    }
    /**
     * Envía una respuesta JSON con el código de estado HTTP apropiado
     */
    private function responderJSON($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
    
    /**
     * Formatea una fecha para mostrarla de manera legible
     */
    /**
     * Limpia las notificaciones antiguas del sistema
     * Ruta: POST /notificacion/limpiarAntiguas
     * 
     * @param int $dias Número de días a conservar (opcional, por defecto 30)
     */
    public function limpiarAntiguas($dias = 30) {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Método no permitido', 405);
            }

            // Verificar permisos de administrador
            if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
                throw new Exception('No autorizado', 403);
            }

            $dias = isset($_POST['dias']) ? (int)$_POST['dias'] : $dias;
            
            if ($dias < 1) {
                throw new Exception('El número de días debe ser mayor a 0', 400);
            }

            $eliminadas = $this->notificacionModel->limpiarNotificacionesAntiguas($dias);
            
            $this->responderJSON([
                'success' => true,
                'eliminadas' => $eliminadas,
                'mensaje' => "Se eliminaron $eliminadas notificaciones con más de $dias días de antigüedad"
            ]);
            
        } catch (Exception $e) {
            $this->responderJSON([
                'success' => false,
                'error' => $e->getMessage()
            ], $e->getCode() ?: 500);
        }
    }
    
    /**
     * Formatea una fecha para mostrarla de manera legible
     */
    private function formatearFecha($fecha) {
        $fechaObj = new DateTime($fecha);
        $ahora = new DateTime();
        $diferencia = $ahora->diff($fechaObj);
        
        if ($diferencia->days === 0) {
            if ($diferencia->h > 0) {
                return 'Hace ' . $diferencia->h . ' hora' . ($diferencia->h > 1 ? 's' : '');
            } elseif ($diferencia->i > 0) {
                return 'Hace ' . $diferencia->i . ' minuto' . ($diferencia->i > 1 ? 's' : '');
            } else {
                return 'Hace unos segundos';
            }
        } elseif ($diferencia->days === 1) {
            return 'Ayer a las ' . $fechaObj->format('H:i');
        } elseif ($diferencia->days < 7) {
            $dias = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
            return $dias[$fechaObj->format('w')] . ' a las ' . $fechaObj->format('H:i');
        } else {
            return $fechaObj->format('d/m/Y H:i');
        }
    }
}
?>
