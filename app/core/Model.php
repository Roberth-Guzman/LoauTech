<?php
/*
 * Modelo base
 * Todos los modelos heredarán de esta clase, asegurando que todos
 * usen la misma conexión a la base de datos (PDO) a través de la clase Database.
 */
class Model
{
    protected $db;

    public function __construct()
    {
        // Instanciamos nuestra clase Database para obtener la conexión PDO.
        $this->db = new Database();
    }
}