<?php
class TerminosController extends Controller {
    public function index() {
        $this->view('terminos/index');
    }
    
    public function tecnicos() {
        $this->view('terminos/tecnicos');
    }
}
