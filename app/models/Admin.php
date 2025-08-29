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

    // Métodos para la creación de administradores/usuarios
    public function crearUsuarioCompleto($data)
    {
        $this->db->beginTransaction();

        try {
            // 1. Insertar en la tabla 'personas'
            $this->db->query('INSERT INTO personas (tipodocumento, numerodoc, nombrecompletoper) VALUES (:tipo_documento, :numero_documento, :nombre)');
            $this->db->bind(':tipo_documento', $data['tipo_documento']);
            $this->db->bind(':numero_documento', $data['numero_documento']);
            $this->db->bind(':nombre', $data['nombre']);
            $this->db->execute();
            $id_persona = $this->db->lastInsertId();

            // 2. Insertar en la tabla 'cuentas'
            $this->db->query('INSERT INTO cuentas (numerodoc, contracue, estadocue) VALUES (:numero_documento, :password, \'activo\')');
            $this->db->bind(':numero_documento', $data['numero_documento']);
            $this->db->bind(':password', $data['password']);
            $this->db->execute();

            // 3. Insertar en la tabla 'contactos'
            $this->db->query('INSERT INTO contactos (correocont, numerocont, direccioncont, IDperso) VALUES (:email, :telefono, :direccion, :id_persona)');
            $this->db->bind(':email', $data['email']);
            $this->db->bind(':telefono', $data['telefono']);
            $this->db->bind(':direccion', $data['direccion']);
            $this->db->bind(':id_persona', $id_persona);
            $this->db->execute();

            // 4. Insertar la asignación de rol en la tabla 'roles'
            $this->db->query('INSERT INTO roles (rol, estadorol, idper) VALUES (:rol, 1, :id_persona)');
            $this->db->bind(':rol', $data['rol']);
            $this->db->bind(':id_persona', $id_persona);
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

    /**
     * Restablece la contraseña de un usuario a un valor temporal.
     * @param int $usuario_id
     * @return bool
     */
    public function restablecerPasswordUsuario($usuario_id)
    {
        // Contraseña temporal. Considera hacerla más aleatoria en el futuro.
        $nueva_password_temporal = "temp123";
        $password_hasheada = password_hash($nueva_password_temporal, PASSWORD_BCRYPT);

        $this->db->query("UPDATE cuentas SET password = :password WHERE IDper = :usuario_id");
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

    /**
     * Obtiene todos los datos necesarios para el dashboard de superadministrador.
     * @return array
     */
    public function getSuperAdminDashboardData()
    {
        $data = [];

        // --- KPIs ---
        $this->db->query("SELECT COUNT(*) as total FROM personas");
        $data['total_usuarios'] = $this->db->single()->total ?? 0;

        $this->db->query("SELECT COUNT(*) as total FROM elementos");
        $data['total_elementos'] = $this->db->single()->total ?? 0;

        // Se cambió 'peticiones' por 'prestamos' para corregir el error.
        // No se encontró una columna de fecha para filtrar por día, por lo que esto muestra el total de préstamos.
        $this->db->query("SELECT COUNT(*) as total FROM prestamos");
        $data['peticiones_hoy'] = $this->db->single()->total ?? 0;

        // No hay una fuente de datos para "Alertas de Seguridad", se deja un valor por defecto.
        $data['alertas_seguridad'] = 3; // Valor de ejemplo

        // --- Datos para Gráficos ---
        // Distribución de usuarios por rol
        $this->db->query("SELECT rol, COUNT(*) as count FROM roles GROUP BY rol");
        $data['distribucion_roles'] = $this->db->resultSet();

        // Estado general del inventario
        $this->db->query("SELECT estado, COUNT(*) as count FROM elementos GROUP BY estado");
        $data['distribucion_inventario'] = $this->db->resultSet();

        // --- Nuevas Estadísticas ---
        // Top 5 Elementos Más Solicitados
        $this->db->query("SELECT e.nombreele, COUNT(p.IDelementos) as total_solicitudes
                           FROM prestamos p
                           JOIN elementos e ON p.IDelementos = e.IDele
                           GROUP BY p.IDelementos
                           ORDER BY total_solicitudes DESC
                           LIMIT 5");
        $data['top_elementos'] = $this->db->resultSet();

        // Tiempo Promedio de Aprobación (Marcador de posición)
        // No se puede calcular sin timestamps de creación y aprobación en la BD.
        $data['tiempo_aprobacion_promedio'] = "N/A";

        return $data;
    }
}