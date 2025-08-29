<?php
require_once __DIR__ . '/Controller.php';


class Router {
    public function run() {
        $url = isset($_GET['url']) ? rtrim($_GET['url'], '/') : 'home';
        $url = filter_var($url, FILTER_SANITIZE_URL);
        $url = explode('/', $url);

        $controllerName = ucfirst($url[0]) . 'Controller';
        $methodName = isset($url[1]) ? $url[1] : 'index';
        $params = array_slice($url, 2);

        $controllerPath = "app/controllers/$controllerName.php";

        if (file_exists($controllerPath)) {
            require_once $controllerPath;
            $controller = new $controllerName;

            if (method_exists($controller, $methodName)) {
                call_user_func_array([$controller, $methodName], $params);
            } else {
                echo "Método '$methodName' no encontrado";
            }
        } else {
            echo "Controlador '$controllerName' no encontrado";
        }
    }
}
