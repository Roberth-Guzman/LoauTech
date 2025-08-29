<?php
session_start();

class EquipoController extends Controller {
    public function index() {
        $this->view('equipo/index');
    }

    public function procesarContacto() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = $_POST['nombre'] ?? '';
            $email = $_POST['email'] ?? '';
            $mensaje = $_POST['mensaje'] ?? '';

            if (!empty($nombre) && !empty($email) && !empty($mensaje)) {
                $_SESSION['mensaje_contacto'] = [
                    'tipo' => 'exito',
                    'texto' => 'Gracias por contactarnos, te responderemos pronto.'
                ];
            } else {
                $_SESSION['mensaje_contacto'] = [
                    'tipo' => 'error',
                    'texto' => 'Por favor completa todos los campos obligatorios.'
                ];
            }
            header('Location: /mvc_dev/equipo');
            exit;
        }
    }
}
