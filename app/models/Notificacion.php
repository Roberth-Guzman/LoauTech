<?php
/**
 * Modelo para gestionar las notificaciones del sistema
 */
class Notificacion {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    /**
     * Obtiene las notificaciones de un usuario con paginación
     * 
     * @param int $id_usuario ID del usuario
     * @param int $offset Desplazamiento para la paginación
     * @param int $limite Cantidad de notificaciones por página
     * @return array Lista de notificaciones
     */
    /**
     * Obtiene las notificaciones de un usuario con paginación
     */
    public function obtenerNotificacionesUsuario($id_usuario, $offset = 0, $limite = 10) { 
        try {
            error_log("Obteniendo notificaciones para usuario ID: $id_usuario, offset: $offset, limite: $limite");
            
            $query = "SELECT  n.IDnot as id, n.Tiponot as tipo, n.mensaje, n.estadonot as estado, n.idautori as IDprestamo,
             n.fechacreacion as fecha_creacion, n.fechalectura as fecha_lectura,CASE  WHEN n.estadonot = 'no_leida' THEN true  
             ELSE false END as es_nueva FROM notificaciones n WHERE n.idusuario = :id_usuario ORDER BY n.fechacreacion 
             DESC LIMIT :offset, :limite";
            
            error_log("Ejecutando consulta: $query");
            $this->db->query($query);
            
            $this->db->bind(':id_usuario', $id_usuario, PDO::PARAM_INT);
            $this->db->bind(':offset', (int)$offset, PDO::PARAM_INT);
            $this->db->bind(':limite', (int)$limite, PDO::PARAM_INT);
            
            $result = $this->db->resultSet();
            error_log("Notificaciones encontradas: " . count($result));
            
            return $result;
        } catch (Exception $e) {
            error_log("Error en obtenerNotificacionesUsuario: " . $e->getMessage());
            throw new Exception("Error al obtener las notificaciones");
        }
    }
    
    /**
     * Cuenta el total de notificaciones de un usuario
     * 
     * @param int $id_usuario ID del usuario
     * @return int Total de notificaciones
     */
    public function contarNotificacionesUsuario($id_usuario) {
        $this->db->query("SELECT COUNT(*) as total FROM notificaciones WHERE idusuario = :id_usuario");
        $this->db->bind(':id_usuario', $id_usuario);
        $resultado = $this->db->single();
        return $resultado ? (int)$resultado->total : 0;
    }
    
