<?php
class HomeController extends Controller {
    public function index() {
        $data = [
            'titulo' => 'Página de Inicio',
            'mensaje' => 'Bienvenido al sistema'
        ];
        $this->view('home/index', $data);
    }
}