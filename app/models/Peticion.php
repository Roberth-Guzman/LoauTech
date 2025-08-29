<?php
class Peticion {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    public function crearPeticionCompleta($data) {
        // 1. Verificar disponibilidad del elemento
        $this->db->query("SELECT cantidadele, estadoelemento, estado, nombreele, descripcionele, codigoinventario FROM elementos WHERE IDele = :id_elemento");
        $this->db->bind(':id_elemento', $data['id_elemento']);
        $elemento = $this->db->single();

        if (!$elemento) {
            throw new Exception('El elemento seleccionado no existe.');
        }
        if ($elemento->estadoelemento !== 'activo' || $elemento->estado === 'en prestamo') {
            throw new Exception('El elemento seleccionado no está disponible.');
        }
        if ($elemento->cantidadele < $data['cantidad']) {
            throw new Exception('No hay suficiente stock disponible. Stock actual: ' . $elemento->cantidadele);
        }

        // 2. Iniciar la transacción
        $this->db->beginTransaction();

        try {
            // 2a. Crear registro en `autorizacion`
            $this->db->query("INSERT INTO autorizacion (VoBoCuentadanteaut, nomquienaturiza, cargoquienautoriza, firmaquienautoriza, estadoaut) VALUES ('Pendiente', 'Sistema', 'sistema', 'pendiente', 'pendiente')");
            $this->db->execute();
            $idAutorizacion = $this->db->lastInsertId();

            // 2b. Crear la notificación
            $this->db->query("INSERT INTO notificaciones (Tiponot, estadonot, idautori) VALUES ('peticion_pendiente', 'pendiente', :id_autorizacion)");
            $this->db->bind(':id_autorizacion', $idAutorizacion);
            $this->db->execute();

            // 2c. Crear registro en `detallesprestamo`
            $descripcionDetalle = $elemento->nombreele . ' - ' . $elemento->descripcionele;
            $this->db->query("INSERT INTO detallesprestamo (descelementodetpre, codigoinvdetpre, estadocaprestamo, estadoeningreso, nombrecuentadante, idelementos) VALUES (:desc, :cod_inv, 'activo', 'inactivo', 'Pendiente de asignación', :id_elemento)");
            $this->db->bind(':desc', $descripcionDetalle);
            $this->db->bind(':cod_inv', $elemento->codigoinventario ?? '');
            $this->db->bind(':id_elemento', $data['id_elemento']);
            $this->db->execute();
            $idDetalle = $this->db->lastInsertId();

            // 2d. Insertar la petición en la tabla `prestamos`
            $this->db->query("INSERT INTO prestamos (cantidad, formacionodependencia, cargopre, lugardetraslado, IDdetalle, IDautorizacion, IDelementos, IDpersonas) VALUES (:cantidad, :formacion, :cargo, :lugar, :id_detalle, :id_autorizacion, :id_elemento, :id_persona)");
            $this->db->bind(':cantidad', $data['cantidad']);
            $this->db->bind(':formacion', $data['formacionodependencia']);
            $this->db->bind(':cargo', $data['cargopre']);
            $this->db->bind(':lugar', $data['lugardetraslado']);
            $this->db->bind(':id_detalle', $idDetalle);
            $this->db->bind(':id_autorizacion', $idAutorizacion);
            $this->db->bind(':id_elemento', $data['id_elemento']);
            $this->db->bind(':id_persona', $data['id_usuario']);
            $this->db->execute();

            // 2e. Actualizar el stock del elemento
            $nuevo_stock = $elemento->cantidadele - $data['cantidad'];
            $this->db->query("UPDATE elementos SET cantidadele = :nuevo_stock WHERE IDele = :id_elemento");
            $this->db->bind(':nuevo_stock', $nuevo_stock);
            $this->db->bind(':id_elemento', $data['id_elemento']);
            $this->db->execute();

            // 2f. Si el stock llega a 0, cambiar estado a 'en prestamo'
            if ($nuevo_stock <= 0) {
                $this->db->query("UPDATE elementos SET estado = 'en prestamo' WHERE IDele = :id_elemento");
                $this->db->bind(':id_elemento', $data['id_elemento']);
                $this->db->execute();
            }

            // 3. Si todo salió bien, confirmar la transacción
            if ($this->db->commit()) {
                return true;
            } else {
                $this->db->rollBack();
                return false;
            }
        } catch (Exception $e) {
            // 4. Revertir transacción en caso de error
            $this->db->rollBack();
            throw $e; // Re-lanzar la excepción para que el controlador la capture
        }
    }
    

