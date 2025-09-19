<?php

class App {
    protected $controller = 'HomeController';
    protected $method = 'index';
    protected $params = [];

    public function __construct() {
        $url = $this->parseUrl();
        
        $baseControllerPath = dirname(dirname(__FILE__)) . '/controllers/';

        // --- Lógica de Enrutamiento Mejorada ---

        // 1. RUTAS ESPECIALES
        $rutaEspecial = isset($url[0]) ? strtolower($url[0]) : '';
        $segundoNivel = isset($url[1]) ? strtolower($url[1]) : '';
        
        // Ruta para términos y condiciones
        if ($rutaEspecial === 'terminos') {
            $this->controller = 'TerminosController';
            $controllerFile = $baseControllerPath . $this->controller . '.php';
            
            if (file_exists($controllerFile)) {
                require_once $controllerFile;
                if (class_exists($this->controller)) {
                    $this->controller = new $this->controller;
                    
                    // Si hay un segundo nivel (ej: /terminos/tecnicos)
                    if (!empty($segundoNivel)) {
                        if (method_exists($this->controller, $segundoNivel)) {
                            $this->method = $segundoNivel;
                            unset($url[1]);
                        }
                    }
                } else {
                    die('Error: La clase ' . $this->controller . ' no existe en ' . $controllerFile);
                }
            } else {
                die('Error: No se pudo encontrar el controlador ' . $controllerFile);
            }
            
            unset($url[0]);
            // Llamar al controlador y método
            call_user_func_array([$this->controller, $this->method], $this->params);
            return;
        }
        
        // 2. BUSCAR CONTROLADOR

        if (isset($url[0]) && is_dir($baseControllerPath . $url[0])) {
            $subDir = $url[0];
            $controllerFound = false;

            // Prioridad 1: Buscar un controlador específico en el subdirectorio
            if (isset($url[1])) {
                $controllerName = ucwords($url[1]) . 'Controller';
                $controllerFile = $baseControllerPath . $subDir . '/' . $controllerName . '.php';
                if (file_exists($controllerFile)) {
                    $this->controller = $controllerName;
                    require_once $controllerFile;
                    unset($url[0]);
                    unset($url[1]);
                    $controllerFound = true;
                }
            }

            // Prioridad 2: Si no se encontró, buscar el controlador principal del subdirectorio (ej: /porteria/peticiones -> PorteriaController)
            if (!$controllerFound) {
                $controllerName = ucwords($subDir) . 'Controller';
                $controllerFile = $baseControllerPath . $subDir . '/' . $controllerName . '.php';
                if (file_exists($controllerFile)) {
                    $this->controller = $controllerName;
                    require_once $controllerFile;
                    unset($url[0]);
                }
            }
        } 
        // ¿La URL apunta a un controlador en el directorio raíz? (ej: /usuario/...)
        elseif (isset($url[0]) && file_exists($baseControllerPath . ucwords($url[0]) . 'Controller.php')) {
            $this->controller = ucwords($url[0]) . 'Controller';
            require_once $baseControllerPath . $this->controller . '.php';
            unset($url[0]);
        }
        
        // Si no, usar el controlador por defecto (HomeController)
        if (!class_exists($this->controller)) {
            require_once $baseControllerPath . 'HomeController.php';
            $this->controller = 'HomeController';
        }

        // 2. CREAR INSTANCIA DEL CONTROLADOR
        $this->controller = new $this->controller;

        // Re-indexar el array de la URL para que el método esté en el índice 0
        $url = array_values($url);

        // 3. BUSCAR MÉTODO (CON LÓGICA PARA RESET PASSWORD)
        if (isset($url[0])) {
            // Caso especial para /login/resetPassword/{token}
            if (strtolower($url[0]) === 'resetpassword' && isset($url[1])) {
                $this->method = 'resetPassword';
                $this->params = [$url[1]]; // El token es el parámetro
                unset($url[0], $url[1]);
            } 
            // Caso general
            elseif (method_exists($this->controller, $url[0])) {
                $this->method = $url[0];
                unset($url[0]);
            }
        }

        // 4. OBTENER PARÁMETROS (si no fueron establecidos por el caso especial)
        if (empty($this->params)) {
            $this->params = $url ? array_values($url) : [];
        }

        // 5. LLAMAR MÉTODO
        call_user_func_array([$this->controller, $this->method], $this->params);
    }

    public function parseUrl() {
        if (isset($_GET['url'])) {
            return explode('/', filter_var(rtrim($_GET['url'], '/'), FILTER_SANITIZE_URL));
        }
        return [];
    }
}