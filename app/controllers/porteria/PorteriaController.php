<?php

class PorteriaController extends Controller
{
    private $peticionModel;
    private $userModel;
    private $ingresoModel;

    public function __construct()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // Condición de seguridad final y correcta
        if (!isset($_SESSION['user_id']) || trim($_SESSION['user_role'] ?? '') !== 'porteria') {
            header('Location: ' . BASE_URL . '/login');
            exit();
        }

        // Cargar los modelos necesarios
        $this->peticionModel = $this->model('Peticion');
        $this->userModel = $this->model('User');
        $this->ingresoModel = $this->model('Ingreso');
    }

    public function index()
    {
        $data = [
            'titulo' => 'Panel de Portería - LOAUTECH',
            'nombre_usuario' => $_SESSION['user_name'] ?? 'Usuario'
        ];

        $this->view('panel-porteria/panel-principal', $data);
    }

    public function perfil()
    {
        // Prepara los datos del usuario para la vista del perfil
        $user_id = $_SESSION['user_id'];
        $usuario = $this->userModel->findUserById($user_id);

        $data = [
            'titulo' => 'Perfil de Usuario',
            'usuario' => $usuario
        ];

        // Carga la vista del perfil con los datos del usuario
        $this->view('panel-porteria/perfil', $data);
    }
    public function panelPrincipal($pagina_actual = 1)
    {
        if (!Session::isLoggedIn() || Session::getUser('rol') !== 'porteria') {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        $registros_por_pagina = 10;
        $offset = ($pagina_actual - 1) * $registros_por_pagina;

        // Obtener las solicitudes pendientes para portería
        $solicitudes_pendientes = $this->peticionModel->obtenerSolicitudesPendientesPorteria();

        $data = [
            'page_title' => 'Panel de Portería',
            'solicitudes' => $solicitudes_pendientes,
            // Puedes añadir más datos como paginación si es necesario
        ];

        $this->view('panel-porteria/panel-principal', $data);
    }
    public function editarPerfil()
    {
        // Inicia la sesión para obtener el user_id
        Session::init();
        $this->userModel = $this->model('User');
        $userId = Session::get('user_id');

        // Si no hay un usuario logueado, redirige al login
        if (!$userId) {
            $this->redirect('login');
            return;
        }

        // Si se envía el formulario (POST)
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Sanitizar los datos del formulario
            $data = [
                'nombre' => trim($_POST['nombre']),
                'email' => trim($_POST['email']),
                'telefono' => trim($_POST['telefono']),
                'id' => $userId,
                'nombre_err' => '',
                'email_err' => '',
                'telefono_err' => ''
            ];

            // Aquí podrías agregar validaciones (ej. si el email es válido, etc.)
            // Por ahora, lo mantenemos simple

            // Si no hay errores (en este caso, siempre procede)
            if (empty($data['nombre_err']) && empty($data['email_err']) && empty($data['telefono_err'])) {
                // Llama al método del modelo para actualizar el perfil
                if ($this->userModel->actualizarPerfil($data)) {
                    // Si la actualización es exitosa, redirige al perfil
                    Session::set('profile_update_success', 'Tu perfil ha sido actualizado correctamente.');
                    $this->redirect('porteria/perfil');
                } else {
                    // Si falla, puedes mostrar un error
                    die('Algo salió mal al actualizar el perfil.');
                }
            } else {
                // Si hubiera errores de validación, carga la vista con los errores
                $data['usuario'] = $this->userModel->findUserById($userId);
                $this->view('panel-porteria/editar-perfil', $data);
            }

        } else {
            // Si no es POST, simplemente muestra el formulario con los datos del usuario
            $usuario = $this->userModel->findUserById($userId);
            $data = [
                'usuario' => $usuario
            ];
            $this->view('panel-porteria/editar-perfil', $data);
        }
    }
    public function registros()
    {
        date_default_timezone_set('America/Bogota');
        $fechaHoy = date('Y-m-d');
        $hora_actual = (int) date('H');
        $fuera_de_horario = ($hora_actual < 6 || $hora_actual >= 23);

        $ingresoModel = $this->model('Ingreso');

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
            if ($fuera_de_horario) {
                Session::set('error', 'Los registros solo se pueden realizar entre las 6:00 AM y las 10:59 PM.');
                header('Location: ' . BASE_URL . '/porteria/registros');
                exit;
            }

            $action = $_POST['action'];

            if ($action === 'registrar_ingreso') {
                $documento = trim($_POST['documento_ingreso'] ?? '');
                if (empty($documento)) {
                    Session::set('error', 'El número de documento es obligatorio para registrar el ingreso.');
                } else {
                    $resultado = $ingresoModel->registrarIngresoPorDocumento($documento);
                    if ($resultado['exito']) {
                        Session::set('mensaje', $resultado['mensaje']);
                    } else {
                        Session::set('error', $resultado['mensaje']);
                    }
                }
            } elseif ($action === 'registrar_salida') {
                $documento = trim($_POST['documento_salida'] ?? '');
                if (empty($documento)) {
                    Session::set('error', 'El número de documento es obligatorio para registrar la salida.');
                } else {
                    $resultado = $ingresoModel->registrarSalidaPorDocumento($documento);
                    if ($resultado['exito']) {
                        Session::set('mensaje', $resultado['mensaje']);
                    } else {
                        Session::set('error', $resultado['mensaje']);
                    }
                }
            }
            header('Location: ' . BASE_URL . '/porteria/registros');
            exit;
        }

        $registrosHoy = $ingresoModel->obtenerRegistrosDeHoy();

        $data = [
            'titulo' => 'Registros de Portería',
            'registros' => $registrosHoy,
            'fuera_de_horario' => $fuera_de_horario,
            'mensaje' => Session::get('mensaje'),
            'error' => Session::get('error')
        ];

        Session::remove('mensaje');
        Session::remove('error');

        $this->view('panel-porteria/registros', $data);
    }


    private function validarFecha($fecha, $formato = 'Y-m-d')
    {
        $d = DateTime::createFromFormat($formato, $fecha);
        return $d && $d->format($formato) === $fecha;
    }

    public function informeHorario()
    {
        $tiposElementos = $this->ingresoModel->obtenerTiposElementos();
        $hoy = date('Y-m-d');

        // Determinar la fecha a mostrar, priorizando el date picker, luego la navegación
        $fechaAMostrar = $hoy; // Por defecto es hoy

        $fechaInput = $_GET['fecha_especifica'] ?? null;
        $fechaNavegacion = $_GET['fecha'] ?? null;

        if ($fechaInput && $this->validarFecha($fechaInput)) {
            $fechaAMostrar = $fechaInput;
        } elseif ($fechaNavegacion && $this->validarFecha($fechaNavegacion)) {
            $fechaAMostrar = $fechaNavegacion;
        }

        // No permitir navegar a fechas futuras
        if ($fechaAMostrar > $hoy) {
            $fechaAMostrar = $hoy;
        }

        $tipoElementoSeleccionado = $_GET['tipo_elemento'] ?? 'todos';

        // Lógica de paginación por día
        $fechaAnterior = date('Y-m-d', strtotime($fechaAMostrar . ' -1 day'));
        $fechaSiguiente = ($fechaAMostrar < $hoy) ? date('Y-m-d', strtotime($fechaAMostrar . ' +1 day')) : null;

        // Construir URLs de navegación
        $urlBase = BASE_URL . '/porteria/informeHorario';
        $queryStringAnterior = http_build_query(['fecha' => $fechaAnterior, 'tipo_elemento' => $tipoElementoSeleccionado]);
        $fechaAnteriorUrl = $urlBase . '?' . $queryStringAnterior;

        $fechaSiguienteUrl = null;
        if ($fechaSiguiente) {
            $queryStringSiguiente = http_build_query(['fecha' => $fechaSiguiente, 'tipo_elemento' => $tipoElementoSeleccionado]);
            $fechaSiguienteUrl = $urlBase . '?' . $queryStringSiguiente;
        }

        // Construir filtros para la consulta
        $filtros = [
            'fecha_inicio' => $fechaAMostrar,
            'tipo_elemento' => $tipoElementoSeleccionado
        ];

        // Obtener registros y estadísticas
        $resultado = $this->ingresoModel->obtenerRegistrosParaInformeHorario($filtros);
        $registros = $resultado['registros'];
        $totalRegistros = $resultado['total'];

        $registrosConSalida = 0;
        foreach ($registros as $registro) {
            if ($registro->estado === 'Registrado') { // Asumiendo que 'Registrado' significa que ya salió
                $registrosConSalida++;
            }
        }
        $registrosPendientes = $totalRegistros - $registrosConSalida;

        $data = [
            'titulo' => 'Informe de Registros por Día',
            'nombre_usuario' => $_SESSION['user_name'] ?? 'Usuario',
            'registros' => $registros,
            'total_registros' => $totalRegistros,
            'stats' => [
                'con_salida' => $registrosConSalida,
                'pendientes' => $registrosPendientes,
            ],
            'fecha_mostrada' => $fechaAMostrar,
            'fecha_anterior_url' => $fechaAnteriorUrl,
            'fecha_siguiente_url' => $fechaSiguienteUrl,
            'hoy' => $hoy,
            'tipos_elementos' => $tiposElementos,
            'tipo_elemento_seleccionado' => $tipoElementoSeleccionado,
        ];

        // Añadir mensaje si no hay registros para la fecha seleccionada
        if (empty($registros)) {
            $data['mensaje'] = 'No se encontraron registros para el día ' . (new DateTime($fechaAMostrar))->format('d/m/Y') . '.';
        }

        $this->view('panel-porteria/informe-horario', $data);
    }

    public function exportarInforme($formato = 'csv')
    {
        // Validar formato
        if (!in_array($formato, ['csv', 'pdf'])) {
            exit('Formato no válido.');
        }

        // Obtener filtros de la URL
        $fecha = $_GET['fecha'] ?? date('Y-m-d');
        $tipoElemento = $_GET['tipo_elemento'] ?? 'todos';

        // Construir filtros para la consulta
        $filtros = [
            'fecha_inicio' => $fecha,
            'tipo_elemento' => $tipoElemento
        ];

        // Obtener los registros usando el método existente
        $resultado = $this->ingresoModel->obtenerRegistrosParaInformeHorario($filtros);
        $registros = $resultado['registros'];

        if ($formato === 'csv') {
            $nombreArchivo = 'informe-horario-' . $fecha . '.csv';

            // Cabeceras para forzar la descarga del archivo
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="' . $nombreArchivo . '"');

            $salida = fopen('php://output', 'w');

            // Añadir BOM para compatibilidad con Excel y UTF-8
            fprintf($salida, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Escribir la fila de encabezados del CSV, usando punto y coma como delimitador
            fputcsv($salida, ['Nombre Elemento', 'Código', 'Tipo', 'Descripción', 'Observación', 'Hora Registro', 'Estado', 'Registrado por', 'Documento'], ';');

            // Escribir los datos de cada registro
            foreach ($registros as $registro) {
                fputcsv($salida, [
                    $registro->nombre,
                    $registro->codigo,
                    $registro->tipo,
                    $registro->marca,
                    $registro->modelo,
                    $registro->hora_registro,
                    $registro->estado,
                    $registro->nombrecompletoper,
                    $registro->numerodoc
                ], ';');
            }

            fclose($salida);
            exit;
        }

        if ($formato === 'pdf') {
            $data = [
                'registros' => $registros,
                'fecha_mostrada' => (new DateTime($fecha))->format('d/m/Y')
            ];
            // Cargar la vista de la plantilla del PDF, que se imprimirá sola
            $this->view('panel-porteria/reporte-pdf-template', $data);
            exit;
        }
    }
    public function buscarRegistrosPorDocumento()
    {
        $data = [
            'titulo' => 'Registros de Ingresos - LOAUTECH',
            'nombre_usuario' => $_SESSION['user_name'] ?? 'Usuario',
            'registros' => [],
            'documento_buscado' => '',
            'mensaje' => ''
        ];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (isset($_POST['registrar_entrada'])) {
                $documento = trim($_POST['documento_entrada']);
                if (!empty($documento)) {
                    $registros = $this->ingresoModel->obtenerRegistrosPorDocumento($documento);
                    $data['registros'] = $registros;
                    $data['documento_buscado'] = $documento;
                    if (empty($registros)) {
                        $data['mensaje'] = "No se encontraron registros de entrada activos para el documento '{$documento}'.";
                    }
                } else {
                    $data['mensaje'] = "Por favor, ingrese un número de documento para buscar.";
                }
            } elseif (isset($_POST['registrar_salida'])) {
                $documento = trim($_POST['documento_salida']);
                if (!empty($documento)) {
                    if ($this->ingresoModel->registrarSalidaPorDocumento($documento)) {
                        $data['mensaje'] = "Salida registrada correctamente para el documento '{$documento}'.";
                    } else {
                        $data['mensaje'] = "No se pudo registrar la salida o no había elementos pendientes para el documento '{$documento}'.";
                    }
                } else {
                    $data['mensaje'] = "Por favor, ingrese un número de documento para registrar la salida.";
                }
            }
        }

        $this->view('panel-porteria/registros', $data);
    }

    public function peticiones($page = 1)
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/login');
            exit();
        }

        $paginaActual = filter_var($page, FILTER_VALIDATE_INT) ? (int) $page : 1;
        if ($paginaActual < 1) {
            $paginaActual = 1;
        }

        $peticionesPorPagina = 10;
        $offset = ($paginaActual - 1) * $peticionesPorPagina;

        // Obtenemos las peticiones con estado 'pendiente_porteria'
        $estado = 'pendiente_porteria';
        $totalPeticiones = $this->peticionModel->contarPeticionesPorEstado($estado);
        $totalPaginas = ceil($totalPeticiones / $peticionesPorPagina);

        $peticionesPendientes = $this->peticionModel->obtenerPeticionesPorEstadoPaginadas($estado, $peticionesPorPagina, $offset);

        $salidasHoy = $this->peticionModel->obtenerSalidasDelDia();

        $data = [
            'titulo' => 'Autorización de Salidas',
            'nombre_usuario' => $_SESSION['user_name'] ?? 'Usuario',
            'peticionesAprobadas' => $peticionesPendientes, // Reutilizamos la variable de la vista con los datos correctos
            'salidasHoy' => $salidasHoy,
            'totalPeticionesAprobadas' => $totalPeticiones, // Reutilizamos la variable de la vista
            'paginaActual' => $paginaActual,
            'totalPaginas' => $totalPaginas
        ];

        $this->view('panel-porteria/peticiones', $data);
    }
    public function autorizarSalida($peticion_id)
    {
        if (!isset($_SESSION['user_id'])) {
            redirect('login');
        }

        $usuario_porteria_id = $_SESSION['user_id'];
        $peticion_id = filter_var($peticion_id, FILTER_VALIDATE_INT);

        if (!$peticion_id) {
            redirect('porteria/peticiones');
        }

        if ($this->peticionModel->registrarSalidaPeticion($peticion_id, $usuario_porteria_id)) {
            Session::set('mensaje_exito', 'Salida autorizada correctamente.');
        } else {
            Session::set('mensaje_error', 'Error al autorizar la salida. La petición no estaba en el estado correcto o ya fue procesada.');
        }
        redirect('porteria/peticiones');
    }

    public function registrarSalida()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Validar y limpiar los datos del POST
            $id_prestamo = filter_input(INPUT_POST, 'id_prestamo', FILTER_VALIDATE_INT);
            $hora_salida = date('Y-m-d H:i:s'); // Usar la hora actual del servidor
            $observaciones_salida = trim(filter_input(INPUT_POST, 'observaciones_salida', FILTER_SANITIZE_STRING));

            if ($id_prestamo === false || $id_prestamo === null) {
                // Manejar error de ID de préstamo inválido
                die('ID de préstamo no válido.');
            }

            $data = [
                'id_prestamo' => $id_prestamo,
                'fecha_salida' => $hora_salida,
                'observaciones_salida' => $observaciones_salida
            ];

            if ($this->peticionModel->registrarSalidaElemento($data)) {
                // Redirigir a la nueva vista de comprobante
                header('Location: ' . BASE_URL . '/porteria/verComprobante/' . $id_prestamo);
                exit();
            } else {
                // Manejar error
                die('Algo salió mal al registrar la salida.');
            }
        }
    }

    public function verComprobante($id_prestamo)
    {
        $id_prestamo = filter_var($id_prestamo, FILTER_VALIDATE_INT);
        if ($id_prestamo === false || $id_prestamo === null) {
            header('Location: ' . BASE_URL . '/porteria/peticiones');
            exit();
        }

        $comprobante = $this->peticionModel->obtenerDetallesPrestamoPorId($id_prestamo);

        if (!$comprobante) {
            // Si no se encuentra el préstamo, redirigir
            header('Location: ' . BASE_URL . '/porteria/peticiones');
            exit();
        }

        $data = [
            'titulo' => 'Comprobante de Salida',
            'nombre_usuario' => $_SESSION['user_name'] ?? 'Usuario',
            'comprobante' => $comprobante
        ];

        $this->view('panel-porteria/comprobante-salida', $data);
    }

    public function devoluciones()
    {
        // Cargar el modelo de Peticion si no se ha hecho
        $this->peticionModel = $this->model('Peticion');

        // Obtener los préstamos que están actualmente 'en_prestamo'
        $prestamosActivos = $this->peticionModel->obtenerPrestamosActivos();

        $data = [
            'prestamos_activos' => $prestamosActivos
        ];

        // Cargar la nueva vista para las devoluciones
        $this->view('panel-porteria/devoluciones', $data);
    }

    public function registrarDevolucion()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Limpiar y preparar los datos del formulario
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            $data = [
                'id_prestamo' => trim($_POST['id_prestamo']),
                'observaciones_devolucion' => trim($_POST['observaciones_devolucion'])
            ];

            // Cargar el modelo
            $this->peticionModel = $this->model('Peticion');

            // Intentar registrar la devolución
            if ($this->peticionModel->registrarDevolucionElemento($data)) {
                // Redirigir a la página de devoluciones con un mensaje de éxito
                header('Location: /mvc_dev/porteria/devoluciones');
            } else {
                // Si algo sale mal
                die('Error al registrar la devolución.');
            }
        } else {
            // Si no es POST, redirigir
            header('Location: /mvc_dev/porteria/devoluciones');
        }
    }

    public function historialPrestamos()
    {
        $filtro_tiempo = $_GET['filtro_tiempo'] ?? 'diario';
        $busqueda_texto = $_GET['busqueda_texto'] ?? '';
        $filtro_estado = $_GET['filtro_estado'] ?? '';

        $registros = $this->peticionModel->obtenerRegistrosPrestamos($filtro_tiempo, $busqueda_texto, $filtro_estado);

        $data = [
            'titulo' => 'Historial de Préstamos - LOAUTECH',
            'nombre_usuario' => $_SESSION['user_name'] ?? 'Usuario',
            'registros' => $registros,
            'filtro_tiempo' => $filtro_tiempo,
            'busqueda_texto' => $busqueda_texto,
            'filtro_estado' => $filtro_estado
        ];

        $this->view('panel-porteria/historial-prestamos', $data);
    }
}