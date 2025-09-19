<?php
class User extends Model
{
    private $error = '';

    public function __construct()
    {
        parent::__construct();
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Obtiene el último mensaje de error
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * Actualiza los datos del perfil del usuario
     */
    /**
     * Actualiza la ruta del avatar del usuario en la base de datos
     */
    public function actualizarAvatar($userId, $avatarPath)
    {
        try {
            $this->db->beginTransaction();


            $updateSql = "UPDATE fotos_perfil SET es_actual = 0 WHERE id_persona = :id";
            $this->db->query($updateSql);
            $this->db->bind(':id', $userId);
            $this->db->execute();

            // Insertar la nueva foto como actual 
            $insertSql = "INSERT INTO fotos_perfil (id_persona, ruta, es_actual) 
                         VALUES (:id, :ruta, 1)";
            $this->db->query($insertSql);
            $this->db->bind(':id', $userId);
            $this->db->bind(':ruta', $avatarPath);

            if (!$this->db->execute()) {
                throw new Exception('Error al insertar la nueva foto de perfil.');
            }

            $this->db->commit();
            return true;

        } catch (Exception $e) {
            $this->db->rollBack();
            $this->error = 'Error en actualizarAvatar: ' . $e->getMessage();
            error_log($this->error);
            error_log('Trace: ' . $e->getTraceAsString());
            return false;
        }
    }

    public function actualizarPerfil($data)
    {
        try {
            $this->db->beginTransaction();

            // 1. Actualizar el nombre en la tabla 'personas'
            $this->db->query('UPDATE personas SET nombrecompletoper = :nombre WHERE IDper = :id');
            $this->db->bind(':nombre', $data['nombre']);
            $this->db->bind(':id', $data['id']);
            if (!$this->db->execute()) {
                throw new Exception('Error al actualizar el nombre.');
            }

            // 2. Lógica para actualizar contactos (tu código original, ligeramente adaptado)
            $checkSql = "SELECT COUNT(*) as count FROM contactos WHERE IDperso = :id";
            $this->db->query($checkSql);
            $this->db->bind(':id', $data['id']);
            $result = $this->db->single();

            if ($result->count == 0) {
                // Insertar nuevo registro si no existe
                $insertSql = "INSERT INTO contactos (IDperso, correocont, numerocont) VALUES (:id, :email, :telefono)";
                $this->db->query($insertSql);
                $this->db->bind(':id', $data['id']);
                $this->db->bind(':email', $data['email']);
                $this->db->bind(':telefono', $data['telefono']);
                if (!$this->db->execute()) {
                    throw new Exception('Error al crear el registro de contacto');
                }
            } else {
                // Actualizar registro existente
                $updateSql = "UPDATE contactos SET correocont = :email, numerocont = :telefono WHERE IDperso = :id";
                $this->db->query($updateSql);
                $this->db->bind(':email', $data['email']);
                $this->db->bind(':telefono', $data['telefono']);
                $this->db->bind(':id', $data['id']);
                if (!$this->db->execute()) {
                    throw new Exception('Error al actualizar el correo y teléfono');
                }
            }

            $this->db->commit();
            return true;

        } catch (Exception $e) {
            $this->db->rollBack();
            $this->error = 'Error al actualizar el perfil: ' . $e->getMessage();
            return false;
        }
    }

    /**
     * Cambia la contraseña del usuario
     */
    public function cambiarPassword($datos)
    {
        // Verificar que se proporcionó la contraseña actual
        if (empty($datos['password_actual'])) {
            $this->error = 'Debes ingresar tu contraseña actual';
            return false;
        }

        // Verificar que las nuevas contraseñas coincidan
        if ($datos['nueva_password'] !== $datos['confirmar_password']) {
            $this->error = 'Las contraseñas no coinciden';
            return false;
        }

        // Obtener la contraseña actual del usuario
        $sql = "SELECT contracue FROM cuentas WHERE IDcue = :id LIMIT 1";
        $this->db->query($sql);
        $this->db->bind(':id', $datos['id']);
        $usuario = $this->db->single();

        if (!$usuario) {
            $this->error = 'Usuario no encontrado';
            return false;
        }

        // Verificar la contraseña actual
        if (!password_verify($datos['password_actual'], $usuario->contracue)) {
            $this->error = 'La contraseña actual es incorrecta';
            return false;
        }

        // Validar fortaleza de la nueva contraseña
        if (strlen($datos['nueva_password']) < 8) {
            $this->error = 'La nueva contraseña debe tener al menos 8 caracteres';
            return false;
        }

        // Actualizar la contraseña
        $nuevaPasswordHash = password_hash($datos['nueva_password'], PASSWORD_DEFAULT);

        $sql = "UPDATE cuentas SET contracue = :password WHERE IDcue = :id";
        $this->db->query($sql);
        $this->db->bind(':password', $nuevaPasswordHash);
        $this->db->bind(':id', $datos['id']);

        return $this->db->execute();
    }

    /**
     * Actualiza la ruta del avatar del usuario en la tabla contactos
     */

    /**
     * Obtiene un usuario activo por documento usando el wrapper de PDO.
     * (MODIFICADO para incluir el ID del rol)
     */
    public function obtenerUsuarioActivoPorDocumento($documento)
    {
        $sql = "SELECT c.*, 
                       p.IDper, p.nombrecompletoper, p.numerodoc,
                       cont.numerocont,
                       r.rol, r.IDrol as rol_id
                FROM cuentas c
                INNER JOIN personas p ON c.numerodoc = p.numerodoc
                LEFT JOIN roles r ON r.idper = p.IDper
                LEFT JOIN contactos cont ON p.IDper = cont.IDperso
                WHERE c.numerodoc = :documento AND c.estadocue = 'activo'";
        $this->db->query($sql);
        $this->db->bind(':documento', $documento);
        return $this->db->single();
    }

    /**
     * Login: valida credenciales y devuelve usuario.
     */
    public function login($documento, $password)
    {
        $sql = "SELECT * FROM cuentas WHERE numerodoc = :documento LIMIT 1";
        $this->db->query($sql);
        $this->db->bind(':documento', $documento);
        $usuario = $this->db->single();

        if ($usuario && password_verify($password, $usuario->contracue)) {
            return $usuario;
        }
        return null;
    }

    /**
     * Validar datos de entrada para registro.
     */
    public function validarDatosRegistro($datos)
    {
        $errores = [];

        if (empty($datos['tipoIdentidad']))
            $errores[] = "El tipo de identidad es obligatorio.";
        if (empty($datos['numeroIdentidad']))
            $errores[] = "El número de identidad es obligatorio.";
        if (empty($datos['nombre']))
            $errores[] = "El nombre es obligatorio.";
        if (empty($datos['correo']))
            $errores[] = "El correo es obligatorio.";
        if (empty($datos['telefono']))
            $errores[] = "El teléfono es obligatorio.";
        if (empty($datos['direccion']))
            $errores[] = "La dirección es obligatoria.";
        if (empty($datos['contrasena']))
            $errores[] = "La contraseña es obligatoria.";

        if (!empty($datos['contrasena']) && strlen($datos['contrasena']) < 8) {
            $errores[] = "La contraseña debe tener al menos 8 caracteres.";
        }
        if (!empty($datos['correo']) && !filter_var($datos['correo'], FILTER_VALIDATE_EMAIL)) {
            $errores[] = "El formato del correo no es válido.";
        }
        if (!empty($datos['numeroIdentidad']) && !preg_match('/^[0-9]{6,12}$/', $datos['numeroIdentidad'])) {
            $errores[] = "El número de identidad debe tener entre 6 y 12 dígitos.";
        }
        if (!empty($datos['telefono']) && !preg_match('/^[0-9]{10,15}$/', $datos['telefono'])) {
            $errores[] = "El teléfono debe tener entre 10 y 15 dígitos.";
        }

        if (!empty($datos['numeroIdentidad']) && $this->existeDocumento($datos['numeroIdentidad'])) {
            $errores[] = "Ya existe un usuario registrado con este número de documento.";
        }
        if (!empty($datos['correo']) && $this->existeCorreo($datos['correo'])) {
            $errores[] = "Ya existe un usuario registrado con este correo electrónico.";
        }

        return $errores;
    }

    /**
     * Verificar si existe un documento.
     */
    public function existeDocumento($documento)
    {
        $this->db->query("SELECT COUNT(*) as count FROM personas WHERE numerodoc = :documento");
        $this->db->bind(':documento', $documento);
        $row = $this->db->single();
        return $row->count > 0;
    }

    /**
     * Verificar si existe un correo.
     */
    public function existeCorreo($correo)
    {
        $this->db->query("SELECT COUNT(*) as count FROM contactos WHERE correocont = :correo");
        $this->db->bind(':correo', $correo);
        $row = $this->db->single();
        return $row->count > 0;
    }

    /**
     * Registrar usuario con transacciones.
     */
    public function registrarUsuario($data)
    {
        try {
            $this->db->beginTransaction();

            $passwordHash = password_hash($data['contrasena'], PASSWORD_DEFAULT);

            // 1. Insertar en personas
            $this->db->query("INSERT INTO personas (nombrecompletoper, tipodocumento, numerodoc) VALUES (:nombre, :tipoIdentidad, :numeroIdentidad)");
            $this->db->bind(':nombre', $data['nombre']);
            $this->db->bind(':tipoIdentidad', $data['tipoIdentidad']);
            $this->db->bind(':numeroIdentidad', $data['numeroIdentidad']);
            $this->db->execute();
            $idPersona = $this->db->lastInsertId();

            // 2. Asignar rol
            $roles = [1 => 'usuario', 2 => 'porteria', 3 => 'admin', 4 => 'cuentadante', 5 => 'almacenes'];
            $rolNombre = $roles[$data['rol']] ?? 'usuario';
            $this->db->query("INSERT INTO roles (rol, estadorol, idper) VALUES (:rol, 'activo', :idPersona)");
            $this->db->bind(':rol', $rolNombre);
            $this->db->bind(':idPersona', $idPersona);
            $this->db->execute();

            // 3. Crear cuenta de usuario
            $this->db->query("INSERT INTO cuentas (numerodoc, contracue, estadocue) VALUES (:numeroIdentidad, :passwordHash, 'activo')");
            $this->db->bind(':numeroIdentidad', $data['numeroIdentidad']);
            $this->db->bind(':passwordHash', $passwordHash);
            $this->db->execute();

            // 4. Insertar información de contacto
            $this->db->query("INSERT INTO contactos (numerocont, direccioncont, correocont, estadocont, IDperso) VALUES (:telefono, :direccion, :correo, 'activo', :idPersona)");
            $this->db->bind(':telefono', $data['telefono']);
            $this->db->bind(':direccion', $data['direccion']);
            $this->db->bind(':correo', $data['correo']);
            $this->db->bind(':idPersona', $idPersona);
            $this->db->execute();

            $this->db->commit();

            return ['success' => true, 'idPersona' => $idPersona, 'nombre' => $data['nombre'], 'documento' => $data['numeroIdentidad'], 'rol' => $rolNombre];
        } catch (Exception $e) {
            $this->db->rollBack();
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Obtener usuario completo por ID.
     */
    public function obtenerUsuarioPorId($id)
    {
        $this->db->query("SELECT p.*, c.*, cont.*, r.rol
                FROM personas p
                LEFT JOIN cuentas c ON p.numerodoc = c.numerodoc
                LEFT JOIN contactos cont ON p.IDper = cont.IDperso
                LEFT JOIN roles r ON p.IDper = r.idper
                WHERE p.IDper = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    /**
     * Actualizar contraseña.
     */
    public function actualizarContrasena($documento, $nuevaContrasena)
    {
        $passwordHash = password_hash($nuevaContrasena, PASSWORD_DEFAULT);
        $this->db->query("UPDATE cuentas SET contracue = :passwordHash WHERE numerodoc = :documento");
        $this->db->bind(':passwordHash', $passwordHash);
        $this->db->bind(':documento', $documento);
        return $this->db->execute();
    }

    /**
     * Cambiar estado de cuenta.
     */
    public function cambiarEstadoCuenta($documento, $estado)
    {
        $this->db->query("UPDATE cuentas SET estadocue = :estado WHERE numerodoc = :documento");
        $this->db->bind(':estado', $estado);
        $this->db->bind(':documento', $documento);
        return $this->db->execute();
    }

    /**
     * Obtener perfil completo del usuario, incluyendo foto.
     */
    public function obtenerPerfilUsuario($idUsuario)
    {
        $sql = "SELECT 
                    p.nombrecompletoper, 
                    p.tipodocumento, 
                    p.numerodoc,
                    c.correocont, 
                    c.numerocont,
                    r.rol,
                    fp.ruta AS foto_ruta
                FROM personas p
                LEFT JOIN contactos c ON p.IDper = c.IDperso
                LEFT JOIN roles r ON p.IDper = r.idper
                LEFT JOIN fotos_perfil fp ON p.IDper = fp.id_persona AND fp.es_actual = 1
                WHERE p.IDper = :idUsuario";

        $this->db->query($sql);
        $this->db->bind(":idUsuario", $idUsuario);
        $result = $this->db->single();

        // Debug: Verificar los datos devueltos
        error_log('Datos del perfil: ' . print_r($result, true));

        return $result;
    }

    public function obtenerPerfilCompletoPorId($id)
    {
        $sql = "SELECT 
                    p.nombrecompletoper, 
                    p.tipodocumento, 
                    p.numerodoc,
                    c.correocont, 
                    c.numerocont,
                    c.direccioncont,
                    r.rol,
                    fp.ruta AS foto_ruta
                FROM personas p
                LEFT JOIN contactos c ON p.IDper = c.IDperso
                LEFT JOIN roles r ON p.IDper = r.idper
                LEFT JOIN fotos_perfil fp ON p.IDper = fp.id_persona AND fp.es_actual = 1
                WHERE p.IDper = :id";

        $this->db->query($sql);
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    /**
     * Obtiene los IDs de usuarios con rol cuentadante activos
     */
    public function obtenerIdsCuentadantesActivos()
    {
        $this->db->query("SELECT p.IDper 
                          FROM personas p 
                          INNER JOIN roles r ON r.idper = p.IDper 
                          WHERE r.rol = 'cuentadante' AND r.estadorol = 'activo'");
        $result = $this->db->resultSet();
        return array_map(function ($row) {
            return (int) $row->IDper; }, $result ?? []);
    }
    /**
     * Obtiene todos los usuarios con el rol de cuentadante
     */
    public function obtenerCuentadantes()
    {
        $sql = "SELECT p.IDper, p.nombrecompletoper
                FROM personas p
                INNER JOIN roles r ON p.IDper = r.idper
                WHERE r.rol = 'cuentadante' AND r.estadorol = 'activo'
                ORDER BY p.nombrecompletoper ASC";
        $this->db->query($sql);
        return $this->db->resultSet();
    }
    /**
     * Obtiene un usuario por su email.
     */
    public function obtenerUsuarioPorEmail($email)
    {
        $sql = "SELECT p.IDper, p.nombrecompletoper, c.IDcue, cont.correocont
                FROM personas p
                JOIN contactos cont ON p.IDper = cont.IDperso
                JOIN cuentas c ON p.numerodoc = c.numerodoc
                WHERE cont.correocont = :email AND c.estadocue = 'activo'";
        $this->db->query($sql);
        $this->db->bind(':email', $email);
        return $this->db->single();
    }

    /**
     * Guarda el token de restablecimiento de contraseña.
     */
    public function guardarToken($idCuenta, $token, $expiracion)
    {
        $sql = "UPDATE cuentas SET reset_token = :token, token_expira = :expiracion WHERE IDcue = :id";
        $this->db->query($sql);
        $this->db->bind(':token', $token);
        $this->db->bind(':expiracion', $expiracion);
        $this->db->bind(':id', $idCuenta);
        return $this->db->execute();
    }

    /**
     * Obtiene un usuario por su token de restablecimiento.
     */
    public function obtenerUsuarioPorToken($token)
    {
        $sql = "SELECT c.IDcue, c.numerodoc, p.nombrecompletoper, cont.correocont
                FROM cuentas c
                JOIN personas p ON c.numerodoc = p.numerodoc
                JOIN contactos cont ON p.IDper = cont.IDperso
                WHERE c.reset_token = :token AND c.token_expira > NOW()";
        $this->db->query($sql);
        $this->db->bind(':token', $token);
        return $this->db->single();
    }

    /**
     * Actualiza la contraseña por ID de cuenta.
     */
    public function actualizarContrasenaPorId($idCuenta, $nuevaContrasena)
    {
        $passwordHash = password_hash($nuevaContrasena, PASSWORD_DEFAULT);
        $this->db->query("UPDATE cuentas SET contracue = :passwordHash WHERE IDcue = :id");
        $this->db->bind(':passwordHash', $passwordHash);
        $this->db->bind(':id', $idCuenta);
        return $this->db->execute();
    }

    /**
     * Limpia el token de restablecimiento de contraseña.
     */
    public function limpiarToken($idCuenta)
    {
        $sql = "UPDATE cuentas SET reset_token = NULL, token_expira = NULL WHERE IDcue = :id";
        $this->db->query($sql);
        $this->db->bind(':id', $idCuenta);
        return $this->db->execute();
    }

    /**
     * Busca usuarios por un término de búsqueda (nombre, email o documento).
     */
    public function buscarUsuariosPorTermino($termino)
    {
        $sql = "SELECT p.IDper, p.nombrecompletoper, p.numerodoc, cont.correocont, c.IDcue
                FROM personas p
                LEFT JOIN contactos cont ON p.IDper = cont.IDperso
                LEFT JOIN cuentas c ON p.numerodoc = c.numerodoc
                WHERE (p.nombrecompletoper LIKE :termino
                   OR p.numerodoc LIKE :termino
                   OR cont.correocont LIKE :termino)
                   AND c.estadocue = 'activo'";

        $this->db->query($sql);
        $this->db->bind(':termino', '%' . $termino . '%');
        return $this->db->resultSet();
    }
    public function updateContactInfo($userId, $email, $telefono) {
    // La $userId que recibimos es IDcue de la tabla 'cuentas'
    // Necesitamos actualizar la tabla 'contactos' usando las relaciones
    $this->db->query('
        UPDATE contactos c
        JOIN personas p ON c.IDperso = p.IDper
        JOIN cuentas cu ON p.numerodoc = cu.numerodoc
        SET c.correocont = :email, c.numerocont = :telefono
        WHERE cu.IDcue = :user_id
    ');

    // Vincular valores
    $this->db->bind(':email', $email);
    $this->db->bind(':telefono', $telefono);
    $this->db->bind(':user_id', $userId);

    // Ejecutar y devolver el resultado
    if ($this->db->execute()) {
        return true;
    } else {
        return false;
    }
}
  public function findUserById($id) {
    $sql = "SELECT 
                p.nombrecompletoper, 
                p.numerodoc, 
                c.correocont, 
                c.numerocont,
                r.rol,
                fp.ruta as avatar
            FROM personas p
            LEFT JOIN contactos c ON p.IDper = c.IDperso
            LEFT JOIN roles r ON p.IDper = r.idper
            LEFT JOIN fotos_perfil fp ON p.IDper = fp.id_persona AND fp.es_actual = 1
            WHERE p.IDper = :id";

    $this->db->query($sql);
    $this->db->bind(':id', $id);
    return $this->db->single();
}

}