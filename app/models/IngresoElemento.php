<?php
class IngresoElemento extends Model {
    private $error = '';
    
    /**
     * Obtiene el último mensaje de error
     */
    public function getError() {
        return $this->error;
    }
    
    /**
     * Guarda un nuevo registro de ingreso de elemento
     */
    public function guardar($datos) {
        try {
            $this->db->beginTransaction();
            
            // Insertar en la tabla ingresoelementos
            $sql = "INSERT INTO ingresoelementos (
                        IDPER, 
                        nombreingele, 
                        tipoelemento, 
                        descripcioningele, 
                        observacioningele,
                        serial,
                        hora_entrada,
                        hora_salida
                    ) VALUES (
                        :id_usuario, 
                        :nombreingele, 
                        :tipoelemento, 
                        :descripcioningele, 
                        :observacioningele,
                        :serial,
                        NOW(), -- CORREGIDO: Usar NOW() en lugar de NULL
                        NULL
                    )";
            
            $this->db->query($sql);
            $this->db->bind(':id_usuario', $_SESSION['user_id']);
            $this->db->bind(':nombreingele', $datos['nombreingele']);
            $this->db->bind(':tipoelemento', $datos['tipoelemento']);
            $this->db->bind(':descripcioningele', $datos['descripcioningele']);
            $this->db->bind(':observacioningele', $datos['observacioningele']);
            $this->db->bind(':serial', $datos['serial']);
            
            $resultado = $this->db->execute();
            
            if (!$resultado) {
                throw new Exception('Error al guardar el registro de ingreso');
            }
            
            $this->db->commit();
            return true;
            
        } catch (Exception $e) {
            $this->db->rollBack();
            $this->error = $e->getMessage();
            error_log('Error en IngresoElemento::guardar: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtiene el historial de ingresos de elementos
     */
    public function obtenerHistorial($idUsuario = null) {
        try {
            $sql = "SELECT ie.*, e.nombreele, u.nombrecompleto 
                    FROM ingresoelementos ie
                    INNER JOIN elementos e ON ie.id_elemento = e.IDele
                    INNER JOIN usuarios u ON ie.id_usuario = u.id";
            
            if ($idUsuario !== null) {
                $sql .= " WHERE ie.id_usuario = :id_usuario";
            }
            
            $sql .= " ORDER BY ie.fecha_ingreso DESC";
            
            $this->db->query($sql);
            
            if ($idUsuario !== null) {
                $this->db->bind(':id_usuario', $idUsuario);
            }
            
            return $this->db->resultSet();
            
        } catch (Exception $e) {
            $this->error = $e->getMessage();
            error_log('Error en IngresoElemento::obtenerHistorial: ' . $e->getMessage());
            return [];
        }
    }
}