    /**
     * Cuenta las notificaciones no leídas de un usuario
     * 
     * @param int $id_usuario ID del usuario
     * @return int Cantidad de notificaciones no leídas
     * @throws Exception Si ocurre un error en la consulta
     */
    public function contarNotificacionesNoLeidas($id_usuario) {
        try {
            $this->db->query("SELECT COUNT(*) as total 
                             FROM notificaciones 
                             WHERE idusuario = :id_usuario 
                             AND (estadonot = 'no_leida' OR leido = 0)");
            $this->db->bind(':id_usuario', $id_usuario, PDO::PARAM_INT);
            $resultado = $this->db->single();
            error_log("Notificaciones no leídas encontradas: " . ($resultado ? $resultado->total : 0));
            return $resultado ? (int)$resultado->total : 0;
        } catch (Exception $e) {
            error_log("Error en contarNotificacionesNoLeidas: " . $e->getMessage());
            throw new Exception("Error al contar notificaciones no leídas");
        }
    }
    
    /**
     * Marca una notificación como leída
     * 
     * @param int $id_notificacion ID de la notificación a marcar como leída
     * @param int $id_usuario ID del usuario propietario de la notificación
     * @return bool True si la operación fue exitosa, false en caso contrario
     * @throws Exception Si ocurre un error al actualizar el estado
     */
    public function marcarNotificacionLeida($id_notificacion, $id_usuario) {
        try {
            $this->db->query("UPDATE notificaciones 
                             SET estadonot = 'leida',
                                 fechalectura = NOW(),
                                 leido = 1 
                             WHERE IDnot = :id_notificacion 
                             AND idusuario = :id_usuario");
            
            $this->db->bind(':id_notificacion', $id_notificacion, PDO::PARAM_INT);
            $this->db->bind(':id_usuario', $id_usuario, PDO::PARAM_INT);
            
            return $this->db->execute();
        } catch (Exception $e) {
            error_log("Error en marcarNotificacionLeida: " . $e->getMessage());
            throw new Exception("Error al marcar la notificación como leída");
        }
    }
    
    /**
     * Marca todas las notificaciones de un usuario como leídas
     * 
     * @param int $id_usuario ID del usuario cuyas notificaciones se marcarán como leídas
     * @return bool True si la operación fue exitosa, false en caso contrario
     * @throws Exception Si ocurre un error al actualizar el estado
     */
    public function marcarTodasLeidas($id_usuario) {
        try {
            $this->db->query("UPDATE notificaciones 
                             SET estadonot = 'leida',
                                 fechalectura = NOW(),
                                 leido = 1
                             WHERE idusuario = :id_usuario
                             AND estadonot = 'no_leida'");
            
            $this->db->bind(':id_usuario', $id_usuario, PDO::PARAM_INT);
            
            return $this->db->execute();
        } catch (Exception $e) {
            error_log("Error en marcarTodasLeidas: " . $e->getMessage());
            throw new Exception("Error al marcar todas las notificaciones como leídas");
        }
    }

    /**
     * Crea una nueva notificación en el sistema
     * 
     * @param int $id_usuario ID del usuario destinatario de la notificación
     * @param string $tipo Tipo de notificación (ej: 'prestamo_aprobado', 'prestamo_rechazado')
     * @param string $mensaje Contenido de la notificación
     * @param int|null $id_prestamo ID del préstamo relacionado (opcional)
     * @return int|bool ID de la notificación creada o false en caso de error
     * @throws Exception Si ocurre un error al crear la notificación
     */
    public function crearNotificacion($id_usuario, $tipo, $mensaje, $id_prestamo = null, $enlace = null) {
        try {
            // Obtener el ID de autorización del préstamo si se proporciona
            $id_autorizacion = null;
            if ($id_prestamo) {
                $this->db->query("SELECT IDautorizacion FROM prestamos WHERE IDpre = :id_prestamo");
                $this->db->bind(':id_prestamo', $id_prestamo);
                $result = $this->db->single();
                if ($result) {
                    $id_autorizacion = $result->IDautorizacion;
                }
            }
            
            $this->db->query("INSERT INTO notificaciones 
                             (Tiponot, estadonot, idautori, mensaje, idusuario, enlace, fechacreacion) 
                             VALUES (:tipo, 'no_leida', :id_autorizacion, :mensaje, :id_usuario, :enlace, NOW())");
            
            $this->db->bind(':tipo', $tipo);
            $this->db->bind(':id_autorizacion', $id_autorizacion);
            $this->db->bind(':mensaje', $mensaje);
            $this->db->bind(':id_usuario', $id_usuario, PDO::PARAM_INT);
            $this->db->bind(':enlace', $enlace);
            
            $resultado = $this->db->execute();
            
            if ($resultado) {
                $id_notificacion = $this->db->lastInsertId();
                return $id_notificacion;
            } else {
                return false;
            }
        } catch (Exception $e) {
            error_log("Error en crearNotificacion: " . $e->getMessage());
            throw new Exception("Error al crear la notificación: " . $e->getMessage());
        }
    }
    
    /**
     * Elimina notificaciones antiguas del sistema
     * 
     * @param int $dias Número de días a conservar (por defecto 30 días)
     * @return int Número de notificaciones eliminadas
     * @throws Exception Si ocurre un error al eliminar las notificaciones
     */
    public function limpiarNotificacionesAntiguas($dias = 30) {
        try {
            $this->db->query("DELETE FROM notificaciones 
                             WHERE fecha_creacion < DATE_SUB(NOW(), INTERVAL :dias DAY)");
            
            $this->db->bind(':dias', (int)$dias, PDO::PARAM_INT);
            
            $this->db->execute();
            return $this->db->rowCount();
        } catch (Exception $e) {
            error_log("Error en limpiarNotificacionesAntiguas: " . $e->getMessage());
            throw new Exception("Error al limpiar notificaciones antiguas");
        }
    }
}
?>
