<?php
class ElementoController extends Controller
{
    // Carga la vista del inventario general
    public function index()
    {
        // Cargar el modelo de elemento
        $elementoModel = $this->model('Elemento');

        // Obtener los datos de los elementos disponibles
        $elementos = $elementoModel->obtenerElementosDisponibles();

        // Pasar los datos a la vista
        $this->view('panel-usuario/inventario', ['elementos' => $elementos, 'titulo' => 'Inventario General']);
    }
}