<?php

class Ingreso extends Model {
    public function __construct() {
        parent::__construct();
    }

    public function registrarElemento($nombre, $tipo, $descripcion, $observacion, $usuario_id, $serial) {
        $this->db->query("INSERT INTO ingresoelementos (nombreingele, tipoelemento, descripcioningele, observacioningele, IDPER, serial, hora_entrada) VALUES (:nombre, :tipo, :descripcion, :observacion, :usuario_id, :serial, NOW())");
        
        $this->db->bind(':nombre', $nombre);
        $this->db->bind(':tipo', $tipo);
        $this->db->bind(':descripcion', $descripcion);
        $this->db->bind(':observacion', $observacion);
        $this->db->bind(':usuario_id', $usuario_id);
        $this->db->bind(':serial', $serial);
        
        return $this->db->execute();
    }

    public function obtenerIngresosPorUsuario($usuario_id) {
        $this->db->query("
            SELECT IDingele, nombreingele, tipoelemento, descripcioningele, observacioningele, serial, hora_entrada, hora_salida 
            FROM ingresoelementos 
            WHERE IDPER = :usuario_id 
            ORDER BY hora_entrada DESC
        ");
        
        $this->db->bind(':usuario_id', $usuario_id);
        return $this->db->resultSet();
    }

    /**
     * Obtiene todos los registros de ingresos de elementos,
     * uniéndolos con la información de la persona que los registró.
     */
    public function obtenerTodosLosIngresos() {
        $this->db->query("
            SELECT p.nombrecompletoper, p.numerodoc, ie.* 
            FROM ingresoelementos ie
            JOIN personas p ON ie.IDPER = p.IDper
            ORDER BY ie.IDingele DESC
        ");
        return $this->db->resultSet();
    }

    /**
     * Registra la hora de salida para un elemento específico.
     */
    public function registrarSalida($id) {
        $this->db->query("UPDATE ingresoelementos SET hora_salida = NOW() WHERE IDingele = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
}