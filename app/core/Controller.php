<?php
class Controller {
    protected $model;

    public function __construct() {
        // Constructor vacío para permitir la herencia
    }

    /**
     * Cargar una vista
     */
    protected function view($view, $data = []) {
        extract($data);
        $rutaVista = __DIR__ . '/../views/' . $view . '.php';
    
        if (file_exists($rutaVista)) {
            require_once $rutaVista;
        } else {
            die("Error 404: La vista {$view} no existe en {$rutaVista}");
        }
    }
    protected function model($model) {
        $modelFile = __DIR__ . '/../models/' . $model . '.php';

        if (file_exists($modelFile)) {
            require_once $modelFile;
            if (class_exists($model)) {
                return new $model();
            } else {
                die("Error 500: La clase de modelo <strong>$model</strong> no está definida dentro de $modelFile");
            }
        } else {
            die("Error 404: El archivo de modelo <strong>$model.php</strong> no existe en $modelFile");
        }
    }
    public function redirect($location) {
        header('Location: ' . BASE_URL . '/' . $location);
        exit();
    }

}
