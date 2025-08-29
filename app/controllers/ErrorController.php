<?php
class ErrorController extends Controller {
    public function index() {
        http_response_code(404);
        $this->view('error/404', [
            'titulo' => 'Página no encontrada'
        ]);
    }
}