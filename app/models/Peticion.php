<?php
class Peticion
{
    private $db;

    public function __construct()
    {
        $this->db = new Database;
    }

     public function contarPeticionesPendientes()
    {
        $this->db->query('SELECT COUNT(*) as total 
                         FROM prestamos p
                         JOIN autorizacion a ON p.IDautorizacion = a.IDaut
                         WHERE a.estadoaut = "pendiente_almacen"');
        $resultado = $this->db->single();
        return $resultado ? (int) $resultado->total : 0;
    }

  public function obtenerPeticionesPaginadas($offset, $limit)
{
    $this->db->query('SELECT 
                        p.IDpre, 
                        p.cantidad,
                        p.fecha_solicitud,
                        p.lugardetraslado,
                        p.cargopre,
                        p.formacionodependencia,
                        e.nombreele as elemento_nombre, 
                        e.codigoinventario,
                        e.descripcionele,
                        per.nombrecompletoper as solicitante,
                        per.numerodoc as documento,
                        per.tipodocumento,
                        a.estadoaut,
                        p.fecha_solicitud as fechasolicitudaut,
                        p.IDelementos,
                        p.IDpersonas
                     FROM prestamos p
                     JOIN elementos e ON p.IDelementos = e.IDele
                     JOIN personas per ON p.IDpersonas = per.IDper
                     JOIN autorizacion a ON p.IDautorizacion = a.IDaut
                     WHERE a.estadoaut = "pendiente_almacen"
                     ORDER BY p.IDpre DESC
                     LIMIT :limit OFFSET :offset');

    $this->db->bind(':limit', $limit, PDO::PARAM_INT);
    $this->db->bind(':offset', $offset, PDO::PARAM_INT);

    return $this->db->resultSet();
}

    // Método mantenido por compatibilidad
    public function obtenerPeticionesPendientes()
    {
        return $this->obtenerPeticionesPaginadas(0, 10);
    }

    public function actualizarEstadoPrestamo($idPrestamo, $estado, $motivoRechazo = null)
    {
        $this->db->query('UPDATE prestamos p 
                         JOIN autorizacion a ON p.IDautorizacion = a.IDaut
                         SET a.estadoaut = :estado, 
                             a.estado = :estado,
                             p.estado_autorizacion = :estado,
                             p.motivo_rechazo = :motivo_rechazo
                         WHERE p.IDpre = :id_prestamo');

        $this->db->bind(':estado', $estado);
        $this->db->bind(':id_prestamo', $idPrestamo);
        $this->db->bind(':motivo_rechazo', $motivoRechazo);

        return $this->db->execute();
    }

    public function getPeticionesPorUsuario($idUsuario)
    {

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
    public function contarPeticionesFiltradas($idUsuario, $filtros = [])
    {
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
        return $resultado ? (int) $resultado->total : 0;
    }

    /**
     * Cuenta el total de peticiones para el cuentadante.
     */
    public function contarPeticionesParaCuentadante($cuentadante_id)
    {
        $this->db->query('SELECT COUNT(p.IDpre) as total
                          FROM prestamos p
                          JOIN elementos e ON p.IDelementos = e.IDele
                          JOIN aprobaciones ap ON p.IDpre = ap.IDprestamo
                          WHERE e.cuentadante_id = :cuentadante_id
                          AND ap.tipo_aprobacion = "cuentadante"
                          AND ap.estado = "pendiente"');
        $this->db->bind(':cuentadante_id', $cuentadante_id);
        $row = $this->db->single();
        return $row ? $row->total : 0;
    }
    public function obtenerPeticionesParaCuentadante($cuentadante_id, $limit, $offset)
    {
        $this->db->query('SELECT p.IDpre, p.cantidad, p.fecha_solicitud,
                             e.nombreele as elemento_nombre,
                             solicitante.nombrecompletoper as solicitante_nombre,
                             ap.id as aprobacion_id
                      FROM prestamos p
                      JOIN elementos e ON p.IDelementos = e.IDele
                      JOIN personas solicitante ON p.IDpersonas = solicitante.IDper
                      JOIN aprobaciones ap ON p.IDpre = ap.IDprestamo
                      JOIN autorizacion a ON p.IDautorizacion = a.IDaut
                      WHERE e.cuentadante_id = :cuentadante_id
                      AND ap.tipo_aprobacion = "cuentadante"
                      AND ap.estado = "pendiente"
                      AND a.estadoaut = "pendiente_cuentadante"
                      ORDER BY p.fecha_solicitud DESC
                      LIMIT :limit OFFSET :offset');
        $this->db->bind(':cuentadante_id', $cuentadante_id);
        $this->db->bind(':limit', $limit);
        $this->db->bind(':offset', $offset);
        return $this->db->resultSet();
    }

    public function crearPeticionCompleta($datos)
    {
        $this->db->beginTransaction();

        try {
            // 1. Obtener información del elemento, incluyendo el cuentadante_id
            $this->db->query('SELECT cuentadante_id, nombreele, codigoele FROM elementos WHERE IDele = :idelemento');
            $this->db->bind(':idelemento', $datos['elemento_id']);
            $elemento = $this->db->single();

            if (!$elemento) {
                throw new Exception("El elemento solicitado no existe.");
            }

            // 2. Determinar el estado inicial basado en si hay un cuentadante
            $estado_inicial_aut = !empty($elemento->cuentadante_id) ? 'pendiente_cuentadante' : 'pendiente_almacen';

            // 3. Insertar en 'autorizacion' con un placeholder de estado único
            $this->db->query('INSERT INTO autorizacion (VoBoCuentadanteaut, nomquienaturiza, cargoquienautoriza, firmaquienautoriza, estadoaut) 
                              VALUES (:vobo, :nombre_autoriza, :cargo_autoriza, :firma_autoriza, :estado_aut)');
            $this->db->bind(':vobo', 'N/A');
            $this->db->bind(':nombre_autoriza', 'N/A');
            $this->db->bind(':cargo_autoriza', 'N/A');
            $this->db->bind(':firma_autoriza', 'N/A');
            $this->db->bind(':estado_aut', $estado_inicial_aut);
            $this->db->execute();
            $autorizacion_id = $this->db->lastInsertId();

            // 4. Insertar en 'detallesprestamo'
            $this->db->query('INSERT INTO detallesprestamo (descelementodetpre, codigoinvdetpre, idelementos) 
                              VALUES (:descripcion, :codigo, :idelemento)');
            $this->db->bind(':descripcion', $elemento->nombreele . ' - Detalles del préstamo');
            $this->db->bind(':codigo', $elemento->codigoele);
            $this->db->bind(':idelemento', $datos['elemento_id']);
            $this->db->execute();
            $detalle_id = $this->db->lastInsertId();

            // 5. Insertar en 'prestamos' con placeholders únicos
            $this->db->query('INSERT INTO prestamos (IDdetalle, IDautorizacion, IDelementos, IDpersonas, cantidad, formacionodependencia, cargopre, lugardetraslado, estado_autorizacion) 
                              VALUES (:id_detalle, :id_autorizacion, :id_elemento, :id_persona, :cantidad, :formacion, :cargo, :lugar, :estado_prestamo)');
            $this->db->bind(':id_detalle', $detalle_id);
            $this->db->bind(':id_autorizacion', $autorizacion_id);
            $this->db->bind(':id_elemento', $datos['elemento_id']);
            $this->db->bind(':id_persona', $datos['usuario_id']);
            $this->db->bind(':cantidad', $datos['cantidad']);
            $this->db->bind(':formacion', $datos['formacionodependencia']);
            $this->db->bind(':cargo', $datos['cargopre']);
            $this->db->bind(':lugar', $datos['lugardetraslado']);
            $this->db->bind(':estado_prestamo', 'pendiente');
            $this->db->execute();
            $prestamo_id = $this->db->lastInsertId();
            
            // 6. Insertar en 'aprobaciones' con un placeholder de estado único
            $tipo_aprobacion = !empty($elemento->cuentadante_id) ? 'cuentadante' : 'almacen';
            $this->db->query('INSERT INTO aprobaciones (IDprestamo, estado, tipo_aprobacion) VALUES (:id_prestamo, :estado_aprobacion, :tipo_aprobacion)');
            $this->db->bind(':id_prestamo', $prestamo_id);
            $this->db->bind(':estado_aprobacion', 'pendiente');
            $this->db->bind(':tipo_aprobacion', $tipo_aprobacion);
            $this->db->execute();


            $this->db->commit();
            return true;

        } catch (Exception $e) {
            $this->db->rollBack();
            // Para depuración, puedes registrar el error: error_log($e->getMessage());
            throw $e; // Re-lanzar la excepción para que el controlador la maneje
        }
    }  
    public function aprobarPeticionCuentadante($aprobacionId)
    {
        $this->db->beginTransaction();
        try {
            // 1. Actualizar el estado en 'aprobaciones'
            $this->db->query('UPDATE aprobaciones SET estado = "aprobado", fecha_aprobacion = NOW() WHERE id = :aprobacion_id');
            $this->db->bind(':aprobacion_id', $aprobacionId);
            if (!$this->db->execute()) {
                throw new Exception("Error al actualizar la tabla de aprobaciones.");
            }

            // 2. Obtener el IDprestamo desde la tabla de aprobaciones
            $this->db->query('SELECT IDprestamo FROM aprobaciones WHERE id = :aprobacion_id');
            $this->db->bind(':aprobacion_id', $aprobacionId);
            $aprobacion = $this->db->single();
            if (!$aprobacion) {
                throw new Exception("No se encontró la aprobación.");
            }

            // 3. Obtener el IDautorizacion desde la tabla de prestamos
            $this->db->query('SELECT IDautorizacion FROM prestamos WHERE IDpre = :id_prestamo');
            $this->db->bind(':id_prestamo', $aprobacion->IDprestamo);
            $prestamo = $this->db->single();
            if (!$prestamo) {
                throw new Exception("No se encontró el préstamo asociado.");
            }

            // 4. Actualizar el estado en 'autorizacion' a 'pendiente_almacen'
            $this->db->query('UPDATE autorizacion SET estadoaut = "pendiente_almacen" WHERE IDaut = :autorizacion_id');
            $this->db->bind(':autorizacion_id', $prestamo->IDautorizacion);
            if (!$this->db->execute()) {
                throw new Exception("Error al actualizar la tabla de autorización.");
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function rechazarPeticionCuentadante($aprobacionId, $motivo)
    {
        $this->db->beginTransaction();
        try {
            // 1. Actualizar el estado y motivo en 'aprobaciones'
            $this->db->query('UPDATE aprobaciones SET estado = "rechazado", fecha_rechazo = NOW(), motivo = :motivo WHERE id = :aprobacion_id');
            $this->db->bind(':motivo', $motivo);
            $this->db->bind(':aprobacion_id', $aprobacionId);
            if (!$this->db->execute()) {
                throw new Exception("Error al actualizar la tabla de aprobaciones.");
            }

            // 2. Obtener el IDprestamo desde la tabla de aprobaciones
            $this->db->query('SELECT IDprestamo FROM aprobaciones WHERE id = :aprobacion_id');
            $this->db->bind(':aprobacion_id', $aprobacionId);
            $aprobacion = $this->db->single();
            if (!$aprobacion) {
                throw new Exception("No se encontró la aprobación.");
            }

            // 3. Obtener el IDautorizacion desde la tabla de prestamos
            $this->db->query('SELECT IDautorizacion FROM prestamos WHERE IDpre = :id_prestamo');
            $this->db->bind(':id_prestamo', $aprobacion->IDprestamo);
            $prestamo = $this->db->single();
            if (!$prestamo) {
                throw new Exception("No se encontró el préstamo asociado.");
            }

            // 4. Actualizar el estado en 'autorizacion' a 'Rechazado'
            $this->db->query('UPDATE autorizacion SET estadoaut = "Rechazado" WHERE IDaut = :autorizacion_id');
            $this->db->bind(':autorizacion_id', $prestamo->IDautorizacion);
            if (!$this->db->execute()) {
                throw new Exception("Error al actualizar la tabla de autorización.");
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function aprobarPeticionAlmacen($peticionId)
    {
        $this->db->beginTransaction();
        try {
            // 1. Obtener el IDautorizacion desde la tabla de prestamos
            $this->db->query('SELECT IDautorizacion FROM prestamos WHERE IDpre = :peticion_id');
            $this->db->bind(':peticion_id', $peticionId);
            $prestamo = $this->db->single();

            if (!$prestamo) {
                throw new Exception("Préstamo no encontrado.");
            }
            $autorizacionId = $prestamo->IDautorizacion;

            // 2. Actualizar el estado en 'autorizacion' a 'pendiente_porteria'
            $this->db->query('UPDATE autorizacion SET estadoaut = "pendiente_porteria" WHERE IDaut = :autorizacion_id');
            $this->db->bind(':autorizacion_id', $autorizacionId);
            if (!$this->db->execute()) {
                throw new Exception("Error al actualizar la autorización.");
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function rechazarPeticionAlmacen($peticionId, $motivo)
    {
        $this->db->beginTransaction();
        try {
            // 1. Obtener el IDautorizacion y el ID de la petición desde la tabla de prestamos
            $this->db->query('SELECT IDautorizacion, IDpre FROM prestamos WHERE IDpre = :peticion_id');
            $this->db->bind(':peticion_id', $peticionId);
            $prestamo = $this->db->single();

            if (!$prestamo) {
                throw new Exception("Préstamo no encontrado.");
            }
            $autorizacionId = $prestamo->IDautorizacion;

            // 2. Actualizar el estado en 'autorizacion' a 'Rechazado'
            $this->db->query('UPDATE autorizacion SET estadoaut = "Rechazado" WHERE IDaut = :autorizacion_id');
            $this->db->bind(':autorizacion_id', $autorizacionId);
            if (!$this->db->execute()) {
                throw new Exception("Error al actualizar la autorización.");
            }

            // 3. Actualizar el motivo de rechazo en la tabla de prestamos
            $this->db->query('UPDATE prestamos SET motivo_rechazo = :motivo WHERE IDpre = :peticion_id');
            $this->db->bind(':motivo', $motivo);
            $this->db->bind(':peticion_id', $peticionId);
            if (!$this->db->execute()) {
                throw new Exception("Error al guardar el motivo del rechazo.");
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    /**
     * Obtiene una lista paginada y filtrada de peticiones para un usuario.
     */
    public function getPeticionesPaginadasPorUsuario($idUsuario, $limit, $offset, $filtros = [])
    {
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

    // --- Métodos para el panel de almacén ---

    public function obtenerPrestamoBasicoPorId($id_prestamo)
    {
        $this->db->query("SELECT p.IDpre, p.IDpersonas, p.IDelementos, e.nombreele 
                          FROM prestamos p 
                          JOIN elementos e ON p.IDelementos = e.IDele 
                          WHERE p.IDpre = :id_prestamo");
        $this->db->bind(':id_prestamo', $id_prestamo);
        return $this->db->single();
    }

    public function cambiarEstadoPeticion($id_prestamo, $nuevo_estado)
    {
        $transactionStarted = false;
        if (!$this->db->inTransaction()) {
            $this->db->beginTransaction();
            $transactionStarted = true;
        }

        try {
            // Actualizar autorización
            $this->db->query("UPDATE autorizacion 
                          SET estadoaut = :nuevo_estado 
                          WHERE IDaut = (SELECT IDautorizacion FROM prestamos WHERE IDpre = :id_prestamo)");
            $this->db->bind(':nuevo_estado', $nuevo_estado);
            $this->db->bind(':id_prestamo', $id_prestamo);
            if (!$this->db->execute()) {
                throw new Exception("Error al actualizar la autorización");
            }

            // Actualizar también el estado del préstamo para consistencia
            $this->db->query("UPDATE prestamos 
                              SET estado_autorizacion = :nuevo_estado 
                              WHERE IDpre = :id_prestamo");
            $this->db->bind(':nuevo_estado', $nuevo_estado);
            $this->db->bind(':id_prestamo', $id_prestamo);
            if (!$this->db->execute()) {
                throw new Exception("Error al actualizar el estado del préstamo");
            }

            if ($transactionStarted && $this->db->inTransaction()) {
                $this->db->commit();
            }
            return true;
        } catch (Exception $e) {
            if ($transactionStarted && $this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $e; // Re-lanzar la excepción
        }
    }

    public function rechazarPeticion($id_prestamo, $motivo = null)
    {
        error_log("Iniciando rechazo de préstamo ID: " . $id_prestamo . ", motivo: " . $motivo);
        $transactionStarted = false;
        if (!$this->db->inTransaction()) {
            $this->db->beginTransaction();
            $transactionStarted = true;
        }

        try {
            if (empty($id_prestamo)) {
                throw new Exception("ID de préstamo no proporcionado");
            }
            // 1. Obtener los detalles completos del préstamo con más información del elemento
            $this->db->query("SELECT p.*, a.estadoaut, e.cantidadele as stock_actual, 
                             e.estado as estado_elemento, e.IDele, e.nombreele,
                             pe.IDper as id_usuario, pe.nombrecompletoper as nombre_usuario
                             FROM prestamos p 
                             JOIN autorizacion a ON p.IDautorizacion = a.IDaut
                             JOIN elementos e ON p.IDelementos = e.IDele
                             JOIN personas pe ON p.IDpersonas = pe.IDper
                             WHERE p.IDpre = :id_prestamo");
            $this->db->bind(':id_prestamo', $id_prestamo);
            $prestamo = $this->db->single();

            if (!$prestamo) {
                error_log("Error: No se encontró el préstamo con ID: " . $id_prestamo);
                throw new Exception("Préstamo no encontrado.");
            }

            error_log("Datos del préstamo: " . print_r($prestamo, true));

            // 2. Actualizar el estado de la autorización a 'rechazado' (coincidir con enum)
            $this->db->query("UPDATE autorizacion SET estadoaut = 'rechazado' WHERE IDaut = :id_autorizacion");
            $this->db->bind(':id_autorizacion', $prestamo->IDautorizacion);
            if (!$this->db->execute()) {
                throw new Exception("Error al actualizar la autorización");
            }

            // 3. Actualizar el estado del préstamo
            $this->db->query("UPDATE prestamos SET estado_autorizacion = 'rechazado', motivo_rechazo = :motivo WHERE IDpre = :id_prestamo");
            $this->db->bind(':id_prestamo', $id_prestamo);
            $this->db->bind(':motivo', $motivo);
            if (!$this->db->execute()) {
                throw new Exception("Error al actualizar el estado del préstamo");
            }

            // 4. Devolver la cantidad al inventario independientemente del estado actual
            $nuevo_stock = $prestamo->stock_actual + $prestamo->cantidad;

            // Actualizar la cantidad en inventario
            $this->db->query("UPDATE elementos SET cantidadele = :nuevo_stock WHERE IDele = :id_elemento");
            $this->db->bind(':nuevo_stock', $nuevo_stock);
            $this->db->bind(':id_elemento', $prestamo->IDelementos);
            if (!$this->db->execute()) {
                throw new Exception("Error al actualizar el inventario");
            }

            // Si el stock estaba en 0 o el elemento estaba inactivo, volver a activar el elemento
            if ($nuevo_stock > 0) {
                $this->db->query("UPDATE elementos SET estado = 'activo' WHERE IDele = :id_elemento");
                $this->db->bind(':id_elemento', $prestamo->IDelementos);
                if (!$this->db->execute()) {
                    throw new Exception("Error al reactivar el elemento");
                }
            }

            // 5. No asignar elementos al usuario en caso de rechazo; solo se devuelve al inventario

            // 6. La notificación se creará desde el controlador para mantener separación de responsabilidades

            // 7. Confirmar la transacción
            if ($transactionStarted && $this->db->inTransaction()) {
                $this->db->commit();
            }
            error_log("Préstamo ID " . $id_prestamo . " rechazado exitosamente");
            return true;
        } catch (Exception $e) {
            if ($transactionStarted && $this->db->inTransaction()) {
                $this->db->rollBack();
            }
            error_log("Error al rechazar el préstamo ID " . $id_prestamo . ": " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            throw $e; // Re-lanzar la excepción para que el controlador la capture
        }
    }

    // --- Nuevos métodos para el panel de portería ---

    public function contarPeticionesAprobadas()
    {
        $this->db->query("SELECT COUNT(*) as total 
                          FROM prestamos pr
                          JOIN autorizacion a ON pr.IDautorizacion = a.IDaut
                          WHERE a.estadoaut = 'Aprobado' AND pr.estado_autorizacion = 'pendiente'");
        $resultado = $this->db->single();
        return $resultado ? (int) $resultado->total : 0;
    }

    public function obtenerPeticionesAprobadasPaginadas($limit, $offset)
    {
        $this->db->query('SELECT 
                            p.IDpre,
                            p.cantidad,
                            p.fecha_solicitud,
                            p.lugardetraslado,
                            p.cargopre,
                            p.formacionodependencia,
                            e.nombreele, 
                            e.codigoinventario,
                            e.descripcionele,
                            per.nombrecompletoper,
                            per.numerodoc,
                            per.tipodocumento,
                            p.estado_autorizacion,
                            p.IDelementos,
                            p.IDpersonas
                         FROM prestamos p
                         JOIN elementos e ON p.IDelementos = e.IDele
                         JOIN personas per ON p.IDpersonas = per.IDper
                         WHERE p.estado_autorizacion = "aprobado"
                         ORDER BY p.IDpre DESC
                         LIMIT :limit OFFSET :offset');

        $this->db->bind(':limit', $limit, PDO::PARAM_INT);
        $this->db->bind(':offset', $offset, PDO::PARAM_INT);

        return $this->db->resultSet();
    }

    public function obtenerDetallesPrestamoPorId($id_prestamo)
    {
        $this->db->query('SELECT 
                            p.IDpre, p.cantidad, p.fecha_solicitud, p.fecha_salida, p.fecha_devolucion,
                            p.lugardetraslado, p.cargopre, p.formacionodependencia,
                            p.observaciones_salida, p.observaciones_devolucion,
                            e.nombreele, e.codigoinventario, e.descripcionele,
                            per.nombrecompletoper, per.numerodoc, per.tipodocumento,
                            p.estado_autorizacion
                         FROM prestamos p
                         JOIN elementos e ON p.IDelementos = e.IDele
                         JOIN personas per ON p.IDpersonas = per.IDper
                         WHERE p.IDpre = :id_prestamo');
        $this->db->bind(':id_prestamo', $id_prestamo);
        return $this->db->single();
    }
    public function obtenerSalidasDelDia()
    {
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

    public function registrarSalidaElemento($data)
    {
        $this->db->beginTransaction();
        try {
            // Extraer datos del array
            $id_prestamo = $data['id_prestamo'];
            $fecha_salida = $data['fecha_salida']; // Asumiendo que es una fecha Y-m-d
            $observaciones_salida = $data['observaciones_salida'];

            // Primero, obtener el préstamo con los datos necesarios
            $this->db->query("SELECT p.*, e.IDele, e.cantidadele 
                             FROM prestamos p 
                             JOIN elementos e ON p.IDelementos = e.IDele 
                             WHERE p.IDpre = :id_prestamo");
            $this->db->bind(':id_prestamo', $id_prestamo);
            $prestamo = $this->db->single();

            if (!$prestamo) {
                throw new Exception("No se encontró el préstamo.");
            }

            // Mantener la inserción en marcaciones por compatibilidad
            $this->db->query("INSERT INTO marcaciones 
                             (hfecsalidamarc, estadomarc, IDpres, IDautori) 
                             VALUES 
                             (CONCAT(:fecha_salida, ' ', CURTIME()), 'activo', :id_prestamo, :id_autorizacion)");
            $this->db->bind(':fecha_salida', $fecha_salida);
            $this->db->bind(':id_prestamo', $id_prestamo);
            $this->db->bind(':id_autorizacion', $prestamo->IDautorizacion);
            $this->db->execute();

            // Actualizar el estado del préstamo a 'en_prestamo' y guardar los nuevos datos
            $this->db->query("UPDATE prestamos 
                              SET estado_autorizacion = 'en_prestamo',
                                  fecha_salida = CONCAT(:fecha_salida, ' ', CURTIME()),
                                  observaciones_salida = :observaciones_salida
                              WHERE IDpre = :id_prestamo");
            $this->db->bind(':id_prestamo', $id_prestamo);
            $this->db->bind(':fecha_salida', $fecha_salida);
            $this->db->bind(':observaciones_salida', $observaciones_salida);
            $this->db->execute();

            // Confirmar la transacción
            $this->db->commit();
            return true;

        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Error en registrarSalidaElemento: " . $e->getMessage());
            return false;
        }
    }

    public function obtenerPrestamosActivos()
    {
        $this->db->query('SELECT 
                            p.IDpre,
                            p.cantidad,
                            p.fecha_salida,
                            e.nombreele as nombre_elemento,
                            per.nombrecompletoper as nombre_persona
                         FROM prestamos p
                         JOIN elementos e ON p.IDelementos = e.IDele
                         JOIN personas per ON p.IDpersonas = per.IDper
                         WHERE p.estado_autorizacion = "en_prestamo"
                         ORDER BY p.fecha_salida DESC');

        return $this->db->resultSet();
    }

    public function registrarDevolucionElemento($data)
    {
        $this->db->beginTransaction();
        try {
            // 1. Obtener datos del préstamo para devolver al stock
            $this->db->query("SELECT IDelementos, cantidad FROM prestamos WHERE IDpre = :id_prestamo");
            $this->db->bind(':id_prestamo', $data['id_prestamo']);
            $prestamo = $this->db->single();

            if (!$prestamo) {
                throw new Exception("Préstamo no encontrado.");
            }

            // 2. Actualizar el estado del préstamo a 'devuelto'
            $this->db->query("UPDATE prestamos 
                              SET estado_autorizacion = 'devuelto',
                                  fecha_devolucion = NOW(),
                                  observaciones_devolucion = :observaciones
                              WHERE IDpre = :id_prestamo");
            $this->db->bind(':id_prestamo', $data['id_prestamo']);
            $this->db->bind(':observaciones', $data['observaciones_devolucion']);
            $this->db->execute();

            // 3. Devolver la cantidad al stock del elemento
            $this->db->query("UPDATE elementos 
                              SET cantidadele = cantidadele + :cantidad,
                                  estado = 'activo'
                              WHERE IDele = :id_elemento");
            $this->db->bind(':cantidad', $prestamo->cantidad);
            $this->db->bind(':id_elemento', $prestamo->IDelementos);
            $this->db->execute();

            // 4. Confirmar la transacción
            $this->db->commit();
            return true;

        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Error en registrarDevolucionElemento: " . $e->getMessage());
            return false;
        }
    }
    public function obtenerRegistrosPrestamos($filtro_tiempo = 'diario', $busqueda_texto = '', $filtro_estado = '')
    {
        $sql = "
            SELECT 
                p.IDpre,
                e.nombreele as nombre_elemento,
                per.nombrecompletoper as nombre_persona,
                p.fecha_salida,
                p.fecha_devolucion,
                p.estado_autorizacion as estado,
                p.observaciones_salida,
                p.observaciones_devolucion,
                p.cantidad,
                p.lugardetraslado,
                p.fecha_solicitud
            FROM prestamos p
            JOIN elementos e ON p.IDelementos = e.IDele
            JOIN personas per ON p.IDpersonas = per.IDper
            WHERE 1=1 AND p.estado_autorizacion IN ('en_prestamo', 'devuelto')
        ";

        $params = [];

        if ($filtro_tiempo === 'diario') {
            $sql .= " AND DATE(p.fecha_salida) = CURDATE() AND TIME(p.fecha_salida) BETWEEN '06:00:00' AND '23:00:00'";
        }

        if (!empty($busqueda_texto)) {
            $sql .= " AND (per.nombrecompletoper LIKE :busqueda_texto OR e.nombreele LIKE :busqueda_texto)";
            $params[':busqueda_texto'] = '%' . $busqueda_texto . '%';
        }

        if (!empty($filtro_estado)) {
            $sql .= " AND p.estado_autorizacion = :filtro_estado";
            $params[':filtro_estado'] = $filtro_estado;
        }

        $sql .= " ORDER BY p.fecha_salida DESC";

        $this->db->query($sql);

        foreach ($params as $key => &$val) {
            $this->db->bind($key, $val);
        }

        return $this->db->resultSet();
    }
  public function obtenerSolicitudesPendientesCuentadante($cuentadante_id)
{
    $this->db->query("
        SELECT
            a.id AS id_aprobacion,
            p.IDpre,
            p.fecha_solicitud AS fecha_prestamo,
            p.cantidad,
            per.nombrecompletoper AS solicitante_nombre,
            e.nombreele AS elemento_nombre,
            aut.estadoaut
        FROM aprobaciones a
        JOIN prestamos p ON a.IDprestamo = p.IDpre
        JOIN personas per ON p.IDpersonas = per.IDper
        JOIN elementos e ON p.IDelementos = e.IDele
        JOIN autorizacion aut ON p.IDautorizacion = aut.IDaut
        WHERE e.cuentadante_id = :cuentadante_id
          AND aut.estadoaut = 'pendiente_cuentadante'
          AND a.tipo_aprobacion = 'cuentadante'
          AND a.estado = 'pendiente'
        ORDER BY p.fecha_solicitud DESC
    ");
    $this->db->bind(':cuentadante_id', $cuentadante_id);
    return $this->db->resultSet();
}
    /**
     * Obtiene las estadísticas (aprobadas, rechazadas, total) para un cuentadante.
     */
    public function obtenerEstadisticasCuentadante($cuentadante_id)
    {
        $this->db->query("
            SELECT
                SUM(CASE WHEN a.estado = 'aprobado' THEN 1 ELSE 0 END) AS aprobadas,
                SUM(CASE WHEN a.estado = 'rechazado' THEN 1 ELSE 0 END) AS rechazadas,
                COUNT(a.id) AS total
            FROM aprobaciones a
            WHERE a.tipo_aprobacion = 'cuentadante'
              AND a.aprobado_por = :cuentadante_id
        ");
        $this->db->bind(':cuentadante_id', $cuentadante_id);
        $stats = $this->db->single();

        return [
            'aprobadas' => $stats->aprobadas ?? 0,
            'rechazadas' => $stats->rechazadas ?? 0,
            'total' => $stats->total ?? 0,
        ];
    }

    /**
     * Cuenta el número total de peticiones en el historial de un cuentadante para la paginación.
     */
    public function contarPeticionesHistorialCuentadante($cuentadante_id)
    {
        $this->db->query("
            SELECT COUNT(a.id) as total
            FROM aprobaciones a
            WHERE a.tipo_aprobacion = 'cuentadante'
              AND a.aprobado_por = :cuentadante_id
              AND a.estado IN ('aprobado', 'rechazado')
        ");
        $this->db->bind(':cuentadante_id', $cuentadante_id);
        $row = $this->db->single();
        return $row ? $row->total : 0;
    }

    /**
     * Obtiene el historial paginado de peticiones procesadas por un cuentadante.
     */
    public function obtenerPeticionesHistorialCuentadante($cuentadante_id, $limit, $offset)
    {
        $this->db->query("
            SELECT
                p.IDpre,
                per.nombrecompletoper AS solicitante,
                e.nombreele AS elemento_nombre,
                e.codigoinventario,
                p.cantidad,
                a.estado AS estadoaut
            FROM aprobaciones a
            JOIN prestamos p ON a.IDprestamo = p.IDpre
            JOIN personas per ON p.IDpersonas = per.IDper
            JOIN elementos e ON p.IDelementos = e.IDele
            WHERE a.tipo_aprobacion = 'cuentadante'
              AND a.aprobado_por = :cuentadante_id
              AND a.estado IN ('aprobado', 'rechazado')
            ORDER BY a.id DESC
            LIMIT :limit OFFSET :offset
        ");
        $this->db->bind(':cuentadante_id', $cuentadante_id);
        $this->db->bind(':limit', $limit);
        $this->db->bind(':offset', $offset);
        return $this->db->resultSet();
    }
     public function obtenerSolicitudesPendientesPorteria()
    {
        $this->db->query("
            SELECT 
                p.IDpre,
                p.fecha_solicitud,
                p.lugardetraslado,
                p.cantidad,
                per.nombre AS solicitante,
                per.tipodocumento,
                per.documento,
                e.nombreele AS elemento_nombre,
                e.codigoinventario,
                aut.estadoaut
            FROM prestamos p
            JOIN autorizacion aut ON p.IDautorizacion = aut.IDaut
            JOIN elementos e ON p.IDelementos = e.IDele
            JOIN personas per ON p.IDpersonas = per.IDper
            WHERE aut.estadoaut = 'pendiente_porteria'
            ORDER BY p.fecha_solicitud ASC
        ");

        return $this->db->resultSet();
    }
    public function contarPeticionesPorEstado($estado) {
        $this->db->query('
            SELECT COUNT(DISTINCT p.id) as total
            FROM peticiones p
            JOIN aprobaciones ap ON p.id = ap.peticion_id
            JOIN autorizacion a ON ap.autorizacion_id = a.IDaut
            WHERE a.estadoaut = :estado
        ');
        $this->db->bind(':estado', $estado);
        $row = $this->db->single();
        return $row->total;
    }

    public function obtenerPeticionesPorEstadoPaginadas($estado, $limit, $offset) {
        $this->db->query('
            SELECT p.*, u.nombre as solicitante_nombre, u.apellido as solicitante_apellido, a.estadoaut, a.IDaut as autorizacion_id
            FROM peticiones p
            JOIN usuarios u ON p.usuario_id = u.id
            JOIN aprobaciones ap ON p.id = ap.peticion_id
            JOIN autorizacion a ON ap.autorizacion_id = a.IDaut
            WHERE a.estadoaut = :estado
            GROUP BY p.id
            ORDER BY p.fecha_peticion DESC
            LIMIT :limit OFFSET :offset
        ');
        $this->db->bind(':estado', $estado);
        $this->db->bind(':limit', $limit);
        $this->db->bind(':offset', $offset);
        return $this->db->resultSet();
    }

    public function registrarSalidaPeticion($peticion_id, $usuario_porteria_id) {
        try {
            $this->db->beginTransaction();
    
            $this->db->query('SELECT autorizacion_id FROM aprobaciones WHERE peticion_id = :peticion_id');
            $this->db->bind(':peticion_id', $peticion_id);
            $aprobacion = $this->db->single();
    
            if (!$aprobacion) {
                throw new Exception("No se encontró la aprobación para la petición.");
            }
            $autorizacion_id = $aprobacion->autorizacion_id;
    
            $this->db->query('UPDATE autorizacion SET estadoaut = :nuevo_estado WHERE IDaut = :autorizacion_id AND estadoaut = :estado_actual');
            $this->db->bind(':nuevo_estado', 'en_prestamo');
            $this->db->bind(':autorizacion_id', $autorizacion_id);
            $this->db->bind(':estado_actual', 'pendiente_porteria');
            $this->db->execute();
            
            if ($this->db->rowCount() == 0) {
                throw new Exception("La petición no estaba en el estado correcto para ser autorizada.");
            }
    
            // Asumo que tienes una tabla 'salidas' para registrar el evento.
            // Si la tabla o los campos son diferentes, ajústalo.
            $this->db->query('INSERT INTO salidas (peticion_id, usuario_salida_id, fecha_salida) VALUES (:peticion_id, :usuario_id, NOW())');
            $this->db->bind(':peticion_id', $peticion_id);
            $this->db->bind(':usuario_id', $usuario_porteria_id);
            $this->db->execute();
    
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log($e->getMessage());
            return false;
        }
    }

}