<?php
class Elemento extends Model {

    /**
     * Obtiene todos los elementos del inventario que están activos.
     * Este método ahora usa la clase Database (PDO Wrapper).
     */
   public function obtenerElementosDisponibles() {
        // 1. Preparamos la consulta usando el método query() de nuestra clase Database.
        $this->db->query("SELECT * FROM elementos WHERE estado = 'activo'");

        // 2. Ejecutamos la consulta y obtenemos todos los resultados.

        return $this->db->resultSet();
    }

    public function obtenerElementoPorId($id) {
        $this->db->query("SELECT * FROM elementos WHERE IDele = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }
 /**
     * Obtiene absolutamente todos los elementos del inventario, sin importar su estado.
     * Ideal para la vista del personal de almacén.
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
                          imagen = :imagen
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

        // Execute
        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }
}
?>