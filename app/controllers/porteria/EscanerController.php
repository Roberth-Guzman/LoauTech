<?php

class EscanerController extends Controller
{
    public function __construct()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // Asegurarse de que solo el personal de portería pueda acceder
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'porteria') {
            header('Location: ' . BASE_URL . '/login');
            exit();
        }
    }

    // Método principal para mostrar la página de escaneo
    public function index()
    {
        $data = [
            'titulo' => 'Escanear Carnet - LOAUTECH',
            'nombre_usuario' => $_SESSION['user_name'] ?? 'Usuario'
        ];

        // Cargar la nueva vista que crearemos a continuación
        $this->view('panel-porteria/escanear-carnet', $data);
    }
}