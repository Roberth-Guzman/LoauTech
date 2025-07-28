<?php
session_start();
include '../conexion.php';

// Verificar si el usuario es de portería
if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit;
}

if ($_SESSION['user']['rol'] !== 'porteria') {
    header("Location: ../panel-usuario/panel-principal.php");
    exit;
}

// Procesar código escaneado
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['codigo'])) {
    $codigo = $conn->real_escape_string($_POST['codigo']);
    
    // Buscar el código en la base de datos
    $sql = "SELECT p.*, c.numerocont, c.correocont, r.rol 
            FROM codigos_barras cb
            JOIN personas p ON cb.idperlas = p.IDper
            JOIN contactos c ON p.IDper = c.IDperso
            JOIN roles r ON p.IDper = r.idper
            WHERE cb.codigo = '$codigo'";
    
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $persona = $result->fetch_assoc();
        
        // Registrar entrada/salida en marcador
        $sql_marcador = "SELECT * FROM marcador 
                         WHERE IDmar IN (
                             SELECT IDmarcador FROM ingresoelementos 
                             WHERE IDPER = {$persona['IDper']}
                         ) ORDER BY IDmar DESC LIMIT 1";
        
        $marcador = $conn->query($sql_marcador)->fetch_assoc();
        
        if ($marcador && $marcador['horaentradamar'] && !$marcador['horasalidamar']) {
            // Registrar salida
            $conn->query("UPDATE marcador SET horasalidamar = NOW() 
                          WHERE IDmar = {$marcador['IDmar']}");
            echo json_encode(['tipo' => 'salida', 'persona' => $persona]);
        } else {
            // Registrar entrada
            $conn->query("INSERT INTO marcador (horaentradamar, fechamar, estadomar) 
                          VALUES (NOW(), CURDATE(), 'activo')");
            $id_marcador = $conn->insert_id;
            
            // Relacionar con el elemento si existe
            $conn->query("UPDATE ingresoelementos SET IDmarcador = $id_marcador 
                          WHERE IDPER = {$persona['IDper']} AND IDmarcador IS NULL");
            
            echo json_encode(['tipo' => 'entrada', 'persona' => $persona]);
        }
    } else {
        echo json_encode(['error' => 'Código no registrado']);
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Escáner - LOAUTECH</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/quagga/dist/quagga.min.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    keyframes: {
                        scan: {
                            '0%': { top: '0', opacity: '0.8' },
                            '50%': { opacity: '1' },
                            '100%': { top: '100%', opacity: '0.8' },
                        },
                    },
                    animation: {
                        'scan': 'scan 2s ease-in-out infinite',
                    },
                },
            },
        }
    </script>
</head>
<body class="bg-gray-100 min-h-screen">
    <!-- Barra de navegación -->
    <nav class="bg-gray-800 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="panel-principal.php" class="flex-shrink-0 flex items-center">
                        <i class="fas fa-arrow-left text-xl mr-2"></i>
                        <span class="text-xl font-bold">LOAUTECH</span>
                    </a>
                </div>
                <div class="flex items-center">
                    <a href="../logout.php" class="text-gray-300 hover:bg-gray-700 hover:text-white px-3 py-2 rounded-md text-sm font-medium">
                        <i class="fas fa-sign-out-alt mr-1"></i> Cerrar Sesión
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Contenido principal -->
    <div class="max-w-4xl mx-auto px-4 py-8 sm:px-6 lg:px-8">
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <!-- Encabezado -->
            <div class="bg-blue-600 px-6 py-4">
                <h1 class="text-2xl font-bold text-white text-center">
                    <i class="fas fa-qrcode mr-2"></i> ESCÁNER DE CÓDIGO DE BARRAS
                </h1>
            </div>

            <!-- Cuerpo -->
            <div class="p-6">
                <p class="text-gray-600 text-center mb-6">
                    Escanea el código de barras del carnet del personal para registrar entrada o salida
                </p>
                
                <!-- Contenedor del escáner -->
                <div class="mb-6">
                    <div id="scanner-container" class="relative w-full max-w-[500px] h-[300px] mx-auto border-4 border-blue-500 rounded-lg overflow-hidden bg-gray-900">
                        <!-- Línea de escaneo -->
                        <div id="scanner-line" class="absolute h-1 w-full bg-red-500 shadow-[0_0_10px_#ef4444] animate-scan z-10"></div>
                        
                        <!-- Esquinas decorativas -->
                        <div class="absolute top-0 left-0 w-8 h-8 border-t-4 border-l-4 border-blue-500 rounded-tl-lg"></div>
                        <div class="absolute top-0 right-0 w-8 h-8 border-t-4 border-r-4 border-blue-500 rounded-tr-lg"></div>
                        <div class="absolute bottom-0 left-0 w-8 h-8 border-b-4 border-l-4 border-blue-500 rounded-bl-lg"></div>
                        <div class="absolute bottom-0 right-0 w-8 h-8 border-b-4 border-r-4 border-blue-500 rounded-br-lg"></div>
                        
                        <!-- Mensaje inicial -->
                        <div class="absolute inset-0 flex items-center justify-center">
                            <p class="text-gray-400 text-center px-4">
                                <i class="fas fa-camera text-3xl block mb-2"></i>
                                La cámara se activará al iniciar el escáner
                            </p>
                        </div>
                        
                            <!-- Elemento de video para la cámara -->
                            <video id="video" class="w-full h-full object-cover hidden"></video>
                    </div>
                </div>

                <!-- Controles del escáner -->
                <div class="flex flex-col sm:flex-row justify-center gap-4 mb-8">
                    <button id="startScannerBtn" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-lg shadow-md hover:shadow-lg transition-all duration-200 flex items-center justify-center">
                        <i class="fas fa-camera mr-2"></i> Iniciar Escáner
                    </button>
                    <button id="stopScannerBtn" class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-6 rounded-lg shadow-md hover:shadow-lg transition-all duration-200 flex items-center justify-center" disabled>
                        <i class="fas fa-stop-circle mr-2"></i> Detener
                    </button>
                </div>

                <!-- Advertencia de cámara -->
                <div id="no-camera-warning" class="hidden bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6 rounded">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-triangle text-yellow-600 text-xl"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-yellow-700">
                                No se pudo acceder a la cámara. Asegúrate de otorgar los permisos necesarios y que la cámara no esté siendo utilizada por otra aplicación.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Resultado del escaneo -->
                <div id="resultado" class="bg-gray-50 rounded-lg p-6 shadow-inner border border-gray-200 hidden">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 text-center border-b pb-2">
                        <i class="fas fa-user-check text-green-500 mr-2"></i> Registro de Asistencia
                    </h3>
                    <div id="datos-persona" class="space-y-3"></div>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const startBtn = document.getElementById('startScannerBtn');
        const stopBtn = document.getElementById('stopScannerBtn');
        const scannerContainer = document.getElementById('scanner-container');
        const noCameraWarning = document.getElementById('no-camera-warning');
        const resultadoDiv = document.getElementById('resultado');
        const nombrePersona = document.getElementById('nombre-persona');
        const documentPersona = document.getElementById('documento-persona');
        const tipoRegistro = document.getElementById('tipo-registro');
        const errorMessage = document.getElementById('error-message');
        
        let scannerActive = false;
        
        startBtn.addEventListener('click', function() {
            startScanner();
        });
        
        stopBtn.addEventListener('click', function() {
            stopScanner();
        });
        
        function startScanner() {
            Quagga.init({
                inputStream: {
                    name: "Live",
                    type: "LiveStream",
                    target: scannerContainer,
                    constraints: {
                        width: 480,
                        height: 320,
                        facingMode: "environment"
                    },
                },
                decoder: {
                    readers: ["code_128_reader", "ean_reader", "ean_8_reader", "code_39_reader", "code_39_vin_reader"]
                },
                locate: true
            }, function(err) {
                if (err) {
                    console.error(err);
                    noCameraWarning.classList.remove('d-none');
                    return;
                }
                
                Quagga.start();
                scannerActive = true;
                startBtn.classList.add('d-none');
                stopBtn.classList.remove('d-none');
                resultadoDiv.style.display = 'none';
                errorMessage.classList.add('d-none');
            });
            
            Quagga.onDetected(function(result) {
                const code = result.codeResult.code;
                stopScanner();
                
                // Enviar código al servidor
                fetch(window.location.href, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'codigo=' + encodeURIComponent(code)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        errorMessage.textContent = data.error;
                        errorMessage.classList.remove('d-none');
                    } else {
                        nombrePersona.textContent = 'Nombre: ' + data.persona.nombrecompletoper;
                        documentPersona.textContent = 'Documento: ' + data.persona.numerodoc;
                        tipoRegistro.textContent = 'Registro de ' + data.tipo.toUpperCase();
                        tipoRegistro.className = 'fw-bold text-' + (data.tipo === 'entrada' ? 'success' : 'danger');
                        resultadoDiv.style.display = 'block';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    errorMessage.textContent = 'Error al procesar el código';
                    errorMessage.classList.remove('d-none');
                });
            });
        }
        
        function stopScanner() {
            if (scannerActive) {
                Quagga.stop();
                scannerActive = false;
                startBtn.classList.remove('d-none');
                stopBtn.classList.add('d-none');
            }
        }
    });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>