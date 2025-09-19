<?php
class Database {
    private $host = DB_HOST;
    private $user = DB_USER;
    private $pass = DB_PASS;
    private $dbname = DB_NAME;

    private $dbh; // Database Handler
    private $stmt; // Statement
    private $error;

    public function __construct() {
        $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->dbname;
        $options = [
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
        ];

        try {
            $this->dbh = new PDO($dsn, $this->user, $this->pass, $options);
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
            echo $this->error;
        }
    }

    // Prepara la consulta
    public function query($sql) {
        $this->stmt = $this->dbh->prepare($sql);
    }

    // Vincula los valores
    public function bind($param, $value, $type = null) {
        if (is_null($type)) {
            switch (true) {
                case is_int($value):
                    $type = PDO::PARAM_INT;
                    break;
                case is_bool($value):
                    $type = PDO::PARAM_BOOL;
                    break;
                case is_null($value):
                    $type = PDO::PARAM_NULL;
                    break;
                default:
                    $type = PDO::PARAM_STR;
            }
        }
        $this->stmt->bindValue($param, $value, $type);
    }

    // Ejecuta la consulta preparada
    public function execute() {
        return $this->stmt->execute();
    }

    // Obtener un conjunto de resultados como un array de objetos
    public function resultSet() {
        $this->execute();
        return $this->stmt->fetchAll();
    }

    // Obtener un único registro como un objeto
    public function single() {
        $this->execute();
        return $this->stmt->fetch();
    }

    // Obtener el número de filas
    public function rowCount() {
        return $this->stmt->rowCount();
    }

    // Iniciar una transacción
    public function beginTransaction() {
        return $this->dbh->beginTransaction();
    }

    // Confirmar una transacción
    public function commit() {
        return $this->dbh->commit();
    }

    // Revertir una transacción
    public function rollBack() {
        return $this->dbh->rollBack();
    }

    // Obtener información de error de la última operación
    public function errorInfo() {
        return $this->stmt->errorInfo();
    }

    // Obtener el último ID insertado
    public function lastInsertId() {
        return $this->dbh->lastInsertId();
    }

    // Verificar si hay una transacción activa
    public function inTransaction() {
        return $this->dbh->inTransaction();
    }
}