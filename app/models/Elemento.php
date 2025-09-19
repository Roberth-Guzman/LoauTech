<?php
class Elemento extends Model {

    /**
     * Obtiene todos los elementos del inventario que están activos.
     */
    public function obtenerElementosDisponibles() {

        $this->db->query("SELECT IDele, nombreele, cantidadele, codigoele, descripcionele, caracteristicasele, cuentadante_id FROM elementos WHERE estado = 'activo' AND estadoelemento = 'activo'");
        return $this->db->resultSet();
    }
    public function obtenerElementoPorId($id) {
        $this->db->query("SELECT * FROM elementos WHERE IDele = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }
 /**
     * Obtiene absolutamente todos los elementos del inventario
     */
    public function obtenerTodosLosElementos() {
        $this->db->query("SELECT * FROM elementos ORDER BY IDele DESC");
        return $this->db->resultSet();
    }

    /**
     * Cuenta el total de elementos en el inventario.
     * @return int El número total de elementos.
     */
    public function contarTodosLosElementos() {
        $this->db->query("SELECT COUNT(*) as total FROM elementos");
        $resultado = $this->db->single();
        return $resultado ? (int)$resultado->total : 0;
    }

    /**
     * Obtiene una lista paginada de todos los elementos del inventario.
     * @param int $pagina El número de página actual.
     * @param int $por_pagina El número de elementos a mostrar por página.
     * @return array La lista de elementos para la página actual.
     */
    public function obtenerTodosLosElementosPaginados($pagina, $por_pagina) {
        // Calculamos el offset (desplazamiento) para la consulta SQL
        $offset = ($pagina - 1) * $por_pagina;

        // Preparamos la consulta con LIMIT y OFFSET para la paginación
        $this->db->query("SELECT * FROM elementos ORDER BY IDele DESC LIMIT :limit OFFSET :offset");

        // Bindeamos los valores de forma segura
        $this->db->bind(':limit', $por_pagina, PDO::PARAM_INT);
        $this->db->bind(':offset', $offset, PDO::PARAM_INT);

        return $this->db->resultSet();
    }

    /**
     * Obtiene todas las categorías (caracteristicasele) únicas de la tabla de elementos.
     * @return array Lista de categorías únicas.
     */
    public function obtenerCategoriasDistintas() {
        $this->db->query("SELECT DISTINCT caracteristicasele FROM elementos WHERE caracteristicasele IS NOT NULL AND caracteristicasele != '' ORDER BY caracteristicasele ASC");
        return $this->db->resultSet();
    }

    /**
     * Actualiza un elemento existente en la base de datos.
     * @param array $data Datos del elemento a actualizar.
     * @return bool True si la actualización fue exitosa, false en caso contrario.
     */
    public function actualizarElemento($data)
    {
        $this->db->query('UPDATE elementos SET 
                          nombreele = :nombreele, 
                          cantidadele = :cantidadele, 
                          codigoele = :codigoele,
                          codigoinventario = :codigoinventario,
                          descripcionele = :descripcionele,
                          caracteristicasele = :caracteristicasele,
                          estado = :estado,
                          estadoelemento = :estadoelemento,
                          imagen = :imagen,
                          cuentadante_id = :cuentadante_id
                          WHERE IDele = :id');

        // Bind values
        $this->db->bind(':id', $data['id']);
        $this->db->bind(':nombreele', $data['nombreele']);
        $this->db->bind(':cantidadele', $data['cantidadele']);
        $this->db->bind(':codigoele', $data['codigoele']);
        $this->db->bind(':codigoinventario', $data['codigoinventario']);
        $this->db->bind(':descripcionele', $data['descripcionele']);
        $this->db->bind(':caracteristicasele', $data['caracteristicasele']);
        $this->db->bind(':estado', $data['estado']);
        $this->db->bind(':estadoelemento', $data['estadoelemento']);
        $this->db->bind(':imagen', $data['imagen']);
        $this->db->bind(':cuentadante_id', $data['cuentadante_id']);

        // Execute
        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * Crea un nuevo elemento en la base de datos.
     * @param array $data Datos del elemento a crear.
     * @return bool True si la creación fue exitosa, false en caso contrario.
     */
    public function crearElemento($data)
    {
        $this->db->query('INSERT INTO elementos (nombreele, cantidadele, cantidadest, codigoele, descripcionele, caracteristicasele, estado, estadoelemento, codigoinventario, imagen) 
                          VALUES (:nombreele, :cantidadele, :cantidadest, :codigoele, :descripcionele, :caracteristicasele, :estado, :estadoelemento, :codigoinventario, :imagen)');

        // Bind values
        $this->db->bind(':nombreele', $data['nombreele']);
        $this->db->bind(':cantidadele', $data['cantidadele']);
        $this->db->bind(':cantidadest', $data['cantidadest'] ?? 'activo');
        $this->db->bind(':codigoele', $data['codigoele']);
        $this->db->bind(':descripcionele', $data['descripcionele']);
        $this->db->bind(':caracteristicasele', $data['caracteristicasele']);
        $this->db->bind(':estado', $data['estado']);
        $this->db->bind(':estadoelemento', $data['estadoelemento']);
        $this->db->bind(':codigoinventario', $data['codigoinventario']);
        $this->db->bind(':imagen', $data['imagen'] ?? null);

        // Execute
        if ($this->db->execute()) {
            return $this->db->lastInsertId();
        } else {
            return false;
        }
    }
    
    /**
     * Elimina un elemento de la base de datos.
     * @param int $id ID del elemento a eliminar.
     * @return bool True si la eliminación fue exitosa, false en caso contrario.
     */
    public function eliminarElemento($id)
    {
        // Cambia el estado del elemento a 'inactivo' en lugar de borrarlo
        $this->db->query("UPDATE elementos SET estado = 'inactivo' WHERE IDele = :id");
        $this->db->bind(':id', $id);
        
        return $this->db->execute();
    }
    
    /**
     * Verifica si un elemento está siendo utilizado en préstamos.
     * @param int $id ID del elemento a verificar.
     * @return bool True si el elemento está en uso, false en caso contrario.
     */
    public function elementoEnUso($id)
    {
        $this->db->query('SELECT COUNT(*) as total FROM prestamos WHERE IDelementos = :id AND estado_autorizacion = "aprobado"');
        $this->db->bind(':id', $id);
        $resultado = $this->db->single();
        
        return $resultado && (int)$resultado->total > 0;
    }
}
?>