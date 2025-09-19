<?php
class Admin extends Model
{
    protected $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    // Métodos para el dashboard principal
    public function obtenerEstadisticas()
    {
        $this->db->query("SELECT 
            (SELECT COUNT(*) FROM personas) as total_usuarios,
            (SELECT COUNT(*) FROM elementos) as total_elementos,
            (SELECT COUNT(*) FROM prestamos WHERE estado_autorizacion = 'pendiente') as peticiones_pendientes,
            (SELECT COUNT(*) FROM prestamos pr JOIN autorizacion a ON pr.IDautorizacion = a.IDaut WHERE a.estadoaut = 'Aprobado' 
            AND pr.estado_autorizacion = 'pendiente') as prestamos_activos,
            (SELECT COUNT(*) FROM roles WHERE rol = 'admin') as total_admins
        ");
        return $this->db->single();
    }

    public function obtenerUltimosUsuarios()
    {
        $this->db->query("SELECT p.nombrecompletoper, p.tipodocumento, p.numerodoc, r.rol 
                           FROM personas p
                           JOIN roles r ON p.IDper = r.idper
                           ORDER BY p.IDper DESC 
                           LIMIT 5");
        return $this->db->resultSet();
    }

    public function obtenerUltimosElementos()
    {
        $this->db->query("SELECT nombreele, descripcionele, estado, codigoele
                           FROM elementos 
                           ORDER BY IDele DESC 
                           LIMIT 5");
        return $this->db->resultSet();
    }

    // Métodos para la gestión de usuarios
    public function contarTotalUsuarios()
    {
        $this->db->query("SELECT COUNT(*) as total FROM personas");
        return $this->db->single()->total;
    }

    public function obtenerTodosLosUsuarios($limit, $offset)
    {
        $this->db->query("SELECT p.IDper, p.nombrecompletoper, p.tipodocumento, p.numerodoc, c.correocont, c.numerocont, r.rol, cu.estadocue
                           FROM personas p
                           LEFT JOIN contactos c ON p.IDper = c.IDperso
                           LEFT JOIN roles r ON p.IDper = r.idper
                           LEFT JOIN cuentas cu ON p.numerodoc = cu.numerodoc
                           ORDER BY p.IDper ASC
                           LIMIT :limit OFFSET :offset");
        $this->db->bind(':limit', $limit);
        $this->db->bind(':offset', $offset);
        return $this->db->resultSet();
    }

    // Métodos para la gestión de elementos
    public function contarTotalElementos()
    {
        $this->db->query("SELECT COUNT(*) as total FROM elementos");
        return $this->db->single()->total;
    }

    public function obtenerTodosLosElementos($limit, $offset)
    {
        
        $this->db->query("SELECT IDele, nombreele, descripcionele, caracteristicasele, estado, codigoele, codigoinventario, cantidadele 
                           FROM elementos
                           ORDER BY IDele ASC
                           LIMIT :limit OFFSET :offset");
        $this->db->bind(':limit', $limit);
        $this->db->bind(':offset', $offset);
        return $this->db->resultSet();
    }

    // Métodos para la gestión de almacenes/autorizaciones
    public function contarTotalAutorizaciones()
    {
        $this->db->query("SELECT COUNT(*) as total FROM autorizacion");
        return $this->db->single()->total;
    }

    public function obtenerTodasLasAutorizaciones($limit, $offset)
    {
        $this->db->query("SELECT * FROM autorizacion ORDER BY IDaut DESC LIMIT :limit OFFSET :offset");
        $this->db->bind(':limit', $limit);
        $this->db->bind(':offset', $offset);
        return $this->db->resultSet();
    }
    
    public function obtenerAutorizacionPorId($id)
    {
        $this->db->query("SELECT * FROM autorizacion WHERE IDaut = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }
    
    public function actualizarAutorizacion($data)
    {
        $this->db->query("UPDATE autorizacion SET 
                          VoBoCuentadanteaut = :VoBoCuentadanteaut, 
                          nomquienaturiza = :nomquienaturiza, 
                          cargoquienautoriza = :cargoquienautoriza, 
                          firmaquienautoriza = :firmaquienautoriza, 
                          estadoaut = :estadoaut 
                          WHERE IDaut = :id");
        
        $this->db->bind(':id', $data['id']);
        $this->db->bind(':VoBoCuentadanteaut', $data['VoBoCuentadanteaut']);
        $this->db->bind(':nomquienaturiza', $data['nomquienaturiza']);
        $this->db->bind(':cargoquienautoriza', $data['cargoquienautoriza']);
        $this->db->bind(':firmaquienautoriza', $data['firmaquienautoriza']);
        $this->db->bind(':estadoaut', $data['estadoaut']);
        
        return $this->db->execute();
    }
    
    public function autorizacionEnUso($id)
    {
        // Verificar en todas las tablas que tienen relación con autorizacion
        $tablas = ['prestamos', 'marcaciones', 'vigilantes'];
        $columnas = ['IDautorizacion', 'IDautori', 'idautorizacion'];
        
        for ($i = 0; $i < count($tablas); $i++) {
            $this->db->query("SELECT COUNT(*) as total FROM " . $tablas[$i] . " WHERE " . $columnas[$i] . " = :id");
            $this->db->bind(':id', $id);
            if ($this->db->single()->total > 0) {
                return true;
            }
        }
        
        return false;
    }
    
    public function eliminarAutorizacion($id)
    {
        try {
            // Verificar primero si la autorización está en uso
            if ($this->autorizacionEnUso($id)) {
                return false;
            }
            
            // Si no está en uso, proceder con la eliminación
            $this->db->query("DELETE FROM autorizacion WHERE IDaut = :id LIMIT 1");
            $this->db->bind(':id', $id);
            $this->db->execute();
            
            // Verificar si se afectó alguna fila
            return $this->db->rowCount() > 0;
        } catch (Exception $e) {
            error_log('Error al eliminar autorización: ' . $e->getMessage());
            return false;
        }
    }

    // Métodos para la creación de administradores/usuarios
    public function crearUsuarioCompleto($datos)
    {
        try {
            $this->db->beginTransaction();

            // Insertar en la tabla personas
            $this->db->query('INSERT INTO personas (tipodocumento, numerodoc, nombrecompletoper) VALUES (:tipo_documento, :numero_documento, :nombre)');
            $this->db->bind(':tipo_documento', $datos['tipo_documento']);
            $this->db->bind(':numero_documento', $datos['numero_documento']);
            $this->db->bind(':nombre', $datos['nombre']);
            $this->db->execute();
            $idPersona = $this->db->lastInsertId();

            // Insertar en la tabla cuentas
            $this->db->query('INSERT INTO cuentas (numerodoc, contracue, estadocue) VALUES (:numero_documento, :password, "activo")');
            $this->db->bind(':numero_documento', $datos['numero_documento']);
            $this->db->bind(':password', $datos['password']);
            $this->db->execute();

            // Insertar en la tabla contactos
            $this->db->query("INSERT INTO contactos (correocont, numerocont, direccioncont, IDperso, estadocont) VALUES (:email, :telefono, :direccion, :idPersona, 'activo')");
            $this->db->bind(':email', $datos['email']);
            $this->db->bind(':telefono', $datos['telefono']);
            $this->db->bind(':direccion', $datos['direccion']);
            $this->db->bind(':idPersona', $idPersona);
            $this->db->execute();

            // Insertar en la tabla roles
            $this->db->query("INSERT INTO roles (rol, estadorol, idper) VALUES (:rol, 'activo', :idPersona)");
            $this->db->bind(':rol', $datos['rol']);
            $this->db->bind(':idPersona', $idPersona);
            $this->db->execute();

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log('Error al crear usuario: ' . $e->getMessage() . ' - Datos: ' . json_encode($datos));
            return false;
        }
    }

     public function actualizarUsuario($datos) {
        // Obtener el número de documento actual antes de cualquier modificación
        $usuario_actual = $this->obtenerUsuarioPorId($datos['id']);
        if (!$usuario_actual) {
            error_log("actualizarUsuario: No se pudo encontrar el usuario actual con ID: " . $datos['id']);
            return false;
        }
        $numerodoc_actual = $usuario_actual->numerodoc;

        $this->db->beginTransaction();

        try {
            // 1. Actualizar la tabla 'personas'
            $this->db->query('UPDATE personas SET nombrecompletoper = :nombre, tipodocumento = :tipo_documento, numerodoc = :numerodoc WHERE IDper = :id');
            $this->db->bind(':id', $datos['id']);
            $this->db->bind(':nombre', $datos['nombre']);
            $this->db->bind(':tipo_documento', $datos['tipo_documento']);
            $this->db->bind(':numerodoc', $datos['numerodoc']);

            if (!$this->db->execute()) {
                error_log("Error al actualizar la tabla personas para el ID: " . $datos['id']);
                throw new Exception("Error al actualizar personas.");
            }

            // 2. Actualizar la tabla 'contactos'
            $this->db->query('UPDATE contactos SET numerocont = :telefono, direccioncont = :direccion, correocont = :email WHERE IDperso = :id');
            $this->db->bind(':id', $datos['id']);
            $this->db->bind(':telefono', $datos['telefono']);
            $this->db->bind(':direccion', $datos['direccion']);
            $this->db->bind(':email', $datos['email']);

            if (!$this->db->execute()) {
                error_log("Error al actualizar la tabla contactos para el IDperso: " . $datos['id']);
                throw new Exception("Error al actualizar contactos.");
            }

            // 3. Actualizar la tabla 'roles' - CORREGIDO
            $this->db->query('UPDATE roles SET rol = :rol WHERE idper = :id');
            $this->db->bind(':id', $datos['id']);
            $this->db->bind(':rol', $datos['rol']);

            if (!$this->db->execute()) {
                error_log("Error al actualizar la tabla roles para el idper: " . $datos['id']);
                throw new Exception("Error al actualizar roles.");
            }

            // 4. Construir y ejecutar la actualización para la tabla 'cuentas' (si es necesario)
            $update_fields = [];
            $bind_params = [];

            // Si el número de documento ha cambiado, hay que actualizarlo en 'cuentas'
            if ($datos['numerodoc'] !== $numerodoc_actual) {
                $update_fields[] = 'numerodoc = :numerodoc_nuevo';
                $bind_params[':numerodoc_nuevo'] = $datos['numerodoc'];
            }

            // Si se proporcionó una nueva contraseña, hay que actualizarla
            if (!empty($datos['password'])) {
                $hashed_password = password_hash($datos['password'], PASSWORD_DEFAULT);
                $update_fields[] = 'contracue = :password';
                $bind_params[':password'] = $hashed_password;
            }

            // Solo ejecutar la actualización si hay campos para cambiar
            if (!empty($update_fields)) {
                $sql_cuentas = 'UPDATE cuentas SET ' . implode(', ', $update_fields) . ' WHERE numerodoc = :numerodoc_actual';
                $this->db->query($sql_cuentas);

                // Vincular todos los parámetros necesarios
                foreach ($bind_params as $key => $value) {
                    $this->db->bind($key, $value);
                }
                $this->db->bind(':numerodoc_actual', $numerodoc_actual);

                if (!$this->db->execute()) {
                    error_log("Error al actualizar la tabla cuentas para el numerodoc: " . $numerodoc_actual);
                    throw new Exception("Error al actualizar cuentas.");
                }
            }

            // Si todo fue bien, confirmar la transacción
            if ($this->db->commit()) {
                return true;
            } else {
                error_log("Error al hacer commit de la transacción para el usuario ID: " . $datos['id']);
                $this->db->rollBack();
                return false;
            }

        } catch (Exception $e) {
            // Si algo falla, revertir la transacción
            error_log("Excepción en actualizarUsuario para ID " . $datos['id'] . ": " . $e->getMessage());
            $this->db->rollBack();
            return false;
        }
    }
    public function eliminarUsuarioPorId($id)
    {
        try {
            $this->db->beginTransaction();
            // eliminar roles
            $this->db->query('DELETE FROM roles WHERE idper = :id');
            $this->db->bind(':id', $id);
            $this->db->execute();
            // obtener numerodoc
            $this->db->query('SELECT numerodoc FROM personas WHERE IDper = :id');
            $this->db->bind(':id', $id);
            $row = $this->db->single();
            if ($row) {
                $this->db->query('DELETE FROM cuentas WHERE numerodoc = :num');
                $this->db->bind(':num', $row->numerodoc);
                $this->db->execute();
            }
            // contactos
            $this->db->query('DELETE FROM contactos WHERE IDperso = :id');
            $this->db->bind(':id', $id);
            $this->db->execute();
            // persona
            $this->db->query('DELETE FROM personas WHERE IDper = :id');
            $this->db->bind(':id', $id);
            $this->db->execute();
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log($e->getMessage());
            return false;
        }
    }

    public function obtenerAdministradores()
    {
        $this->db->query("
            SELECT 
                p.IDper,
                p.nombrecompletoper,
                co.correocont,
                r.rol,
                c.estadocue
            FROM 
                personas p
            JOIN 
                contactos co ON p.IDper = co.IDperso
            JOIN 
                cuentas c ON p.numerodoc = c.numerodoc
            JOIN 
                roles r ON p.IDper = r.idper
            WHERE 
                r.rol = :rol AND p.IDper != :current_user_id
        ");
        $this->db->bind(':rol', 'admin');
        $this->db->bind(':current_user_id', $_SESSION['user_id']);

        return $this->db->resultSet();
    }

    /**
     * Obtiene todos los usuarios para el formulario de reseteo de contraseña,
     * excluyendo al administrador que está realizando la operación.
     */
    public function obtenerUsuariosParaReset()
    {
        $this->db->query("
            SELECT p.IDper, p.nombrecompletoper, co.correocont as email
            FROM personas p
            JOIN contactos co ON p.IDper = co.IDperso
            WHERE p.IDper != :current_user_id
            ORDER BY p.nombrecompletoper ASC
        ");
        $this->db->bind(':current_user_id', $_SESSION['user_id']);
        return $this->db->resultSet();
    }

    public function obtenerUsuarioPorId($id)
    {
        $this->db->query("SELECT p.*, c.correocont, c.numerocont, c.direccioncont, r.rol, cu.estadocue
                           FROM personas p
                           LEFT JOIN contactos c ON p.IDper = c.IDperso
                           LEFT JOIN roles r ON p.IDper = r.idper
                           LEFT JOIN cuentas cu ON p.numerodoc = cu.numerodoc
                           WHERE p.IDper = :id LIMIT 1");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }
    
    public function obtenerUsuarioPorEmail($email)
    {
        $this->db->query("SELECT p.*, c.correocont 
                          FROM personas p
                          JOIN contactos c ON p.IDper = c.IDperso
                          WHERE c.correocont = :email LIMIT 1");
        $this->db->bind(':email', $email);
        return $this->db->single();
    }
    
    public function obtenerUsuarioPorDocumento($numero_documento)
    {
        $this->db->query("SELECT * FROM personas WHERE numerodoc = :numero_documento LIMIT 1");
        $this->db->bind(':numero_documento', $numero_documento);
        return $this->db->single();
    }
    
    public function verificarDocumentoExistente($numero_documento, $excluir_id = null)
    {
        $sql = "SELECT COUNT(*) as total FROM personas WHERE numerodoc = :numero_documento";
        if ($excluir_id) {
            $sql .= " AND IDper != :excluir_id";
        }
        
        $this->db->query($sql);
        $this->db->bind(':numero_documento', $numero_documento);
        if ($excluir_id) {
            $this->db->bind(':excluir_id', $excluir_id);
        }
        
        $resultado = $this->db->single();
        return $resultado->total > 0;
    }
    
    public function verificarEmailExistente($email, $excluir_id = null)
    {
        $sql = "SELECT COUNT(*) as total FROM contactos c 
                JOIN personas p ON c.IDperso = p.IDper 
                WHERE c.correocont = :email";
                
        if ($excluir_id) {
            $sql .= " AND p.IDper != :excluir_id";
        }
        
        $this->db->query($sql);
        $this->db->bind(':email', $email);
        if ($excluir_id) {
            $this->db->bind(':excluir_id', $excluir_id);
        }
        
        $resultado = $this->db->single();
        return $resultado->total > 0;
    }

    /**
     * Restablece la contraseña de un usuario a un valor temporal.
     * @param int $usuario_id
     * @return bool
     */
    public function restablecerPasswordUsuario($usuario_id)
    {
        // Contraseña temporal. Considera hacerla más aleatoria en el futuro.
        $nueva_password_temporal = "temp123";
        $password_hasheada = password_hash($nueva_password_temporal, PASSWORD_DEFAULT);

        // Actualiza en cuentas usando el numerodoc relacionado al IDper
        $this->db->query("UPDATE cuentas c 
                          JOIN personas p ON c.numerodoc = p.numerodoc 
                          SET c.contracue = :password 
                          WHERE p.IDper = :usuario_id");
        $this->db->bind(':password', $password_hasheada);
        $this->db->bind(':usuario_id', $usuario_id);

        return $this->db->execute();
    }

    /**
     * Obtiene la lista de roles únicos para los formularios.
     * @return array
     */
    public function obtenerRoles()
    {
        $this->db->query("SELECT DISTINCT rol FROM roles ORDER BY rol ASC");
        return $this->db->resultSet();
    }

    public function obtenerEstadisticasAvanzadas()
    {
        $this->db->query("SELECT (SELECT COUNT(*) FROM personas) as total_usuarios");
        $total_usuarios = $this->db->single()->total_usuarios;

        $this->db->query("SELECT COUNT(*) as total_elementos FROM elementos");
        $total_elementos = $this->db->single()->total_elementos;

        $this->db->query("SELECT COUNT(*) as peticiones_hoy FROM prestamos WHERE DATE(fecha_solicitud) = CURDATE()");
        $peticiones_hoy = $this->db->single()->peticiones_hoy;

        // Simulado por ahora
        $alertas_seguridad = 0;

        $this->db->query("SELECT rol, COUNT(*) as count FROM roles GROUP BY rol");
        $distribucion_roles = $this->db->resultSet();

        $this->db->query("SELECT estado, COUNT(*) as count FROM elementos GROUP BY estado");
        $distribucion_inventario = $this->db->resultSet();

        $this->db->query("SELECT e.nombreele, COUNT(p.IDelementos) as total_solicitudes FROM prestamos p JOIN elementos e ON p.IDelementos = e.IDele GROUP BY p.IDelementos ORDER BY total_solicitudes DESC LIMIT 5");
        $top_elementos = $this->db->resultSet();

        $this->db->query("SELECT AVG(TIMESTAMPDIFF(HOUR, p.fecha_solicitud, p.fecha_salida)) as avg_hours FROM prestamos p JOIN autorizacion a ON p.IDautorizacion = a.IDaut WHERE p.fecha_salida IS NOT NULL AND a.estadoaut = 'aprobado'");
        $avg_hours_row = $this->db->single();
        $avg_hours = $avg_hours_row ? $avg_hours_row->avg_hours : null;
        $tiempo_aprobacion_promedio = $avg_hours ? round($avg_hours, 1) . ' horas' : 'N/A';

        return [
            'total_usuarios' => $total_usuarios,
            'total_elementos' => $total_elementos,
            'peticiones_hoy' => $peticiones_hoy,
            'alertas_seguridad' => $alertas_seguridad,
            'distribucion_roles' => $distribucion_roles,
            'distribucion_inventario' => $distribucion_inventario,
            'top_elementos' => $top_elementos,
            'tiempo_aprobacion_promedio' => $tiempo_aprobacion_promedio
        ];
    }

    /**
     * Obtiene un listado paginado del historial de peticiones (préstamos) gestionadas.
     * Une las tablas prestamos, aprobaciones, elementos y personas.
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function obtenerHistorialPeticiones($offset, $limit)
    {
        $this->db->query("
            SELECT
                pr.IDpre,
                a.id AS id_aprobacion,
                p.nombrecompletoper AS solicitante,
                e.nombreele AS elemento,
                a.estado,
                a.fecha_aprobacion,
                a.fecha_rechazo,
                a.motivo
            FROM
                aprobaciones a
            JOIN
                prestamos pr ON a.IDprestamo = pr.IDpre
            JOIN
                personas p ON pr.IDpersonas = p.IDper
            JOIN
                elementos e ON pr.IDelementos = e.IDele
            WHERE
                a.estado IN ('aprobado', 'rechazado')
            ORDER BY
                a.fecha_creacion DESC
            LIMIT :limit OFFSET :offset
        ");

        // Añadir los binds que faltaban
        $this->db->bind(':limit', $limit, PDO::PARAM_INT);
        $this->db->bind(':offset', $offset, PDO::PARAM_INT);

        return $this->db->resultSet();
    }

    public function obtenerTotalHistorialPeticiones()
    {
        $this->db->query("SELECT COUNT(*) as total FROM aprobaciones WHERE tipo_aprobacion = 'almacen'");
        return $this->db->single()->total;
    }


    // --- Exportar / Importar base de datos ---
    public function exportarBaseDeDatos() {
        // Obtener todas las tablas
        $this->db->query("SHOW TABLES");
        $tablas = $this->db->resultSet();
        if (!$tablas) return '';

        $dump = "-- Export generado por Admin::exportarBaseDeDatos\n";
        $dump .= "SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';\nSTART TRANSACTION;\nSET time_zone = '+00:00';\n\n";

        foreach ($tablas as $row) {
            $nombreTabla = array_values((array)$row)[0];
            // Estructura
            $this->db->query("SHOW CREATE TABLE `{$nombreTabla}`");
            $create = $this->db->single();
            $createSql = array_values((array)$create)[1] ?? '';
            $dump .= "\n-- ----------------------------\n-- Estructura de tabla {$nombreTabla}\n-- ----------------------------\n";
            $dump .= "DROP TABLE IF EXISTS `{$nombreTabla}`;\n";
            $dump .= $createSql . ";\n\n";

            // Datos
            $this->db->query("SELECT * FROM `{$nombreTabla}`");
            $rows = $this->db->resultSet();
            if ($rows && count($rows) > 0) {
                $columnas = array_keys(get_object_vars($rows[0]));
                $colList = '`' . implode('`,`', $columnas) . '`';
                $dump .= "-- Volcado de datos para la tabla {$nombreTabla}\n";
                foreach ($rows as $r) {
                    $vals = [];
                    foreach ($columnas as $c) {
                        $val = isset($r->$c) ? $r->$c : null;
                        if ($val === null) { $vals[] = 'NULL'; }
                        else { $vals[] = $this->quoteSql($val); }
                    }
                    $dump .= "INSERT INTO `{$nombreTabla}` ({$colList}) VALUES (" . implode(',', $vals) . ");\n";
                }
                $dump .= "\n";
            }
        }

        $dump .= "COMMIT;\n";
        return $dump;
    }

    private function quoteSql($value) {
        // Escapar comillas y backslashes
        $escaped = str_replace(["\\", "'"], ["\\\\", "\\'"], (string)$value);
        return "'{$escaped}'";
    }

    public function importarBaseDeDatos($sqlContenido) {
        try {
            // Primero, crear la base de datos si no existe
            $this->crearBaseDeDatosSiNoExiste();
            
            // Limpiar el contenido SQL
            $sqlContenido = $this->limpiarContenidoSQL($sqlContenido);
            
            // Dividir en statements individuales
            $statements = $this->dividirEnStatements($sqlContenido);
            
            // Configurar MySQL para la importación
            $this->configurarMySQLParaImportacion();
            
            $this->db->beginTransaction();
            
            foreach ($statements as $stmt) {
                $stmt = trim($stmt);
                if (empty($stmt)) continue;
                
                // Saltar comandos específicos de phpMyAdmin que pueden causar problemas
                if ($this->esComandoAIgnorar($stmt)) continue;
                
                try {
                    $this->db->query($stmt);
                    $this->db->execute();
                } catch (Exception $e) {
                    // Log del error pero continuar con otros statements
                    error_log('Error en statement: ' . substr($stmt, 0, 100) . '... Error: ' . $e->getMessage());
                    // Solo fallar en errores críticos
                    if ($this->esCritico($e)) {
                        throw $e;
                    }
                }
            }
            
            $this->db->commit();
            return true;
            
        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            error_log('Error importando DB: ' . $e->getMessage());
            return false;
        }
    }
    
    private function crearBaseDeDatosSiNoExiste() {
        try {
            // Conectar sin especificar base de datos
            $dsn = 'mysql:host=' . DB_HOST;
            $pdo = new PDO($dsn, DB_USER, DB_PASS);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Crear la base de datos si no existe
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci");
            $pdo->exec("USE `" . DB_NAME . "`");
            
        } catch (Exception $e) {
            error_log('Error creando base de datos: ' . $e->getMessage());
        }
    }
    
    private function limpiarContenidoSQL($contenido) {
        // Remover BOM si existe
        $contenido = preg_replace('/^\xEF\xBB\xBF/', '', $contenido);
        
        // Normalizar saltos de línea
        $contenido = str_replace(["\r\n", "\r"], "\n", $contenido);
        
        return $contenido;
    }
    
    private function dividirEnStatements($contenido) {
        $statements = [];
        $buffer = '';
        $inString = false;
        $stringChar = '';
        $escaped = false;
        
        $lines = explode("\n", $contenido);
        
        foreach ($lines as $line) {
            $line = trim($line);
            
            // Saltar líneas vacías y comentarios
            if (empty($line) || preg_match('/^(--|#|\/\*)/', $line)) {
                continue;
            }
            
            // Procesar caracteres para detectar strings y puntos y coma
            for ($i = 0; $i < strlen($line); $i++) {
                $char = $line[$i];
                
                if ($escaped) {
                    $escaped = false;
                    continue;
                }
                
                if ($char === '\\') {
                    $escaped = true;
                    continue;
                }
                
                if (!$inString && ($char === '"' || $char === "'")) {
                    $inString = true;
                    $stringChar = $char;
                } elseif ($inString && $char === $stringChar) {
                    $inString = false;
                    $stringChar = '';
                } elseif (!$inString && $char === ';') {
                    // Encontramos el final de un statement
                    $buffer .= substr($line, 0, $i + 1);
                    if (!empty(trim($buffer))) {
                        $statements[] = $buffer;
                    }
                    $buffer = '';
                    $line = substr($line, $i + 1);
                    $i = -1; // Reiniciar el bucle
                    continue;
                }
            }
            
            $buffer .= $line . " ";
        }
        
        // Agregar el último statement si no termina en punto y coma
        if (!empty(trim($buffer))) {
            $statements[] = $buffer;
        }
        
        return $statements;
    }
    
    private function configurarMySQLParaImportacion() {
        try {
            $this->db->query("SET FOREIGN_KEY_CHECKS=0");
            $this->db->execute();
            
            $this->db->query("SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO'");
            $this->db->execute();
            
            $this->db->query("SET AUTOCOMMIT = 0");
            $this->db->execute();
            
            $this->db->query("SET time_zone = '+00:00'");
            $this->db->execute();
        } catch (Exception $e) {
            error_log('Error configurando MySQL: ' . $e->getMessage());
        }
    }
    
    private function esComandoAIgnorar($stmt) {
        $comandosIgnorar = [
            'START TRANSACTION',
            'COMMIT',
            'SET SQL_MODE',
            'SET time_zone',
            'SET AUTOCOMMIT',
            'SET FOREIGN_KEY_CHECKS',
            '/*!40101 SET',
            '/*!40000 ALTER TABLE',
            'LOCK TABLES',
            'UNLOCK TABLES'
        ];
        
        foreach ($comandosIgnorar as $comando) {
            if (stripos($stmt, $comando) === 0) {
                return true;
            }
        }
        
        return false;
    }
    
    private function esCritico($exception) {
        $mensajeError = $exception->getMessage();
        
        // Errores que deben detener la importación
        $erroresCriticos = [
            'Access denied',
            'Unknown database',
            'Table doesn\'t exist',
            'Duplicate entry for key \'PRIMARY\''
        ];
        
        foreach ($erroresCriticos as $error) {
            if (stripos($mensajeError, $error) !== false) {
                return true;
            }
        }
        
        return false;
    }

    public function eliminarBaseDeDatos() {
        try {
            // Verificar que la base de datos existe y tiene tablas
            $this->db->query("SELECT COUNT(*) as count FROM information_schema.tables WHERE table_schema = :dbname");
            $this->db->bind(':dbname', DB_NAME);
            $result = $this->db->single();
            
            if (!$result || $result->count == 0) {
                error_log('No hay tablas para eliminar en la base de datos: ' . DB_NAME);
                return true; // Consideramos exitoso si ya no hay tablas
            }
            
            // Configurar MySQL para eliminación segura
            $this->configurarMySQLParaEliminacion();
            
            // Obtener todas las tablas de la base de datos específica
            $this->db->query("SELECT table_name FROM information_schema.tables WHERE table_schema = :dbname ORDER BY table_name");
            $this->db->bind(':dbname', DB_NAME);
            $tablas = $this->db->resultSet();
            
            if (!$tablas || empty($tablas)) {
                error_log('No se pudieron obtener las tablas de la base de datos');
                return false;
            }
            
            // Eliminar tablas en orden inverso para evitar problemas de foreign keys
            $tablasArray = [];
            foreach ($tablas as $row) {
                $tablasArray[] = $row->table_name;
            }
            
            // Eliminar todas las tablas
            foreach (array_reverse($tablasArray) as $tabla) {
                try {
                    $this->db->query("DROP TABLE IF EXISTS `{$tabla}`");
                    $this->db->execute();
                    error_log("Tabla eliminada: {$tabla}");
                } catch (Exception $e) {
                    error_log("Error eliminando tabla {$tabla}: " . $e->getMessage());
                    // Continuar con las demás tablas
                }
            }
            
            // Verificar que todas las tablas fueron eliminadas
            $this->db->query("SELECT COUNT(*) as count FROM information_schema.tables WHERE table_schema = :dbname");
            $this->db->bind(':dbname', DB_NAME);
            $verificacion = $this->db->single();
            
            if ($verificacion && $verificacion->count > 0) {
                error_log('Algunas tablas no pudieron ser eliminadas. Tablas restantes: ' . $verificacion->count);
                return false;
            }
            
            // Restaurar configuración de MySQL
            $this->restaurarConfiguracionMySQL();
            
            error_log('Base de datos eliminada exitosamente');
            return true;
            
        } catch (Exception $e) {
            error_log('Error eliminando BD: ' . $e->getMessage());
            // Intentar restaurar configuración en caso de error
            try {
                $this->restaurarConfiguracionMySQL();
            } catch (Exception $restoreError) {
                error_log('Error restaurando configuración: ' . $restoreError->getMessage());
            }
            return false;
        }
    }
    
    private function configurarMySQLParaEliminacion() {
        try {
            // Deshabilitar foreign key checks
            $this->db->query("SET FOREIGN_KEY_CHECKS=0");
            $this->db->execute();
            
            // Deshabilitar autocommit para mejor control
            $this->db->query("SET AUTOCOMMIT=0");
            $this->db->execute();
            
            error_log('MySQL configurado para eliminación de tablas');
        } catch (Exception $e) {
            error_log('Error configurando MySQL para eliminación: ' . $e->getMessage());
            throw $e;
        }
    }
    
    private function restaurarConfiguracionMySQL() {
        try {
            // Rehabilitar foreign key checks
            $this->db->query("SET FOREIGN_KEY_CHECKS=1");
            $this->db->execute();
            
            // Rehabilitar autocommit
            $this->db->query("SET AUTOCOMMIT=1");
            $this->db->execute();
            
            error_log('Configuración de MySQL restaurada');
        } catch (Exception $e) {
            error_log('Error restaurando configuración MySQL: ' . $e->getMessage());
        }
    }
    
    public function verificarEstadoBaseDeDatos() {
        try {
            $this->db->query("SELECT COUNT(*) as count FROM information_schema.tables WHERE table_schema = :dbname");
            $this->db->bind(':dbname', DB_NAME);
            $result = $this->db->single();
            
            return [
                'existe' => true,
                'tablas' => $result ? (int)$result->count : 0,
                'vacia' => $result ? ((int)$result->count === 0) : true
            ];
        } catch (Exception $e) {
            error_log('Error verificando estado de BD: ' . $e->getMessage());
            return [
                'existe' => false,
                'tablas' => 0,
                'vacia' => true
            ];
        }
    }
}