    public function getPeticionesPorUsuario($idUsuario) {
     
        $this->db->query('SELECT pr.*, 
                                 e.nombreele as elemento_nombre, 
                                 e.codigoinventario as elemento_codigo, 
                                 a.estadoaut as estpet 
                          FROM prestamos pr
                          JOIN elementos e ON pr.IDelementos = e.IDele
                          JOIN autorizacion a ON pr.IDautorizacion = a.IDaut
                          WHERE pr.IDpersonas = :id_usuario 
                          ORDER BY pr.IDpre DESC');
        
        $this->db->bind(':id_usuario', $idUsuario);
        return $this->db->resultSet();
    }

     /**
     * Cuenta el total de peticiones para un usuario.
     */
    public function contarPeticionesFiltradas($idUsuario, $filtros = []) {
        $sql = 'SELECT COUNT(*) as total 
                FROM prestamos pr
                JOIN elementos e ON pr.IDelementos = e.IDele
                JOIN autorizacion a ON pr.IDautorizacion = a.IDaut
                WHERE pr.IDpersonas = :id_usuario';

        $params = [':id_usuario' => $idUsuario];

        if (!empty($filtros['search'])) {
            $sql .= ' AND e.nombreele LIKE :search';
            $params[':search'] = '%' . $filtros['search'] . '%';
        }
        if (!empty($filtros['status'])) {
            $sql .= ' AND a.estadoaut = :status';
            $params[':status'] = $filtros['status'];
        }

        $this->db->query($sql);
        foreach ($params as $key => &$val) {
            $this->db->bind($key, $val);
        }
        
        $resultado = $this->db->single();
        return $resultado ? (int)$resultado->total : 0;
    }

    /**
     * Obtiene una lista paginada y filtrada de peticiones para un usuario.
     */
    public function getPeticionesPaginadasPorUsuario($idUsuario, $limit, $offset, $filtros = []) {
        $sql = 'SELECT pr.*, 
                       e.nombreele as elemento_nombre, 
                       e.codigoinventario as elemento_codigo, 
                       a.estadoaut as estpet 
                FROM prestamos pr
                JOIN elementos e ON pr.IDelementos = e.IDele
                JOIN autorizacion a ON pr.IDautorizacion = a.IDaut
                WHERE pr.IDpersonas = :id_usuario';
        
        $params = [':id_usuario' => $idUsuario];

        if (!empty($filtros['search'])) {
            $sql .= ' AND e.nombreele LIKE :search';
            $params[':search'] = '%' . $filtros['search'] . '%';
        }
        if (!empty($filtros['status'])) {
            $sql .= ' AND a.estadoaut = :status';
            $params[':status'] = $filtros['status'];
        }

        $sql .= ' ORDER BY pr.IDpre DESC LIMIT :limit OFFSET :offset';
        $params[':limit'] = $limit;
        $params[':offset'] = $offset;

        $this->db->query($sql);
        foreach ($params as $key => &$val) {
            // PDO necesita saber el tipo para LIMIT/OFFSET
            if ($key == ':limit' || $key == ':offset') {
                $this->db->bind($key, $val, PDO::PARAM_INT);
            } else {
                $this->db->bind($key, $val);
            }
        }
        
        return $this->db->resultSet();
    }

    // --- Nuevos métodos para el panel de almacén ---

    public function obtenerPeticionesPendientes() {
        $this->db->query("SELECT pr.IDpre, pr.cantidad,
                               p.nombrecompletoper, p.numerodoc,
                               e.nombreele, e.codigoinventario,
                               a.estadoaut
                        FROM prestamos pr
                        JOIN personas p ON pr.IDpersonas = p.IDper
                        JOIN elementos e ON pr.IDelementos = e.IDele
                        JOIN autorizacion a ON pr.IDautorizacion = a.IDaut
                        WHERE a.estadoaut = 'Pendiente'
                        ORDER BY pr.IDpre DESC");
        return $this->db->resultSet();
    }

    public function cambiarEstadoPeticion($id_prestamo, $nuevo_estado) {
        $this->db->query("UPDATE autorizacion 
                          SET estadoaut = :nuevo_estado 
                          WHERE IDaut = (SELECT IDautorizacion FROM prestamos WHERE IDpre = :id_prestamo)");
        $this->db->bind(':nuevo_estado', $nuevo_estado);
        $this->db->bind(':id_prestamo', $id_prestamo);
        return $this->db->execute();
    }

    public function rechazarPeticion($id_prestamo) {
        $this->db->beginTransaction();

        try {
            // 1. Obtener los detalles del préstamo (ID del elemento y cantidad)
            $this->db->query("SELECT IDelementos, cantidad FROM prestamos WHERE IDpre = :id_prestamo");
            $this->db->bind(':id_prestamo', $id_prestamo);
            $prestamo = $this->db->single();

            if (!$prestamo) {
                throw new Exception("Préstamo no encontrado.");
            }

            // 2. Devolver la cantidad al stock del elemento
            $this->db->query("UPDATE elementos SET cantidadele = cantidadele + :cantidad WHERE IDele = :id_elemento");
            $this->db->bind(':cantidad', $prestamo->cantidad);
            $this->db->bind(':id_elemento', $prestamo->IDelementos);
            $this->db->execute();

            // 3. Actualizar el estado de la autorización a 'Rechazado'
            $this->db->query("UPDATE autorizacion 
                              SET estadoaut = 'Rechazado' 
                              WHERE IDaut = (SELECT IDautorizacion FROM prestamos WHERE IDpre = :id_prestamo)");
            $this->db->bind(':id_prestamo', $id_prestamo);
            $this->db->execute();
            
            // 4. Opcional: Si el elemento estaba 'en prestamo' porque el stock era 0, volver a ponerlo 'activo'
            $this->db->query("UPDATE elementos SET estado = 'activo' WHERE IDele = :id_elemento AND cantidadele > 0 AND estado = 'en prestamo'");
            $this->db->bind(':id_elemento', $prestamo->IDelementos);
            $this->db->execute();


            // Confirmar la transacción
            if ($this->db->commit()) {
                return true;
            } else {
                $this->db->rollBack();
                return false;
            }
        } catch (Exception $e) {
            $this->db->rollBack();
            // Podríamos registrar el error $e->getMessage()
            return false;
        }
    }

    // --- Nuevos métodos para el panel de portería ---

    public function contarPeticionesAprobadas() {
        $this->db->query("SELECT COUNT(*) as total 
                          FROM prestamos pr
                          JOIN autorizacion a ON pr.IDautorizacion = a.IDaut
                          WHERE a.estadoaut = 'Aprobado' AND pr.estado_autorizacion = 'pendiente'");
        $resultado = $this->db->single();
        return $resultado ? (int)$resultado->total : 0;
    }

    public function obtenerPeticionesAprobadasPaginadas($limit, $offset) {
        $this->db->query("SELECT pr.IDpre, pr.cantidad, pr.formacionodependencia, pr.lugardetraslado,
                               e.nombreele, e.codigoinventario,
                               p.nombrecompletoper, p.numerodoc
                        FROM prestamos pr
                        JOIN elementos e ON pr.IDelementos = e.IDele
                        JOIN personas p ON pr.IDpersonas = p.IDper
                        JOIN autorizacion a ON pr.IDautorizacion = a.IDaut
                        WHERE a.estadoaut = 'Aprobado' AND pr.estado_autorizacion = 'pendiente'
                        ORDER BY pr.IDpre DESC
                        LIMIT :limit OFFSET :offset");

        $this->db->bind(':limit', $limit, PDO::PARAM_INT);
        $this->db->bind(':offset', $offset, PDO::PARAM_INT);

        return $this->db->resultSet();
    }

    public function obtenerSalidasDelDia() {
        $this->db->query("SELECT DATE(m.hfecsalidamarc) as fecsalida, TIME(m.hfecsalidamarc) as horasalida,
                               e.nombreele, e.codigoinventario,
                               p.nombrecompletoper, p.numerodoc
                        FROM marcaciones m
                        JOIN prestamos pr ON m.IDpres = pr.IDpre
                        JOIN elementos e ON pr.IDelementos = e.IDele
                        JOIN personas p ON pr.IDpersonas = p.IDper
                        WHERE DATE(m.hfecsalidamarc) = CURDATE()
                        ORDER BY m.hfecsalidamarc DESC");
        return $this->db->resultSet();
    }

    public function registrarSalidaElemento($id_prestamo, $fecha_salida) {
        $this->db->beginTransaction();
        try {
            // Primero, obtener el ID de autorización del préstamo
            $this->db->query("SELECT IDautorizacion FROM prestamos WHERE IDpre = :id_prestamo");
            $this->db->bind(':id_prestamo', $id_prestamo);
            $prestamo = $this->db->single();

            if (!$prestamo) {
                throw new Exception("No se encontró el préstamo.");
            }
            $id_autorizacion = $prestamo->IDautorizacion;

            // Combinamos la fecha de salida con la hora actual para el campo DATETIME
            $this->db->query("INSERT INTO marcaciones (hfecsalidamarc, estadomarc, IDpres, IDautori) VALUES (CONCAT(:fecha_salida, ' ', CURTIME()), 'activo', :id_prestamo, :id_autorizacion)");
            $this->db->bind(':fecha_salida', $fecha_salida);
            $this->db->bind(':id_prestamo', $id_prestamo);
            $this->db->bind(':id_autorizacion', $id_autorizacion);
            $this->db->execute();

            $this->db->query("UPDATE prestamos SET estado_autorizacion = 'finalizado' WHERE IDpre = :id_prestamo");
            $this->db->bind(':id_prestamo', $id_prestamo);
            $this->db->execute();

            if ($this->db->commit()) {
                return true;
            } else {
                $this->db->rollBack();
                return false;
            }
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    // --- Nuevos métodos para el panel de Cuentadante ---

    public function contarPeticionesParaCuentadante($filtros = []) {
        $sql = "SELECT COUNT(*) as total 
                FROM prestamos pr
                JOIN autorizacion a ON pr.IDautorizacion = a.IDaut
                WHERE a.estadoaut IN ('Pendiente', 'Rechazado')";


        $this->db->query($sql);
        $resultado = $this->db->single();
        return $resultado ? (int)$resultado->total : 0;
    }

    public function obtenerPeticionesParaCuentadante($limit, $offset, $filtros = []) {
        $sql = "SELECT pr.IDpre, pr.cantidad, pr.formacionodependencia, pr.lugardetraslado,
                       e.nombreele, e.codigoinventario,
                       p.nombrecompletoper, p.numerodoc,
                       a.estadoaut
                FROM prestamos pr
                JOIN elementos e ON pr.IDelementos = e.IDele
                JOIN personas p ON pr.IDpersonas = p.IDper
                JOIN autorizacion a ON pr.IDautorizacion = a.IDaut
                WHERE a.estadoaut IN ('Pendiente', 'Rechazado')";

        // Aquí podrías añadir filtros si los necesitas en el futuro
        
        $sql .= " ORDER BY pr.IDpre DESC LIMIT :limit OFFSET :offset";

        $this->db->query($sql);
        $this->db->bind(':limit', $limit, PDO::PARAM_INT);
        $this->db->bind(':offset', $offset, PDO::PARAM_INT);

        return $this->db->resultSet();
    }

    public function obtenerEstadisticasCuentadante() {
        $this->db->query("SELECT 
            SUM(CASE WHEN a.estadoaut = 'Aprobado' THEN 1 ELSE 0 END) as aprobadas,
            SUM(CASE WHEN a.estadoaut = 'Rechazado' THEN 1 ELSE 0 END) as rechazadas,
            COUNT(pr.IDpre) as total
            FROM prestamos pr
            JOIN autorizacion a ON pr.IDautorizacion = a.IDaut");
        
        return $this->db->single();
    }
}