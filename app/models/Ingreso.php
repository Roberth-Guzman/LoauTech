<?php

class Ingreso extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function registrarElemento($datos)
    {
        try {
            // Se añade hora_entrada a la consulta con el valor NOW() para que la base de datos inserte la fecha y hora actual
            $sql = "INSERT INTO ingresoelementos (nombreingele, tipoelemento, descripcioningele, observacioningele, IDPER, serial, hora_entrada) VALUES (:nombre, :tipo, :descripcion, :observacion, :id_persona, :serial, NOW())";

            $this->db->query($sql);

            // Bindeo de valores
            $this->db->bind(':nombre', $datos['nombre']);
            $this->db->bind(':tipo', $datos['tipo']);
            $this->db->bind(':descripcion', $datos['descripcion']);
            $this->db->bind(':observacion', $datos['observacion']);
            $this->db->bind(':id_persona', $datos['id_persona']);
            $this->db->bind(':serial', $datos['serial']);

            // Ejecutar
            if ($this->db->execute()) {
                return true;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            // Manejo de errores de la base de datos
            // Puedes loggear el error si lo necesitas: error_log($e->getMessage());
            return false;
        }
    }

    public function obtenerIngresosPorUsuario($usuario_id)
    {
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
    public function obtenerIngresosConFiltro($filtro_tiempo = 'general')
    {
        $sql = "
            SELECT p.nombrecompletoper, p.numerodoc, ie.* 
            FROM ingresoelementos ie
            JOIN personas p ON ie.IDPER = p.IDper
        ";

        if ($filtro_tiempo == 'diario') {
            // Filtra los registros del día actual entre las 6:00 y las 22:59
            $sql .= " WHERE DATE(ie.hora_entrada) = CURDATE() AND HOUR(ie.hora_entrada) >= 6 AND HOUR(ie.hora_entrada) < 23";
        }

        $sql .= " ORDER BY ie.hora_entrada DESC";

        $this->db->query($sql);
        return $this->db->resultSet();
    }

    public function obtenerTodosLosIngresos()
    {
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
    public function registrarSalida($id)
    {
        $this->db->query("UPDATE ingresoelementos SET hora_salida = NOW() WHERE IDingele = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    public function obtenerRegistrosParaInformeHorario($filtros)
    {
        $sql = "SELECT 
                    ie.IDingele as id,
                    ie.nombreingele as nombre,
                    ie.serial as codigo,
                    ie.tipoelemento as tipo,
                    ie.descripcioningele as marca,
                    ie.observacioningele as modelo,
                    DATE_FORMAT(ie.hora_entrada, '%h:%i %p') as hora_registro,
                    CASE 
                        WHEN ie.hora_salida IS NOT NULL THEN 'Registrado'
                        ELSE 'Pendiente'
                    END as estado,
                    p.nombrecompletoper,
                    p.numerodoc
                FROM 
                    ingresoelementos ie
                JOIN
                    personas p ON ie.IDPER = p.IDper
                WHERE 1=1";

        $params = [];

        // Filtro por fecha
        if (!empty($filtros['fecha_inicio'])) {
            $sql .= " AND DATE(ie.hora_entrada) = :fecha_inicio";
            $params[':fecha_inicio'] = $filtros['fecha_inicio'];
        }

        // Filtro por tipo de elemento
        if (!empty($filtros['tipo_elemento']) && $filtros['tipo_elemento'] !== 'todos') {
            $sql .= " AND ie.tipoelemento = :tipo_elemento";
            $params[':tipo_elemento'] = $filtros['tipo_elemento'];
        }

        $sql .= " ORDER BY ie.hora_entrada DESC";

        $this->db->query($sql);
        foreach ($params as $key => &$val) {
            $this->db->bind($key, $val);
        }

        $registros = $this->db->resultSet();
        $total = $this->db->rowCount();

        return [
            'registros' => $registros,
            'total' => $total
        ];
    }
     public function obtenerTiposElementos()
    {
        // Definimos las categorías predeterminadas
        $categorias_predeterminadas = [
            'Equipos de Cómputo y Periféricos',
            'Equipos de Audio y Video',
            'Instrumentos Musicales',
            'Herramientas y Kits Técnicos',
            'Material Didáctico Específico',
            'Otro'
        ];

        // Convertimos el array a objetos stdClass para mantener consistencia con lo que la vista espera
        $tipos = array_map(function($categoria) {
            $obj = new stdClass();
            $obj->tipoelemento = $categoria;
            return $obj;
        }, $categorias_predeterminadas);

        return $tipos;
    }

    public function obtenerInventarioPorUsuario($usuario_id)
    {
        $this->db->query("
            SELECT 
                nombreingele, 
                serial,
                tipoelemento,
                descripcioningele,
                COALESCE(NULLIF(serial, ''), nombreingele) as identificador_unico
            FROM 
                ingresoelementos
            WHERE 
                IDPER = :usuario_id
            GROUP BY 
                nombreingele, serial, tipoelemento, descripcioningele, identificador_unico
            ORDER BY 
                nombreingele
        ");
        $this->db->bind(':usuario_id', $usuario_id);
        return $this->db->resultSet();
    }

    public function obtenerIngresosDeHoyPorUsuario($usuario_id)
    {
        $this->db->query("
            SELECT IDingele, nombreingele, tipoelemento, descripcioningele, observacioningele, serial, hora_entrada, hora_salida 
            FROM ingresoelementos 
            WHERE IDPER = :usuario_id 
            AND DATE(hora_entrada) = CURDATE()
            ORDER BY hora_entrada DESC
        ");
        $this->db->bind(':usuario_id', $usuario_id);
        return $this->db->resultSet();
    }

    public function obtenerHistorialIngresos($idUsuario, $limit, $offset)
    {
        $this->db->query("
            SELECT nombreingele, serial, tipoelemento, hora_entrada, hora_salida
            FROM ingresoelementos
            WHERE IDPER = :id_usuario
            ORDER BY hora_entrada DESC
            LIMIT :limit OFFSET :offset
        ");
        $this->db->bind(':id_usuario', $idUsuario);
        $this->db->bind(':limit', $limit);
        $this->db->bind(':offset', $offset);
        return $this->db->resultSet();
    }

     public function contarHistorialIngresos($idUsuario)
    {
        $this->db->query("SELECT COUNT(*) as total FROM ingresoelementos WHERE IDPER = :id_usuario");
        $this->db->bind(':id_usuario', $idUsuario);
        $row = $this->db->single();
        return $row->total;
    }

    public function registrarIngresoPorDocumento($documento)
    {
        // 1. Encontrar a la persona
        $this->db->query('SELECT IDper FROM personas WHERE numerodoc = :documento');
        $this->db->bind(':documento', $documento);
        $persona = $this->db->single();

        if (!$persona) {
            return ['exito' => false, 'mensaje' => 'Usuario no encontrado.'];
        }
        $idUsuario = $persona->IDper;

        // 2. Verificar si ya tiene un ingreso activo hoy para evitar duplicados
        $this->db->query("SELECT COUNT(*) as total FROM ingresoelementos WHERE IDPER = :id_usuario AND DATE(hora_entrada) = CURDATE() AND hora_salida IS NULL");
        $this->db->bind(':id_usuario', $idUsuario);
        if ($this->db->single()->total > 0) {
            return ['exito' => false, 'mensaje' => 'Este usuario ya tiene un registro de ingreso activo hoy.'];
        }

        // 3. Obtener el inventario "maestro" del usuario
        $this->db->query("
            SELECT nombreingele, serial, tipoelemento, descripcioningele
            FROM ingresoelementos
            WHERE IDPER = :id_usuario
            GROUP BY nombreingele, serial, tipoelemento, descripcioningele
        ");
        $this->db->bind(':id_usuario', $idUsuario);
        $inventario = $this->db->resultSet();

        if (empty($inventario)) {
            return ['exito' => false, 'mensaje' => 'El usuario no tiene elementos en su inventario para registrar.'];
        }

        // 4. Iniciar transacción
        $this->db->beginTransaction();
        try {
            $sql = "INSERT INTO ingresoelementos (nombreingele, tipoelemento, descripcioningele, IDPER, serial, hora_entrada, observacioningele) VALUES (:nombre, :tipo, :descripcion, :id_persona, :serial, NOW(), 'Ingreso ordinario')";
            
            foreach ($inventario as $item) {
                $this->db->query($sql);
                $this->db->bind(':nombre', $item->nombreingele);
                $this->db->bind(':tipo', $item->tipoelemento);
                $this->db->bind(':descripcion', $item->descripcioningele);
                $this->db->bind(':id_persona', $idUsuario);
                $this->db->bind(':serial', $item->serial);
                $this->db->execute();
            }

            $this->db->commit();
            return ['exito' => true, 'mensaje' => 'Ingreso registrado correctamente.'];

        } catch (Exception $e) {
            $this->db->rollBack();
            return ['exito' => false, 'mensaje' => 'Error al registrar el ingreso.'];
        }
    }

    public function registrarSalidaPorDocumento($documento) {
        // Verificar si hay registros activos para ese documento
        $this->db->query("
            SELECT ie.IDingele 
            FROM ingresoelementos ie
            JOIN personas p ON ie.IDPER = p.IDper
            WHERE p.numerodoc = :documento AND ie.hora_salida IS NULL AND DATE(ie.hora_entrada) = CURDATE()
        ");
        $this->db->bind(':documento', $documento);
        $registros_activos = $this->db->resultSet();

        if (empty($registros_activos)) {
            return ['exito' => false, 'mensaje' => 'No se encontraron registros de ingreso activos para este documento hoy.'];
        }

        // Si hay, registrar la salida
        $sql = "UPDATE ingresoelementos ie
                JOIN personas p ON ie.IDPER = p.IDper
                SET ie.hora_salida = NOW()
                WHERE p.numerodoc = :documento AND ie.hora_salida IS NULL AND DATE(ie.hora_entrada) = CURDATE()";
        $this->db->query($sql);
        $this->db->bind(':documento', $documento);
        
        if ($this->db->execute()) {
            return ['exito' => true, 'mensaje' => 'Salida registrada correctamente.'];
        } else {
            return ['exito' => false, 'mensaje' => 'Error al registrar la salida.'];
        }
    }

    public function obtenerRegistrosDeHoy()
    {
        $this->db->query("
            SELECT 
                p.numerodoc,
                p.nombrecompletoper,
                MIN(ie.hora_entrada) AS hora_ingreso,
                MAX(ie.hora_salida) AS hora_salida
            FROM 
                ingresoelementos ie
            JOIN 
                personas p ON ie.IDPER = p.IDper
            WHERE 
                DATE(ie.hora_entrada) = CURDATE()
            GROUP BY
                p.IDper, p.numerodoc, p.nombrecompletoper, DATE(ie.hora_entrada)
            ORDER BY 
                hora_ingreso DESC
        ");
        return $this->db->resultSet();
    }

    public function obtenerFechasConRegistros()
    {
        $this->db->query("
            SELECT DISTINCT DATE(hora_entrada) as fecha
            FROM ingresoelementos
            ORDER BY fecha DESC
        ");
        $results = $this->db->resultSet();
        $fechas = [];
        foreach ($results as $row) {
            $fechas[] = $row->fecha;
        }
        return $fechas;
    }

    public function obtenerUltimaFechaConRegistro()
    {
        $this->db->query("SELECT MAX(DATE(hora_entrada)) as ultima_fecha FROM ingresoelementos");
        $resultado = $this->db->single();
        return $resultado ? $resultado->ultima_fecha : null;
    }
}