<?php
class RegistroModel {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function verificarCorreoExistente($correo) {
        try {
            $this->db->query("SELECT IDcont FROM contactos WHERE correocont = :correo");
            $this->db->bind(':correo', $correo);
            $this->db->execute();
            return $this->db->rowCount() > 0;
        } catch (PDOException $e) {
            error_log('Error en verificarCorreoExistente: ' . $e->getMessage());
            return false;
        }
    }

    public function verificarIdentidadExistente($numero_identidad) {
        try {
            $this->db->query("SELECT IDper FROM personas WHERE numerodoc = :numero_identidad");
            $this->db->bind(':numero_identidad', $numero_identidad);
            $this->db->execute();
            return $this->db->rowCount() > 0;
        } catch (PDOException $e) {
            error_log('Error en verificarIdentidadExistente: ' . $e->getMessage());
            return false;
        }
    }

    public function registrarUsuario($datos) {
        error_log('Iniciando registro de usuario con datos: ' . print_r($datos, true));
        
        // Verificar conexión a la base de datos
        if (!$this->db) {
            error_log('Error: No hay conexión a la base de datos');
            return false;
        }
        
        try {
            error_log('Iniciando transacción...');
            $transactionStarted = $this->db->beginTransaction();
            
            if (!$transactionStarted) {
                throw new Exception('No se pudo iniciar la transacción');
            }
            
            error_log('Transacción iniciada correctamente');
           $this->db->query("INSERT INTO personas (nombrecompletoper, tipodocumento, numerodoc) 
                             VALUES (:nombre, :tipo_doc, :numero_doc)");
            $this->db->bind(':nombre', trim($datos['nombre']));
            $this->db->bind(':tipo_doc', strtoupper($datos['tipoIdentidad'])); // Asegurar que sea mayúscula
            $this->db->bind(':numero_doc', $datos['numeroIdentidad']);
            
            if (!$this->db->execute()) {
                $errorInfo = $this->db->errorInfo();
                throw new Exception('Error al insertar en la tabla personas: ' . ($errorInfo[2] ?? 'Error desconocido'));
            }
            
            $id_persona = $this->db->lastInsertId();
            error_log('Persona insertada correctamente. ID: ' . $id_persona);

            // 2. Insertar en la tabla contactos
            $this->db->query("INSERT INTO contactos (numerocont, direccioncont, correocont, estadocont, IDperso) 
                             VALUES (:telefono, :direccion, :correo, 'activo', :id_persona)");
            $this->db->bind(':telefono', $datos['telefono']);
            $this->db->bind(':direccion', $datos['direccion'] ?? 'Sin dirección');
            $this->db->bind(':correo', $datos['correo']);
            $this->db->bind(':id_persona', $id_persona);
            
            if (!$this->db->execute()) {
                $errorInfo = $this->db->errorInfo();
                error_log('Error al insertar en contactos: ' . print_r($errorInfo, true));
                throw new Exception('Error al insertar en la tabla contactos: ' . ($errorInfo[2] ?? 'Error desconocido'));
            }
            error_log('Contacto insertado correctamente');

            // 3. Insertar en la tabla cuentas (contraseña hasheada)
            $password_hash = password_hash($datos['password'], PASSWORD_DEFAULT);
            $this->db->query("INSERT INTO cuentas (numerodoc, contracue, estadocue) 
                             VALUES (:numero_doc, :contracue, 'activo')");
            $this->db->bind(':numero_doc', $datos['numeroIdentidad']);
            $this->db->bind(':contracue', $password_hash);
            
            if (!$this->db->execute()) {
                $errorInfo = $this->db->errorInfo();
                error_log('Error al insertar en cuentas: ' . print_r($errorInfo, true));
                throw new Exception('Error al insertar en la tabla cuentas: ' . ($errorInfo[2] ?? 'Error desconocido'));
            }
            error_log('Cuenta creada correctamente');

            // 4. Asignar rol de usuario por defecto
            $this->db->query("INSERT INTO roles (rol, estadorol, idper) 
                             VALUES ('usuario', 'activo', :id_persona)");
            $this->db->bind(':id_persona', $id_persona);
            
            if (!$this->db->execute()) {
                $errorInfo = $this->db->errorInfo();
                error_log('Error al asignar rol: ' . print_r($errorInfo, true));
                throw new Exception('Error al asignar el rol de usuario: ' . ($errorInfo[2] ?? 'Error desconocido'));
            }
            error_log('Rol asignado correctamente');

            // Confirmar transacción
            error_log('Confirmando transacción...');
            $commitResult = $this->db->commit();
            
            if ($commitResult) {
                error_log('Transacción confirmada correctamente');
                return $id_persona;
            } else {
                throw new Exception('Error al confirmar la transacción');
            }
            
        } catch (Exception $e) {
            error_log('Error en RegistroModel: ' . $e->getMessage());
            if (isset($this->db) && $this->db->inTransaction()) {
                error_log('Haciendo rollback de la transacción...');
                $this->db->rollBack();
            }
            return false;
        }
    }
}